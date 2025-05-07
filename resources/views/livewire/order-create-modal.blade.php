<div
    wire:if="show"
    wire:click.self="cancel"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
>
    <div class="bg-white rounded-xl shadow-lg w-3/4 max-w-2xl p-8 relative">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Nuovo Ordine</h2>

        {{-- Form --}}
        <div class="space-y-6">
            {{-- Cliente --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Cliente</label>
                <select
                    wire:model.defer="customer_id"
                    class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                >
                    <option value="">Seleziona cliente</option>
                    @foreach($availableCustomers as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Data & Codice --}}
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data</label>
                    <input
                        type="date"
                        wire:model.defer="order_date"
                        class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    >
                    @error('order_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Codice</label>
                    <input
                        type="text"
                        wire:model.defer="order_code"
                        class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    >
                    @error('order_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Stato --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Stato</label>
                <select
                    wire:model.defer="status"
                    class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prodotti --}}
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Prodotti</h3>
                    <button
                        wire:click.prevent="addProductLine"
                        class="text-sm text-blue-600 hover:underline"
                    >+ Aggiungi prodotto</button>
                </div>

                @foreach($products as $i => $line)
                    <div class="grid grid-cols-4 gap-4 items-end mb-4">
                        {{-- Selezione Prodotto --}}
                        <div>
                            <select
                                wire:model="products.{{ $i }}.product_id"
                                class="border-gray-300 rounded-lg px-3 py-2 w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                            >
                                <option value="">Seleziona</option>
                                @foreach($availableProducts as $p)
                                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                            @error("products.{$i}.product_id")
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quantità --}}
                        <div>
                            <input
                                type="number"
                                min="0"
                                wire:model="products.{{ $i }}.quantity"
                                wire:change="$refresh"
                                class="border-gray-300 rounded-lg px-3 py-2 w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                placeholder="Qty"
                            >
                            @error("products.{$i}.quantity")
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prezzo Unitario e Subtotale --}}
                        <div>
                            @php
                                $prod = collect($availableProducts)
                                          ->firstWhere('id', $line['product_id']);
                                $unit = $prod['price'] ?? 0;
                            @endphp
                            <p class="text-sm text-gray-600">Unit: € {{ number_format($unit,2,',','.') }}</p>
                            <p class="text-sm font-medium text-gray-800">
                                Sub: € {{ number_format($line['price'] ?? 0,2,',','.') }}
                            </p>
                        </div>

                        {{-- Rimuovi Riga --}}
                        <div>
                            <button
                                wire:click.prevent="removeProductLine({{ $i }})"
                                class="text-red-600 hover:underline text-sm"
                            >Rimuovi</button>
                        </div>
                    </div>
                @endforeach
                @error('products')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Azioni --}}
        <div class="mt-8 flex justify-end space-x-4">
            <button
                wire:click="cancel"
                class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 text-gray-700"
            >Annulla</button>
            <button
                wire:click="save"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >Salva</button>
        </div>
    </div>
</div>
