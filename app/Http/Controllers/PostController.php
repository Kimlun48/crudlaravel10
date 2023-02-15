<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
class PostController extends Controller
{
    public function index(){
        //get posts
        $posts = Post::latest()->paginate(5);
        //render view with posts
        return view('posts.index', compact('posts'));
    }

    public function create(){
        return view('posts.create');
    }

    public function store(Request $request){
        //valiadet form
        $this->validate($request,[
            'image' => 'required|image|mimes:jpeg,jpg.png|max:6048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
        $image = $request->file('image');
        $image -> storeAs('public/posts', $image->hashName());
        //create posts
        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);
        //riderct to index
        return redirect()->route('posts.index')->with(['success'=> 'Data Berhasil Disimpan']);
    }

    public function show($id)
    {
        //get psot by id
        $post = Post::findOrFail($id);
        //render view with post
        return view('posts.show', compact('post'));
    }

    public function edit($id)
    {
        //get psot by id
        $post = Post::findOrFail($id);
        //render view with post
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
          //validate form
          $this->validate($request, [
            'image'     => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //get post by ID
        $post = Post::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.$post->image);

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

public function destroy($id)
{
     //get post by ID
     $post = Post::findOrFail($id);

     //delete image
     Storage::delete('public/posts/'. $post->image);

     //delete post
     $post->delete();

     //redirect to index
     return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
}

}
