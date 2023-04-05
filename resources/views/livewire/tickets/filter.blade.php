<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search...">
        </div>
        <!--end::Search-->
   </div>

   <div class="card-toolbar flex-row-fluid justify-content-end gap-5 w-lg-75">
         <select wire:model="filter.status" class="filter-select selectpicker w-15 px-3" data-width="100%" id="status">
            <option class="optionSelect" value="">All Status</option>
            <option class="optionGroup" value="open">Open</option>            
            <option class="optionGroup" value="completed">Completed</option>
            <option class="optionGroup" value="rejected">Rejected</option>
       </select>
   </div>
</div> 