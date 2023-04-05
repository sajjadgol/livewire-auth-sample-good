@section('page_title')
    Transactions
@endsection
 <div  class="container-fluid py-4" wire:init="init">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-card">
               <!-- Card header -->
                @include('livewire.transaction.filter')
                <!-- Card header end -->
             <div class="card-body pt-0">
                <x-table>
                    <x-slot name="head">
                        <x-table.heading sortable wire:click="sortBy('order_id')"
                        :direction="$sortField === 'order_id' ? $sortDirection : null">  Order Number
                        </x-table.heading> 
                        <x-table.heading sortable wire:click="sortBy('user_id')"
                        :direction="$sortField === 'user_id' ? $sortDirection : null"> Customer
                        </x-table.heading> 
                        <x-table.heading>Amount
                        </x-table.heading>  
                        <x-table.heading>Payment Method
                        </x-table.heading>     
                        <x-table.heading>Status
                        </x-table.heading>                   
                        <x-table.heading >
                            Creation Date
                        </x-table.heading>
                                               
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($transactions as $transaction)
                        <x-table.row wire:key="row-{{$transaction->id }}">
                            <x-table.cell class="p-2"><a href="{{ route('order-details', $transaction->order) }}">#{{ $transaction->order->order_number }}</a></x-table.cell>     
                            <x-table.cell class="p-2">@role('Admin')<a href="{{ route('view-user',  $transaction->user) }}">{{ $transaction->user->name  }}</a>@endrole
                                                      @role('Provider'){{ $transaction->user->name  }}@endrole
                        </x-table.cell>
                            <x-table.cell class="p-2">{{  \Utils::ConvertPrice($transaction->amount) }}</x-table.cell>
                            <x-table.cell class="p-2">{{ ucfirst($transaction->payment_mode)  }}</x-table.cell>
                            <x-table.cell class="p-2">{{ ucfirst($transaction->status) }}</x-table.cell>                            
                            <x-table.cell class="p-2">{{ $transaction->created_at->format(config('app_settings.date_format.value').' '.config('app_settings.time_format.value')) }}</x-table.cell>
                           
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>
                @if($transactions && $transactions->total() > 10)
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
                            @if ($transactions)
                            <div id="datatable-bottom">
                                {{ $transactions->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @if( $transactions && $transactions->total() == 0)
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




@endpush
