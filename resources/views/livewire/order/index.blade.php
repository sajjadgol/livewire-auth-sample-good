@section('page_title')
    @if ($filter['order_status'] == '')
       All Order 
    @else
       {{ ucfirst($filter['orderStatus']) }} Order
    @endif
@endsection
<div class="container-fluid py-4" wire:init="init">
  
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">                
                <!-- Card header -->
                @include('livewire.order.filter')
                <!-- Card header end -->
                <div class="card-body pt-0">
                    <x-table class="table table-flush">
                        <x-slot name="head">
                            <x-table.heading> Order Number
                            </x-table.heading>
                            <x-table.heading> Date
                            </x-table.heading>
                            <x-table.heading> Status
                            </x-table.heading>
                            <x-table.heading> Store
                            </x-table.heading>
                            <x-table.heading> Customer
                            </x-table.heading>                           
                            <x-table.heading> Amount
                            </x-table.heading>
                            <x-table.heading>Actions</x-table.heading>
                        
                        </x-slot>
                        <x-slot name="body">
                            @foreach ($orders as $order)
                            <x-table.row wire:key="row-{{$order->id }}">
                                <x-table.cell><a href="{{ route('order-details', $order) }}">#{{ $order->order_number }}</a></x-table.cell>
                                <x-table.cell>{{ $order->created_at->format(config('app_settings.date_format.value').' '.config('app_settings.time_format.value')) }}</x-table.cell>
                                <x-table.cell><span class="text-{{$statusLabels[$order->order_status]}}"> {{  ucfirst(str_replace('_', ' ', $order->order_status)) }}</span></x-table.cell>
                                <x-table.cell>{{ $order->store->name }}</x-table.cell>
                                <x-table.cell> 
                                    @role('Admin')<a href="{{ route('view-user',  $order->user) }}">{{ $order->user->name }}@endrole</a>
                                    @role('Provider'){{ $order->user->name }}@endrole
                                </x-table.cell>
                                <x-table.cell>{{ \Utils::ConvertPrice($order->total_amount) }}</x-table.cell>
                                <x-table.cell> 
                                    @can('order-details')                               
                                    <a class="btn" href="{{ route('order-details', $order) }}" data-bs-toggle="tooltip"
                                        data-bs-original-title="Preview">
                                        <i class="material-icons text-secondary position-relative text-lg">visibility</i>
                                    </a>
                                    @endcan
                                    @role('Admin')
                                        <a  class="btn"  href="javascript:;" data-bs-toggle="tooltip"
                                            data-bs-original-title="Delete"  wire:click="destroyConfirm({{ $order->id }})">
                                            <i class="material-icons text-secondary position-relative text-lg">delete</i>
                                        </a>   
                                    @endrole                                                                 
                                </x-table.cell>
                            </x-table.row>
                            
                            @endforeach
                        </x-slot>
                    </x-table>
                    @if($orders && $orders->total() > 10)
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
                                @if ($orders)
                                <div id="datatable-bottom">
                                    {{ $orders->links() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($orders && $orders->total() == 0)
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
<!--   Core JS Files   -->
@push('js')
<script src="{{ asset('assets') }}/js/plugins/flatpickr.min.js"></script> 
<script src="{{ asset('assets') }}/js/plugins/datatables.js"></script>
  
@endpush
