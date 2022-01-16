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
                    <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                @endcomponent
            </div>
            <div class="col-12 col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Edit Profile</div>
                    <div class="card-body">
                        <img src="{{ asset("storage/profile/".Auth::user()->photo) }}" class="w-100 rounded" alt="">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="photo" class="mt-3">Choose New Photo</label>
                                <input type="file" name="photo" id="photo" class="form-control p-1">
                                @error('photo')
                                    <small class="fw-bold text-danger">{{ $message }}</small>
                                    @enderror
                            </div>
                            <button class="btn btn-primary w-100">Update Profile Photo</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Change Password</div>
                    <div class="card-body">
                    <form method="POST" action="{{ route('profile.changePassword') }}">
                        @csrf 
   
                         @foreach ($errors->all() as $error)
                            <p class="text-danger">{{ $error }}</p>
                         @endforeach 
  
                        <div class="form-group">
                            <label for="password">Current Password</label>
                            <input id="password" type="password" class="form-control" name="current_password" autocomplete="current-password">
                        </div>
  
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input id="new_password" type="password" class="form-control" name="new_password" autocomplete="current-password">
                        </div>
  
                        <div class="form-group">
                            <label for="password">New Confirm Password</label>
                            <input id="new_confirm_password" type="password" class="form-control" name="new_confirm_password" autocomplete="current-password">
                        </div>
   
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                    </form>
                    </div>
                </div>
            </div>
            <div class="">
                @isset($arr)
                    <ul>
                    @foreach($arr as $a)
                        @if($a != '.' && $a !='..')
                            <li>
                                <img src="{{ asset("storage/".$a) }}" alt="" style="width:150px">
                            </li>
                            @endif
                    @endforeach
                    </ul>
                @endisset
            </div>
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header">Uploaded Photo</div>
                    <div class="card-body">
                    @foreach(Auth::user()->getPhoto as $img)
                        <div class="article-thumbnail shadow-sm" style="background-image: url('{{ asset("storage/article/".$img->location) }}')"></div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection