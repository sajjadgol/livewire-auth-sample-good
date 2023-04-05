<?php
namespace App\Http\DataTable;
trait WithSingleAction
{
    public $dltid = false;
    public function initializeWithSingleAction()
    {
        return $this->listeners = array_merge($this->listeners, [
            'remove', 'confirm',
        ]);
    }
    public function renderingWithSingleAction()
    {
        if ($this->dltid) $this->dltid;
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($dltid)
    {
        $this->dltid  = $dltid;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('slider.Yes, delete it!'),
                'cancelButtonText' => __('slider.No, cancel!'),
                'message' => __('slider.Are you sure?'), 
                'text' => __('slider.If deleted, you will not be able to recover this Slider!')
            ]);
    }
}
