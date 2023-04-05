<?php

namespace App\Http\Livewire\UserManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Exports\UsersExport;
use App\Models\Driver\UserDriver;
use App\Models\Util;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use App\Events\InstantMailNotification;
use Mail;


class Index extends Component
{

    use AuthorizesRequests;
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $account_status = '';
    public $is_live = '';
    public $filter = ['role' => null, 'status' => null , 'is_live'=> null, 'account_status' => null];
    public $deleteId = '';
    public $actionStatus = '';
    public $userId = '';
    public $roles;
    protected $listeners = ['remove', 'confirmApplication'];
    protected $queryString = ['sortField', 'sortDirection', 'account_status'];
    protected $paginationTheme = 'bootstrap';
    public bool $loadData = false;
  
    public function init()
    {
         $this->loadData = true;
    }

    public function mount($role = null) { 
        $this->filter['role'] = $role;
        $this->filter['account_status'] = $this->account_status;
        $this->filter['is_live'] = $this->is_live;
        $this->perPage = config('commerce.pagination_per_page');
        $this->roles = Role::where('status', 1)->get(['id','name']);
    }

    public function sortBy($field){
        if($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
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
        return view('livewire.user-management.index',[
            'users' =>$this->loadData ? User::with(['roles', 'driver', 'store'])->searchMultipleUsers(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage) : [],
        ]);
    }


   /**
     * Write code on Method
     *
     * @return response()
     */
    public function destroyConfirm($userId)
    {
        $this->deleteId  = $userId;
        $this->dispatchBrowserEvent('swal:confirm', [
                'action' => 'remove',
                'type' => 'warning',  
                'confirmButtonText' => __('user.Yes, delete it!'),
                'cancelButtonText' => __('user.No, cancel!'),
                'message' => __('user.Are you sure?'), 
                'text' => __('user.If deleted, you will not be able to recover this user!')
            ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove()
    {
        User::find($this->deleteId)->delete();

        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('user.User Delete Successfully!')]);
    }

    

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function applicationConfirm($userId, $status)
    {
        $this->userId  = $userId;
        $this->actionStatus = $status;

        $this->dispatchBrowserEvent('swal:confirmApplication', [
                'action' => 'confirmApplication',
                'type' => 'warning',  
                'confirmButtonText' =>  $status == 'approved' ? __('user.Yes, approve it!') : __('user.Yes reject it'),
                'cancelButtonText' => __('user.No, cancel!'),
                'message' => $status == 'approved' ? __('user.Are you approve?') : __('user.Are you Reject'), 
                'text' =>  $status == 'approved' ?  __('user.If approved, driver will be listed in driver sections!') : __('user.If rejected, driver will be not listed in driver sections!')
            ]);
    }  

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function confirmApplication()
    {        
        UserDriver::where('user_id','=', $this->userId )->update(['account_status' => $this->actionStatus]);
        $user = User::whereId($this->userId)->first();
        $password = 'password';
        User::where('id', '=' , $this->userId)->update(['status' => 1, 'password' => Hash::make( $password )]);

        if ($this->actionStatus == 'approved') {
            // send msg to user with user login details
            $message = __("sms/customer.Dear :name,your application login, Username - :username , Password - :password",['name' => $user->name,"username" => $user->phone, 'password' => $password]);
        }else{
            // send msg to user for reject request
            $message = __("sms/customer.Dear :name,your apllication has been rejected, Please contact to administrator",['name' => $user->name]);
        }
        Util::sendMessage($user->phone, $message);

        $this->dispatchBrowserEvent('swal:modal', [
            'type' => 'success',  
            'message' => $this->actionStatus == 'approved' ? __('user.Driver Application Approved Successfully!') : __('user.Driver Application Rejected'), 
            'text' => __('user.It will not list on users table soon.')
        ]);

    }

        
    /**
     * update store status
     *
     * @return response()
     */
    public function statusUpdate($userId, $status)
    {     
        $status = ( $status == 1 ) ? 0 : 1;
        User::where('id', $userId )->update(['status' => $status]);

        $user=User::select(['name'])->where('id', $userId )->first();
        
        event(new InstantMailNotification($userId, [
            "code" =>  'forget_password',
            "args" => [
                'name' => $user->name,
               ]
        ]));

   }

   /**
    * @return \Illuminate\Support\Collection
    *
    */
   public function export() 
   {  
        $users = User::with(['roles', 'driver', 'store'])->searchMultipleUsers(trim(strtolower($this->search)), $this->filter)->orderBy($this->sortField, $this->sortDirection)->get();
        if(!$users->isEmpty()) {
            return Excel::download(new UsersExport($users), 'users.xlsx');
        }

        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => 'No users data found to export.']);
   }


}
