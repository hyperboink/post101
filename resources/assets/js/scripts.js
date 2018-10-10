$(function(){

	var win = $(window),
		modalForm = $('#modal-form'),
		commentForm = $('.comment-form'),
		postForm = $('.post-form'),
		modalInputTitle = $('.modal-input-title'),
		modalInputContent = $('.modal-input-content'),
		modalTitle = $('.modal-title'),
		modalPostId = $('.modal-post-id'),
		modalComment = $('#modal-comment'),
		modalCommentInput =  $('.modal-comment-input'),
		validateTxt = $('.validation-text'),
		postWrap = $('.post-wrap'),
		postData = postWrap.data('post'),
		token = window.tokens.csrf,
		authId = window.authId,
		isEdit = false,
		pageNumPost = 1,
		pageNumComment = 1;
		
		var defaults = {
			token: $('.token').data('token'),
			user: {
				authId: $('.profile-panel').data('user-id'),
			},
			post:{
				none: 'You can add a post by clicking "Add Post".',
				submission: {
					error: 'There\'s and error while submitting the comment. Please try again.',
					success: 'Successfully added post!'
				},
				update: {
					error: 'There\'s and error while updating the comment. Please try again.',
					success: 'Successfully updated post!'
				},
				delete: {
					error: 'There\'s and error while updating the comment. Please try again.',
					success: 'Successfully deleted post!'
				}
			},
			comment:{
				none: '<div class="no-comment">No Comments.</div>',
				submission: {
					error: 'There\'s and error while submitting the comment. Please try again.',
					success: 'Successfully added post!'
				},
				update: {
					error: 'There\'s and error while updating the comment. Please try again.',
					success: 'Successfully updated post!'
				},
				delete: {
					error: 'There\'s and error while deleting the comment. Please try again.',
					success: 'Successfully updated post!'
				}
			}
			
		};

	init();

	$(document).on('click', '.like-btn', function(e){
		var self = $(this),
			postId = self.closest('[data-post-id]').data('post-id');
			
		self.data('auth-liked', self.data('auth-liked') ? 0 : 1);

		$.ajax({
			method: 'POST',
			url: '/post/like/'+ postId,
			data: JSON.stringify({
				is_liked: self.data('auth-liked'),
				post_id: postId,
				user_id: authId,
				_token: token
			}),
			contentType: 'application/json',
		}).then(function(res){

			var isLike = parseInt(res.is_liked),
				likeCount = self.next().find('.likes-count-num');
				currentCount = (parseInt(likeCount.data('count')) + (isLike ? 1 : -1));

			self.find('span').text((isLike ? 'un' : '') + 'like');

			self[isLike ? 'addClass' : 'removeClass']('liked');
			
			likeCount.data('count', currentCount);

			likeCount.children().text(currentCount ? '('+currentCount+')' : '');

		}).fail(function(){
			console.log('fail');
		});

		e.preventDefault();
	});

	$(document).on('click', '.comment-btn', function(e){
		$(this).closest('.post').find('.comment').focus();
		e.preventDefault();
	});

	$(document).on('submit', '.comment-input form', function(e){
		var self = $(this),
			post = self.closest('.post'),
			content = self.find('[name="content"]'),
			comment = $(this).closest('.comment-box');

		e.preventDefault();

		if($.trim(content.val())){
			$.ajax({
				method: 'POST',
				url: self.attr('action'),
				data: JSON.stringify({
					user_id: self.find('[name="user_id"]').val(),
					post_id: self.find('[name="post_id"]').val(),
					content: content.val(),
					_token: token
				}),
				contentType: 'application/json'
			}).then(function(res){

				var post = $('.post[data-post-id="'+res.post_id+'"]'); 

				self.find('[name="content"]').val('');

				post.find('.no-comment').remove();

				post.find('.comment-boxes').append(renderComment(res));

				post.find('.comment-count')
					.text(post.find('.comment-box').length 
						? '(' + post.find('.comment-box').length + ')'
						: '');

					

			});
		}
		
	});

	$(document).on('click', '.comment-edit', function(){
		var comment = $(this).closest('.comment-box');

		if(authId === comment.data('comment-user-id')){
			$.ajax({
				type: 'GET',
				url: '/post/comment/' + comment.data('comment-id'),
				contentType: 'application/json'
			}).then(function(res){

				//$('.modal-comment-id').val(res.id);
				modalCommentInput.val(res.content);
				modalComment.modal('toggle');
				commentForm.data('comment', {id: res.id, user_id: res.user_id});
			});
		}
		
		
	});

	commentForm.on('submit', function(e){
		var self = $(this),
			comment = self.data('comment'),
			commentInput = $('.modal-comment-input');

		e.preventDefault();
		console.log(authId, comment.id);
		if(authId === comment.user_id){
			$.ajax({
				type: 'PUT',
				url: '/post/comment/update/' + comment.id,
				data: JSON.stringify({
					id: comment.id,
					content: commentInput.val(),
					_token: token
				}),
				contentType: 'application/json'
			}).then(function(res){
				var updatedComment = $('.comment-box[data-comment-id="'+res.id+'"]');

				modalCommentInput.val(res.content);
				updatedComment.find('.comment-text').text(res.content);
				updatedComment.find('.comment-edited').text(isEdited(res) ? '| Edited' : '');
				modalComment.modal('toggle');
			});
		}else{
			modalComment.modal('toggle');
		}
		
	});

	$(document).on('click', '.comment-remove', function(e){
		var self = $(this),
			post = self.closest('.post'),
			comment = self.closest('.comment-box');

		e.preventDefault();

		if(comment.data('comment-user-id') === authId || post.data('post-user-id') === authId){
			$.ajax({
				type: 'DELETE',
				url: '/post/comment/delete',
				data: JSON.stringify({
					id: self.parent().data('comment-id'),
					user_id: post.find('[name="user_id"]').val(),
					post_id: post.find('[name="post_id"]').val(),
					_token: token
				}),
				contentType: 'application/json'
			}).then(function(res){
				var parent = $('.post[data-post-id="'+res.deleted.post_id+'"]');

				parent.find('.comment-box[data-comment-id="'+res.deleted.id+'"]').remove();

				if(!res.updated.total){
					parent.find('.comment-boxes')
					.html('<div class="no-comment">No Comments.</div>');
				}

				console.log(res.updated.total);

				parent.find('.comment-count')
					.text(res.updated.total 
						? '('+res.updated.total+')'
						: '');

			});
		}
		
	});


	$(document).on('click', '.view-more-comments', function(){
		var self = $(this);
		pageNumComment++;

		if(!self.hasClass('no-more-comments')){
			$.ajax({
				method: 'GET',
				url: '/comments/paginate',
				data: {
					post_id: self.closest('.post').data('post-id'),
					page: pageNumComment
				},
				contentType: 'application/json'
			}).then(function(res){
				console.log(res);

				if(!res.data.length){
					self.addClass('no-more-comments');
				}

				$.each(res.data, function(i, comment){
					$('.post[data-post-id="'+comment.post_id+'"]').find('.comment-boxes').prepend(renderComment(comment));

					updateComment($('.post[data-post-id="'+comment.post_id+'"]'), comment, res.data);
				})

			});
		}

		
		
	});

	$('.post-add').on('click', function(){
		isEdit = false;
		clear();
		updateModal(this);
	});

	$(document).on('click', '.post-edit', function(e){

		var post = $(this).closest('.post');

		e.preventDefault();

		isEdit = true;

		if(post.data('post-user-id') === authId){
			$.ajax({
				type: 'GET',
				url: '/post/' + post.data('post-id'),
				contentType: 'application/json'
			}).then(function(res){
				modalInputTitle.val(res.title);
				modalInputContent.val(res.content);
				//modalPostId.val(res.id);
				modalForm.modal('toggle');
				postForm.data('post-id', res.id);
			});

			updateModal(this);
		}
		
	});

	postForm.on('submit', function(e){

		var self = $(this),
			postId = self.data('post-id'),
			post = $('.post[data-post-id="'+postId+'"]'),
			data = {
				user_id: authId,
				title: modalInputTitle.val(),
				content: modalInputContent.val(),
				_token: token
			};

		e.preventDefault();

		//console.log(postId);

		if(isEdit && post.data('post-user-id') !== authId){
			return false;
		}

		if(isEdit){
			data.post_id = postId;
		}

		$.ajax({
			type: (isEdit ? 'PUT' : 'POST'),
			url: 'post' + (isEdit ? '/update/' + postId : '/'),
			data: JSON.stringify(data),
			contentType: 'application/json'
		}).then(function(res){

			var post = $('[data-post-id="'+res.id+'"]');
			self.find('.validation-text').text('');
			if(res.errors){
				
				$.each(res.errors, function(key, err){
					self.find('.modal-form-input[name="'+key+'"]')
						.next().text(key ? err : '');
				});
			}else{
				if(isEdit){
					post.find('.post-title').text(res.title);
					post.find('.post-content p').text(res.content);
					post.find('.post-edited').text(isEdited(res) ? '| Edited' : '');
				}else{
					postWrap.prepend(renderPost(res));
					$('.no-post').remove();
				}
				
				modalForm.modal('toggle');
				
			}

		});

	});

	$(document).on('click', '.post-delete', function(e){

		var post = $(this).closest('.post');

		e.preventDefault();

		if(post.data('post-user-id') == authId){
			$.ajax({
				type: 'DELETE',
				url: '/post/delete/',
				data: JSON.stringify({
					id: post.data('post-id'),
					_token: token
				}),
				contentType: 'application/json'
			}).then(function(res){

				post.remove();

				if(!$('.post').length){
					postWrap.html('<div class="no-post card text-center">No post to show.</div>');
				}

			});
		}

	});

	modalForm.on('hide.bs.modal', function(){
		validateTxt.text('');
	});

	win.scroll(function(){
		var height = $(document).height() - win.height(),
			page = $('.page-con').data('page');
			loadPost = $('.post-load'),
			hasMorePost = ((postWrap.data('on-scroll') ? postData.total : 0) > $('.post').length);
			
		if(win.scrollTop() >= height && postWrap.data('on-scroll')){
			pageNumPost++;

			if(hasMorePost){
				loadPost.addClass('show');
			}
			
			if(authId && hasMorePost){
				$.ajax({
					method: 'GET',
					url: '/post/paginate'+(page == 'profile' ? '/profile' : ''),
					data: {
						pageNum: pageNumPost
					},
					contentType: 'application/json'
				}).then(function(res){

					loadPost.removeClass('show');

					$.each(res.data, function(i, data){

						postWrap.append(renderPost(data));

						$.each(data.comments, function(i, comment){

							$('.post[data-post-id="'+data.id+'"]').find('.comment-boxes').append(renderComment(comment));
							
							updateComment($('.post[data-post-id="'+data.id+'"]'), comment, data);
							
							if(res.total === $('.post').length){
								loadPost.remove();
								postWrap.data('load-done', true);
							}

						});

						updatePost($('.post[data-post-id="'+data.id+'"]'), data);

					});


				});

			}

		}
	});

	$('.follow-btn').on('click', function(){
		var self = $(this),
			follow = self.data('follow');

		if(authId){
			$.ajax({
				method: follow.isFollowing ? 'DELETE' : 'POST',
				url: '/user/'+(follow.isFollowing ? "unfollow/" : "follow/") + follow.user_id,
				data: JSON.stringify({
					user_id: follow.user_id,
					_token: token
				}),
				contentType: 'application/json'
			}).then(function(res){
				follow.isFollowing = res.isFollowing;
				self.text(res.isFollowing ? 'Unfollow' : 'Follow')
				self[res.isFollowing ? 'addClass' : 'removeClass']('gray');
			});
		}
		
	});

	function init(){
		//$('.view-more-comments').data('has-comments', true);
		$('.view-more-comments').each(function(){
			$(this).data('has-comments', true);
		});

		notiCount = 0;

		if(authId){
			$.ajax({
				method: 'GET',
				url: '/notifications',
				contentType: 'application/json'
			}).then(function(res){
				console.log(res);
				render = '';

				$.each(res.data, function(i, data){
					render += renderNotification(data);
					if(!data.read_at){
						notiCount++;
					}
				});
				$('.notification-count').text(notiCount);
				$('.notification-items').html(notiCount ? render:'<div class="no-notification">No notifications.</div>');
			});
		}

	}

	var notificationTypes;

	function notification(){

	}

	function renderNotification(notification){
		return '<a class="dropdown-item '+(!notification.read_at ? 'unread': 'read')+'" href="'+notificationTxt(notification).link+'">\
				<span class="notification-name">'+notification.data.name+'</span> '+notificationTxt(notification).msg+'\
			</a>'
	}

	function notificationTxt(notification){

		var noti = {
			msg: '',
			link: '/post/show/'+notification.data.post_id+'?read='+notification.id
		}

		switch(notification.data.action){
			case 'follower':
				noti.link = '/profile/'+notification.data.name+'?read='+notification.id;
				noti.msg = 'is now following you.';
				break;
			case 'poster':
				noti.msg = 'has a new post.';
				break;
			case 'commenter':
				noti.msg = 'commented on your post.';
				break;
			case 'liker':
				noti.msg = 'liked your post.'
		}

		return noti;
	}

	function updateModal(el){
		var modalData = $(el).data('modal-details');
		modalTitle.text(modalData.title);
		modalTitle.closest('#modal-form').find('.submit-post').val(modalData.buttonText);
	}

	function clear(){
		modalInputTitle.val('');
		modalInputContent.val('');
	}

	function formatDate(date, format){
		var format = format ? format : 'MMM. DD, YYYY hh:mma';
		return moment(date, 'YYYY-MM-DD HH:mm:ss').format(format);
	}

	function isYesterday(date){
		var now = moment().format(format),
			date = moment(date, 'MM/DD/YYYY').format('YYYY-MM-DD HH:mm:ss');

		return moment(now).isAfter(date);
	}

	function diffForHumans(date, customFormat){
		var format = 'YYYY, MM, DD, H, mm, ss',
			date = moment(date, customFormat ? customFormat : 'YYYY-MM-DD HH:mm:ss').format(format),
			now = moment().format(format);

		return moment(date.split(',')).from(now.split(','));
	}

	function dateHumanize(date){
		//var date = '2018-09-25 16:03:00',

	  	var theDate = (dateInSeconds(date) < 60) 
	  		? dateInSeconds(date)
	  		: moment.duration({s: dateInSeconds(date)}).humanize();

	  	return theDate + ' ago';
	}
  	

	function dateInSeconds(date){
		var date = moment(date, 'YYYY-MM-DD HH:mm:ss'),
			now = moment().format('YYYY-MM-DD HH:mm:ss');

		return moment(now, 'YYYY-MM-DD HH:mm:ss').diff(date, 'second');
	}

	function isEdited(data){
		return !moment(data.created_at).isSame(data.updated_at);
	}

	function renderComment(data){
		return '<div class="comment-box" data-comment-user-id="'+data.user.id+'" data-comment-id="'+data.id+'">\
			<div class="comment-remove">x</div>\
			<div class="comment-box-head clearfix">\
				<div class="comment-name float-left">\
					<a href="/profile/'+data.user.name+'">'+data.user.name+'</a>\
				</div>\
				<div class="comment-date float-left">Just now</div>\
				<div class="comment-edited edited float-left"></div>\
				<div class="comment-edit float-left">| \
					<i class="fa fa-edit"></i>\
				</div>\
			</div>\
			<div class="comment-text">'+data.content+'</div>\
		</div>';
	}

	function updateComment(el, data, postData){
		if(el.find('.comment-box').length){
			el.find('.no-comment').remove();
			el.find('.comment-box[data-comment-id="'+data.id+'"] .comment-date').text(formatDate(data.created_at.date));

			if(data.user_id !== authId ){
				el.find('.comment-remove').remove();
			}
		}
	}

	function renderPost(data){
		return '<div class="card post" data-post-id="'+data.id+'" data-post-user-id="'+data.user_id+'">\
			<div class="card-body post-body">\
				<div class="post-body-title">\
					<div class="post-title-con">\
						<h4>'+data.title+'</h4>\
						<div class="post-details">\
							<span class="post-date">Just Now</span> by \
							<a class="posted-by" href="profile/'+(authId === data.user_id ? '' :  data.user.name)+'">'+(authId === data.user_id ? "You" :  data.user.name)+'</a>\
							<span class="post-edited edited"></span>\
						</div>\
					</div>\
					<div class="post-action">\
						<div class="dropdown">\
							<span class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">...</span>\
							<div class="dropdown-menu dropdown-menu-right">\
								<a href="/profile/post/'+data.id+'" class="post-edit" data-toggle="modal" data-target="#modal-form" data-modal-details=\'{\"title\": \"Edit Post\",\"buttonText\": \"Update Post\"}\'>edit</a>\
								<a href="/profile/post/delete/" class="post-delete">delete</a>\
							</div>\
						</div>\
					</div>\
				</div>\
				<div class="post-content">\
					<p>'+data.content+'</p>\
				</div>\
			</div>\
			<div class="card-footer">\
				<a href="#" class="comment-btn">\
					<i class="fa fa-comments" aria-hidden="true"></i>Comments \
					<span class="comment-count"></span>\
				</a>\
				<a href="#" class="like-btn like-thumbs' + (data.authLike ? 'liked' : '') + '" data-auth-liked="'+(data.authLike ? 1 : 0)+'">\
					<i class="fa fa-thumbs-up" aria-hidden="true"></i>\
					<span>' + (data.authLike ? 'un' : '') + 'like </span>\
				</a>\
				<div class="likes-count">\
					<div class="likes-count-num" data-count="0"> \
						<span></span>\
					</div>\
					<div class="likes-users">\
						<ul>\
							<li></li>\
						</ul>\
					</div>\
				</div>\
			</div>\
			<div class="comment-section">\
				<div class="comment-boxes">\
					<div class="no-comment">No Comments.</div>\
				</div>\
				<div class="comment-input form-group">\
					<form action="post/comments" method="POST">\
						<input type="hidden" name="user_id" value="'+authId+'">\
						<input type="hidden" name="post_id" value="'+data.id+'">\
						<input type="text" name="content" class="comment form-control" placeholder="Add Comment..."><br>\
						<input type="submit" class="btn btn-secondary float-right add-comment" value="Add Comment">\
					</form>\
				</div>\
			</div>\
		</div>';
	}

	function updatePost(el, data){
		el.find('.comment-count').text(data.comments.length ? '('+data.comments.length+')' : '');
		el.find('.likes-count-num span').text(data.likes.length ? '('+data.likes.length+')' : '');
		el.find('.post-date').text(formatDate(data.created_at.date));
		el.find('.like-thumbs span').text((data.likes.length ? 'un' : '')+'like ');
		el.find('.like-thumbs')[data.likes.length ? 'addClass' : 'removeClass']('liked');

	}

});