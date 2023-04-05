@section('page_title')
    Reviews
@endsection
<div class="container-fluid py-4" wire:init="init">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-card">
                <!-- Card header -->
                @include('livewire.reviews.filter')
                <!-- Card header end -->
               
            <div class="card-body pt-0">
                
                <x-table>
                    <x-slot name="head">
                        <x-table.heading>Order Number
                        </x-table.heading> 
                        <x-table.heading>Reviewer
                        </x-table.heading> 
                        <x-table.heading>Reviewee
                        </x-table.heading> 
                        <x-table.heading sortable wire:click="sortBy('rating')"
                            :direction="$sortField === 'rating' ? $sortDirection : null">Rating
                        </x-table.heading> 
                        <x-table.heading>Creation Date
                        </x-table.heading> 
                        <x-table.heading>Actions
                        </x-table.heading>
                    </x-slot>
                    <x-slot name="body">
                        @foreach ($orderReviews as $orderReview)
                        <x-table.row wire:key="row-{{ $orderReview->id }}">
                            <x-table.cell class="p-2"><a href="{{ route('order-details', $orderReview->order) }}">#{{ $orderReview->order->order_number }}</a></x-table.cell> 
                            <x-table.cell class="p-2">@role('Admin')<a href="{{ route('view-user', $orderReview->sender) }}">{{ $orderReview->sender->name }}</a>@endrole
                            @role('Provider'){{ $orderReview->sender->name }}@endrole
                        </x-table.cell> 
                            <x-table.cell class="p-2">{{ $orderReview->rating_for == 'customer' || $orderReview->rating_for == 'driver' ?  $orderReview->receiver->name: $orderReview->order->store->name}}
                              <em>({{ $orderReview->rating_for }})</<em>
                            </x-table.cell> 
                            <x-table.cell class="p-2">
                                {{ $orderReview->rating }}
                                @if($orderReview->remark) 
                                   <a data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $orderReview->remark }}" data-container="body" data-animation="true"><span class="material-symbols-outlined">reviews</span></a> 
                                @endif
                            </x-table.cell>
                            <x-table.cell>{{ $orderReview->created_at->format(config('app_settings.date_format.value'))   }}</x-table.cell>
                           
                            <x-table.cell> @can('review-delete')    
                                    <div class="dropdown dropup dropleft">
                                    <a class="btn" data-original-title="Remove" title="Remove" wire:click="destroyConfirm({{$orderReview['id'] }})"> <span class="material-symbols-outlined">
                                    delete
                                    </span></a> 
                                </div> @endcan
                            </x-table.cell> 
                           
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>
             @if($orderReviews && $orderReviews->total() > 10)
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
                            @if ($orderReviews)
                            <div id="datatable-bottom">
                                {{ $orderReviews->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
              @endif
                @if($orderReviews && $orderReviews->total() == 0)
                    <div>
                        <p class="text-center">No records found!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-loder ></x-loder>
</div>
@push('js') 
<script src="{{ asset('assets') }}/js/plugins/flatpickr.min.js"></script>


@endpush
