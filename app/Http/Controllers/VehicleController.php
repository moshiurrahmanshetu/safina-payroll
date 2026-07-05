<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
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
   * Display a listing of vehicles
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $vehicles = Vehicle::orderBy('name')->get();
    return view('admin.vehicles.index', compact('vehicles'));
  }

  /**
   * Show the form for creating a new vehicle
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.vehicles.create');
  }

  /**
   * Store a newly created vehicle
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = $request->all();
    $validator = Validator::make($data, [
      'name' => 'required|string|max:255',
      'base_price' => 'required|numeric|min:0',
      'status' => 'required|in:active,inactive',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    Vehicle::create($data);

    return redirect()->route('vehicles.index')->with('flash_success', 'Vehicle created successfully');
  }

  /**
   * Show the form for editing a vehicle
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $vehicle = Vehicle::findOrFail($id);
    return view('admin.vehicles.edit', compact('vehicle'));
  }

  /**
   * Update the specified vehicle
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $vehicle = Vehicle::findOrFail($id);
    $data = $request->all();

    $validator = Validator::make($data, [
      'name' => 'required|string|max:255',
      'base_price' => 'required|numeric|min:0',
      'status' => 'required|in:active,inactive',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $vehicle->update($data);

    return redirect()->route('vehicles.index')->with('flash_success', 'Vehicle updated successfully');
  }

  /**
   * Get vehicle rates as JSON for AJAX
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getRates()
  {
    $rates = Vehicle::where('status', 'active')
      ->pluck('base_price', 'id')
      ->toArray();

    return response()->json($rates);
  }
}
