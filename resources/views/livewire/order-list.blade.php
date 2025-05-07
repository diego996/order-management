<div class="p-6 bg-gray-50 min-h-screen">

    {{-- Filtri e pulsanti --}}
    <div class="flex flex-wrap items-center mb-6 gap-4">
        <select wire:model="filterCustomer"
                wire:change="$refresh"
                class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring focus:ring-blue-300 focus:outline-none">
            <option value="">Tutti i clienti</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>

        <select wire:model="filterStatus" wire:change="$refresh"
                class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring focus:ring-blue-300 focus:outline-none">
            <option value="">Tutti gli stati</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <input type="date"
               wire:model="filterDateFrom" wire:change="$refresh"
               class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring focus:ring-blue-300 focus:outline-none"
               placeholder="Da data">

        <input type="date"
               wire:model="filterDateTo" wire:change="$refresh"
               class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring focus:ring-blue-300 focus:outline-none"
               placeholder="A data">

        <button wire:click="resetFilters"
                class="ml-auto text-sm text-gray-600 hover:text-gray-800 hover:underline">
            Reset filtri
        </button>

        <button wire:click="openCreateModal"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 focus:ring focus:ring-blue-300 focus:outline-none">
            Nuovo Ordine
        </button>
    </div>

    {{-- Tabella ordini --}}
    <div wire:loading.class="opacity-50" wire:target="filterCustomer,filterStatus,filterDateFrom,filterDateTo">
        <table class="min-w-full bg-white shadow-lg rounded-lg overflow-hidden">
            <thead class="bg-blue-100 border-b">
                <tr>
                    <th wire:click="sortBy('id')" class="px-6 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer hover:text-gray-900">
                        #
                        @if($sortField === 'id')
                            @if($sortDirection === 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('order_code')" class="px-6 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer hover:text-gray-900">
                        Codice
                        @if($sortField === 'order_code')
                            @if($sortDirection === 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('customer_name')" class="px-6 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer hover:text-gray-900">
                        Cliente
                        @if($sortField === 'customer_name')
                            @if($sortDirection === 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('order_date')" class="px-6 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer hover:text-gray-900">
                        Data
                        @if($sortField === 'order_date')
                            @if($sortDirection === 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer hover:text-gray-900">
                        Stato
                        @if($sortField === 'status')
                            @if($sortDirection === 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('total')" class="px-6 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer hover:text-gray-900">
                        Totale
                        @if($sortField === 'total')
                            @if($sortDirection === 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Azioni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($orders as $o)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $o->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $o->order_code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $o->customer->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $o->order_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-white text-xs font-medium
                                {{ $o->status === 'pending' ? 'bg-yellow-500' : '' }}
                                {{ $o->status === 'processing' ? 'bg-blue-500' : '' }}
                                {{ $o->status === 'completed' ? 'bg-green-500' : '' }}
                                {{ $o->status === 'cancelled' ? 'bg-red-500' : '' }}">
                                {{ ucfirst($o->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">€ {{ number_format($o->total,2,',','.') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('orders.show', $o) }}" class="text-blue-600 hover:underline">Dettagli</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Nessun ordine trovato.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- Modale di creazione --}}
    @if($showCreateModal)
        <livewire:order-create-modal
            wire:key="order-create-modal"
            wire:model="showCreateModal" />
    @endif

</div>
