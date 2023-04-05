<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search...">
        </div>
        <!--end::Search-->
   </div>

    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
        @can('add-tax')
            <a class="btn bg-gradient-dark mb-0 me-4" href="{{ route('add-tax') }}"><i
                class="material-icons text-sm">add</i>&nbsp;&nbsp;Add Tax</a>
        @endcan
    </div>
</div> 