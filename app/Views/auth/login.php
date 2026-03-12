<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MM&Co Accounting Review Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php
    $assetBase = base_path();
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if ($docRoot !== '' && is_dir($docRoot . $assetBase . '/public/assets')) {
        $assetBase .= '/public';
    }
    $loginBgPath = $assetBase . '/assets/images/mmco_login_bg.jpg';
    $loginBgVer = '';
    if ($docRoot !== '' && is_file($docRoot . $loginBgPath)) {
        $loginBgVer = '?v=' . filemtime($docRoot . $loginBgPath);
    }
    ?>
    <link rel="stylesheet" href="<?= $assetBase ?>/assets/css/auth/login.css">
    <style>
        .login-bg {
            background-image: url('<?= $loginBgPath ?><?= $loginBgVer ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="login-page login-bg">

    <div class="login-page__inner">
        <div class="login-form-wrap">
            <h2 class="login-form-wrap__title">LOGIN</h2>

            <form id="loginForm" action="<?= base_path() ?>/login" method="POST" class="login-form" novalidate>
                <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">

                <div class="login-field">
                    <div class="login-input-wrap">
                        <span class="login-input-icon" aria-hidden="true">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                        </span>
                        <input type="email" id="email" name="email" class="login-input" placeholder="Email Address" required autocomplete="email">
                    </div>
                    <span id="emailError" class="login-field__error hidden"></span>
                    <?php if (!empty($errorEmail)): ?>
                        <p class="login-field__error login-error-shake"><?= htmlspecialchars($errorEmail) ?></p>
                    <?php endif; ?>
                </div>

                <div class="login-field">
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
                    <span id="passwordError" class="login-field__error hidden"></span>
                    <?php if (!empty($errorPassword)): ?>
                        <p class="login-field__error login-error-shake"><?= htmlspecialchars($errorPassword) ?></p>
                    <?php endif; ?>
                </div>

                <div class="login-options">
                    <label class="login-checkbox">
                        <input type="checkbox" name="remember" class="login-checkbox__input">
                        <span class="login-checkbox__label">Remember Me</span>
                    </label>
                    <a href="#" class="login-link">Forgot Password?</a>
                </div>

                <button type="submit" class="login-btn">Log In</button>

                <p class="login-footer">
                    Don't have an account? <a href="<?= base_path() ?>/register" class="login-link">Register</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        // Form Validation
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

            if (!isValid) {
                e.preventDefault();
            }
        });

        function hideErrorOnChange(inputElement, errorElement) {
            inputElement.addEventListener('input', () => {
                if (!errorElement.classList.contains('hidden')) {
                    errorElement.classList.add('hidden');
                    inputElement.classList.remove('login-input--error');
                }
            });
        }
        hideErrorOnChange(email, emailError);
        hideErrorOnChange(password, passwordError);

        if (togglePassword && password && eyeIcon) {
            togglePassword.addEventListener('click', () => {
                const isPassword = password.getAttribute('type') === 'password';
                password.setAttribute('type', isPassword ? 'text' : 'password');

                eyeIcon.innerHTML = isPassword
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.543-7a9.97 9.97 0 012.188-3.568M6.223 6.223A9.953 9.953 0 0112 5c4.478 0 8.27 2.943 9.543 7a9.978 9.978 0 01-4.132 5.411M6.223 6.223L3 3m3.223 3.223l3.563 3.563m0 0A3 3 0 1015 12m-5.214-2.214L15 15m-5.214-5.214L15 15m0 0l3 3" />'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            });
        }
    </script>
</body>
</html>
