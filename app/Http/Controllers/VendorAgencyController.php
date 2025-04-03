<?php

namespace App\Http\Controllers;

use App\Models\VendorAgency;
use App\Models\VendorAgencyCategory;
use App\Models\VendorBlog;
use App\Models\VendorCategory;
use App\Models\VendorDirectoryCompanyDetail;
use App\Models\VendorDirectoryContactEmail;
use App\Models\VendorProduct;
use App\Models\VendorRelease;
use App\Models\VendorReview;
use App\Models\VendorSubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VendorAgencyController extends Controller
{
    public function index()
    {
        $agencies = VendorAgency::latest()->paginate(10);
        return view('dashboard.vendor_agencies.index', compact('agencies'));
    }

    public function create()
    {
        $vendorCategories = VendorCategory::all();

        return view("dashboard.vendor_agencies.add", compact("vendorCategories"));
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = VendorSubCategory::where("vendor_category_id", $request->category_id)->get();
        return response()->json($subcategories);
    }

    public function store(Request $request)
    {
        //dd($request->releases['0']['title']);
        // $request->validate([
        //     'company_name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:vendor_agencies,email',
        //     'phone_number' => 'required',
        //     'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        //     'vendor_categories.*.category_id' => 'required|exists:vendor_categories,id',
        //     'vendor_categories.*.subcategories' => 'required|array',

        //     'products.*.title' => 'nullable|string|max:255',
        //     'products.*.desc' => 'nullable|string',
        //     'products.*.logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        //     'products.*.content' => 'nullable|string',

        //     'blogs.*.title' => 'nullable|string|max:255',
        //     'blogs.*.desc' => 'nullable|string',
        //     'blogs.*.logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        //     'blogs.*.content' => 'nullable|string',

        //     'releases.*.title' => 'nullable|string|max:255',
        //     'releases.*.desc' => 'nullable|string',
        //     'releases.*.logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        //     'releases.*.content' => 'nullable|string',
        // ]);

        DB::beginTransaction(); // Start Transaction

        try {
            // Store the agency logo if uploaded
            $va_image = null;
            if ($request->hasFile('logo')) {
                $image = $request->file("logo");
                $va_image = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                $image->move(public_path("uploads/vendoragency"), $va_image);
            }
            
            // Store Vendor Agency
            $vendorAgency = VendorAgency::create([
                'company_name' => $request->company_name,
                'tagline' => $request->tagline,
                'website' => $request->website,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'logo' => $va_image,
                'desc' => $request->desc,
                'youtube' => $request->youtube ?? '',
                'linkedin' => $request->linkedin ?? '',
                'instagram' => $request->instagram ?? '',
                'twitter' => $request->twitter ?? '',
                'facebook' => $request->facebook ?? '',
            ]);

            \Log::info("VendorAgency Created Successfully", ['vendor_agencies_id' => $vendorAgency->id]);

            // Store Vendor Categories & Subcategories
            if ($request->has('vendor_categories')) {
                foreach ($request->vendor_categories as $categoryData) {
                    VendorAgencyCategory::create([
                        'vendor_agencies_id' => $vendorAgency->id,
                        'vendor_categories_id' => $categoryData['category_id'],
                        'vendor_subcategories_ids' => json_encode($categoryData['subcategories'] ?? []), // Ensure valid JSON
                    ]);
                }
                \Log::info("Vendor Categories Stored", ['vendor_agencies_id' => $vendorAgency->id]);
            }
            
            // Store Vendor Products
            if ($request->has('products')) {
                foreach ($request->products as $index => $product) {
                    $productImage = null;
                    
                    // Manually check if file exists in $_FILES
                    // Check if the file exists and is valid before accessing it
                    if ($request->hasFile("products.$index.logo")) {
                        if (array_key_exists("products", $_FILES) && 
                            array_key_exists($index, $_FILES["products"]["name"]) &&
                            array_key_exists("logo", $_FILES["products"]["name"][$index])) {
                            
                            $image = $request->file("products")[$index]["logo"];

                            if ($image->isValid()) {
                                $productImage = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                                $image->move(public_path("uploads/vendoragency_product"), $productImage);
                            }
                        }
                    }

                    VendorProduct::create([
                        'vendor_agencies_id' => $vendorAgency->id,
                        'product_title' => $product['title'],
                        'desc' => $product['desc'],
                        'logo' => $productImage,
                        // 'content' => $product['content'],
                    ]);
                }
            }
            \Log::info("products created" );

           // Store Vendor Blogs
            if (!empty($request->blog)) {
                foreach ($request->blogs as $index => $blog) {
                    $blogImage = null;

                    // Manually check if file exists in $_FILES
                    if ($request->hasFile("blogs.$index.logo")) {
                        if (array_key_exists("blogs", $_FILES) && 
                            array_key_exists($index, $_FILES["blogs"]["name"]) &&
                            array_key_exists("logo", $_FILES["blogs"]["name"][$index])) {
                            
                            $image = $request->file("blogs")[$index]["logo"];

                            if ($image->isValid()) {
                                $blogImage = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                                $image->move(public_path("uploads/vendoragency_blog"), $blogImage);
                            }
                        }
                    }

                    VendorBlog::create([
                        'vendor_agencies_id' => $vendorAgency->id,
                        'title' => $blog['title'],
                        'desc' => $blog['desc'],
                        'logo' => $blogImage,
                        // 'content' => $blog['content'],
                    ]);
                }
            }
            \Log::info("blog created" );

            // Store Vendor Releases
            if (!empty($request->releases['0']['title'])) {
                foreach ($request->releases as $index => $release) {
                    $releaseImage = null;

                    VendorRelease::create([
                        'vendor_agencies_id' => $vendorAgency->id,
                        'title' => $release['title'],
                        'desc' => $release['desc'],
                    ]);
                }
            }
            \Log::info("releases created" );

            DB::commit(); // Commit Transaction

            return redirect()->route('dashboard.vendor_agencies.index')->with('success', 'Vendor Agency created successfully!');

        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction in case of error
            return redirect()->back()->with('error', 'Failed to create Vendor Agency: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $vendorAgency = VendorAgency::findOrFail($id);
        $vendorCategories = VendorCategory::all();
        $selectedCategories = VendorAgencyCategory::where('vendor_agencies_id', $id)
                        ->get(['vendor_categories_id', 'vendor_subcategories_ids']) // Fetch both columns
                        ->map(function ($item) {
                            return [
                                'vendor_categories_id' => $item->vendor_categories_id,
                                'vendor_subcategories_ids' => json_decode($item->vendor_subcategories_ids, true) ?? [],
                            ];
                        })->toArray();

        $vendorProducts = VendorProduct::where('vendor_agencies_id', $id)->get();
        $vendorBlogs = VendorBlog::where('vendor_agencies_id', $id)->get();
        $vendorNews = VendorRelease::where('vendor_agencies_id', $id)->get();

        return view('dashboard.vendor_agencies.edit', compact('vendorAgency', 'vendorCategories', 'selectedCategories', 'vendorProducts', 'vendorBlogs', 'vendorNews'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:vendor_agencies,email,' . $id,
            'phone_number' => 'nullable',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'vendor_categories.*.category_id' => 'required|exists:vendor_categories,id',
            'vendor_categories.*.subcategories' => 'required|array',

            'products.*.title' => 'nullable|string|max:255',
            'products.*.desc' => 'nullable|string',
            'products.*.logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'products.*.content' => 'nullable|string',

            'blogs.*.title' => 'nullable|string|max:255',
            'blogs.*.desc' => 'nullable|string',
            'blogs.*.logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'blogs.*.content' => 'nullable|string',

            'releases.*.title' => 'nullable|string|max:255',
            'releases.*.desc' => 'nullable|string',
            'releases.*.logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'releases.*.content' => 'nullable|string',
        ]);

        DB::beginTransaction(); // Start Transaction

        try {
            // Store the agency logo if uploaded
            $va_image = null;
            if ($request->hasFile('logo')) {
                $image = $request->file("logo");
                $va_image = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                $image->move(public_path("uploads/vendoragency"), $va_image);
            }

            // Update Vendor Agency
            $vendorAgency = VendorAgency::where('id',$id)->update([
                'company_name' => $request->company_name,
                'tagline' => $request->tagline,
                'website' => $request->website,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'desc' => $request->desc,
                'youtube' => $request->youtube ?? '',
                'linkedin' => $request->linkedin ?? '',
                'instagram' => $request->instagram ?? '',
                'twitter' => $request->twitter ?? '',
                'facebook' => $request->facebook ?? '',
            ]);
            if(!empty($va_image)) {
                VendorAgency::where('id', $id)->update([
                    'logo' => $va_image,
                ]);
            }

            // Update Vendor Categories & Subcategories
            if ($request->has('vendor_categories')) {
                // Delete existing categories  
                VendorAgencyCategory::where('vendor_agencies_id', $id)->delete();               
                foreach ($request->vendor_categories as $categoryData) {
                    VendorAgencyCategory::create([
                        'vendor_agencies_id' => $id,
                        'vendor_categories_id' => $categoryData['category_id'],
                        'vendor_subcategories_ids' => json_encode($categoryData['subcategories'] ?? []), // Ensure valid JSON
                    ]);
                }
            }

            // Update Vendor Products
            if ($request->has('products')) {
                // Delete existing products
                VendorProduct::where('vendor_agencies_id', $id)
                    ->whereNotIn('id', collect($request->products)->pluck('id')->filter())
                    ->delete();
                foreach ($request->products as $index => $product) {
                    // Manually check if file exists in $_FILES
                    // Check if the file exists and is valid before accessing it
                    if ($request->hasFile("products.$index.logo")) {
                        if (
                            array_key_exists("products", $_FILES) &&
                            array_key_exists($index, $_FILES["products"]["name"]) &&
                            array_key_exists("logo", $_FILES["products"]["name"][$index])
                        ) {
                            $image = $request->file("products")[$index]["logo"];
                            if ($image->isValid()) {
                                $productImage = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                                $image->move(public_path("uploads/vendoragency_product"), $productImage);
                            }
                        }
                    }
                    if(!empty($product['id'])) {
                        VendorProduct::where('id', $product['id'])->update(
                            collect([
                                'vendor_agencies_id' => $id,
                                'product_title' => $product['title'] ?? null,
                                'desc' => $product['desc'] ?? null,
                                'logo' => $productImage ?? null,
                            ])->filter()->toArray()
                        );
                    } else {
                        if(!empty($product['title']) || !empty($product['desc'])) {
                            VendorProduct::create(
                                collect([
                                    'vendor_agencies_id' => $id,
                                    'product_title' => $product['title'] ?? null,
                                    'desc' => $product['desc'] ?? null,
                                    'logo' => $productImage ?? null,
                                ])->filter()->toArray()
                            );
                        }
                    }
                }
            }

            // Store Vendor Blogs
            if ($request->has('blogs')) {
                // Delete existing blogs
                VendorBlog::where('vendor_agencies_id', $id)
                    ->whereNotIn('id', collect($request->blogs)->pluck('id')->filter())
                    ->delete();
                foreach ($request->blogs as $index => $blog) {
                    $blogImage = null;
                    // Manually check if file exists in $_FILES
                    if ($request->hasFile("blogs.$index.logo")) {
                        if (
                            array_key_exists("blogs", $_FILES) &&
                            array_key_exists($index, $_FILES["blogs"]["name"]) &&
                            array_key_exists("logo", $_FILES["blogs"]["name"][$index])
                        ) {

                            $image = $request->file("blogs")[$index]["logo"];

                            if ($image->isValid()) {
                                $blogImage = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                                $image->move(public_path("uploads/vendoragency_blog"), $blogImage);
                            }
                        }
                    }

                    if(!empty($blog['id'])) {
                        VendorBlog::where('id', $blog['id'])->update(
                            collect([
                                'vendor_agencies_id' => $id,
                                'title' => $blog['title'] ?? null,
                                'desc' => $blog['desc'] ?? null,
                                'logo' => $blogImage ?? null,
                            ])->filter()->toArray()
                        );
                    } else {
                        if(!empty($blog['title']) || !empty($blog['desc'])) {
                            VendorBlog::create(collect([
                                'vendor_agencies_id' => $id,
                                'title' => $blog['title'] ?? null,
                                'desc' => $blog['desc'] ?? null,
                                'logo' => $blogImage ?? null,
                            ])->filter()->toArray());
                        }
                    }
                }
            }
            // Update Vendor Releases
            if ($request->has('releases')) {
                // Delete existing releases
                VendorRelease::where('vendor_agencies_id', $id)
                    ->whereNotIn('id', collect($request->releases)->pluck('id')->filter())
                    ->delete();
                foreach ($request->releases as $index => $release) {
                    if(!empty($release['id'])) {
                        VendorRelease::where('id',$release['id'])->update([
                            'vendor_agencies_id' => $id,
                            'title' => $release['title'],
                            'desc' => $release['desc'],
                        ]);
                    } else {
                        if(!empty($release['title']) || !empty($release['desc'])) {
                            VendorRelease::create([
                                'vendor_agencies_id' => $id,
                                'title' => $release['title'],
                                'desc' => $release['desc'],
                            ]);
                        }
                    }
                }
            }
            DB::commit(); // Commit Transaction
            return redirect()->route('dashboard.vendor_agencies.index')->with('success', 'Vendor Agency Updated Successfully!');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction in case of error
            return redirect()->back()->with('error', 'Failed to create Vendor Agency: ' . $e->getMessage());
        }
    }


    /**
     * Delete vendor agency
     */
    public function destroy($id)
    {
        $vendorAgency = VendorAgency::find($id);
        $vendorAgency->delete();
        return back()->with("success", "Vendor Agency deleted!");
    }

    /**
     * Get Contact Lists of agencies
     */
    public function getEmailList() 
    {
        $contactLists = VendorDirectoryContactEmail::leftjoin('vendor_agencies',
            'vendor_directory_contact_emails.vendor_agencies_id', '=', 'vendor_agencies.id')
            ->select('vendor_directory_contact_emails.*', 'vendor_agencies.company_name')
            ->latest()->paginate(10);
        return view("dashboard.vendor_agencies.contact_list", compact("contactLists"));
    }


    /**
     * Get Reviews of agencies
     */
    public function getReviewsFeedback()
    {
        $agencyReviews = VendorReview::join('vendor_agencies', 'vendor_agencies.id', '=', 'vendor_reviews.vendor_agencies_id')
                            ->join('users', 'users.id', '=', 'vendor_reviews.user_id')
                            ->select('vendor_reviews.*', 'vendor_agencies.company_name', 'users.name as user_name', 'users.email')
                            ->orderBy('id', 'desc')
                            ->latest()->paginate(10);
        return view("dashboard.vendor_agencies.agency-reviews-feedback", compact("agencyReviews"));
    }
    
    /**
     * Update agency review
     */
    public function vendorAgencyReviewsUpdate(Request $request)
    {
        $request->validate([
            'vendor_agency_id' => 'required|exists:vendor_reviews,id',
        ]);
        $updateReview = VendorReview::findOrFail($request->vendor_agency_id);
        if ($request->has('is_approved')) {
            $updateReview->update(['is_approved' => $request->query('is_approved')]);
            $msg = $request->query('is_approved') == 1 ? 'Approved' : 'Declined';
        } else {
            $updateReview->delete();
            $msg = 'Deleted';
        }
        return redirect()->back()->with('success', "Review {$msg} successfully!");
    }


    /**
     * Get Company Lists of agencies
     */
    public function getContactList()
    {
        $contactLists = VendorDirectoryCompanyDetail::leftjoin(
            'vendor_categories',
            'vendor_directory_company_details.vendor_categories_id',
            '=',
            'vendor_categories.id')
            ->leftjoin(
                'vendor_subcategories',
                'vendor_directory_company_details.vendor_sub_categories_id',
                '=',
                'vendor_subcategories.id')
            ->select('vendor_directory_company_details.*', 'vendor_categories.title', 'vendor_subcategories.title as vendor_sub_categories_title')
            ->orderBy('id','desc')
            ->paginate(10);
        return view("dashboard.vendor_agencies.company_list", compact("contactLists"));
    }

    /**
     * Delete email list
     */
    public function deleteEmailList(Request $request)
    {
        $vendorContact = VendorDirectoryContactEmail::find($request->id);
        $vendorContact->delete();
        return back()->with("success", "Email details deleted!");
    }


    /**
     * Delete company list
     */
    public function deleteCompanyList(Request $request)
    {
        $vendorCompany = VendorDirectoryCompanyDetail::find($request->id);
        $vendorCompany->delete();
        return back()->with("success", "Company List deleted!");
    }

}