<?php
/**
 * Employee Dashboard Controller
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        if (Auth::isAdmin()) {
            $this->redirect('/admin/dashboard');
        }

        $user = $this->user;
        $attendance = new Attendance();

        // Today's attendance
        $todayAttendance = $attendance->getTodayForUser($user->id);

        // Today's hours
        $todayHours = $todayAttendance && $todayAttendance->clock_out_time
            ? (float) $todayAttendance->total_hours
            : 0;

        // This week's hours
        $weekHours = $attendance->getWeekHoursForUser($user->id);

        // Can clock in? (no attendance today yet)
        $canClockIn = !$todayAttendance;

        // Can clock out? (clocked in, 5+ hours passed)
        $minHours = $this->config['min_clock_hours'] ?? 5;
        $elapsedHours = 0;
        if ($todayAttendance && $todayAttendance->clock_in_time && !$todayAttendance->clock_out_time) {
            $elapsed = time() - strtotime($todayAttendance->clock_in_time);
            $elapsedHours = round($elapsed / 3600, 2);
        }
        $canClockOut = $todayAttendance && !$todayAttendance->clock_out_time && $elapsedHours >= $minHours;

        // OJT progress
        $ojtProgress = null;
        if ($user->is_ojt && $user->required_hours) {
            $userModel = new User();
            $totalCompleted = $attendance->getTotalHoursForUser($user->id, $user->ojt_start_date ?? '2000-01-01', date('Y-m-d'));
            $remaining = max(0, $user->required_hours - $totalCompleted);
            $percent = min(100, $user->required_hours > 0 ? round(($totalCompleted / $user->required_hours) * 100, 1) : 0);
            $ojtProgress = [
                'required' => $user->required_hours,
                'completed' => $totalCompleted,
                'remaining' => $remaining,
                'percent' => $percent,
                'done' => $percent >= 100,
            ];
        }

        // Recent attendance
        $recentAttendance = $attendance->getByUser($user->id, 7);

        $this->view('dashboard.employee', [
            'todayAttendance' => $todayAttendance,
            'todayHours' => $todayHours,
            'weekHours' => $weekHours,
            'canClockIn' => $canClockIn,
            'canClockOut' => $canClockOut,
            'elapsedHours' => $elapsedHours,
            'minHours' => $minHours,
            'ojtProgress' => $ojtProgress,
            'recentAttendance' => $recentAttendance,
        ]);
    }
}
