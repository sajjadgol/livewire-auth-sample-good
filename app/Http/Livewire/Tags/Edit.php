<?php

namespace App\Http\Livewire\Tags;

use Livewire\Component;
use App\Models\Tags\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Edit extends Component
{   
    use AuthorizesRequests;
    
    public Tag $tag;

    public $lang = '';
    public $languages = '';

    protected function rules(){
        return [
            'tag.title'    => 'required|string|max:75',
            'tag.status'   => 'nullable|between:0,1',
        ];
    }

    public function mount($id) {

        $this->tag = Tag::find($id);
        //  Faq translate
        $this->lang = request()->ref_lang;
        $this->languages = request()->language;

        $this->tag->title = isset($this->tag->translate($this->lang)->title) ?  $this->tag->translate($this->lang)->title: $this->tag->translate(app()->getLocale())->title;
      
        //  Faq translate
    }

    public function updated($propertyName){

        $this->validateOnly($propertyName);
    }

    public function edit(){

        $this->validate();
        $this->tag->update();

        return redirect(route('product-tag-management'))->with('status', __('tag.Tag successfully updated.'));
    }

    public function editTranslate()
    {
        $request =  $this->validate([
           'tag.title'    => 'required|string|max:75',
        ]);

        $data = [
            $this->lang => $request['tag']
        ];
        $tag = Tag::findOrFail($this->tag->id);
        $tag->update($data);

        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => 'Tag successfully updated.']);
    }

    public function render()
    {
        if ($this->lang != app()->getLocale()) {
            return view('livewire.tags.edit-language');
        }
        return view('livewire.tags.edit');
    }
}
