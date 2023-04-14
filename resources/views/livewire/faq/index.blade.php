{{-- Page Title --}}
@section('page_title')
    @lang("components/faq.page_title")
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
                    <x-dropdown label="{{ __('components/faq.Actions') }}">
                        <x-dropdown.item wire:click="exportSelected">
                            @lang('components/faq.Export')
                        </x-dropdown.item>

                        
                        <x-dropdown.item wire:click="destroyMultiple()" class="dropdown-item text-danger">
                            @lang('components/faq.Delete')
                        </x-dropdown.item>
                    </x-dropdown>


                     {{-- Filter Action  --}}
                     <x-dropdown class="px-2 py-3 dropdown-md" label="{{ __('component.Filter') }}">
                        <x-input.group inline for="filters.status" label="{{ __('components/faq.Status') }}">
                            <x-input.select wire:model="filters.status" placeholder="{{ __('components/faq.Any Status') }}">
                                <option value="1"> @lang('components/faq.Active') </option>
                                <option value="0"> @lang('components/faq.Inactive') </option>
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
                        <x-table.button.add icon href="{{ route('add-store-type') }}" />
                    @endcan

                </x-core.card-toolbar>
        </x-slot>
        <x-slot name="body">

            {{--  Table with perPage and pagination --}}
            <x-table perPage total="{{ $faqs->total() }}" id="users-list" paginate="{{ $faqs->links() }}">
                <x-slot name="head">

                    {{-- Select-all checkbox  --}}
                    <x-table.heading-selected total="{{ $faqs->total() }}" />

                    {{-- Dynamic columns heading --}}
                    <x-table.heading columns />
                    <x-table.heading> @lang('components/faq.Actions') </x-table.heading>

                </x-slot>
                <x-slot name="body">
                    {{-- Select records count (which rows checkbox checked) --}}
                    <x-table.row-selected-count selectPage="{{ $selectPage }}" selectedAll="{{ $selectAll }}"
                        count="{{ $faqs->count() }}" total="{{ $faqs->total() }}" />

                        {{-- Table row --}}
                        @forelse ($faqs as $faq)
                        <x-table.row wire:key="row-{{ $faq->id }}">

                            {{-- Select checkbox --}}
                            <x-table.cell-selected value="{{ $faq->id }}" />
                        
                            <x-table.cell column="title" href="">{{ $faq->title }}</x-table.cell>
                            <x-table.cell column="role_type" href="">{{ $faq->role_type }}</x-table.cell>

                            <x-table.cell-date column="created_at">{{ $faq->created_at }}</x-table.cell-date>

                            <x-table.cell-switch column="status" status="{{ $faq->status }}"
                                wire:change="statusUpdate({{ $faq->id }},{{ $faq->status }})">
                            </x-table.cell-switch>

                            <x-table.cell-lang :data="json_decode($faq)" route="edit-faq"/>
                        
                            {{-- Action , examples- edit, view, delete  --}}
                            <x-table.cell-dropdown>
                                @can('edit-faq')
                                <x-table.dropdown-item class="dropdown-item" 
                                    title="{{ __('components/faq.Edit') }}" href="{{ route('edit-faq', $faq) }}">
                                    {{ __('components/faq.Edit') }}
                                </x-table.dropdown-item>
                                @endcan
                                <x-table.dropdown-item class="dropdown-item text-danger" 
                                    title="{{ __('components/faq.Delete') }}" wire:click="destroyConfirm({{ $faq->id }})">
                                    {{ __('components/faq.Delete') }}
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
