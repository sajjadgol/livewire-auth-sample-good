<?php

namespace App\Http\Livewire\Stores;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stores\Store;
use App\Models\Stores\StoreType;
use App\Models\Stores\StoreOwners;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Events\InstantMailNotification;
use Mail;

class Index extends Component
{

    use AuthorizesRequests;
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $application_status ;
    public $filter = ["status" => null, "store_type" => null , "application_status"];
    public $deleteId = '';
    public $actionStatus = '';
    public $storeId = '';
    public $storeTypes;
    protected $listeners = ['remove', 'confirmApplication'];

    protected $queryString = ['sortField', 'sortDirection', 'application_status'];
    protected $paginationTheme = 'bootstrap';
    public bool $loadData = false;

  
    public function init()
    {
         $this->loadData = true;
    }


    public function mount() {  
        $this->filter['application_status'] = $this->application_status; 
        $this->filter['store_type'] = $this->storeTypes;   
        $this->perPage = config('commerce.pagination_per_page');
        $this->storeTypes = StoreType::withTranslation()->get();
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
    if($this->filter['application_status']== 'waiting') {
       
        $store=Store::where('is_primary' , 0 )->withTranslation()->searchMultipleStore(trim(strtolower($this->search)), $this->filter)->withAvg('OrderRating','rating')->withCount('OrderRating')->orderByTranslation($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }
    else{
        $store=Store::where('is_primary' , 0 )->withTranslation()->whereNotIn('application_status' , ['waiting'])->searchMultipleStore(trim(strtolower($this->search)), $this->filter)->withAvg('OrderRating','rating')->withCount('OrderRating')->orderByTranslation($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }    
        return view('livewire.store.index',[
            'stores' => $this->loadData ? $store : [],
        ]);
    }
 
    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    
    public function updatingFilter()
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
    public function destroyConfirm($storeId)
    {
        $this->deleteId  = $storeId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('store.Yes, delete it!'),
                'cancelButtonText' => __('store.No, cancel!'),
                'message' => __('store.Are you sure?'), 
                'text' => __( 'store.If deleted, you will not be able to recover this store data!')
            ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        Store::find($this->deleteId)->delete();

        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('store.Store Delete Successfully!')]);

    }    

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function applicationConfirm($storeId, $status)
    {   
        $this->storeId  = $storeId;
        $this->actionStatus = $status;

        $this->dispatchBrowserEvent('swal:confirmApplication', [
                'action' => 'confirmApplication',
                'type' => 'warning',  
                'confirmButtonText' =>  $status == 'approved' ? __('store.Yes, approve it!') : __('store.Yes reject it'),
                'cancelButtonText' => __('store.No, cancel!'),
                'message' => $status == 'approved' ? __('store.Are you approve?') : __('store.Are you Reject'), 
                 'text' =>  $status == 'approved' ?  __('store.If approved, store will be listed in store sections!') : __('store.If rejected, store will be not listed in store sections!')
            ]);

        $storeDatas =  StoreOwners::where("store_id", $storeId)->with(["user"])->get();
    
        if($storeDatas) {
            foreach($storeDatas as $storeData ) {
                event(new InstantMailNotification($storeData["user_id"], [
                    "code" =>  'forget_password',
                    "args" => [
                            'name' => $storeData["user"]["name"],
                    ]
                ]));
            }
        }
    }  

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function confirmApplication()
    {        
        Store::where('id', '=' , $this->storeId )->update(['application_status' => $this->actionStatus]);
       
        $this->dispatchBrowserEvent('swal:modal', [
            'type' => 'success',  
            'message' => $this->actionStatus == 'approved' ? __('store.Store Application Approved Successfully!') : __('store.Store Application Rejected'), 
        ]);
    }


    
    /**
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($storeId, $status)
    {        
        $status = ( $status == 1 ) ? 0 : 1;
        Store::where('id', '=' , $storeId )->update(['status' => $status]); 

        $storeDatas =  StoreOwners::where("store_id", $storeId)->with(["user"])->get();
       
        if($storeDatas) {
            foreach($storeDatas as $storeData ) {
                event(new InstantMailNotification($storeData["user_id"], [
                    "code" =>  'forget_password',
                    "args" => [
                          'name' => $storeData["user"]["name"],
                    ]
                ]));
            }
        }
   }


       /**
     * update searchable status
     *
     * @return response()
     */
    public function searchableConfirm($store)
    {        
        $is_searchable = ( $store['is_searchable'] == 1 ) ? 0 : 1;
        Store::where('id', '=' , $store['id']  )->update(['is_searchable' => $is_searchable]);     
        
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => __('store.Search Status Updated Successfully!')]);
   }

    /**
     * update featured status
     *
     * @return response()
     */
    public function featuresConfirm($store)
    {  
        $is_features = ( $store['is_features'] == 1 ) ? 0 : 1;
        Store::where('id', '=' , $store['id']  )->update(['is_features' => $is_features]);      
        
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => __('store.Top Restaurants Updated Successfully!')]);
   }

 

}
