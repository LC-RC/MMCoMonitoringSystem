<?php
/**
 * Attendance Controller
 * Clock In/Out, Admin Attendance View
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\User;

class AttendanceController extends Controller
{
    /**
     * Clock In
     */
    public function clockIn(): void
    {
        Auth::requireAuth();
        $this->requireCsrf();

        if (Auth::isAdmin()) {
            $_SESSION['error'] = 'Admin cannot clock in.';
            $this->redirect('/admin/dashboard');
        }

        $attendance = new Attendance();
        $user = $this->user;

        if ($attendance->hasClockedInToday($user->id)) {
            $_SESSION['error'] = 'You have already clocked in today.';
            $this->redirect('/dashboard');
        }

        $now = date('Y-m-d H:i:s');
        $attendance->create([
            'user_id' => $user->id,
            'clock_in_time' => $now,
            'date' => date('Y-m-d'),
        ]);

        $_SESSION['success'] = 'Successfully clocked in.';
        $this->redirect('/dashboard');
    }

    /**
     * Clock Out
     */
    public function clockOut(): void
    {
        Auth::requireAuth();
        $this->requireCsrf();

        if (Auth::isAdmin()) {
            $_SESSION['error'] = 'Admin cannot clock out.';
            $this->redirect('/admin/dashboard');
        }

        $attendance = new Attendance();
        $user = $this->user;

        $today = $attendance->getOpenSession($user->id);
        if (!$today) {
            $_SESSION['error'] = 'No active session to clock out.';
            $this->redirect('/dashboard');
        }

        $minHours = $this->config['min_clock_hours'] ?? 5;
        $elapsed = time() - strtotime($today->clock_in_time);
        $elapsedHours = $elapsed / 3600;

        if ($elapsedHours < $minHours) {
            $_SESSION['error'] = "You must work at least {$minHours} hours before clocking out.";
            $this->redirect('/dashboard');
        }

        $now = date('Y-m-d H:i:s');
        $regularHours = min(8, $elapsedHours);
        $overtimeHours = max(0, $elapsedHours - 8);

        $attendance->update($today->id, [
            'clock_out_time' => $now,
            'total_hours' => round($elapsedHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
        ]);

        $_SESSION['success'] = 'Successfully clocked out.';
        $this->redirect('/dashboard');
    }

    /**
     * Admin: View all attendance with filters
     */
    public function adminIndex(): void
    {
        Auth::requireAdmin();

        $filters = [
            'department' => $_GET['department'] ?? '',
            'is_ojt' => $_GET['is_ojt'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'search' => trim($_GET['search'] ?? ''),
        ];

        $attendance = new Attendance();
        $records = $attendance->getAllFiltered($filters);

        // Export CSV
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $this->exportCsv($records);
            return;
        }

        $this->view('attendance.admin', [
            'records' => $records,
            'filters' => $filters,
        ]);
    }

    /**
     * Export attendance to CSV
     */
    private function exportCsv(array $records): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Name', 'Department', 'OJT', 'Clock In', 'Clock Out', 'Total Hours', 'Overtime']);
        foreach ($records as $r) {
            fputcsv($output, [
                $r->date ?? '',
                $r->name ?? '',
                $r->department ?? '',
                $r->is_ojt ? 'Yes' : 'No',
                $r->clock_in_time ?? '',
                $r->clock_out_time ?? '',
                $r->total_hours ?? '',
                $r->overtime_hours ?? '',
            ]);
        }
        fclose($output);
        exit;
    }
}
