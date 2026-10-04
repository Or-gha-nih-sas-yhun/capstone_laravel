<x-layout>

<x-slot:styles>
<style>
    /* ─── VERIFY CARD ─────────────────────────────────── */
    .verify-card {
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

    /* Gold accent stripe */
    .card-accent {
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light));
    }

    .card-body {
        padding: 2.25rem 2rem 2.5rem;
    }

    /* ─── HEADING ─────────────────────────────────── */
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

    /* ─── FORM GROUPS ─────────────────────────────── */
    .form-group {
        margin-bottom: 1.25rem;
        text-align: left;
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

    /* ─── BUTTONS ─────────────────────────────────── */
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

    .error {
        color: #e57373;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
</style>
</x-slot:styles>

<div class="verify-card">
    <div class="card-accent"></div>
    <div class="card-body">
        <div class="card-heading">
            <h2>Email Verification</h2>
            <p>Please enter your email and verify it to continue.</p>
        </div>

        {{-- Step 1: Send Verification Code --}}
        <form action="{{ route('verification.send_code') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="verify-email">Email Address</label>
                <input
                    type="email"
                    id="verify-email"
                    name="email"
                    placeholder="Enter your email address"
                    value="{{ old('email', session('verified_email') ?? Auth::user()->email) }}"
                    required
                >
                @error('email')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" id="sendCodeBtn" class="btn-submit"
                    style="background:transparent;border:1.5px solid var(--gold);color:var(--navy);"
                    data-label="{{ session('code_sent') ? 'Resend Verification Code' : 'Send Verification Code' }}"
                    {{ ($remaining ?? 0) > 0 ? 'disabled' : '' }}>
                {{ session('code_sent') ? 'Resend Verification Code' : 'Send Verification Code' }}
            </button>
            @if(session('success'))
            <p style="color:#1e6b3a;font-size:0.75rem;margin-top:0.5rem;">{{ session('success') }}</p>
            @endif
        </form>

        {{-- Step 2: Enter Verification Code --}}
        @if(session('code_sent'))
        <form action="{{ route('verification.confirm') }}" method="POST" style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px dashed var(--border);">
            @csrf
            <input type="hidden" name="email" value="{{ session('verified_email') }}">
            
            <div class="form-group">
                <label for="reg-code">Verification Code</label>
                <input type="text" id="reg-code" name="code" placeholder="6-digit code" maxlength="6" required>
                @error('code')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Verify and Continue</button>
        </form>
        @endif
        
        <form action="{{ route('logout') }}" method="POST" style="margin-top:1.5rem; text-align:center;">
            @csrf
            <button type="submit" style="background:none; border:none; color:var(--text-muted); font-size:0.78rem; text-decoration:underline; cursor:pointer;">
                Sign Out / Cancel
            </button>
        </form>
    </div>
</div>
<x-slot:scripts>
<script>
(function () {
    const btn = document.getElementById('sendCodeBtn');
    if (!btn) return;
    let left = {{ (int) ($remaining ?? 0) }};
    const label = btn.dataset.label || 'Send Verification Code';

    function paint() {
        if (left > 0) {
            btn.disabled = true;
            btn.style.opacity = '.6';
            btn.style.cursor = 'not-allowed';
            btn.textContent = 'Resend available in ' + left + 's';
        } else {
            btn.disabled = false;
            btn.style.opacity = '';
            btn.style.cursor = '';
            btn.textContent = label;
        }
    }

    paint();
    if (left > 0) {
        const t = setInterval(function () {
            left--;
            paint();
            if (left <= 0) clearInterval(t);
        }, 1000);
    }

    // after the form is submitted, lock the button so it can't be double-clicked
    btn.closest('form').addEventListener('submit', function () {
        setTimeout(function () { btn.disabled = true; btn.style.opacity = '.6'; }, 0);
    });
})();
</script>
</x-slot:scripts>
</x-layout>