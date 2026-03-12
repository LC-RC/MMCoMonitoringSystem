<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MM&Co Accounting Review Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php
    $assetBase = base_path();
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if ($docRoot !== '' && is_dir($docRoot . $assetBase . '/public/assets')) {
        $assetBase .= '/public';
    }

    $regBgPath = $assetBase . '/assets/images/reg_bg.jpeg';
    $regBgVer = '';
    if ($docRoot !== '') {
        $regBgCandidates = [
            $docRoot . $assetBase . '/assets/images/reg_bg_svg.svg' => $assetBase . '/assets/images/reg_bg_svg.svg',
            $docRoot . $assetBase . '/assets/images/reg_bg.svg' => $assetBase . '/assets/images/reg_bg.svg',
            $docRoot . $assetBase . '/assets/images/reg_bg.jpg' => $assetBase . '/assets/images/reg_bg.jpg',
            $docRoot . $assetBase . '/assets/images/reg_bg.jpeg' => $assetBase . '/assets/images/reg_bg.jpeg',
        ];

        foreach ($regBgCandidates as $fs => $url) {
            if (is_file($fs)) {
                $regBgPath = $url;
                $regBgVer = (string) filemtime($fs);
                break;
            }
        }
    }
    ?>
    <link rel="stylesheet" href="<?= $assetBase ?>/assets/css/auth/register.css">
</head>
<?php
$errors = $errors ?? [];
$old = $old ?? [];
$activeStep = (int) ($activeStep ?? 1);
$departments = $departments ?? ['IT', 'Accounting', 'HR'];
$ojtKinds = $ojtKinds ?? ['IT OJT', 'Accounting OJT', 'HR OJT'];

$mmcoEmailRaw = (string)($old['mmco_email'] ?? '');
$mmcoUsernameRaw = $mmcoEmailRaw;
$mmcoDomainRaw = '';
if ($mmcoEmailRaw !== '') {
    if (str_ends_with($mmcoEmailRaw, '.mmco.ojt@gmail.com')) {
        $mmcoDomainRaw = '.mmco.ojt@gmail.com';
        $mmcoUsernameRaw = substr($mmcoEmailRaw, 0, -strlen($mmcoDomainRaw));
    } elseif (str_ends_with($mmcoEmailRaw, '.mmco@gmail.com')) {
        $mmcoDomainRaw = '.mmco@gmail.com';
        $mmcoUsernameRaw = substr($mmcoEmailRaw, 0, -strlen($mmcoDomainRaw));
    } elseif (str_contains($mmcoEmailRaw, '@')) {
        $parts = explode('@', $mmcoEmailRaw, 2);
        $mmcoUsernameRaw = $parts[0];
        $mmcoDomainRaw = '@' . ($parts[1] ?? '');
    }
}

$personalEmailRaw = (string)($old['personal_email'] ?? '');
$personalUsernameRaw = $personalEmailRaw;
$personalDomainRaw = '';
if ($personalEmailRaw !== '') {
    if (str_ends_with($personalEmailRaw, '@gmail.com')) {
        $personalDomainRaw = '@gmail.com';
        $personalUsernameRaw = substr($personalEmailRaw, 0, -strlen($personalDomainRaw));
    } elseif (str_contains($personalEmailRaw, '@')) {
        $parts = explode('@', $personalEmailRaw, 2);
        $personalUsernameRaw = $parts[0];
        $personalDomainRaw = '@' . ($parts[1] ?? '');
    }
}

function oldv(array $old, string $key, string $default = ''): string {
    return htmlspecialchars((string)($old[$key] ?? $default));
}

function err(array $errors, string $key): string {
    return htmlspecialchars((string)($errors[$key] ?? ''));
}

