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


   /**
     * Lista gli ordini con filtri.
     *
     * @queryParam customer_id integer Filtra per ID cliente. Example: 1
     * @queryParam status string Filtra per stato (pending, completed). Example: pending
     * @queryParam date_from date Filtra data inizio. Example: 2023-10-01
     * @queryParam date_to date Filtra data fine. Example: 2023-10-31
     * @queryParam per_page integer Numero di risultati per pagina. Example: 15
     *
     * @response 200 {
     *   "data": [array di ordini ],
    *   "meta": {  paginazione  }
    * }
    */


    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['customer_id','status','date_from','date_to']);
        $perPage = $request->get('per_page', 15);

        $orders = $this->service->list($filters, $perPage);

        return response()->json($orders);
    }
 /**
     * Mostra i dettagli di un singolo ordine.
     *
     * @urlParam order integer required ID dell’ordine. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "customer_id": 1,
     *   "status": "pending",
     *   "total": 100.00,
     *   "products": []
     * }
     * @response 404 {
     *   "message": "Order not found"
     * }
     */
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
/**
     * Crea un nuovo ordine.
     *
     * @bodyParam customer_id integer required ID cliente. Example: 1
     * @bodyParam status string required Stato ordine. Example: pending
     * @bodyParam products array required Elenco prodotti con id e quantità. Example: [{"id":1,"quantity":2}]
     *
     * @response 201 {
     *      restituzione ordine creato
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {}
     * }
     */

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $order = $this->service->create($data);

        return response()->json($order, 201);
    }
     /**
     * Aggiorna un ordine esistente.
     *
     * @urlParam order integer required ID dell’ordine. Example: 1
     * @bodyParam customer_id integer ID cliente.
     * @bodyParam status string Stato.
     * @bodyParam products array Elenco prodotti.
     *
     * @response 200 {  restituzione ordine aggiornato  }
     * @response 404 { "message": "Order not found" }
     * @response 422 {  errore di validazione  }
     */

    public function update(UpdateOrderRequest $request, int $order): JsonResponse
    {
        $data = $request->validated();
        $updated = $this->service->update($order, $data);
        return response()->json($updated);
    }
    /**
     * Elimina un ordine.
     *
     * @urlParam order integer required ID dell’ordine. Example: 1
     * @response 204 - No Content
     * @response 404 { "message": "Order not found" }
     */
    public function destroy(int $order): JsonResponse
    {
        $deleted = $this->service->delete($order);

        if (! $deleted) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(null, 204);
    }


}
