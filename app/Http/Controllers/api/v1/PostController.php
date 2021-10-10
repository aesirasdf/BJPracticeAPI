<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    //
    public function Index(){
        return response(["posts" => DB::select('select p.id, p.name, p.content, p.post_id, p.created_at, p.updated_at, (SELECT count(*) FROM posts c WHERE p.id = c.post_id) commentCount from posts p WHERE p.post_id is null')]);
    }

    public function Create(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'content' => 'required|string',
                'post_id' => 'numeric|exists:Post,id'
            ]
        );
        if($validator->fails()){
            return response(['messages' => $validator->errors()]);
        }
        else{
            return response(['post' => Post::create($request->all())]);
        }
    }

    public function View($id){
        $validator = Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => 'required|exists:Posts,id|numeric'
            ]
        );
        if($validator->fails()){
            return response(['messages' => $validator->errors()], 403);
        }
        else{
            $post = Post::find($id);
            $comments = DB::select("select p.id, p.name, p.content, p.post_id, p.created_at, p.updated_at, (SELECT COUNT(*) FROM posts c WHERE c.post_id = p.id) commentCount from posts p WHERE p.post_id = :id", ['id' => $id]);
            return response(["post" => Post::find($id), "comments" => $comments]);
        }
    }

    public function Edit($id, Request $request){
        $data = [
            'id' => $id,
            'name' => $request->name,
            'content' => $request->content,
        ];
        $validator = Validator::make(
            $data,
            [
                'id' => 'required|exists:Posts,id|numeric',
                'name' => 'required|string',
                'content' => 'required|string'
            ]
        );
        $post = Post::find($id);
        $code = 403;
        if($post && $post->name != $request->name){
            $validator->errors()->add('name', 'You are not the creator of this object.');
            $code = 401;
        }
        $errors = $validator->errors();
        if(count($errors) > 0){
            return response(['messages' => $errors], $code);
        }
        else{
            Post::find($data['id'])->update($request->all());
            return response(['messages' => ['post' => "Post has been edited!"]]);
        }

    }

    public function Delete(Request $request, $id){
        $data = [
            'id' => $id,
            'name' => $request->name
        ];
        $validator = Validator::make(
            $data,
            [
                'id' => 'required|exists:Posts,id|numeric',
                'name' => 'required|string'
            ]
        );
        $post = Post::find($id);
        $code = 403;
        if($post && $post->name != $request->name){
            $validator->errors()->add('name', 'You are not the creator of this object.');
            $code = 401;
        }
        $errors = $validator->errors();
        if(count($errors) > 0){
            return response(['messages' => $validator->errors()]);
        }
        else{
            $count = count(DB::select("select * from posts WHERE post_id = :id", ['id' => $id]));
            if($count > 0){
                return response(['messages' => ['post' => "Post has comments, you can't delete this!"]]);
            }
            else
                $post->delete();
                return response(["messages" => ["post" => "Post has been deleted!"]]);
        }
    }
    public function Comment(Request $request, $id){
        $data = [
            'name' => $request->name,
            'content' => $request->content,
            'post_id' => $id
        ];
        $validator = Validator::make(
            $data,
            [
                'name' => 'required|string',
                'content' => 'required|string',
                'post_id' => 'required|numeric|exists:Posts,id'
            ]
        );
        if($validator->fails()){
            return response(["messages" => $validator->errors()], 403);
        }
        else{
            return response(['post' => Post::create($data)]);
        }
    }


}
