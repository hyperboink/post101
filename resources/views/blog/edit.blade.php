@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Post</div>

                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="/profile/post/update/{{$post->id}}" method="post">
                        {{csrf_field()}}
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        <div class="form-group">
                            <input type="text" name="title" placeholder="Title" class="form-control" value="{{$post->title}}">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="content" placeholder="Content" rows="6">{{$post->content}}</textarea>
                        </div> 
                        <div class="form-group text-right">
                            <a href="/profile" class="btn btn-danger">Cancel</a>
                            <input type="submit" class="btn btn-primary" value="Update">
                        </div> 
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
