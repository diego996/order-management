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


    //Laravel Scribe COMMENTA 
    // @group Orders
    // @authenticated
    // @response 200 {
    //     "data": [
    //         {
    //             "id": 1,
    //             "customer_id": 1,
    //             "status": "pending",
    //             "total": 100.00,
    //             "created_at": "2023-10-01T12:00:00Z",
    //             "updated_at": "2023-10-01T12:00:00Z",
    //             "products": [
    //                 {
    //                     "id": 1,
    //                     "name": "Product 1",
    //                     "price": 50.00,
    //                     "quantity": 2
    //                 },
    //                 {
    //                     "id": 2,
    //                     "name": "Product 2",
    //                     "price": 25.00,
    //                     "quantity": 1
    //                 }
    //             ]
    //         }
    //     ]
    // }
    // @response 404 {
    //     "message": "Order not found"
    // }
    // @response 422 {
    //     "message": "The given data was invalid.",
    //     "errors": {
    //         "customer_id": [
    //             "The customer id field is required."
    //         ],
    //         "status": [
    //             "The status field is required."
    //         ],
    //         "products": [
    //             "The products field is required."
    //         ]
    //     }
    // }
    // }
    // @response 401 {
    //     "message": "Unauthenticated."
    // }


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

        if ($model) {
            $model->load('products');
        }

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
