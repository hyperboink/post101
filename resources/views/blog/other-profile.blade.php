@extends('layouts.app')

@section('content')
<div class="container page-con" data-page="profile">
	<div class="row justify-content-center">

		<!-- <span class="token" data-token="{{csrf_token()}}"></span>
		<span class="user" data-user-id="{{Auth::user()->id}}"></span> -->

		<div class="col-md-3 profile-panel">

			<div class="card">
				<div class="card-img-wrap">
					<img class="card-img-top" src="http://hyperboink.com/assets/svg/hypermonsta.svg" alt="Card image cap">
				</div>

				<div class="card-body text-center">
					<h5 class="card-title">{{$user->name}}</h5>
					<p class="card-text">{{$user->email}}</p>
				</div>
				<ul class="list-group list-group-flush text-center">
					<li class="list-group-item">Joined: {{$user->created_at->diffForHumans()}}</li>
				</ul>
			</div>
		</div>

		<div class="col-md-9">
			<div class="text-right">
				<button data-follow='{
					"user_id": "{{$user->id}}",
					"isFollowing": "{{auth()->user()->isFollowing($user->id)}}"
				}'
				class="follow-btn btn btn-primary {{auth()->user()->isFollowing($user->id) ? 'gray' : ''}}">{{auth()->user()->isFollowing($user->id) ? 'Unfollow' : 'Follow'}}</button>
			</div>

			@if(auth()->user()->id == $user->id)
			<div class="post-top clearfix">
				<div class="col-md-2 float-right">
					<a href="{{route('post.create')}}" 
						class="btn btn-success float-right post-add"  
						data-toggle="modal" 
						data-target="#modal-form" 
						data-modal-details='{
							"title": "Create Post",
							"buttonText": "Add Post"
						}'>
							<i class="fa fa-plus"></i> Create Post
					</a>
				</div>
				
				<div class="post-top-txt col-md-9 float-left">
					<h3><i class="fa fa-sticky-note"></i> Your Posts</h3>
				</div>
				
			</div>
			@endif
			<div class="post-wrap"
				data-on-scroll="true"
				data-post='{
					"limit": "",
					"total": "{{$posts->total()}}"
				}'>

				@foreach($posts as $post)
				<div class="card post" data-post-id="{{$post->id}}" data-post-user-id="{{$post->user_id}}">
					
					<div class="card-body post-body">
						@if (session('status'))
						<div class="alert alert-success" role="alert">
							{{ session('status') }}
						</div>
						@endif

						<div class="post-body-title clearfix">
							<div class="float-left">
								<h4 class="post-title">{{$post->title}}</h4>
								<div class="post-details">
									<span class="post-date">
									@switch($post->created_at->diffInDays(carbon\Carbon::now()))
										@case(0)
											{{$post->created_at->diffForHumans()}}
											@break
										@case(1)
											Yesterday
											@break
										@default
											{{$post->created_at->format('M. d, Y h:ma')}}
									@endswitch
									</span> by <a class="posted-by" href="profile/{{Auth::user()->id === $post->user_id ? '' :  $post->user->name}}">{{Auth::user()->id === $post->user_id ? 'You' :  $post->user->name}}</a>
									@if($post->isEdited())
									<span class="post-edited edited"> | Edited</span>
									@endif
									
								</div>

							</div>
							
							@if(Auth::user()->id === $post->user_id)
							<div class="float-right"> 

								
								<div class="dropdown post-action">
									<span class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">...</span>

									<div class="dropdown-menu dropdown-menu-right">
										<a href="/profile/post/{{$post->id}}" 
											class="post-edit"
											data-modal-details='{
												"title": "Edit Post",
												"buttonText": "Update Post"
											}'><i class="fa fa-edit"></i> edit
										</a>
										<a href="/profile/post/delete/{{$post->id}}" class="post-delete"><i class="fa fa-times"></i> delete</a>
									</div>
								</div>
							</div>
							@endif
						</div>

						<div class="post-content">
							<p>{{$post->content}}</p>
						</div>
					</div>

					

					<div class="card-footer">
						<a href="#" class="comment-btn">
							<i class="fa fa-comments" aria-hidden="true"></i>Comments 
							<span class="comment-count">({{$post->countComments()}})</span>
						</a>
						
						<a href="#" class="like-btn like-thumbs {{$post->authLike() ? 'liked' : ''}}" data-auth-liked="{{$post->authLike()}}"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span>{{$post->authLike() ? 'un' : ''}}like</span></a>
						<div class="likes-count">
							<div class="likes-count-num" data-count="{{$post->countLikes()}}">
								<span>{{$post->countLikes() ? '('.$post->countLikes().')' : ''}}</span>
							</div>
							
							<div class="likes-users">
								<ul>
									@foreach($post->likes as $like)
										<li><a href="{{url('profile/'.$like->user->name)}}">{{$like->user->name}}</a></li>
									@endforeach
								</ul>
								
							</div>
						</div>
					</div>
					<div class="comment-section">
						<div class="comment-boxes">
							
							@if(count($post->comments))
								@foreach($post->comments as $comment)
								<div class="comment-box" data-comment-user-id="{{$comment->user_id}}" data-comment-id="{{$comment->id}}">
									@if((Auth::user()->id === $comment->user_id) || (Auth::user()->id === $post->user_id) )
										<span class="comment-remove">x</span>
									@endif
									<div class="comment-box-head clearfix">
										<div class="comment-name float-left"><a href="/profile/{{$comment->user->name}}">{{$comment->user->name}}</a></div>
										<div class="comment-date float-left">{{$comment->created_at->diffForHumans()}}</div>
										<div class="comment-edited edited float-left">{{$comment->isEdited() ? '| Edited' : ''}}</div>
										@if((Auth::user()->id === $comment->user_id))
										<div class="comment-edit float-left">| <i class="fa fa-edit"></i></div>
										@endif
										
										
									</div>

									<div class="comment-text">{{$comment->content}}</div>
								</div>
								@endforeach
							@else
								<div class="no-comment">No Comments.</div>
							@endif
							

						</div>
						<div class="comment-input form-group">
							<form action="{{route('comment.save')}}" method="POST">
								{{csrf_field()}}
								<input type="hidden" name="user_id" value="{{Auth::user()->id}}">
								<input type="hidden" name="post_id" value="{{$post->id}}">
								<input type="text" name="content" class="comment form-control" placeholder="Add Comment..."><br>
								<input type="submit" class="btn btn-secondary float-right comment-submit" value="Add Comment">
							</form>
							
						</div>
					</div>
				</div>

			@endforeach

		</div>

		<div class="post-load text-center">
			<span class="post-load-spinner">
				<i class="fa fa-circle-o-notch fa-spin"></i>
			</span>
			<span class="post-load-text">Loading more post...</span>
		</div>

			


	</div>
</div>

<!-- Post Modal -->
<div class="modal fade post-modal" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<form class="post-form">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLongTitle">Edit</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="text" name="title" placeholder="Title here..." class="form-control modal-input-title" value="asffsd">
					</div>
					<div class="form-group">
						<textarea class="form-control modal-input-content" name="content" placeholder="Content here..." rows="6">sadfdsfsf</textarea>
					</div> 
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success submit-post" value="Update">
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Edit Comment Modal -->
<div class="modal fade" id="modal-comment" tabindex="-1" role="dialog" aria-labelledby="ModalEditCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<form class="comment-form">
				<div class="modal-header">
					<h5 class="modal-title" id="modalCommentEditTitle">Edit Comment</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="text" name="content" placeholder="Comment here..." class="form-control modal-comment-input" value="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success update-comment" value="Update">
				</div>
			</form>
		</div>
	</div>
</div>

@endsection
