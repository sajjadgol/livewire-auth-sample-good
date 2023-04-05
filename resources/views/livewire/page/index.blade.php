@section('page_title')
    Pages
@endsection
<div class="container-fluid py-4" wire:init="init">
    <div class="row mt-4">
        <div class="col-12">
            <x-alert></x-alert>
            <div class="card custom-card">
                <!-- Card header -->
                @include('livewire.page.filter')
                <!-- Card header end -->
             <div class="card-body pt-0"> 
                <x-table>

                    <x-slot name="head">
                        <x-table.heading sortable wire:click="sortBy('title')"
                            :direction="$sortField === 'title' ? $sortDirection : null"> Title
                        </x-table.heading>   
                        <x-table.heading> Slug
                        </x-table.heading>                       
                        <x-table.heading sortable wire:click="sortBy('created_at')"
                            :direction="$sortField === 'created_at' ? $sortDirection : null">
                            Creation Date
                        </x-table.heading>
                        <x-table.heading>
                        Published
                        </x-table.heading>
                        <x-table.heading>
                            {{ implode(' | ',config('translatable.locales')) }}
                        </x-table.heading>     
                        <x-table.heading>Actions</x-table.heading>
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($pages as $page)
                        <x-table.row wire:key="row-{{ $page->id }}">
                            <x-table.cell>{{ $page->title }}</x-table.cell>  
                            <x-table.cell>{{ $page->slug }}</x-table.cell>                                                  
                            <x-table.cell>{{ $page->created_at->format(config('app_settings.date_format.value')) }}</x-table.cell>
                            <x-table.cell> 
                                @if(!in_array($page->slug, $this->defaultPages) )  
                                    <div class="form-check form-switch ms-3">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault35" wire:loading.attr="disabled"   wire:change="statusUpdate({{ $page->id }}, '{{ $page->status}}')"
                                            @if($page->status == 'published') checked="" @endif>
                                    </div>
                                @endif   
                            </x-table.cell> 

                            <x-table.cell> 
                                @foreach (config('translatable.locales') as $locale)
                                <a href="@if(app()->getLocale() != $locale) {{ route('edit-page', ['id' => $page->id,'ref_lang' => $locale]) }}  @else {{ route('edit-page', $page) }} @endif" class="" data-original-title="{{ $locale }}" title="{{ $locale }}"> 
                                    <span class="material-symbols-outlined text-md">
                                     {{ in_array($locale, array_column(json_decode($page->translations, true), 'locale')) ? 'edit' : 'add' }}
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
                                        @can('edit-page')
                                            <li><a class="dropdown-item"  data-original-title="Edit" title="Edit" href="{{ route('edit-page', $page) }}">Edit</a></li>
                                        @endcan
                                        @if(!in_array($page->slug, $this->defaultPages) )  
                                            <li><a class="dropdown-item text-danger"  data-original-title="Remove" title="Remove" wire:click="destroyConfirm({{ $page->id }})">Delete</a></li>
                                        @endif 
                                    </ul>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>
                @if($pages && $pages->total() > 10)
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
                            @if ($pages)
                            <div id="datatable-bottom">
                                {{ $pages->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @if($pages && $pages->total() == 0)
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
 