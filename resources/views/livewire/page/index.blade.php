{{-- Page Title --}}
@section('page_title')
    @lang("components/pages.page_title")
@endsection

<x-core.container wire:init="init">
    <x-loder />

    {{-- Alert message - alert-success, examples- alert-danger, alert-warning, alert-primary  --}}
    <x-slot name="alert">
        @if (session('status'))
            <x-alert class="alert-success">{{ Session::get('status') }}</x-alert>
        @endif
    </x-slot>

    {{-- Card --}}
    <x-core.card class="custom-card">
        <x-slot name="header">

            {{-- Filter row with seachable --}}
            <x-table.container-filter-row seachable />

                <x-core.card-toolbar>
                    {{-- Header Bulk actions  --}}
                    <x-dropdown label="{{ __('components/pages.Actions') }}">
                        <x-dropdown.item wire:click="exportSelected">
                            @lang('components/pages.Export')
                        </x-dropdown.item>

                        
                        <x-dropdown.item wire:click="destroyMultiple()" class="dropdown-item text-danger">
                            @lang('components/pages.Delete')
                        </x-dropdown.item>
                    </x-dropdown>


                     {{-- Filter Action  --}}
                     <x-dropdown class="px-2 py-3 dropdown-md" label="{{ __('component.Filter') }}">
                        <x-input.group inline for="filters.status" label="{{ __('components/pages.Status') }}">
                            <x-input.select wire:model="filters.status" placeholder="{{ __('components/pages.Any Status') }}">
                                <option value="published"> @lang('components/pages.Published') </option>
                                <option value="draft">@lang('components/pages.Draft') </option>
                                <option value="unpublished">@lang('components/pages.Unpublished') </option>
                        </x-input.select>
                        </x-input.group>
                    
                        {{-- Date renge filter --}}
                        <x-table.filter-date-input />

                        <x-button.link wire:click="resetFilters" class="mt-2"> @lang('component.Reset Filters') </x-button.link>

                    </x-dropdown>

                    {{--  Hide & show columns dropdown --}}
<x-table.view-columns/>

                    @can('add-user')
                        {{-- button with icon,href --}}
                        <x-table.button.add icon href="{{ route('add-page') }}"> @lang('components/pages.Add Page')</x-table.button.add>
                    @endcan

                </x-core.card-toolbar>
        </x-slot>
        <x-slot name="body">

            {{--  Table with perPage and pagination --}}
            <x-table perPage total="{{ $pages->total() }}" id="page-list" paginate="{{ $pages->links() }}">
                <x-slot name="head">

                    {{-- Select-all checkbox  --}}
                    <x-table.heading-selected total="{{ $pages->total() }}" />

                    {{-- Dynamic columns heading --}}
                    <x-table.heading columns />
                    <x-table.heading> @lang('components/pages.Actions') </x-table.heading>

                </x-slot>
                <x-slot name="body">
                    {{-- Select records count (which rows checkbox checked) --}}
                    <x-table.row-selected-count selectPage="{{ $selectPage }}" selectedAll="{{ $selectAll }}"
                        count="{{ $pages->count() }}" total="{{ $pages->total() }}" />

                        {{-- Table row --}}
                        @forelse ($pages as $page)
                        <x-table.row wire:key="row-{{ $page->id }}">

                            {{-- Select checkbox --}}
                            <x-table.cell-selected value="{{ $page->id }}" />
                        
                            <x-table.cell column="title" href="">{{ $page->title }}</x-table.cell>
                            <x-table.cell column="slug" href="">{{ $page->slug }}</x-table.cell>

                            <x-table.cell-date column="created_at">{{ $page->created_at }}</x-table.cell-date>

                            <x-table.cell-switch column="status" status="{{ $page->status == 'published' }}"
                                wire:change="statusUpdate({{ $page->id }},{{ $page->status }})">
                            </x-table.cell-switch>

                            <x-table.cell-lang :data="json_decode($page)" route="edit-page"/>
                        
                            {{-- Action , examples- edit, view, delete  --}}
                            <x-table.cell-dropdown>
                                @can('edit-page')
                                <x-table.dropdown-item class="dropdown-item" 
                                    title="{{ __('components/pages.Edit') }}" href="{{ route('edit-page', $page) }}">
                                    {{ __('components/pages.Edit') }}
                                </x-table.dropdown-item>
                                @endcan
                                @if(!in_array($page->slug, $this->defaultPages) )  
                                <x-table.dropdown-item class="dropdown-item text-danger" 
                                    title="{{ __('components/pages.Delete') }}" wire:click="destroyConfirm({{ $page->id }})">
                                    {{ __('components/pages.Delete') }}
                                </x-table.dropdown-item>
                                @endif
                            </x-table.cell-dropdown>
                        </x-table.row>
                    @empty
                        <x-table.no-record-found />
                    @endforelse
                </x-slot>
            </x-table>
        </x-slot>
    </x-core.card>
</x-core.container>
