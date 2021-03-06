<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use App\User;

class Helper
{
  public static function avatar($avatar)
    {
      //  echo file_exists(public_path('/uploads/avatars/'.$avatar));exit;
        if($avatar) {
            if (file_exists(public_path('/uploads/avatars/'.$avatar))) {
                return '/uploads/avatars/'.($avatar);
            }
        }
        return '';
    }


    public static function calcElapsed($seconds) {

        $seconds = time() - $seconds;
        $year = floor($seconds /31556926);
        $months = floor($seconds /2629743);
        $week=floor($seconds /604800);
        $day = floor($seconds /86400);
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);
        if($seconds < 60) $time = $secs." seconds ago..";
        else if($seconds < 3600 ) $time =($mins==1)?"Just now":$mins." mins ago..";
        else if($seconds < 86400) $time = ($hours==1)?$hours." hour ago..":$hours." hours ago..";
        else if($seconds < 604800) $time = ($day==1)?$day." day ago..":$day." days ago..";
        else if($seconds < 2629743) $time = ($week==1)?$week." week ago..":$week." weeks ago..";
        else if($seconds < 31556926) $time =($months==1)? $months." month ago..":$months." months ago..";
        else $time = ($year==1)? $year." year ago..":$year." years ago..";
        return $time;

    }

    public static function  since($seconds)
    {
        if($seconds > 0) {
            $time = Helper::calcElapsed($seconds);
            return 'You last posted a question '.$time;
        }else {
            return 'Go ahead and post your first question';
        }
    }

    public static function user_posted_since($seconds) {

        if($seconds > 0) {
            $time = Helper::calcElapsed($seconds);
            return 'last posted a question '.$time;
        }else {
            return 'No Questions posted yet!';
        }

    }

    public static function slug($user_id, $slug) {
    		return ($slug)? $slug : "/user/".$user_id;
    }

    public static function shared_slug($user_id1, $slug1, $user_id2, $slug2) {
        $str1 = ($slug1)? $slug1 : "user/".$user_id;
        $str2 = ($slug2)? $slug2 : "user/".$user_id2;
        return  $str1.'/'.$str2;
    }

    public static function shared_formatted_string($user_id1, $slug1, $user_id2, $slug2) {
        $str1 = ($slug1)? $slug1 : "/user/".$user_id;
        $str2 = ($slug2)? $slug2 : "/user/".$user_id2;
        return  $str1.' ← '.$str2;
    }


    public static function formatWithSuffix($input)
    {
        $suffixes = array('', 'k', 'm', 'g', 't');
        $suffixIndex = 0;

        while(abs($input) >= 1000 && $suffixIndex < sizeof($suffixes))
        {
            $suffixIndex++;
            $input /= 1000;
        }

        return (
            $input > 0
                // precision of 3 decimal places
                ? floor($input * 1000) / 1000
                : ceil($input * 1000) / 1000
            )
            . $suffixes[$suffixIndex];
    }

    public static function name_or_slug($user) {
        return  ucwords (($user->name)?$user->name:$user->slug);
    }
    public static function eligible_to_ask() {
        return User::eligible_to_ask();
}

    public static function read_svg($filename) {
        echo \Illuminate\Support\Facades\File::get(public_path($filename));

    }

    public static function back($path=null) {

            if(!$path) {
                $path = url()->previous();
            }
            $back_img = \Illuminate\Support\Facades\File::get(public_path('img/svg/long-arrow-left.svg'));
            //$path = URL::previous();
            //$path = \Illuminate\Routing\UrlGenerator::previous();
            echo '
            <span class="back-arrow dib">
            <a href="'.$path.'" class="dib">'.$back_img.'
            </a>
        </span>';

    }

    public static function close() {

        $need_close = false;
        if (strstr(url()->full(), "signup")) {
            $need_close = true;
        }

        if($need_close) {
            $close_img = \Illuminate\Support\Facades\File::get(public_path('img/svg/times.svg'));
            echo '
                <span class="close-button fc">
                <a href="/" class="fc">'.$close_img.'
                </a>
            </span>';
        }

    }


}
