@section('page_title')
    Sliders
@endsection
<div class="container-fluid py-4" wire:init="init">
    <div class="row mt-4">
        <div class="col-12">
            <x-alert></x-alert> 
            <div class="card custom-card">
                 <!-- Card header -->
                 @include('livewire.slider.filter')
                 <!-- Card header end -->
             <div class="card-body pt-0"> 
                <x-table>

                    <x-slot name="head">
                        <x-table.heading sortable wire:click="sortBy('name')"
                        :direction="$sortField === 'name' ? $sortDirection : null">  Name
                        </x-table.heading> 
                       <x-table.heading>
                            Start Date Time	
                        </x-table.heading>
                        <x-table.heading>
                            End Date Time
                        </x-table.heading>
                        <x-table.heading>Status
                        </x-table.heading>       
                        
                        <x-table.heading>Actions
                        </x-table.heading>
                         
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($sliders as $slider)
                     
                        <x-table.row wire:key="row-{{ $slider->id }}">
                            <x-table.cell><a href="{{ route('edit-slider', $slider) }}">{{ $slider->name }}</a></x-table.cell>   
                            <x-table.cell>{{ Carbon\Carbon::parse($slider->start_date_time)->format(config('app_settings.date_format.value').' '.config('app_settings.time_format.value'))	 }}</x-table.cell>
                            <x-table.cell>{{ Carbon\Carbon::parse($slider->end_date_time)->format(config('app_settings.date_format.value').' '.config('app_settings.time_format.value'))  }}</x-table.cell>
                            <x-table.cell><div class="form-check form-switch ms-3">
                                <input class="form-check-input" wire:loading.attr="disabled"  type="checkbox" id="flexSwitchCheckDefault35"  wire:change="statusUpdate({{ $slider->id }},{{  $slider->status}})"
                                    @if( $slider->status) checked="" @endif>
                            </div>
                            </x-table.cell>


                            <x-table.cell>
                                <div class="dropdown dropup dropleft">
                                    <button class="btn bg-gradient-default" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('edit-slider')
                                            <li><a class="dropdown-item"  data-original-title="Edit" title="Edit" href="{{ route('edit-slider', $slider) }}">Edit</a></li>
                                        @endcan
                                        <li><a class="dropdown-item text-danger"  data-original-title="Remove" title="Remove" wire:click="destroyConfirm({{$slider->id }})">Delete</a></li>
                                    </ul>
                                </div>                           
                            </x-table.cell>
                            
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>
                @if($sliders && $sliders->total() > 10)
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
                            @if ($sliders)
                            <div id="datatable-bottom">
                                {{ $sliders->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
               @endif
                @if($sliders && $sliders->total() == 0)
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
 