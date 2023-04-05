<?php

namespace App\Http\Livewire\World\State;

use App\Models\Worlds\State;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $state = '';
    public $filter = [];
    public $deleteId = '';
    public $stateId = '';
    protected $listeners = ['remove', 'confirm'];
    protected $queryString = ['sortField', 'sortDirection'];
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
        return view('livewire.world.state.index',[
            'states' =>$this->loadData ? State::searchMultipleState(trim(strtolower($this->search)))->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }

    
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($stateId)
    {
        $this->deleteId  = $stateId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('world.Yes, delete it!'),
                'cancelButtonText' => __('world.No, cancel!'),
                'message' => __('world.Are you sure?'), 
                'text' => __('world.If deleted, you will not be able to recover this State!')
            ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        State::find($this->deleteId)->delete();
        
        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('world.State Delete Successfully!')]);

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
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($stateId, $status)
    {        
        $status = ( $status == 1 ) ? 0 : 1;
        State::where('id', '=' ,$stateId)->update(['status' => $status]);      

   }
}
