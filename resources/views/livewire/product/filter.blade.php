<div class="card-header">
    <div class="card-title">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 input-group input-group-outline">
            <input wire:model="search" type="text" class="form-control form-control-solid w-250px pl-5" placeholder="Search...">
        </div>
        <!--end::Search-->
   </div>

    <div class="card-toolbar flex-row-fluid justify-content-end gap-5 w-lg-75">
       
        @if(auth()->user()->hasRole('Admin'))
            <select wire:model="filter.store_id" class="filter-select select2 selectpicker w-15 px-3"  data-width="100%" id="roles">
                <option class="optionSelect" value="">All Store</option>
                @foreach ($stores as $store)
                    <option class="optionGroup" value="{{ $store->id }}"> {{ $store->name }}</option>
                @endforeach                       
            </select>
        @endif

        <select wire:model="filter.category_id" class="filter-select select2 selectpicker w-15 px-3"  data-width="100%" id="roles">
                <option class="optionSelect" value="">All Category</option>
                @foreach ($categories as $category)
                    <option class="optionGroup" value="{{ $category->id }}"> {{ $category->name }}</option>
                @endforeach                       
            </select>
    
        <button wire:loading.attr="disabled" wire:click="export" class="btn btn-outline-secondary mb-0 py-2" data-type="csv"
            type="button" name="button"> <i class="material-icons text-sm">download</i>
            <span  wire:target="export"></span>
        </button>

        @can('add-product') 
            <a class="btn bg-gradient-dark mb-0 me-4" href="{{ route('add-product') }}"><i
            class="material-icons text-sm">add</i>&nbsp;&nbsp;Add Product</a>
        @endcan   
    </div>
</div> 