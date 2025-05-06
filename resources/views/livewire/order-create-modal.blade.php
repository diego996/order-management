<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg w-3/4 max-w-2xl p-6">
        <h2 class="text-xl font-semibold mb-4">Nuovo Ordine</h2>

        <div class="space-y-3">
            <div>
                <label class="block text-sm">Cliente</label>
                <select wire:model.defer="customer_id" class="w-full border rounded px-2 py-1">
                    <option value="">Seleziona cliente</option>
                    @foreach($availableCustomers as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Data</label>
                    <input type="date" wire:model.defer="order_date" class="w-full border rounded px-2 py-1">
                    @error('order_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm">Codice</label>
                    <input type="text" wire:model.defer="order_code" class="w-full border rounded px-2 py-1">
                    @error('order_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm">Stato</label>
                <select wire:model.defer="status" class="w-full border rounded px-2 py-1">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <h3 class="font-medium mb-2">Prodotti</h3>
                <button wire:click.prevent="addProductLine" class="mb-2 text-sm text-blue-600 hover:underline">
                    + Aggiungi prodotto
                </button>

                @foreach($products as $i => $prodLine)
                    <div class="grid grid-cols-4 gap-2 items-end mb-2">
                        <div>
                            <select wire:model.defer="products.{{ $i }}.product_id" class="border rounded px-2 py-1 w-full">
                                <option value="">Seleziona</option>
                                @foreach($availableProducts as $p)
                                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                            @error("products.{$i}.product_id") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <input type="number" min="1" wire:model.defer="products.{{ $i }}.quantity" class="border rounded px-2 py-1 w-full" placeholder="Qty">
                            @error("products.{$i}.quantity") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <input type="text" wire:model.defer="products.{{ $i }}.price" class="border rounded px-2 py-1 w-full" placeholder="Prezzo">
                            @error("products.{$i}.price") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <button wire:click.prevent="removeProductLine({{ $i }})" class="text-red-600 hover:underline text-sm">Rimuovi</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <button wire:click="$set('show', false)" class="px-4 py-1 border rounded hover:bg-gray-100">
                Annulla
            </button>
            <button wire:click="save" class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                Salva
            </button>
        </div>
    </div>
</div>