$birthdayMin = date('Y-m-d', strtotime('-100 years'));
$birthdayMax = date('Y-m-d', strtotime('-19 years'));
?>
<body class="reg-page">

    <div class="reg-page__inner">
        <div class="reg-form-card">
            <header class="reg-brand reg-brand--onboard">
                <div class="reg-brand__left">
                    <div class="reg-brand__mark">
                        <svg class="reg-brand__logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <span class="reg-brand__name">MM&Co</span>
                </div>
                <div class="reg-brand__right">
                    <span class="reg-brand__greeting">Hi, Guest</span>
                    <span class="reg-brand__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                </div>
            </header>
            <div class="reg-welcome">
                <h1 class="reg-welcome__title">Welcome to MM&Co</h1>
                <p class="reg-welcome__subtitle">Complete the steps to finish your registration</p>
            </div>

            <div class="reg-form-card__body reg-onboard">
                <aside class="reg-onboard__aside" aria-label="Registration progress">
                    <nav class="reg-stepper" id="regStepper" aria-label="Steps">
                        <div class="reg-stepper__item" data-step="1"><span class="reg-stepper__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="reg-stepper__label">Personal Information</span></div>
                        <div class="reg-stepper__item" data-step="2"><span class="reg-stepper__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><span class="reg-stepper__label">Work &amp; Department</span></div>
                        <div class="reg-stepper__item" data-step="3"><span class="reg-stepper__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span><span class="reg-stepper__label">Account Credentials</span></div>
                        <div class="reg-stepper__item" data-step="4"><span class="reg-stepper__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span><span class="reg-stepper__label">Terms and Conditions</span></div>
                        <div class="reg-stepper__item" data-step="5"><span class="reg-stepper__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span><span class="reg-stepper__label">Privacy Policy</span></div>
                    </nav>
                </aside>
                <div class="reg-onboard__main">
                    <div class="reg-step-progress">
                        <div class="reg-step-progress__bar"><div class="reg-step-progress__fill" id="regProgressFill" style="width: 17%;"></div></div>
                        <span class="reg-step-progress__pct" id="regProgressPct">17%</span>
                    </div>
                    <h2 class="reg-step-title" id="regStepTitle">Personal Information</h2>

                <?php if (!empty($errorGeneral)): ?>
                    <div class="reg-alert--error">
                        <?= htmlspecialchars($errorGeneral) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="reg-success-overlay">
                        <div class="reg-success-card">
                            <div class="reg-success-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-[#0f172a]">Account Successfully Created</h3>
                            <p class="text-sm text-[#475569] mt-2 leading-relaxed">
                                Your account has been successfully created.
                                Please wait for approval from the Human Resources Department before you can access the system.
                            </p>
                            <p class="text-sm text-[#475569] mt-2 leading-relaxed">
                                You will be granted access once your account request has been reviewed and approved.
                            </p>
                            <a href="<?= base_path() ?>/login" class="reg-btn reg-btn--primary">
                                Go Back to Login
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="registerForm" action="<?= base_path() ?>/register" method="POST" enctype="multipart/form-data" class="reg-onboard-form" data-base-path="<?= htmlspecialchars(rtrim(base_path(), '/')) ?>">
                    <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                    <input type="hidden" id="active_step" name="active_step" value="1">
                    <input type="hidden" name="user_type" id="user_type" value="<?= oldv($old,'user_type') ?>">

                    <div class="reg-step-panels">
                        <div class="reg-step-panel" id="stepPanel1" data-step="1">
                        <div id="profileBlock" class="reg-profile-block">
                            <div class="reg-profile-avatar-wrap">
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden" form="registerForm">
                                <img id="profilePreview" alt="Profile" class="reg-profile-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Crect width='256' height='256' fill='%23F3F4F6'/%3E%3Crect x='60' y='52' width='136' height='136' rx='28' fill='%23CBD5E1'/%3E%3Cpath d='M88 178c18-30 40-45 60-45s42 15 60 45' fill='%23B6C2D1'/%3E%3Ccircle cx='128' cy='112' r='22' fill='%23B6C2D1'/%3E%3C/svg%3E">
                                <button type="button" id="btnPickPhoto" class="reg-profile-btn" aria-label="Upload photo">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                        <path d="M12 9a3 3 0 100 6 3 3 0 000-6z"/>
                                        <path fill-rule="evenodd" d="M9.828 3a2 2 0 00-1.414.586L6.586 5.414A2 2 0 015.172 6H4a2 2 0 00-2 2v11a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2h-1.172a2 2 0 01-1.414-.586l-1.828-1.828A2 2 0 0014.172 3H9.828zM12 17a5 5 0 100-10 5 5 0 000 10z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                            <?php if (err($errors,'profile_photo') !== ''): ?><p class="reg-field__error"><?= err($errors,'profile_photo') ?></p><?php endif; ?>
                        </div>
                        <section class="reg-section">
                            <h2 class="reg-section__title">Personal Information</h2>
                            <p class="reg-section__hint">Fields marked <span class="required">*</span> are required</p>
                            <div class="reg-section__content">
                                <div class="reg-form-grid reg-form-grid--3">
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="first_name">First Name <span class="required">*</span></label>
                                        <input type="text" name="first_name" id="first_name" value="<?= oldv($old,'first_name') ?>" required placeholder="Enter your given name" maxlength="30" class="reg-input" autocomplete="given-name">
                                        <p id="first_name_client_error" class="reg-field__error hidden"></p>
                                        <?php if (err($errors,'first_name') !== ''): ?><p class="reg-field__error"><?= err($errors,'first_name') ?></p><?php endif; ?>
                                    </div>
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" value="<?= oldv($old,'middle_name') ?>" placeholder="Optional" maxlength="30" class="reg-input" autocomplete="additional-name">
                                        <p id="middle_name_client_error" class="reg-field__error hidden"></p>
                                        <?php if (err($errors,'middle_name') !== ''): ?><p class="reg-field__error"><?= err($errors,'middle_name') ?></p><?php endif; ?>
                                    </div>
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="last_name">Last Name <span class="required">*</span></label>
                                        <input type="text" name="last_name" id="last_name" value="<?= oldv($old,'last_name') ?>" required placeholder="Enter your family name" maxlength="30" class="reg-input" autocomplete="family-name">
                                        <p id="last_name_client_error" class="reg-field__error hidden"></p>
                                        <?php if (err($errors,'last_name') !== ''): ?><p class="reg-field__error"><?= err($errors,'last_name') ?></p><?php endif; ?>
                                    </div>
                                </div>
                                <div class="reg-form-grid reg-form-grid--3">
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="birthday">Birthday <span class="required">*</span></label>
                                        <input type="date" name="birthday" id="birthday" value="<?= oldv($old,'birthday') ?>" required min="<?= $birthdayMin ?>" max="<?= $birthdayMax ?>" class="reg-input" autocomplete="bday" title="Age must be between 19 and 100">
                                        <p id="birthday_client_error" class="reg-field__error hidden"></p>
                                        <?php if (err($errors,'birthday') !== ''): ?><p class="reg-field__error"><?= err($errors,'birthday') ?></p><?php endif; ?>
                                    </div>
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="gender">Gender <span class="required">*</span></label>
                                        <select name="gender" id="gender" required class="reg-select" data-hint-select>
                                            <option value="" disabled selected hidden>Select</option>
                                            <option value="Female" <?= (oldv($old,'gender') === 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Male" <?= (oldv($old,'gender') === 'Male') ? 'selected' : '' ?>>Male</option>
                                        </select>
                                        <?php if (err($errors,'gender') !== ''): ?><p class="reg-field__error"><?= err($errors,'gender') ?></p><?php endif; ?>
                                    </div>
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="civil_status">Civil Status <span class="required">*</span></label>
                                        <select name="civil_status" id="civil_status" required class="reg-select" data-hint-select>
                                            <option value="" disabled selected hidden>Select</option>
                                            <option value="Single" <?= (oldv($old,'civil_status') === 'Single') ? 'selected' : '' ?>>Single</option>
                                            <option value="Married" <?= (oldv($old,'civil_status') === 'Married') ? 'selected' : '' ?>>Married</option>
                                            <option value="Widowed" <?= (oldv($old,'civil_status') === 'Widowed') ? 'selected' : '' ?>>Widowed</option>
                                        </select>
                                        <?php if (err($errors,'civil_status') !== ''): ?><p class="reg-field__error"><?= err($errors,'civil_status') ?></p><?php endif; ?>
                                    </div>
                                </div>
                                <div class="reg-field">
                                    <label class="reg-field__label" for="contact_number">Contact Number <span class="required">*</span></label>
                                    <input type="text" name="contact_number" id="contact_number" value="<?= oldv($old,'contact_number') ?>" required placeholder="Contact Number" maxlength="11" inputmode="numeric" class="reg-input" autocomplete="tel">
                                    <p id="contact_number_client_error" class="reg-field__error hidden"></p>
                                    <?php if (err($errors,'contact_number') !== ''): ?><p class="reg-field__error"><?= err($errors,'contact_number') ?></p><?php endif; ?>
                                </div>
                                <div class="reg-field">
                                    <label class="reg-field__label" for="address">Address <span class="required">*</span></label>
                                    <input type="text" name="address" id="address" value="<?= htmlspecialchars(oldv($old,'address') ?? '') ?>" required placeholder="Street, city, province" maxlength="500" class="reg-input" autocomplete="street-address">
                                    <p id="address_client_error" class="reg-field__error hidden"></p>
                                    <?php if (err($errors,'address') !== ''): ?><p class="reg-field__error"><?= err($errors,'address') ?></p><?php endif; ?>
                                </div>
                                <div class="reg-field">
                                    <label class="reg-field__label" for="personal_email_username">Personal Email Address <span class="required">*</span></label>
                                    <div class="reg-input-group">
                                        <input type="text" name="personal_email_username" id="personal_email_username" value="<?= htmlspecialchars($personalUsernameRaw) ?>" required placeholder="Enter your personal email username (at least 8 characters)" minlength="8" maxlength="60" class="reg-input--left" autocomplete="email" inputmode="email">
                                        <span id="personal_email_domain" class="reg-input-suffix"><?= htmlspecialchars($personalDomainRaw !== '' ? $personalDomainRaw : '@gmail.com') ?></span>
                                    </div>
                                    <input type="hidden" name="personal_email" id="personal_email" value="<?= oldv($old,'personal_email') ?>">
                                    <p id="personal_email_client_error" class="reg-field__error hidden"></p>
                                    <?php if (err($errors,'personal_email') !== ''): ?><p class="reg-field__error"><?= err($errors,'personal_email') ?></p><?php endif; ?>
                                </div>
                            </div>
                        </section>
                        </div>

                        <div class="reg-step-panel reg-step-panel--hidden" id="stepPanel2" data-step="2">
                        <section class="reg-section">
                            <h2 class="reg-section__title">Work / Department Information</h2>
                            <div class="reg-section__content reg-section__content--step2">
                                <div class="reg-role-block">
                                    <span class="reg-field__label">Role <span class="required">*</span></span>
                                    <div class="reg-role-group">
                                        <label class="reg-role-option">
                                            <input type="radio" name="role_choice" id="role_employee" value="employee" <?= (oldv($old,'user_type') === 'employee') ? 'checked' : '' ?> required>
                                            <span>Employee</span>
                                        </label>
                                        <label class="reg-role-option">
                                            <input type="radio" name="role_choice" id="role_intern" value="ojt" <?= (oldv($old,'user_type') === 'ojt') ? 'checked' : '' ?> required>
                                            <span>Intern</span>
                                        </label>
                                    </div>
                                    <?php if (err($errors,'user_type') !== ''): ?><p class="reg-field__error"><?= err($errors,'user_type') ?></p><?php endif; ?>
                                </div>

                                <div id="employeeBlock" class="hidden">
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="employeeDepartment">Department <span class="required">*</span></label>
                                        <select name="department" id="employeeDepartment" data-hint-select class="reg-select">
                                            <option value="" disabled selected hidden>Select</option>
                                            <?php foreach ($departments as $d): ?>
                                                <option value="<?= htmlspecialchars($d) ?>" <?= (oldv($old,'department') === $d) ? 'selected' : '' ?>><?= htmlspecialchars($d) ?></option>
                                            <?php endforeach; ?>
                                            <option value="Other" <?= (oldv($old,'department') === 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                        <?php if (err($errors,'department') !== ''): ?><p class="reg-field__error"><?= err($errors,'department') ?></p><?php endif; ?>
                                    </div>
                                    <div id="departmentOtherWrap" class="hidden reg-field">
                                        <label class="reg-field__label" for="department_other">Enter Department <span class="required">*</span></label>
                                        <input type="text" name="department_other" id="department_other" value="<?= oldv($old,'department_other') ?>" placeholder="Type your department name" class="reg-input">
                                        <?php if (err($errors,'department_other') !== ''): ?><p class="reg-field__error"><?= err($errors,'department_other') ?></p><?php endif; ?>
                                    </div>
                                </div>

                                <div class="reg-field reg-field--employee-id">
                                    <label class="reg-field__label" for="employee_id_display">Employee ID</label>
                                    <input type="text" id="employee_id_display" class="reg-input reg-input--readonly" value="<?= htmlspecialchars(oldv($old,'employee_id') ?? '') ?>" readonly aria-readonly="true" placeholder="Select Role and Department">
                                    <input type="hidden" name="employee_id" id="employee_id" value="<?= oldv($old,'employee_id') ?>">
                                    <?php if (err($errors,'employee_id') !== ''): ?><p class="reg-field__error"><?= err($errors,'employee_id') ?></p><?php endif; ?>
                                </div>

                                <div id="internDetails" class="hidden reg-stack-4">
                                        <div class="reg-form-grid reg-form-grid--2">
                                            <div class="reg-field">
                                                <label class="reg-field__label" for="school_name">School Name <span class="required">*</span></label>
                                                <input type="text" name="school_name" id="school_name" value="<?= oldv($old,'school_name') ?>" placeholder="School / university name" maxlength="50" class="reg-input">
                                                <p id="school_name_client_error" class="reg-field__error hidden"></p>
                                                <?php if (err($errors,'school_name') !== ''): ?><p class="reg-field__error"><?= err($errors,'school_name') ?></p><?php endif; ?>
                                            </div>
                                            <div class="reg-field">
                                                <label class="reg-field__label" for="hours_option">Hours Needed to Complete <span class="required">*</span></label>
                                                <select id="hours_option" class="reg-select" aria-label="Select required hours">
                                                    <option value="" disabled selected hidden>Select</option>
                                                    <option value="300" <?= (oldv($old,'hours_needed') === '300') ? 'selected' : '' ?>>300</option>
                                                    <option value="400" <?= (oldv($old,'hours_needed') === '400') ? 'selected' : '' ?>>400</option>
                                                    <option value="500" <?= (oldv($old,'hours_needed') === '500') ? 'selected' : '' ?>>500</option>
                                                    <option value="Custom" <?= (oldv($old,'hours_needed') !== '' && !in_array(oldv($old,'hours_needed'), ['300','400','500'], true)) ? 'selected' : '' ?>>Custom</option>
                                                </select>
                                                <div id="hours_custom_wrap" class="reg-field hidden" style="margin-top: 0.75rem;">
                                                    <label class="reg-field__label" for="hours_custom">Enter hours</label>
                                                    <input type="text" id="hours_custom" inputmode="numeric" maxlength="4" placeholder="e.g. 350" class="reg-input" autocomplete="off">
                                                </div>
                                                <input type="hidden" name="hours_needed" id="hours_needed" value="<?= oldv($old,'hours_needed') ?>">
                                                <?php if (err($errors,'hours_needed') !== ''): ?><p class="reg-field__error"><?= err($errors,'hours_needed') ?></p><?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="reg-form-grid reg-form-grid--2">
                                            <div class="reg-field">
                                                <label class="reg-field__label" for="start_date">Start Date <span class="required">*</span></label>
                                                <input type="date" name="start_date" id="start_date" value="<?= oldv($old,'start_date') ?>" required class="reg-input">
                                                <?php if (err($errors,'start_date') !== ''): ?><p class="reg-field__error"><?= err($errors,'start_date') ?></p><?php endif; ?>
                                            </div>
                                            <div class="reg-field">
                                                <label class="reg-field__label" for="end_date">Estimated End Date</label>
                                                <input type="date" name="end_date" id="end_date" value="<?= oldv($old,'end_date') ?>" class="reg-input" readonly aria-readonly="true">
                                                <p class="reg-field__hint">Computed from start date and required hours (excluding weekends).</p>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </section>
                        </div>

                        <div class="reg-step-panel reg-step-panel--hidden" id="stepPanel3" data-step="3">
                        <section class="reg-section">
                            <h2 class="reg-section__title">Account Credentials</h2>
                            <div class="reg-section__content">
                                <div class="reg-field reg-field--spaced">
                                    <label class="reg-field__label" for="mmco_email_username">MMCO Email Address <span class="required">*</span></label>
                                    <div class="reg-input-group">
                                        <input type="text" name="mmco_email_username" id="mmco_email_username" value="<?= htmlspecialchars($mmcoUsernameRaw) ?>" required placeholder="Enter your MMCO username" maxlength="46" class="reg-input--left" autocomplete="username" inputmode="email">
                                        <span id="mmco_email_domain" class="reg-input-suffix"><?= htmlspecialchars($mmcoDomainRaw !== '' ? $mmcoDomainRaw : '.mmco@gmail.com') ?></span>
                                    </div>
                                    <input type="hidden" name="mmco_email" id="mmco_email" value="<?= oldv($old,'mmco_email') ?>">
                                    <?php if (err($errors,'mmco_email') !== ''): ?><p class="reg-field__error"><?= err($errors,'mmco_email') ?></p><?php endif; ?>
                                </div>

                                <div class="reg-form-grid reg-form-grid--2">
                                    <div class="reg-field">
                                        <label class="reg-field__label" for="password">Password <span class="required">*</span></label>
                                        <div class="reg-input-wrap">
                                            <input type="password" name="password" id="password" required placeholder="Create a strong password" maxlength="25" class="reg-input" autocomplete="new-password">
                                            <button type="button" id="togglePassword" class="eye-btn" aria-label="Toggle password">
                                                <svg id="eyeIconPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-[18px] w-[18px]">
                                                    <path d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                                    <path d="M12 15.75A3.75 3.75 0 1 0 12 8.25a3.75 3.75 0 0 0 0 7.5z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <?php if (err($errors,'password') !== ''): ?><p class="reg-field__error"><?= err($errors,'password') ?></p><?php endif; ?>

                                        <div id="passwordHelp" class="reg-password-help hidden">
                                            <div class="text-xs font-semibold text-[#334155]">Password rules</div>
                                            <div class="reg-password-rules space-y-1 text-xs">
                                                <div id="ruleLen" class="text-[#64748b]">Minimum 8 characters</div>
                                                <div id="ruleNum" class="text-[#64748b]">At least 1 number</div>
                                                <div id="ruleUpper" class="text-[#64748b]">At least 1 uppercase letter</div>
                                                <div id="ruleSpecial" class="text-[#64748b]">At least 1 special character</div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="flex items-center justify-between text-xs text-[#64748b]">
                                                    <span>Password strength</span>
                                                    <span id="strengthLabel" class="font-semibold text-[#334155]">Weak</span>
                                                </div>
                                                <div class="reg-strength-bar mt-1">
                                                    <div id="strengthBar" class="reg-strength-fill w-[10%] bg-red-500"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="reg-field">
                                        <label class="reg-field__label" for="confirm_password">Confirm Password <span class="required">*</span></label>
                                        <div class="reg-input-wrap">
                                            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Re-type to confirm" maxlength="25" class="reg-input" autocomplete="new-password">
                                            <button type="button" id="toggleConfirmPassword" class="eye-btn" aria-label="Toggle confirm password">
                                                <svg id="eyeIconConfirm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-[18px] w-[18px]">
                                                    <path d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                                    <path d="M12 15.75A3.75 3.75 0 1 0 12 8.25a3.75 3.75 0 0 0 0 7.5z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <?php if (err($errors,'confirm_password') !== ''): ?><p class="reg-field__error"><?= err($errors,'confirm_password') ?></p><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </section>
                        </div>

                        <div class="reg-step-panel reg-step-panel--hidden" id="stepPanel4" data-step="4">
                            <div class="reg-policy-step">
                                <div class="reg-policy-step__scroll">
                                    <p>Placeholder: The Terms and Conditions for MM&Co will be displayed here. This section will outline the rules, responsibilities, acceptable use policies, and legal agreements required for using the MM&Co system.</p>
                                </div>
                                <div class="reg-policy-step__actions">
                                    <div class="reg-policy-step__agree">
                                        <label class="reg-policy-step__checkbox-wrap">
                                            <input type="checkbox" name="terms" id="terms" required>
                                            <span>I agree to the Terms and Conditions <span class="required">*</span></span>
                                        </label>
                                        <?php if (err($errors,'terms') !== ''): ?><p class="reg-field__error"><?= err($errors,'terms') ?></p><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reg-step-panel reg-step-panel--hidden" id="stepPanel5" data-step="5">
                            <div class="reg-policy-step">
                                <div class="reg-policy-step__scroll">
                                    <p>Placeholder: The MM&Co Privacy Policy will be displayed here. This section explains how user data is collected, processed, stored, and protected in accordance with company policies and applicable data protection regulations.</p>
                                </div>
                                <div class="reg-policy-step__actions">
                                    <div class="reg-policy-step__agree">
                                        <label class="reg-policy-step__checkbox-wrap">
                                            <input type="checkbox" name="privacy_agreed" id="privacy_agreed" required>
                                            <span>I agree to the Privacy Policy <span class="required">*</span></span>
                                        </label>
                                        <?php if (err($errors,'privacy_agreed') !== ''): ?><p class="reg-field__error"><?= err($errors,'privacy_agreed') ?></p><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="reg-step-nav">
                        <div class="reg-step-nav__left">
                            <a href="<?= base_path() ?>/login" id="regBackToLogin" class="reg-step-btn reg-step-btn--circle" aria-label="Back to Login" title="Back to Login">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                            <button type="button" id="regPrevStep" class="reg-step-btn reg-step-btn--circle hidden" aria-label="Previous step">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                        </div>
                        <div class="reg-step-nav__center">
                            <button type="button" id="regNextStep" class="reg-btn reg-btn--primary reg-step-nav-next">Next</button>
                            <button type="submit" id="btnSubmit" class="reg-btn reg-btn--primary reg-step-nav-submit hidden">Create Account</button>
                        </div>
                        <div class="reg-step-nav__right">
                            <button type="button" id="regNextStepCircle" class="reg-step-btn reg-step-btn--circle hidden" aria-label="Next step">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </form>

                </div>
            </div>
        </div>
    </div>

    <div id="modalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 reg-modal-overlay"></div>
        <div class="relative h-full w-full flex items-center justify-center p-4">
            <div id="termsModal" class="hidden reg-modal-dialog fade-in" role="dialog" aria-modal="true" aria-labelledby="termsTitle">
                <div class="p-6 sm:p-7">
                    <h3 id="termsTitle" class="text-xl sm:text-2xl font-bold text-[#0f172a]">Terms and Conditions</h3>
                    <div id="termsContent" class="modal-scroll">
                        <p>This Terms and Conditions agreement governs your access to and use of the MM&Co Monitoring System.</p>
                        <p class="mt-3">By creating an account, you acknowledge that you will use the system responsibly, provide accurate information, and comply with applicable company policies.</p>
                        <p class="mt-3">You agree not to attempt unauthorized access, disrupt system availability, or misuse any data that you are permitted to view.</p>
                        <p class="mt-3">The company may update these terms from time to time. Continued use of the system after updates constitutes acceptance of the revised terms.</p>
                        <p class="mt-3">If you do not agree with these Terms and Conditions, you must not proceed with registration.</p>
                    </div>
                    <div class="reg-modal-actions">
                        <button type="button" id="termsDisagree" class="reg-btn reg-btn--secondary">Disagree</button>
                        <button type="button" id="termsAgree" disabled class="reg-btn reg-btn--primary">Agree</button>
                    </div>
                    <div class="reg-modal-hint">Scroll to the bottom to enable Agree.</div>
                </div>
            </div>

            <div id="privacyModal" class="hidden reg-modal-dialog fade-in" role="dialog" aria-modal="true" aria-labelledby="privacyTitle">
                <div class="p-6 sm:p-7">
                    <h3 id="privacyTitle" class="text-xl sm:text-2xl font-bold text-[#0f172a]">Privacy Policy</h3>
                    <div id="privacyContent" class="modal-scroll">
                        <p>This Privacy Policy explains how your personal information is collected, used, and protected within the MM&Co Monitoring System.</p>
                        <p class="mt-3">Information you provide during registration may be used for account verification, access control, and internal administrative purposes.</p>
                        <p class="mt-3">Access to your data is limited to authorized personnel. Reasonable safeguards are applied to protect the confidentiality and integrity of stored information.</p>
                        <p class="mt-3">By proceeding, you consent to the processing of your information for legitimate business purposes related to system operation.</p>
                        <p class="mt-3">If you do not agree with this Privacy Policy, you must not proceed with registration.</p>
                    </div>
                    <div class="reg-modal-actions">
                        <button type="button" id="privacyDisagree" class="reg-btn reg-btn--secondary">Disagree</button>
                        <button type="button" id="privacyAgree" disabled class="reg-btn reg-btn--primary">Agree</button>
                    </div>
                    <div class="reg-modal-hint">Scroll to the bottom to enable Agree.</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');

        (function initOnboardSteps() {
            const TOTAL_STEPS = 5;
            const stepTitles = ['Personal Information', 'Work & Department', 'Account Credentials', 'Terms and Conditions', 'Privacy Policy'];
            let currentStep = 1;

            const progressFill = document.getElementById('regProgressFill');
            const progressPct = document.getElementById('regProgressPct');
            const stepTitleEl = document.getElementById('regStepTitle');
            const stepper = document.getElementById('regStepper');
            const backToLogin = document.getElementById('regBackToLogin');
            const prevBtn = document.getElementById('regPrevStep');
            const nextBtn = document.querySelector('.reg-step-nav-next');
            const nextCircle = document.getElementById('regNextStepCircle');
            const submitBtn = document.getElementById('btnSubmit');
            const submitBtnWrap = document.querySelector('.reg-step-nav-submit');
            const activeStepInput = document.getElementById('active_step');
            const termsCheckbox = document.getElementById('terms');
            const privacyCheckbox = document.getElementById('privacy_agreed');

            function getPanel(step) { return document.getElementById('stepPanel' + step); }
            function getStepperItem(step) { return stepper && stepper.querySelector('[data-step="' + step + '"]'); }

            function trimVal(el) { return (el && el.value) ? el.value.trim() : ''; }
            function isStepValid(step) {
                if (step === 1) {
                    var fn = document.getElementById('first_name');
                    var ln = document.getElementById('last_name');
                    var mn = document.getElementById('middle_name');
                    var pe = document.getElementById('personal_email_username');
                    var peHidden = document.getElementById('personal_email');
                    var bd = document.getElementById('birthday');
                    var addr = document.getElementById('address');
                    var cn = document.getElementById('contact_number');
                    var gen = document.getElementById('gender');
                    var civ = document.getElementById('civil_status');
                    if (!trimVal(fn).length || trimVal(fn).length < 3) return false;
                    if (!trimVal(ln).length || trimVal(ln).length < 3) return false;
                    if (trimVal(mn).length > 0 && trimVal(mn).length < 3) return false;
                    var peUser = trimVal(pe);
                    if (!peUser.length || !peHidden || !trimVal(peHidden).length || peUser.length < 8) return false;
                    if (!trimVal(bd).length || !trimVal(addr).length || trimVal(addr).length < 15 || !trimVal(cn).length) return false;
                    if (!gen || !gen.value || !civ || !civ.value) return false;
                    var birth = new Date(bd.value);
                    var today = new Date();
                    today.setHours(0,0,0,0);
                    birth.setHours(0,0,0,0);
                    var age = Math.floor((today - birth) / (365.25 * 24 * 60 * 60 * 1000));
                    return age >= 19 && age <= 100;
                }
                if (step === 2) {
                    var re = document.getElementById('role_employee');
                    var ri = document.getElementById('role_intern');
                    var dept = document.getElementById('employeeDepartment');
                    var deptOther = document.getElementById('department_other');
                    var empId = document.getElementById('employee_id');
                    if (typeof window.syncHoursHidden === 'function') window.syncHoursHidden();

                    // Require radios to exist and at least one role selected
                    if (!re || !ri) return false;
                    if (!re.checked && !ri.checked) return false;

                    // Require Employee ID and Department to be present
                    if (!empId || !trimVal(empId.value).length) return false;
                    if (!dept || !dept.value) return false;
                    if (dept.value === 'Other' && !trimVal(deptOther).length) return false;

                    // Detailed intern requirements (school, hours, etc.) are enforced
                    // via [required] attributes and validateForm()/server-side checks
                    // when submitting the final step, so we don't block step navigation here.
                    return true;
                }
                if (step === 3) {
                    var mmco = document.getElementById('mmco_email_username');
                    var pw = document.getElementById('password');
                    var cpw = document.getElementById('confirm_password');
                    return trimVal(mmco).length > 0 && trimVal(pw).length > 0 && trimVal(cpw).length > 0;
                }
                if (step === 4) return termsCheckbox && termsCheckbox.checked;
                if (step === 5) return termsCheckbox && termsCheckbox.checked && privacyCheckbox && privacyCheckbox.checked;
                return true;
            }

            function canProceedFromCurrentStep() {
                return isStepValid(currentStep);
            }

            function updateNextAndSubmitState() {
                var nextEnabled = currentStep < TOTAL_STEPS && isStepValid(currentStep);
                if (nextBtn) {
                    nextBtn.disabled = !nextEnabled;
                }
                var submitEnabled = currentStep === TOTAL_STEPS && isStepValid(5);
                if (submitBtn) {
                    submitBtn.disabled = !submitEnabled;
                }
            }

            function updateStepUI() {
                var pct = Math.round((currentStep / TOTAL_STEPS) * 100);
                if (progressFill) progressFill.style.width = pct + '%';
                if (progressPct) progressPct.textContent = pct + '%';
                if (stepTitleEl) stepTitleEl.textContent = stepTitles[currentStep - 1] || '';
                if (activeStepInput) activeStepInput.value = String(currentStep);

                for (var s = 1; s <= TOTAL_STEPS; s++) {
                    var panel = getPanel(s);
                    var item = getStepperItem(s);
                    if (panel) panel.classList.toggle('reg-step-panel--hidden', s !== currentStep);
                    if (item) {
                        item.classList.remove('reg-stepper__item--active', 'reg-stepper__item--completed');
                        if (s < currentStep) item.classList.add('reg-stepper__item--completed');
                        else if (s === currentStep) item.classList.add('reg-stepper__item--active');
                    }
                }

                if (backToLogin) backToLogin.classList.toggle('hidden', currentStep > 1);
                if (prevBtn) prevBtn.classList.toggle('hidden', currentStep <= 1);
                if (nextBtn) nextBtn.classList.toggle('hidden', currentStep >= TOTAL_STEPS);
                if (submitBtnWrap) submitBtnWrap.classList.toggle('hidden', currentStep !== TOTAL_STEPS);
                if (currentStep === 2 && typeof window.syncHoursHidden === 'function') window.syncHoursHidden();
                updateNextAndSubmitState();
            }

            function goNext() {
                if (currentStep >= TOTAL_STEPS) return;
                if (!canProceedFromCurrentStep()) {
                    if (currentStep === 1) validateIdentityFields(true);
                    if (currentStep === 4 && termsCheckbox) termsCheckbox.focus();
                    if (currentStep === 5 && privacyCheckbox) privacyCheckbox.focus();
                    return;
                }
                currentStep++;
                updateStepUI();
            }
            function goPrev() {
                if (currentStep > 1) { currentStep--; updateStepUI(); }
            }

            if (nextBtn) nextBtn.addEventListener('click', goNext);
            if (nextCircle) nextCircle.addEventListener('click', goNext);
            if (prevBtn) prevBtn.addEventListener('click', goPrev);
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    if (currentStep !== TOTAL_STEPS) { e.preventDefault(); return; }
                    if (!canProceedFromCurrentStep()) {
                        e.preventDefault();
                        if (privacyCheckbox) privacyCheckbox.focus();
                        return;
                    }
                });
            }

            /* Enter key: move to next field; if on last field and step is valid, act as Next (advance step or submit) */
            if (form) {
                form.addEventListener('keydown', function(e) {
                    if (e.key !== 'Enter') return;
                    var target = e.target;
                    if (!target || !form.contains(target)) return;
                    var tag = (target.tagName && target.tagName.toUpperCase()) || '';
                    if (tag === 'TEXTAREA') return;
                    if (target.type === 'submit' || target.type === 'button') return;
                    if (tag !== 'INPUT' && tag !== 'SELECT') return;
                    e.preventDefault();
                    var panel = getPanel(currentStep);
                    var focusables = panel ? panel.querySelectorAll('input:not([type=hidden]):not([type=submit]):not([type=button]), select, textarea') : [];
                    var list = [];
                    for (var i = 0; i < focusables.length; i++) {
                        var el = focusables[i];
                        if (el.offsetParent !== null && !el.disabled) list.push(el);
                    }
                    var idx = list.indexOf(target);
                    if (idx >= 0 && idx < list.length - 1) {
                        list[idx + 1].focus();
                        return;
                    }
                    if (idx >= 0 && idx === list.length - 1 && isStepValid(currentStep)) {
                        if (currentStep < TOTAL_STEPS) goNext();
                        else if (canProceedFromCurrentStep() && submitBtn) submitBtn.click();
                    }
                });
            }

            var step1Ids = ['first_name', 'middle_name', 'last_name', 'personal_email_username', 'birthday', 'address', 'contact_number', 'gender', 'civil_status'];
            var step2Ids = ['role_employee', 'role_intern', 'employeeDepartment', 'department_other', 'school_name', 'hours_option', 'hours_custom', 'employee_id_display'];
            var step3Ids = ['mmco_email_username', 'password', 'confirm_password'];
            var step4Ids = ['terms', 'privacy_agreed'];
            function addListeners(ids, handler) {
                ids.forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', handler);
                        el.addEventListener('change', handler);
                    }
                });
            }
            addListeners(step1Ids, updateNextAndSubmitState);
            addListeners(step2Ids, updateNextAndSubmitState);
            addListeners(step3Ids, updateNextAndSubmitState);
            addListeners(step4Ids, updateNextAndSubmitState);
            if (termsCheckbox) {
                termsCheckbox.addEventListener('change', updateNextAndSubmitState);
            }
            if (privacyCheckbox) {
                privacyCheckbox.addEventListener('change', updateNextAndSubmitState);
            }

            window.regUpdateStepState = updateNextAndSubmitState;
            updateStepUI();
        })();

        const profileFile = document.getElementById('profile_photo');
        const profilePreview = document.getElementById('profilePreview');
        const btnPickPhoto = document.getElementById('btnPickPhoto');

        const roleEmployee = document.getElementById('role_employee');
        const roleIntern = document.getElementById('role_intern');
        const employeeBlock = document.getElementById('employeeBlock');

        const firstName = document.getElementById('first_name');
        const middleName = document.getElementById('middle_name');
        const lastName = document.getElementById('last_name');
        const birthdayEl = document.getElementById('birthday');
        const addressEl = document.getElementById('address');
        const contactNumber = document.getElementById('contact_number');
        const employeeDepartment = document.getElementById('employeeDepartment');
        const employeeIdDisplay = document.getElementById('employee_id_display');
        const employeeIdHidden = document.getElementById('employee_id');
        const personalEmailUsername = document.getElementById('personal_email_username');
        const personalEmailDomain = document.getElementById('personal_email_domain');
        const personalEmailHidden = document.getElementById('personal_email');
        const mmcoEmailUsername = document.getElementById('mmco_email_username');
        const mmcoEmailDomain = document.getElementById('mmco_email_domain');
        const mmcoEmailHidden = document.getElementById('mmco_email');
        const userType = document.getElementById('user_type');

        const departmentOtherWrap = document.getElementById('departmentOtherWrap');
        const departmentOther = document.getElementById('department_other');

        const internDetails = document.getElementById('internDetails');
        const schoolName = document.getElementById('school_name');
        const hoursNeeded = document.getElementById('hours_needed');
        const hoursOption = document.getElementById('hours_option');
        const hoursCustomWrap = document.getElementById('hours_custom_wrap');
        const hoursCustom = document.getElementById('hours_custom');
        const startDateEl = document.getElementById('start_date');
        const endDateEl = document.getElementById('end_date');

        function updateEstimatedEndDate() {
            if (!startDateEl || !endDateEl || !hoursNeeded) return;
            var startVal = startDateEl.value;
            var hoursVal = parseInt(hoursNeeded.value, 10);
            if (!startVal || !hoursVal || isNaN(hoursVal) || hoursVal <= 0) {
                endDateEl.value = '';
                return;
            }
            var HOURS_PER_DAY = 8;
            var workDays = Math.ceil(hoursVal / HOURS_PER_DAY);
            var d = new Date(startVal + 'T12:00:00');
            var count = 0;
            while (count < workDays) {
                var day = d.getDay();
                if (day !== 0 && day !== 6) count++;
                if (count >= workDays) break;
                d.setDate(d.getDate() + 1);
            }
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            endDateEl.value = y + '-' + m + '-' + day;
        }

        (function initHoursDropdown() {
            if (!hoursOption || !hoursNeeded) return;
            function syncHoursHidden() {
                var opt = hoursOption.value;
                if (opt === '300' || opt === '400' || opt === '500') {
                    hoursNeeded.value = opt;
                } else if (opt === 'Custom' && hoursCustom) {
                    var v = (hoursCustom.value || '').replace(/\D/g, '').slice(0, 4);
                    hoursNeeded.value = v;
                } else {
                    hoursNeeded.value = '';
                }
                if (window.regUpdateStepState) window.regUpdateStepState();
                if (typeof updateEstimatedEndDate === 'function') updateEstimatedEndDate();
            }
            function toggleCustomWrap() {
                if (!hoursCustomWrap) return;
                if (hoursOption.value === 'Custom') {
                    hoursCustomWrap.classList.remove('hidden');
                    if (hoursCustom) hoursCustom.required = true;
                } else {
                    hoursCustomWrap.classList.add('hidden');
                    if (hoursCustom) { hoursCustom.required = false; hoursCustom.value = ''; }
                }
                syncHoursHidden();
            }
            hoursOption.addEventListener('change', function() {
                toggleCustomWrap();
            });
            if (hoursCustom) {
                hoursCustom.addEventListener('input', function() {
                    var before = this.value;
                    var cleaned = before.replace(/\D/g, '').slice(0, 4);
                    if (cleaned !== before) this.value = cleaned;
                    syncHoursHidden();
                });
                hoursCustom.addEventListener('change', syncHoursHidden);
            }
            toggleCustomWrap();
            var initialHours = hoursNeeded.value;
            if (hoursOption.value === 'Custom' && initialHours && hoursCustom) {
                hoursCustom.value = initialHours;
            }
            window.syncHoursHidden = syncHoursHidden;
        })();

        if (startDateEl) startDateEl.addEventListener('change', updateEstimatedEndDate);
        if (hoursNeeded) {
            hoursNeeded.addEventListener('change', updateEstimatedEndDate);
            var hoursOptionEl = document.getElementById('hours_option');
            if (hoursOptionEl) hoursOptionEl.addEventListener('change', updateEstimatedEndDate);
        }
        if (hoursCustom) hoursCustom.addEventListener('input', function() { setTimeout(updateEstimatedEndDate, 0); });

        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordHelp = document.getElementById('passwordHelp');
        const strengthLabel = document.getElementById('strengthLabel');
        const strengthBar = document.getElementById('strengthBar');
        const btnSubmit = document.getElementById('btnSubmit');

        const termsCheckbox = document.getElementById('terms');
        const openTermsBtn = document.getElementById('openTerms');
        const openPrivacyBtn = document.getElementById('openPrivacy');
        const modalOverlay = document.getElementById('modalOverlay');
        const termsModal = document.getElementById('termsModal');
        const privacyModal = document.getElementById('privacyModal');
        const termsContent = document.getElementById('termsContent');
        const privacyContent = document.getElementById('privacyContent');
        const termsAgree = document.getElementById('termsAgree');
        const termsDisagree = document.getElementById('termsDisagree');
        const privacyAgree = document.getElementById('privacyAgree');
        const privacyDisagree = document.getElementById('privacyDisagree');

        let lastActiveElement = null;
        let activeModal = null;

        function getFocusable(container) {
            if (!container) return [];
            return Array.from(container.querySelectorAll('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'))
                .filter(el => !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden'));
        }

        function trapFocus(e) {
            if (!activeModal) return;
            if (e.key !== 'Tab') return;
            const focusable = getFocusable(activeModal);
            if (focusable.length === 0) return;

            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (e.shiftKey) {
                if (document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                if (document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }

        function closeModal({ restoreFocus = true } = {}) {
            if (!modalOverlay) return;
            modalOverlay.classList.add('hidden');
            termsModal.classList.add('hidden');
            privacyModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.removeEventListener('keydown', trapFocus, true);
            activeModal = null;
            if (restoreFocus && lastActiveElement && typeof lastActiveElement.focus === 'function') {
                lastActiveElement.focus();
            }
        }

        function updateAgreeEnabled(scrollEl, agreeBtn) {
            if (!scrollEl || !agreeBtn) return;
            const atBottom = Math.ceil(scrollEl.scrollTop + scrollEl.clientHeight) >= (scrollEl.scrollHeight - 2);
            agreeBtn.disabled = !atBottom;
        }

        function openModal(which) {
            if (!modalOverlay) return;
            lastActiveElement = document.activeElement;
            document.body.classList.add('overflow-hidden');
            modalOverlay.classList.remove('hidden');

            termsModal.classList.toggle('hidden', which !== 'terms');
            privacyModal.classList.toggle('hidden', which !== 'privacy');
            activeModal = which === 'terms' ? termsModal : privacyModal;

            if (which === 'terms') {
                termsContent.scrollTop = 0;
                termsAgree.disabled = true;
                updateAgreeEnabled(termsContent, termsAgree);
                setTimeout(() => {
                    const focusable = getFocusable(termsModal);
                    (focusable[0] || termsModal).focus?.();
                }, 0);
            } else {
                privacyContent.scrollTop = 0;
                privacyAgree.disabled = true;
                updateAgreeEnabled(privacyContent, privacyAgree);
                setTimeout(() => {
                    const focusable = getFocusable(privacyModal);
                    (focusable[0] || privacyModal).focus?.();
                }, 0);
            }

            document.addEventListener('keydown', trapFocus, true);
        }

        /* Terms and Conditions: popup when Terms checkbox is clicked; checkbox stays checked only after Agree */
        if (termsCheckbox) {
            termsCheckbox.checked = false;
            termsCheckbox.addEventListener('click', (e) => {
                if (e.target.checked) {
                    e.preventDefault();
                    termsCheckbox.checked = false;
                    openModal('terms');
                }
            });
        }

        /* Privacy Policy: popup only when Privacy checkbox is clicked; checkbox stays checked only after Agree */
        const privacyCheckboxEl = document.getElementById('privacy_agreed');
        if (privacyCheckboxEl) {
            privacyCheckboxEl.addEventListener('click', (e) => {
                if (e.target.checked) {
                    e.preventDefault();
                    privacyCheckboxEl.checked = false;
                    openModal('privacy');
                }
            });
        }

        if (termsContent && termsAgree) {
            termsContent.addEventListener('scroll', () => updateAgreeEnabled(termsContent, termsAgree));
        }
        if (privacyContent && privacyAgree) {
            privacyContent.addEventListener('scroll', () => updateAgreeEnabled(privacyContent, privacyAgree));
        }

        if (termsDisagree) {
            termsDisagree.addEventListener('click', () => closeModal());
        }
        if (termsAgree) {
            termsAgree.addEventListener('click', () => {
                if (termsCheckbox) termsCheckbox.checked = true;
                closeModal();
                if (window.regUpdateStepState) window.regUpdateStepState();
            });
        }

        if (privacyDisagree) {
            privacyDisagree.addEventListener('click', () => {
                if (privacyCheckboxEl) privacyCheckboxEl.checked = false;
                closeModal();
            });
        }
        if (privacyAgree) {
            privacyAgree.addEventListener('click', () => {
                if (privacyCheckboxEl) privacyCheckboxEl.checked = true;
                closeModal();
                if (window.regUpdateStepState) window.regUpdateStepState();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (!activeModal) return;
            if (e.key !== 'Escape') return;
            e.preventDefault();
            closeModal();
        });

        function clampString(v) {
            return (v || '').replace(/\s{2,}/g, ' ');
        }

        function titleCase(v) {
            const collapsed = clampString(v);
            const hasTrailingSpace = / $/.test(collapsed);
            const base = collapsed.trimEnd();
            if (base === '') return hasTrailingSpace ? ' ' : '';
            const parts = base.toLowerCase().split(' ');
            const formatted = parts.map(w => w ? (w.charAt(0).toUpperCase() + w.slice(1)) : '').join(' ');
            return hasTrailingSpace ? (formatted + ' ') : formatted;
        }

        function applyTextRules(el) {
            if (!el || typeof el.value !== 'string') return;
            el.value = titleCase(el.value);
        }

        function isInternSelected() {
            return (roleIntern && roleIntern.checked) === true;
        }

        function isEmployeeSelected() {
            return (roleEmployee && roleEmployee.checked) === true;
        }

        function updateRoleUI() {
            const emp = isEmployeeSelected();
            const intern = isInternSelected();

            // Department block is shared for both employees and interns
            employeeBlock.classList.toggle('hidden', !emp && !intern);

            if (emp) {
                employeeBlock.classList.add('fade-in');
                setTimeout(() => employeeBlock.classList.remove('fade-in'), 220);
                userType.value = 'employee';
            } else if (intern) {
                userType.value = 'ojt';
            } else {
                userType.value = '';
            }

            updateDepartmentOtherUI();
            updateInternOtherUI();
            updateInternDetailsUI();
            updateRequiredByRole();
            updateSelectHintColors();
            updateMmcoEmailByRole();
            fetchNextEmployeeId();
        }

        function getDepartmentForEmployeeId() {
            // For both employees and interns, Employee ID is derived from the selected department
            if (!employeeDepartment) return '';
            return employeeDepartment.value || '';
        }

        function fetchNextEmployeeId() {
            if (!employeeIdDisplay || !employeeIdHidden) return;
            const role = (roleIntern && roleIntern.checked) ? 'ojt' : (roleEmployee && roleEmployee.checked) ? 'employee' : '';
            const department = getDepartmentForEmployeeId();
            if (!role || !department) {
                employeeIdDisplay.value = '';
                employeeIdHidden.value = '';
                if (window.regUpdateStepState) window.regUpdateStepState();
                return;
            }
            var formEl = document.getElementById('registerForm');
            var basePath = (formEl && formEl.getAttribute('data-base-path')) || '';
            var path = (basePath ? basePath + '/' : '') + 'register/next-employee-id';
            var url = path + '?role=' + encodeURIComponent(role) + '&department=' + encodeURIComponent(department);
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(res) {
                    if (!res.ok) return null;
                    return res.json();
                })
                .then(function(data) {
                    if (data && data.employee_id) {
                        employeeIdDisplay.value = data.employee_id;
                        employeeIdHidden.value = data.employee_id;
                    }
                    // On error or missing employee_id, leave existing value so server-rendered $old or prior fetch is preserved
                    if (window.regUpdateStepState) window.regUpdateStepState();
                })
                .catch(function() {
                    // On network/fetch error, do not clear - preserve existing employee_id (e.g. from $old on validation re-display)
                    if (window.regUpdateStepState) window.regUpdateStepState();
                });
        }

        function getMmcoDomain() {
            if (isInternSelected()) return '.mmco.ojt@gmail.com';
            if (isEmployeeSelected()) return '.mmco@gmail.com';
            return '.mmco@gmail.com';
        }

        function updateMmcoEmailByRole() {
            if (!mmcoEmailUsername || !mmcoEmailDomain || !mmcoEmailHidden) return;
            const domain = getMmcoDomain();
            mmcoEmailDomain.textContent = domain;

            const maxUser = Math.max(0, 60 - domain.length);
            mmcoEmailUsername.setAttribute('maxlength', String(maxUser));
            if ((mmcoEmailUsername.value || '').length > maxUser) {
                mmcoEmailUsername.value = (mmcoEmailUsername.value || '').slice(0, maxUser);
            }

            const userPart = (mmcoEmailUsername.value || '').trim();
            mmcoEmailHidden.value = userPart !== '' ? (userPart + domain) : '';
        }

        function updatePersonalEmail() {
            if (!personalEmailUsername || !personalEmailDomain || !personalEmailHidden) return;
            const domain = '@gmail.com';
            personalEmailDomain.textContent = domain;

            const maxUser = Math.max(0, 60 - domain.length);
            personalEmailUsername.setAttribute('maxlength', String(maxUser));
            if ((personalEmailUsername.value || '').length > maxUser) {
                personalEmailUsername.value = (personalEmailUsername.value || '').slice(0, maxUser);
            }

            const userPart = (personalEmailUsername.value || '').trim();
            personalEmailHidden.value = userPart !== '' ? (userPart + domain) : '';
        }

        function updateDepartmentOtherUI() {
            const show = (isEmployeeSelected() || isInternSelected()) && employeeDepartment.value === 'Other';
            departmentOtherWrap.classList.toggle('hidden', !show);
            departmentOther.required = show;
            if (!show) {
                departmentOther.value = departmentOther.value;
            }
        }

        // Intern type field was removed; keep this as a no-op to avoid errors from older calls.
        function updateInternOtherUI() {}

        function updateInternDetailsUI() {
            const show = isInternSelected();
            internDetails.classList.toggle('hidden', !show);
            if (show) {
                internDetails.classList.add('fade-in');
                setTimeout(() => internDetails.classList.remove('fade-in'), 220);
            }
        }

        function updateRequiredByRole() {
            const intern = isInternSelected();
            const emp = isEmployeeSelected();

            // Department is required for both employees and interns
            employeeDepartment.required = emp || intern;

            schoolName.required = intern;
            hoursNeeded.required = intern;
        }

        function togglePasswordVisibility(input, icon) {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('text-sky-700', isHidden);
        }

        function scorePassword(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;
            if (pw.length >= 12) score++;
            return score;
        }

        function updatePasswordUI() {
            if (!password || !strengthLabel || !strengthBar) return;
            const pw = password.value || '';
            const lenOk = pw.length >= 8;
            const numOk = /[0-9]/.test(pw);
            const upperOk = /[A-Z]/.test(pw);
            const specialOk = /[^A-Za-z0-9]/.test(pw);

            const setRule = (id, ok) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.toggle('text-gray-500', !ok);
                el.classList.toggle('text-emerald-600', ok);
            };

            setRule('ruleLen', lenOk);
            setRule('ruleNum', numOk);
            setRule('ruleUpper', upperOk);
            setRule('ruleSpecial', specialOk);

            const score = scorePassword(pw);
            let label = 'Weak';
            let width = '10%';
            let color = 'bg-red-500';

            if (score === 2) { label = 'Average'; width = '35%'; color = 'bg-orange-500'; }
            if (score === 3) { label = 'Moderately Strong'; width = '55%'; color = 'bg-yellow-500'; }
            if (score === 4) { label = 'Strong'; width = '75%'; color = 'bg-lime-500'; }
            if (score >= 5) { label = 'Very Strong'; width = '100%'; color = 'bg-emerald-600'; }

            strengthLabel.textContent = label;
            strengthBar.classList.remove('bg-red-500','bg-orange-500','bg-yellow-500','bg-lime-500','bg-emerald-600');
            strengthBar.classList.add(color);
            strengthBar.style.width = width;
        }

        function validateForm() {
            if (!form) return false;
            const identityOk = validateIdentityFields(false);

            // Role selection: at least one must be selected
            const emp = isEmployeeSelected();
            const intern = isInternSelected();
            if (!emp && !intern) return false;

            // Employee ID must be generated (set when role + department selected)
            if ((emp || intern) && employeeIdHidden && !(employeeIdHidden.value || '').trim()) return false;

            if (!identityOk) return false;

            const required = form.querySelectorAll('[required]');
            for (const el of required) {
                if (el.disabled) continue;
                if (el.type === 'checkbox' && !el.checked) return false;
                if ((el.value || '').trim() === '') return false;
            }
            if (password && confirmPassword && password.value !== confirmPassword.value) return false;
            return true;
        }

        function setClientError(inputEl, errorElId, message, tone = 'error') {
            const errEl = document.getElementById(errorElId);
            if (!errEl) return;
            const show = (message || '') !== '';
            errEl.textContent = show ? message : '';
            errEl.classList.toggle('hidden', !show);

            const isHint = tone === 'hint';
            errEl.classList.toggle('text-gray-500', isHint);
            errEl.classList.toggle('text-red-600', !isHint);

            if (inputEl) {
                inputEl.setAttribute('aria-invalid', show && !isHint ? 'true' : 'false');
                inputEl.classList.toggle('reg-input--error', show && !isHint);
            }
        }

        function normalizeSpaces(v) {
            return (v || '').replace(/\s{2,}/g, ' ').trimStart();
        }

        function sanitizeLettersAndSpaces(v) {
            const collapsed = normalizeSpaces(v);
            return collapsed.replace(/[^a-zA-Z ]/g, '');
        }

        function isLettersAndSpacesValid(v) {
            const s = (v || '').trim();
            if (s === '') return true;
            return /^[A-Za-z]+(?: [A-Za-z]+)*$/.test(s);
        }

        function sanitizeDigits(v) {
            return (v || '').replace(/\D+/g, '');
        }

        function validateIdentityFields(showErrors) {
            const show = !!showErrors;
            let ok = true;

            if (firstName) {
                const trimmed = (firstName.value || '').trim();
                const validChars = isLettersAndSpacesValid(firstName.value);
                let msg = '';
                if (trimmed === '') msg = 'First name is required.';
                else if (trimmed.length < 3) msg = 'First name must be at least 3 characters.';
                else if (!validChars) msg = 'First name must contain letters only. Numbers/special characters are not allowed.';
                if (show) setClientError(firstName, 'first_name_client_error', msg);
                ok = ok && trimmed.length >= 3 && validChars;
            }

            if (lastName) {
                const trimmed = (lastName.value || '').trim();
                const validChars = isLettersAndSpacesValid(lastName.value);
                let msg = '';
                if (trimmed === '') msg = 'Last name is required.';
                else if (trimmed.length < 3) msg = 'Last name must be at least 3 characters.';
                else if (!validChars) msg = 'Last name must contain letters only. Numbers/special characters are not allowed.';
                if (show) setClientError(lastName, 'last_name_client_error', msg);
                ok = ok && trimmed.length >= 3 && validChars;
            }

            if (middleName) {
                const trimmed = (middleName.value || '').trim();
                const validChars = isLettersAndSpacesValid(middleName.value);
                let msg = '';
                if (trimmed.length > 0 && trimmed.length < 3) msg = 'Middle name must be at least 3 characters if provided.';
                else if (trimmed.length > 0 && !validChars) msg = 'Middle name must contain letters only. Numbers/special characters are not allowed.';
                if (show) setClientError(middleName, 'middle_name_client_error', msg);
                ok = ok && (trimmed.length === 0 || (trimmed.length >= 3 && validChars));
            }

            if (addressEl) {
                const trimmed = (addressEl.value || '').trim();
                let msg = '';
                if (trimmed === '') msg = 'Address is required.';
                else if (trimmed.length < 15) msg = 'Address must be at least 15 characters to ensure a complete address.';
                if (show) setClientError(addressEl, 'address_client_error', msg);
                ok = ok && trimmed.length >= 15;
            }

            if (personalEmailHidden && personalEmailUsername) {
                const usernamePart = (personalEmailUsername.value || '').trim();
                let msg = '';
                if (usernamePart.length === 0) msg = 'Personal email is required.';
                else if (usernamePart.length < 8) msg = 'Email is too short.';
                if (show) setClientError(personalEmailUsername, 'personal_email_client_error', msg);
                ok = ok && usernamePart.length >= 8;
            }

            if (schoolName && !schoolName.closest('.hidden')) {
                const trimmed = (schoolName.value || '').trim();
                const valid = isLettersAndSpacesValid(schoolName.value);
                if (show) setClientError(schoolName, 'school_name_client_error', (trimmed === '' || valid) ? '' : 'School name must contain letters only. Numbers/special characters are not allowed.');
                ok = ok && valid;
            }

            if (birthdayEl) {
                const v = (birthdayEl.value || '').trim();
                if (v) {
                    const birth = new Date(v);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    birth.setHours(0, 0, 0, 0);
                    const age = Math.floor((today - birth) / (365.25 * 24 * 60 * 60 * 1000));
                    const valid = age >= 19 && age <= 100;
                    if (show) setClientError(birthdayEl, 'birthday_client_error', valid ? '' : 'Age must be between 19 and 100 years old.');
                    ok = ok && valid;
                } else {
                    if (show) setClientError(birthdayEl, 'birthday_client_error', '');
                }
            }

            if (contactNumber) {
                const trimmed = (contactNumber.value || '').trim();
                const digitsOnly = /^\d+$/.test(trimmed);
                const startsOk = trimmed.startsWith('09');
                const valid = trimmed === '' || (digitsOnly && startsOk && trimmed.length === 11);

                let msg = '';
                let tone = 'error';
                if (trimmed !== '' && !valid) {
                    if (!digitsOnly) {
                        msg = 'Contact number must contain numbers only.';
                    } else if (!startsOk) {
                        msg = 'Contact number must start with 09.';
                    } else {
                        msg = 'Contact number must be 11 digits.';
                        tone = 'hint';
                    }
                }
                if (show) setClientError(contactNumber, 'contact_number_client_error', msg, tone);
                ok = ok && valid;
            }

            return ok;
        }

        function validateAndShowOneField(fieldId) {
            const el = document.getElementById(fieldId);
            if (!el) return;
            const trimmed = (v) => (v || '').trim();
            if (fieldId === 'first_name' && firstName) {
                const t = trimmed(firstName.value);
                const validChars = isLettersAndSpacesValid(firstName.value);
                let msg = '';
                if (t === '') msg = 'First name is required.';
                else if (t.length < 3) msg = 'First name must be at least 3 characters.';
                else if (!validChars) msg = 'First name must contain letters only. Numbers/special characters are not allowed.';
                setClientError(firstName, 'first_name_client_error', msg);
            } else if (fieldId === 'last_name' && lastName) {
                const t = trimmed(lastName.value);
                const validChars = isLettersAndSpacesValid(lastName.value);
                let msg = '';
                if (t === '') msg = 'Last name is required.';
                else if (t.length < 3) msg = 'Last name must be at least 3 characters.';
                else if (!validChars) msg = 'Last name must contain letters only. Numbers/special characters are not allowed.';
                setClientError(lastName, 'last_name_client_error', msg);
            } else if (fieldId === 'middle_name' && middleName) {
                const t = trimmed(middleName.value);
                const validChars = isLettersAndSpacesValid(middleName.value);
                let msg = '';
                if (t.length > 0 && t.length < 3) msg = 'Middle name must be at least 3 characters if provided.';
                else if (t.length > 0 && !validChars) msg = 'Middle name must contain letters only. Numbers/special characters are not allowed.';
                setClientError(middleName, 'middle_name_client_error', msg);
            } else if (fieldId === 'address' && addressEl) {
                const t = trimmed(addressEl.value);
                let msg = '';
                if (t === '') msg = 'Address is required.';
                else if (t.length < 15) msg = 'Address must be at least 15 characters to ensure a complete address.';
                setClientError(addressEl, 'address_client_error', msg);
            } else if (fieldId === 'personal_email_username' && personalEmailUsername) {
                const usernamePart = trimmed(personalEmailUsername.value);
                let msg = '';
                if (usernamePart.length === 0) msg = 'Personal email is required.';
                else if (usernamePart.length < 8) msg = 'Email is too short.';
                setClientError(personalEmailUsername, 'personal_email_client_error', msg);
            } else if (fieldId === 'contact_number' && contactNumber) {
                const t = trimmed(contactNumber.value);
                const digitsOnly = /^\d+$/.test(t);
                const startsOk = t.startsWith('09');
                const valid = t === '' || (digitsOnly && startsOk && t.length === 11);
                let msg = '', tone = 'error';
                if (t !== '' && !valid) {
                    if (!digitsOnly) msg = 'Contact number must contain numbers only.';
                    else if (!startsOk) msg = 'Contact number must start with 09.';
                    else { msg = 'Contact number must be 11 digits.'; tone = 'hint'; }
                }
                setClientError(contactNumber, 'contact_number_client_error', msg, tone);
            } else if (fieldId === 'birthday' && birthdayEl) {
                const v = trimmed(birthdayEl.value);
                if (v) {
                    const birth = new Date(v);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    birth.setHours(0, 0, 0, 0);
                    const age = Math.floor((today - birth) / (365.25 * 24 * 60 * 60 * 1000));
                    const valid = age >= 19 && age <= 100;
                    setClientError(birthdayEl, 'birthday_client_error', valid ? '' : 'Age must be between 19 and 100 years old.');
                } else {
                    setClientError(birthdayEl, 'birthday_client_error', '');
                }
            } else if (fieldId === 'school_name' && schoolName && !schoolName.closest('.hidden')) {
                const t = trimmed(schoolName.value);
                const valid = isLettersAndSpacesValid(schoolName.value);
                setClientError(schoolName, 'school_name_client_error', (t === '' || valid) ? '' : 'School name must contain letters only. Numbers/special characters are not allowed.');
            }
        }

        function updateSubmitEnabled() {
            const ok = validateForm();
            btnSubmit.disabled = !ok;
        }

        btnPickPhoto.addEventListener('click', () => profileFile.click());
        profilePreview.addEventListener('click', () => profileFile.click());
        profileFile.addEventListener('change', () => {
            const f = profileFile.files && profileFile.files[0];
            if (!f) return;
            const url = URL.createObjectURL(f);
            profilePreview.src = url;
        });

        roleEmployee.addEventListener('change', () => { updateRoleUI(); updateSelectHintColors(); updateMmcoEmailByRole(); updateSubmitEnabled(); });
        roleIntern.addEventListener('change', () => { updateRoleUI(); updateSelectHintColors(); updateMmcoEmailByRole(); updateSubmitEnabled(); });

        employeeDepartment.addEventListener('change', () => { updateRoleUI(); updateSelectHintColors(); updateMmcoEmailByRole(); fetchNextEmployeeId(); updateSubmitEnabled(); });
        if (departmentOther) departmentOther.addEventListener('input', () => { fetchNextEmployeeId(); updateSubmitEnabled(); });
        if (departmentOther) departmentOther.addEventListener('change', () => { fetchNextEmployeeId(); updateSubmitEnabled(); });

        if (mmcoEmailUsername) {
            mmcoEmailUsername.addEventListener('input', () => {
                updateMmcoEmailByRole();
                updateSubmitEnabled();
            });
            mmcoEmailUsername.addEventListener('blur', () => {
                mmcoEmailUsername.value = (mmcoEmailUsername.value || '').trim();
                updateMmcoEmailByRole();
            });
        }

        if (personalEmailUsername) {
            personalEmailUsername.addEventListener('input', () => {
                updatePersonalEmail();
                updateSubmitEnabled();
            });
            personalEmailUsername.addEventListener('blur', () => {
                personalEmailUsername.value = (personalEmailUsername.value || '').trim();
                updatePersonalEmail();
                validateAndShowOneField('personal_email_username');
                updateSubmitEnabled();
            });
        }

        [firstName, lastName, middleName, addressEl, contactNumber, birthdayEl].forEach(function(el) {
            if (el) el.addEventListener('blur', function() { validateAndShowOneField(el.id); updateSubmitEnabled(); });
        });
        if (birthdayEl) birthdayEl.addEventListener('change', function() { validateAndShowOneField('birthday'); updateSubmitEnabled(); });
        if (schoolName) schoolName.addEventListener('blur', function() { validateAndShowOneField('school_name'); updateSubmitEnabled(); });

        function updateSelectHintColors() {
            const selects = form.querySelectorAll('select[data-hint-select]');
            for (const sel of selects) {
                const isHint = (sel.value || '') === '';
                sel.classList.toggle('text-gray-400', isHint);
                sel.classList.toggle('text-gray-900', !isHint);
            }
        }

        const textLikeSelector = 'input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea';
        form.addEventListener('input', (e) => {
            const el = e.target;
            if (!el) return;

            if (el.id === 'personal_email_username') {
                const before = el.value;
                const cleaned = before
                    .toLowerCase()
                    .replace(/\s+/g, '')
                    .replace(/[^a-z0-9._-]/g, '');
                if (cleaned !== before) {
                    const caret = el.selectionStart;
                    el.value = cleaned;
                    if (typeof caret === 'number') {
                        el.setSelectionRange(caret, caret);
                    }
                }
                updatePersonalEmail();
                if (personalEmailUsername && (personalEmailUsername.value || '').trim().length >= 8) {
                    setClientError(personalEmailUsername, 'personal_email_client_error', '');
                }
                return;
            }

            if (el.id === 'first_name' || el.id === 'last_name') {
                const before = el.value;
                const cleaned = sanitizeLettersAndSpaces(before).slice(0, 30);
                if (cleaned !== before) {
                    const pos = el.selectionStart;
                    el.value = cleaned;
                    try { el.setSelectionRange(pos, pos); } catch (_) {}
                    setClientError(el, el.id === 'first_name' ? 'first_name_client_error' : 'last_name_client_error', 'Only letters and spaces are allowed.');
                } else {
                    const trimmed = (el.value || '').trim();
                    const validChars = isLettersAndSpacesValid(el.value);
                    const valid = el.id === 'first_name' || el.id === 'last_name'
                        ? (trimmed.length >= 3 && validChars)
                        : (trimmed === '' || validChars);
                    if (valid) {
                        setClientError(el, el.id === 'first_name' ? 'first_name_client_error' : 'last_name_client_error', '');
                    }
                }
            }

            if (el.id === 'middle_name') {
                const before = el.value;
                const cleaned = sanitizeLettersAndSpaces(before).slice(0, 30);
                if (cleaned !== before) {
                    const pos = el.selectionStart;
                    el.value = cleaned;
                    try { el.setSelectionRange(pos, pos); } catch (_) {}
                    setClientError(el, 'middle_name_client_error', 'Only letters and spaces are allowed.');
                } else {
                    const trimmed = (el.value || '').trim();
                    const validChars = isLettersAndSpacesValid(el.value);
                    const valid = trimmed === '' || (trimmed.length >= 3 && validChars);
                    if (valid) {
                        setClientError(el, 'middle_name_client_error', '');
                    }
                }
            }

            if (el.id === 'address' && addressEl === el) {
                const trimmed = (el.value || '').trim();
                if (trimmed.length >= 15) setClientError(addressEl, 'address_client_error', '');
            }

            if (el.id === 'school_name') {
                const before = el.value;
                const cleaned = sanitizeLettersAndSpaces(before).slice(0, 50);
                if (cleaned !== before) {
                    const pos = el.selectionStart;
                    el.value = cleaned;
                    try { el.setSelectionRange(pos, pos); } catch (_) {}
                    setClientError(el, 'school_name_client_error', 'Only letters and spaces are allowed.');
                } else {
                    const trimmed = (el.value || '').trim();
                    const isValidNow = trimmed === '' || isLettersAndSpacesValid(el.value);
                    if (isValidNow) {
                        setClientError(el, 'school_name_client_error', '');
                    }
                }
            }

            if (el.id === 'contact_number') {
                const before = el.value;
                let cleaned = sanitizeDigits(before);
                if (cleaned.startsWith('9')) {
                    cleaned = '0' + cleaned;
                }
                cleaned = cleaned.slice(0, 11);
                if (cleaned !== before) {
                    const pos = el.selectionStart;
                    el.value = cleaned;
                    try { el.setSelectionRange(pos, pos); } catch (_) {}
                }
                const trimmed = (el.value || '').trim();
                const digitsOnly = /^\d+$/.test(trimmed);
                const startsOk = trimmed.startsWith('09');
                const isValidNow = trimmed === '' || (digitsOnly && startsOk && trimmed.length === 11);
                if (isValidNow) setClientError(el, 'contact_number_client_error', '');
            }

            if (el.id === 'mmco_email_username') {
                const before = el.value;
                const cleaned = before
                    .toLowerCase()
                    .replace(/\s+/g, '')
                    .replace(/[^a-z0-9._-]/g, '');
                if (cleaned !== before) {
                    const caret = el.selectionStart;
                    el.value = cleaned;
                    if (typeof caret === 'number') {
                        el.setSelectionRange(caret, caret);
                    }
                }
                updateMmcoEmailByRole();
                return;
            }

            // Allow normal spaces between words but prevent multiple consecutive spaces.
            if (el.matches(textLikeSelector) && el.type !== 'number' && el.id !== 'mmco_email' && el.id !== 'mmco_email_username' && el.id !== 'personal_email' && el.id !== 'personal_email_username' && el.type !== 'email') {
                const before = el.value;
                const collapsed = clampString(before);
                if (collapsed !== before) {
                    const pos = el.selectionStart;
                    el.value = collapsed;
                    try { el.setSelectionRange(pos, pos); } catch (_) {}
                }
            }

            // Title-case only for human-name style inputs.
            if (el.id === 'department_other' || el.id === 'school_name' || el.id === 'first_name' || el.id === 'middle_name' || el.id === 'last_name') {
                const before = el.value;
                const formatted = titleCase(before);
                if (formatted !== before) {
                    const pos = el.selectionStart;
                    el.value = formatted;
                    try { el.setSelectionRange(pos, pos); } catch (_) {}
                }
            }

            if (el.id === 'password') {
                updatePasswordUI();
            }

            updateSubmitEnabled();
        });

        var togglePasswordEl = document.getElementById('togglePassword');
        var toggleConfirmPasswordEl = document.getElementById('toggleConfirmPassword');
        if (togglePasswordEl && password) togglePasswordEl.addEventListener('click', () => {
            togglePasswordVisibility(password, document.getElementById('eyeIconPassword'));
        });
        if (toggleConfirmPasswordEl && confirmPassword) toggleConfirmPasswordEl.addEventListener('click', () => {
            togglePasswordVisibility(confirmPassword, document.getElementById('eyeIconConfirm'));
        });

        if (password) {
            password.addEventListener('focus', () => {
                if (passwordHelp) { passwordHelp.classList.remove('hidden'); passwordHelp.classList.add('fade-in'); }
                updatePasswordUI();
            });
        }
        function hidePasswordHelpIfFocusLeaves() {
            setTimeout(() => {
                var active = document.activeElement;
                if (active === password || active === confirmPassword) return;
                if (passwordHelp) passwordHelp.classList.add('hidden');
            }, 0);
        }
        if (password) password.addEventListener('blur', hidePasswordHelpIfFocusLeaves);
        if (confirmPassword) confirmPassword.addEventListener('blur', hidePasswordHelpIfFocusLeaves);

        // Ensure UI matches any server-restored values (do not show errors on load)
        (function init() {
            updateRoleUI();
            updateSelectHintColors();
            updateMmcoEmailByRole();
            updatePersonalEmail();
            validateIdentityFields(false);
            updateSubmitEnabled();
            // Apply red highlight to fields that have server-side error messages
            form.querySelectorAll('.reg-field__error:not(.hidden)').forEach(function(p) {
                if (!p.textContent || !p.textContent.trim()) return;
                var field = p.closest('.reg-field');
                var input = field && field.querySelector('input:not([type=hidden]):not([type=submit]):not([type=button]), select');
                if (input) input.classList.add('reg-input--error');
            });
        })();

        form.addEventListener('submit', (e) => {
            updateRoleUI();
            updateSubmitEnabled();
            if (!validateForm()) {
                e.preventDefault();
                return;
            }

        });
    </script>
</body>
</html>
