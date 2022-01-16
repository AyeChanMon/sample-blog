<?php

namespace App\Http\Controllers;

use App\User;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit(){
        return view('profile.edit');
    }

    public function update(Request $request){
        $request ->validate([
            "photo"=>"required|mimes:jpg,png,jpeg"
        ]);
        $file = $request->file('photo');
        $newFileName = uniqid()."_profile.".$file->getClientOriginalExtension(); 
        $dir = '/public/profile/';
        //$file->move("store/",$newFileName); //to public
        //Storage::put("/public",$file);
        //Storage::putFileAs($dir,$file,$newFileName); //use storage access
        $file->storeAs($dir,$newFileName); //use file from request
        $user = User::find(Auth::id());
        $user->photo = $newFileName;
        $user->update();
        //$arr = scandir(public_path("/storage"));
        return redirect()->route('profile.edit');
    }

    public function changePassword(Request $request){
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        //User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        $user = new User();
        $currentUser = $user->find(Auth::id());
        $currentUser->password = Hash::make($request->new_password);
        $currentUser -> update();
        Auth::logout();
        return redirect()->route('login');
    }
}
