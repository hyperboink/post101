@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Post</div>

                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{route('post.save')}}" method="post">
                        {{csrf_field()}}
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        <div class="form-group">
                            <input type="text" name="title" placeholder="Title" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="content" placeholder="Content" rows="6"></textarea>
                        </div> 
                        <div class="form-group text-right">
                            <input type="submit" class="btn btn-primary"value="Create">
                        </div> 
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
