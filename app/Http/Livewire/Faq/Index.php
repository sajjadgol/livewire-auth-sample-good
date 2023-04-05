<?php

namespace App\Http\Livewire\Faq;

use App\Models\Faq\Faq;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Route;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = '' ;
    public $deleteId;
    public $faqId = '';
    protected $listeners = ['remove', 'confirm'];
    protected $queryString = ['sortField' , 'sortDirection'];
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
    public function mount()
    {
         $this->perPage = config("commerce.pagination_per_page");
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
    public function destroyConfirm($faqId)
    {
        $this->deleteId  = $faqId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('faq.Yes delete it'),
                'cancelButtonText' => __('faq.No cancel'),
                'message' => __('faq.Are you sure'), 
                'text' => __('faq.If deleted you will not be able to recover this FAQ')
            ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        faq::find($this->deleteId)->delete();
        
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => __('faq.FAQ Delete Successfully')]);
    }

     /**
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($faqId, $status)
    {        
        $status = ( $status == 1 ) ? 0 : 1;
        faq::where('id', '=' , $faqId )->update(['status' => $status]);      

   }

    public function render()
    {
        return view('livewire.faq.index', [
            'faqs' => $this->loadData ? Faq::withTranslation()->searchMultipleFaqs(trim(strtolower($this->search)))->orderByTranslation($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }
  
}
