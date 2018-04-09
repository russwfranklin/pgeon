<?php

namespace App\Http\Controllers;

use App\Mail\EmailChanged;

use App\User;
use App\UserActivation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


use Route;



class EmailChangeController extends Controller
{
       

    
    public function changeEmail() {
        
     
	    
      
       $validator = Validator::make(Request::all(), [
        'email' => ' required|string|email|max:255',

    ]);
    $email = Request::input('email');


    if ($validator->fails()) {
        return response()->json(['error' => 'Invalid email'], 401);
    }
    $password = Request::input('pwd');

    //var_dump($password);

    if (!Hash::check($password, Auth::user()->password))
    {
        return response()->json(['error' => 'Invalid password'], 401);
    }

     if(User::where('email', $email)->first()){
         return response()->json(['error' => 'This email already exists. Use a different one'], 401);
     }


      if($act_code = UserActivation::makeActivationCode(Auth::user()->id, $email)) {
          
        Mail::to('prasanth@object90.com')
            ->send(new EmailChanged($act_code));
         //Mail::to('prasanth@object90.com')
        //     ->send(new QuestionReported(10, 'user_slug'));
           
           
       }else {
            return response()->json(['error' => 'Error saving'], 401);
       }
        

        
        
	   
	}


    public function processActivation($base64_code) {

        list($email, $act_code) = explode("####",base64_decode($base64_code));

        if(UserActivation::validateCodeAndChangeEmail($email, $act_code)) {
            return view('confirmation')->with('message', 'Email updated successfully.')->with('class','success');

       }else {
        return view('confirmation')->with('message', 'Not a valid code!')->with('class','error');
       }
        

    }
}
