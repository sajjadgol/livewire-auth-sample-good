<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use App\Notifications\ResetPassword;
use App\Models\User;
use Illuminate\Notifications\Notifiable;
use App\Events\InstantMailNotification;
use Mail;

class ForgetPassword extends Component
{
    use Notifiable;

    public $email='';
    
    protected $rules = [
        'email' => 'required|email',
    ];

    public function render()
    {
        return view('livewire.auth.forget-password');
    }


    public function routeNotificationForMail() {
        return $this->email;
    }

    public function show(){

        if(env('IS_DEMO')){
            return back()->with('demo', "You are in a demo version, you can't reset the password");
        }
        else{

        $this->validate();

        $user = User::where('email', $this->email)->first();

            if($user){

                $role = $user->getRoleNames()->implode(",") ;

                if(in_array($role, array("Admin", "Provider"))) {
                    event(new InstantMailNotification($user->id, [
                            "code" =>  'forget_password',
                            "args" => [
                                'name' => $user->name,
                            ]
                    ]));
                }

                return back()->with('status', "We have emailed your password reset link/OTP!");
              
            } else {
                return back()->with('email', "We can't find a user with that email address.");
            }
    }
}
}
