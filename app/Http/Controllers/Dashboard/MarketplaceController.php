<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class MarketplaceController extends Controller
{
    public function index() {
        
        // $marketplaces = Marketplace::withCount(["posts" => function($q) {
        //     $q->withTrashed();
        // }])->orderBy("title", "ASC")->paginate(20);

        $marketplaces = Marketplace::orderBy("title", "ASC")->paginate(20);

        

        return view("dashboard.marketplace.index", compact("marketplaces"));
    }

    public function create() {
        return view("dashboard.marketplace.add");
    }

    public function store(Request $request) {
        $validated = $request->validate([
            "title" => ["required", "string", "max:150"],
            "slug" => ["required", "max:150", "unique:news_categories,slug"],
            "description" => ["nullable", "string"],
            "image" => ["nullable", "image"],
            "status" => ["required", Rule::in(["0", "1"])],
        ]);
        if (Arr::has($validated, "image")) {
            $image = $request->file("image");
            $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
            $image->move(public_path("uploads/marketplace"), $imageName);
        }
        Marketplace::create([
            "title" => $validated["title"],
            "slug" => $validated["slug"],
            "description" => Arr::has($validated, "description") ? $validated["description"] : null,
            "image" => Arr::has($validated, "image") ? $imageName : null,
            "status" => $validated["status"],
        ]);
        return redirect()->route("dashboard.marketplaces.index")->with("success", "Marketplace created!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {

    }

    public function edit(string $id) {
        $marketplace = Marketplace::find($id);
        if ($marketplace) {
            return view("dashboard.marketplace.edit", compact("marketplace"));
        }
        return back()->withErrors("Marketplace not exists!");
    }

    public function update(Request $request, string $id) {
        $marketplace = Marketplace::find($id);
        if ($marketplace) {
            $validated = $request->validate([
                "title" => ["required", "string", "max:150"],
                "slug" => ["required", "max:150", Rule::unique("marketplaces", "slug")->ignore($id)],
                "description" => ["nullable", "string"],
                "image" => ["nullable", "image"],
                "status" => ["required", Rule::in(["0", "1"])],
            ]);
            $marketplace->title = $validated["title"];
            $marketplace->slug = $validated["slug"];
            $marketplace->description = Arr::has($validated, "description") ? $validated["description"] : null;
            $marketplace->status = $validated["status"];
            if (Arr::has($validated, "image")) {
                $image = $request->file("image");
                $imageName = md5(time().rand(11111, 99999)).".".$image->extension();
                $image->move(public_path("uploads/marketplace"), $imageName);
                if ($marketplace->image) {
                    if (File::exists(public_path("uploads/marketplace/".$marketplace->image))) {
                        File::delete(public_path("uploads/marketplace/".$marketplace->image));
                    }
                }
                $marketplace->image = $imageName;
            }
            $marketplace->save();
            return redirect()->route("dashboard.marketplaces.index")->with("success", "Category updated!");
        }
        return back()->withErrors("Marketplace not exists!");
    }

    public function destroy(string $id) {
        $marketplace = Marketplace::find($id);
        if ($marketplace) {
            $marketplace->delete();
            return back()->with("success", "Marketplace deleted!");
        }
        return back()->withErrors("Marketplace not exists!");
    }

    public function restore($id) {
        $marketplace = Marketplace::onlyTrashed()->find($id);
        if ($marketplace) {
            $marketplace->restore();
            return back()->with("success", "Marketplace restored!");
        }
        return back()->withErrors("Marketplace not exists!");
    }

    public function status($id) {
        $marketplace = Marketplace::find($id);
        if ($marketplace) {
            $marketplace->status = $marketplace->status ? "0" : "1";
            $marketplace->save();
            $alert = $marketplace->status ? "Marketplace Activated!" : "Category Inactivated!";
            return back()->with("success", $alert);
        }
        return back()->withErrors("Marketplace not exists!");
    }

    public function trashed() {
        $marketplaces = Marketplace::onlyTrashed()->orderBy("title", "ASC")->paginate(20);
        return view("dashboard.marketplace.trashed", compact("marketplaces"));
    }

    public function delete($id) {
       
        $marketplace = Marketplace::onlyTrashed()->find($id);
        if ($marketplace) {
            if (File::exists(public_path("uploads/marketplace/".$marketplace->image))) {
                File::delete(public_path("uploads/marketplace/".$marketplace->image));
            }
            // $marketplace->posts()->forceDelete();
            $marketplace->forceDelete();
            return back()->with("success", "Marketplace deleted!");
        }
        return back()->withErrors("Marketplace not exists!");
    }
}
