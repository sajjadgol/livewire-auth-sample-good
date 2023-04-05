<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Order\Transaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\GlobalTrait;
use App\Constants\OrderPaymentStatus;
use App\Models\Stores\Store;

class Index extends Component
{

    use AuthorizesRequests;
    use WithPagination;
    use GlobalTrait;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = '';
    public $product = '';
    public $currency = '';
    public $filter = ['status' => null, 'store_id'=>null, 'created_at'=> null];
    public $transactionId = '';
    public $allPaymentStatus;
    public $stores;
    public $to_date ;
    public $from_date ;
    public bool $loadData = false;
  
    public function init()
    {
         $this->loadData = true;
    }
     
    

    protected $listeners = ['remove', 'confirm','statusUpdateChange','removeMultiple' ];

    protected $queryString = ['sortField', 'sortDirection'];
    protected $paginationTheme = 'bootstrap';
    
 
    public function mount() {
        $this->filter["from_date"] = $this->from_date;
        $this->filter["to_date"] = $this->to_date;
        if(auth()->user()->hasRole('Provider')){
            $this->filter['is_provider'] = true;
            $this->filter['store_id'] = $this->getStoreId();
            
        }
       
        $this->perPage = config('commerce.pagination_per_page');

        $OrderPaymentStatus = new OrderPaymentStatus();
        $this->allPaymentStatus = $OrderPaymentStatus->getConstants();

        $this->stores = Store::withTranslation()->where('is_primary' , 0)->get();
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

    public function updatingFilter()
    {
        
        $this->gotoPage(1);
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
        return view('livewire.transaction.index' ,[
            'transactions' =>$this->loadData ? Transaction::with(['user','order'])->searchMultipleTransaction(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }
}
