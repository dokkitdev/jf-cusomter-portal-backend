<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\GetCustomerRequest;
use App\Http\Requests\Customers\SearchCustomerRequest;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public function get(GetCustomerRequest $request, CustomerService $service, $id)
    {
        $result = $service
            ->with($request->onlyValidated('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchCustomerRequest $request, CustomerService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
