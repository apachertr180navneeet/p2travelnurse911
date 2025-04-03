<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Marketplace;
use App\Models\State;
use App\Models\City;
use App\Models\Classified;
use App\Models\ContactSeller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ClassifiedController extends Controller
{
    public function index()
    {

        $classifieds = Classified::with(['marketplace', 'state', 'city'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.classified.index', compact('classifieds'));
    }

    public function approve($id)
    {
        // Retrieve the classified ad by ID
        $classified = Classified::findOrFail($id);

        // Update the status to approved (1 for active)
        $classified->status = '1'; // Assuming '1' means approved
        $classified->save();

        // Redirect back with success message
        return redirect()->route('dashboard.classifieds.index')->with('success', 'Classified ad approved successfully!');
    }

    public function contactseller()
    {
        // Fetch all contact messages with pagination
        $contacts = ContactSeller::with('classified') // Load related classified ads
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Return the view with the contacts
        return view('dashboard.classified.contactseller', compact('contacts'));
    }

    public function delete($id)
    {
        $classified = Classified::findOrFail($id);

        // Delete the classified ad
        if ($classified->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function contactsellerDelete($id){

        $contactSeller = ContactSeller::findOrFail($id);

        // Delete the classified ad
        if ($contactSeller->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);

    }

    public function show($id) {
       
        $classified = Classified::with(['marketplace', 'state', 'city'])->findOrFail($id);
        return response()->json($classified);
    }
    
    public function updateStatus(Request $request, $id) {
               
        $classified = Classified::findOrFail($id);
        $classified->status = $request->input('status');
        $classified->save();
        return response()->json(['success' => true]);
    }





}
