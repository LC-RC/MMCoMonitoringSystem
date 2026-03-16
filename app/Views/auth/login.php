<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MM&Co Certified Public Accountants</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php
    $assetBase = base_path();
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if ($docRoot !== '' && is_dir($docRoot . $assetBase . '/public/assets')) {
        $assetBase .= '/public';
    }
    ?>
    <link rel="stylesheet" href="<?= $assetBase ?>/assets/css/auth/login.css">
</head>
<body class="login-page">
    <!-- Decorative background elements -->
    <div class="login-bg-deco login-bg-deco--tl" aria-hidden="true"><span class="login-bg-deco__dot"></span></div>
    <div class="login-bg-deco login-bg-deco--br" aria-hidden="true"><span class="login-bg-deco__dot"></span></div>

    <div class="login-page__inner">
        <div class="login-card">
            <!-- Logo section: circular logo above Certified Public Accountants -->
            <div class="login-card__brand">
                <div class="login-card__logo-circle">
                    <img src="<?= rtrim($assetBase, '/') ?>/assets/images/mmco_login_logo.png" alt="MM&Co" class="login-card__logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="login-card__logo-fallback" style="display:none;" aria-hidden="true">MM&Co</span>
                </div>
                <h2 class="login-card__title">Certified Public Accountants</h2>
                <p class="login-card__tagline">Centralized employee monitoring system.</p>
            </div>
            <h1 class="login-card__welcome">Welcome</h1>

            <form id="loginForm" action="<?= base_path() ?>/login" method="POST" class="login-form" novalidate>
                <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">

                <div class="login-field">
                    <label class="login-label" for="email">Email Address</label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon" aria-hidden="true">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                        </span>
                        <input type="email" id="email" name="email" class="login-input" placeholder="Enter your email address" required autocomplete="email">
                    </div>
                    <span id="emailError" class="login-field__error hidden"></span>
                    <?php if (!empty($errorEmail)): ?>
                        <p class="login-field__error login-error-shake"><?= htmlspecialchars($errorEmail) ?></p>
                    <?php endif; ?>
                </div>

                <div class="login-field">
                    <label class="login-label" for="password">Password</label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon" aria-hidden="true">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </span>
                        <input type="password" id="password" name="password" class="login-input login-input--password" placeholder="Password" required autocomplete="current-password">
                        <button type="button" id="togglePassword" class="login-input-toggle" aria-label="Toggle password visibility">
                            <svg id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p class="login-secure"><span class="login-secure__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></span> Secure sign-in. We never share your data.</p>
                    <span id="passwordError" class="login-field__error hidden"></span>
                    <?php if (!empty($errorPassword)): ?>
                        <p class="login-field__error login-error-shake"><?= htmlspecialchars($errorPassword) ?></p>
                    <?php endif; ?>
                </div>

                <div class="login-options">
                    <div class="login-options__remember">
                        <label class="login-checkbox">
                            <input type="checkbox" name="remember" class="login-checkbox__input">
                            <span class="login-checkbox__label">Remember me</span>
                        </label>
                    </div>
                    <a href="#" class="login-link login-link--small login-options__forgot">Forgot password?</a>
                </div>

                <button type="submit" class="login-btn">LOGIN</button>

                <div class="login-divider">
                    <span class="login-divider__line"></span>
                    <span class="login-divider__text">OR</span>
                    <span class="login-divider__line"></span>
                </div>
                <button type="button" class="login-btn login-btn--google" aria-label="Continue with Google">
                    <svg class="login-google-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Continue with Google
                </button>
                <p class="login-card__prompt">Don't have an account yet? <a href="<?= base_path() ?>/register" class="login-link">Sign up</a></p>
            </form>
        </div>
        <footer class="login-footer">© <?= date('Y') ?> MM&Co. Employee Monitoring System.</footer>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        form.addEventListener('submit', function(e) {
            let isValid = true;
            emailError.classList.add('hidden');
            passwordError.classList.add('hidden');
            email.classList.remove('login-input--error');
            password.classList.remove('login-input--error');
            if (email.value.trim() === '') {
                emailError.textContent = 'Email address is required.';
                emailError.classList.remove('hidden');
                email.classList.add('login-input--error');
                isValid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
                emailError.textContent = 'Please enter a valid email address.';
                emailError.classList.remove('hidden');
                email.classList.add('login-input--error');
                isValid = false;
            }
            if (password.value.trim() === '') {
                passwordError.textContent = 'Password is required.';
                passwordError.classList.remove('hidden');
                password.classList.add('login-input--error');
                isValid = false;
            }
            if (!isValid) e.preventDefault();
        });

        function hideErrorOnChange(inputElement, errorElement) {
            inputElement.addEventListener('input', function() {
                if (!errorElement.classList.contains('hidden')) {
                    errorElement.classList.add('hidden');
                    inputElement.classList.remove('login-input--error');
                }
            });
        }
        hideErrorOnChange(email, emailError);
        hideErrorOnChange(password, passwordError);

        if (togglePassword && password && eyeIcon) {
            togglePassword.addEventListener('click', function() {
                var isPassword = password.getAttribute('type') === 'password';
                password.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.innerHTML = isPassword
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.543-7a9.97 9.97 0 012.188-3.568M6.223 6.223A9.953 9.953 0 0112 5c4.478 0 8.27 2.943 9.543 7a9.978 9.978 0 01-4.132 5.411M6.223 6.223L3 3m3.223 3.223l3.563 3.563m0 0A3 3 0 1015 12m-5.214-2.214L15 15m-5.214-5.214L15 15m0 0l3 3" />'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            });
        }
    </script>
</body>
</html>
