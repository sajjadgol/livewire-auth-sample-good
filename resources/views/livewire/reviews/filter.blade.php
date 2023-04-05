<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search...">
        </div>
        <!--end::Search-->
   </div>

    <div class="card-toolbar flex-row-fluid justify-content-end gap-5 w-lg-75">
        <div class="filter-select selectpicker p-0" wire:ignore x-data x-init="flatpickr($refs.picker, {allowInput: false, enableTime: 'false',
             dateFormat:  '{{config('app_settings.date_format.value')}}' });">
            <input id="from_date" name="from_date" wire:model="filter.from_date"  x-ref="picker" class="filter-select h-100 rounded-3 text-center px-0 border-1" type="text" placeholder="FROM DATE" />
        </div>
       
        <div class="filter-select selectpicker p-0" wire:ignore x-data x-init="flatpickr($refs.picker, {allowInput: false, enableTime: 'false', 
            dateFormat:  '{{config('app_settings.date_format.value')}}' });">
            <input id="to_date" name ="to_date" wire:model="filter.to_date" x-ref="picker" class="filter-select h-100 rounded-3 text-center px-0 border-1" type="text" placeholder="TO DATE" />
        </div>


        @if(auth()->user()->hasRole('Admin'))
            <select wire:model="filter.store_id" class="filter-select selectpicker w-15 px-3" id="roles">
                <option class="optionSelect" value="">All Store</option>
                @foreach ($stores as $store)
                    <option class="optionGroup" value="{{ $store->id }}"> {{ $store->name }}</option>
                @endforeach                       
            </select>
              
            <select wire:model="filter.receiver_id" class="filter-select selectpicker" id="roles">
                <option class="optionSelect" value="">All Driver</option>
                @foreach ($drivers as $driver)
                    <option class="optionGroup" value="{{ $driver->id }}"> {{ $driver->name }}</option>
                @endforeach                       
            </select>
        @endif
    </div>
</div> 