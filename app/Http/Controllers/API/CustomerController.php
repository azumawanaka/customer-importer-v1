<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of all customers.
     * GET /customers
     */
    public function index(): JsonResponse
    {
        try {
            $customers = Customer::select('first_name', 'last_name', 'email', 'country')
                ->get()
                ->map(function ($customer) {
                    return [
                        'full_name' => $customer->first_name . ' ' . $customer->last_name,
                        'email' => $customer->email,
                        'country' => $customer->country,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified customer details.
     * GET /customers/{customerId}
     */
    public function show(string $customerId): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($customerId);

            return response()->json([
                'success' => true,
                'data' => [
                    'full_name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'username' => $customer->username,
                    'gender' => $customer->gender,
                    'country' => $customer->country,
                    'city' => $customer->city,
                    'phone' => $customer->phone,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
