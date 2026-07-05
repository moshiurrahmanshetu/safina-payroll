<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketSale;
use App\Models\Gate;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Validator;

class TicketSaleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = Auth::user();

    // Check permission to view all ticket sales
    $canViewAll = $this->hasPermission($user->role_id, 'view_all_ticket_sales');

    $query = TicketSale::select('qr_code', 'ticket_id', 'price', 'discount_amount', 'total_price', 'gate_id', 'date', 'is_used', 'used_at', 'sale_group_token', 'created_by', 'created_at', 'updated_at')
      ->with(['ticket', 'creator', 'gate']);

    // Apply permission-based visibility filtering
    if (!$canViewAll) {
      // Regular users see only ticket sales they created
      $query->where('created_by', $user->id);
    }

    // Apply from date filter
    if ($request->has('from_date') && $request->from_date) {
      $query->where('date', '>=', $request->from_date);
    }

    // Apply to date filter
    if ($request->has('to_date') && $request->to_date) {
      $query->where('date', '<=', $request->to_date);
    }

    // Apply user filter
    if ($request->has('user_id') && $request->user_id) {
      $query->where('created_by', $request->user_id);
    }

    // Apply counter (gate) filter
    if ($request->has('gate_id') && $request->gate_id) {
      $query->where('gate_id', $request->gate_id);
    }

    // Apply ticket filter
    if ($request->has('ticket_id') && $request->ticket_id) {
      $query->where('ticket_id', $request->ticket_id);
    }

    // Apply search filter (search by qr_code)
    if ($request->has('search') && $request->search) {
      $query->where('qr_code', 'like', '%' . $request->search . '%');
    }

    $ticket_sales = $query->orderBy('qr_code','desc')->get();
    $users = User::where('status', 1)->pluck('name','id');
    $gates = Gate::where('status', 1)->pluck('name','id');
    $tickets = Ticket::where('status', 1)->pluck('name','id');

    return view('admin.ticket_sales.index',compact('ticket_sales','users','gates','tickets'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $user = Auth::user();
    $gate = $user->gates()->with('tickets')->first();
    $gateName = $gate->name ?? 'N/A';

    // Filter tickets by gate's allowed tickets and active status
    if ($gate && $gate->tickets->count() > 0) {
      $tickets = $gate->tickets->where('status', 1)->pluck('name', 'id')->toArray();
    } else {
      // User has no gate or gate has no tickets assigned - show empty
      $tickets = [];
    }

    return view('admin.ticket_sales.create',compact('tickets', 'gateName'));
  }

  /**
   * Show print view for ticket sale
   *
   * @param  string  $qr_code
   * @return \Illuminate\Http\Response
   */
  public function print($qr_code)
  {
    $ticket_sale = TicketSale::with(['ticket', 'gate', 'creator'])->findOrFail($qr_code);
    return view('admin.ticket_sales.print',compact('ticket_sale'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data=request()->all();
    $validator=Validator::make($data,
      array(
        'ticket_id'       =>'required',
        'quantity'        =>'required|integer|min:1',
        'price'           =>'required|numeric',
        'discount_amount' =>'nullable|numeric|min:0',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user = Auth::user();

    // Validate ticket access for user's gates
    if ($user->gates()->exists()) {
      $gate = $user->gates()->with('tickets')->first();
      if ($gate && $gate->tickets->count() > 0) {
        $allowedTicketIds = $gate->tickets->pluck('id')->toArray();
        if (!in_array($data['ticket_id'], $allowedTicketIds)) {
          return redirect()->back()->withErrors(['ticket_id' => 'Unauthorized ticket access for this gate'])->withInput();
        }
      } else {
        // Gate has no tickets assigned
        return redirect()->back()->withErrors(['ticket_id' => 'No tickets assigned to this gate'])->withInput();
      }
    } else {
      // User has no gate assigned
      return redirect()->back()->withErrors(['ticket_id' => 'User is not assigned to any gate'])->withInput();
    }

    $quantity = intval($data['quantity']);
    $price = floatval($data['price']);
    $discount_amount = floatval($data['discount_amount'] ?? 0);

    // Calculate per-ticket final price
    $final_price = $price - $discount_amount;

    // Generate ONE sale group token for this batch of tickets
    $sale_group_token = TicketSale::generateSaleGroupToken();

    // Get the user's primary gate for ticket creation
    $gate = $user->gates()->first();

    // Create individual ticket records (1 row = 1 ticket)
    $ticket_sales = [];
    for ($i = 0; $i < $quantity; $i++) {
      $totalPrice = $price - $discount_amount;
      $ticket_sales[] = TicketSale::create([
        'ticket_id'         => $data['ticket_id'],
        'price'             => $price,
        'discount_amount'   => $discount_amount,
        'total_price'       => $totalPrice,
        'gate_id'           => $gate ? $gate->id : null,
        'date'              => now()->toDateString(),
        'qr_code'           => TicketSale::generateQrCode(),
        'is_used'           => false,
        'sale_group_token'  => $sale_group_token,
        'created_by'        => $user->id,
      ]);
    }

    if($ticket_sales){
      $message="Ticket sale created successfully with {$quantity} tickets";
      // Redirect to group print page with sale_group_token
      return redirect()->route('ticket_sales.group_print', $sale_group_token)->with('flash_success',$message);
    }else{
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  /**
   * Get ticket price via AJAX
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function getTicketPrice($id)
  {
    $ticket = Ticket::find($id);
    if($ticket){
      return response()->json([
        'price'   => $ticket->price,
        'success' => true
      ]);
    }
    return response()->json(['success' => false], 404);
  }

  /**
   * Display group print page for tickets with same sale_group_token
   *
   * @param  string  $sale_group_token
   * @return \Illuminate\Http\Response
   */
  public function groupPrint($sale_group_token)
  {
    $tickets = TicketSale::with(['ticket', 'gate', 'creator'])
      ->where('sale_group_token', $sale_group_token)
      ->get();

    if ($tickets->isEmpty()) {
      return redirect()->route('ticket_sales.index')->with('flash_error', 'Ticket group not found');
    }

    return view('admin.ticket_sales.group_print', compact('tickets', 'sale_group_token'));
  }

  /**
   * Display the ticket sales report.
   *
   * @return \Illuminate\Http\Response
   */
  public function report(Request $request)
  {
    $user = Auth::user();

    // Check permission to view all ticket sales
    $canViewAll = $this->hasPermission($user->role_id, 'view_all_ticket_sales');

    // Main report query - now counting individual tickets
    $query = TicketSale::selectRaw('ticket_id, count(*) as total_quantity, sum(price) as total_amount, sum(discount_amount) as total_discount, sum(price - discount_amount) as total_final_amount')
      ->with('ticket');

    // Apply permission-based visibility filtering
    if (!$canViewAll) {
      // Regular users see only ticket sales they created
      $query->where('created_by', $user->id);
    }

    // Apply from date filter
    if ($request->has('from_date') && $request->from_date) {
      $query->where('date', '>=', $request->from_date);
    }

    // Apply to date filter
    if ($request->has('to_date') && $request->to_date) {
      $query->where('date', '<=', $request->to_date);
    }

    // Apply user filter
    if ($request->has('user_id') && $request->user_id) {
      $query->where('created_by', $request->user_id);
    }

    // Apply counter (gate) filter
    if ($request->has('gate_id') && $request->gate_id) {
      $query->where('gate_id', $request->gate_id);
    }

    // Apply ticket filter
    if ($request->has('ticket_id') && $request->ticket_id) {
      $query->where('ticket_id', $request->ticket_id);
    }

    $report = $query->groupBy('ticket_id')->get();

    $grand_quantity = $report->sum('total_quantity');
    $grand_gross = $report->sum('total_amount');
    $grand_discount = $report->sum('total_discount');
    $grand_amount = $report->sum('total_final_amount');

    $users = User::where('status', 1)->pluck('name','id');
    $gates = Gate::where('status', 1)->pluck('name','id');
    $tickets = Ticket::where('status', 1)->pluck('name','id');

    return view('admin.ticket_sales.report', compact('report', 'grand_quantity', 'grand_gross', 'grand_discount', 'grand_amount', 'users', 'gates', 'tickets'));
  }

  /**
   * Show QR validation form
   *
   * @return \Illuminate\Http\Response
   */
  public function validationForm()
  {
    return view('admin.ticket_sales.validate');
  }

  /**
   * Validate ticket by QR code or ticket number
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function validateTicket(Request $request)
  {
    $qr_code = $request->ticket_number;

    $ticketSale = TicketSale::with(['ticket'])
      ->where('qr_code', $qr_code)
      ->first();

    if (!$ticketSale) {
      return response()->json([
        'valid'   => false,
        'message' => 'Ticket not found'
      ]);
    }

    if ($ticketSale->is_used) {
      return response()->json([
        'valid'       => false,
        'message'     => 'Ticket already used',
        'used_at'     => $ticketSale->used_at,
        'ticket'      => $ticketSale->ticket->name,
        'qr_code'     => $ticketSale->qr_code
      ]);
    }

    // Mark as used
    $ticketSale->markAsUsed();

    return response()->json([
      'valid'         => true,
      'message'       => 'Ticket validated successfully',
      'ticket'        => $ticketSale->ticket->name,
      'qr_code'       => $ticketSale->qr_code,
      'price'         => $ticketSale->price
    ]);
  }

  /**
   * Show simple scan page for QR validation
   *
   * @return \Illuminate\Http\Response
   */
  public function scan()
  {
    return view('admin.ticket_sales.scan');
  }

  /**
   * Validate ticket from scan page
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function scanValidate(Request $request)
  {
    $qr_code = $request->ticket_number;

    $ticketSale = TicketSale::with(['ticket'])
      ->where('qr_code', $qr_code)
      ->first();

    if (!$ticketSale) {
      return response()->json([
        'status'  => 'invalid',
        'message' => 'Ticket not found'
      ]);
    }

    if ($ticketSale->is_used) {
      return response()->json([
        'status'        => 'used',
        'message'       => 'Ticket already used',
        'used_at'       => $ticketSale->used_at ? $ticketSale->used_at->format('d-m-Y h:i A') : null,
        'ticket'        => $ticketSale->ticket->name,
        'qr_code'       => $ticketSale->qr_code
      ]);
    }

    // Mark as used
    $ticketSale->markAsUsed();

    return response()->json([
      'status'        => 'valid',
      'message'       => 'Ticket validated successfully',
      'ticket'        => $ticketSale->ticket->name,
      'qr_code'       => $ticketSale->qr_code,
      'price'         => $ticketSale->price
    ]);
  }

  /**
   * Preview ticket for QR scan (show details without marking as used)
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function verifyTicket($ticket_number)
  {
    $ticketSale = TicketSale::with(['ticket'])
      ->where('qr_code', $ticket_number)
      ->first();

    // Check if ticket exists
    if (!$ticketSale) {
      return view('admin.ticket_sales.verify_preview', [
        'status' => 'invalid',
        'message' => 'Invalid Ticket',
        'color' => 'danger'
      ]);
    }

    // Check if ticket date is today
    $ticketDate = $ticketSale->date->toDateString();
    $today = now()->toDateString();
    if ($ticketDate != $today) {
      return view('admin.ticket_sales.verify_preview', [
        'status' => 'expired',
        'message' => 'Expired Ticket',
        'ticket' => $ticketSale,
        'color' => 'warning'
      ]);
    }

    // Check if already used
    if ($ticketSale->is_used) {
      return view('admin.ticket_sales.verify_preview', [
        'status' => 'used',
        'message' => 'Already Used',
        'ticket' => $ticketSale,
        'used_at' => $ticketSale->used_at,
        'color' => 'warning'
      ]);
    }

    // Show preview page with verify button (do NOT mark as used yet)
    return view('admin.ticket_sales.verify_preview', [
      'status' => 'preview',
      'message' => 'Ticket Preview',
      'ticket' => $ticketSale,
      'color' => 'info'
    ]);
  }

  /**
   * Confirm and validate ticket (manual verification)
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function confirmVerify(Request $request)
  {
    $ticket_number = $request->input('ticket_number');

    $ticketSale = TicketSale::with(['ticket'])
      ->where('qr_code', $ticket_number)
      ->first();

    // Check if ticket exists
    if (!$ticketSale) {
      return redirect()->route('ticket_sales.scan')
        ->with('flash_error', 'Invalid Ticket');
    }

    // Check if ticket date is today
    $ticketDate = $ticketSale->date->toDateString();
    $today = now()->toDateString();
    if ($ticketDate != $today) {
      return redirect()->route('ticket_sales.scan')
        ->with('flash_error', 'Expired Ticket');
    }

    // Check if already used
    if ($ticketSale->is_used) {
      return redirect()->route('ticket_sales.scan')
        ->with('flash_warning', 'Ticket already used on ' . $ticketSale->used_at->format('d-m-Y h:i A'));
    }

    // Mark as used
    $ticketSale->markAsUsed();

    return redirect()->route('ticket_sales.scan')
      ->with('flash_success', 'Ticket validated successfully! ' . $ticketSale->ticket->name . ' - Entry allowed.');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  string  $qr_code
   * @return \Illuminate\Http\Response
   */
  public function destroy($qr_code)
  {
    $deleted = TicketSale::where('qr_code',$qr_code)->delete();
    $message = "Ticket sale deleted successfully";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }

  /**
   * Check if a role has a specific permission
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
