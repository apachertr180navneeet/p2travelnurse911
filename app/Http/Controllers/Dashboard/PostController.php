<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Post::with(["newscategory", "tags", "user"])
                 ->withCount(["comments"])
                 ->orderBy("id", "DESC");

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
    
        if ($request->filled('category')) {
            $query->where('news_category_id', $request->category);
        }
    
        if ($request->filled('posted_date')) {
            $query->whereDate('posted_date', $request->posted_date);
        }
    
        if ($request->filled('status')) {
            $status = $request->status === 'published' ? 1 : 0;
            $query->where('status', $status);
        }
    
        $posts = $query->paginate(20)->appends(request()->query());

        $categories = NewsCategory::where("status", true)->orderBy("id", "ASC")->get();
        return view("dashboard.post.index", compact("posts","categories"));
    }

    public function create()
    {
        $categories = NewsCategory::where("status", true)->orderBy("title", "ASC")->get();
        $tags = Tag::orderBy("name", "ASC")->get();
        return view("dashboard.post.add", compact("categories", "tags"));
    }

    /*
    public function store(Request $request) {
        $validated = $request->validate([
            "title" => ["required", "string"],
            "slug" => ["required", "string", "unique:posts,slug"],
            "content" => ["required", "string"],
            "category" => ["required", "exists:news_categories,id"],
            "tags" => ["nullable", "array"],           
            "status" => ["required", Rule::in(["0", "1"])],
            "thumbnail" => ["nullable", "image"],
        ]);

        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $image = $request->file("thumbnail");
            $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
            $image->move(public_path("uploads/news"), $imageName);
        } else {
            // Handle the case where no image is uploaded or the upload failed
            $imageName = null; // or set a default value if necessary
        }
        $post = Post::create([
            "user_id" => Auth::user()->id,
            "title" => $validated["title"],
            "slug" => Str::slug($validated["slug"]),
            "news_category_id" => $validated["category"],
            "content" => $validated["content"],
            "thumbnail" => $imageName,
            "status" => Auth::user()->role == 1 ? "0" : $validated["status"],
        ]);
        if (Arr::has($validated, "tags")) {
            foreach ($validated["tags"] as $tag) {
                $tag = Tag::firstOrCreate(["name" => Str::lower($tag)]);
                //$post->tags()->attach([$tag->id]);
                $post->tags()->attach($tag->id);
            }
        }
        return redirect()->route("dashboard.posts.index")->with("success", "News created!");
    }
    */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            "title" => ["required", "string"],
            // "slug" => ["required", "string", "unique:posts,slug"],
            
            "slug" => [
                $request->filled('link') ? "nullable" : "required",
                $request->filled('link') ? null : "unique:posts,slug",
            ],
            "content" => ["required", "string"],
            "category" => ["required", "exists:news_categories,id"],
            "tags" => ["nullable", "array"],
            "status" => ["required", Rule::in(["0", "1"])],
            "thumbnail" => ["nullable", "image"],
            "link" => [
                $request->filled('slug') ? "nullable" : "required",
                "nullable",
                "url"
            ],
            "posted_date" => ["required"],
        ]);
    

        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $image = $request->file("thumbnail");
            $imageName = md5(time() . rand(11111, 99999)) . "." . $image->extension();
            $image->move(public_path("uploads/news"), $imageName);
        } else {
            // Handle the case where no image is uploaded or the upload failed
            $imageName = null; // or set a default value if necessary
        }


        if (!empty($request->link)) {
            $slugData = $request->link;
            $isExternalUrl = $request->option;
        } else {
            $slugData = Str::slug($validated['slug']);
            $isExternalUrl = 0;
        }
        // echo $slugData;

        $post = Post::create([
            "user_id" => Auth::user()->id,
            "title" => $validated["title"],
            "slug" => $slugData,
            "news_category_id" => $validated["category"],
            "is_external_url" => $isExternalUrl,
            "content" => $validated["content"],
            "thumbnail" => $imageName,
            "status" => Auth::user()->role == 1 ? "0" : $validated["status"],
            "posted_date" => $validated["posted_date"]
        ]);
        if (Arr::has($validated, "tags")) {
            foreach ($validated["tags"] as $tag) {
                $tag = Tag::firstOrCreate(["name" => Str::lower($tag)]);
                //$post->tags()->attach([$tag->id]);
                $post->tags()->attach($tag->id);
            }
        }
        return redirect()->route("dashboard.posts.index")->with("success", "News created!");
    }

    public function edit($id)
    {
        $post = Post::with(["tags"])->withCount(["tags"])->find($id);
        $categories = NewsCategory::where("status", true)->orderBy("title", "ASC")->get();
        $tags = Tag::orderBy("name", "ASC")->get();
        return view("dashboard.post.edit", compact("post", "categories", "tags"));

        return back()->withErrors("Post not exists!");
    }
    /*
    public function update(Request $request, $id) {
        $post = Post::find($id);        
        if($post){
            $validated = $request->validate([
                "title" => ["required", "string"],
                "slug" => ["required", "string", Rule::unique("posts", "slug")->ignore($post->id)],
                "content" => ["required", "string"],
                "category" => ["required", "exists:news_categories,id"],
                "tags" => ["nullable", "array"],
                "featured" => ["nullable", Rule::in(["0", "1"])],
                "comment" => ["nullable", Rule::in(["0", "1"])],
                "status" => ["required", Rule::in(["0", "1"])],
                "thumbnail" => ["nullable", "image"],
            ]);
            $post->title = $validated["title"];
            $post->slug = Str::slug($validated["slug"]);
            $post->news_category_id = $validated["category"];
            $post->content = $validated["content"];
            $post->is_featured = Arr::has($validated, "featured");
            $post->enable_comment = Arr::has($validated, "comment");
            $post->status = Auth::user()->role == 1 ? "0" : $validated["status"];
            if ($request->hasFile("thumbnail")) {
                $image = $request->file("thumbnail");
                $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
                $image->move(public_path("uploads/news"), $imageName);
                if (File::exists(public_path("uploads/news/".$post->thumbnail))) {
                    File::delete(public_path("uploads/news/".$post->thumbnail));
                }
                $post->thumbnail = $imageName;
            }
            $post->save();
            if (Arr::has($validated, "tags")) {
                $tagArr = [];               
                foreach ($validated["tags"] as $tag) {
                    $tag = Tag::firstOrCreate(["name" => Str::lower($tag)]);                   
                    $tagArr[] = $tag->id;
                }
                $post->tags()->sync($tagArr);
            } else {
                $post->tags()->sync([]);
            }
            return redirect()->route("dashboard.posts.index")->with("success", "News updated!");
        }
        
        return back()->withErrors("Post not exists!");
    }
   */

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if ($post) {
            $validated = $request->validate([
                "title" => ["required", "string"],

                // "slug" => ["required", "string", Rule::unique("posts", "slug")->ignore($post->id)],

                // Conditional validation for 'slug'
                "slug" => [
                    $request->has('link') ? "nullable" : "required",  // If 'link' exists, 'slug' is nullable
                    "string",
                    $request->has('link') ? "nullable" : Rule::unique("posts", "slug")->ignore($id), // Unique slug unless 'link' exists
                ],

                // Conditional validation for 'link'
                "link" => [
                    $request->has('slug') ? "nullable" : "required",  // If 'slug' exists, 'link' is nullable
                    "url"  // Ensure 'link' is a valid URL if provided
                ],



                "content" => ["required", "string"],
                "category" => ["required", "exists:news_categories,id"],
                "tags" => ["nullable", "array"],
                "featured" => ["nullable", Rule::in(["0", "1"])],
                "comment" => ["nullable", Rule::in(["0", "1"])],
                "status" => ["required", Rule::in(["0", "1"])],
                "thumbnail" => ["nullable", "image"],
                "posted_date" => ["required", "string"]
            ]);

            // Conditional logic for 'slugData' and 'isExternalUrl'
            if (!empty($request->link)) {
                $slugData = $request->link;  // Use the link as slugData
                $isExternalUrl = $request->option;  // Set the external URL flag based on 'option' field
            } else {
                $slugData = Str::slug($validated['slug']);  // Generate slug from 'slug' field
                $isExternalUrl = 0;  // Set to 0 if it's not an external URL
            }

            $post->title = $validated["title"];
            $post->slug = $slugData;
            $post->news_category_id = $validated["category"];
            $post->content = $validated["content"];
            $post->is_featured = Arr::has($validated, "featured");
            $post->enable_comment = Arr::has($validated, "comment");
            $post->status = Auth::user()->role == 1 ? "0" : $validated["status"];
            $post->is_external_url = $isExternalUrl;
            $post->posted_date = $validated["posted_date"];

            if ($request->hasFile("thumbnail")) {
                $image = $request->file("thumbnail");
                $imageName = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                $image->move(public_path("uploads/news"), $imageName);
                if (File::exists(public_path("uploads/news/" . $post->thumbnail))) {
                    File::delete(public_path("uploads/news/" . $post->thumbnail));
                }
                $post->thumbnail = $imageName;
            }
            $post->save();
            if (Arr::has($validated, "tags")) {
                $tagArr = [];
                foreach ($validated["tags"] as $tag) {
                    $tag = Tag::firstOrCreate(["name" => Str::lower($tag)]);
                    $tagArr[] = $tag->id;
                }
                $post->tags()->sync($tagArr);
            } else {
                $post->tags()->sync([]);
            }
            return redirect()->route("dashboard.posts.index")->with("success", "News updated!");
        }

        return back()->withErrors("Post not exists!");
    }


    public function destroy($id)
    {
        $post = Post::find($id);

        $post->delete();
        return back()->with("success", "Post deleted!");

        return back()->withErrors("Post not exists!");
    }

    public function status($id)
    {
        $post = Post::find($id);
        $post->status = $post->status ? "0" : "1";
        $post->save();
        $alert = $post->status ? "Post published!" : "Post drafted!";
        return back()->with("success", $alert);

        return back()->withErrors("Post not exists!");
    }

    public function featured($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->is_featured = $post->is_featured ? "0" : "1";
            $post->save();
            $alert = $post->is_featured ? "Post added to featured!" : "Post removed from featured!";
            return back()->with("success", $alert);
        }
        return back()->withErrors("Post not exists!");
    }

    public function comment($id)
    {
        $post = Post::find($id);

        $post->enable_comment = $post->enable_comment ? "0" : "1";
        $post->save();
        $alert = $post->enable_comment ? "Post comment enabled!" : "Post comment disabled!";
        return back()->with("success", $alert);

        return back()->withErrors("Post not exists!");
    }


    public function trashed()
    {

        $posts = Post::onlyTrashed()->with(["newscategory" => function ($q) {
            $q->withTrashed();
        }, "tags", "user"])->withCount(["comments" => function ($q) {
            $q->withTrashed();
        }])->orderBy("id", "DESC")->paginate(20);

        return view("dashboard.post.trashed", compact("posts"));
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->find($id);

        if ($post->newscategory()->withTrashed()->first()->deleted_at) {
            return back()->withErrors("Restore the category first!");
        }
        $post->restore();
        return back()->with("success", "Post restored!");

        return back()->withErrors("Post not exists!");
    }

    public function delete($id)
    {
        $post = Post::onlyTrashed()->find($id);
        if (File::exists(public_path("uploads/news/" . $post->thumbnail))) {
            File::delete(public_path("uploads/news/" . $post->thumbnail));
        }
        $post->tags()->sync([]);
        $post->comments()->forceDelete();
        $post->forceDelete();
        return back()->with("success", "Post deleted!");

        return back()->withErrors("Post not exists!");
    }
}
