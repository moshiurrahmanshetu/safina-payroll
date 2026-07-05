<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Ticket;
use Validator;

class PackageController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = Auth::user();

    $query = Package::with('items.ticket')->orderBy('id', 'desc');

    // Apply access restrictions - ALL users must have package counter assignment
    $userPackageCounterIds = $user->packageCounters()->where('status', 1)->pluck('package_counter_id')->toArray();

    if (!empty($userPackageCounterIds)) {
      // Get all package IDs from user's package counters
      $packageIds = \App\Models\PackageCounter::whereIn('id', $userPackageCounterIds)
        ->whereHas('packages')
        ->with('packages')
        ->get()
        ->pluck('packages')
        ->flatten()
        ->pluck('id')
        ->unique()
        ->toArray();

      if (!empty($packageIds)) {
        $query->whereIn('id', $packageIds);
      } else {
        // User's package counters have no packages assigned
        $query->where('id', 0);
      }
    } /* else {
      // User has no package counters, show no packages
      $query->where('id', 0);
    } */

    $packages = $query->get();
    return view('admin.packages.index', compact('packages'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    // Get active tickets for package composition
    $tickets = Ticket::where('status', 1)
      ->pluck('name', 'id')
      ->toArray();

    return view('admin.packages.create', compact('tickets'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = $request->all();

    $validator = Validator::make($data, [
      'name'               => 'required',
      'base_price'         => 'required|numeric',
      'default_person'     => 'required|integer|min:1',
      'extra_person_price' => 'required|numeric',
      'status'             => 'required',
      'tickets'            => 'required|array',
      'tickets.*'          => 'exists:tickets,id'
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;

    // Create package
    $package = Package::create([
      'name'               => $data['name'],
      'base_price'         => $data['base_price'],
      'default_person'     => $data['default_person'],
      'extra_person_price' => $data['extra_person_price'],
      'status'             => $data['status']
    ]);

    if ($package) {
      // Save package items (selected tickets)
      // service_id column stores ticket_id
      foreach ($data['tickets'] as $ticket_id) {
        PackageItem::create([
          'package_id' => $package->id,
          'service_id' => $ticket_id  // ticket_id: reference to tickets table
        ]);
      }

      return redirect()->route('packages.index')->with('flash_success', 'Package created successfully');
    } else {
      return redirect()->back()->withErrors(['error' => 'Failed to create package'])->withInput();
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $package = Package::with('items.ticket')->findOrFail($id);
    return view('admin.packages.show', compact('package'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $package = Package::with('items')->findOrFail($id);

    // Get active tickets for package composition
    $tickets = Ticket::where('status', 1)
      ->pluck('name', 'id')
      ->toArray();

    // Get currently selected ticket IDs (from service_id column)
    $selectedTickets = $package->items->pluck('service_id')->toArray();

    return view('admin.packages.edit', compact('package', 'tickets', 'selectedTickets'));
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
    $data = $request->all();

    $validator = Validator::make($data, [
      'name'               => 'required',
      'base_price'         => 'required|numeric',
      'default_person'     => 'required|integer|min:1',
      'extra_person_price' => 'required|numeric',
      'status'             => 'required',
      'tickets'            => 'required|array',
      'tickets.*'          => 'exists:tickets,id'
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $package = Package::findOrFail($id);

    // Update package
    $package->update([
      'name'               => $data['name'],
      'base_price'         => $data['base_price'],
      'default_person'     => $data['default_person'],
      'extra_person_price' => $data['extra_person_price'],
      'status'             => $data['status']
    ]);

    // Update package items - delete old and create new
    // service_id column stores ticket_id
    PackageItem::where('package_id', $package->id)->delete();

    foreach ($data['tickets'] as $ticket_id) {
      PackageItem::create([
        'package_id' => $package->id,
        'service_id' => $ticket_id  // ticket_id: reference to tickets table
      ]);
    }

    return redirect()->route('packages.index')->with('flash_success', 'Package updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $package = Package::findOrFail($id);

    // Delete related items first (cascade should handle this, but being safe)
    PackageItem::where('package_id', $id)->delete();

    // Delete package
    $deleted = $package->delete();

    if ($deleted) {
      return redirect()->back()->with('flash_success', 'Package deleted successfully');
    } else {
      return redirect()->back()->with('flash_error', 'Failed to delete package');
    }
  }
}
