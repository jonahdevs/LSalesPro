<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Store\StoreOrderRequest;
use App\Http\Resources\Api\OrdersResource;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\OrderService;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrdersController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $filters = request()->only(['search', 'status']);
        $query = Order::with('items');

        $query = $this->advancedFiltering($query, $filters);
        // Fetch all orders with their items
        // This will apply the advanced filtering based on the provided filters
        $orders = $query->paginate(request()->get('per_page', 10));

        // Return the paginated collection of orders
        return OrdersResource::collection($orders);
    }

    protected function advancedFiltering($query, $filters)
    {
        // filter by status
        $query->when($filters['status'] ?? null, fn($q, $status) =>
            $q->where('status', $status))

            // filter by search
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        return $query;
    }

    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $validated = $request->validated();

        $order = $orderService->create($validated);

        return $this->success([
            'order_number' => $order->order_number,
        ], 'Order created successfully', 201);
    }


    protected array $transitions = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        try {

            $validStatuses = array_keys($this->transitions);

            if (!in_array($validated['status'], $validStatuses)) {
                throw ValidationException::withMessages([
                    'status' => 'Invalid status'
                ]);
            }

            $currentStatus = $order->status;

            $allowedNext = $this->transitions[$currentStatus] ?? [];

            if (!in_array($validated['status'], $allowedNext)) {
                throw ValidationException::withMessages([
                    'status' => "Cannot change status from $currentStatus to {$validated['status']}"
                ]);
            }

            $order->update(['status' => $validated['status']]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'old_status' => $currentStatus,
                'new_status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
            ]);
            return $this->success($order->status, 'Order Status updated successfully', 200);

        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function show(Order $order)
    {
        $order->load('items');
        return new OrdersResource($order);
    }




}
