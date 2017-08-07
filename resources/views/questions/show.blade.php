@extends('layouts.app')
@section('content')


<div class="container p-t-md">
            <ul class="nav nav-pills tabs-padding">
                <li>
                    <a href="/questions"><span class="icon icon-home"></span></a>
                </li>
                <li class="disabled">
                    <a href="#"><span class="icon icon-chevron-left"></span>
      Back</a>
                </li>
                <li>
                    <a href="#">
      Next<span class="icon icon-chevron-right"></span></a>
                </li>
            </ul>
            <div class="row">
                <div class="col-md-12">
                    <ul class="media-list media-list-conversation c-w-md">
                        <li class="media p-a media-divider">
                            <a class="media-left" href="#">

                                <img class="media-object img-circle" src="{{ $question->user->avatar ? '/uploads/avatars/'.$question->user->avatar:  URL::asset('img/profile-placeholder.svg')}} " alt="">
                            </a>
                            <div class="media-body">
                                <div class="media-heading">
                                    <small class="pull-right"> <span class="question_clock">Validity :   {!! ($question->expiring_at) !!}
</span></small>
                                    <h5 class="m-b-0">{{$question->user->name}}</h5>
                                </div>
                                <ul class="media-list media-list-conversation c-w-md">
                                    <li class="media m-b-md">
                                        <div class="media-body">
                                            <div class="media-body-text media-question"><?php echo $question->question; ?>
</div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>

     
                
                <answers></answers>
                
                
                
            </div>
            <div class="row">
                <div class="footer navbar-fixed-bottom">
                    <div class="col-md-12">
                      
                         <form v-on:submit.prevent="submit_answer({{ $question->id }})" class='form-horizontal'>   
                        <ul class="media-list">
                            <li class="media m-b-md media-divider">
                                <div class="media-body">
                                    <li class="media media-current-user m-b-md">
                                        <div class="input-group">
                                            <input v-model="submitted_text"  class="form-control response-form" placeholder="Enter your response here.." type="text" maxlength="150"  />
                                            <span class="input-group-btn"><button  class="btn btn-default response-button" type="submit">
                                                    <span class="icon icon-circle-with-plus response-icon"></span>
                                                </button></span>
                                        </div>
                                        <div class="media-footer text-right">
                                        </div>
                                    </li>
                                </div>
                            </li>
                        </ul>
                        </form>  
                    </div>
                    <!-- /input-group -->
                </div>
            </div>
      </div>



@endsection

<!-- Push a style dynamically from a view -->
@push('styles')
      <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.6/summernote.css" rel="stylesheet">
@endpush

<!-- Push a script dynamically from a view -->
@push('scripts')
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.6/summernote.js"></script>
    <script src="{{ asset('js/question.index.js') }}"></script>
@endpush