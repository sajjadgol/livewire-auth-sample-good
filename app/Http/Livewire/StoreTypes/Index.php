<?php

namespace App\Http\Livewire\StoreTypes;

use Livewire\Component;
use App\Models\Stores\StoreType;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{   
    use AuthorizesRequests;
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $deleteId = '';
    public $storeTypeId = '';

    protected $listeners = ['remove'];
    protected $queryString = ['sortField', 'sortDirection',];
    protected $paginationTheme = 'bootstrap';

    public bool $loadData = false;
  
    public function init()
    {
         $this->loadData = true;
    }

    public function mount() {
        $this->perPage = config('commerce.pagination_per_page');
    }

    public function sortBy($field){
        if($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        return view('livewire.store-types.index', [
            'storeTypes' =>$this->loadData ? StoreType::withTranslation()->searchMultipleStoreType(trim(strtolower($this->search)))->orderByTranslation($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($storeTypeId)
    {
        $this->deleteId  = $storeTypeId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('storeType.Yes, delete it!'),
                'cancelButtonText' => __('storeType.No, cancel!'),
                'message' => __('storeType.Are you sure?'), 
                'text' => __('storeType.If deleted, you will not be able to recover this store type data!')
            ]);
    }

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }


    public function updatingPerPage()
    {
        $this->resetPage();
    }   

    
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        StoreType::find($this->deleteId)->delete();
       
        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('storeType.Store Type Delete Successfully!')]);
    }  


    /**
     * update store type  status
     *
     * @return response()
     */
    public function statusUpdate($storeTypeId, $status)
    {        
        $status = ( $status == 1 ) ? 0 : 1;
        StoreType::where('id', '=' , $storeTypeId )->update(['status' => $status]);      
    }
}
