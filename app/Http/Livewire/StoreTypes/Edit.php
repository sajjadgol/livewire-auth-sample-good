<?php

namespace App\Http\Livewire\StoreTypes;
use App\Models\Stores\StoreType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Edit extends Component
{  
    use AuthorizesRequests;
    public StoreType $storeType;
    public $lang = '';
    public $languages = '';

    protected function rules() {
        $storeType = isset($this->storeType->translate($this->lang)->id)  ? ','.$this->storeType->translate($this->lang)->id : null;
        $this->storeType->name = trim($this->storeType->name);

        return [
            'storeType.name'   => 'required|unique:App\Models\Stores\StoreTypeTranslation,name'.$storeType,
            'storeType.status' => 'nullable|between:0,1',
        ];
    }

    public function mount($id) {

        $this->storeType = StoreType::find($id);
        //store type translate
        $this->lang = request()->ref_lang;
        $this->languages = request()->language;

        $this->storeType->name = isset($this->storeType->translate($this->lang)->name) ?  $this->storeType->translate($this->lang)->name: $this->storeType->translate(app()->getLocale())->name;
      
        //store type translate

    }

    public function updated($propertyName){

        $this->validateOnly($propertyName);
    }

    public function edit() {

        $this->validate();
        $this->storeType->update();

        return redirect(route('store-type-management'))->with('status', 'Store type successfully updated.');

    }

    public function editTranslate()
    {
        $storeType = isset($this->storeType->translate($this->lang)->id)  ? ','.$this->storeType->translate($this->lang)->id : null;
        $request =  $this->validate([
            'storeType.name' => 'required|unique:App\Models\Stores\StoreTypeTranslation,name'.$storeType,
        ]);

        $data = [
            $this->lang => $request['storeType']
        ];
        $storeType = StoreType::findOrFail($this->storeType->id);
        $storeType->update($data);

        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => 'Store type successfully updated.']);
    }

    public function render()
    {
        
        if ($this->lang != app()->getLocale()) {
            return view('livewire.store-types.edit-language');
        }
        return view('livewire.store-types.edit');
    }

}
