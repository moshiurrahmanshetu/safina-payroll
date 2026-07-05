<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackageCounter;
use App\Models\User;
use App\Models\Package;
use Validator;

class PackageCounterController extends Controller
{
    public function index()
    {
        $counters = PackageCounter::with('users', 'packages')->orderBy('id', 'desc')->get();
        return view('admin.package_counters.index', compact('counters'));
    }

    public function create()
    {
        $users = User::where('status', 1)->pluck('name', 'id');
        $packages = Package::where('status', 1)->pluck('name', 'id');
        return view('admin.package_counters.create', compact('users', 'packages'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'status' => 'required|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'packages' => 'nullable|array',
            'packages.*' => 'exists:packages,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $counter = PackageCounter::create([
            'name' => $request->name,
            'status' => $request->status
        ]);

        // Attach users to counter
        if ($request->has('users')) {
            $counter->users()->attach($request->users);
        }

        // Attach packages to counter
        if ($request->has('packages')) {
            $counter->packages()->attach($request->packages);
        }

        return redirect()->route('package_counters.index')->with('flash_success', 'Package Counter created successfully');
    }

    public function edit($id)
    {
        $counter = PackageCounter::with(['users', 'packages'])->findOrFail($id);
        $users = User::where('status', 1)->pluck('name', 'id');
        $packages = Package::where('status', 1)->pluck('name', 'id');
        $selectedUsers = $counter->users->pluck('id')->toArray();
        $selectedPackages = $counter->packages->pluck('id')->toArray();
        return view('admin.package_counters.edit', compact('counter', 'users', 'packages', 'selectedUsers', 'selectedPackages'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'status' => 'required|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'packages' => 'nullable|array',
            'packages.*' => 'exists:packages,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $counter = PackageCounter::findOrFail($id);
        $counter->update([
            'name' => $request->name,
            'status' => $request->status
        ]);

        // Sync users to counter
        if ($request->has('users')) {
            $counter->users()->sync($request->users);
        } else {
            $counter->users()->detach();
        }

        // Sync packages to counter
        if ($request->has('packages')) {
            $counter->packages()->sync($request->packages);
        } else {
            $counter->packages()->detach();
        }

        return redirect()->route('package_counters.index')->with('flash_success', 'Package Counter updated successfully');
    }

    public function destroy($id)
    {
        $counter = PackageCounter::findOrFail($id);
        $counter->delete();
        return redirect()->route('package_counters.index')->with('flash_success', 'Package Counter deleted successfully');
    }
}
