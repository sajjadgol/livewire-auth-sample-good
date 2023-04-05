@section('page_title')
    Promotions
@endsection
<div class="container-fluid py-4" wire:init="init">
    <div class="row mt-4">
        <x-alert></x-alert> 
        <div class="col-12">
            <div class="card custom-card">
                <!-- Card header -->
                 @include('livewire.promotions.filter')
                <!-- Card header end -->
            <div class="card-body pt-0">  
                <x-table>

                    <x-slot name="head">
                        <x-table.heading>Title
                        </x-table.heading>
                        <x-table.heading>Store
                        </x-table.heading>  
                        <x-table.heading>Target
                        </x-table.heading> 
                        <x-table.heading sortable wire:click="sortBy('start_date')"
                        :direction="$sortField === 'start_date' ? $sortDirection : null"> Start Date
                        </x-table.heading> 
                      
                        <x-table.heading sortable wire:click="sortBy('end_date')"
                        :direction="$sortField === 'end_date' ? $sortDirection : null"> End Date
                        </x-table.heading> 
                        <x-table.heading>Status
                        </x-table.heading>                      
                        <x-table.heading sortable wire:click="sortBy('created_at')"
                            :direction="$sortField === 'created_at' ? $sortDirection : null">
                            Creation Date
                        </x-table.heading>
                        
                        <x-table.heading>Actions</x-table.heading>
                         
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($promotions as $promotion)
                        <x-table.row wire:key="row-{{ $promotion->id }}">
                            <x-table.cell>{{ucfirst(str_replace('_', ' ',$promotion->title))}}</x-table.cell>     
                            <x-table.cell>{{ucfirst(str_replace('_', ' ',$promotion->discount_on))}}</x-table.cell>
                            <x-table.cell>{{ucfirst(str_replace('_', ' ',$promotion->target)) }}</x-table.cell> 
                            <x-table.cell>{{$promotion->start_date ? $promotion->start_date->format(config('app_settings.date_format.value')): '' }}</x-table.cell>
                            <x-table.cell>{{ $promotion->end_date ?$promotion->end_date->format(config('app_settings.date_format.value')) : ''  }}</x-table.cell>
                            <x-table.cell><div class="form-check form-switch ms-3">
                                <input class="form-check-input" wire:loading.attr="disabled"  type="checkbox" id="flexSwitchCheckDefault35"  wire:change="statusUpdate({{ $promotion->id }},{{ $promotion->status}})"
                                    @if($promotion->status) checked="" @endif>  
                            </div>
                            </x-table.cell>                            
                            <x-table.cell>{{ $promotion->created_at->format(config('app_settings.date_format.value')) }}</x-table.cell>
                            <x-table.cell>
                                 
                               <div class="dropdown dropup dropleft">
                                    <button class="btn bg-gradient-default" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">                                      
                                         <li><a class="dropdown-item"  data-original-title="Edit" title="Edit" href="{{ route('edit-promotion', $promotion) }}">Edit</a></li>
                                         <li><a class="dropdown-item text-danger"  data-original-title="Remove" title="Remove" wire:click="destroyConfirm({{ $promotion->id }})">Delete</a></li>
                                    </ul>
                                </div>
                           
                            </x-table.cell>
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>
             @if($promotions && $promotions->total() > 10)
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
                            @if ($promotions)
                            <div id="datatable-bottom">
                                {{ $promotions->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
              @endif
                @if($promotions && $promotions->total() == 0)
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
 