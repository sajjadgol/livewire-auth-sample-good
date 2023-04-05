<?php

namespace App\Http\Livewire\Promotions;

use App\Models\Promotions\Promotion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($PromotionId)
    {
        $this->deleteId = $PromotionId;
       
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('Promotions.Yes delete it'),
                'cancelButtonText' => __('Promotions.No cancel'),
                'message' => __('Promotions.Are you sure'), 
                'text' => __('Promotions.If deleted you will not be able to recover this Promotion')
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
       Promotion::find($this->deleteId)->delete();

       $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('promotions.Promotion Delete Successfully')]);

    }

     /**
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($PromotionId, $status)
    {        
        $status = ( $status == 1 ) ? 0 : 1;
        Promotion::where('id', '=' , $PromotionId )->update(['status' => $status]); 
   }


    public function render()
    {
        return view('livewire.promotions.index' , [
            'promotions' => $this->loadData ? Promotion::searchMultiplePromotions($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }
}
