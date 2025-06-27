<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StockTransferRequest;
use App\Http\Resources\Api\StockTransferResource;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    use HttpResponses;

    public function transfer(StockTransferRequest $request)
    {
        $validated = $request->validated();

        try {
            $productId = $validated['product_id'];
            $fromId = $validated['from_warehouse_id'];
            $toId = $validated['to_warehouse_id'];
            $qty = $validated['quantity'];

            $fromStock = Stock::where('warehouse_id', $fromId)->where('product_id', $productId)->first();
            $toWarehouse = Warehouse::findOrFail($toId);

            if (!$fromStock || $fromStock->quantity < $qty) {
                return $this->error(null, 'Insufficient stock source in warehouse', 400);
            }

            $totalInfo = $toWarehouse->totalStockedQuantity();

            if (($totalInfo + $qty) > $toWarehouse->capacity) {
                return $this->error(null, 'Exceeds destination warehouse capacity.', 400);
            }

            // transfer logic
            DB::beginTransaction();
            $fromStock->decrement('quantity', $qty);

            $toStock = Stock::firstOrCreate([
                'warehouse_id' => $toId,
                'product_id' => $productId,
            ]);

            $toStock->increment('quantity', $qty);

            StockTransfer::create([
                'product_id' => $productId,
                'from_warehouse_id' => $fromId,
                'to_warehouse_id' => $toId,
                'quantity' => $qty,
            ]);

            DB::commit();
            return $this->success(null, 'Stock transferred successfully', 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function history()
    {
        $transfers = StockTransfer::with(['product', 'fromWarehouse', 'toWarehouse'])->latest()->get();
        return StockTransferResource::collection($transfers);
    }
}
