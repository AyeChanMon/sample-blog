<?php

namespace App\Http\Controllers;

use App\Article;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Http\Middleware\IsAdmin;

class ArticleController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        //$this->middleware('isAdmin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if(Auth::user()->role == 0){
        //     $articles = Article::orderBy("id","desc")->paginate(5);
        // }else{
        //     $articles = Article::where('user_id',Auth::id())->orderBy("id","desc")->paginate(5);
        // }

        $articles = Article::when(Auth::user()->role!=0,function($query){
            $query-> where('user_id',Auth::id());
        })->when(request()->search,function($query){
            $searchKey = request()->search;
            $query -> where("title","LIKE","%$searchKey%")->orWhere('description','LIKE',"%$searchKey%");
        })->with(['getUser','getPhotos'])->orderBy("id","desc")->paginate(5);
        
        return view('article.index',compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("article.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if(!$request->hasFile('photo')){
        //     return redirect()->back()->withErrors(['photo.*'=>'Photo is required']);
        // }
        $request->validate([
            'title'=>'required|min:10|max:255',
            'description'=>'required|min:30',
            'photo.*'=>'required|mimetypes:image/jpeg,image/png'
        ]);      
       
        $article = new Article();
        $article->title = $request->title;
        $article->description = $request->description;
        $article->user_id = Auth::id();
        $article->save();
       
        if($request->hasFile('photo')){
            $fileNameArr = [];
            foreach($request->file('photo') as $file){
                $newFileName = uniqid()."_profile.".$file->getClientOriginalExtension(); 
                array_push($fileNameArr,$newFileName);
                $dir = '/public/article/';
                $file->storeAs($dir,$newFileName); //use file from request
            }
            foreach($fileNameArr as $f){
                $photo = new Photo();
                $photo->article_id = $article->id;
                $photo->location =$f;
                $photo->save();
            }
        }
        return redirect()->route('article.create')->with('status',"<b>$request->title</b> is added.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view('article.show',compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view('article.edit',compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title'=>'required|min:10|max:255',
            'description'=>'required|min:30'
        ]);
        $article->title = $request->title;
        $article->description = $request->description;
        $article->update();
        return redirect()->route('article.index')->with('updateStatus',"<b>$request->title</b> is updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        if(Gate::allows('article-delete',$article)){
            if(isset($article->getPhotos)){
                $dir = "/public/article/";
                foreach($article->getPhotos as $p){
                    Storage::delete($dir.$p->location);
                }
                $toDel = $article->getPhotos->pluck("id");
                Photo::destroy($toDel);
            }
            $title = $article->title;
            $article->delete();
            return redirect()->route('article.index')->with('status',"<b>$title</b> is deleted.");
        }
        return abort(404);        
    }

    // public function search(Request $request){
    //     $searchKey = $request->search;
    //     $articles = Article::where('title',"LIKE","%$searchKey%")->orWhere('description',"LIKE","%$searchKey%") ->paginate(5);
    //     return view('article.index',compact('articles'));
    // }
}
