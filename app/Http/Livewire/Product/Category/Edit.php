<?php

namespace App\Http\Livewire\Product\Category;

use App\Models\Products\ProductCategories;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;


    public $image;
    public $category;
    public $lang = '';
    public $languages = '';

    protected function rules(){
        $category = isset($this->category->translate($this->lang)->id)  ? ','.$this->category->translate($this->lang)->id : null;

        return [
            'category.name' => 'required|max:255|unique:App\Models\Products\ProductCategoriesTranslation,name'.$category,
            'category.description' => 'required|max:1000',
            'category.status' => 'nullable|between:0,1',
        ];
    }

    public function mount($id) {
        $this->category = ProductCategories::find($id);
        $this->lang = request()->ref_lang;
        $this->languages = request()->language;
       
        $this->category->name = isset($this->category->translate($this->lang)->name) ?  $this->category->translate($this->lang)->name: $this->category->translate(app()->getLocale())->name;
        $this->category->description = isset($this->category->translate($this->lang)->description) ? $this->category->translate($this->lang)->description : $this->category->translate(app()->getLocale())->description;

    }

    public function updated($propertyName){

        $this->validateOnly($propertyName);
    }

    public function edit(){

        $this->validate();
        if ($this->image){
            $categoryImage = Image::make($this->image->getRealPath());
            $categoryImageName  = time() . '.' . $this->image->getClientOriginalExtension();
            Storage::disk(config('app_settings.filesystem_disk.value'))->put('ProductCategory'.'/'.$categoryImageName, (string) $categoryImage->encode());
            
            $categoryImage->resize(170, null, function ($constraint) {
                $constraint->aspectRatio();    
                $constraint->upsize();             
            });
            
            Storage::disk(config('app_settings.filesystem_disk.value'))->put('thumbnails'.'/'.$categoryImageName, $categoryImage->stream());
            $categoryImagePath = 'thumbnails'.'/'.$categoryImageName;

            $this->category->image = $categoryImagePath ;
            
        }
        
        $this->category->update();

        return redirect(route('product-category-management'))->with('status', __('product.Product Category successfully updated'));
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


    public function editTranslate() {

        $category = isset($this->category->translate($this->lang)->id)  ? ','.$this->category->translate($this->lang)->id : null;
       
        $request =  $this->validate([
            'category.name' => 'required|max:255|unique:App\Models\Products\ProductCategoriesTranslation,name'.$category,
            'category.description' => 'required|max:1000',
        ]);

        $data = [
            $this->lang => $request['category']
        ];
      
        $category= ProductCategories::findOrFail($this->category->id);
        $category->update($data);

        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => 'Product Category successfully updated.']);
        
    }



    public function render()
    {
        if ($this->lang != app()->getLocale()) {
            return view('livewire.product.category.edit-language');
        }
        return view('livewire.product.category.edit');
    }
}
