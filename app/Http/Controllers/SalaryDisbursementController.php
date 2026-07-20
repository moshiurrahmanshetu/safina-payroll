<?php

namespace App\Http\Controllers;

use App\Models\SalaryDisbursement;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalaryDisbursementController extends Controller
{
    /**
     * Display a listing of salary disbursements.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = SalaryDisbursement::with(['payroll', 'employee', 'creator'])->orderBy('payment_date', 'desc');

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply payment status filter
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Apply payment method filter
        if ($request->has('payment_method') && $request->payment_method !== '') {
            $query->where('payment_method', $request->payment_method);
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $disbursements = $query->get();

        return view('admin.salary_disbursements.index', compact('disbursements'));
    }

    /**
     * Show the form for creating a new salary disbursement.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get approved payrolls that are not yet paid
        $approvedPayrolls = Payroll::where('approval_status', 'approved')
                                   ->whereDoesntHave('disbursement')
                                   ->with(['user', 'salary'])
                                   ->get();

        return view('admin.salary_disbursements.create', compact('approvedPayrolls'));
    }

    /**
     * Store a newly created salary disbursement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->all();

        $validator = Validator::make($data, [
            'payroll_id' => 'required|exists:payrolls,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:Cash,Bank,Mobile Banking,Cheque',
            'reference_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if payroll is approved
        $payroll = Payroll::findOrFail($data['payroll_id']);
        if ($payroll->approval_status !== 'approved') {
            return redirect()->back()->withErrors(['payroll_id' => 'Only approved payrolls can be paid'])->withInput();
        }

        // Check if payroll already has a disbursement (duplicate prevention)
        $existingDisbursement = SalaryDisbursement::where('payroll_id', $data['payroll_id'])->exists();
        if ($existingDisbursement) {
            return redirect()->back()->withErrors(['payroll_id' => 'This payroll has already been paid'])->withInput();
        }

        $user_id = Auth::user()->id;

        // Use transaction for data integrity
        DB::beginTransaction();
        try {
            // Create salary disbursement
            $disbursement = new SalaryDisbursement();
            $disbursement->payroll_id = $data['payroll_id'];
            $disbursement->employee_id = $payroll->user_id;
            $disbursement->payment_date = $data['payment_date'];
            $disbursement->payment_method = $data['payment_method'];
            $disbursement->reference_number = $data['reference_number'] ?? null;
            $disbursement->amount = $data['amount'];
            $disbursement->payment_status = 'Paid';
            $disbursement->remarks = $data['remarks'] ?? null;
            $disbursement->created_by = $user_id;
            $disbursement->updated_by = $user_id;
            $disbursement->save();

            // Update payroll status to Paid
            $payroll->approval_status = 'Paid';
            $payroll->updated_by = $user_id;
            $payroll->save();

            DB::commit();

            return redirect()->route('salary_disbursements.index')->with('flash_success', 'Salary disbursement created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error creating salary disbursement: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified salary disbursement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $disbursement = SalaryDisbursement::with(['payroll', 'employee', 'creator', 'updater'])->findOrFail($id);

        return view('admin.salary_disbursements.show', compact('disbursement'));
    }

    /**
     * Process payment from payroll list (modal)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processPayment(Request $request)
    {
        $data = request()->all();

        $validator = Validator::make($data, [
            'payroll_id' => 'required|exists:payrolls,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:Cash,Bank,Mobile Banking,Cheque',
            'reference_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if payroll is approved
        $payroll = Payroll::findOrFail($data['payroll_id']);
        if ($payroll->approval_status !== 'approved') {
            return response()->json(['error' => 'Only approved payrolls can be paid'], 422);
        }

        // Check if payroll already has a disbursement (duplicate prevention)
        $existingDisbursement = SalaryDisbursement::where('payroll_id', $data['payroll_id'])->exists();
        if ($existingDisbursement) {
            return response()->json(['error' => 'This payroll has already been paid'], 422);
        }

        $user_id = Auth::user()->id;

        // Use transaction for data integrity
        DB::beginTransaction();
        try {
            // Create salary disbursement
            $disbursement = new SalaryDisbursement();
            $disbursement->payroll_id = $data['payroll_id'];
            $disbursement->employee_id = $payroll->user_id;
            $disbursement->payment_date = $data['payment_date'];
            $disbursement->payment_method = $data['payment_method'];
            $disbursement->reference_number = $data['reference_number'] ?? null;
            $disbursement->amount = $payroll->generated_salary;
            $disbursement->payment_status = 'Paid';
            $disbursement->remarks = $data['remarks'] ?? null;
            $disbursement->created_by = $user_id;
            $disbursement->updated_by = $user_id;
            $disbursement->save();

            // Update payroll status to Paid
            $payroll->approval_status = 'Paid';
            $payroll->updated_by = $user_id;
            $payroll->save();

            DB::commit();

            return response()->json(['success' => 'Payment processed successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error processing payment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate payslip for a disbursement
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payslip($id)
    {
        $disbursement = SalaryDisbursement::with(['payroll', 'employee', 'payroll.salary', 'payroll.attendanceMonth'])->findOrFail($id);

        return view('admin.salary_disbursements.payslip', compact('disbursement'));
    }

    /**
     * Cancel a salary disbursement
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $disbursement = SalaryDisbursement::findOrFail($id);

        // Cannot cancel already cancelled disbursement
        if ($disbursement->payment_status === 'Cancelled') {
            return redirect()->back()->withErrors(['payment_status' => 'Disbursement is already cancelled']);
        }

        // Use transaction for data integrity
        DB::beginTransaction();
        try {
            // Mark disbursement as cancelled
            $disbursement->payment_status = 'Cancelled';
            $disbursement->updated_by = Auth::user()->id;
            $disbursement->save();

            // Update payroll status back to approved
            $payroll = $disbursement->payroll;
            $payroll->approval_status = 'approved';
            $payroll->updated_by = Auth::user()->id;
            $payroll->save();

            DB::commit();

            return redirect()->back()->with('flash_success', 'Salary disbursement cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error cancelling disbursement: ' . $e->getMessage()]);
        }
    }

    /**
     * Payment Register Report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentRegister(Request $request)
    {
        $query = SalaryDisbursement::with(['payroll', 'employee', 'creator'])->orderBy('payment_date', 'desc');

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $disbursements = $query->get();
        $totalAmount = $disbursements->sum('amount');

        return view('admin.salary_disbursements.reports.payment_register', compact('disbursements', 'totalAmount'));
    }

    /**
     * Cash Payment Report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cashPaymentReport(Request $request)
    {
        $query = SalaryDisbursement::with(['payroll', 'employee', 'creator'])
                                   ->byPaymentMethod('Cash')
                                   ->orderBy('payment_date', 'desc');

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $disbursements = $query->get();
        $totalAmount = $disbursements->sum('amount');

        return view('admin.salary_disbursements.reports.cash_payment', compact('disbursements', 'totalAmount'));
    }

    /**
     * Bank Payment Report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bankPaymentReport(Request $request)
    {
        $query = SalaryDisbursement::with(['payroll', 'employee', 'creator'])
                                   ->whereIn('payment_method', ['Bank', 'Mobile Banking', 'Cheque'])
                                   ->orderBy('payment_date', 'desc');

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $disbursements = $query->get();
        $totalAmount = $disbursements->sum('amount');

        return view('admin.salary_disbursements.reports.bank_payment', compact('disbursements', 'totalAmount'));
    }

    /**
     * Monthly Salary Register
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function monthlySalaryRegister(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        
        $query = SalaryDisbursement::with(['payroll', 'employee', 'creator'])
                                   ->whereHas('payroll', function($q) use ($month) {
                                       $q->where('payroll_month', $month);
                                   })
                                   ->orderBy('payment_date', 'desc');

        $disbursements = $query->get();
        $totalAmount = $disbursements->sum('amount');

        return view('admin.salary_disbursements.reports.monthly_register', compact('disbursements', 'totalAmount', 'month'));
    }
}
