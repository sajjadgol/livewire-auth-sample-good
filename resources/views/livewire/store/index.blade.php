@section('page_title')
    @if($this->application_status == 'waiting')
        Unverified Stores
    @else
        Stores
    @endif
@endsection

<div class="container-fluid py-2"  wire:init="init">
    <div class="row mt-4">
        <div class="col-12">
            <x-alert></x-alert> 

            <div class="card custom-card">               
                <!-- Card header -->
                    @include('livewire.store.filter')
                <!-- Card header end -->
                <div class="card-body pt-0">  
                  <x-table>
                    <x-slot name="head">
                        <x-table.heading> Logo
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('name')"
                            :direction="$sortField === 'name' ? $sortDirection : null">Store Name
                        </x-table.heading>
                       
                        <x-table.heading>Email
                        </x-table.heading>
                        <x-table.heading>Phone
                        </x-table.heading> 
                        <x-table.heading>Status
                        </x-table.heading>

                        <x-table.heading>
                            {{ implode(' | ',config('translatable.locales')) }}
                        </x-table.heading>     

                        <x-table.heading sortable wire:click="sortBy('created_at')"
                            :direction="$sortField === 'created_at' ? $sortDirection : null">
                            Creation Date
                        </x-table.heading>                     
                        <x-table.heading>Actions</x-table.heading>
                    
                    </x-slot>
                    
                    <x-slot name="body">
                        @foreach ($stores as $store)
                        <x-table.row wire:key="row-{{ $store->id }}">
                            <x-table.cell class="position-relative text-sm font-weight-normal align-middle">
                                @if($store->logo_path)
                                <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url($store->logo_path)}}" alt="picture"
                                    class="avatar avatar-sm mt-2">
                                @else
                                <img src="{{ asset('assets') }}/img/default-avatar.png" alt="avatar"
                                    class="avatar avatar-sm mt-2">
                                @endif
                            </x-table.cell>
 
 
                            <x-table.cell class="text-break text-wrap text-sm font-weight-normal align-middle"><a href="{{ route('edit-store' , $store) }}">{{ $store->name }}</a></x-table.cell>
 
                       
                            <x-table.cell>{{ $store->email }} </x-table.cell>
                            <x-table.cell>{{ $store->phone }}</x-table.cell>
                            <x-table.cell> 
                               
                            <div class="form-check form-switch ms-3">
 
                                    <input class="form-check-input" wire:loading.attr="disabled"  type="checkbox" id="flexSwitchCheckDefault35"  wire:change="statusUpdate({{ $store->id }},{{ $store->status}})"
 
                                        @if($store->status) checked="" @endif>
                                </div>
                                <br>
                                @if($store->application_status =='suspended')
                                <div class="text-xxs text-warning text-center" >Suspended</div>
                                @endif
                            
                            </x-table.cell>
                            
                            <x-table.cell> 
                                @foreach (config('translatable.locales') as $locale)
                                <a href="@if(app()->getLocale() != $locale) {{ route('edit-store', ['id' => $store->id,'ref_lang' => $locale]) }}  @else {{ route('edit-store', $store) }} @endif" class="" data-original-title="{{ $locale }}" title="{{ $locale }}"> 
                                    <span class="material-symbols-outlined text-md">
                                     {{ in_array($locale, array_column(json_decode($store->translations, true), 'locale')) ? 'edit' : 'add' }}
                                   </span>
                                </a> 
                                @endforeach
                            </x-table.cell>
                            <x-table.cell>{{  $store->created_at->format(config('app_settings.date_format.value'))  }}</x-table.cell>
                            <x-table.cell>
                              
                            @if($this->application_status == 'waiting')
                                <button type="button" class="btn btn-success btn-link" data-original-title="Approve" title="Approve"
                                wire:click="applicationConfirm({{ $store->id }}, 'approved')">
                                    <i class="material-icons">check</i>
                                    <div class="ripple-container"></div>
                                </button>
                                <button type="button" class="btn btn-danger btn-link" data-original-title="Reject" title="Reject"
                                wire:click="applicationConfirm({{ $store->id }}, 'rejected')">
                                    <i class="material-icons">close</i>
                                    <div class="ripple-container"></div>
                                </button>
                            @else 
                                <div class="dropdown dropup dropleft">
                                    <button class="btn bg-gradient-default" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('edit-store')
                                            <li><a class="dropdown-item"  data-original-title="Searchable" title="Searchable" wire:click="searchableConfirm({{ $store }})">{{ $store->is_searchable ? 'Remove to Searchable' : 'Mark as Searchable'}}</a></li>
                                        @endcan
                                        @can('edit-store')
                                            <li><a class="dropdown-item"  data-original-title="Top Restaurants" title="Top Restaurants" wire:click="featuresConfirm({{ $store }})">{{ $store->is_features ? 'Remove to Top Restaurants' : 'Mark as Top Restaurants'}}</a></li>
                                        @endcan

                                        @can('revenues-management')
                                            <li><a class="dropdown-item"  data-original-title="user-revenue" title="user-revenue" href="{{ route('revenues-management',['type' => 'store','id' => $store->id]) }}">Revenues</a></li>
                                        @endcan

                                        @can('edit-store')
                                            <li><a class="dropdown-item"  data-original-title="Edit" title="Edit" href="{{ route('edit-store' , $store) }}">Edit</a></li>
                                        @endcan
                                        @if ($store->is_primary == 0)
                                            <li><a class="dropdown-item text-danger"  data-original-title="Remove" title="Remove"  wire:click="destroyConfirm({{ $store->id }})">Delete</a></li>
                                        @endif 
                                    </ul>
                                </div>


                            @endif  
                            </x-table.cell>
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>

                @if($stores && $stores->total() > 10)
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
                            @if ($stores)
                            <div id="datatable-bottom">
                                {{ $stores->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($stores && $stores->total() == 0)
                    <div>
                        <p class="text-center">No records found!</p>
                    </div>
                @endif
                
            </div>
            </div>
        </div>
    </div>
    
    <x-loder></x-loder>
 
</div>
 
