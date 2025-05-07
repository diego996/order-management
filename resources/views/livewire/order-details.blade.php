<div class="p-6 bg-white rounded-lg shadow-md">

    <!-- Intestazione -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <button onclick="window.location.href='{{ route('orders.index') }}'"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                ← Torna alla lista
            </button>
            <h1 class="text-2xl font-bold text-gray-800">
                @if($editing)
                    <input type="text"
                           wire:model.defer="order_code"
                           class="border-b border-gray-300 focus:outline-none focus:border-blue-500"
                           placeholder="Codice ordine">
                    @error('order_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @else
                    Ordine #{{ $order->id }} ({{ $order->order_code }})
                @endif
            </h1>
        </div>

        <div class="space-x-2">
            @if($editing)
                <button wire:click="cancel"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    Annulla
                </button>
                <button wire:click="save"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Salva
                </button>
            @else
                <button wire:click="edit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Modifica
                </button>
            @endif
        </div>
    </div>

    <!-- Dettagli Ordine -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="space-y-4">
            <p><span class="font-semibold text-gray-700">Codice:</span> 
                @if($editing)
                    <input type="text"
                           wire:model.defer="order_code"
                           class="border-b border-gray-300 focus:outline-none focus:border-blue-500"
                           placeholder="Codice ordine">
                    @error('order_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @else
                    {{ $order->order_code }}
                @endif
            </p>
            <p><span class="font-semibold text-gray-700">Data:</span> 
                @if($editing)
                    <input type="date"
                           wire:model.defer="order_date"
                           class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:ring-blue-300">
                    @error('order_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @else
                    {{ $order->order_date->format('Y-m-d') }}
                @endif
            </p>
            <p><span class="font-semibold text-gray-700">Stato:</span> 
                @if($editing)
                    <select wire:model.defer="status"
                            class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:ring-blue-300">
                        @foreach($availableStatuses as $st)
                            <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @else
                    <span class="px-2 py-1 rounded-full text-white {{ $order->status === 'completed' ? 'bg-green-500' : 'bg-yellow-500' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                @endif
            </p>
        </div>
        <div class="space-y-4">
            <p><span class="font-semibold text-gray-700">Cliente:</span> {{ $order->customer->name }}</p>
            <p><span class="font-semibold text-gray-700">Email:</span> {{ $order->customer->email }}</p>
            <p><span class="font-semibold text-gray-700">Totale:</span> 
                <span class="text-lg font-bold text-gray-800">€ {{ number_format($order->total, 2, ',', '.') }}</span>
            </p>
        </div>
    </div>

    <!-- Tabella Prodotti -->
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Prodotti</h2>
    @if($editing)
        {{-- Pulsante Aggiungi Riga --}}
        <div class="mb-4">
            <button wire:click="addLine"
                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Aggiungi prodotto
            </button>
            @error('lines') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
    @endif
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md text-center">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-sm font-medium text-gray-600 uppercase">
                        Prodotto
                    </th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-600 uppercase">
                        Quantità
                    </th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-600 uppercase">
                        Prezzo Unitario
                    </th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-600 uppercase">
                        Subtotale
                    </th>
                    @if($editing)
                        <th class="px-6 py-3 text-sm font-medium text-gray-600 uppercase">
                            Azioni
                        </th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @if($editing)
                    @foreach($lines as $i => $line)
                        <tr class="hover:bg-gray-50">
                            {{-- Select Prodotto --}}
                            <td class="px-6 py-4">
                                <select wire:model="lines.{{ $i }}.product_id"
                                        class="border rounded-lg px-2 py-1 w-full">
                                    <option value="">— Seleziona —</option>
                                    @foreach($availableProducts as $p)
                                        <option value="{{ $p['id'] }}">
                                            {{ $p['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("lines.{$i}.product_id") 
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </td>

                            {{-- Input Quantità --}}
                            <td class="px-6 py-4">
                                <input type="number" min="1"
                                       wire:model.defer="lines.{{ $i }}.quantity"
                                       class="border rounded-lg px-2 py-1 w-20">
                                @error("lines.{$i}.quantity") 
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </td>

                            {{-- Prezzo Unitario --}}
                            <td class="px-6 py-4 text-gray-800">
                                @php
                                    $prod = collect($availableProducts)
                                        ->firstWhere('id', $line['product_id']);
                                    $unit = $prod['price'] ?? 0;
                                @endphp
                                € {{ number_format($unit,2,',','.') }}
                            </td>

                            {{-- Subtotale --}}
                            <td class="px-6 py-4 text-gray-800 font-semibold">
                                @if($line['product_id'])
                                    @php
                                        // prendo il prezzo unitario corrente
                                        $product = collect($availableProducts)
                                                    ->firstWhere('id', $line['product_id']);
                                        $unit     = $product['price'] ?? 0;
                                        $qty      = $line['quantity'] ?? 0;
                                        $subtotal = $unit * $qty;
                                    @endphp
                            
                                    {{-- mostro sempre: quantità × unitario = subtotale --}}
                                    {{ $qty }} × €{{ number_format($unit,2,',','.') }}
                                    = €{{ number_format($subtotal,2,',','.') }}
                                @else
                                    —
                                @endif
                            </td>
                            {{-- Rimuovi --}}
                            <td class="px-6 py-4">
                                <button wire:click="removeLine({{ $i }})"
                                        class="text-red-600 hover:underline">
                                    Rimuovi
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    @foreach($order->products as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $p->name }} 
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $p->pivot->quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                € {{ number_format($p->price,2,',','.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 font-semibold">
                                € {{ number_format($p->price * $p->pivot->quantity ,2,',','.') }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>

            @if($editing)
                <tfoot>
                    <tr class="border-t font-medium">
                        <td colspan="3" class="px-6 py-4 text-right">Totale:</td>
                        <td class="px-6 py-4">
                            € {{ number_format($order->total, 2, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
