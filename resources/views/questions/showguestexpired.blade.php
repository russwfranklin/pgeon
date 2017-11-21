@extends('layouts.app-profile')
@section('content')



<nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="nav-contain">
                <nav class="container nav-container header-nav">
                      <div style="width: 42px;">
                          <a href="/"><i class="fal fa-home fa-lg text-muted"></i></a>
                      </div>
                    <h4><img class="img-circle header-img" src="{{ Helper::avatar($question->user->avatar) }}"> </h4>
                    <ul class="nav navbar-nav m-r-0">
                        <li>
                            <div>
                                <a href="/login" class="btn btn-sm btn-default">Log In</a>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </nav>








      <div style="width: auto;">
            <div class="container">
                <ul class="media-list media-list-conversation c-w-md">
                    <div class="media-body">
                        <div class="h5 m-b-5">
                            <span class="tmw"><a href="#">{{$question->user->name}}</a></span>
                             @if(isset($answer))
                            <span class="fa fa-long-arrow-left text-muted"></span>
                            <span class="tmw"><a href="#">{{$answer->user->name}}</a></span>
                            @endif
                            <span class="text-muted time-align">Ended: {{date("m/d/Y H:i", $question->expiring_at)}}</span>
                        </div>
                        <ul class="media-list media-list-conversation c-w-md">
                            <li class="media">
                                <div class="media-body">
                                    <div class="media-body-text media-question">
                                    {{$question->question}}
</div>
								@if(isset($answer))
                                    <ul class="media-list media-seconday media-list-conversation c-w-md">
                                        <li class="media media-current-user media-divider">
                                            <div class="media-body">
                                                <div class="media-body-text media-response media-response-margin" style="cursor: pointer;">
                                                    {{$answer->answer}}
</div>
                                            </div>
                                        </li>
                                    </ul>
                                     @else
                                            <ul class="media-list  media-secondary media-list-conversation c-w-md">
                                <li class="media media-current-user media-divider">
                                    <div class="media-body" style="text-align: center">
                                        <div class="media-body-text media-response">
                                            <div class="statcard p-a-md" style="display: inline-block">
                                                <div class="loader-2">
                                                    <div class="dot"></div>
                                                    <div class="dot"></div>
                                                    <div class="dot"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                             @endif

                                </div>
                            </li>
                        </ul>
                    </div>
                </ul>
            </div>
        </div>





@endsection

<!-- Push a style dynamically from a view -->
@push('styles')
<link href="{{ asset('css/up-voting.css') }}" rel="stylesheet">
 @endpush

<!-- Push a script dynamically from a view -->
@push('scripts')
<script src="{{ asset('js/question.index.js') }}"></script>
@endpush