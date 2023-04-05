<?php

namespace App\Http\Livewire\Tags;

use App\Models\Tags\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{   
    use AuthorizesRequests;
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;
    
    public $deleteId = '';
    
    protected $listeners = ['remove'];
    protected $queryString = ['sortField', 'sortDirection'];
    protected $paginationTheme = 'bootstrap';
    public bool $loadData = false;
  
    public function init()
    {
         $this->loadData = true;
    }

    public function sortBy($field){
        if($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function mount() {
        $this->perPage = config('commerce.pagination_per_page');
    }

    public function render()
    {
       return view('livewire.tags.index', [
          'tags' =>$this->loadData ? Tag::withTranslation()->searchMultipleTag(trim(strtolower($this->search)))->orderByTranslation($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
       ]);

    }

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        
        Tag::find($this->deleteId)->delete();
        
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => __('tag.Tag Delete Successfully!')]);

    } 

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($tagId)
    {
        $this->deleteId  = $tagId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('tag.Yes, delete it!'),
                'cancelButtonText' => __('tag.No, cancel!'),
                'message' => __('tag.Are you sure?'), 
                'text' => __('tag.If deleted, you will not be able to recover this tag data!')
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
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($tagId, $status)
    {        
        $status = ( $status == 1 ) ? 0 : 1;
        Tag::where('id', '=' ,$tagId )->update(['status' => $status]);      

   }

}
