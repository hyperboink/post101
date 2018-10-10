@extends('layouts.app')

@section('content')
<div class="container page-con" data-page="profile">
	
	
	<form action="{{route('test')}}"  method="GET">
		<input type="file" name="weeimage">
		<input type="submit">
	</form>

	
	
	<div class="user-lists">
		<ul class="list-group">
			@foreach($users as $user)
			<li class="list-group-item">
				<div class="row">
					<div class="col-1">
						
						
						@if(auth()->user()->isFollowing($user->id))
						<form action="{{route('unfollow', $user->id)}}" method="POST">
							{{csrf_field()}}
							{{ method_field('DELETE') }}
							<input type="hidden" name="user_id" value="{{$user->id}}">
							<button class="btn btn-secondary">Unfollow</button>
						</form>
						@else
						<form action="{{route('follow', $user->id)}}" method="POST">
							{{csrf_field()}}
							<input type="hidden" name="user_id" value="{{$user->id}}">
							<button class="btn btn-primary">Follow</button>
						</form>
						@endif

					</div>
					<div class="col-11">
						<span data-id="{{$user->id}}">{{$user->name}}</span>
					</div>
				</div>
				
			</li>
			@endforeach

			@foreach($notifications as $notification)
				{{$notification->data['followers_id']}} {{$notification->data['followers_name']}}
			@endforeach
		</ul>
	</div>

</div>
@endsection
