@extends('layouts.app-no-top-bar')
@section('content')


    <div class="container m-t-10">
        <div class="row">
            <div class="col-md-12">

                            <notifications current_user_id="{{Auth::user()->id}}" @bubbleCountChanged="bubbleChangedFromChild"></notifications>
            </div>
        </div>
    </div>








@endsection


@push('scripts')
    <script src="{{ asset('js/question.index.js') }}"></script>
@endpush
