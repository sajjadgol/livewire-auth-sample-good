<?php

namespace App\Http\Livewire\Product;

use App\Models\Product\AddonOption;
use App\Models\Products\Product AS ProductModel;
use App\Models\Products\ProductAddons;
use App\Models\Products\ProductCategories;
use App\Models\Products\ProductImages;
use App\Models\Stores\Store;
use App\Models\Tax\Tax;
use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;


class Edit extends Component
{

    use AuthorizesRequests;
    use WithFileUploads;
    use GlobalTrait;

    public ProductModel $product;
    public $image;
    public $product_addon_option_id = [] ;
    public $addonValue =  [] ;
    public $product_image = "";
    public $product_store ;

    protected $listeners = [
        'getAddonOptionForInput'
    ];

    protected function rules(){ 
        return [     
            'product.store_id' => 'required', 
            'product.name' => 'required|string', 
            'product.sku' => 'nullable',
            'product.descriptions' => 'nullable|max:1000',
            'product.status' => 'nullable|between:0,1',
            'product.categories_ids' => 'required',
            'product.tax_id' =>  'nullable',               
            'product.price' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'product.price_sale' => 'nullable|numeric', 
            'product.sale_start_date' => 'nullable',
            'product.sale_end_date' => 'nullable',
            'product_addon_option_id' => 'nullable',
        ];  
    }

    public function mount($id){

        //  Product translate
        $this->lang = request()->ref_lang;
        $this->languages = request()->language;

        $this->product = ProductModel::find($id);
        //  Product translate
        $this->product->name = isset($this->product->translate($this->lang)->name) ?  $this->product->translate($this->lang)->name: $this->product->translate(app()->getLocale())->name;
        $this->product->descriptions = isset($this->product->translate($this->lang)->descriptions) ? $this->product->translate($this->lang)->descriptions : $this->product->translate(app()->getLocale())->descriptions;
        //  Product translate
        $this->category = ProductCategories::whereStatus(1)->withTranslation()->get();
        $this->taxs = Tax::whereStatus(1)->get(); 
        $this->product_addon_option_id = ProductAddons::where('product_id', $id)->pluck('product_addon_option_id')->toArray();
    
        if(auth()->user()->hasRole('Admin')){
            $this->addonValue =   AddonOption::where('store_id' , $this->product->store_id)->withTranslation()->orderBy('addon_type', 'ASC')->whereStatus(1)->get();
            $this->product_store = Store::whereStatus(1)->whereIsOpen(1)->withTranslation()->Where('application_status', 'approved')->get();
        }else{
            $this->addonValue = AddonOption::where('store_id' , $this->getStoreId())->withTranslation()->whereStatus(1)->orderBy('addon_type', 'ASC')->get();
        }

    }

    public function hydrate()
    {
        $this->emit('select2');
    }

    public function getAddonOptionForInput($value){ 
        $this->product_addon_option_id = $value;
    }
    
    
    public function updated($propertyName){
         $this->validateOnly($propertyName);

    } 
  
    public function updatedStoreId(){ 
        $this->addonValue = AddonOption::where('store_id' , $this->store_id)->whereStatus(1)->orderBy('addon_type', 'ASC')->get();
    }    

    public function update(){

        $validated = $this->validate();      
        $validated['sale_start_date'] = !empty($validated['sale_start_date']) ? $validated['sale_start_date']: NULL; 
        $validated['sale_end_date'] = !empty($validated['sale_end_date']) ? $validated['sale_end_date']: NULL;
         $this->product->update(); 
        if($this->product_addon_option_id){
            ProductAddons::where('product_id',  $this->product->id)->delete();
            foreach($this->product_addon_option_id as $value){
                $addonOption[] = ['product_id'=>  $this->product->id , 'product_addon_option_id' => $value , 'created_at' => Carbon::now() , 'updated_at' => Carbon::now()];
            }
            !empty($addonOption) ? ProductAddons::insert( $addonOption ) : "";
        }
       
        if ($this->image) {
            
            $productImage = Image::make($this->image->getRealPath());
            $productImageName  = time() . '.' . $this->image->getClientOriginalExtension();
            Storage::disk(config('app_settings.filesystem_disk.value'))->put('products/original/'.$productImageName, (string) $productImage->encode());
            
            $productImage->resize(170, null, function ($constraint) {
                $constraint->aspectRatio(); 
                $constraint->upsize();                
            });

            Storage::disk(config('app_settings.filesystem_disk.value'))->put('products/thumbnails'.'/'.$productImageName, $productImage->stream());
            $productImagePath = 'products/thumbnails'.'/'.$productImageName;
            
            ProductImages::create([
                'product_id' => $this->product->id ,
                'image_path' => $productImagePath ,
            ]);
        }

     
        return redirect(route('product-management'))->with('status',__('product.Product successfully updated'));
    }


    public function render()
    {
        if ($this->lang != app()->getLocale()) {
            return view('livewire.product.edit-language');
        }
        return view('livewire.product.edit');
    }

    public function editTranslate()
    {
        $request =  $this->validate([
            'product.name' => 'required|string', 
            'product.descriptions'    => 'required',
        ]);
        
        $data = [
            $this->lang => $request['product']
        ];

        $product = ProductModel::findOrFail($this->product->id);
        $product->update($data);

        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => 'Product successfully updated.']);
    }


    public function updatedImage() {
        $validator = Validator::make(
            ['image' => $this->image],
            ['image' => 'mimes:jpg,jpeg,png|required|max:4096'],
        );

        if ($validator->fails()) {
            $this->reset('image');
            $this->setErrorBag($validator->getMessageBag());
            return redirect()->back();
        }
    }


}
