<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Store\StoreCustomerRequest;
use App\Http\Requests\Api\Update\UpdateCustomerRequest;
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
        $customers = Customer::paginate(10);
        return CustomersResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        try {
            $territory_id = Territory::where('name', $validated['territory'])->first()?->id ?? null;

            if ($validated['territory'] && !isset($territory_id)) {
                return $this->error(null, 'Territory not found', 422);
            }

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
        $validated = $request->validated();

        try {
            if (isset($validated['territory'])) {
                $territory_id = Territory::where('name', $validated['territory'])->first()?->id ?? null;

                if ($validated['territory'] && !isset($territory_id)) {
                    return $this->error(null, 'Territory not found', 422);
                }
                $validated['territory_id'] = $territory_id;
            }

            $customer->update($validated);

            return $this->success(null, 'Customer updated successfully');

        } catch (\Throwable $th) {
            return $this->error(null, 'Failed to update customer', 500);
        }
    }

    public function destroy(Customer $customer)
    {
        $count = $customer->delete();

        if ($count == 0) {
            return $this->error(null, 'Something went wrong. Please try again later.');
        }

        return $this->success(null, 'Customer deleted successfully!');
    }
}
