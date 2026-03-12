<?php
/**
 * Admin Dashboard Controller
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Project;
use App\Models\InventoryItem;
use App\Models\PayrollRecord;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        Auth::requireAdmin();

        $userModel = new User();
        $attendance = new Attendance();
        $projectModel = new Project();
        $inventory = new InventoryItem();
        $payrollModel = new PayrollRecord();

        // Stats
        $totalEmployees = $userModel->countEmployees();
        $clockedInToday = $attendance->countClockedInToday();
        $departmentSummary = $attendance->getTodaySummaryByDepartment();
        $activeProjects = $projectModel->getActiveProjects();
        $lowStockItems = $inventory->getLowStock();
        $ojts = $userModel->getOJTs();

        // Payroll period
        $periodStart = date('Y-m-01');
        $periodEnd = date('Y-m-t');
        $payrollRecords = $payrollModel->getByPeriod($periodStart, $periodEnd);
        $payrollTotal = array_sum(array_column($payrollRecords, 'net_pay'));

        // OJT progress overview
        $ojtProgressData = [];
        foreach ($ojts as $ojt) {
            if ($ojt->required_hours) {
                $completed = $attendance->getTotalHoursForUser($ojt->id, $ojt->ojt_start_date ?? '2000-01-01', date('Y-m-d'));
                $percent = min(100, round(($completed / $ojt->required_hours) * 100, 1));
                $ojtProgressData[] = [
                    'user' => $ojt,
                    'completed' => $completed,
                    'percent' => $percent,
                ];
            }
        }

        $this->view('dashboard.admin', [
            'totalEmployees' => $totalEmployees,
            'clockedInToday' => $clockedInToday,
            'departmentSummary' => $departmentSummary,
            'activeProjects' => $activeProjects,
            'lowStockItems' => $lowStockItems,
            'ojtProgressData' => $ojtProgressData,
            'payrollTotal' => $payrollTotal,
            'payrollRecords' => $payrollRecords,
        ]);
    }
}
