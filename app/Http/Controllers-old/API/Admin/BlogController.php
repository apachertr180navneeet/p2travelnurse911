<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
use DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }


    ## Function to get blogs
    public function getBlogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $blogs = DB::table('blogs as b')
                ->select('b.*')
                ->where('b.deleted_at', NULL);

            if (isset($request->keyword) && !empty($request->keyword)) {
                $blogs->where(function ($query) use ($request) {
                    $query->where('b.title', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('b.short_description', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('b.description', 'LIKE', "%{$request->keyword}%");
                });
            }
            if (isset($request->status) && $request->status != 'all') {
                $blogs->where('b.status', $request->status);
            }


            $blogs = $blogs->orderBy('b.id', 'desc')->get()
            ->map(function ($blogs) {
                // Add dir_path column and its value to each record
                $blogs->profile_pic = !empty($blogs->image) ? url(config('custom.blog_folder') . $blogs->image) : public_path('assets/images/default.jpg');
                return $blogs;
            })
                ->toArray();


            $result = array('status' => true, 'message' => (count($blogs)) . " Record found", 'data' => $blogs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    public function generateBlogSlug($title, $id = null) {
        
        $slug = Str::slug($title);
        
        $originalSlug = $slug;
        $iteration = 1;
        
        if($id != null)
        {
            while (DB::table('blogs')->where('slug', $slug)->where('id','!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $iteration;
                $iteration++;
            }
        }
        else
        {
            while (DB::table('blogs')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $iteration;
                $iteration++;
            }
        }    
        return $slug;
    }
    
    ## Function to add/update blogs
    public function updateBlog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'short_description' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = $request->all();
            
            if ($request->file('profile_pic')) {
                $file = $request->file('profile_pic');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() * rand() . '.' . $ext;
                $path = config('custom.blog_folder');
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $param['image'] = $fileName;
                }
            }
            
            $user_id = $request->user_id;
            unset($param['user_id']);
            unset($param['profile_pic']);

            if (isset($request->id)) {
                $param['slug'] = $this->generateBlogSlug($request->title, $request->id);
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('blogs')->where('id', $request->id)->update($param);
                $msg = "Blog has been been successfully updated";
            } else {
                $param['slug'] = $this->generateBlogSlug($request->title);
                $param['status'] = 1;
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('blogs')->insert($param);
                $msg = "Blog has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to delete blog
    public function deleteBlog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

            $param['deleted_at'] = $this->entryDate;
            $param['updated_at'] = $this->entryDate;
            $param['updated_by'] = $request->user_id;
            DB::table('blogs')->where('id', $request->id)->update($param);
            $msg = "Blog has been been successfully deleted";


            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update blog's status
    public function updateBlogStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();


            $param = array(
                'status' => $request->status,
                'updated_at' => $this->entryDate,
                'updated_by' => $request->user_id,
            );
            DB::table('blogs')->where('id', $request->id)->update($param);
            $msg = "Blog status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    
}
