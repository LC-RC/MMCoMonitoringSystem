<?php
/**
 * Payroll Controller
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\PayrollRecord;

class PayrollController extends Controller
{
    /**
     * Admin: Payroll list
     */
    public function adminIndex(): void
    {
        Auth::requireAdmin();

        $periodStart = $_GET['period_start'] ?? date('Y-m-01');
        $periodEnd = $_GET['period_end'] ?? date('Y-m-t');

        $payrollModel = new PayrollRecord();
        $records = $payrollModel->getByPeriod($periodStart, $periodEnd);

        // Regenerate if empty but period selected
        if (empty($records) && isset($_GET['generate'])) {
            $this->generatePayroll($periodStart, $periodEnd);
            $records = $payrollModel->getByPeriod($periodStart, $periodEnd);
        }

        $this->view('payroll.admin', [
            'records' => $records,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
        ]);
    }

    /**
     * Generate payroll for period
     */
    private function generatePayroll(string $start, string $end): void
    {
        $userModel = new User();
        $attendance = new Attendance();
        $payrollModel = new PayrollRecord();
        $hourlyRate = $this->config['regular_hourly_rate'] ?? 500;
        $overtimeMultiplier = $this->config['overtime_multiplier'] ?? 1.5;

        $employees = $userModel->getEmployees();
        foreach ($employees as $emp) {
            $existing = $payrollModel->getOrCreateForUser($emp->id, $start, $end);
            if ($existing) continue;

            // Get total and overtime from attendance
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare(
                "SELECT COALESCE(SUM(total_hours), 0) as total, COALESCE(SUM(overtime_hours), 0) as ot
                 FROM attendance WHERE user_id = ? AND date BETWEEN ? AND ? AND clock_out_time IS NOT NULL"
            );
            $stmt->execute([$emp->id, $start, $end]);
            $row = $stmt->fetch(\PDO::FETCH_OBJ);

            $totalHours = (float) ($row->total ?? 0);
            $overtimeHours = (float) ($row->ot ?? 0);
            $regularHours = $totalHours - $overtimeHours;

            $grossPay = ($regularHours * $hourlyRate) + ($overtimeHours * $hourlyRate * $overtimeMultiplier);
            $deductions = 0; // Placeholder
            $netPay = $grossPay - $deductions;

            $payrollModel->create([
                'user_id' => $emp->id,
                'period_start' => $start,
                'period_end' => $end,
                'regular_hours' => round($regularHours, 2),
                'overtime_hours' => round($overtimeHours, 2),
                'gross_pay' => round($grossPay, 2),
                'deductions' => $deductions,
                'net_pay' => round($netPay, 2),
            ]);
        }

        $_SESSION['success'] = 'Payroll generated for the selected period.';
        $this->redirect('/admin/payroll?period_start=' . $start . '&period_end=' . $end);
    }

    /**
     * Admin: Generate payroll action
     */
    public function generate(): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $start = $_POST['period_start'] ?? date('Y-m-01');
        $end = $_POST['period_end'] ?? date('Y-m-t');

        $this->generatePayroll($start, $end);
    }

    /**
     * View payslip
     */
    public function payslip(int $id): void
    {
        Auth::requireAuth();

        $payrollModel = new PayrollRecord();
        $record = $payrollModel->find($id);
        if (!$record) {
            $_SESSION['error'] = 'Record not found.';
            $this->redirect('/dashboard');
        }

        // Employees see only their own; admin sees all
        if (!Auth::isAdmin() && $record->user_id != $this->user->id) {
            $this->redirect('/dashboard');
        }

        $userModel = new User();
        $user = $userModel->find($record->user_id);

        $this->view('payroll.payslip', [
            'record' => $record,
            'user' => $user,
        ]);
    }
}
