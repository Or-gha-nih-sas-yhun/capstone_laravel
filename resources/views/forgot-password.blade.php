<x-layout>

<x-slot:styles>
<style>
    /* ─── FORGOT CARD ─────────────────────────────────── */
    .forgot-card {
        width: 100%;
        max-width: 440px;
        background: var(--white);
        border-radius: 16px;
        box-shadow:
            0 2px 8px  rgba(13, 27, 46, 0.06),
            0 8px 32px rgba(13, 27, 46, 0.08);
        overflow: hidden;
        margin: 2rem auto;
        animation: cardIn 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .card-accent {
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light));
    }

    .card-body {
        padding: 2.25rem 2rem 2.5rem;
    }

    .card-heading {
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .card-heading h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.9rem;
        font-weight: 600;
        color: var(--navy);
        line-height: 1.2;
        margin-bottom: 0.35rem;
    }

    .card-heading p {
        font-size: 0.78rem;
        color: var(--muted);
    }

    .form-group {
        margin-bottom: 1.25rem;
        text-align: left;
        position: relative;
    }

    .form-group label {
        display: block;
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .form-group input {
        width: 100%;
        background: #faf8f4;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: var(--text);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px var(--gold-glow);
        background: var(--white);
    }

    .password-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-input-wrapper input {
        padding-right: 2.5rem;
    }

    .password-toggle-btn {
        position: absolute;
        right: 0.875rem;
        background: none;
        border: none;
        color: var(--muted);
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s;
    }

    .password-toggle-btn:hover {
        color: var(--navy);
    }

    .btn-submit {
        width: 100%;
        margin-top: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--navy);
        color: var(--gold);
        border: none;
        border-radius: 8px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.025em;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
    }

    .btn-submit:hover {
        background: var(--navy-hover);
        transform: translateY(-1px);
    }

    .btn-submit:active { transform: translateY(0); }

    .btn-submit:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
    }

    .btn-outline-small {
        background: transparent;
        border: 1.5px solid var(--gold);
        color: var(--navy);
        padding: 0.55rem 1rem;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .btn-outline-small:hover:not(:disabled) {
        background: var(--gold);
    }

    .btn-outline-small:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .resend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        gap: 0.75rem;
    }

    .resend-row .sent-to {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .error {
        color: #e57373;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* ─── STEP TRANSITIONS ─────────────────────────────── */
    .step-panel {
        animation: cardIn 0.3s cubic-bezier(0.22, 1, 0.36, 1) both;
    }
    .step-hidden {
        display: none !important;
    }
    .verified-banner {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #eefaf0;
        border: 1px solid #b7dfc5;
        color: #1e6b3a;
        font-size: 0.78rem;
        font-weight: 500;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
        margin-bottom: 1.25rem;
    }

    /* ─── PASSWORD STRENGTH INDICATOR ─────────────────── */
    .password-strength {
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .strength-bars {
        display: flex;
        gap: 3px;
        flex: 1;
    }

    .strength-bar {
        height: 4px;
        flex: 1;
        background: #e8e3d7;
        border-radius: 999px;
        transition: background-color 0.25s ease;
    }

    .strength-label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        min-width: 58px;
        text-align: right;
        transition: color 0.25s ease;
    }

    .strength-hint {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
        display: flex;
        align-items: flex-start;
        gap: 0.35rem;
        line-height: 1.4;
    }

    .strength-hint i {
        color: var(--gold-dark);
        margin-top: 0.15rem;
        font-size: 0.7rem;
    }
</style>
</x-slot:styles>

<div class="forgot-card">
    <div class="card-accent"></div>
    <div class="card-body">
        <div class="card-heading">
            <h2>Reset Password</h2>
            <p id="card-subtext">
                @if(session('reset_code_sent'))
                    Enter the verification code we sent to your email.
                @else
                    Enter the email address on your account to receive a reset code.
                @endif
            </p>
        </div>

        @if(!session('reset_code_sent'))
        {{-- STEP 1: Enter email to request code --}}
        <div class="step-panel">
            <form action="{{ route('password.email') }}" method="POST" id="sendCodeForm">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="e.g. you@example.com"
                        value="{{ old('email') }}"
                        required
                    >
                    @error('email')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-submit" style="background:transparent;border:1.5px solid var(--gold);color:var(--navy);">
                    Send Reset Code
                </button>
            </form>
        </div>
        @else
        {{-- Resend row — hidden once the code has been verified --}}
        <div class="resend-row" id="resendRow">
            <span class="sent-to">Code sent to <strong>{{ session('reset_email') }}</strong></span>
            <form action="{{ route('password.email') }}" method="POST" id="resendForm">
                @csrf
                <input type="hidden" name="email" value="{{ session('reset_email') }}">
                <button type="submit" class="btn-outline-small" id="resendBtn" disabled>
                    Resend in <span id="resendCountdown">600</span>s
                </button>
            </form>
        </div>

        @if(session('success'))
        <p style="color:#1e6b3a;font-size:0.75rem;margin-bottom:1rem;">{{ session('success') }}</p>
        @endif

        {{-- The actual submission still posts code + password together to password.update,
             but the UI only reveals the password fields once the code looks valid,
             so it reads and behaves like two separate steps. --}}
        <form action="{{ route('password.update') }}" method="POST" id="resetForm">
            @csrf
            <input type="hidden" name="user_id" value="{{ session('reset_user_id') }}">
            <input type="hidden" name="reset_email" value="{{ session('reset_email') }}">
            {{-- STEP 2a: code entry --}}
            <div class="step-panel" id="codeStep">
                <div class="form-group">
                    <label for="reset-code">Reset Verification Code</label>
                    <input type="text" id="reset-code" name="code" placeholder="6-digit code" maxlength="6"
                           inputmode="numeric" pattern="[0-9]*" required
                           value="{{ old('code') }}">
                    @error('code')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="button" class="btn-submit" id="verifyCodeBtn" style="background:transparent;border:1.5px solid var(--gold);color:var(--navy);" disabled>
                    Verify Code
                </button>
            </div>

            {{-- STEP 2b: new password (revealed only after the code step is confirmed) --}}
            <div class="step-panel step-hidden" id="passwordStep">
                <div class="verified-banner">
                    <i class="fas fa-circle-check"></i> Code confirmed — choose your new password.
                </div>

                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new-password" name="password" placeholder="Min 6 characters" minlength="6" autocomplete="new-password">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('new-password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    {{-- Password strength indicator --}}
                    <div class="password-strength" id="strengthWrap" style="display:none;">
                        <div class="strength-bars">
                            <div class="strength-bar" data-index="1"></div>
                            <div class="strength-bar" data-index="2"></div>
                            <div class="strength-bar" data-index="3"></div>
                            <div class="strength-bar" data-index="4"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>
                    <div class="strength-hint" id="strengthHint" style="display:none;">
                        <i class="fas fa-circle-info"></i>
                        <span id="strengthHintText"></span>
                    </div>

                    @error('password')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm-password" name="password_confirmation" placeholder="Re-enter your new password" minlength="6" autocomplete="new-password">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('confirm-password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="error" id="matchError" style="display:none;">Passwords do not match.</p>
                </div>

                <button type="button" class="btn-outline-small" id="backToCodeBtn" style="margin-bottom:0.75rem;">
                    <i class="fas fa-arrow-left" style="margin-right:0.35rem;"></i> Back
                </button>
                <button type="submit" class="btn-submit" id="submitResetBtn">Reset Password</button>
            </div>
        </form>
        @endif

        <div style="margin-top:1.5rem; text-align:center;">
            <a href="/" style="color:var(--text-muted); font-size:0.78rem; text-decoration:underline;">
                Back to Sign In
            </a>
        </div>
    </div>
</div>

<x-slot:scripts>
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    // ── PASSWORD STRENGTH METER ─────────────────────────
    function evaluatePasswordStrength(pwd) {
        if (!pwd) return { score: 0, label: '', color: '', hint: '' };

        let score = 0;

        // Length
        if (pwd.length >= 6)  score++;
        if (pwd.length >= 10) score++;
        if (pwd.length >= 14) score++;

        // Character variety
        const hasLower   = /[a-z]/.test(pwd);
        const hasUpper   = /[A-Z]/.test(pwd);
        const hasNumber  = /[0-9]/.test(pwd);
        const hasSpecial = /[^A-Za-z0-9]/.test(pwd);

        const varietyCount = [hasLower, hasUpper, hasNumber, hasSpecial].filter(Boolean).length;
        if (varietyCount >= 2) score++;
        if (varietyCount >= 3) score++;
        if (varietyCount >= 4) score++;

        // Cap the score to 4 levels (weak, fair, good, strong)
        let level;
        if (score <= 2)      level = 1;
        else if (score <= 4) level = 2;
        else if (score <= 5) level = 3;
        else                 level = 4;

        // Build the hint based on what's missing
        const missing = [];
        if (!hasLower)   missing.push('lowercase letter');
        if (!hasUpper)   missing.push('uppercase letter');
        if (!hasNumber)  missing.push('number');
        if (!hasSpecial) missing.push('symbol');
        if (pwd.length < 8) missing.push('8+ characters');

        let hintText = '';
        if (missing.length === 0) {
            hintText = 'Excellent — your password meets all recommended criteria.';
        } else if (missing.length <= 2) {
            hintText = 'Add a ' + missing.join(' and a ') + ' to make it stronger.';
        } else {
            hintText = 'Try adding: ' + missing.slice(0, 3).join(', ') + '.';
        }

        const map = {
            1: { label: 'Weak',   color: '#a12b2b' },
            2: { label: 'Fair',   color: '#b88d3a' },
            3: { label: 'Good',   color: '#1e6b3a' },
            4: { label: 'Strong', color: '#0f5132' },
        };

        return { level, label: map[level].label, color: map[level].color, hint: hintText };
    }

    function renderPasswordStrength(pwd) {
        const wrap     = document.getElementById('strengthWrap');
        const label    = document.getElementById('strengthLabel');
        const hintWrap = document.getElementById('strengthHint');
        const hintText = document.getElementById('strengthHintText');
        if (!wrap || !label) return;

        if (!pwd) {
            wrap.style.display = 'none';
            if (hintWrap) hintWrap.style.display = 'none';
            return;
        }

        const result = evaluatePasswordStrength(pwd);
        wrap.style.display = 'flex';

        const bars = wrap.querySelectorAll('.strength-bar');
        bars.forEach((bar, i) => {
            const idx = i + 1;
            bar.style.background = idx <= result.level ? result.color : '#e8e3d7';
        });

        label.textContent = result.label;
        label.style.color = result.color;

        if (hintWrap && hintText) {
            hintWrap.style.display = 'flex';
            hintText.textContent = result.hint;
        }
    }

    // 10-minute resend cooldown
    @if(session('reset_code_sent'))
    (function () {
        const resendBtn   = document.getElementById('resendBtn');
        const countdownEl = document.getElementById('resendCountdown');
        if (resendBtn && countdownEl) {
            let seconds = 600;
            resendBtn.disabled = true;

            const timer = setInterval(() => {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend Code';
                } else {
                    countdownEl.textContent = seconds;
                }
            }, 1000);
        }

        // ── Step 2 → Step 3 flow: code first, then password ──
        const codeInput       = document.getElementById('reset-code');
        const verifyBtn       = document.getElementById('verifyCodeBtn');
        const codeStep        = document.getElementById('codeStep');
        const passwordStep    = document.getElementById('passwordStep');
        const backBtn         = document.getElementById('backToCodeBtn');
        const newPassword     = document.getElementById('new-password');
        const confirmPassword = document.getElementById('confirm-password');
        const matchError      = document.getElementById('matchError');
        const submitBtn       = document.getElementById('submitResetBtn');
        const subtext         = document.getElementById('card-subtext');
        const resetForm       = document.getElementById('resetForm');
        const resendRow       = document.getElementById('resendRow');

        function updateVerifyEnabled() {
            verifyBtn.disabled = codeInput.value.trim().length !== 6;
        }
        codeInput.addEventListener('input', () => {
            codeInput.value = codeInput.value.replace(/\D/g, '').slice(0, 6);
            updateVerifyEnabled();
        });
        updateVerifyEnabled();

        function goToPasswordStep() {
            codeStep.classList.add('step-hidden');
            passwordStep.classList.remove('step-hidden');

            // Hide the "Code sent to ..." + Resend row once the code is verified.
            if (resendRow) resendRow.classList.add('step-hidden');

            newPassword.required = true;
            confirmPassword.required = true;
            if (subtext) subtext.textContent = 'Choose a new password for your account.';
            newPassword.focus();
        }

        function goToCodeStep() {
            passwordStep.classList.add('step-hidden');
            codeStep.classList.remove('step-hidden');

            // Restore the resend row when the user goes back to the code step.
            if (resendRow) resendRow.classList.remove('step-hidden');

            newPassword.required = false;
            confirmPassword.required = false;
            if (subtext) subtext.textContent = 'Enter the verification code we sent to your email.';
        }

        // The actual code check happens server-side on final submit — this button
        // just moves the UI forward once the code looks complete.
        verifyBtn.addEventListener('click', goToPasswordStep);
        backBtn.addEventListener('click', goToCodeStep);

        // If the server re-rendered this page with a code error, jump straight
        // back to the code step instead of showing password fields.
        @if($errors->has('code'))
            goToCodeStep();
        @elseif(old('code'))
            goToPasswordStep();
        @endif

        function checkMatch() {
            const mismatch = confirmPassword.value.length > 0 && newPassword.value !== confirmPassword.value;
            matchError.style.display = mismatch ? 'block' : 'none';
            return !mismatch;
        }
        newPassword.addEventListener('input', () => {
            checkMatch();
            renderPasswordStrength(newPassword.value);
        });
        confirmPassword.addEventListener('input', checkMatch);

        resetForm.addEventListener('submit', function (e) {
            if (passwordStep.classList.contains('step-hidden')) {
                // Safety net: never let the form submit while still on the code step.
                e.preventDefault();
                goToPasswordStep();
                return;
            }
            if (!checkMatch()) {
                e.preventDefault();
            }
        });
    })();
    @endif
</script>
</x-slot:scripts>

</x-layout>