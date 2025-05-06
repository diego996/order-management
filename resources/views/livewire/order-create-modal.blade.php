<div
    wire:model="show"
    wire:click.self="cancel"
    class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
>
    <div class="bg-white rounded-lg w-3/4 max-w-2xl p-6 relative">
        <h2 class="text-xl font-semibold mb-4">Nuovo Ordine</h2>

        {{-- Form --}}
        <div class="space-y-4">
            {{-- Cliente --}}
            <div>
                <label class="block text-sm">Cliente</label>
                <select
                    wire:model.defer="customer_id"
                    class="w-full border rounded px-2 py-1"
                >
                    <option value="">Seleziona cliente</option>
                    @foreach($availableCustomers as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Data & Codice --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Data</label>
                    <input
                        type="date"
                        wire:model.defer="order_date"
                        class="w-full border rounded px-2 py-1"
                    >
                    @error('order_date')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm">Codice</label>
                    <input
                        type="text"
                        wire:model.defer="order_code"
                        class="w-full border rounded px-2 py-1"
                    >
                    @error('order_code')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Stato --}}
            <div>
                <label class="block text-sm">Stato</label>
                <select
                    wire:model.defer="status"
                    class="w-full border rounded px-2 py-1"
                >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                @error('status')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prodotti --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-medium">Prodotti</h3>
                    <button
                        wire:click.prevent="addProductLine"
                        class="text-sm text-blue-600 hover:underline"
                    >+ Aggiungi prodotto</button>
                </div>

                @foreach($products as $i => $line)
                    <div class="grid grid-cols-4 gap-2 items-end mb-2">
                        {{-- Selezione Prodotto --}}
                        <div>
                            <select
                                wire:model="products.{{ $i }}.product_id"
                                class="border rounded px-2 py-1 w-full"
                            >
                                <option value="">Seleziona</option>
                                @foreach($availableProducts as $p)
                                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                            @error("products.{$i}.product_id")
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quantità --}}
                        <div>
                            <input
                                type="number"
                                min="0"
                                wire:model="products.{{ $i }}.quantity"
                                wire:change="$refresh"
                                class="border rounded px-2 py-1 w-full"
                                placeholder="Qty"
                            >
                            @error("products.{$i}.quantity")
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prezzo Unitario e Subtotale --}}
                        <div>
                            @php
                                $prod = collect($availableProducts)
                                          ->firstWhere('id', $line['product_id']);
                                $unit = $prod['price'] ?? 0;
                            @endphp
                            <p class="text-sm"  wire:change="$refresh">Unit: € {{ number_format($unit,2,',','.') }}</p>
                            <p class="text-sm font-medium"  wire:change="$refresh">
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
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Azioni --}}
        <div class="mt-6 flex justify-end space-x-2">
            <button
                wire:click="cancel"
                class="px-4 py-1 border rounded hover:bg-gray-100"
            >Annulla</button>
            <button
                wire:click="save"
                class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700"
            >Salva</button>
        </div>
    </div>
</div>
