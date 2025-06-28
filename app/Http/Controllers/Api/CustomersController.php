<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Store\StoreCustomerRequest;
use App\Http\Requests\Api\Update\UpdateCustomerRequest;
use App\Http\Resources\Api\CustomerMapData;
use App\Http\Resources\Api\customerOrdersResource;
use App\Http\Resources\Api\CustomersResource;
use App\Models\Customer;
use App\Models\Territory;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    use HttpResponses;

    public function index()
    {
        // Fetch all customers with pagination
        $customers = Customer::paginate(10);
        return CustomersResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        try {
            // Check if territory exists and get its ID
            $territory_id = Territory::where('name', $validated['territory'])->first()?->id ?? null;

            // If territory is provided but not found, return an error
            if ($validated['territory'] && !isset($territory_id)) {
                return $this->error(null, 'Territory not found', 422);
            }

            // Create the customer with validated data
            Customer::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'categiry' => $validated['category'],
                'contact_person' => $validated['contact_person'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'tax_id' => $validated['tax_id'],
                'payment_terms' => $validated['payment_terms'],
                'credit_limit' => $validated['credit_limit'],
                'current_balance' => $validated['current_balance'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'address' => $validated['address'],
                'territory_id' => $territory_id,
            ]);

            return $this->success(null, 'Customer created successfully');
        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function show(Customer $customer)
    {
        return new CustomersResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        // Validate the request data
        $validated = $request->validated();

        try {
            // Check if territory exists and get its ID
            if (isset($validated['territory'])) {
                $territory_id = Territory::where('name', $validated['territory'])->first()?->id ?? null;

                // If territory is provided but not found, return an error
                if ($validated['territory'] && !isset($territory_id)) {
                    return $this->error(null, 'Territory not found', 422);
                }
                $validated['territory_id'] = $territory_id;
            }

            // Update the customer with validated data
            $customer->update($validated);

            return $this->success(null, 'Customer updated successfully');

        } catch (\Throwable $th) {
            return $this->error(null, 'Failed to update customer', 500);
        }
    }

    public function destroy(Customer $customer)
    {
        // delete the customer
        $count = $customer->delete();

        if ($count == 0) {
            return $this->error(null, 'Something went wrong. Please try again later.');
        }

        return $this->success(null, 'Customer deleted successfully!');
    }

    public function orderHistory(Customer $customer)
    {
        // Load the customer's orders and their items
        $customer->load('orders.items');

        return new customerOrdersResource($customer);
    }

    public function creditStatus(Customer $customer)
    {
        // Calculate the total used credit from pending, confirmed, and processing orders
        $totalUsed = $customer->orders()
            ->whereIn('status', ['pending', 'confirmed', 'processing'])
            ->sum('total');

        return $this->success([
            'customer_id' => $customer->id,
            'credit_limit' => $customer->credit_limit,
            'used_credit' => $totalUsed,
            'available_credit' => $customer->credit_limit - $totalUsed,
        ], null, 200);
    }

    public function mapData()
    {
        // Fetch customers with latitude and longitude, along with their territory
        $customers = Customer::select('id', 'name', 'latitude', 'longitude', 'territory_id')
            ->with('territory')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return CustomerMapData::collection($customers);
    }
}
