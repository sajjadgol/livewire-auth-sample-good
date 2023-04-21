{{-- Page Title --}}
@section('page_title')
    @lang("components/storeType.page_title")
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
                    <x-dropdown label="{{ __('components/storeType.Actions') }}">
                        <x-dropdown.item wire:click="exportSelected">
                            @lang('components/storeType.Export')
                        </x-dropdown.item>

                        
                        <x-dropdown.item wire:click="destroyMultiple()" class="dropdown-item text-danger">
                            @lang('components/storeType.Delete')
                        </x-dropdown.item>
                    </x-dropdown>


                     {{-- Filter Action  --}}
                     <x-dropdown class="px-2 py-3 dropdown-md" label="{{ __('component.Filter') }}">
                        <x-input.group inline for="filters.status" label="{{ __('components/storeType.Status') }}">
                            <x-input.select wire:model="filters.status" placeholder="{{ __('components/storeType.Any Status') }}">
                                <option value="1"> @lang('components/storeType.Active') </option>
                                <option value="0"> @lang('components/storeType.Inactive') </option>
                            </x-input.select>
                        </x-input.group>
                    
                        {{-- Date renge filter --}}

                        <x-button.link wire:click="resetFilters" class="mt-2"> @lang('component.Reset Filters') </x-button.link>

                    </x-dropdown>

                    {{--  Hide & show columns dropdown --}}
                    <x-dropdown>
                        <x-slot name="label">
                            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium mui-datatables-i4bv87-MuiSvgIcon-root" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ViewColumnIcon"><path d="M14.67 5v14H9.33V5h5.34zm1 14H21V5h-5.33v14zm-7.34 0V5H3v14h5.33z"></path></svg>
                        </x-slot>
                        @foreach ($columns as $column)
                            <x-dropdown.item>
                                <x-input.checkbox label="{{ Str::ucfirst($column['label']) }}"
                                    wire:model="selectedColumns" value="{{ $column['field'] }}" />
                            </x-dropdown.item>
                        @endforeach
                    </x-dropdown>

                    @can('add-user')
                        {{-- button with icon,href --}}
                        <x-table.button.add icon href="{{ route('add-store-type') }}" >@lang('components/storeType.Add Store')</x-table.button.add>
                    @endcan

                </x-core.card-toolbar>
        </x-slot>
        <x-slot name="body">

            {{--  Table with perPage and pagination --}}
            <x-table perPage total="{{ $storeTypes->total() }}" id="users-list" paginate="{{ $storeTypes->links() }}">
                <x-slot name="head">

                    {{-- Select-all checkbox  --}}
                    <x-table.heading-selected total="{{ $storeTypes->total() }}" />

                    {{-- Dynamic columns heading --}}
                    <x-table.heading columns />
                    <x-table.heading> @lang('components/storeType.Actions') </x-table.heading>

                </x-slot>
                <x-slot name="body">
                    {{-- Select records count (which rows checkbox checked) --}}
                    <x-table.row-selected-count selectPage="{{ $selectPage }}" selectedAll="{{ $selectAll }}"
                        count="{{ $storeTypes->count() }}" total="{{ $storeTypes->total() }}" />

                        {{-- Table row --}}
                        @forelse ($storeTypes as $storeType)
                        <x-table.row wire:key="row-{{ $storeType->id }}">

                            {{-- Select checkbox --}}
                            <x-table.cell-selected value="{{ $storeType->id }}" />
                        
                            <x-table.cell column="name" href="">{{ $storeType->name }}</x-table.cell>

                            <x-table.cell-date column="created_at">{{ $storeType->created_at }}</x-table.cell-date>

                            <x-table.cell-switch column="status" status="{{ $storeType->status }}"
                                wire:change="statusUpdate({{ $storeType->id }},{{ $storeType->status }})">
                            </x-table.cell-switch>

                            <x-table.cell-lang :data="json_decode($storeType)" route="edit-store-type"/>
                        
                            {{-- Action , examples- edit, view, delete  --}}
                            <x-table.cell-dropdown>
                                <x-table.dropdown-item class="dropdown-item" 
                                    title="{{ __('components/storeType.Edit') }}" href="{{ route('edit-store-type', $storeType) }}">
                                    {{ __('components/storeType.Edit') }}
                                </x-table.dropdown-item>
                                <x-table.dropdown-item class="dropdown-item text-danger" 
                                    title="{{ __('components/storeType.Delete') }}" wire:click="destroyConfirm({{ $storeType->id }})">
                                    {{ __('components/storeType.Delete') }}
                                </x-table.dropdown-item>
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
