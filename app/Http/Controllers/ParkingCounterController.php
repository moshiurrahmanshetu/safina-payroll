<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ParkingCounter;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Validator;

class ParkingCounterController extends Controller
{
    /**
     * Constructor - call parent for menu_list
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        // Check permission
        if (!$this->hasPermission($user->role_id, 'view_parking_counters')) {
            abort(403, 'Unauthorized access');
        }

        $counters = ParkingCounter::with('users')->orderBy('id', 'desc')->get();

        return view('admin.parking_counters.index', compact('counters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        // Check permission
        if (!$this->hasPermission($user->role_id, 'create_parking_counter')) {
            abort(403, 'Unauthorized access');
        }

        // Get all users for assignment
        $users = User::where('status', 1)->pluck('name', 'id')->toArray();

        return view('admin.parking_counters.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        // Check permission
        if (!$this->hasPermission($user->role_id, 'create_parking_counter')) {
            abort(403, 'Unauthorized access');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'required|integer|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $counter = ParkingCounter::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
        ]);

        // Assign users if provided
        if ($counter && isset($data['users']) && !empty($data['users'])) {
            $counter->users()->sync($data['users']);
        }

        return redirect()->route('parking_counters.index')
            ->with('flash_success', 'Parking Counter created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        // Check permission
        if (!$this->hasPermission($user->role_id, 'view_parking_counters')) {
            abort(403, 'Unauthorized access');
        }

        $counter = ParkingCounter::with('users')->findOrFail($id);

        return view('admin.parking_counters.show', compact('counter'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        // Check permission
        if (!$this->hasPermission($user->role_id, 'edit_parking_counter')) {
            abort(403, 'Unauthorized access');
        }

        $counter = ParkingCounter::with('users')->findOrFail($id);

        // Get all users for assignment
        $users = User::where('status', 1)->pluck('name', 'id')->toArray();

        // Get currently assigned user IDs
        $assignedUserIds = $counter->users->pluck('id')->toArray();

        return view('admin.parking_counters.edit', compact('counter', 'users', 'assignedUserIds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        // Check permission
        if (!$this->hasPermission($user->role_id, 'edit_parking_counter')) {
            abort(403, 'Unauthorized access');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'required|integer|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $counter = ParkingCounter::findOrFail($id);

        $counter->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
        ]);

        // Update user assignments
        if (isset($data['users'])) {
            $counter->users()->sync($data['users']);
        } else {
            $counter->users()->sync([]);
        }

        return redirect()->route('parking_counters.index')
            ->with('flash_success', 'Parking Counter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        // Check permission
        if (!$this->hasPermission($user->role_id, 'delete_parking_counter')) {
            abort(403, 'Unauthorized access');
        }

        $counter = ParkingCounter::findOrFail($id);

        // Check if counter has parking tickets
        if ($counter->parkingTickets()->count() > 0) {
            return redirect()->back()->with('flash_error', 'Cannot delete counter with parking tickets.');
        }


        // Detach all users
        $counter->users()->detach();

        // Delete counter
        $counter->delete();

        return redirect()->route('parking_counters.index')
            ->with('flash_success', 'Parking Counter deleted successfully.');
    }

    /**
     * Check if role has specific permission
     *
     * @param int $role_id
     * @param string $permissionName
     * @return bool
     */
    private function hasPermission($role_id, $permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission) {
            return false;
        }

        return RolePermission::where('role_id', $role_id)
            ->where('permission_id', $permission->id)
            ->exists();
    }
}
