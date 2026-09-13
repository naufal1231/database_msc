<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SwimClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classes = SwimClass::all();
        $selectedClass = $request->get('swim_class_id');
        $selectedDate = $request->get('date', Carbon::today()->toDateString());
        
        $attendances = Attendance::with(['athlete', 'createdBy'])
            ->when($selectedClass, function ($query) use ($selectedClass) {
                return $query->where('swim_class_id', $selectedClass);
            })
            ->whereDate('attendance_date', $selectedDate)
            ->get();
            
        return view('admin.attendance.index', compact('classes', 'selectedClass', 'selectedDate', 'attendances'));
    }
    
    public function recap(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $classId = $request->get('swim_class_id');
        
        $attendances = Attendance::with('athlete')
            ->when($classId, function ($query) use ($classId) {
                return $query->where('swim_class_id', $classId);
            })
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get()
            ->groupBy('athlete_id');
            
        $summary = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'hadir')->count(),
            'permission' => $attendances->where('status', 'izin')->count(),
            'sick' => $attendances->where('status', 'sakit')->count(),
            'absent' => $attendances->where('status', 'tanpa_keterangan')->count(),
        ];
        
        return view('admin.attendance.recap', compact('attendances', 'summary', 'month', 'year'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'swim_class_id' => 'required|exists:classes,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,tanpa_keterangan',
            'note' => 'nullable|string|max:500',
        ]);
        
        // Check if attendance already exists for this athlete on this date
        $existing = Attendance::where('athlete_id', $request->athlete_id)
            ->whereDate('attendance_date', $request->attendance_date)
            ->first();
            
        if ($existing) {
            return back()->withErrors(['attendance' => 'Absensi untuk atlet ini sudah dicatat hari ini.']);
        }
        
        Attendance::create([
            'athlete_id' => $request->athlete_id,
            'swim_class_id' => $request->swim_class_id,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Absensi berhasil dicatat.');
    }
    
    public function export($month, $year, $classId = null)
    {
        // This method would implement Excel export functionality
        // For now, just return a message
        return response()->json([
            'message' => 'Export functionality will be implemented here',
            'month' => $month,
            'year' => $year,
            'class_id' => $classId,
        ]);
    }
}