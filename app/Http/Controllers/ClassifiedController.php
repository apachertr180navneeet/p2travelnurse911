<?php

namespace App\Http\Controllers;

use App\Helper\CommonFunction;
use Illuminate\Http\Request;
use DB;
use URL;
use Session;
/*use PDF;*/
use Validator;
use Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\NewsCategory;
use App\Models\Post;
use App\Models\Marketplace;
use App\Models\Service;
use App\Models\Subscribe;
use App\Models\State;
use App\Models\City;
use App\Models\Classified;
use App\Models\ContactSeller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;





class ClassifiedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $entryLimit;
    private $entryDate;
    public function __construct()
    {

        //$this->middleware('auth');
        $this->entryLimit = 10;
        $this->entryDate = date("Y-m-d H:i:s");
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        if (Auth::check()) {
            $email = Auth::user()->email;
            $classifieds = Classified::latest()->where('email',$email)->get();
        }else{
            $classifieds = Classified::latest()->where('status','1')->get();
        }
        
        $states = State::orderBy('name', 'asc')->get();
        $cities = City::orderBy('city_name', 'asc')->get();
        $marketplaces = MarketPlace::get();
        return view('classified.index', compact('classifieds', 'states', 'cities', 'marketplaces'));
    }

    public function save(Request $request)
    {
        try {

            if($request->marketplace_id != "4"){

            // Validate request data
            $validatedData = $request->validate([
                'marketplace_id' => 'required|exists:marketplaces,id',
                'state_id' => 'required',
                'city_id' => 'required',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'name' => 'nullable',
                'phone' => 'nullable',
                'email' => 'nullable|email',
            ]);

        }
            $imageName = '';
            if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                $image = $request->file("thumbnail");
                $imageName = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                $image->move(public_path("uploads/marketplace"), $imageName);
            } else {
                // Handle the case where no image is uploaded or the upload failed
                $imageName = null; // or set a default value if necessary
            }



            // Prepare data for insertion



            $classifiedData = [
                'marketplace_id' => $request->marketplace_id,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id ?? null,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'price' => $request->price ?? null,
                'price_type' => $request->price_type ?? null,
                'bedrooms' => $request->bedrooms ?? null,
                'certification_type' => $request->certification_type ?? null,
                'service_type' => $request->service_type ?? null,
                'description' => $request->description ?? null,
                'name' => $request->name ?? null,
                'thumbnail' => $imageName ?? null,
                'phone' => $request->phone ?? null,
                'email' => !empty($request->email) ? $request->email : (Auth::check() ? Auth::user()->email : null),
                'website' => $request->website ?? null,
            ];

            // Insert into database
            Classified::create($classifiedData);

            // Return success message and redirect
            return redirect()->route('classified.index')
                ->with('success', 'Thank you for your post! Our team is reviewing your submission and will post as soon as possible.!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors to Blade file
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Handle general errors
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function add(Request $request)
    {       
        $states = State::orderBy('name', 'asc')->get();
        $cities = City::orderBy('city_name', 'asc')->get();
        $marketplaces = MarketPlace::get();
        $serviceagencys = Service::where('marketplace','3')->get();
        $servicenursingcurtifactes = Service::where('marketplace','2')->get();
        return view('classified.add', compact('states', 'cities', 'marketplaces','serviceagencys','servicenursingcurtifactes'));
    }

    public function edit($id)
    {
        $classifieds = Classified::latest()->get();
        $classifiedAds = Classified::findOrFail($id);
        $states = State::orderBy('name', 'asc')->get();
        $cities = City::orderBy('city_name', 'asc')->get();
        $marketplaces = MarketPlace::get();
        return view('classified.edit', compact('classifieds', 'states', 'cities', 'marketplaces', 'classifiedAds'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Find the classified ad by ID
            $classified = Classified::findOrFail($id);

            if($request->marketplace_id != "4"){
                
                // Validate request data
                $validatedData = $request->validate([
                    'marketplace_id' => 'required|exists:marketplaces,id',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'title' => 'required',
                    'description' => 'required',
                    'name' => 'required',
                    'phone' => 'required',
                    'email' => 'required|email',
                ]);
            }



            // Handle thumbnail upload if provided
            if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                // Delete old image if exists
                if ($classified->thumbnail && file_exists(public_path("uploads/marketplace/" . $classified->thumbnail))) {
                    unlink(public_path("uploads/marketplace/" . $classified->thumbnail));
                }

                // Upload new image
                $image = $request->file("thumbnail");
                $imageName = md5(time() . rand(11111, 99999)) . "." . $image->extension();
                $image->move(public_path("uploads/marketplace"), $imageName);
            } else {
                // Keep old image if no new image is uploaded
                $imageName = $classified->thumbnail;
            }

            // Prepare data for update
            $classifiedData = [
                'marketplace_id' => $request->marketplace_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'title' => $request->title,
                'slug' => Str::slug($request->title),                
                'price' => $request->price ?? null,
                'price_type' => $request->price_type ?? null,
                'bedrooms' => $request->bedrooms ?? null,
                'certification_type' => $request->certification_type ?? null,
                'service_type' => $request->service_type ?? null,
                'description' => $request->description,
                'name' => $request->name ?? null,
                'thumbnail' => $imageName,
                'phone' => $request->phone ?? null,
                'email' => $request->email ?? null,
                'website' => $request->website ?? null,
            ];

            // Update classified ad in database
            $classified->update($classifiedData);

            // Return success message and redirect
            return redirect()->route('classified.index')
                ->with('success', 'Classified Ads updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors to Blade file
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Handle general errors
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Find the classified ad by ID
            $classified = Classified::findOrFail($id);

            // Delete the classified ad
            $classified->delete();

            // Redirect with success message
            return redirect()->route('classified.index')
                ->with('success', 'Classified ad deleted successfully!');
        } catch (\Exception $e) {
            // Handle errors if the delete operation fails
            return redirect()->route('classified.index')
                ->with('error', 'Error deleting classified ad: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $stateId = $request->state_id ?? '';
        $cityId = $request->city_id ?? '';
        $marketplaceId = $request->marketplace_id ?? '';

        if (Auth::check()) {
            $email = Auth::user()->email;
            $classifieds = Classified::query()->latest()->where('email',$email);
        }else{
            $classifieds = Classified::query()->latest()->where('status','1');
        }       

        if ($stateId) {
            $classifieds->where('state_id', $stateId)->orderBy('name', 'asc');
        }

        if ($cityId) {
            $classifieds->where('city_id', $cityId);
        }

        if ($marketplaceId) {
            $classifieds->where('marketplace_id', $marketplaceId);
        }
        // Fetch the filtered classifieds
        $classifieds = $classifieds->get();       
        $states = State::orderBy('name', 'asc')->get();
        $cities = City::orderBy('city_name', 'asc')->get();
        $marketplaces    = MarketPlace::get();
        return view('classified.search', compact('classifieds', 'states', 'cities', 'marketplaces'));       
    }




    public function singledetail($slug)
    {
        $classifiedAds = Classified::with(['state', 'city'])->where('slug', $slug)->first();
        return view('classified.singledetail', compact('classifiedAds'));
    }

    public function contact_seller(Request $request)
    {
        $request->validate([
            'classified_id' => 'required|exists:classifieds,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        ContactSeller::create($request->all());
        return back()->with('success', 'Your message has been sent successfully.');
    }

    // Method to get cities by state
    public function getCitiesByState(Request $request)
    {
        if ($request->ajax()) {
            // Fetch cities where the state_id matches the requested state_id
            $state = State::where('id', $request->state_id)->first();
            $cities = City::where('state_code', $state->code)->get();

            // Return a JSON response with the cities
            return response()->json(['cities' => $cities]);
        }
    }  


}
