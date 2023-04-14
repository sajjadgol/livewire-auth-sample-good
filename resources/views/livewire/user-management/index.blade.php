{{-- Page Title --}}
@section('page_title')
    @lang("components/user.page_title")
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
                    <x-dropdown label="{{ __('components/user.Actions') }}">
                        <x-dropdown.item wire:click="exportSelected">
                            @lang('components/user.Export')
                        </x-dropdown.item>

                        
                        <x-dropdown.item wire:click="destroyMultiple()" class="dropdown-item text-danger">
                            @lang('components/user.Delete')
                        </x-dropdown.item>
                    </x-dropdown>


                     {{-- Filter Action  --}}
                     <x-dropdown class="px-2 py-3 dropdown-md" label="{{ __('component.Filter') }}">
                        <x-input.group inline for="filters.status" label="{{ __('components/user.Status') }}">
                            <x-input.select wire:model="filters.status" placeholder="{{ __('components/user.Any Status') }}">
                                <option value="1"> @lang('components/user.Active') </option>
                                <option value="0"> @lang('components/user.Inactive') </option>
                            </x-input.select>
                        </x-input.group>
                    
                        <x-input.group inline for="filter-date-min" label="{{ __('components/user.Role') }}">
                            <x-input.select wire:model="filters.role" placeholder="{{ __('components/user.Any Role') }}">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"> {{ $role->name }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>
                    
                        {{-- Date renge filter --}}
                        <x-table.filter-date-input />

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
                        <x-table.button.add icon href="{{ route('add-user', ['role' => $this->filters['role']]) }}" />
                    @endcan

                </x-core.card-toolbar>
        </x-slot>
        <x-slot name="body">

            {{--  Table with perPage and pagination --}}
            <x-table perPage total="{{ $users->total() }}" id="users-list" paginate="{{ $users->links() }}">
                <x-slot name="head">

                    {{-- Select-all checkbox  --}}
                    <x-table.heading-selected total="{{ $users->total() }}" />

                    {{-- Dynamic columns heading --}}
                    <x-table.heading columns />
                    <x-table.heading> @lang('components/user.Actions') </x-table.heading>

                </x-slot>
                <x-slot name="body">
                    {{-- Select records count (which rows checkbox checked) --}}
                    <x-table.row-selected-count selectPage="{{ $selectPage }}" selectedAll="{{ $selectAll }}"
                        count="{{ $users->count() }}" total="{{ $users->total() }}" />

                        {{-- Table row --}}
                        @forelse ($users as $user)
                        <x-table.row wire:key="row-{{ $user->id }}">

                            {{-- Select checkbox --}}
                            <x-table.cell-selected value="{{ $user->id }}" />
                        
                            <x-table.cell-avatar column="profile_photo" url="{{ $user->profile_photo }}"
                                class="position-relative" />
                        
                            <x-table.cell column="name" href="">{{ $user->name }}</x-table.cell>

                            <x-table.cell-phone column="phone" code="{{ $user->country_code }}"
                                value="{{ $user->phone }}" />

                            <x-table.cell-switch column="status" status="{{ $user->status }}"
                                wire:change="statusUpdate({{ $user->id }},{{ $user->status }})">
                            </x-table.cell-switch>

                            <x-table.cell column="role"> {{ $user->getRoleNames()->implode(',') }}</x-table.cell>

                            <x-table.cell-date column="created_at">{{ $user->created_at }}</x-table.cell-date>

                            {{-- Action , examples- edit, view, delete  --}}
                            <x-table.cell-dropdown>
                                <x-table.dropdown-item class="dropdown-item" 
                                    title="{{ __('components/user.Edit') }}" href="{{ route('view-user', $user) }}">
                                    {{ __('components/user.Edit') }}
                                </x-table.dropdown-item>
                                <x-table.dropdown-item class="dropdown-item text-danger" 
                                    title="{{ __('components/user.Delete') }}" wire:click="destroyConfirm({{ $user->id }})">
                                    {{ __('components/user.Delete') }}
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
