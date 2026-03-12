<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'MM&Co') ?> | MM&Co Accounting Review Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '<?= $theme['primary'] ?? '#1E3A8A' ?>', light: '<?= $theme['primary_light'] ?? '#3B82F6' ?>' },
                        accent: { DEFAULT: '<?= $theme['accent'] ?? '#FACC15' ?>', light: '<?= $theme['accent_light'] ?? '#FDE68A' ?>' }
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_path() ?>/public/assets/css/layout.css">
    <style>
        .sidebar-bg { background-color: <?= $theme['sidebar_bg'] ?? '#1E3A8A' ?>; }
        .btn-primary { background-color: <?= $theme['primary'] ?? '#1E3A8A' ?>; }
        .btn-primary:hover { filter: brightness(1.1); }
        .badge-accent { background-color: <?= $theme['accent'] ?? '#FACC15' ?>; color: #1f2937; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen transition-smooth">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="sidebar-bg w-64 fixed inset-y-0 left-0 flex flex-col shadow-lg z-30">
            <div class="p-6">
                <a href="<?= base_path() . ($user && $user->role === 'admin' ? '/admin/dashboard' : '/dashboard') ?>" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-lg" style="background: rgba(255,255,255,0.2);">M</div>
                    <span class="text-white font-semibold text-lg">MM&Co</span>
                </a>
            </div>
            <nav class="flex-1 px-4 space-y-1">
                <?php if ($user && $user->role === 'admin'): ?>
                    <a href="<?= base_path() ?>/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path></svg>
                        Dashboard
                    </a>
                    <a href="<?= base_path() ?>/admin/attendance" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Attendance
                    </a>
                    <a href="<?= base_path() ?>/admin/payroll" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Payroll
                    </a>
                    <a href="<?= base_path() ?>/admin/inventory" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Inventory
                    </a>
                    <a href="<?= base_path() ?>/admin/projects" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Projects
                    </a>
                    <a href="<?= base_path() ?>/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Users
                    </a>
                <?php else: ?>
                    <a href="<?= base_path() ?>/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path></svg>
                        Dashboard
                    </a>
                <?php endif; ?>
            </nav>
            <div class="p-4 border-t border-white/20">
                <form action="<?= base_path() ?>/logout" method="POST" class="w-full">
                    <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth w-full text-left">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 min-h-screen">
            <!-- Top Navbar -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20 shadow-sm">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
                    <div class="flex items-center gap-4">
                        <?php if ($user): ?>
                            <div class="text-right">
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($user->name) ?></p>
                                <p class="text-sm text-gray-500">
                                    <?= $user->role === 'admin' ? 'Admin' : \App\Models\User::getDisplayLabel($user) ?>
                                </p>
                            </div>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold btn-primary">
                                <?= strtoupper(substr($user->name, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="p-8">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center justify-between">
                        <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">×</button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center justify-between">
                        <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                        <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">×</button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
</body>
</html>
