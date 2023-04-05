<?php

namespace App\Http\Livewire\Product\Category;

use App\Models\Products\ProductCategories;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Traits\GlobalTrait;

class Create extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;
    use GlobalTrait;
    
    public $image;
    public $name = '';
    public $status = '';
    public $description = '';

    protected $rules = [
        'name' => 'required|max:255|unique:product_categories_translations,name',
        'description' => 'required|max:1000',
        'status' => 'nullable|between:0,1',
    ];


    public function updated($propertyName){

        $this->validateOnly($propertyName);

    } 

    public function store() {
        $this->validate();
        $categoryImagePath ="";
        if($this->image){
            $categoryImage = Image::make($this->image->getRealPath());
            $categoryImageName  = time() . '.' . $this->image->getClientOriginalExtension();
            Storage::disk(config('app_settings.filesystem_disk.value'))->put('ProductCategory'.'/'.$categoryImageName, (string) $categoryImage->encode());
            
            $categoryImage->resize(170,null, function ($constraint) {
                $constraint->aspectRatio();    
                $constraint->upsize();             
            });

            Storage::disk(config('app_settings.filesystem_disk.value'))->put('thumbnails'.'/'.$categoryImageName, $categoryImage->stream());
            $categoryImagePath = 'thumbnails'.'/'.$categoryImageName;
        }

        $store_id = null;
        if(!auth()->user()->hasRole('Admin')){
            $store_id = $this->getStoreId();
        }
       
        ProductCategories::create([
            'store_id' => $store_id,
            'image' => $categoryImagePath,
            'name' => $this->name,
            'description' => $this->description,
            'status'=> $this->status ? 1:0,
       ]);
         
        return redirect(route('product-category-management'))->with('status',__('product.ProductCategory successfully created'));
    }

    public function updatedImage() {
        $validator = Validator::make(
            ['image' => $this->image],
            ['image' => 'nullable|mimes:jpg,jpeg,png,bmp,tiff|max:4096'],
        );

        if ($validator->fails()) {
            $this->reset('image');
            $this->setErrorBag($validator->getMessageBag());
            return redirect()->back();
        }
    }

    public function render()
    {
        return view('livewire.product.category.create');
    }
}
