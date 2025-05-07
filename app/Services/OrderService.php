<?php


namespace App\Services;

use App\Contracts\OrderRepositoryInterface;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Events\OrderDeleted;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class OrderService
{
    public function __construct(protected OrderRepositoryInterface $repo) {}

    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->repo->list($filters, $perPage, $sortField, $sortDirection);
    }

    public function all(array $filters = []): \Illuminate\Support\Collection
    {
        return $this->repo->all($filters);
    }

    public function get(int $id): ?Order
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Order
    {
        // 1) Estrai e rimuovi le righe prodotti dal payload
        $lines = $data['products'] ?? [];
        unset($data['products']);

        // 2) Associa l’utente loggato (o fallback)
        $data['user_id'] = Auth::check() ? Auth::id() : 1;

        // 3) Crea l’ordine senza total
        $order = $this->repo->create($data);

        $total = 0;

        // 4) Per ogni prodotto, prendi prezzo, attach pivot e somma
        foreach ($lines as $line) {
            // recupera il prezzo unitario dal modello Product
            $product   = Product::findOrFail($line['product_id']);
            $unitPrice = $product->price;
            $quantity  = $line['quantity'];

            // associa nella pivot: quantità + prezzo unitario
            $order->products()->attach($product->id, [
                'quantity' => $quantity,
                'price'    => $unitPrice,
            ]);

            // somma il subtotale
            $total += $unitPrice * $quantity;
        }

        // 5) Aggiorna il campo total nell’ordine

        event(new OrderCreated($order));
        return $order;
    }

    public function update(int $id, array $data): Order
    {
        $lines = $data['products'] ?? null;
        unset($data['products']);

        // Recupero l'ordine e aggiorno i campi
        $order = $this->repo->find($id)
            ?? throw new RuntimeException("Order #{$id} non trovato");
        $order = $this->repo->update($order, $data);

        if ($lines) {
            // Sincronizzo i prodotti nella pivot
            $syncData = collect($lines)->mapWithKeys(function ($line) {
                return [
                    $line['product_id'] => [
                        'quantity' => $line['quantity'],
                        'price'    => $line['price'],
                    ],
                ];
            })->toArray();

            $order->products()->sync($syncData);

            // Calcolo il totale aggiornato
            $total = collect($syncData)->sum(fn($pivot) => $pivot['price']);
            $order->update(['total' => $total]);
        }

        event(new OrderUpdated($order));
        return $order;
    }

    public function delete(int $id): bool
    {
        $order = $this->repo->find($id);
        $deleted = $this->repo->delete($order);
        if ($deleted) {
            event(new OrderDeleted($id));
        }
        return $deleted;
    }
}
