<?php

namespace App\Http\Controllers;

use App\Models\FingerprintSession;
use App\Services\FingerprintProcessingService;
use Illuminate\Http\Request;

class FingerprintSessionController extends Controller
{
    protected $fingerprintProcessingService;

    public function __construct(FingerprintProcessingService $fingerprintProcessingService)
    {
        $this->fingerprintProcessingService = $fingerprintProcessingService;
    }

    public function index(Request $request)
    {
        $query = FingerprintSession::query();

        if ($request->has('user_id')) {
            $query->byEmployee($request->user_id);
        }

        if ($request->has('date')) {
            $query->byDate($request->date);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $sessions = $query->with(['user', 'shift'])->orderBy('attendance_date', 'desc')->paginate(50);

        $stats = [
            'total' => FingerprintSession::count(),
            'processed' => FingerprintSession::processed()->count(),
            'pending' => FingerprintSession::pending()->count(),
        ];

        return view('admin.fingerprint_sessions.index', compact('sessions', 'stats'));
    }

    public function process()
    {
        $result = $this->fingerprintProcessingService->processBatch();

        return redirect()->route('fingerprint_sessions.index')
            ->with('success', "Processing completed. Processed: {$result['processed']}, Skipped: {$result['skipped']}");
    }

    public function show($id)
    {
        $session = FingerprintSession::with(['user', 'shift'])->findOrFail($id);
        return view('admin.fingerprint_sessions.show', compact('session'));
    }

    public function destroy($id)
    {
        $session = FingerprintSession::findOrFail($id);
        $session->delete();

        return redirect()->route('fingerprint_sessions.index')
            ->with('success', 'Fingerprint session deleted successfully.');
    }
}
