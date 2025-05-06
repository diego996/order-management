<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'        => 'required|exists:users,id',
            'customer_id'    => 'required|exists:customers,id',
            'order_date'     => 'required|date',
            'order_code'     => 'required|unique:orders,order_code',
            'status'         => 'required|string|in:pending,processing,completed,cancelled',
            'total'          => 'required|numeric|min:0',
            'products'       => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer|min:1',
            'products.*.price'      => 'required|numeric|min:0',
        ];
    }
}
