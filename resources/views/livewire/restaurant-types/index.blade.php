{{-- Page Title --}}
@section('page_title')
    @lang("components/restaurantType.page_title")
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
                    <x-dropdown label="{{ __('components/restaurantType.Actions') }}">
                        <x-dropdown.item wire:click="exportSelected">
                            @lang('components/restaurantType.Export')
                        </x-dropdown.item>

                        
                        <x-dropdown.item wire:click="destroyMultiple()" class="dropdown-item text-danger">
                            @lang('components/restaurantType.Delete')
                        </x-dropdown.item>
                    </x-dropdown>


                     {{-- Filter Action  --}}
                     <x-dropdown class="px-2 py-3 dropdown-md" label="{{ __('component.Filter') }}">
                        <x-input.group inline for="filters.status" label="{{ __('components/restaurantType.Status') }}">
                            <x-input.select wire:model="filters.status" placeholder="{{ __('components/restaurantType.Any Status') }}">
                                <option value="1"> @lang('components/restaurantType.Active') </option>
                                <option value="0"> @lang('components/restaurantType.Inactive') </option>
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
                        <x-table.button.add icon href="{{ route('add-restaurant-type') }}" >@lang('components/restaurantType.Add Restaurant')</x-table.button.add>
                    @endcan

                </x-core.card-toolbar>
        </x-slot>
        <x-slot name="body">

            {{--  Table with perPage and pagination --}}
            <x-table perPage total="{{ $restaurantTypes->total() }}" id="users-list" paginate="{{ $restaurantTypes->links() }}">
                <x-slot name="head">

                    {{-- Select-all checkbox  --}}
                    <x-table.heading-selected total="{{ $restaurantTypes->total() }}" />

                    {{-- Dynamic columns heading --}}
                    <x-table.heading columns />
                    <x-table.heading> @lang('components/restaurantType.Actions') </x-table.heading>

                </x-slot>
                <x-slot name="body">
                    {{-- Select records count (which rows checkbox checked) --}}
                    <x-table.row-selected-count selectPage="{{ $selectPage }}" selectedAll="{{ $selectAll }}"
                        count="{{ $restaurantTypes->count() }}" total="{{ $restaurantTypes->total() }}" />

                        {{-- Table row --}}
                        @forelse ($restaurantTypes as $restaurantType)
                        <x-table.row wire:key="row-{{ $restaurantType->id }}">

                            {{-- Select checkbox --}}
                            <x-table.cell-selected value="{{ $restaurantType->id }}" />
                        
                            <x-table.cell column="name" href="">{{ $restaurantType->name }}</x-table.cell>

                            <x-table.cell-date column="created_at">{{ $restaurantType->created_at }}</x-table.cell-date>

                            <x-table.cell-switch column="status" status="{{ $restaurantType->status }}"
                                wire:change="statusUpdate({{ $restaurantType->id }},{{ $restaurantType->status }})">
                            </x-table.cell-switch>

                            <x-table.cell-lang :data="json_decode($restaurantType)" route="edit-restaurant-type"/>
                        
                            {{-- Action , examples- edit, view, delete  --}}
                            <x-table.cell-dropdown>
                                <x-table.dropdown-item class="dropdown-item" 
                                    title="{{ __('components/restaurantType.Edit') }}" href="{{ route('edit-restaurant-type', $restaurantType) }}">
                                    {{ __('components/restaurantType.Edit') }}
                                </x-table.dropdown-item>
                                <x-table.dropdown-item class="dropdown-item text-danger" 
                                    title="{{ __('components/restaurantType.Delete') }}" wire:click="destroyConfirm({{ $restaurantType->id }})">
                                    {{ __('components/restaurantType.Delete') }}
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
