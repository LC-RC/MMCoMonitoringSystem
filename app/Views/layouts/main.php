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
        <aside id="sidebar" class="sidebar-bg sidebar flex-shrink-0 w-64 fixed inset-y-0 left-0 flex flex-col shadow-lg z-30 transition-[width] duration-200 ease-in-out">
            <div class="sidebar-header p-4 flex items-center justify-between gap-2 min-h-[5rem]">
                <a href="<?= base_path() . ($user && $user->role === 'admin' ? '/admin/dashboard' : '/dashboard') ?>" class="flex items-center gap-2 min-w-0">
                    <div class="w-10 h-10 flex-shrink-0 rounded-xl flex items-center justify-center text-white font-bold text-lg" style="background: rgba(255,255,255,0.2);">M</div>
                    <span class="sidebar-label text-white font-semibold text-lg whitespace-nowrap overflow-hidden">MM&Co</span>
                </a>
                <button type="button" id="sidebar-toggle" class="sidebar-toggle flex-shrink-0 p-2 rounded-lg text-white/90 hover:bg-white/10 transition-smooth" aria-label="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
            <nav class="flex-1 px-4 space-y-1 sidebar-nav">
                <?php if ($user && $user->role === 'admin'): ?>
                    <a href="<?= base_path() ?>/admin/dashboard" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth" title="Dashboard">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path></svg>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden">Dashboard</span>
                    </a>
                    <a href="<?= base_path() ?>/admin/attendance" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth" title="Attendance">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden">Attendance</span>
                    </a>
                    <a href="<?= base_path() ?>/admin/payroll" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth" title="Payroll">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden">Payroll</span>
                    </a>
                    <a href="<?= base_path() ?>/admin/inventory" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth" title="Inventory">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden">Inventory</span>
                    </a>
                    <a href="<?= base_path() ?>/admin/projects" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth" title="Projects">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden">Projects</span>
                    </a>
                    <a href="<?= base_path() ?>/admin/users" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth" title="Users">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden">Users</span>
                    </a>
                <?php else: ?>
                    <a href="<?= base_path() ?>/dashboard" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-smooth" title="Dashboard">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path></svg>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden">Dashboard</span>
                    </a>
                <?php endif; ?>
            </nav>
            <!-- Sidebar footer intentionally left without logout.
                 Logout is handled via the profile dropdown in the top navbar. -->
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 min-h-screen transition-[margin-left] duration-200 ease-in-out" style="margin-left: 16rem;">
            <!-- Top Navbar -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20 shadow-sm">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
                    <?php if ($user): ?>
                        <div class="relative">
                            <button
                                type="button"
                                id="profile-menu-button"
                                class="flex items-center gap-3 rounded-full px-3 py-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/70"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <div class="text-right hidden sm:block">
                                    <p class="font-medium text-gray-800 leading-tight"><?= htmlspecialchars($user->name) ?></p>
                                    <p class="text-xs text-gray-500 leading-tight">
                                        <?= $user->role === 'admin' ? 'Admin' : \App\Models\User::getDisplayLabel($user) ?>
                                    </p>
                                </div>
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold btn-primary">
                                    <?= strtoupper(substr($user->name, 0, 1)) ?>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                id="profile-menu"
                                class="hidden absolute right-0 mt-2 w-44 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1 text-sm z-30"
                                role="menu"
                                aria-labelledby="profile-menu-button"
                            >
                                <a
                                    href="<?= base_path() ?>/profile"
                                    class="block px-3 py-2 text-gray-700 hover:bg-gray-50"
                                    role="menuitem"
                                >
                                    Edit Profile
                                </a>
                                <a
                                    href="<?= base_path() ?>/change-password"
                                    class="block px-3 py-2 text-gray-700 hover:bg-gray-50"
                                    role="menuitem"
                                >
                                    Change Password
                                </a>
                                <form action="<?= base_path() ?>/logout" method="POST">
                                    <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                                    <button
                                        type="submit"
                                        class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50"
                                        role="menuitem"
                                    >
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
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
    <script>
        (function () {
            var sidebar = document.getElementById('sidebar');
            var toggle = document.getElementById('sidebar-toggle');
            var main = document.getElementById('main-content');
            var storageKey = 'mmco-sidebar-collapsed';

            if (!sidebar || !main || !toggle) {
                return;
            }

            function setCollapsed(isCollapsed) {
                var labels = sidebar.querySelectorAll('.sidebar-label');
                var links = sidebar.querySelectorAll('.sidebar-link');

                if (isCollapsed) {
                    sidebar.style.width = '5rem';
                    main.style.marginLeft = '5rem';
                    sidebar.classList.add('sidebar-collapsed');

                    labels.forEach(function (el) {
                        el.style.display = 'none';
                    });
                    links.forEach(function (el) {
                        el.style.justifyContent = 'center';
                        el.style.paddingLeft = '1rem';
                        el.style.paddingRight = '1rem';
                    });
                } else {
                    sidebar.style.width = '16rem';
                    main.style.marginLeft = '16rem';
                    sidebar.classList.remove('sidebar-collapsed');

                    labels.forEach(function (el) {
                        el.style.display = '';
                    });
                    links.forEach(function (el) {
                        el.style.justifyContent = '';
                        el.style.paddingLeft = '';
                        el.style.paddingRight = '';
                    });
                }

                localStorage.setItem(storageKey, isCollapsed ? '1' : '0');
            }

            var initiallyCollapsed = localStorage.getItem(storageKey) === '1';
            setCollapsed(initiallyCollapsed);

            toggle.addEventListener('click', function () {
                var nextState = !(localStorage.getItem(storageKey) === '1');
                setCollapsed(nextState);
            });
        })();

        (function () {
            var button = document.getElementById('profile-menu-button');
            var menu = document.getElementById('profile-menu');
            if (!button || !menu) return;

            function closeMenu() {
                menu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
            }

            function openMenu() {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
            }

            function toggleMenu() {
                var isOpen = !menu.classList.contains('hidden');
                if (isOpen) closeMenu(); else openMenu();
            }

            button.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu();
            });

            document.addEventListener('click', function (e) {
                if (!menu.classList.contains('hidden')) {
                    if (!menu.contains(e.target) && !button.contains(e.target)) {
                        closeMenu();
                    }
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMenu();
                }
            });
        })();
    </script>
</body>
</html>
