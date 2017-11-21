@extends('layouts.app-profile') @section('content')

  <div class="nav-contain">
    <nav class="container app-navbar header-nav">
      <a onclick="window.history.back()"><span class="fal fa-arrow-left fa-lg"></span></a>
      <h4>Pending</h4>
    </nav>
  </div>
  <div style="width: auto;">
  </div>

    <div class="container p-t-md">

      <div class="tabs">
        <div class="pending">
        
         @foreach ($pending as $key => $val) 
        
          <ul class="media-list media-list-stream c-w-md">
            <div class="media-body m-b">
              <ul class="media-list media-list-conversation c-w-md">
                <li class="media">
                  <div class="media-body">
                    <div class="media-body-text media-question"> {{$val['question']->question}}
                    </div>
                  </div>
                </li>
              </ul>
              <ul class="media-list media-list-conversation c-w-md">
                <li class="media media-current-user media-divider">
                  <div class="media-body-text media-response">
                     @if ($val['answer'])
                                          {{$val['answer']->answer}}
                                       @endif   
                  </div>
                  <div style="padding-top: 10px;">
                   @if ($val['answer'])
                        <a data-toggle="modal" href="#viewAll" v-on:click="callChildPendingAnswers({{$val['question']->id}}, '{{$val['question']->user->name}}', '{{$val['question']->question}}', '{{date('m/d/Y H:i', $val['question']->expiring_at)}}')" style="vertical-align: sub;">&nbsp;<i class="fal fa-comments"></i>&nbsp;View all</a>
                                    @endif
                    <div class="pull-right">
                    <button type="button" rel="{{$val['question']->id}}" class="btn btn-danger-outline delete">Delete</button>
                       
                                              @if ($val['answer'])
                                                <form  method="post" id="publish_form" action="/accept_answer"> 
                                                {{ Form::token() }}
                                 <input type="hidden" value="{{$val['answer']->id}}" name="answer_id" >
                                <input type="hidden" name="question_id" value="{{$val['question']->id}}" >
                                <button type="submit"  class="btn btn-primary">Publish</button>
                               </form>
                                           
                                        @endif  
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </ul>
          
           @endforeach
          
        </div>
     
      </div>
  </div>




        
        
        
<div class="modal fade" id="viewAll" tabindex="-1" role="dialog" aria-labelledby="viewAll" aria-hidden="true">
  <div class="modal-dialog">
  <answers_expired_owner ref="answersexpiredowner"></answers_expired_owner>

  </div>
</div>
 



@endsection

<!-- Push a style dynamically from a view -->
@push('styles') @endpush

<!-- Push a script dynamically from a view -->
@push('scripts')
<script src="{{ asset('js/question.index.js') }}"></script>
@endpush