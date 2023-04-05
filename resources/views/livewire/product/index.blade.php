@section('page_title')
    All Products
@endsection
<div class="container-fluid py-4 bg-gray-200" wire:init="init">
    <div class="row">
        <div class="col-12">
            <x-alert></x-alert> 
            <div class="card custom-card">                
                <!-- Card header --> 
                    @include('livewire.product.filter')
                <!-- Card header end -->               
                {{-- <div class="card-header pb-0">
                    <div class="d-lg-flex"> 
                        <div>
                            @if ($destroyMultiple)
                            <a class="btn btn-link fst-normal lh-1 pe-3 mb-0 ms-auto w-25 w-md-auto me-12" wire:click="destroyMultiple()">
                                Delete
                            </a>
                            @endif
                        </div>
                    </div>
                </div> --}}
               
                <div class="card-body pt-0">
                    <x-table wire:loading.table>

                        <x-slot name="head">
                            {{-- <x-table.heading>
                            </x-table.heading> --}}
                            <x-table.heading  > Product
                            </x-table.heading>
                            <x-table.heading  > Category
                            </x-table.heading>
                            <x-table.heading sortable wire:click="sortBy('price')"
                            :direction="$sortField === 'price' ? $sortDirection : null">Price
                            </x-table.heading>
                           @if(auth()->user()->hasRole('Admin'))
                            <x-table.heading>Store
                            </x-table.heading>
                            @endif
                            <x-table.heading sortable wire:click="sortBy('status')"
                            :direction="$sortField === 'status' ? $sortDirection : null">Status
                            </x-table.heading>
                            <x-table.heading>
                                {{ implode(' | ',config('translatable.locales')) }}
                            </x-table.heading>     
                            <x-table.heading>Actions</x-table.heading>
                        
                        </x-slot>
                            
                        <x-slot name="body">
                            @foreach ($products as $product)
                            <x-table.row wire:key="row-{{$product->id }}">
                                {{-- <x-table.cell><div class="d-flex">
                                 <div class="form-check my-auto">
                                     <input class="form-check-input" type="checkbox" id="customCheck1" value="{{ $product->id }}" wire:model="destroyMultiple">
                                 </div></x-table.cell> --}}
                                <x-table.cell class="position-relative text-sm font-weight-normal align-middle">                                     
                                    <div class="d-flex">
                                       @if ($product->image)
                                        <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url($product->image->image_path) }} " alt="picture"
                                            class="avatar avatar-sm mt-2">
                                        @else
                                        <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url(config('app_settings.product_image.value')) }}" alt="avatar"
                                            class="avatar avatar-sm mt-2">
                                        @endif
                                        <h6 class="ms-3 my-auto"><a href="{{ route('edit-product', $product) }}">{{ $product->name }}</a></h6>                                      
                                    </div>
                                </x-table.cell>

                                
                                <x-table.cell>@if($product->productCategories) {{ $product->productCategories->name}}  @endif</x-table.cell>
                                <x-table.cell> {{ \Utils::ConvertPrice($product->price) }}</x-table.cell>                               
                                @if(auth()->user()->hasRole('Admin'))
                                    <x-table.cell><a  href="{{ route('edit-store', $product->Productstore) }}">{{$product->Productstore->name}} </a></x-table.cell> 
                                @endif                                
                                <x-table.cell>
                                    <div class="form-check form-switch ms-3">
                                        <input class="form-check-input" type="checkbox" wire:loading.attr="disabled"  id="flexSwitchCheckDefault35"  wire:change="statusUpdate({{ $product->id }},{{ $product->status}})"
                                            @if($product->status) checked="" @endif>
                                    </div>
                                </x-table.cell>
                                <x-table.cell> 
                                    @foreach (config('translatable.locales') as $locale)
                                    <a href="@if(app()->getLocale() != $locale) {{ route('edit-product', ['id' => $product->id,'ref_lang' => $locale]) }}  @else {{ route('edit-product', $product) }} @endif" class="" data-original-title="{{ $locale }}" title="{{ $locale }}"> 
                                        <span class="material-symbols-outlined text-md">
                                         {{ in_array($locale, array_column(json_decode($product->translations, true), 'locale')) ? 'edit' : 'add' }}
                                       </span>
                                    </a> 
                                    @endforeach
                                </x-table.cell>

                                <x-table.cell>                                       
                                   <div class="dropdown dropup dropleft">
                                        <button class="btn bg-gradient-default" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-icons">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton"> 
                                            @can('edit-product') 
                                            <li><a class="dropdown-item"  data-original-title="Edit" title="Edit"  href="{{ route('edit-product', $product) }}">Edit</a></li>
                                            @endcan
                                            <li><a class="dropdown-item text-danger"  data-original-title="Remove" title="Remove" wire:click="destroyConfirm({{ $product->id }})">Delete</a></li>
                                        </ul>
                                    </div>
                                                                  
                             
                                </x-table.cell>
                            </x-table.row>
                            @endforeach
                        </x-slot>
                    </x-table>
                    @if($products && $products->total() > 10)
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
                                @if ($products)
                                <div id="datatable-bottom">
                                    {{ $products->links() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($products && $products->total() == 0)
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
<script src="{{ asset('assets') }}/js/plugins/datatables.js"></script>
  
@endpush
