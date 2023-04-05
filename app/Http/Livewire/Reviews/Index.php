<?php

namespace App\Http\Livewire\Reviews;

use App\Models\OrderReviews\OrderReview;
use App\Models\Stores\Store;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\GlobalTrait;


class Index extends Component
{   
    use AuthorizesRequests;
    use WithPagination;
    use GlobalTrait;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $reviewId = '';
    public $filter = ['store_id' => null, 'receiver_id' => null, 'created_at' => null ];
    public $stores;
    public $customers;
    public $drivers;
    protected $listeners = ['remove'];
    protected $queryString = ['sortField', 'sortDirection'];
    protected $paginationTheme = 'bootstrap';
    
    public $to_date ;
    public $from_date ;
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
        $this->stores = Store::withTranslation()->where('is_primary' , 0)->get();

        $this->drivers  = User::role('Driver')->get();
        
        $this->filter["from_date"] = $this->from_date;
        $this->filter["to_date"] = $this->to_date;

        if(auth()->user()->hasRole('Provider')){
            $this->filter['is_provider'] = true;
            $this->filter['store_id'] = $this->getStoreId();
        }
    }
   

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }


    public function updatingPerPage()
    {
        $this->resetPage();
    }   

    public function updatingStores()
    {
        $this->gotoPage(1);
    }

    public function updatingDrivers()
    {
        $this->gotoPage(1);
    }


     /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($reviewId)
    {
        $this->reviewId  = $reviewId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('review.Yes delete it'),
                'cancelButtonText' => __('review.No cancel'),
                'message' => __('review.Are you sure'), 
                'text' => __('review.If deleted you will not be able to recover this review data')
            ]);
    }

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        OrderReview::find($this->reviewId)->delete();
        
        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('review.Review Delete Successfully!')]);
    }  

    public function render()
    {
        return view('livewire.reviews.index',[
            'orderReviews' => $this->loadData ? OrderReview::with(['order', 'order.store', 'sender', 'receiver'])->searchMultipleOrderReview(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }
}
