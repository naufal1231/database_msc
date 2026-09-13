<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ParentController extends Controller
{
    public function attendanceHistory()
    {
        $athletes = auth()->user()->children;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $attendanceData = [];
        foreach ($athletes as $athlete) {
            $attendanceData[$athlete->id] = [
                'name' => $athlete->name,
                'attendance_percentage' => $athlete->getMonthlyAttendancePercentage($currentMonth, $currentYear),
                'attendance_this_month' => $athlete->attendances()
                    ->whereMonth('attendance_date', $currentMonth)
                    ->whereYear('attendance_date', $currentYear)
                    ->orderBy('attendance_date', 'desc')
                    ->take(10)
                    ->get(),
            ];
        }
        
        return view('parent.attendance.history', compact('athletes', 'attendanceData', 'currentMonth', 'currentYear'));
    }
    
    public function attendanceMonthly($month, $year)
    {
        $athletes = auth()->user()->children;
        
        $attendanceData = [];
        foreach ($athletes as $athlete) {
            $attendances = $athlete->attendances()
                ->whereMonth('attendance_date', $month)
                ->whereYear('attendance_date', $year)
                ->orderBy('attendance_date', 'desc')
                ->get();
            
            $presentCount = $attendances->where('status', 'hadir')->count();
            $permissionCount = $attendances->where('status', 'izin')->count();
            $sickCount = $attendances->where('status', 'sakit')->count();
            $absentCount = $attendances->where('status', 'tanpa_keterangan')->count();
            $totalRecords = $attendances->count();
            
            $attendanceData[$athlete->id] = [
                'name' => $athlete->name,
                'attendances' => $attendances,
                'percentage' => $athlete->getMonthlyAttendancePercentage($month, $year),
                'present_count' => $presentCount,
                'permission_count' => $permissionCount,
                'sick_count' => $sickCount,
                'absent_count' => $absentCount,
                'total_count' => $totalRecords,
            ];
        }
        
        return view('parent.attendance.monthly', compact('athletes', 'attendanceData', 'month', 'year'));
    }
}