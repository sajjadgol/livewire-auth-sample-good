<?php

namespace App\Http\Livewire\Order;

use Livewire\Component;
use App\Models\Order\Order;
use App\Traits\GlobalTrait;
use App\Models\Stores\Store;
use Livewire\WithPagination;
use App\Exports\OrdersExport;
use App\Constants\OrderStatus;
use Illuminate\Support\Facades\DB;
use App\Constants\OrderStatusLabel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{  
    use AuthorizesRequests;
    use WithPagination;
    use GlobalTrait;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = '';
    public $currency = '';
    public $filter = ['store_id' => null, 'created_at' => null, 'order_status' => null];
    public $deleteId = '';
    public $orderId = '';
    public $orderStatus = '';
    public $statusLabels;
    public $allOrderStatus = '';
    public $stores;
    public $to_date ;
    public $from_date ;
    protected $listeners = ['remove', 'confirm'];

    protected $queryString = ['sortField', 'sortDirection', 'orderStatus'];
    protected $paginationTheme = 'bootstrap';
    public bool $loadData = false;
  
    public function init()
    {
         $this->loadData = true;
    }
    
 
    public function mount() {
        $this->perPage = config('commerce.pagination_per_page');
        $this->currency = config('commerce.price');
        $this->filter['orderStatus'] = $this->orderStatus;
        $this->filter['order_status'] = $this->orderStatus;
        $this->filter["from_date"] = $this->from_date;
        $this->filter["to_date"] = $this->to_date;
        $this->stores = Store::withTranslation()->where('is_primary' , 0)->get();

        if(auth()->user()->hasRole('Provider')){
            $this->filter['is_provider'] = true;
            $this->filter['store_id'] = $this->getStoreId();
        }
        
        $orderStatusLabelConstant = new OrderStatusLabel();
        $this->statusLabels = $orderStatusLabelConstant->getConstants();

        $orderStatusConstant = new OrderStatus();
        $this->allOrderStatus = $orderStatusConstant->getConstants();
        
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

   

      /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($productId)
    {
        $this->deleteId  = $productId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('orders.Yes delete it'),
                'cancelButtonText' => __('orders.No cancel'),
                'message' => __('orders.Are you sure'), 
                'text' => __('orders.If deleted you will not be able to recover this product')
            ]);
    }   
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        order::find($this->deleteId)->delete();
        
        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('orders.Order Delete Successfully')]);
    }
 
    public function render()
    {   
        return view('livewire.order.index',[
             'orders' => $this->loadData ?  Order::searchMultipleOrder(trim(strtolower($this->search)), $this->filter )->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }
            
    /**
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($orderId, $status)
    {        
        
    }

    /**
    * @return \Illuminate\Support\Collection
    *
    */
   public function export() 
   {   
        $orders= Order::with(['TransactionHistory'])->searchMultipleOrder(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->get();
        
        if(!$orders->isEmpty()) {
            return Excel::download(new OrdersExport($orders), 'orders.xlsx');
        }

        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => 'No orders data found to export.']);
   }

}
