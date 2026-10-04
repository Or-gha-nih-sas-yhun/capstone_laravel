(function () {
    'use strict';
    const _fetch = window.fetch.bind(window);
window.fetch = function () {
    return _fetch.apply(null, arguments).then(function (res) {
        if (res.status === 419 || res.status === 401) {
            try { localStorage.removeItem('capstoneLastActivity'); } catch (_) {}
            window.location.href = '/';
        }
        return res;
    });
};
    const IDLE_LIMIT_MS = 10 * 60 * 1000;   // 10 minutes
    const WARNING_MS    = 30 * 1000;       // warn 30s before logout
    const STORAGE_KEY   = 'capstoneLastActivity';
    const THROTTLE_MS   = 1000;

    let lastWrite = 0, warningShown = false, tickTimer = null;

    const now = () => Date.now();
    const getLast = () => parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10) || now();

    function markActive() {
        const t = now();
        if (t - lastWrite < THROTTLE_MS) return;
        lastWrite = t;
        try { localStorage.setItem(STORAGE_KEY, String(t)); } catch (_) {}
        if (warningShown) hideWarning();
    }

    // ── Warning dialog ──
    function showWarning() {
        if (warningShown) return;
        warningShown = true;
        const el = document.createElement('div');
        el.id = 'idle-warning';
        el.style.cssText = 'position:fixed;inset:0;z-index:10001;background:rgba(5,16,33,.55);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;padding:1rem;';
        el.innerHTML = `
            <div style="background:#fff;border-radius:1.25rem;max-width:24rem;width:100%;padding:1.75rem;text-align:center;border:1px solid rgba(214,177,92,.3);box-shadow:0 40px 60px -20px rgba(5,16,33,.3);">
                <div style="height:3px;background:linear-gradient(90deg,#d6b15c,#f0e0b0);border-radius:3px;margin-bottom:1.25rem;"></div>
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:600;color:#0a1428;margin:0 0 .5rem;">Still there?</h2>
                <p style="font-size:.875rem;color:#5b6375;margin:0 0 1.25rem;">You'll be signed out due to inactivity in <strong id="idle-countdown" style="color:#a12b2b;">30</strong> seconds.</p>
                <div style="display:flex;gap:.5rem;justify-content:center;">
                    <button type="button" id="idle-logout-now" style="background:transparent;border:none;color:#5b6375;padding:.5rem 1rem;cursor:pointer;border-radius:2rem;">Sign out</button>
                    <button type="button" id="idle-stay" style="background:#0a1428;color:#f0e0b0;border:none;padding:.6rem 1.3rem;border-radius:2rem;font-weight:500;cursor:pointer;">Stay signed in</button>
                </div>
            </div>`;
        document.body.appendChild(el);
        document.getElementById('idle-stay').addEventListener('click', () => { keepAlive(); markActive(); });
        document.getElementById('idle-logout-now').addEventListener('click', logout);
    }

    function hideWarning() {
        warningShown = false;
        document.getElementById('idle-warning')?.remove();
    }

    // Ping the server so the Laravel session doesn't expire while the user stays
        function keepAlive() {
            try { fetch('/keep-alive', { credentials: 'same-origin' }); } catch (_) {}
        }

    function logout() {
        try { localStorage.removeItem(STORAGE_KEY); } catch (_) {}
        const form = document.getElementById('logout-form');
        if (form) form.submit();
        else window.location.href = '/login';
    }

    // ── Main tick (uses timestamps, so sleeping laptops / throttled tabs still work) ──
    function tick() {
        const idle = now() - getLast();
        const remaining = IDLE_LIMIT_MS - idle;

        if (remaining <= 0) { logout(); return; }

        if (remaining <= WARNING_MS) {
            showWarning();
            const c = document.getElementById('idle-countdown');
            if (c) c.textContent = Math.ceil(remaining / 1000);
        } else if (warningShown) {
            hideWarning(); // activity happened in another tab
        }
    }


    try { localStorage.setItem(STORAGE_KEY, String(now())); } catch (_) {}

    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click', 'wheel']
        .forEach(evt => window.addEventListener(evt, markActive, { passive: true, capture: true }));

    // Scrolling inside <main> doesn't bubble to window, so capture covers it above.
    document.addEventListener('visibilitychange', () => { if (!document.hidden) tick(); });

    tickTimer = setInterval(tick, 1000);
})();