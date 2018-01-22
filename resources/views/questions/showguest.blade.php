@extends('layouts.app-profile-no-top-bar')
@section('content')


<nav class="navbar navbar-inverse navbar-fixed-top">
                <nav class="container nav-container header-nav">
                  <div style="width: 42px;">
                      <a href="/"><i class="icon fal fa-home fa-lg text-muted"></i></a>
                  </div>
                    <h4>
                      <a href="#"><img class="img-circle header-img" href="#" src="{{ Helper::avatar($question->user->avatar) }}"></a>
                    </h4>
                    <div class="dropdown">
                        <a class="btn btn-link p-x-0" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="fal fa-ellipsis-v ellipsis"></span></a>
                        <ul class="dropdown-menu header-dropdown" role="menu">
                          <li>
                              <a href="/login"> <span class="fal fa-plus dropdown-icon"></span>Follow</a>
                          </li>
                            <li>
                                <a data-toggle="modal" href="#shareQuestion"> <span class="fal fa-share-alt dropdown-icon"></span>Share</a>
                            </li>
                        </ul>
                    </div>
                </nav>
        </nav>

        <!-- begin share question modal -->
        <div class="modal fade" id="shareQuestion" tabindex="-1" role="dialog" aria-labelledby="shareQuestion" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Share question</h4>
                    </div>
                    <div class="modal-body p-y-sm">
                        <div class="flexbox-container">

                        <div class="sharethis-inline-share-buttons"></div>


                        </div>
                    </div>
                    <div class="modal-body p-a">
                        <div class="flexbox-container">
                            <div>
                                <div>
                                    <div class="input-group">
                                        <input class="form-control" id="txt_current_url" value="{{url()->current()}}">
                                        <span class="input-group-btn"> <button id="copy_to_cb" class="btn btn-default" type="button" style="height: 36px;">
                                                <span class="fa fa-copy"></span>
                                            </button> </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>










          <div style="width: auto;">
            <div class="container">
                <ul class="media-list">
                    <li class="media media-divider">
                              <div class="h5 m-b-5">
                            <span class="tmw"><a href="#">{{Helper::slug($question->user->id ,$question->user->slug)}}</a></span>
                            <span class="text-muted time-align">
                            <allqtimer :initial="{{$lq_expiring_in}}"
								:question_id="{{$question->id}}" @event="reload"></allqtimer></span>
                            </span>
                        </div>
                        <ul class="media-list media-list-conversation c-w-md">
                            <li class="media">
                                <div class="media-body">
                                    <div class="media-body-text live-media-question">
                                    <?php echo $question->question; ?></div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>












        <!-- Guests are not able to view posted answers -->


        <div class="fixed-bottom-footer">
          <div class="navbar-fixed-bottom">
            <a href="/login">
            <div style="background-color: #33cccc; width: auto; height: 55px; color: #fff; font-size: 16px;" class="p-a text-center">
Sign up | Log in
            </div></a>
            </div>
            </div>
         <?php /* <answers_guest question_id="{{$question->id}}" question_owner_id="{{$question->user_id}}" ></answers_guest> */ ?>



@endsection

<!-- Push a style dynamically from a view -->
@push('after-core-styles')
<link href="{{ asset('css/up-voting.css') }}" rel="stylesheet">
 @endpush

<!-- Push a script dynamically from a view -->
@push('scripts')
<script src="{{ asset('js/question.index.js') }}"></script>
@endpush
