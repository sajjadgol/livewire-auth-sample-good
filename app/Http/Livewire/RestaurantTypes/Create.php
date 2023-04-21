<?php

namespace App\Http\Livewire\RestaurantTypes;

use Livewire\Component;
use App\Models\Stores\RestaurantType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Create extends Component
{    
    use AuthorizesRequests;

    public $name ;
    public $status;

    protected function rules(){
        $this->name = trim($this->name);
      return  [
            'name'   => 'required|max:255|unique:App\Models\Stores\RestaurantTypeTranslation,name',
            'status' => 'nullable|between:0,1',
        ];
    }


    public function updated($propertyName){

        $this->validateOnly($propertyName);

    } 

    public function store() {
        $this->validate();
        
        RestaurantType::create([
            'name'   => $this->name,
            'status' => $this->status ? 1 : 0,
        ]);

        return redirect(route('restaurant-type-management'))->with('status','Restaurant type successfully created.');
    }

    public function render()
    {
        return view('livewire.restaurant-types.create');
    }
}
