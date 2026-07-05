<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Counter;
use App\Models\User;
use App\Models\Service;
use Validator;

class CounterController extends Controller
{
    public function index()
    {
        $counters = Counter::with('users', 'services')->orderBy('id', 'desc')->get();
        return view('admin.counters.index', compact('counters'));
    }

    public function create()
    {
        $users = User::where('status', 1)->pluck('name', 'id');
        $services = Service::where('status', 1)->with('service_category')->get();
        return view('admin.counters.create', compact('users', 'services'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'status' => 'required|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $counter = Counter::create([
            'name' => $request->name,
            'status' => $request->status
        ]);

        // Attach users to counter
        if ($request->has('users')) {
            $counter->users()->attach($request->users);
        }

        // Attach services to counter
        if ($request->has('services')) {
            $counter->services()->attach($request->services);
        }

        return redirect()->route('counters.index')->with('flash_success', 'Counter created successfully');
    }

    public function edit($id)
    {
        $counter = Counter::with(['users', 'services'])->findOrFail($id);
        $users = User::where('status', 1)->pluck('name', 'id');
        $services = Service::where('status', 1)->with('service_category')->get();
        $selectedUsers = $counter->users->pluck('id')->toArray();
        $selectedServices = $counter->services->pluck('id')->toArray();
        return view('admin.counters.edit', compact('counter', 'users', 'services', 'selectedUsers', 'selectedServices'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'status' => 'required|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $counter = Counter::findOrFail($id);
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

        // Sync services to counter
        if ($request->has('services')) {
            $counter->services()->sync($request->services);
        } else {
            $counter->services()->detach();
        }

        return redirect()->route('counters.index')->with('flash_success', 'Counter updated successfully');
    }

    public function destroy($id)
    {
        $counter = Counter::findOrFail($id);
        $counter->delete();
        return redirect()->route('counters.index')->with('flash_success', 'Counter deleted successfully');
    }
}
