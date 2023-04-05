<?php

namespace App\Http\Livewire\Ecommerce;

use Livewire\Component;

class UtilsController extends Component
{

    public function mount()
    {
        $agent =  request()->header('User-Agent');
        //agents
        $iPhone  = stripos($agent,"iPhone");
        $iPad    = stripos($agent,"iPad");
        $Android = stripos($agent,"Android");
        $webOS   = stripos($agent,"webOS");
        
        $host = config('app_settings.deep_link_url.value');
        $id = request()->store_id;
        $type = request()->type;
        //do something with this information
        if($iPhone || $iPad){
            return redirect()->to('dinggy://'.$host.$type.'/'.$id.'?source=sharing');
        }else{
            return redirect()->to('https://'.$host.$type.'/'.$id.'?source=sharing');
        }
    }
}

