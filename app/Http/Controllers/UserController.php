<?php

namespace App\Http\Controllers;


use App\Answer;
use App\Question;
use App\User;
use App\UserFollowing;
use App\UserActivation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use Image;
use Route;
use Helper;
use Config;


class UserController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function index($id)
    {
        $user = User::findOrFail($id);

        if (!$user)
            abort(404, "Page Not Found");

        $questions = Question::where('user_id', '=', $id)->take(10)->orderBy('id','DESC')->get();
        $answers = Answer::where('user_id', '=', $id)->take(10)->orderBy('id','DESC')->get();
        return view('user.index')->with('questions',$questions)->with('user',$user)->with('answers',$answers)->with('page_title', $user->name . '');
    }



    public function questions($id)
    {
        $user = User::findOrFail($id);
        $questions = Question::where('user_id', '=', $id)->orderBy('id','DESC')->paginate(10);
        return view('user.questions')->with('questions',$questions)->with('user',$user)->with('page_title', $user->name . ' Questions');
    }

    public function answers($id)
    {
        $user = User::findOrFail($id);
        $answers = Answer::where('user_id', '=', $id)->orderBy('id','DESC')->paginate(10);
        return view('user.answers')->with('user',$user)->with('answers',$answers)->with('page_title', $user->name . 'Answers');
    }

    public function skip() {
        // do non-safe stuff on click of skip..user may never come to this point if they close the window.
        return redirect('/questions');
    }

    public function participation($id) {
        $user = User::findOrFail($id);
        $questions = User::get_participation($id);

        return view('user.participation')->with('user',$user)->with('questions',$questions)->with('page_title', $user->name . 'Answers');
    }

    public function profile () {
      $user = Auth::user();
      $error = "";
      return view('user.profile')->with('user',$user)->with('error',$error);

    }


    public function changename() {
        $user = Auth::user();
        return view('user.change-name')->with('user',$user);
    }

    public function changeemail() {
        $user = Auth::user();
        return view('user.change-email')->with('user',$user);
    }

    public function security () {
      $user = Auth::user();
      $error = "";
      return view('user.security')->with('user',$user)->with('error',$error);

    }


    public function step2 () {

        $skip_url = Session::get('backUrl')?Session::get('backUrl'):"/skip";
        return view('user.step2')->with('skip_url',$skip_url);

    }


    public function membership () {

//    toaster()->add('ma sdfjs lkfjs flksjma',null, ['duration' => 20000000])->error()->width('auto');
      $user = Auth::user();
      $stripe_id = "";
      $sub = $user->subs()->latest()->first();

      //will determine if it is a stripe subscribed or locally subscribed.
      //last
      if($sub)
        $stripe_id = $sub->stripe_id;

       $followers_counts = UserFollowing::get_followers_count($user->id);
      $error = "";
      if($user->subscribedToPlan('pgeon_monthly','main')) {
        $plan = "Monthly";
        $user_type = "Member";
      }elseif($user->subscribedToPlan('pgeon_yearly','main')) {
        $plan = "Yearly";
        $user_type = "Member";
      }else {
        $plan = "Free";
        $user_type = "Standard";
      }

      if($error)
      toaster()->add($error)->error()->width('auto');
      return view('user.membership')->with('user',$user)->with('plan', $plan)->with('followers_counts', $followers_counts)->with('user_type', $user_type)->with('stripe_id', $stripe_id);

    }


    public function avatar () {

        $user = Auth::user();
        return view('user.avatar');

    }

    public function preferences () {

        /*

        $followers_counts = UserFollowing::get_followers_count($user->id);
        $error = "";
        if($user->subscribedToPlan('pgeon_monthly','main')) {
            $plan = "Monthly";
            $user_type = "Member";
        }elseif($user->subscribedToPlan('pgeon_yearly','main')) {
            $plan = "Yearly";
            $user_type = "Member";
        }else {
            $plan = "Free";
            $user_type = "Standard";
        }
        */
        $user = Auth::user();
        return view('user.preferences')->with('subscribed_to_newsletter',$user->subscribed_to_newsletter)->with('someone_i_followed_posted',$user->someone_i_followed_posted)->with('my_response_selected',$user->my_response_selected)->with('my_response_got_points',$user->my_response_got_points)->with('email_receipts',$user->email_receipts);

    }


    public function notifications () {
      $user = Auth::user();
      $error = "";
      return view('user.notifications')->with('user',$user)->with('error',$error);

    }



    public function status () {
        $user = Auth::user();
        return response()->json($user);
    }

    public function unsubscribe() {
        $user = Auth::user();
        $user->subscription('main')->cancelNow();

        $user->role_id = 2;
        $user->save();
        toaster()->add('Pro membership cancelled')->success()->width('auto');
        return back();
    }
    public function subscribe()
    {
         $user = Auth::user();
         $stripeToken = Request::input('stripeToken');
         $plan = Request::input('plan');
         $coupon = trim(Request::input('coupon'));

         try {
           if($coupon)  {
                $user->newSubscription('main', $plan)
                       ->withCoupon($coupon)
                       ->create($stripeToken, [ 'email' => $user->email,  ]);
           }else {
                $user->newSubscription('main', $plan)
                       ->create($stripeToken, [ 'email' => $user->email,  ]);
           }

            $user->role_id = 3;
            $user->save();
            toaster()->add('Subscription is completed.')->success()->width('auto');
             return back();
         } catch (\Stripe\Error\Card  $e) {
            $body = $e->getJsonBody();
            $err  = $body['error']['message'];
            //Request::session()->flash('error','Error: ' . $err);
            toaster()->add('Error: ' . $err)->error()->width('auto');
            return back();

            // return back()->with('success',$e->getMessage());
         }

    }


    public function updatecard()
    {


         $user = Auth::user();
         $user->asStripeCustomer();
         $stripeToken = Request::input('stripeToken');

         try {
            $user->updateCard($stripeToken);
            toaster()->add('Card updated.')->success()->width('auto');
            return back();
         } catch (\Stripe\Error\Card  $e) {
            $body = $e->getJsonBody();
            $err  = $body['error']['message'];
            toaster()->add('Error: ' . $err)->error()->width('auto');

          //  Request::session()->flash('error','Error: ' . $err);
            return back();

            // return back()->with('success',$e->getMessage());
         }

    }



    public function update(){

      $user = Auth::user();

       if (Request::input('step2') == 1) {
           $view = "user.step2";
           $success_view = Session::get('backUrl') ? Session::get('backUrl') : "/questions";

       }else {
           $success_view = '/my-account';
           $view = "user.profile";
       }

        // Handle the user upload of avatar
    	if(Input::hasFile('avatar')){
            
        $image = Input::file('avatar');
        if($user->avatar)
            Storage::delete('/uploads/avatars/'.$user->avatar);


            $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('/uploads/avatars/' . $filename);
        
            Image::make($image->getRealPath())->resize(200, 200)->save($path);
            $user->avatar = $filename;
            $success_view = '/avatar';



    	}

        if(Input::hasFile('banner')){
            $image = Input::file('banner');
            $banners = Config::get('constants.default_banners');

            //if it is a default banner don't use the same name...
            if($user->banner && !in_array($user->banner, $banners))
                $filename  = $user->banner;
            else
                $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('/uploads/banners/' . $filename);
            Image::make($image->getRealPath())->resizeCanvas(2000, 200)->save($path);
                $user->banner = $filename;
        }



//only save the slug if he is a member
    //  if($user->role_id == 3) {

      if (Request::input('slug')) {


        $validator = Validator::make(Request::all(), [
             'slug' => 'max:25|alpha_num',

         ]);


         if ($validator->fails()) {
            // flash('Invalid display name. Should not contain special chars and should not exceed 25 letters.');
            abort(401, 'Invalid display name. Should not contain special chars and should not exceed 25 letters.');
            // echo json_encode(array('error', ));
            //exit;
            // return view($view)->with('user',$user)->with('skip_url', $success_view);
         }

         $routes = [];
         $slugs = User::select('slug')->whereNotIn('id', [$user->id])->get();

         foreach ($slugs as $key => $val) {
             $routes[]  = $val['slug'];
         }


         // You need to iterate over the RouteCollection you receive here
         // to be able to get the paths and add them to the routes list
         foreach (Route::getRoutes() as $route)
         {
             $routes[] = $route->uri;
         }




         $slug = strtolower(Request::input('slug'));

         if (in_array ($slug, $routes)) {
            abort(401,  "'$slug' taken! Try another one.");
             //flash("'$slug' taken! Try another one.");
             //return view($view)->with('user',$user)->with('skip_url',  $success_view);;
         }


         $user->slug = $slug;

      //}
      }



        if (Request::input('name') != null ) {

            $user->name = Request::input('name');


        }

      if (Request::input('subscribed_to_newsletter') != null ) {
          $user->subscribed_to_newsletter = Request::input('subscribed_to_newsletter');
      }

      if (Request::input('someone_i_followed_posted') != null ) {
        $user->someone_i_followed_posted = Request::input('someone_i_followed_posted');
      }

        if (Request::input('my_response_selected') != null ) {
            $user->my_response_selected = Request::input('my_response_selected');
        }
        if (Request::input('my_response_got_points') != null ) {
            $user->my_response_got_points = Request::input('my_response_got_points');
        }
        if (Request::input('email_receipts') != null ) {
            $user->email_receipts = Request::input('email_receipts');
        }

      $user->save();
      //user hasn't generated a slug yet..and already generated slug would simply have an id in it
      if ($user->id == $user->slug) {
        User::generateSlug($user);
      }

      return redirect($success_view)->with('user',$user);
     //return response()->json(array("fine" => "ttt"));

    }

    public function getProfileBySlug($slug) {
        $user = User::where('slug', '=', $slug)->first();
        return $this->showPublicProfile($user);
    }

    public function getUserIdFromSlug($slug) {
        $id = User::where('slug', '=', $slug)->pluck('id');
        return ($id)?$id[0]:0;
    }

    public function getProfile($id) {
          $user =  User::find($id);
          return $this->showPublicProfile($user);
    }

    private function showPublicProfile($user) {
      if(!$user)
            return view('user.usernotfound');
          else {
               $q_count = Question::question_asked_count($user->id);
              $answers =   User::get_accepted_answers_of_user($user->id);
              $users = User::convoDetails($user->id);

              //$replies = User::replies($user->id);
              $replies = array();
              foreach ($users as $key => $val) {
                  $val->rslug = Helper::shared_slug($val->q_by_uid, $val->q_by_slug, $val->ans_by_uid, $val->ans_by_slug) ;
                  $val->rslug_formatted = Helper::shared_formatted_string($val->q_by_uid, $val->q_by_slug, $val->ans_by_uid, $val->ans_by_slug) ;
                  //dd($val->created_at);
                  $val->ago = Helper::calcElapsed($val->created_at);
                  $replies [] = $val;
              }



              $is_following = false;
              if (Auth::user()) {
                  $current_user = Auth::user();
                  $followings = UserFollowing::get_followed_by($current_user->id)->toArray();
                  if (in_array($user->id, $followings)) {
                      $is_following = true;
                  }
              }

              return view('user.public_profile')->with('user',$user)->with('replies', $replies)->with('points', User::get_points_as_non_negative($user->id))->with('is_following', $is_following)->with('answers_count', count($answers))->with('q_count', $q_count);
          }
    }

   public function topResponders($user_id) {
      $users = User::get_users_of_accepted_answers($user_id);
      return response()->json($users);
   }

    public static function fetchConvoFromTargetUser($answered_by, $question_by) {

        $convo = Answer::fetchR($answered_by, $question_by);
        return $convo;

    }


    public  function fetchOneWayConvoFromTargetUser($keyw1orslug1,$id1orslug2,$keyw2 = null,$id2 = null) {


    //    echo ' $keyw1orslug1 '.$keyw1orslug1.' $id1orslug2 '.$id1orslug2.' $keyw2 '.$keyw2.' $id2 '.$id2;
        $from_user = $target_user = null;
        if($keyw1orslug1 == "user") { //r/user/2/
            $target_user =  $id1orslug2;
        }else { // this is a slug /r/john
            $target_user =  $this->getUserIdFromSlug($keyw1orslug1);
        }
        if ($id1orslug2 =="user") {   //jac/user/5
            $from_user =  $keyw2;
        }


        if(!$from_user) {
            //r/john/jac
            if (!$keyw2) { //if no keyw2 found
                $from_user =  UserController::getUserIdFromSlug($id1orslug2);
            }elseif($keyw2 == "user") { //r/(*)/user/45
                $from_user =  $id2;
            }else {  //user/34/john

                $from_user =  UserController::getUserIdFromSlug($keyw2);
            }
        }

        $replies =  UserController::fetchConvoFromTargetUser($from_user, $target_user);


        $fuser = User::find($from_user);
        $tuser = User::find($target_user);

        $rslug_formatted = Helper::shared_formatted_string($tuser->id, $tuser->slug, $fuser->id, $fuser->slug) ;

        return view('user.friendship')->with('replies', $replies)->with('rslug_formatted', $rslug_formatted)->with('fuser', $fuser)->with('tuser', $tuser);
      //  print_r($replies);


    }
    public function points() {
      return $this->id;
    }

    public function notification_count() {
        $user = Auth::user();
        return $user->notifications()->where('seen','=',0)->count();
    }

    public static function getAcceptedAnswersOfUser($user_id) {
        $topAnswers = User::get_accepted_answers_of_user($user_id);
        $response = array();

        foreach ($topAnswers as $key => $val) {
            $val->created_at =  Helper::calcElapsed($val->expiring_at);
            $response[] = $val;
        }

        return response()->json($response);
    }


    public static function delete() {
        $id = Request::input('id');
        $password = Request::input('password');
        $user = User::find($id);
        $status = array();

        if(Hash::check($password, $user->password)) {
            //delete the questions created by user
            if(User::deleteEntities($id))
                Auth::logout();
                return response()->json(array('status' => 1, 'message' => 'Account deleted successfully!'));
        }else {
            return response()->json(array('status' => 0, 'message' => 'Wrong did not match'));
        }

    }

    public static function deletesso() {
        $id = Request::input('id');
        if(User::deleteEntities($id)) {
                Auth::logout();
                return response()->json(array('status' => 1, 'message' => 'Account deleted successfully!'));
        }

    }



}
