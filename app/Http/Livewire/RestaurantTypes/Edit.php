<?php

namespace App\Http\Livewire\RestaurantTypes;
use App\Models\Stores\RestaurantType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Edit extends Component
{  
    use AuthorizesRequests;
    public RestaurantType $restaurantType;
    public $lang = '';
    public $languages = '';

    protected function rules() {
        $restaurantType = isset($this->restaurantType->translate($this->lang)->id)  ? ','.$this->restaurantType->translate($this->lang)->id : null;
        $this->restaurantType->name = trim($this->restaurantType->name);

        return [
            'restaurantType.name'   => 'required|unique:App\Models\Stores\RestaurantTypeTranslation,name'.$restaurantType,
            'restaurantType.status' => 'nullable|between:0,1',
        ];
    }

    public function mount($id) {

        $this->restaurantType = RestaurantType::find($id);
        //store type translate
        $this->lang = request()->ref_lang;
        $this->languages = request()->language;

        $this->restaurantType->name = isset($this->restaurantType->translate($this->lang)->name) ?  $this->restaurantType->translate($this->lang)->name: $this->restaurantType->translate(app()->getLocale())->name;
      
        //store type translate

    }

    public function updated($propertyName){

        $this->validateOnly($propertyName);
    }

    public function edit() {

        $this->validate();
        $this->restaurantType->update();

        return redirect(route('restaurant-type-management'))->with('status', 'Restaurant type successfully updated.');

    }

    public function editTranslate()
    {
        $restaurantType = isset($this->restaurantType->translate($this->lang)->id)  ? ','.$this->restaurantType->translate($this->lang)->id : null;
        $request =  $this->validate([
            'restaurantType.name' => 'required|unique:App\Models\Stores\RestaurantTypeTranslation,name'.$restaurantType,
        ]);

        $data = [
            $this->lang => $request['restaurantType']
        ];
        $restaurantType = RestaurantType::findOrFail($this->restaurantType->id);
        $restaurantType->update($data);

        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => 'Restaurant type successfully updated.']);
    }

    public function render()
    {
        
        if ($this->lang != app()->getLocale()) {
            return view('livewire.restaurant-types.edit-language');
        }
        return view('livewire.restaurant-types.edit');
    }

}
