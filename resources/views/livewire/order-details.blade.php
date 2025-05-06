<div class="p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Dettagli Ordine #{{ $order->id }}</h1>
        <a href="{{ route('orders.index') }}"
           class="text-sm text-gray-600 hover:underline">← Torna alla lista</a>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <p><span class="font-medium">Codice:</span> {{ $order->order_code }}</p>
            <p><span class="font-medium">Data:</span> {{ $order->order_date->format('Y-m-d') }}</p>
            <p><span class="font-medium">Stato:</span> {{ ucfirst($order->status) }}</p>
        </div>
        <div>
            <p><span class="font-medium">Cliente:</span> {{ $order->customer->name }}</p>
            <p><span class="font-medium">Email:</span> {{ $order->customer->email }}</p>
            <p><span class="font-medium">Totale:</span> € {{ number_format($order->total, 2, ',', '.') }}</p>
        </div>
    </div>

    <h2 class="text-xl font-medium mb-2">Prodotti</h2>
    <table class="w-full text-left border-collapse">
        <thead>
        <tr class="border-b bg-gray-100">
            <th class="px-3 py-2">Prodotto</th>
            <th class="px-3 py-2">Quantità</th>
            <th class="px-3 py-2">Prezzo Unitario</th>
            <th class="px-3 py-2">Subtotale</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->products as $p)
            <tr class="border-b">
                <td class="px-3 py-2">{{ $p->name }}</td>
                <td class="px-3 py-2">{{ $p->pivot->quantity }}</td>
                <td class="px-3 py-2">€ {{ number_format($p->pivot->price, 2, ',', '.') }}</td>
                <td class="px-3 py-2">
                    € {{ number_format($p->pivot->quantity * $p->pivot->price, 2, ',', '.') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
