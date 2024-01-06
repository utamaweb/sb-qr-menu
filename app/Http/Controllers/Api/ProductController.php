<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() {
        $products = Product::get()->map(function ($item) {
                                $item->image = $item->image ? url('storage/product_images/'.$item->image) : "";
                                return $item;
                            });
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // return $data;

        $validator = Validator::make($data, [
            'title' => 'required|string',
            'content' => 'required|string',
            'tag' => 'required',
            'ingredient' => 'required',
            'thumbnail' => 'file|image|mimes:jpeg,png,jpg|max:8012',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $post = Post::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                // 'thumbnail' => $thumbnail,
                'user_id' => auth()->user()->id,
            ]);
            if (isset($request->tag)) {
                $this->handleTags($request, $post);
            }
            if (isset($request->ingredient)) {
                $this->handleIngredients($request, $post);
            }
            $thumbnail = "https://storage.googleapis.com/ecocrafters_bucket/post_thumbnail/default-image.png";
            if ($request->thumbnail){
                $thumbnail = $request->hasFile('thumbnail') ? $this->uploadFile($request->file('thumbnail'), 'post_thumbnail', $post->id . "-" . Str::slug($request->title)) : null;
                // $data['thumbnail'] = $link;
            }
            $post->update([
                'thumbnail' => $thumbnail,
            ]);
            DB::commit();
            return response()->json($post, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function detail($id)
    {
        $product = Product::where('id', $id)->first();
        if ($product == NULL){
            return response()->json(['message' => 'Data Tidak Ditemukan.'], 404);
        }
        $product->image = $product->image ? url('storage/product_images/'.$product->image) : "";
        return response()->json($product, 200);
    }

    public function getPostByTitle(Request $request, $title)
    {
        $posts = Post::where('title', 'LIKE', '%'.$title.'%')->with('user')->get();
        $posts->map(function ($item) {
            $item['user']['avatar_url'] = $item['user']['avatar'] ? "https://storage.googleapis.com/ecocrafters_bucket/".$item['user']['avatar'] : "https://storage.googleapis.com/ecocrafters-api.appspot.com/avatar.png";

            return $item;
        });
        return response()->json($posts, 200);
    }

    public function getAllPosts(Request $request)
    {
        $posts = Post::with('user')->get();
        $posts->map(function ($item) {
            $item['user']['avatar_url'] = $item['user']['avatar'] ? "https://storage.googleapis.com/ecocrafters_bucket/".$item['user']['avatar'] : "https://storage.googleapis.com/ecocrafters-api.appspot.com/avatar.png";

            return $item;
        });
        return response()->json($posts, 200);
    }

    public function searchPostOrUser(Request $request, $search)
    {
        $result['posts'] = Post::where('title', 'LIKE', '%'.$search.'%')->get();
        $result['users'] = User::where('full_name', 'LIKE', '%'.$search.'%')->get();
        if ($result['posts']->count() < 1 && $result['users']->count() < 1) {
            return response()->json(['message' => 'Posts & Users Are Not Available..'], 404);
        }
        return response()->json($result, 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        // return $data;

        $validator = Validator::make($data, [
            'title' => 'required|string',
            'content' => 'required|string',
            'thumbnail' => 'file|image|mimes:jpeg,png,jpg|max:8012',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $post = Post::find($id);
            if ($request->thumbnail && $post->thumbnail != "https://storage.googleapis.com/ecocrafters_bucket/post_thumbnail/default-image.png"){
                $this->deleteFile($post->thumbnail);
                $thumbnail = $request->hasFile('thumbnail') ? $this->uploadFile($request->file('thumbnail'), 'post_thumbnail', $post->id . "-" . Str::slug($request->title)) : null;
                $post->thumbnail = $thumbnail;
            } else {
                $thumbnail = $request->hasFile('thumbnail') ? $this->uploadFile($request->file('thumbnail'), 'post_thumbnail', $post->id . "-" . Str::slug($request->title)) : null;
                $post->thumbnail = $thumbnail;
            }
            $post->title = $request->title;
            $post->content = $request->content;
            $post->slug = Str::slug($request->title);
            // $data = $request->only('title', 'content', 'thumbnail');
            $post->update();
            if (isset($request->tag)) {
                $this->handleTags($request, $post);
            }
            if (isset($request->ingredient)) {
                $this->handleIngredients($request, $post);
            }
            DB::commit();
            return response()->json($post, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function deleteFile($path = null)
    {
        Storage::disk('gcs')->delete($path);
    }

    public function delete(Request $request, $id)
    {
        $post = Post::find($id);
        if ($post->thumbnail != "https://storage.googleapis.com/ecocrafters_bucket/post_thumbnail/default-image.png") {
            Storage::disk('gcs')->delete($post->thumbnail);
        }
        $post->delete();
        return response()->json(['message' => 'Post Succesfully Deleted.'], 200);
    }

    public function savePost(Request $request, $id)
    {
        $post = Post::find($id);
        $user = auth()->user()->id;
        $check = UserSavePost::wherePostId($id)->whereUserId($user)->exists();
        if ($check == False){
            UserSavePost::create([
                'user_id' => $user,
                'post_id' => $id,
            ]);
        } else {
            return response()->json(['message' => 'Post Already Saved Before.'], 500);
        }
        return response()->json(['message' => 'Post Succesfully Saved.'], 200);
    }
}
