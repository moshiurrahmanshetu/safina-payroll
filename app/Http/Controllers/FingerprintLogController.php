<?php

namespace App\Http\Controllers;

use App\Models\FingerprintLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FingerprintLogController extends Controller
{
    public function index(Request $request)
    {
        $query = FingerprintLog::query();

        if ($request->has('employee_code')) {
            $query->byEmployeeCode($request->employee_code);
        }

        if ($request->has('batch')) {
            $query->byBatch($request->batch);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $logs = $query->active()->orderBy('punch_datetime', 'desc')->paginate(50);

        $stats = [
            'total' => FingerprintLog::active()->count(),
            'processed' => FingerprintLog::active()->processed()->count(),
            'pending' => FingerprintLog::active()->pending()->count(),
        ];

        $recentBatches = FingerprintLog::active()
            ->select('import_batch')
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->pluck('import_batch')
            ->take(10);

        return view('admin.fingerprint_logs.index', compact('logs', 'stats', 'recentBatches'));
    }

    public function create()
    {
        return view('admin.fingerprint_logs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $importBatch = 'BATCH_' . Carbon::now()->format('Ymd_His') . '_' . strtoupper(substr(md5(uniqid()), 0, 8));

        $imported = 0;
        $skipped = 0;
        $errors = [];

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) < 4) {
                    $skipped++;
                    continue;
                }

                $employeeCode = trim($row[0]);
                $punchDate = trim($row[1]);
                $punchTime = trim($row[2]);
                $punchType = trim(strtoupper($row[3]));

                // Validation
                if (empty($employeeCode)) {
                    $skipped++;
                    continue;
                }

                if (!in_array($punchType, ['IN', 'OUT'])) {
                    $skipped++;
                    continue;
                }

                try {
                    $punchDateTime = Carbon::parse($punchDate . ' ' . $punchTime);
                } catch (\Exception $e) {
                    $skipped++;
                    continue;
                }

                FingerprintLog::create([
                    'employee_code' => $employeeCode,
                    'punch_datetime' => $punchDateTime,
                    'punch_type' => $punchType,
                    'device_id' => null,
                    'source' => 'CSV',
                    'import_batch' => $importBatch,
                    'processed' => false,
                    'processed_at' => null,
                    'status' => 'Active',
                ]);

                $imported++;
            }

            fclose($handle);
        }

        return redirect()->route('fingerprint_logs.index')
            ->with('success', "Import completed. Imported: {$imported}, Skipped: {$skipped}")
            ->with('import_batch', $importBatch);
    }

    public function show($id)
    {
        $log = FingerprintLog::findOrFail($id);
        return view('admin.fingerprint_logs.show', compact('log'));
    }

    public function destroy($id)
    {
        $log = FingerprintLog::findOrFail($id);
        $log->delete();

        return redirect()->route('fingerprint_logs.index')
            ->with('success', 'Fingerprint log deleted successfully.');
    }
}
