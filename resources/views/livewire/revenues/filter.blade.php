<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search...">
        </div>
        <!--end::Search-->
   </div>

   <div class="card-toolbar flex-row-fluid justify-content-end gap-5 w-lg-75">
        <div class="filter-select selectpicker p-0 " wire:ignore 
            >
            <input id="from_date" name="from_date" wire:model="filter.from_date"  x-ref="picker" class="filter-select h-100 rounded-3 text-center px-0 border-1" type="text" placeholder="FROM DATE" />
        </div>

        <div class="filter-select selectpicker p-0" wire:ignore x-data x-init="flatpickr($refs.picker, {allowInput: false, enableTime: 'false', 
            dateFormat:  '{{config('app_settings.date_format.value')}}' });">
            <input id="to_date" name ="to_date" wire:model="filter.to_date" x-ref="picker" class="filter-select h-100 rounded-3 text-center px-0 border-1" type="text" placeholder="TO DATE" />
        </div>
  
        <select wire:model="filter.payment_status" class="filter-select selectpicker w-15 px-2 text-center"  data-width="100%" id="status">
            <option class="optionSelect" value="">All Status</option>
            @foreach($allPaymentStatus as $paymentStatus)
                <option class="optionGroup" value={{ $paymentStatus }}>{{  ucfirst($paymentStatus) }}</option>
            @endforeach
        </select>

        <select wire:model="filter.transaction_type" class="filter-select selectpicker  px-2"  data-width="100%" id="status">
            <option class="optionSelect" value="">All Transaction Type</option>
            @foreach($transcationTypes as $transcationType)
                <option class="optionGroup" value={{ $transcationType }}>{{  ucfirst($transcationType) }}</option>
            @endforeach
        </select>
    </div>
</div> 