<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Marketplace;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ServiceController extends Controller
{
    public function index()
    {
        $marketplaces = Marketplace::where('status','1')->orderBy("title", "ASC")->get();
        $services = Service::with(['marketplacedata'])->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.service.index', compact('services','marketplaces'));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'marketplace' => 'required|exists:marketplaces,id',
                'name' => 'required|string|min:3|max:255|unique:services,name',
            ]);

            $service = Service::create([
                'marketplace' => $request->marketplace,
                'name' => $request->name,
            ]);

            return response()->json(['success' => true, 'message' => 'Service added successfully!', 'data' => $service]);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->serviceId;
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['status' => 'error', 'message' => 'Service not found.'], 404);
        }

        $service->delete(); // Soft delete

        return response()->json(['status' => 'success', 'message' => 'Service deleted successfully.']);
    }

    public function status(Request $request)
    {
        $service = Service::find($request->id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found']);
        }

        // Toggle status (active/inactive)
        $service->status = $request->status;
        $service->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    
}
