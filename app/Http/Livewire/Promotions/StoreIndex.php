<?php

namespace App\Http\Livewire\Promotions;

use App\Models\Promotions\Promotion;
use App\Models\Promotions\PromotionsStores;
use App\Traits\GlobalTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class StoreIndex extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    use GlobalTrait;

    public $search = '';
    public $sortField = 'start_date';
    public $sortDirection = 'desc';
    public $perPage = '' ;
    public $deleteId;
    public $joinId ;
    public $filter = [];
    public $today;
    public $faqId = '';
    public $promo_store ;
    protected $listeners = ['remove', 'confirm' , 'join'];
    protected $queryString = ['sortField' , 'sortDirection'];
    protected $paginationTheme = 'bootstrap';

    public $data = [] ;
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


    public function mount( )
    {      
         $this->perPage = config("commerce.pagination_per_page"); 
         $this->today =  Carbon::now();
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
    public function destroyConfirm($PromotionId)
    {
        $this->deleteId  = $PromotionId;
      
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('Yes remove it'),
                'cancelButtonText' => __('Promotions.No cancel'),
                'message' => __('Promotions.Are you sure'), 
                'text' => __('Promotions.If remove you will be able to join this Promotion again')
            ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
     
        PromotionsStores::wherePromotionId($this->deleteId)->whereStoreId($this->getStoreId())->delete();
        
        $this->dispatchBrowserEvent('alert', 
               ['type' => 'success',  'message' => __('Promotions.Promotion remove Successfully')]);
    }


     /**
     * Write code on Method
     *
     * @return response()
     */
    public function joinPromotion($PromotionId)
    {
        $this->joinId  = $PromotionId;
        $this->dispatchBrowserEvent('swal:joinPromotion', [
                'action' => 'join',
                'type' => 'warning',  
                'confirmButtonText' => __('Promotions.Yes Join it'),
                'cancelButtonText' => __('Promotions.No cancel'),
                'message' => __('Promotions.Are you sure'), 
                'text' => __('Promotions.You Want To Join This Promotion')
            ]);
    }

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function join()

    {       PromotionsStores::create([
                'promotion_id' => $this->joinId, 
                 'store_id' => $this->getStoreId(),
            ]);

            $this->dispatchBrowserEvent('swal:modal', [
                    'type' => 'success',  
                    'message' => __('Promotions.Promotion joined Successfully'), 
                    'text' => __('Promotions.It will apply on all your order')
                ]);
        
    }

 
    public function render()
    {
        $store_id = $this->getStoreId();

        return view('livewire.promotions.store-index'  , [
            'promotionsStore' =>$this->loadData ? Promotion::select('promotions.*','promotions_stores.promotion_id as joined_promotion_id')->leftJoin('promotions_stores',function ($query) use($store_id)
            {
                $query->on('promotions_stores.promotion_id','promotions.id');
                $query->where('promotions_stores.store_id', $store_id);
            
            })->where('discount_on', 'selected_store')->searchMultiplePromotionsStore(strtolower($this->search))->orderBy('promotions.'.$this->sortField, $this->sortDirection)->paginate($this->perPage) :[],
        ]);
    }
}
