<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model.debounce.500ms="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search User">
        </div>
        <!--end::Search-->
   </div>
 
    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            
            @if(strtolower($this->filter['role']) == 'driver')
                <select wire:model="filter.is_live"  class="filter-select selectpicker"  data-width="100%">
                    <option  class="optionSelect" value="">All availablity</option>
                    <option class="optionGroup" value="1">Online</option>
                    <option class="optionGroup" value="0">Offline</option>                     
                </select> 
                
                <select wire:model="filter.account_status"  class="filter-select selectpicker"  data-width="100%">
                    <option  class="optionSelect" value="">All Status</option>
                    <option class="optionGroup" value="waiting">Pending</option>
                    <option class="optionGroup" value="approved">Approved</option>
                    <option class="optionGroup" value="rejected">Rejected</option>
                    <option class="optionGroup" value="suspended">Suspended</option>                     
                </select>
            @endif

            @if ($this->filter['role'] != 'driver')                
            <select wire:model="filter.role"  class="filter-select selectpicker"  data-width="100%">
                <option  class="optionSelect" value="">All Role</option>
                @foreach ($roles as $role)
                    @if($role->name != 'Unverified')
                        <option class="optionGroup" value="{{ $role->name }}"> {{ $role->name }}</option>
                    @endif
                @endforeach                       
            </select>            
            @endif
        
           <select  wire:model="filter.status"  class="filter-select selectpicker"  data-width="100%">
                <option class="optionSelect" value="">All Status</option>
                <option class="optionGroup" value="1">Active</option>
                <option class="optionGroup" value="0">In Active</option>       
            </select>

            <button  wire:loading.attr="disabled" wire:click="export" class="btn btn-outline-secondary mb-0 py-2" data-type="csv"
                type="button" name="button"> <i class="material-icons text-sm">download</i>
                <span  wire:target="export"></span>
            </button>

        
            @can('add-user')
                <a class="btn bg-gradient-dark mb-0 me-4" href="{{ route('add-user', ['role' => $this->filter['role']]) }}"><i
                        class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New</a>
            @endcan
    
       
    </div>
</div> 