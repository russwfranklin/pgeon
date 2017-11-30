<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/questions';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
    * Redirect the user to the GitHub authentication page.
    *
    * @return Response
    */
   public function redirectToProvider($provider)
   {
       return Socialite::driver($provider)->redirect();
   }

   /**
    * Obtain the user information from GitHub.
    *
    * @return Response
    */
   public function handleProviderCallback($provider)
   {
       $user = Socialite::driver($provider)->user();

       $array_user = $this->findOrCreateUser($user, $provider);
       $authUser = $array_user['user'];
       $registered_new =  $array_user['registred_new'];
       Auth::login($authUser, true);
       if($registered_new)
            return redirect('/step2');
       else 
           return redirect($this->redirectTo);
        
       

       // $user->token;
   }


   public function findOrCreateUser($user, $provider)
   {
       $authUser = User::where('provider_id', $user->id)->first();
       if ($authUser) {
           $registred_new = false;
       }else{
       
           $authUser = User::create([
           'name'     => $user->name,
           'email'    => $user->email,
           'provider' => $provider,
           'provider_id' => $user->id
       ]);
        $registred_new = true;
       }
       return array('user' => $authUser, "registred_new" => $registred_new);
   }

/*   protected function redirectTo()
   {
      $user = Auth::user();
       if($user->role_id == 3) {
         return '/questions';
       }else {
         return '/people';
       }

   }
   */
}
