<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search...">
        </div>
        <!--end::Search-->
   </div>

    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
        
        <select  wire:model="filter.status" class="filter-select selectpicker"  data-width="100%">
             <option class="optionSelect" value="">All Status</option>
            <option  class="optionGroup" value="1">Active</option>
            <option class="optionGroup" value="0">In Active</option>        
        </select>

        @if ($application_status != 'waiting')
            <select wire:model="filter.application_status" class="filter-select selectpicker"  data-width="100%">
                <option  class="optionSelect" value="">All Account Status</option>
                <option  class="optionGroup"  value="approved">Approved</option>
                <option  class="optionGroup"  value="suspended">Suspend</option>  
            </select>
       @endif

       <select wire:model="filter.store_type" class="filter-select selectpicker">
        <option  class="optionSelect" value="">All Type</option>
        @foreach ($storeTypes as $storeType)
            <option  class="optionGroup"  value="{{ $storeType->name }}"> {{ $storeType->name }}</option>
        @endforeach                       
    </select>
         
        @can('add-store') 
            <a class="btn bg-gradient-dark mb-0 me-4" href="{{ route('add-store') }}"><i
                    class="material-icons text-sm">add</i>&nbsp;&nbsp;Add Store</a>
        @endcan
    </div>
</div> 