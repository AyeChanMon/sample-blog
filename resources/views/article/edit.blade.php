@extends('layouts.app')
@section('head')
<style>
    .article-thumbnail{
        margin-top: 10px;
        width:150px;
        height:150px;
        display: inline-block;
        border-radius: 0.25rem;
        background-size:cover;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                @component("component.breadcrumb")
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('article.index') }}">Article</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Article</li>
                @endcomponent
                <div class="card">
                    <div class="card-header">Update Article</div>
                    <div class="card-body">
                        @if(session('status'))
                            <p class="alert alert-success">
                                {!! session('status') !!}
                            </p>
                            @endif
                        <form action="{{ route('article.update',$article->id) }}" id="article-form" method="POST">
                            @csrf
                            @method('put')
                            <div class="form-group">
                                <label for="title">Article Title</label>
                                <input type="text" name="title" id="title" value="{{ $article->title }}" class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                    <small class="fw-bold text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description"></label>
                                <textarea name="description" id="description" rows="10" class="form-control @error('description') is-invalid @enderror">{{ $article->description }}</textarea>
                                @error('description')
                                    <small class="fw-bold text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button class="btn btn-primary" form="article-form">Update Article</button>
                        </form>
                            @foreach($article->getPhotos as $img)
                                <div class="d-inline-block">
                                    <div class="article-thumbnail shadow-sm" style="background-image: url('{{ asset("storage/article/".$img->location) }}')"></div>
                                    <form action="{{ route('photo.destroy',$img->id) }}" method="POST">
                                        @csrf
                                        @method("delete")
                                        <button class="btn btn-danger btn-sm" style="margin-top:-80px;margin-left:5px;">Delete</button>
                                    </form>
                                </div>
                                @endforeach
                            <form action="{{ route('photo.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="article_id" value="{{ $article->id }}">
                                <div class="form-row">
                                    <div class="col-6">
                                        <input type="file" name="photo[]" id="photo" class="form-control p-1" multiple required>
                                        @error('photo.*')
                                            <small class="fw-bold text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-primary">Upload New Photo</button>
                                    </div>
                                </div>
                            </form>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection