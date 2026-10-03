<x-layout>

<x-slot:styles>
<style>
    /* ─── AUTH CARD ─────────────────────────────────── */
    .auth-card {
        width: 100%;
        max-width: 440px;
        background: var(--white);
        border-radius: 20px;
        box-shadow:
            0 2px 8px  rgba(13, 27, 46, 0.06),
            0 20px 45px rgba(13, 27, 46, 0.10);
        overflow: hidden;
        border: 1px solid rgba(214, 177, 92, 0.14);
        animation: cardIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Gold accent stripe */
    .card-accent {
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold-dark));
    }

    .card-body {
        padding: 2.5rem 2.25rem 2.75rem;
    }

    /* ─── ICON BADGE ──────────────────────────────── */
    .auth-icon-badge {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.1rem;
        box-shadow: 0 10px 22px -6px rgba(10, 20, 40, 0.35), inset 0 1px 0 rgba(255,255,255,0.08);
    }

    .auth-icon-badge svg {
        width: 24px;
        height: 24px;
        stroke: var(--gold);
        stroke-width: 1.8;
        fill: none;
    }

    /* ─── HEADING ─────────────────────────────────── */
    .card-heading {
        text-align: center;
        margin-bottom: 1.9rem;
    }

    .card-heading h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.95rem;
        font-weight: 700;
        color: var(--navy);
        line-height: 1.2;
        margin-bottom: 0.4rem;
        letter-spacing: -0.01em;
    }

    .card-heading p {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    .card-heading .sub {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin-top: 0.3rem;
        padding: 0.5rem 0.9rem;
        background: var(--gold-glow);
        border-radius: 10px;
        display: inline-block;
        border: 1px solid rgba(214, 177, 92, 0.2);
    }

    .card-heading .sub strong {
        color: var(--navy);
        font-weight: 600;
    }

    /* ─── TAB SWITCHER ────────────────────────────── */
    .tab-switch {
        display: flex;
        background: #eeebe3;
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 1.75rem;
    }

    .tab-btn {
        flex: 1;
        padding: 0.55rem 1rem;
        border: none;
        border-radius: 9px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        background: transparent;
        color: var(--text-muted);
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }

    .tab-btn.active {
        background: var(--white);
        color: var(--navy);
        box-shadow: 0 1px 4px rgba(13, 27, 46, 0.1);
    }

    /* ─── TAB PANES ───────────────────────────────── */
    .tab-pane          { display: none; }
    .tab-pane.active   { display: block; animation: paneIn 0.25s ease both; }

    @keyframes paneIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── FORM ELEMENTS ───────────────────────────── */
    .form-group {
        margin-bottom: 1.15rem;
    }

    .form-group label {
        display: block;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.45rem;
    }

/* ─── SHARED INPUT WRAPPER (username, email, password, etc.) ─── */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper .input-icon {
    position: absolute;
    left: 0.9rem;
    width: 16px;
    height: 16px;
    stroke: var(--text-muted);
    stroke-width: 1.8;
    fill: none;
    pointer-events: none;
    transition: stroke 0.15s;
    z-index: 1;
}

.input-wrapper input {
    padding-left: 2.5rem;
}

.input-wrapper.has-toggle input {
    padding-right: 2.6rem;
}

.input-wrapper input:focus ~ .input-icon,
.input-wrapper input:focus + .input-icon {
    stroke: var(--gold-dark);
}

.password-toggle-btn {
    position: absolute;
    right: 0.875rem;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s;
    font-size: 0.85rem;
    z-index: 1;
}

.password-toggle-btn:hover {
    color: var(--navy);
}

    .btn-submit {
        width: 100%;
        margin-top: 0.6rem;
        padding: 0.8rem 1.5rem;
        background: var(--navy);
        color: var(--gold);
        border: none;
        border-radius: 10px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 6px 16px -4px rgba(10, 20, 40, 0.3);
        transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
    }

    .btn-submit svg {
        width: 15px;
        height: 15px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
    }

    .btn-submit:hover {
        background: var(--navy-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 22px -4px rgba(10, 20, 40, 0.4);
    }

    .btn-submit:active { transform: translateY(0); }

    /* ─── AUTHENTICATED STATE ─────────────────────── */
    .auth-welcome {
        text-align: center;
        padding: 0.5rem 0 0.75rem;
    }

    .btn-logout {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.9rem;
        padding: 0.55rem 1.3rem;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        background: transparent;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.76rem;
        font-weight: 500;
        color: var(--text-muted);
        cursor: pointer;
        transition: border-color 0.15s, color 0.15s, background 0.15s;
    }

    .btn-logout:hover {
        border-color: #e57373;
        color: #d95f5f;
        background: #fdf5f5;
    }

    .btn-logout svg {
        width: 14px; height: 14px;
        stroke: currentColor;
        stroke-width: 1.75;
        fill: none;
        flex-shrink: 0;
    }

    /* ─── INLINE ERROR MESSAGE ─────────────────────── */
    .error {
        display: flex;
        align-items: flex-start;
        gap: 0.4rem;
        color: #c0392b;
        font-size: 0.74rem;
        font-weight: 500;
        margin-top: 0.4rem;
        line-height: 1.4;
    }

    .error svg {
        width: 13px;
        height: 13px;
        stroke: #c0392b;
        stroke-width: 2.2;
        fill: none;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* ─── DIVIDER LABEL (for ID screen etc.) ────────── */
    .card-footnote {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px dashed var(--border);
        font-size: 0.72rem;
        color: var(--text-muted);
    }
</style>
</x-slot:styles>
@php
  $user = App\Models\User::where('user_id', session('user_id'))->first(); 
@endphp


@if($user)
<div class="auth-card">
        <div class="card-accent"></div>
        <div class="card-body">

            <div class="card-heading">
                <div class="auth-icon-badge">
                    @if(!is_null($user->password))
                        <svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 10V7a4 4 0 018 0v3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @else
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @endif
                </div>
                <h2>{{ $greetings }}</h2>
                @if(!is_null($user->password))
                    <p>Sign in to your account to continue.</p>
                    <p class="sub">Welcome back, <strong>{{ $user->name }}</strong> &middot; {{ $user->user_id }}</p>
                @else
                    <p>Create your account to continue.</p>
                    <p class="sub">Registering for ID <strong>{{ $user->user_id }}</strong></p>
                @endif
                <form action="{{ route('destroy.session') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        Change User
                        <svg viewBox="0 0 24 24">
                            <path d="M16 17l5-5m0 0l-5-5m5 5H9m4 5v1a2 2 0 002 2h3a2 2 0 002-2v-1m-6-10V7a2 2 0 012-2h3a2 2 0 012 2v1" />
                        </svg>
                    </button>
                </form>
            </div>

            @if(!is_null($user->password))
                {{-- ── SIGN IN ── --}}
                <div class="tab-pane active" id="tab-login">
                    <form action="/login" method="POST">
                        @csrf   

                        <div class="form-group">
                            <label for="logname">Username</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input
                                    type="text"
                                    id="logname"
                                    name="logname"
                                    class="{{ $errors->has('logname') ? 'has-error' : '' }}"
                                    placeholder="Enter your name"
                                    autocomplete="username"
                                >
                            </div>
  
                        </div>
<div class="form-group">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
        <label for="logpassword" style="margin-bottom: 0;">Password</label>
        <a href="/forgot-password" style="font-size: 0.7rem; color: #b88d3a; font-weight: 600; text-decoration: none;" class="hover:underline">Forgot Password?</a>
    </div>
    <div class="input-wrapper has-toggle">
        <svg class="input-icon" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 10V7a4 4 0 018 0v3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <input
            type="password"
            id="logpassword"
            name="logpassword"
            class="{{ $errors->has('logpassword') ? 'has-error' : '' }}"
            placeholder="••••••••"
            autocomplete="current-password"
        >
        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('logpassword', this)">
            <i class="fas fa-eye"></i>
        </button>
    </div>

</div>

                        <button type="submit" class="btn-submit">
                            Sign In
                            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </form>
                </div>
            @else
                {{-- ── REGISTER ── --}}
                <div class="tab-pane active" id="tab-register">
                    <form action="/register" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="reg-name">Username</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="text" id="reg-name" name="name" class="{{ $errors->has('name') ? 'has-error' : '' }}" placeholder="Your Username" autocomplete="name" required>
                            </div>
                            @error('name')
                            <p class="error">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12.5" stroke-linecap="round"/><circle cx="12" cy="16" r="0.5" fill="currentColor" stroke="currentColor"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reg-email">Email Address</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 7l9 6 9-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="email" id="reg-email" name="email" class="{{ $errors->has('email') ? 'has-error' : '' }}" placeholder="you@email.com" autocomplete="email" required>
                            </div>
                            @error('email')
                            <p class="error">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12.5" stroke-linecap="round"/><circle cx="12" cy="16" r="0.5" fill="currentColor" stroke="currentColor"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reg-password">Password</label>
                            <div class="input-wrapper has-toggle">
                                <svg class="input-icon" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 10V7a4 4 0 018 0v3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="password" id="reg-password" name="password" class="{{ $errors->has('password') ? 'has-error' : '' }}" placeholder="Create a password" autocomplete="new-password" required>
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('reg-password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <p class="error">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12.5" stroke-linecap="round"/><circle cx="12" cy="16" r="0.5" fill="currentColor" stroke="currentColor"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-submit">
                            Create Account
                            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
    
@else
    <div class="auth-card">
        <div class="card-accent"></div>
        <div class="card-body">
            <div class="card-heading">
                <div class="auth-icon-badge">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 8h10M7 12h10M7 16h6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h2>WELCOME TO CAPSTONE TRACKER</h2>
                <p>Please enter your School ID to continue</p>
            </div>
            <div class="tab-pane active" id="tab-login">
                <form action="/id" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="id">School ID</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="11" r="1.5"/><path d="M7 15c0.5-1.2 1.5-2 2-2s1.5 0.8 2 2M14 10h4M14 13h4" stroke-linecap="round"/></svg>
                            <input
                                type="text"
                                id="id"
                                name="id"
                                class="{{ $errors->has('id') ? 'has-error' : '' }}"
                                placeholder="Enter your ID"
                                autocomplete="off"
                            >
                        </div>
                        @error('id')
                        <p class="error">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12.5" stroke-linecap="round"/><circle cx="12" cy="16" r="0.5" fill="currentColor" stroke="currentColor"/></svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">
                        Check ID
                        <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>
            </div>

            <div class="card-footnote">
                Having trouble? Contact your administrator for assistance.
            </div>
        </div>
    </div>
@endif


<x-slot:scripts>
<script>
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = this.dataset.tab;

            // Toggle active button
            document.querySelectorAll('.tab-btn').forEach(function (b) {
                b.classList.remove('active');
            });
            this.classList.add('active');

            // Toggle active pane
            document.querySelectorAll('.tab-pane').forEach(function (p) {
                p.classList.remove('active');
            });
            document.getElementById('tab-' + tab).classList.add('active');
        });
    });

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
</script>
</x-slot:scripts>

</x-layout>