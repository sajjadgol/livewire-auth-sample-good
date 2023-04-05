<?php

namespace App\Http\Livewire\Product;

use Livewire\Component;
use App\Traits\GlobalTrait;
use Livewire\WithPagination;
use App\Exports\ProductsExport;
use App\Models\Products\Product;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Products\ProductImages;
use App\Models\Products\ProductCategories;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
    public $filter = [];
    public $CheckedProduct = [];
    public $deleteID='';
    public $productId = '';
    public $destroyMultiple =  [];
    public $deleteIDs =  [];
    public $productImage ;
    public $stores;
    public $categories = [];

    protected $listeners = ['remove', 'confirm','deleteCheckedProduct','removeMultiple'];

    protected $queryString = ['sortField', 'sortDirection',];
    protected $paginationTheme = 'bootstrap';
    public bool $loadData = false;
  
    public function init()
    {
         $this->loadData = true;
    }
    
 
    public function mount() {
        $this->perPage = config('app_settings.pagination_per_page.value');
        $this->categories = ProductCategories::whereStatus(1)->get();
        if(auth()->user()->hasRole('Provider')){
            $this->filter['is_provider'] = true;
            $this->filter['store_id'] = $this->getStoreId();
        }else{
            $this->stores = Store::withTranslation()->where('is_primary' , 0)->get();
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
        $this->deleteID = $productId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('product.Yes delete it'),
                'cancelButtonText' => __('product.No cancel'),
                'message' => __('product.Are you sure'), 
                'text' => __('product.If deleted you will not be able to recover this product')
            ]);
    }   
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        Product::find($this->deleteID)->delete();

        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('product.Product Delete Successfully')]);
    }
    
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyMultiple()
    {
        ;
        $this->dispatchBrowserEvent('swal:destroyMultiple', [
                'action' => 'deleteCheckedProduct',
                'type' => 'warning',  
                'confirmButtonText' => __('product.Yes delete it'),
                'cancelButtonText' => __('product.No cancel'),
                'message' => __('product.Are you sure'), 
                'text' => __('product.If deleted you will not be able to recover this products'),
            ]);
    }   

/**
     * Write code on Method
     *
     * @return response()
     */
    public function deleteCheckedProduct( )
    {
        Product::whereKey( $this->destroyMultiple )->delete();
        $this->destroyMultiple = [];
        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('product.Products Delete Successfully')]);
    }
    

    public function render()
    {
        return view('livewire.product.index',[
            'products' =>$this->loadData ? Product::withTranslation()->with(['productCategories', 'Productstore' , 'image'])->searchMultipleProduct(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }
            
    /**
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($productId, $status)
    {        
        $status = ( $status == 1 ) ? 0 : 1;
        Product::where('id', '=' , $productId )->update(['status' => $status]);      

   }

   /**
    * @return \Illuminate\Support\Collection
    *
    */

    public function export() 
    {   
        $products = Product::with(['productCategories', 'Productstore', 'image', 'productAddons'])->searchMultipleProduct(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->get();
        
        if(!$products->isEmpty()) {
            return Excel::download(new ProductsExport($products), 'products.xlsx');
        }
 
        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => 'No products data found to export.']);
    }

}
