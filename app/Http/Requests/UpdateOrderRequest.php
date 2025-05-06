<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderId = $this->route('order');

        return [
            'user_id'        => 'sometimes|required|exists:users,id',
            'customer_id'    => 'sometimes|required|exists:customers,id',
            'order_date'     => 'sometimes|required|date',
            'order_code'     => [
                'sometimes','required',
                Rule::unique('orders','order_code')->ignore($orderId),
            ],
            'status'         => 'sometimes|required|string|in:pending,processing,completed,cancelled',
            'total'          => 'sometimes|required|numeric|min:0',
            'products'       => 'sometimes|required|array|min:1',
            'products.*.product_id' => 'required_with:products|exists:products,id',
            'products.*.quantity'   => 'required_with:products|integer|min:1',
            'products.*.price'      => 'required_with:products|numeric|min:0',
        ];
    }
}
