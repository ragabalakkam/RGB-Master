<?php

namespace App\Http\Controllers\API\Users;

use App\Models\Users\Customer;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Users\CustomerRequest;

class CustomersController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }
    
    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->all());
        return response()->json($customer);
    }
    
    public function show(Customer $customer)
    {
        return response()->json($customer);
    }
    
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());
        return response()->json($customer);
    }
    
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json();
    }
}
