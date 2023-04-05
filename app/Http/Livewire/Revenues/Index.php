<?php

namespace App\Http\Livewire\Revenues;

use Livewire\Component;
use App\Models\Revenues\Revenue;
use App\Constants\OrderPaymentStatus;
use App\Constants\TransactionType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Traits\GlobalTrait;

class Index extends Component
{   

    use WithPagination;
    use AuthorizesRequests;
    use GlobalTrait;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = '' ;
    public $to_date ;
    public $from_date ;
    public $allPaymentStatus;
    public $transcationTypes;
    public $payment_status;
    public $transaction_type;
    public $filter = ['created_at' => null, 'payment_status' => null, 'transaction_type' => null];
    protected $queryString = ['sortField' , 'sortDirection'];
    protected $paginationTheme = 'bootstrap';
    public bool $loadData = false;

    public function init()
    {
         $this->loadData = true;
    }

    public function sortBy($field){

        if($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function mount()
    {
        $this->filter["from_date"] = $this->from_date;
        $this->filter["to_date"] = $this->to_date;
        $this->filter["payment_status"] = $this->payment_status;
        $this->filter["transaction_type"] = $this->transaction_type;
        $this->filter['type'] = request()->type;
        $this->filter['id'] = request()->id;

        if(auth()->user()->hasRole('Provider')){
            $this->filter['type'] = 'store';
            $this->filter['id'] = $this->getStoreId();
        }
        
        $orderPaymentStatusConstant = new OrderPaymentStatus();
        $this->allPaymentStatus = $orderPaymentStatusConstant->getConstants();

        $transactionTypeConstant = new TransactionType();
        $this->transcationTypes = $transactionTypeConstant->getConstants();

        $this->perPage = config("commerce.pagination_per_page");
    }

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }  

    public function updatingFilter()
    {
        $this->gotoPage(1);
    }
    
    public function render()
    {
        return view('livewire.revenues.index',[
            'revenues' => $this->loadData ? !empty($this->filter['type']) ? Revenue::with(['user', 'order', 'store'])->searchMultipleRevenues(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) :[] : [],
        ]);
    }
}
