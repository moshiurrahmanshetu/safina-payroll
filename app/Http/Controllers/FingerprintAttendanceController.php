<?php

namespace App\Http\Controllers;

use App\Services\FingerprintAttendanceService;
use Illuminate\Http\Request;

class FingerprintAttendanceController extends Controller
{
    protected $fingerprintAttendanceService;

    public function __construct(FingerprintAttendanceService $fingerprintAttendanceService)
    {
        parent::__construct();
        $this->fingerprintAttendanceService = $fingerprintAttendanceService;
    }


    public function index()
    {
        $stats = $this->fingerprintAttendanceService->getStats();
        return view('admin.fingerprint_attendance.index', compact('stats'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'date' => 'nullable|date',
        ]);

        $userId = $request->input('user_id');
        $date = $request->input('date');

        $result = $this->fingerprintAttendanceService->generateAttendance($userId, $date);

        return redirect()->route('fingerprint_attendance.index')
            ->with('success', "Attendance generation completed. Processed: {$result['processed']}, Skipped: {$result['skipped']}, Failed: {$result['failed']}");
    }

    public function history(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $sessions = $this->fingerprintAttendanceService->getHistory($userId, $startDate, $endDate);

        return view('admin.fingerprint_attendance.history', compact('sessions'));
    }
}
