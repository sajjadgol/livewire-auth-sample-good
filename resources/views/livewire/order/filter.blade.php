<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search...">
        </div>
        <!--end::Search-->
   </div>

    <div class="card-toolbar flex-row-fluid justify-content-end gap-5 w-lg-75">
        <div class="filter-select selectpicker p-0 " wire:ignore x-data x-init="flatpickr($refs.picker, {allowInput: false, enableTime: 'false',
            dateFormat:  '{{config('app_settings.date_format.value')}}' });">
            <input id="from_date" name="from_date" wire:model="filter.from_date"  x-ref="picker" class="filter-select h-100 rounded-3 text-center px-0 border-1" type="text" placeholder="FROM DATE" />
        </div>

        <div class="filter-select selectpicker p-0" wire:ignore x-data x-init="flatpickr($refs.picker, {allowInput: false, enableTime: 'false', 
            dateFormat:  '{{config('app_settings.date_format.value')}}' });">
            <input id="to_date" name ="to_date" wire:model="filter.to_date" x-ref="picker" class="filter-select h-100 rounded-3 text-center px-0 border-1" type="text" placeholder="TO DATE" />
        </div>

        @if (!in_array($filter['orderStatus'],['completed','pending']))
            <select wire:model="filter.order_status" class="filter-select selectpicker w-15 px-2"  data-width="100%" id="status">
                <option class="optionSelect" value="">All Status</option>
                @foreach($allOrderStatus as $orderStatus)
                    <option class="optionGroup" value={{ $orderStatus }}>{{  ucfirst(str_replace('_', ' ', $orderStatus)) }}</option>
                @endforeach
            </select>
        @endif

        @if(auth()->user()->hasRole('Admin'))
            <select wire:model="filter.store_id" class="filter-select selectpicker w-15 px-3"  data-width="100%" id="roles">
                <option class="optionSelect" value="">All Store</option>
                @foreach ($stores as $store)
                    <option class="optionGroup" value="{{ $store->id }}"> {{ $store->name }}</option>
                @endforeach                       
            </select>
        @endif
         <button  wire:loading.attr="disabled" wire:click="export" class="btn btn-outline-secondary mb-0 py-2" data-type="csv"
            type="button" name="button"><i class="material-icons text-sm">download</i>
            <span  wire:target="export"></span>
        </button> 
    </div>
</div> 

