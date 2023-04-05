<?php

namespace App\Http\Livewire\Tickets;

use App\Models\Tickets\Ticket;
use App\Constants\TicketsStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use App\Events\InstantPushNotification;

class Index extends Component
{   
    use AuthorizesRequests;
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $deleteId = '';
    public $statusList;
    public $new_status;
    public $filter;
     
    protected $listeners = ['remove', 'update'];   
    protected $queryString = ['sortField', 'sortDirection'];
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

    public function mount($status = 'open') {
        $this->perPage = config('commerce.pagination_per_page');
        $this->filter['status'] = $status;
        $statusConst = new TicketsStatus();
        $this->statusList =  $statusConst->getConstants();
    }

    public function statusUpdate($ticket, $status)
	{    
        if(!in_array($status, $this->statusList)){
            $this->dispatchBrowserEvent('alert', 
            ['type' => 'error',  'message' => __('ticket.Invalid action you cannot perform that action !')]);
        }
        
        $this->ticketId  = $ticket['id'];
        $this->new_status  = $status;
        
        $textStatus = $status == 'closed' ? __('ticket.Approve') : __('ticket.Reject');
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'update',
                'type' => 'warning',  
                'confirmButtonText' => __('ticket.Yes,')." ".$textStatus." ". __('ticket.it!'),
                'cancelButtonText' => __('ticket.No, cancel!'),
                'message' => __('ticket.Are you sure want to').$textStatus.'?', 
                'text' => __('ticket.If')." ".$textStatus.__('ticket., you will not be able to revert this action!')
            ]);
 	}

    public function update()
    {
        $ticket = Ticket::find($this->ticketId);
        $ticket->status = $this->new_status;
        
            if($ticket->category->name == 'update-mobile-number'){
                $user = User::find($ticket->user_id);
                $newData = json_decode($ticket->content);
                $user->country_code = $newData->country_code;
                $user->phone = $newData->country_code.$newData->mobile;
                $user->save();

                $textStatus = $this->new_status == 'closed' ? __('ticket.Approved') : __('ticket.Rejected');
                event(new InstantPushNotification($ticket->user_id, [
                    "title" => __( 'ticket.Request ').$textStatus ,
                    "body" => __('ticket.Your phone number update request has been ').$textStatus,
                    "data" => [
                        'type' => 'update-mobile-number',
                        'status' =>$this->new_status ,
                    ]
                ]));

            }      
       
        $ticket->save();
    }

    public function render()
    {
        return view('livewire.tickets.index', [
            "tickets" =>$this->loadData ? Ticket::with(['user', 'category'])->searchMultipleTicket(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        Ticket::find($this->deleteId)->delete();

        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('ticket.Ticket Delete Successfully!')]);

    } 

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }   

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($ticketId)
    {
        $this->deleteId  = $ticketId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('ticket.Yes, delete it!'),
                'cancelButtonText' => __('ticket.No, cancel!'),
                'message' => __('ticket.Are you sure?'), 
                'text' => ('ticket.If deleted, you will not be able to recover this ticket data!')
            ]);
    }
}
