<?php

namespace App\Http\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Posts\Post;
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
    public $pageId = '';
    protected $listeners = ['remove', 'refresh'];

    protected $defaultPages = ['about-us'];

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

    public function updatingPerPage()
    {
        $this->resetPage();
    }   


    public function sortBy($field){
        if($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }
    
    public function updatingSearch()
    {
        $this->gotoPage(1);
    }


    public function render()
    {
        return view('livewire.page.index',[
            'pages' => $this->loadData ?  Post::searchMultiplePage(trim(strtolower($this->search)))->orderByTranslation($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($pageId)
    {
        $this->deleteId  = $pageId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('pages.Yes delete it'),
                'cancelButtonText' => __('pages.No cancel'),
                'message' => __('pages.Are you sure'), 
                'text' => __('pages.If deleted you will not be able to recover this page data')
            ]);
    }

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        Post::find($this->deleteId)->delete();

        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('pages.Page Delete Successfully')]);
    }  

     /**
     * update page status
     *
     * @return response()
     */
    public function statusUpdate($pageId, $status)
    {      
        $status = ( $status == "published") ? 'unpublished' : 'published';
        Post::where('id', $pageId )->update(['status' => $status]);      

   }
}
