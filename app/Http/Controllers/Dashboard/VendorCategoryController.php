<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class VendorCategoryController extends Controller
{
    public function index() {
        
        $categories = VendorCategory::withCount(["vendorsubcategories" => function($q) {
            $q->withTrashed();
        }])->orderBy("title", "ASC")->paginate(20);
        
        return view("dashboard.vendorcategories.index", compact("categories"));
    }

    public function create() {
        return view("dashboard.vendorcategories.add");
    }

    public function store(Request $request) {
       
        $validated = $request->validate([
            "title" => ["required", "string", "max:150"],
            "slug" => ["required", "max:150", "unique:vendor_categories,slug"],
            "description" => ["nullable", "string"],
            // "image" => ["nullable", "image"],
            "status" => ["required", Rule::in(["0", "1"])],
        ]);
       
        // if (Arr::has($validated, "image")) {
        //     $image = $request->file("image");
        //     $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
        //     $image->move(public_path("uploads/vendorcategory"), $imageName);
        // }
        
        VendorCategory::create([
            "title" => $validated["title"],
            "slug" => $validated["slug"],
            "description" => Arr::has($validated, "description") ? $validated["description"] : null,
            // "image" => Arr::has($validated, "image") ? $imageName : null,
            "status" => $validated["status"],
        ]);
       
        return redirect()->route("dashboard.vendorcategories.index")->with("success", "Category created!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {

    }

    public function edit(string $id) {
        $category = VendorCategory::find($id);
        if ($category) {
            return view("dashboard.vendorcategories.edit", compact("category"));
        }
        return back()->withErrors("Category not exists!");
    }

    public function update(Request $request, string $id) {
        $category = VendorCategory::find($id);
        if ($category) {
            $validated = $request->validate([
                "title" => ["required", "string", "max:150"],
                "slug" => ["required", "max:150", Rule::unique("vendor_categories", "slug")->ignore($id)],
                "description" => ["nullable", "string"],
                // "image" => ["nullable", "image"],
                "status" => ["required", Rule::in(["0", "1"])],
            ]);
            $category->title = $validated["title"];
            $category->slug = $validated["slug"];
            $category->description = Arr::has($validated, "description") ? $validated["description"] : null;
            $category->status = $validated["status"];
            // if (Arr::has($validated, "image")) {
            //     $image = $request->file("image");
            //     $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
            //     $image->move(public_path("uploads/vendorcategory"), $imageName);
            //     if ($category->image) {
            //         if (File::exists(public_path("uploads/vendorcategory/".$category->image))) {
            //             File::delete(public_path("uploads/vendorcategory/".$category->image));
            //         }
            //     }
            //     $category->image = $imageName;
            // }
            $category->save();
            return redirect()->route("dashboard.vendorcategories.index")->with("success", "Category updated!");
        }
        return back()->withErrors("Category not exists!");
    }

    public function destroy(string $id) {
        $category = VendorCategory::find($id);
        if ($category) {
            $category->delete();
            return back()->with("success", "Category deleted!");
        }
        return back()->withErrors("Category not exists!");
    }

    public function restore($id) {
        $category = VendorCategory::onlyTrashed()->find($id);
        if ($category) {
            $category->restore();
            return back()->with("success", "Category restored!");
        }
        return back()->withErrors("Category not exists!");
    }

    public function status($id) {
        $category = VendorCategory::find($id);
        if ($category) {
            $category->status = $category->status ? "0" : "1";
            $category->save();
            $alert = $category->status ? "Category Activated!" : "Category Inactivated!";
            return back()->with("success", $alert);
        }
        return back()->withErrors("Category not exists!");
    }

    public function trashed() {
        $categories = VendorCategory::onlyTrashed()->withCount(["vendorsubcategories" => function($q) {
            $q->withTrashed();
        }])->orderBy("title", "ASC")->paginate(20);
    
        return view("dashboard.vendorcategories.trashed", compact("categories"));
    }

    public function delete($id) {
        $category = VendorCategory::onlyTrashed()->find($id);
        if ($category) {
            // if (File::exists(public_path("uploads/vendorcategory/".$category->image))) {
            //     File::delete(public_path("uploads/vendorcategory/".$category->image));
            // }
            $category->vendorsubcategories()->forceDelete();
            $category->forceDelete();
            return back()->with("success", "Category deleted!");
        }   
        return back()->withErrors("Category not exists!");
    }
}
