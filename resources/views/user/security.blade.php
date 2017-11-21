@extends('layouts.app')
@section('content')
<div class="container p-t-md">
	<div class="row">



		@component('user.menu',['current_menu' => 'security'])
		@endcomponent


		<div class="col-md-8" style="margin-top: 10px">
			<form action="/profile" method="POST">
				<ul class="list-group media-list media-list-stream">
					<li class="list-group-item media p-a">
						<div class="input-group">
							<label for="email">Email</label>
							<p>
								{{$user->email}} <small><a href="#" type="button" data-toggle="modal" data-target="#changeE" style="color: #24c4bc">(change)</a></small>
							</p>
						</div>
						<div class="input-group" style="margin-bottom: 0">
							<label for="password">Password</label>
							<a class="btn btn-default-outline m-t-5" href="password/reset">Reset Your Password</a>
						</div>
					</li>
				</ul>
				{{ csrf_field() }}
			</form>
		</div>
		<!-- email change modal start -->
		<div class="modal" id="changeE">
				<div class="modal-dialog modal-sm">
						<div class="modal-content">
								<div class="modal-header">
										<h4 class="modal-title pull-left">Change email</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
										</button>
								</div>
								<div class="modal-body">
										<input type="email" placeholder="Enter your new email" class="form-control m-b-sm">
										<input type="email" placeholder="Confirm new email" class="form-control m-b-sm">
										<input type="password" class="form-control m-b-xs" placeholder="Password">
								</div>
								<div class="modal-actions">
										<button type="button" class="btn-link modal-action confirm" data-dismiss="modal">
												<strong style="color: #3fc3ad;">Save</strong>
										</button>
										<button type="button" class="btn-link modal-action cancel" data-dismiss="modal" style="color:#C9CCD4">Cancel</button>
								</div>
						</div>
				</div>
		</div>





	</div>
</div>
@endsection