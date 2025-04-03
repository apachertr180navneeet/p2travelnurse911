<?php

namespace App\Http\Controllers;

use App\Mail\VendorDirectoryContactMail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\VendorCategory;
use App\Models\VendorAgency;
use App\Models\VendorAgencyCategory;
use App\Models\VendorProduct;
use App\Models\VendorBlog;
use App\Models\VendorRelease;
use App\Models\VendorReview;
use App\Models\VendorSubCategory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    public function vendorcategory(Request $request)
    {        
        // Fetch all vendor categories
        $vendor_categories = VendorCategory::all();
        $vendor_sub_categories = VendorSubCategory::all();
        $search = $request->input('search');
        $filter = $request->input('filter');

        $categories = VendorCategory::leftJoin('vendor_agency_category', 'vendor_agency_category.vendor_categories_id', '=', 'vendor_categories.id')
            ->leftJoin('vendor_subcategories', 'vendor_subcategories.vendor_category_id', '=', 'vendor_categories.id')
            ->leftJoin('vendor_agencies', 'vendor_agencies.id', '=', 'vendor_agency_category.vendor_agencies_id')
            ->select('vendor_categories.*') // Ensures only category columns are selected
            ->distinct() // Prevents duplicate rows due to joins
            ->where('vendor_categories.status', 1)
            ->where(function ($query) use ($search, $filter) {
                if (!empty($search)) {
                    if ($filter == 'company') {
                        $query->where('vendor_agencies.company_name', 'LIKE', "%$search%");
                    } elseif ($filter == 'category') {
                        $query->where('vendor_categories.title', 'LIKE', "%$search%")
                            ->orWhere('vendor_subcategories.title', 'LIKE', "%$search%");
                    }
                }
            })
            ->get();
            
        return view('vendordir.vendorcategory', compact('categories', 'vendor_categories','vendor_sub_categories'));
    }

    public function vendorSubList(Request $request , $category_slug)
    {        
        // echo $category_slug;
        // die;
        // Fetch all vendor categories
        $vendor_categories = VendorCategory::all();
        $search = $request->input('search');
        $filter = $request->input('filter');

        $categories = VendorCategory::leftJoin('vendor_agency_category', 'vendor_agency_category.vendor_categories_id', '=', 'vendor_categories.id')
            ->leftJoin('vendor_subcategories', 'vendor_subcategories.vendor_category_id', '=', 'vendor_categories.id')
            ->leftJoin('vendor_agencies', 'vendor_agencies.id', '=', 'vendor_agency_category.vendor_agencies_id')
            ->select('vendor_categories.*') // Ensures only category columns are selected
            ->distinct() // Prevents duplicate rows due to joins
            ->where(function ($query) use ($search, $filter) {
                if (!empty($search)) {
                    if ($filter == 'company') {
                        $query->where('vendor_agencies.company_name', 'LIKE', "%$search%");
                    } elseif ($filter == 'category') {
                        $query->where('vendor_categories.title', 'LIKE', "%$search%")
                            ->orWhere('vendor_subcategories.title', 'LIKE', "%$search%");
                    }
                }
            })
            ->get();
            $sub_cat_agency_count = 0;
            foreach ($categories as $category) {
                if ($category->slug == $category_slug){
                    $search = request('search');
                    $subcategories = VendorSubCategory::select('vendor_subcategories.id', 'vendor_subcategories.title', 'vendor_subcategories.slug')
                        ->leftJoin('vendor_categories', 'vendor_subcategories.vendor_category_id', '=', 'vendor_categories.id')
                        ->leftJoin('vendor_agency_category', 'vendor_agency_category.vendor_categories_id', '=', 'vendor_categories.id')
                        ->leftJoin('vendor_agencies', 'vendor_agency_category.vendor_agencies_id', '=', 'vendor_agencies.id')
                        ->where('vendor_subcategories.vendor_category_id', $category->id)
                        ->when(request('filter') == 'company' && !empty($search), function ($query) {
                            $query->where('vendor_agencies.company_name', 'LIKE', '%' . request('search') . '%');
                        })
                        ->when(request('filter') == 'category' && !empty($search), function ($query) {
                            $query->where('vendor_subcategories.title', 'LIKE', '%' . request('search') . '%');
                        })
                        ->distinct() // Prevents duplicate subcategories due to joins
                        ->get()
                        ->map(function ($subcategory) {
                            $subcategoryId = (string) $subcategory->id;
                            // Use REGEXP to match the value inside the JSON string
                            $subcategory->vendor_agency_count = VendorAgencyCategory::whereRaw(
                                'JSON_UNQUOTE(vendor_subcategories_ids) LIKE ?',
                                ['%"' . $subcategoryId . '"%'],
                            )->count();
                            return $subcategory;
                        });
                        foreach ($subcategories as $sub){
                            //echo $sub->vendor_agency_count;
                            $sub_cat_agency_count += $sub->vendor_agency_count;
                        }
                }
                
            }
            //echo $sub_cat_agency_count; die;
        return view('vendordir.vendorsubcategory', compact('categories', 'vendor_categories','category_slug','sub_cat_agency_count'));
    }

    public function vendorList($category_slug,$sub_category_slug)
    {
        $vendor_categories = VendorCategory::all();
        $vendor_sub_categories = VendorSubCategory::all();
        // Get the subcategory ID
        $subcategory = VendorSubCategory::where('slug', $sub_category_slug)->first();
        if(!$sub_category_slug) {
            return redirect()->route('vendorcategory');
        }
        // Get all vendor IDs that belong to this category
        $vendorIds = VendorAgencyCategory::whereRaw(
            'JSON_UNQUOTE(vendor_subcategories_ids) LIKE ?',
            ['%"' . $subcategory->id . '"%'],
        )->pluck('vendor_agencies_id')->toArray();    

        // Fetch vendors that match the IDs
        // $vendors = VendorAgency::whereIn('id', $vendorIds)
        //     ->withCount([
        //         'products',
        //         'blogs',
        //         'releases',
        //         'reviews' => function ($query) {
        //             $query->where('is_approved', 1); // Count only approved reviews
        //         }
        //     ])
        //     ->withAvg(['reviews' => function ($query) {
        //         $query->where('is_approved', 1); // Average only approved reviews
        //     }], 'rating')
        //     ->get();

        $vendors = VendorAgency::whereIn('id', $vendorIds)
                ->withCount([
                    'products',
                    'blogs',
                    'releases',
                    'reviews' => function ($query) {
                        $query->where('is_approved', 1);
                    }
                ])
                ->withAvg(['reviews' => function ($query) {
                    $query->where('is_approved', 1);
                }], 'rating')
                ->get();




        return view('vendordir.vendorlist', compact('vendors', 'subcategory','vendor_categories','vendor_sub_categories'));
    }

    public function vendorDetails($vendor_id)
    {
        // Fetch vendor details
        $decryptId = Crypt::decrypt($vendor_id);
        $vendor = VendorAgency::findOrFail($decryptId);

        // Fetch related data
        $products = VendorProduct::where('vendor_agencies_id', $decryptId)->get();
        $blogs = VendorBlog::where('vendor_agencies_id', $decryptId)->get();
        $pressReleases = VendorRelease::where('vendor_agencies_id', $decryptId)->get();

        return view('vendordir.vendordetails', compact('vendor', 'products', 'blogs', 'pressReleases'));
    }

    public function vendorProducts($vendor_id)
    {
        // Get Vendor Agency Details
        $vendor = VendorAgency::findOrFail($vendor_id);

        // Fetch All Products Related to the Vendor Agency
        $products = VendorProduct::where('vendor_agencies_id', $vendor_id)->get();
        return view('vendordir.vendorproducts', compact('vendor', 'products'));
    }

    public function vendorBlogs($vendor_id)
    {
        $vendor = VendorAgency::findOrFail($vendor_id);
        $blogs = VendorBlog::where('vendor_agencies_id', $vendor_id)->get();
        return view('vendordir.vendorblogs', compact('vendor', 'blogs'));
    }
    
    public function vendorPressReleases($vendor_id)
    {
        $vendor = VendorAgency::findOrFail($vendor_id);
        $pressReleases = VendorRelease::where('vendor_agencies_id', $vendor_id)->get();
        return view('vendordir.vendorpressreleases', compact('vendor', 'pressReleases'));
    }


    /**
     * Mail send functionality
     */
    public function contactMailSend(Request $request) 
    {
        $request->validate([
            'vendor_agencies_id' => 'required',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $param = array(
                'vendor_agencies_id' => $request->vendor_agencies_id,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'created_at' => $this->entryDate,
                'updated_at' => $this->entryDate,
            );
            $last_id = DB::table('vendor_directory_contact_emails')->insertGetId($param);
            if ($last_id) {
                //Send Notification Email
                $param = array(
                    'description' => "",
                    'email' => $request->email,
                    'subject' => $request->subject,
                    'usermessage' => $request->message
                );
                // Send email
                Mail::send('emails.contactSubmission', $param, function ($message) use ($param) {
                    $message->subject($param['subject']);
                    $message->to(config('custom.email_delivery_to'));
                });
                return response()->json(['success' => 'Message sent successfully!']);
            } else {
                return response()->json(['error' => 'Something went wrong!']);
            }
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!']);
        }
    }

    
    /**
     * Vendor Product Details
     */
    public function vendorProductDetails(Request $request) {
        try {
            $product_id = Crypt::decrypt($request->id);
            $product = VendorProduct::findOrFail($product_id);
            $vendor = VendorAgency::findOrFail($product->vendor_agencies_id);
            return view('vendordir.vendorproductdetails', compact('product', 'vendor'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    /**
     * Vendor Blog Details
     */
    public function vendorBlogDetails(Request $request)
    {
        try {
            $blog_id = Crypt::decrypt($request->id);
            $blog = VendorBlog::findOrFail($blog_id);
            $vendor = VendorAgency::findOrFail($blog->vendor_agencies_id);
            return view('vendordir.vendorblogdetails', compact('blog', 'vendor'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }
    

    /**
     * Vendor Press Release Details
     */
    public function vendorPressReleaseDetails(Request $request) 
    {
        try {
            $release_id = Crypt::decrypt($request->id);
            $release = VendorRelease::findOrFail($release_id);
            $vendor = VendorAgency::findOrFail($release->vendor_agencies_id);
            return view('vendordir.vendornewsdetails', compact('release', 'vendor'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    /**
     * Vendor agency review store
     */
    public function vendorAgencyReviewStore(Request $request) 
    {
        $request->validate([
            'vendor_agencies_id' => 'required',
            'rating' => 'required',
            'email' => 'required|email',
        ]);

        // Check user exist or not
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json(['error' => 'This email is not registered with us, Please register!']);
        }
        if($user->role_id != 0) {
            return response()->json(['error' => 'You are not authorized to write review!']);
        }
        try {
            $param = array(
                'vendor_agencies_id' => $request->vendor_agencies_id,
                'user_id' => $user->id,
                'rating' => $request->rating,
                'review_text' => $request->review ?? '',
                'created_at' => $this->entryDate,
                'updated_at' => $this->entryDate,
            );
            $last_id = DB::table('vendor_reviews')->insertGetId($param);
            if ($last_id) {
                return response()->json(['success' => 'Review submitted successfully!']);
            } else {
                return response()->json(['error' => 'Something went wrong!']);
            }
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!']);
        }

    }

    public function vendorAgencyRegister(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|min:3|max:50',
            'useremail' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
        try {
            //code...
            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->useremail,
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['status' => 'success', 'message' => 'User registered successfully! Now you can Write review ']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!']);
        }
    }

    /**
     * Vendor agency review list    
     */
    public function vendorAgencyReviewList(Request $request) 
    {
        $vendor_agencies_id = $request->vendor_agencies_id;
        $vendor = VendorAgency::findOrFail($vendor_agencies_id);
        $reviews = $vendor->reviews()->with('user')->where('vendor_reviews.is_approved',1)->get();
        $html = view('vendordir.vendorreviews', compact('vendor', 'reviews'))->render();
        return response()->json(['success' => 1, 'data' => $html]);
    }

    /**
     * Mail send functionality
     */
    public function storeContactDetails(Request $request)
    {
        $request->validate([
            'vendor_agencies_id' => 'required',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $param = array(
                'vendor_agencies_id' => $request->vendor_agencies_id,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'created_at' => $this->entryDate,
                'updated_at' => $this->entryDate,
            );
            $last_id = DB::table('vendor_directory_contact_emails')->insertGetId($param);
            if ($last_id) {
                //Send Notification Email
                $param = array(
                    'description' => "",
                    'email' => $request->email,
                    'subject' => $request->subject,
                    'usermessage' => $request->message
                );
                // Send email
                Mail::send('emails.contactSubmission', $param, function ($message) use ($param) {
                    $message->subject($param['subject']);
                    $message->to(config('custom.email_delivery_to'));
                });
                return response()->json(['success' => 'Message sent successfully!']);
            } else {
                return response()->json(['error' => 'Something went wrong!']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!']);
        }
    }

    /**
     * Store Company Details 
     */
    // public function storeCompanyDetails(Request $request)
    // {
    //     dd($request->all());
    //     $request->validate([
    //         'vendor_categories_id' => 'required',
    //         'email' => 'required|email',
    //         'phone' => 'required',
    //         'company_name' => 'required',
    //         'address' => 'required',
    //         'about' => 'required',
    //     ]);
    //     try {
    //         $param = array(
    //             'vendor_categories_id' => $request->vendor_categories_id,
    //             'email' => $request->email,
    //             'phone' => $request->phone,
    //             'company_name' => $request->company_name,
    //             'address' => $request->address,
    //             'about' => $request->about,
    //             'website' => $request->website,
    //             'press_releases' => $request->press_releases,
    //             'created_at' => $this->entryDate,
    //             'updated_at' => $this->entryDate
    //         );
    //         DB::table('vendor_directory_company_details')->insertGetId($param);
    //         return response()->json(['success' => 'Company details store successfully!']);
    //     } catch (\Exception $e) {
    //         dd($e);
    //         return response()->json(['error' => 'Something went wrong!']);
    //     }
    // }


    public function storeCompanyDetails(Request $request)
    {

        $request->validate([
            'vendor_categories_id' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'company_name' => 'required',
            'address' => 'required',
            'about' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        try {
            // Handle file upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/company_logo'), $filename);
                $logoPath = 'uploads/company_logo/' . $filename; // Save relative path
            }

            // Prepare data for insertion
            $param = [
                'vendor_categories_id' => $request->vendor_categories_id,
                'vendor_sub_categories_id' => $request->sub_category,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'about' => $request->about,
                'website' => $request->website,
                'press_releases' => $request->press_releases,
                'logo' => $filename, // Save path in DB
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::table('vendor_directory_company_details')->insert($param);

            return response()->json(['success' => 'Company details stored successfully!']);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Something went wrong!']);
        }
    }

    
}
