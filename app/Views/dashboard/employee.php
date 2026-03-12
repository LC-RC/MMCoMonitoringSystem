<?php
$title = 'Dashboard';
$pageTitle = 'Employee Dashboard';
ob_start();
?>

<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($user->name) ?>!</h2>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 rounded-full text-sm font-medium btn-primary text-white">
                        <?= htmlspecialchars($user->department) ?> Staff
                    </span>
                    <?php if ($user->is_ojt): ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium <?= \App\Core\ThemeHelper::getOjtBadgeColor($user->department) ?> text-white">
                            OJT
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Clock In/Out -->
            <div class="flex gap-3">
                <?php if ($canClockIn): ?>
                    <form action="<?= base_path() ?>/attendance/clock-in" method="POST">
                        <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                        <button type="submit" class="px-6 py-3 rounded-xl font-medium text-white btn-primary hover:opacity-90 shadow-lg transition-smooth">
                            Clock In
                        </button>
                    </form>
                <?php elseif ($todayAttendance && !$todayAttendance->clock_out_time): ?>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">Elapsed: <strong><?= number_format($elapsedHours, 1) ?>h</strong>
                            <?php if (!$canClockOut): ?>
                                (min <?= $minHours ?>h to clock out)
                            <?php endif; ?>
                        </span>
                        <form action="<?= base_path() ?>/attendance/clock-out" method="POST" class="<?= !$canClockOut ? 'opacity-50 pointer-events-none' : '' ?>">
                            <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                            <button type="submit" <?= !$canClockOut ? 'disabled' : '' ?> class="px-6 py-3 rounded-xl font-medium bg-amber-500 hover:bg-amber-600 text-gray-900 shadow-lg transition-smooth">
                                Clock Out
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <span class="px-6 py-3 rounded-xl bg-gray-100 text-gray-600 font-medium">Clocked out for today</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Today's Hours</p>
            <p class="text-3xl font-bold mt-1" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= number_format($todayHours, 1) ?>h</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">This Week</p>
            <p class="text-3xl font-bold mt-1" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= number_format($weekHours, 1) ?>h</p>
        </div>
        <?php if ($ojtProgress): ?>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">OJT Progress</p>
            <p class="text-3xl font-bold mt-1" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= $ojtProgress['percent'] ?>%</p>
            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all" style="width: <?= $ojtProgress['percent'] ?>%; background: <?= $theme['accent'] ?? '#FACC15' ?>"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1"><?= $ojtProgress['completed'] ?>h / <?= $ojtProgress['required'] ?>h</p>
            <?php if ($ojtProgress['done']): ?>
                <p class="text-green-600 font-medium mt-2">✓ Completion reached!</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- OJT Progress Card (if OJT) -->
    <?php if ($ojtProgress && !$ojtProgress['done']): ?>
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">OJT Hour Tracking</h3>
        <div class="flex gap-6 flex-wrap">
            <div>
                <p class="text-sm text-gray-500">Required</p>
                <p class="text-xl font-bold"><?= $ojtProgress['required'] ?>h</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Completed</p>
                <p class="text-xl font-bold text-green-600"><?= number_format($ojtProgress['completed'], 1) ?>h</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Remaining</p>
                <p class="text-xl font-bold"><?= number_format($ojtProgress['remaining'], 1) ?>h</p>
            </div>
        </div>
        <div class="mt-4 h-4 bg-gray-200 rounded-xl overflow-hidden">
            <div class="h-full rounded-xl transition-all duration-500" style="width: <?= $ojtProgress['percent'] ?>%; background: <?= $theme['accent'] ?? '#FACC15' ?>"></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <h3 class="font-semibold text-gray-800 p-6 pb-0">Recent Attendance</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Date</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Clock In</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Clock Out</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Total Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAttendance as $r): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="py-4 px-6"><?= date('M d, Y', strtotime($r->date)) ?></td>
                        <td class="py-4 px-6"><?= $r->clock_in_time ? date('h:i A', strtotime($r->clock_in_time)) : '-' ?></td>
                        <td class="py-4 px-6"><?= $r->clock_out_time ? date('h:i A', strtotime($r->clock_out_time)) : '-' ?></td>
                        <td class="py-4 px-6 font-medium"><?= $r->total_hours ? number_format((float)$r->total_hours, 1) . 'h' : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentAttendance)): ?>
                    <tr><td colspan="4" class="py-8 px-6 text-center text-gray-500">No attendance records yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$theme = \App\Core\ThemeHelper::getTheme($user);
require __DIR__ . '/../layouts/main.php';
?>
