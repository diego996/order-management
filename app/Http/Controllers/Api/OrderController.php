<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['customer_id','status','date_from','date_to']);
        $perPage = $request->get('per_page', 15);

        $orders = $this->service->list($filters, $perPage);

        return response()->json($orders);
    }

    public function show(int $order): JsonResponse
    {
        $model = $this->service->get($order);

        if (! $model) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($model);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $order = $this->service->create($data);

        return response()->json($order, 201);
    }

    public function update(UpdateOrderRequest $request, int $order): JsonResponse
    {
        $data = $request->validated();
        $updated = $this->service->update($order, $data);
        return response()->json($updated);
    }

    public function destroy(int $order): JsonResponse
    {
        $deleted = $this->service->delete($order);

        if (! $deleted) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(null, 204);
    }


}
