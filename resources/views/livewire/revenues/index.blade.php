@section('page_title')
   Revenue
@endsection
<div class="container-fluid py-4 " wire:init="init">
 
    <div class="row mt-4" >
        <div class="col-12">
            <x-alert></x-alert> 
            <div class="card custom-card">
                <!-- Card header -->
                @include('livewire.revenues.filter')
                <!-- Card header end -->
            <div class="card-body pt-0"> 
                <x-table >
                    <x-slot name="head" >
                        <x-table.heading sortable wire:click="sortBy('order_id')"
                        :direction="$sortField === 'order_id' ? $sortDirection : null"> Order ID
                        </x-table.heading> 
                        <x-table.heading> Store Name
                        </x-table.heading>
                        <x-table.heading>Transaction Type
                        </x-table.heading>                      
                        <x-table.heading sortable wire:click="sortBy('amount')"
                        :direction="$sortField === 'amount' ? $sortDirection : null">Amount
                        </x-table.heading>                      
                        <x-table.heading sortable wire:click="sortBy('current_balance')"
                        :direction="$sortField === 'current_balance' ? $sortDirection : null">Current Balance
                        </x-table.heading>                      
                        <x-table.heading>Status
                        </x-table.heading>                      
                        <x-table.heading sortable wire:click="sortBy('created_at')"
                            :direction="$sortField === 'created_at' ? $sortDirection : null">
                            Creation Date
                        </x-table.heading>
                        
                    </x-slot>
                 
                    <x-slot name="body">
                      
                      @foreach ($revenues as $revenue )
                       
                        <x-table.row wire:key="row-{{ $revenue->id }}" >
                            <x-table.cell class="p-2"><a href="{{ route('order-details', $revenue->order) }}">#{{ $revenue->order->order_number }}</a></x-table.cell>     
                            <x-table.cell class="p-2">{{ $revenue->store->name }}</x-table.cell>
                            <x-table.cell class="p-2">{{ ucfirst($revenue->transaction_type) }}</x-table.cell>
                            <x-table.cell class="p-2">{{  \Utils::ConvertPrice($revenue->amount) }}</x-table.cell>
                            <!-- <x-table.cell class="p-2">{{  \Utils::ConvertPrice($revenue->order_delivery_amount) }}</x-table.cell> -->
                            <x-table.cell class="p-2">{{  \Utils::ConvertPrice($revenue->current_balance) }}</x-table.cell>
                            <x-table.cell class="p-2">{{ ucfirst($revenue->status) }}</x-table.cell>
                            <x-table.cell class="p-2">{{ $revenue->created_at->format(config('app_settings.date_format.value')) }}</x-table.cell>
                        </x-table.row>
                    @endforeach 
                       
                    </x-slot>
                </x-table> 
              @if($revenues && $revenues->total() > 10)
                <div class="row mx-2">
                    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start"><div class="dataTables_length" id="kt_ecommerce_sales_table_length">
                        <label>
                            <select  wire:model="perPage"  name="kt_ecommerce_sales_table_length" aria-controls="kt_ecommerce_sales_table" class="form-select form-select-sm form-select-solid">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                    </div>
                    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                        <div class="dataTables_paginate paging_simple_numbers" id="kt_ecommerce_sales_table_paginate">
                            @if ($revenues)
                            <div id="datatable-bottom">
                                {{ $revenues->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
               @endif
                @if($revenues && $revenues->total() == 0)
                    <div>
                        <p class="text-center">No records found!</p>
                    </div> 
                @endif
            </div>
            </div>
        </div>
    </div>
    <x-loder ></x-loder>
</div>

@push('js')
<script src="{{ asset('assets') }}/js/plugins/flatpickr.min.js"></script> 
<script src="{{ asset('assets') }}/js/plugins/datatables.js"></script>
@endpush
