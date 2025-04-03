<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use App\Models\VendorSubCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class VendorSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subcategories = VendorSubCategory::with("vendorcategory")->orderBy("title", "ASC")->paginate(20);
        return view("dashboard.vendorsubcategories.index", compact("subcategories"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $categories = VendorCategory::all();
        return view("dashboard.vendorsubcategories.add", compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validated = $request->validate([
            "title" => ["required", "string", "max:150"],
            "slug" => ["required", "max:150", "unique:vendor_subcategories,slug"],
            "vendor_category_id" => ["required", "exists:vendor_categories,id"],
            "description" => ["nullable", "string"],
            // "image" => ["nullable", "image"],
            "status" => ["required", Rule::in(["0", "1"])],
        ]);

        // if (Arr::has($validated, "image")) {
        //     $image = $request->file("image");
        //     $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
        //     $image->move(public_path("uploads/vendorsubcategory"), $imageName);
        // }

        VendorSubCategory::create([
            "title" => $validated["title"],
            "slug" => $validated["slug"],
            "vendor_category_id" => $validated["vendor_category_id"],
            "description" => Arr::has($validated, "description") ? $validated["description"] : null,
            // "image" => Arr::has($validated, "image") ? $imageName : null,
            "status" => $validated["status"],
        ]);

        return redirect()->route("dashboard.vendorsubcategories.index")->with("success", "Subcategory created!");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id) {
        $subcategory = VendorSubCategory::find($id);
        $categories = VendorCategory::all();
        if ($subcategory) {
            return view("dashboard.vendorsubcategories.edit", compact("subcategory", "categories"));
        }
        return back()->withErrors("Subcategory not exists!");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id) {
        $subcategory = VendorSubCategory::find($id);
        if ($subcategory) {
            $validated = $request->validate([
                "title" => ["required", "string", "max:150"],
                "slug" => ["required", "max:150", Rule::unique("vendor_subcategories", "slug")->ignore($id)],
                "vendor_category_id" => ["required", "exists:vendor_categories,id"],
                "description" => ["nullable", "string"],
                // "image" => ["nullable", "image"],
                "status" => ["required", Rule::in(["0", "1"])],
            ]);

            $subcategory->fill($validated);

            // if (Arr::has($validated, "image")) {
            //     $image = $request->file("image");
            //     $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
            //     $image->move(public_path("uploads/vendorsubcategory"), $imageName);
            //     if ($subcategory->image) {
            //         File::delete(public_path("uploads/vendorsubcategory/".$subcategory->image));
            //     }
            //     $subcategory->image = $imageName;
            // }

            $subcategory->save();
            return redirect()->route("dashboard.vendorsubcategories.index")->with("success", "Subcategory updated!");
        }
        return back()->withErrors("Subcategory not exists!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id) {
        $subcategory = VendorSubCategory::find($id);
        if ($subcategory) {
            $subcategory->delete();
            return back()->with("success", "Subcategory deleted!");
        }
        return back()->withErrors("Subcategory not exists!");
    }

    public function status($id) {
        $category = VendorSubCategory::find($id);
        if ($category) {
            $category->status = $category->status ? "0" : "1";
            $category->save();
            $alert = $category->status ? "Subcategory Activated!" : "Subcategory Inactivated!";
            return back()->with("success", $alert);
        }
        return back()->withErrors("Subcategory not exists!");
    }

    public function trashed() {
        $subcategories = VendorSubCategory::onlyTrashed()->orderBy("title", "ASC")->paginate(20);
        return view("dashboard.vendorsubcategories.trashed", compact("subcategories"));
    }

    public function restore($id) {
        $subcategory = VendorSubCategory::onlyTrashed()->find($id);
        if ($subcategory) {
            $subcategory->restore();
            return back()->with("success", "Subcategory restored!");
        }
        return back()->withErrors("Subcategory not exists!");
    }

    public function delete($id) {
        $subcategory = VendorSubCategory::onlyTrashed()->find($id);
        if ($subcategory) {
            // if (File::exists(public_path("uploads/vendorsubcategory/".$subcategory->image))) {
            //     File::delete(public_path("uploads/vendorsubcategory/".$subcategory->image));
            // }
            $subcategory->forceDelete();
            return back()->with("success", "Subcategory deleted permanently!");
        }
        return back()->withErrors("Subcategory not exists!");
    }
}
