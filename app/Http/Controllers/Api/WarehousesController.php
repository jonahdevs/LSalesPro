<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WarehouseInventoryResource;
use App\Http\Resources\Api\WarehousesResource;
use App\Models\Warehouse;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $warehouses = Warehouse::withCount('stock')->get();
        return WarehousesResource::collection($warehouses);
    }

    public function inventory(Warehouse $warehouse)
    {
        $inventory = $warehouse->stock()->with('product')->get();

        return WarehouseInventoryResource::collection($inventory);
    }
}
