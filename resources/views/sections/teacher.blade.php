<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Capstone Tracker | Teacher Dashboard</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="icon" type="image/jpeg" href="{{ asset('pictures/favicon.jpg') }}">
    <script src="/js/app.js" defer></script>
 <style>
    :root {
        --navy: #0a1428;
        --navy-deep: #051021;
        --navy-hover: #162c47;
        --gold: #d6b15c;
        --gold-dark: #b88d3a;
        --gold-light: #f0e0b0;
        --gold-glow: rgba(214, 177, 92, 0.10);
        --cream: #f8f6f0;
        --cream-dark: #ece6db;
        --white: #ffffff;
        --text: #171e2c;
        --text-muted: #5b6375;
        --border: #e2dacf;
        --shadow-sm: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        --shadow-md: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0,0,0,0.02);
        --shadow-lg: 0 30px 45px -15px rgba(0, 0, 0, 0.10);
        --transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.0);
    }
    * { font-family: 'DM Sans', sans-serif; }
    body {
        background: linear-gradient(145deg, var(--cream) 0%, #f2ede2 100%);
        color: var(--text);
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--cream-dark); border-radius: 8px; }
    ::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 8px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--gold-dark); }
    ::selection { background: var(--gold); color: var(--navy); }

    .transition-smooth { transition: var(--transition); }
    .section-card { animation: fadeInUp 0.35s cubic-bezier(0.22, 1, 0.36, 1) both; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── SIDEBAR ── */
    .desktop-sidebar {
        background: linear-gradient(180deg, var(--navy-deep) 0%, var(--navy) 100%);
        border-right: 1px solid rgba(214, 177, 92, 0.18);
        box-shadow: 2px 0 30px rgba(5, 16, 33, 0.25);
    }
    .nav-link {
        border-radius: 0.75rem;
        transition: var(--transition);
    }
    .nav-link.active-link {
        background: rgba(214, 177, 92, 0.13);
        color: var(--gold) !important;
        box-shadow: inset 0 0 0 1px rgba(214, 177, 92, 0.25);
    }
    .nav-link:not(.active-link):hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--gold-light) !important;
    }

    /* ── CARDS ── */
    .stat-card {
        background: var(--white);
        border-radius: 1.25rem;
        border: 1px solid rgba(214, 177, 92, 0.14);
        box-shadow: var(--shadow-md);
        transition: var(--transition);
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg), 0 0 0 1px rgba(214, 177, 92, 0.25);
        border-color: rgba(214, 177, 92, 0.35);
    }
    .stat-card .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px -4px rgba(10, 20, 40, 0.25);
        transition: var(--transition);
    }
    .stat-card:hover .icon-circle {
        background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
        box-shadow: 0 12px 22px -4px rgba(214, 177, 92, 0.35);
    }

    .content-card {
        background: var(--white);
        border-radius: 1.25rem;
        border: 1px solid rgba(214, 177, 92, 0.12);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }
    .content-card:hover {
        box-shadow: var(--shadow-md);
        border-color: rgba(214, 177, 92, 0.22);
    }
    .content-card .card-accent {
        height: 3px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold-dark));
        border-radius: 3px 3px 0 0;
        opacity: 0;
        transition: opacity 0.35s ease;
    }
    .content-card:hover .card-accent { opacity: 1; }

    .progress-bar-bg { background: #e8e3d7; border-radius: 999px; overflow: hidden; }
    .progress-fill { border-radius: 999px; transition: width 0.6s cubic-bezier(0.22, 1, 0.36, 1); }

    .badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.02em;
    }
    .badge-gold { background-color: rgba(214,177,92,0.15); color: #8b6914; border: 1px solid rgba(214,177,92,0.3); }
    .badge-navy { background-color: rgba(10,20,40,0.08); color: var(--navy); border: 1px solid rgba(10,20,40,0.15); }
    .badge-muted { background-color: #f0ece4; color: var(--text-muted); border: 1px solid var(--border); }
    .badge-green { background-color: #e6f4ea; color: #1e6b3a; border: 1px solid #b7dfc5; }
    .badge-amber { background-color: #fef7e6; color: #8a5d0b; border: 1px solid #f5d78a; }

    .btn-primary {
        background: var(--navy);
        color: var(--gold-light);
        border: none;
        padding: 0.6rem 1.3rem;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.85rem;
        letter-spacing: 0.02em;
        transition: var(--transition);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(10,20,40,0.12);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-primary:hover { background: var(--navy-hover); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(10,20,40,0.2); color: #fff; }
    .btn-primary:active { transform: scale(0.97); }
    .btn-outline {
        background: transparent;
        border: 1.5px solid var(--gold);
        color: var(--navy);
        padding: 0.55rem 1.2rem;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.85rem;
        transition: var(--transition);
        cursor: pointer;
    }
    .btn-outline:hover { background: var(--gold); color: var(--navy); box-shadow: 0 4px 14px rgba(214,177,92,0.3); }
    .btn-ghost {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 0.5rem 0.9rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: var(--transition);
    }
    .btn-ghost:hover { background: rgba(214,177,92,0.08); color: var(--navy); }

    table { border-collapse: separate; border-spacing: 0; }
    table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        font-weight: 600;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border);
    }
    table td {
        padding: 0.85rem 0;
        border-bottom: 1px solid rgba(226,218,207,0.5);
        font-size: 0.875rem;
        color: var(--text);
    }
    table tr:last-child td { border-bottom: none; }
    table tr:hover td { background: rgba(248,246,240,0.5); }

    /* ── MODALS ── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(5,16,33,0.55);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 50;
        padding: 1rem;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: var(--white);
        border-radius: 1.25rem;
        width: 100%;
        max-width: 30rem;
        max-height: 90vh;
        overflow-y: auto;
        padding: 1.75rem;
        animation: fadeInUp 0.25s ease-out both;
        box-shadow: 0 40px 60px -20px rgba(5,16,33,0.3);
        border: 1px solid rgba(214,177,92,0.2);
    }
    .modal-box.wide { max-width: 44rem; }
    .modal-box .modal-accent {
        height: 3px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light));
        border-radius: 3px;
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-bottom: 0.3rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .form-input, .form-select {
        width: 100%;
        background: #faf8f4;
        border: 1.5px solid var(--border);
        border-radius: 0.65rem;
        padding: 0.6rem 0.85rem;
        font-size: 0.875rem;
        color: var(--text);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: 'DM Sans', sans-serif;
    }
    .form-input:focus, .form-select:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-glow); background: var(--white); }
    .form-input::placeholder { color: #c8c4bc; }
    .form-select option { background: var(--white); color: var(--text); }

    /* ── MOBILE BOTTOM NAV ── */
.mobile-bottom-nav {
    display: none !important;
}

@media (max-width: 768px) {
    .mobile-bottom-nav {
        display: flex !important;
        background: rgba(10,20,40,0.96);
        backdrop-filter: blur(12px);
        border-top: 1px solid rgba(214,177,92,0.3);
        box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
        padding-bottom: env(safe-area-inset-bottom, 0px);
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .mobile-bottom-nav::-webkit-scrollbar {
        display: none;
    }
    .mobile-nav-link {
        transition: color 0.2s;
        flex-shrink: 0;
        min-width: 60px;
    }
    main {
        padding-bottom: calc(5rem + env(safe-area-inset-bottom, 0px));
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
    @media (min-width: 769px) {
        .mobile-bottom-nav { display: none; }
    }

    h1, h2, h3, h4, .serif-heading {
        font-family: 'Cormorant Garamond', serif;
        font-weight: 600;
        letter-spacing: -0.01em;
    }
    h1 { font-size: 2rem; color: var(--navy); }
    h2 { font-size: 1.5rem; color: var(--navy); }
    h3 { font-size: 1.2rem; color: var(--navy); }
    .gold-accent-line {
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--gold), var(--gold-dark));
        border-radius: 3px;
        margin-top: 0.4rem;
    }
    .toast-container {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    max-width: 28rem;
    width: 100%;
    transform: translateX(120%);
    transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.4s ease;
    opacity: 0;
}
.toast-container.show { transform: translateX(0); opacity: 1; }
.toast-content {
    background: var(--white);
    border-radius: 1rem;
    padding: 1rem 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border-left: 5px solid var(--gold);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.toast-content i { color: var(--gold); font-size: 1.25rem; }
.toast-content .toast-message { font-size: 0.9rem; color: var(--text); font-weight: 500; flex: 1; }
.toast-content .toast-close { background: transparent; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.2rem; transition: color 0.2s; }
.toast-content .toast-close:hover { color: var(--navy); }

.profile-banner {
    height: 80px;
    background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
    border-radius: 1.25rem 1.25rem 0 0;
}
.profile-avatar-ring {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    padding: 3px;
    margin: -45px auto 0;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.profile-avatar-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: var(--navy);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold-light);
    font-weight: 700;
    font-size: 1.8rem;
    font-family: 'DM Sans', sans-serif;
}
.info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0;
    border-bottom: 1px solid rgba(226,218,207,0.6);
}
.info-row:last-child { border-bottom: none; }
.info-row .info-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: var(--gold-glow);
    color: var(--gold-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    flex-shrink: 0;
}
.info-row .info-label {
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    font-weight: 600;
}
.info-row .info-value {
    font-size: 0.875rem;
    color: var(--text);
    font-weight: 500;
}
.form-fieldset-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--gold-dark);
    font-weight: 700;
    margin-bottom: 0.9rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px dashed var(--border);
}
.security-note {
    background: #faf8f4;
    border: 1px solid var(--border);
    border-radius: 0.85rem;
    padding: 1rem 1.1rem;
}
.security-note li { display: flex; gap: 0.5rem; align-items: flex-start; }
.security-note li i { color: var(--gold-dark); margin-top: 0.2rem; font-size: 0.75rem; }
#password_strength_bar { transition: width 0.3s ease, background 0.3s ease; }

/* ── Timeline table (matches the printed schedule sheet) ── */
#viewModal .timeline-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8rem;
    z-index: 1;
}
#viewModal .timeline-table th,
#viewModal .timeline-table td {
    border: 1px solid var(--border);
    padding: 0.6rem 0.75rem;
    vertical-align: top;
    text-align: left;
}
#viewModal .timeline-table thead th {
    background: var(--gold);
    color: var(--navy);
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-size: 0.7rem;
}
#viewModal .timeline-table td:first-child {
    text-align: center;
    white-space: nowrap;
    font-weight: 600;
    color: var(--navy);
    width: 15%;
}
#viewModal .timeline-table td:nth-child(2) { width: 45%; }
#viewModal .timeline-table td:nth-child(3) { width: 40%; }

#viewModal .timeline-table .stage-divider td {
    background: var(--navy);
    color: var(--gold-light);
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-size: 0.75rem;
    padding: 0.5rem;
}

#viewModal .timeline-table tr.row-completed td { background: #f4faf6; }
#viewModal .timeline-table tr.row-next td { background: #fdf9ef; }

#viewModal .task-title {
    font-weight: 600;
    color: var(--text);
    display: block;
    margin-bottom: 0.25rem;
}
#viewModal .task-desc {
    font-size: 0.75rem;
    color: var(--text-muted);
    display: block;
}
#viewModal .task-status {
    display: inline-block;
    margin-top: 0.4rem;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.15rem 0.55rem;
    border-radius: 9999px;
}
#viewModal .task-status.completed { background: #e6f4ea; color: #1e6b3a; }
#viewModal .task-status.next { background: var(--gold); color: var(--navy); }
#viewModal .task-status.pending { background: #f0ece4; color: #8b8477; }

#viewModal .remark-line {
    display: block;
    margin-bottom: 0.3rem;
    color: var(--text);
}
#viewModal .remark-line .box {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 1.5px solid var(--text);
    margin-right: 0.4rem;
    text-align: center;
    line-height: 10px;
    font-size: 0.65rem;
    vertical-align: middle;
}
#viewModal .remark-line .box.checked {
    background: var(--navy);
    color: white;
    border-color: var(--navy);
}
#viewModal .remark-names {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-left: 1.1rem;
    display: block;
}
#viewModal .remark-empty {
    color: #b8b0a0;
    font-style: italic;
    font-size: 0.75rem;
}
#viewModal .remark-feedback {
    background: #faf8f4;
    border: 1px dashed var(--border);
    border-radius: 0.4rem;
    padding: 0.4rem 0.55rem;
    margin-top: 0.4rem;
    font-style: italic;
    font-size: 0.75rem;
    color: var(--text);
}
#viewModal .remark-summary { display:flex; flex-direction:column; gap:0.5rem; }

#viewModal .remark-status-badge {
    display:inline-flex; align-items:center; gap:0.4rem;
    padding:0.25rem 0.7rem; border-radius:9999px;
    font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.03em;
    width:fit-content;
}
#viewModal .remark-status-badge.on-time { background:#e6f4ea; color:#1e6b3a; border:1px solid #b7dfc5; }
#viewModal .remark-status-badge.late    { background:#fbe9e7; color:#a12b2b; border:1px solid #f3c1ba; }
#viewModal .remark-status-badge.early   { background:#eaf1fb; color:#1e4e8b; border:1px solid #c1d6f3; }

#viewModal .remark-deduction {
    font-size:0.72rem; color:#a12b2b; font-weight:600;
    display:flex; align-items:center; gap:0.35rem;
}
#viewModal .remark-attendance {
    font-size:0.78rem; color:var(--text);
    display:flex; align-items:center; gap:0.4rem;
}
#viewModal .remark-attendance i { color: var(--gold-dark); }

#viewModal .absence-table {
    width:100%; border-collapse:collapse; margin-top:0.35rem; font-size:0.72rem;
    border:1px solid var(--border); border-radius:0.5rem; overflow:hidden;
}
#viewModal .absence-table thead th {
    background:#faf2df; color:var(--gold-dark); text-transform:uppercase; letter-spacing:0.03em;
    font-size:0.63rem; font-weight:700; padding:0.4rem 0.6rem; text-align:left;
    border-bottom:1px solid var(--border);
}
#viewModal .absence-table td {
    padding:0.4rem 0.6rem; border-bottom:1px solid rgba(226,218,207,0.5); color:var(--text);
}
#viewModal .absence-table tr:last-child td { border-bottom:none; }
#viewModal .absence-table td:first-child { width:1.75rem; color:var(--text-muted); font-weight:600; }
/* ── Stage toggle ── */
.stage-toggle {
    user-select: none;
}
.stage-toggle .stage-toggle-btn {
    background: none;
    border: none;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    transition: var(--transition);
    cursor: pointer;
}
.stage-toggle .stage-toggle-btn:hover {
    background: rgba(214, 177, 92, 0.15);
}
.stage-toggle .stage-toggle-btn .fa-chevron-down {
    transform: rotate(0deg);
}
.stage-toggle .stage-toggle-btn .fa-chevron-up {
    transform: rotate(180deg);
}
.stage-milestones {
    transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
    overflow: hidden;
}
.stage-milestones.collapsed {
    max-height: 0;
    opacity: 0;
    margin: 0;
    padding: 0;
    pointer-events: none;
}
.stage-milestones:not(.collapsed) {
    max-height: 2000px;
    opacity: 1;
}
#viewModal .milestone-card {
    flex-direction: column;
    align-items: stretch;
}
#viewModal .milestone-card .milestone-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
#viewModal .remarks-block {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px dashed var(--border);
    font-size: 0.78rem;
    color: var(--text-muted);
}
#viewModal .remarks-block .remark-row {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.35rem;
}
#viewModal .remarks-block .remark-row i {
    font-size: 0.75rem;
    width: 14px;
}
#viewModal .remarks-block .remark-ok { color: #1e6b3a; }
#viewModal .remarks-block .remark-bad { color: #a12b2b; }
#viewModal .remarks-block .remark-feedback {
    background: #faf8f4;
    border: 1px solid var(--border);
    border-radius: 0.5rem;
    padding: 0.5rem 0.65rem;
    margin-top: 0.35rem;
    font-style: italic;
    color: var(--text);
}
#viewModal .cert-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: #faf8f4;
    border: 1px solid #e2dacf;
    border-radius: 0.65rem;
}
#viewModal .cert-row .cert-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #0a1428;
}
#viewModal .cert-row .cert-date {
    font-size: 0.7rem;
    color: #5b6375;
}

/* ── Evaluation modal (split view) ── */
#evaluationModal .modal-box {
    padding: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}
#evaluationModal .eval-modal-header {
    padding: 1.5rem 1.75rem 1.25rem;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, #fff 0%, #faf8f4 100%);
    flex-shrink: 0;
}
#evaluationModal .eval-modal-body {
    display: grid;
    grid-template-columns: 1fr;
    flex: 1;
    min-height: 0;
    overflow: hidden;
}
@media (min-width: 1024px) {
    #evaluationModal .eval-modal-body {
        grid-template-columns: 1.05fr 0.95fr;
    }
}
#evaluationModal .eval-panel {
    padding: 1.5rem 1.75rem;
    overflow-y: auto;
    min-height: 0;
}
#evaluationModal .eval-panel-left {
    border-right: 1px solid var(--border);
}
#evaluationModal .eval-panel-right {
    background: #fbfaf6;
}
#evaluationModal .eval-modal-footer {
    padding: 1.1rem 1.75rem;
    border-top: 1px solid var(--border);
    background: #faf8f4;
    display: flex;
    justify-content: flex-end;
    gap: 0.6rem;
    flex-shrink: 0;
}

#eval_revision_sheet_content .form-fieldset-title {
    font-size: 0.66rem;
    margin-bottom: 0.65rem;
}
#eval_revision_sheet_content table {
    box-shadow: var(--shadow-sm);
    border-radius: 0.6rem;
    overflow: hidden;
}
#eval_revision_sheet_content thead tr {
    background: #faf2df !important;
}
#eval_revision_sheet_content .empty-state {
    text-align: center;
    padding: 1.75rem 1rem;
    color: var(--text-muted);
    font-size: 0.8rem;
    background: #faf8f4;
    border: 1px dashed var(--border);
    border-radius: 0.75rem;
}
/* ── Rubric Scores modal ── */
#rubricScoresModal .rs-card {
    background: #faf8f4;
    border: 1px solid var(--border);
    border-radius: 0.85rem;
    padding: 1.1rem 1.25rem;
    transition: var(--transition);
}
#rubricScoresModal .rs-card:hover {
    border-color: rgba(214, 177, 92, 0.4);
    box-shadow: var(--shadow-sm);
}
#rubricScoresModal .rs-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}
#rubricScoresModal .rs-panelist {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
#rubricScoresModal .rs-panelist-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
    color: var(--gold-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    flex-shrink: 0;
}
#rubricScoresModal .rs-panelist-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--navy);
}
#rubricScoresModal .rs-milestone {
    font-size: 0.72rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-weight: 600;
}
#rubricScoresModal .rs-score {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e6b3a;
    line-height: 1;
    white-space: nowrap;
}
#rubricScoresModal .rs-score small {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.7rem;
    font-weight: 500;
    color: var(--text-muted);
}
#rubricScoresModal .rs-date {
    font-size: 0.7rem;
    color: var(--text-muted);
    margin-top: 0.15rem;
    text-align: right;
}
#rubricScoresModal .rs-criteria-toggle {
    font-size: 0.72rem;
    color: var(--gold-dark);
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    margin-top: 0.75rem;
}
#rubricScoresModal .rs-criteria-toggle:hover { color: #8b6914; }
#rubricScoresModal .rs-criteria-table {
    margin-top: 0.6rem;
    width: 100%;
    font-size: 0.75rem;
    border-collapse: collapse;
}
#rubricScoresModal .rs-criteria-table th {
    text-align: left;
    color: var(--text-muted);
    font-weight: 600;
    padding-bottom: 0.4rem;
    border-bottom: 1px solid var(--border);
    text-transform: uppercase;
    font-size: 0.62rem;
    letter-spacing: 0.03em;
}
#rubricScoresModal .rs-criteria-table td {
    padding: 0.45rem 0;
    border-bottom: 1px solid rgba(226,218,207,0.5);
    color: var(--text);
}
#rubricScoresModal .rs-feedback {
    margin-top: 0.75rem;
    padding: 0.65rem 0.85rem;
    background: #fff;
    border-left: 3px solid var(--gold);
    border-radius: 0.4rem;
    font-style: italic;
    font-size: 0.78rem;
    color: var(--text);
}
#rubricScoresModal .rs-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--text-muted);
}
#rubricScoresModal .rs-empty i {
    font-size: 2rem;
    color: #d8d2c4;
    display: block;
    margin-bottom: 0.6rem;
}
.btn-check-primary {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    color: var(--navy);
    border: none;
    font-weight: 700;
    letter-spacing: 0.02em;
    padding: 0.65rem 1.4rem;
    border-radius: 0.75rem;
    box-shadow: 0 4px 14px rgba(214, 177, 92, 0.35), inset 0 1px 0 rgba(255,255,255,0.3);
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.82rem;
}
.btn-check-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(214, 177, 92, 0.45), inset 0 1px 0 rgba(255,255,255,0.3);
}
.btn-check-primary:active { transform: scale(0.97); }
.btn-check-primary i { font-size: 0.9rem; }
#viewModal .remark-edit-mode textarea { resize: vertical; }
#viewModal .remark-edit-mode .form-label { margin-bottom: 2px; }

/* ══════════════════════════════════════════════════════════════ */
/*  PROFESSIONAL LOADING SYSTEM                                    */
/* ══════════════════════════════════════════════════════════════ */

/* ── Shared spinner ── */
.spinner {
    width: 38px; height: 38px; border-radius: 50%;
    border: 3px solid rgba(214, 177, 92, 0.18);
    border-top-color: var(--gold);
    animation: loader-spin .7s linear infinite;
}
@keyframes loader-spin { to { transform: rotate(360deg); } }

/* ── In-modal spinner (smaller, for modal bodies) ── */
.spinner-sm {
    width: 30px; height: 30px; border-radius: 50%;
    border: 2.5px solid rgba(214, 177, 92, 0.18);
    border-top-color: var(--gold);
    animation: loader-spin .7s linear infinite;
}

/* ── 1. First-paint splash screen ── */
#app-splash {
    position: fixed; inset: 0; z-index: 10000;
    display: flex; align-items: center; justify-content: center;
    background: radial-gradient(120% 120% at 50% 0%, #162c47 0%, #0a1428 45%, #051021 100%);
    transition: opacity .5s cubic-bezier(.4, 0, .2, 1), visibility .5s;
}
#app-splash.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
#app-splash::after {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background: radial-gradient(60% 50% at 50% 45%, rgba(214, 177, 92, .12), transparent 70%);
}
.splash-inner {
    position: relative; z-index: 1;
    display: flex; flex-direction: column; align-items: center; text-align: center;
    padding: 2rem;
}
.splash-logo { position: relative; width: 92px; height: 92px; display: grid; place-items: center; margin-bottom: 1.5rem; }
.splash-ring {
    position: absolute; inset: 0; border-radius: 50%;
    border: 2px solid rgba(214, 177, 92, .15);
    border-top-color: var(--gold);
    animation: splash-spin 1s linear infinite;
}
.splash-ring::after {
    content: ''; position: absolute; inset: 9px; border-radius: 50%;
    border: 2px solid transparent;
    border-bottom-color: rgba(214, 177, 92, .5);
    animation: splash-spin 1.5s linear infinite reverse;
}
.splash-mark {
    width: 56px; height: 56px; border-radius: 18px;
    display: grid; place-items: center;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    color: var(--navy); font-size: 1.4rem;
    box-shadow: 0 12px 30px -8px rgba(214, 177, 92, .55);
    animation: splash-pulse 2.2s ease-in-out infinite;
}
@keyframes splash-spin { to { transform: rotate(360deg); } }
@keyframes splash-pulse {
    0%, 100% { transform: scale(1); }
    50%      { transform: scale(1.05); }
}
.splash-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.85rem; font-weight: 600; letter-spacing: .02em; color: #fff;
}
.splash-sub {
    font-size: .68rem; letter-spacing: .24em; text-transform: uppercase;
    color: var(--gold-light); opacity: .7; margin-top: .4rem;
}
.splash-bar {
    width: 230px; height: 3px; border-radius: 999px;
    background: rgba(255, 255, 255, .08);
    overflow: hidden; margin-top: 1.75rem;
}
#splash_bar_fill {
    display: block; height: 100%; width: 0%; border-radius: 999px;
    background: linear-gradient(90deg, var(--gold-dark), var(--gold), var(--gold-light));
    transition: width .35s ease;
}
.splash-status {
    font-size: .7rem; color: rgba(255, 255, 255, .42);
    margin-top: .9rem; letter-spacing: .05em;
    min-height: 1em;
}

/* ── 2. Top route progress bar ── */
#route-progress {
    position: fixed; top: 0; left: 0; right: 0; height: 3px;
    z-index: 9997; pointer-events: none;
    opacity: 0; transition: opacity .25s ease;
}
#route-progress.active { opacity: 1; }
#route-progress-fill {
    height: 100%; width: 0%;
    background: linear-gradient(90deg, var(--gold-dark), var(--gold), var(--gold-light));
    box-shadow: 0 0 10px rgba(214, 177, 92, .7), 0 0 4px rgba(214, 177, 92, .5);
    transition: width .25s ease;
}

/* ── 3. Overlay loader ── */
#page-loader {
    position: fixed; inset: 0; z-index: 9998;
    background: rgba(248, 246, 240, .68);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; visibility: hidden; pointer-events: none;
    transition: opacity .22s ease, visibility .22s ease;
}
#page-loader.active { opacity: 1; visibility: visible; pointer-events: all; }
#page-loader .loader-box {
    display: flex; flex-direction: column; align-items: center; gap: .9rem;
    padding: 1.75rem 2.5rem;
    border-radius: 1.25rem;
    background: rgba(255, 255, 255, .94);
    border: 1px solid rgba(214, 177, 92, .25);
    box-shadow: 0 30px 60px -20px rgba(5, 16, 33, .35);
    animation: fadeInUp .25s cubic-bezier(.22, 1, .36, 1) both;
}
#page-loader .loader-label {
    font-size: .7rem; letter-spacing: .16em; text-transform: uppercase;
    color: var(--text-muted); font-weight: 700;
}

/* ── 4. Skeleton shimmer ── */
.skeleton {
    position: relative; overflow: hidden;
    background: #ece5d8; border-radius: 8px;
}
.skeleton::after {
    content: ''; position: absolute; inset: 0;
    transform: translateX(-100%);
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .75), transparent);
    animation: skeleton-shimmer 1.25s infinite;
}
@keyframes skeleton-shimmer { 100% { transform: translateX(100%); } }

/* ── 5. Button loading state ── */
.btn-primary.is-loading,
.btn-outline.is-loading,
.btn-check-primary.is-loading,
.btn-ghost.is-loading {
    pointer-events: none; opacity: .78; cursor: progress;
}
.btn-primary.is-loading i,
.btn-outline.is-loading i,
.btn-check-primary.is-loading i { animation: loader-spin .7s linear infinite; }

/* ── Reduced motion ── */
@media (prefers-reduced-motion: reduce) {
    .splash-ring, .splash-ring::after, .splash-mark, .spinner, .spinner-sm,
    .skeleton::after, .btn-primary.is-loading i { animation: none !important; }
}
/* ── RECOMMENDATION SHEET DOCUMENT ── */
.recommendation-sheet-modal {
    display: flex; flex-direction: column;
    min-height: 85vh; max-height: 92vh;
    background: var(--white); padding: 1.5rem !important;
}
.recommendation-document {
    flex: 1; background: #fffdf8;
    border: 2px solid #0a1428; border-radius: 4px;
    padding: 2rem 2.75rem 1.5rem;
    display: flex; flex-direction: column; text-align: center;
    position: relative; overflow-y: auto;
    box-shadow: inset 0 0 0 1px rgba(10, 20, 40, 0.05);
}
.recommendation-document .rec-header-image { text-align: center; margin-bottom: 1.1rem; }
.recommendation-document .rec-header-image img { max-width: 55%; height: auto; display: inline-block; }
.recommendation-document .rec-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.75rem; font-weight: 700; letter-spacing: 0.08em;
    text-transform: uppercase; color: #0a1428;
    border-bottom: 2px solid #0a1428; padding-bottom: 0.6rem;
    margin: 0 auto 0.4rem; max-width: 90%;
}
.recommendation-document .rec-serial {
    font-family: 'Courier New', monospace; font-size: 0.78rem;
    letter-spacing: 0.12em; color: #8b6914; margin-bottom: 1.5rem; min-height: 1em;
}
.recommendation-document .rec-body {
    flex: 1; font-size: 1rem; line-height: 1.75;
    color: #171e2c; max-width: 640px; margin: 0 auto;
    display: flex; flex-direction: column; justify-content: center; gap: 0.65rem;
}
.recommendation-document .rec-body p { margin: 0; font-size: 1rem; line-height: 1.75; color: #171e2c; }
.recommendation-document .rec-capstone-title {
    font-family: 'Cormorant Garamond', serif; font-size: 1.45rem;
    font-weight: 600; font-style: italic; line-height: 1.35;
    color: #0a1428; margin: 0.6rem auto 1.1rem; max-width: 620px; padding: 0 1rem;
}
.recommendation-document .rec-capstone-title::before,
.recommendation-document .rec-capstone-title::after {
    content: ''; display: block; width: 55px; height: 1px;
    background: #d9cda6; margin: 0.6rem auto;
}
.recommendation-document .rec-capstone-title::before { margin-top: 0; }
.recommendation-document .rec-capstone-title::after  { margin-bottom: 0; }
.recommendation-document .rec-members { font-weight: 600; color: #0a1428; }
.recommendation-document .rec-signature { margin: 2.25rem auto 1rem; text-align: center; min-width: 320px; }
.recommendation-document .rec-signature .rec-sig-name {
    display: inline-block; min-width: 260px; padding: 0 0.5rem 0.3rem;
    border-bottom: 1.5px solid #0a1428; font-size: 1.05rem;
    font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: #0a1428;
}
.recommendation-document .rec-signature .rec-sig-label {
    display: block; margin-top: 0.4rem; font-size: 0.72rem;
    letter-spacing: 0.14em; text-transform: uppercase; color: #5b6375;
}
.recommendation-document .rec-footer {
    display: flex; justify-content: space-between; align-items: flex-end;
    gap: 2rem; margin-top: auto; padding-top: 1.1rem; border-top: 1px solid #e2dacf;
}
.recommendation-document .rec-footer-block { text-align: center; min-width: 150px; }
.recommendation-document .rec-footer-value { font-size: 0.9rem; font-weight: 600; color: #171e2c; }
.recommendation-document .rec-footer-label {
    font-size: 0.65rem; text-transform: uppercase;
    letter-spacing: 0.12em; color: #9a9385; margin-top: 0.15rem;
}
@media (max-width: 640px) {
    .recommendation-document { padding: 1.25rem 1rem; }
    .recommendation-document .rec-title { font-size: 1.35rem; letter-spacing: 0.05em; }
    .recommendation-document .rec-capstone-title { font-size: 1.15rem; }
    .recommendation-document .rec-header-image img { max-width: 80%; }
    .recommendation-document .rec-footer { flex-direction: column; align-items: center; gap: 1rem; }
}

/* ── APPROVAL SHEET DOCUMENT ── */
.approval-sheet-doc {
    color: #171e2c; background: #fff; font-family: 'Times New Roman', serif;
    text-align: center; line-height: 1.35;
}
.approval-sheet-doc .approval-intro { font-size: .9rem; margin: 0 0 .35rem; }
.approval-sheet-doc .approval-title {
    font-size: 1.05rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .025em; margin: 0 auto 1rem; max-width: 42rem;
}
.approval-sheet-doc .approval-body { font-size: .9rem; max-width: 42rem; margin: 0 auto 1.35rem; }
.approval-signature { text-align: center; margin: 0 auto 1.15rem; }
.approval-signature .approval-sig-line {
    display: inline-block; min-width: 2.45in; border-bottom: 1px solid #222;
    font-weight: 700; text-transform: uppercase; font-size: .85rem;
    padding-bottom: .18rem; margin-bottom: .2rem;
}
.approval-signature .approval-sig-role { font-size: .72rem; color: #444; }
.approval-panel-heading { font-weight: 700; font-size: .85rem; margin: 0 0 .85rem; }
.approval-panel-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.5rem;
    max-width: 34rem; margin: 0 auto 1rem;
}
.approval-panel-grid .approval-signature { margin: 0; }
.approval-panel-grid .approval-sig-line { width: 100%; min-width: 0; font-size: .78rem; }
.approval-accepted { max-width: 39rem; margin: 0 auto 1.1rem; font-size: .88rem; }
.approval-oral-results {
    display: flex; flex-direction: column; align-items: center;
    gap: .25rem; margin-bottom: 1rem; font-size: .86rem;
}
.approval-oral-results .oral-label { margin-right: .35rem; }
.approval-oral-results .oral-value { border-bottom: 1px solid #222; font-weight: 700; padding-bottom: .05rem; }
.approval-approved-label { margin: 0 0 .25rem; font-size: .86rem; }
</style>
</head>
<body class="bg-[#f8f6f0] text-[#171e2c]">

<!-- ══════════════ FIRST-PAINT SPLASH ══════════════ -->
<div id="app-splash" role="status" aria-live="polite" aria-label="Loading Capstone Tracker">
    <div class="splash-inner">
        <div class="splash-logo">
            <div class="splash-ring"></div>
            <div class="splash-mark"><i class="fas fa-graduation-cap"></i></div>
        </div>
        <h1 class="splash-title">Capstone Tracker</h1>
        <p class="splash-sub">Teacher Portal</p>
        <div class="splash-bar"><span id="splash_bar_fill"></span></div>
        <p class="splash-status" id="splash_status">Initializing workspace…</p>
    </div>
</div>

<!-- ══════════════ TOP ROUTE PROGRESS BAR ══════════════ -->
<div id="route-progress" aria-hidden="true"><div id="route-progress-fill"></div></div>

<!-- ══════════════ OVERLAY LOADER ══════════════ -->
<div id="page-loader" role="status" aria-live="polite">
    <div class="loader-box">
        <div class="spinner"></div>
        <span class="loader-label">Loading…</span>
    </div>
</div>

<div id="toast" class="toast-container">
    <div class="toast-content">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage" class="toast-message">Operation successful!</span>
        <button type="button" class="toast-close" onclick="hideToast()">&times;</button>
    </div>
</div>

<!-- ======================= SIDEBAR (DESKTOP) ======================= -->
<aside class="desktop-sidebar hidden md:flex fixed left-0 top-0 h-full w-64 flex-col justify-between z-20">
    <div>
        <div class="p-6 flex items-center space-x-3 border-b border-[rgba(214,177,92,0.12)]">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md flex-shrink-0"
                 style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-sm text-white tracking-[0.5px]">Capstone Tracker</span>
                <small class="text-[10px] font-semibold tracking-[1px] uppercase mt-0.5" style="color: var(--gold-light); opacity: 0.85;">Teacher</small>
            </div>
        </div>
        <nav class="mt-6 px-4 space-y-1">
            <a href="#" data-section="dashboard" class="nav-link active-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[#d6b15c]">
                <i class="fas fa-th-large w-4"></i> <span>Dashboard</span>
            </a>
            <a href="#" data-section="assignedsections" class="nav-link active-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[#d6b15c]">
                <i class="fas fa-th-large w-4"></i> <span>View as Instructor</span>
            </a>
            <a href="#" data-section="sections" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                <i class="fas fa-layer-group w-4"></i> <span>View as Adviser</span>
            </a>
            <a href="#" data-section="evaluate" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                <i class="fas fa-door-open w-4"></i> <span>View as Panelist</span>
            </a>
            <a href="#" data-section="profile" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                <i class="fa-regular fa-user w-4"></i> <span>Profile</span>
            </a>
        </nav>
    </div>
    <div class="p-4 border-t border-[rgba(214,177,92,0.15)]">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-lg"
                 style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                {{ strtoupper(substr($teacher->teacher_first_name ?? $user->name ?? 'T', 0, 2)) }}
            </div>
            <div>
                <p class="text-sm font-medium text-white">{{ $teacher->teacher_first_name ?? '' }} {{ $teacher->teacher_last_name ?? $user->name ?? 'Teacher' }}</p>
                <p class="text-[11px]" style="color:rgba(255,255,255,0.5);">{{ $teacher->teacher_email ?? $user->email ?? 'No email' }}</p>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center space-x-2 text-sm text-[rgba(255,255,255,0.5)] hover:text-[#f0e0b0] transition">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

<!-- ======================= MOBILE BOTTOM NAV ======================= -->
<div class="mobile-bottom-nav fixed bottom-0 left-0 right-0 py-2 px-2 justify-around items-center z-30 flex md:hidden">
    <a href="#" data-section="dashboard" class="mobile-nav-link flex flex-col items-center text-[#d6b15c] text-xs py-1">
        <i class="fas fa-th-large text-lg"></i><span class="text-[10px] mt-1">Home</span>
    </a>
    <a href="#" data-section="assignedsections" class="mobile-nav-link flex flex-col items-center text-[#d6b15c] text-xs py-1">
        <i class="fas fa-th-large text-lg"></i><span class="text-[10px] mt-1">View as Instructor</span>
    </a>
    <a href="#" data-section="sections" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
        <i class="fas fa-layer-group text-lg"></i><span class="text-[10px] mt-1">View as Adviser</span>
    </a>
    <a href="#" data-section="evaluate" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
        <i class="fa-regular fa-pen-to-square text-lg"></i><span class="text-[10px] mt-1">View as Panelist</span>
    </a>
    <a href="#" data-section="profile" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
        <i class="fa-regular fa-user text-lg"></i><span class="text-[10px] mt-1">Profile</span>
    </a>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="mobile-nav-link flex flex-col items-center text-red-400 hover:text-red-300 text-xs py-1">
        <i class="fas fa-sign-out-alt text-lg"></i><span class="text-[10px] mt-1">Sign Out</span>
    </a>
</div>

<!-- ======================= MAIN CONTENT ======================= -->
<main class="ml-0 md:ml-64 p-4 md:p-8 overflow-y-auto max-h-screen pb-20">

<!-- ==================== DASHBOARD ==================== -->
    <div id="dashboard-section" class="section-container section-card max-w-7xl mx-auto">
        <div class="mb-8">
            <h1>Teacher Dashboard</h1>
            <div class="gold-accent-line"></div>
            <p class="text-[#5b6375] mt-2 text-sm mb-4">Welcome back, {{ $teacher->teacher_first_name ?? $user->name ?? 'Teacher' }}! You are handling {{ $totalGroups ?? 0 }} groups with {{ $totalStudents ?? 0 }} students.</p>

            <div class="space-y-3">
                @if($teacherSections->isNotEmpty())
                <div class="p-4 bg-[#faf8f4] border border-[#d6b15c] rounded-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white flex-shrink-0" style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);">
                            <i class="fas fa-columns"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-[#0a1428] uppercase tracking-wide">Assigned Section(s)</h4>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($teacherSections as $section)
                                <span class="badge badge-gold font-medium">
                                    <i class="fas fa-chalkboard mr-1"></i> {{ $section->section_name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-[#5b6375] italic">
                        You can manage these sections and create groups within them.
                    </div>
                </div>
                @else
                <div class="p-4 bg-[#faf8f4] border border-[#e2dacf] rounded-xl flex items-center space-x-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-[#5b6375] bg-[#f0ece4] flex-shrink-0">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-[#5b6375] uppercase tracking-wide">No Assigned Sections</h4>
                        <p class="text-xs text-[#5b6375] mt-0.5">Contact the administrator to assign sections to your account.</p>
                    </div>
                </div>
                @endif

                @if($assignedRooms->isNotEmpty())
                <div class="p-4 bg-[#faf8f4] border border-[#d6b15c] rounded-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white flex-shrink-0" style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%); shadow: 0 4px 10px rgba(10,20,40,0.15);">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-[#0a1428] uppercase tracking-wide">Assigned Evaluation Classrooms</h4>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($assignedRooms as $room)
                                <span class="badge badge-gold font-medium">
                                    <i class="fas fa-location-dot mr-1"></i> {{ $room->room_name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-[#5b6375] italic flex-shrink-0">
                        You can only see and evaluate the groups assigned to these rooms.
                    </div>
                </div>
                @else
                <div class="p-4 bg-[#faf8f4] border border-[#e2dacf] rounded-xl flex items-center space-x-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-[#5b6375] bg-[#f0ece4] flex-shrink-0">
                        <i class="fas fa-door-closed"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-[#5b6375] uppercase tracking-wide">No Assigned Classrooms</h4>
                        <p class="text-xs text-[#5b6375] mt-0.5">You have not been assigned to any evaluation classrooms/rooms yet.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('groups')">
                <div><p class="text-[#5b6375] text-xs font-medium">Total Groups</p><p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ $totalGroups ?? 0 }}</p></div>
                <div class="icon-circle text-[#d6b15c]"><i class="fas fa-layer-group text-lg"></i></div>
            </div>
            <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('students')">
                <div><p class="text-[#5b6375] text-xs font-medium">Total Students</p><p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ $totalStudents ?? 0 }}</p></div>
                <div class="icon-circle text-[#d6b15c]"><i class="fa-regular fa-user text-lg"></i></div>
            </div>
            <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('evaluations')">
                <div><p class="text-[#5b6375] text-xs font-medium">Evaluations</p><p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ $totalEvaluations ?? 0 }}</p></div>
                <div class="icon-circle text-[#d6b15c]"><i class="fa-regular fa-circle-check text-lg"></i></div>
            </div>
            <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('sections')">
                <div><p class="text-[#5b6375] text-xs font-medium">My Sections</p><p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ count($teacherSections ?? []) }}</p></div>
                <div class="icon-circle text-[#d6b15c]"><i class="fas fa-columns text-lg"></i></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="content-card">
                <div class="card-accent"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6"><h3>Group Progress</h3><i class="fas fa-chart-simple text-[#b8b0a0]"></i></div>
                    <div class="space-y-4">
                        @php
    $incompleteGroupProgress = collect($groupProgress ?? [])->filter(fn($gp) => ($gp->progress ?? 0) < 100);
                @endphp

                @forelse($incompleteGroupProgress as $gp)
                    <div class="p-3 bg-[#faf8f4] rounded-lg border border-[#e2dacf]">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-sm text-[#0a1428]">{{ $gp->group_name }}</p>
                                <p class="text-xs text-[#5b6375]">{{ $gp->capstone_title }}</p>
                            </div>
                            <span class="text-sm font-bold text-[#b88d3a]">{{ $gp->progress }}%</span>
                        </div>
                        <div class="progress-bar-bg h-2.5 w-full">
                            <div class="progress-fill h-full" style="width:{{ $gp->progress }}%; background: var(--gold);"></div>
                        </div>
                        <p class="text-xs text-[#5b6375] mt-1">{{ $gp->completed }} / {{ $gp->total }} milestones</p>
                    </div>
                @empty
                    <div class="text-center py-8 text-[#5b6375]">
                        <i class="fa-regular fa-folder-open text-3xl mb-2"></i>
                        <p>No groups in progress</p>
                    </div>
                @endforelse
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-accent"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6"><h3>Completed Groups</h3><i class="fa-regular fa-file-lines text-[#b8b0a0]"></i></div>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @php
                           $enabledMilestoneIdsForCompleted = $milestones->pluck('id')->toArray();
                                $completedGroups = ($adviserGroups ?? collect())->filter(function ($g) use ($milestones, $enabledMilestoneIdsForCompleted) {
                                    $completed = $g->groupMilestones
                                        ->where('status', 'completed')
                                        ->whereIn('milestone_id', $enabledMilestoneIdsForCompleted)
                                        ->count();
                                    $total = $milestones->count() ?: 1;
                                    return round(($completed / $total) * 100) >= 100;
                                });
                        @endphp
                        @forelse($completedGroups as $cg)
                            @php
                                $cgEvaluations = \App\Models\Evaluation::where('group_id', $cg->id)
                                    ->with('milestone')
                                    ->orderByDesc('evaluation_date')
                                    ->get();
                                $cgSection = $cg->students->first()->section ?? 'No Section';
                            @endphp
                            <div class="p-4 bg-[#faf8f4] rounded-xl border border-[#e2dacf] hover:border-[#d6b15c] transition">
                                <div class="flex justify-between items-start gap-2 mb-2">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-sm text-[#0a1428]">{{ $cg->group_name }}</span>
                                            <span class="badge badge-navy text-[9px]">{{ $cgSection }}</span>
                                            <span class="badge badge-green text-[9px]"><i class="fa-solid fa-circle-check mr-1"></i> 100% Complete</span>
                                        </div>
                                        <p class="text-xs text-[#5b6375] mt-0.5">{{ $cg->capstone_title }}</p>
                                    </div>
                                   @if($cg->revision_status == 'needs_revision')
                                        <span class="badge badge-amber text-[9px] whitespace-nowrap"><i class="fas fa-spinner fa-spin mr-1"></i> Needs Revision</span>
                                    @elseif($cg->revision_status == 'revised')
                                        <span class="badge badge-green text-[9px] whitespace-nowrap"><i class="fas fa-check-double mr-1"></i> Revised</span>
                                    @else
                                        <button type="button"
                                            onclick="openGroupRevisionsModal({{ $cg->id }}, '{{ addslashes($cg->group_name) }}')"
                                           class="badge bg-green-600 text-white text-[9px] whitespace-nowrap hover:bg-green-700 transition cursor-pointer">
    <i class="fas fa-file-lines mr-1"></i> View Revisions
                                        </button>
                                    @endif
                                </div>

                                @if($cg->students->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($cg->students as $mem)
                                        <span class="badge badge-muted text-[9px]"><i class="fa-regular fa-user text-[8px] mr-1"></i>{{ $mem->student_first_name }} {{ $mem->student_last_name }}</span>
                                    @endforeach
                                </div>
                                @endif

                                @if($cgEvaluations->isNotEmpty())
                                <div class="space-y-1.5 pt-2 border-t border-dashed border-[#e2dacf]">
                                    @foreach($cgEvaluations as $ev)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-[#5b6375]">{{ $ev->milestone->milestone_title ?? 'Milestone' }}</span>
                                            <span class="font-bold text-[#1e6b3a]">{{ $ev->score }}/{{ $ev->max_score }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-[11px] text-[#5b6375] italic pt-2 border-t border-dashed border-[#e2dacf]">No panelist scores recorded yet.</p>
                                @endif

                                <div class="flex justify-end mt-2">
                                    <button onclick="openRubricScoresModal({{ $cg->id }}, '{{ addslashes($cg->group_name) }}')" class="text-[#b88d3a] hover:text-[#8b6914] text-xs font-semibold transition">
                                        <i class="fas fa-star mr-1"></i>View Full Scores
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-[#5b6375]"><i class="fa-regular fa-pen-to-square text-3xl mb-2"></i><p>No groups have completed the capstone yet</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

         <div class="content-card">
            <div class="card-accent"></div>
            <div class="p-6">
                <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
                    <h3>All Assigned Groups</h3>
                    <span class="text-xs text-[#5b6375]" id="ag_group_count">{{ $adviserGroups->count() ?? 0 }} groups</span>
                </div>

                <div class="flex flex-wrap gap-3 mb-5">
                    <div class="relative flex-1 min-w-[200px]">
                        <input type="text" id="ag_search_input" class="form-input text-xs w-full pl-9 py-2" placeholder="Search by group, title...">
                        <i class="fas fa-search absolute left-3 top-2.5 text-[#b8b0a0] text-xs"></i>
                    </div>
                    <select id="ag_section_filter" class="form-select text-xs w-48">
                        <option value="All">All Sections</option>
                        @foreach($allSections ?? [] as $section)
                            <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="ag_group_list" class="space-y-3">
                    @forelse($adviserGroups ?? [] as $group)
                    @php
                        $teacherRevision = \App\Models\Revision::with([
                            'documentation',
                            'enhancements',
                            'objectives'
                        ])
                        ->where('group_id', $group->id)
                        ->where('panelist_id', $teacher->id)
                        ->first();

                        $revisionComplete = false;
                        if ($teacherRevision) {
                            $allDocumentationComplete = $teacherRevision->documentation
                                ->every(fn($item) => strtolower(trim($item->remarks ?? '')) === 'completed');
                            $allEnhancementsComplete = $teacherRevision->enhancements
                                ->every(fn($item) => strtolower(trim($item->remarks ?? '')) === 'completed');
                            $allObjectivesComplete = $teacherRevision->objectives
                                ->every(fn($item) => strtolower(trim($item->remarks ?? '')) === 'completed');

                            $hasRevisionItems = $teacherRevision->documentation->isNotEmpty()
                                || $teacherRevision->enhancements->isNotEmpty()
                                || $teacherRevision->objectives->isNotEmpty();

                            $revisionComplete = $hasRevisionItems
                                && $allDocumentationComplete
                                && $allEnhancementsComplete
                                && $allObjectivesComplete;
                        }

                        $hasEvaluated = false;
                        if ($teacherRevision && $group->room && $group->room->required_milestone_id) {
                            $hasEvaluated = \App\Models\Evaluation::where('group_id', $group->id)
                                ->where('milestone_id', $group->room->required_milestone_id)
                                ->where('teacher_id', $teacher->id)
                                ->exists();
                        }
                    @endphp
                        @php
                            $completed = $group->groupMilestones
                                ->where('status','completed')
                                ->whereIn('milestone_id', $milestones->pluck('id'))
                                ->count();
                            $total = $milestones->count() ?: 1;
                            $progress = round(($completed/$total)*100);
                            $section = $group->students->first()->section ?? 'No Section';
                        @endphp
                        @continue($progress >= 100)
                          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-[#faf8f4] border border-[#e2dacf] rounded-xl text-sm group-item ag-row transition hover:shadow-sm" data-search="{{ strtolower($group->group_name . ' ' . $group->capstone_title . ' ' . $section) }}" data-ag-section="{{ $section }}">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-[#0a1428] text-base">{{ $group->group_name }}</span>
                                    <span class="badge badge-navy text-[9px]">{{ $section }}</span>
                                    <span class="text-xs text-[#5b6375]">• {{ $group->students->count()??0 }} members</span>
                                </div>
                                <span class="text-xs text-[#3d4450]">{{ $group->capstone_title }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-[#5b6375]">Progress:</span>
                                    <div class="w-24 progress-bar-bg h-1.5">
                                        <div class="progress-fill h-1.5" style="width:{{ $progress }}%; background:var(--gold);"></div>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $progress }}%</span>
                                </div>

                            </div>
                           @php
                                $isPanelist = $group->room && $group->room->panelists->contains($teacher->id);
                            @endphp
                            <div class="mt-3 sm:mt-0 flex gap-2">
                                <button onclick="window.openViewModal({{ $group->id }})" class="btn-check-primary">
                                    <i class="fas fa-chart-line"></i> Check Progress
                                </button>

                                @if($teacherRevision)
                                    @if($revisionComplete)
                                        <button
                                            type="button"
                                            onclick="window.openEvaluationModal({{ $group->id }})"
                                            class="btn-primary text-xs px-3 py-1.5 rounded-lg flex items-center gap-1.5"
                                            style="background-color:#15803d; border-color:#15803d;"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                            {{ $hasEvaluated ? 'Evaluated' : 'Evaluate' }}
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            onclick="openRevisionCheckModal(
                                                {{ $group->id }},
                                                '{{ addslashes($group->group_name) }}',
                                                '{{ addslashes($group->capstone_title) }}'
                                            )"
                                            class="btn-primary text-xs px-3 py-1.5 rounded-lg bg-green-700 hover:bg-green-800"
                                            style="background-color:#15803d;"
                                        >
                                            <i class="fas fa-check-double"></i>
                                            Verify
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i>No groups assigned</div>
                    @endforelse
                </div>

                <p id="ag_no_results" class="hidden text-center py-8 text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i>No groups in progress found  </p>

                <div id="ag_pagination" class="flex justify-center items-center gap-2 mt-5 pt-4 border-t border-[#e2dacf]"></div>
            </div>
        </div>

    </div>
 <!-- ==================== ASSIGNED SECTIONS ==================== -->
    <div id="assignedsections-section" class="section-container hidden section-card max-w-7xl mx-auto">
        <div class="mb-8 ">
            <h1>Assigned Section</h1>
            <div class="gold-accent-line">
                </div>
                <p class="text-[#5b6375] mt-2 text-sm">Manage handled sections</p>
        </div>
        <div class="grid grid-cols-1 gap-6">
            @forelse($teacherSections ?? [] as $section)
                @php
                    $groupsInSection = \App\Models\Group::where('section_id', $section->id)->where('is_archived', false)->with(['students', 'groupMilestones', 'room'])->get();
                    $sectionStudents = \App\Models\Student::where('section', $section->section_name)->with(['user', 'groups'])->get();
                @endphp
                <div class="content-card">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <div class="flex flex-wrap justify-between items-center mb-4 gap-2 border-b border-[#faf8f4] pb-2">
                            <div class="flex items-center gap-2.5">
                                <button type="button" onclick="toggleSectionCollapse('{{ $section->id }}')" class="text-[#b8b0a0] hover:text-[#0a1428] transition focus:outline-none w-6 h-6 flex items-center justify-center rounded-full hover:bg-[#faf8f4]">
                                    <i class="fas fa-chevron-down transform transition-transform duration-200 text-xs" id="collapse_icon_{{ $section->id }}"></i>
                                </button>
                                <h3 class="text-lg font-bold text-[#0a1428] cursor-pointer select-none" onclick="toggleSectionCollapse('{{ $section->id }}')">{{ $section->section_name }}</h3>
                            </div>
                        </div>

                        <!-- Collapsible Content container -->
                        <div id="section_collapsible_content_{{ $section->id }}" class="space-y-4">
                            <div class="flex flex-wrap justify-between items-center mb-2 gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex bg-[#faf8f4] border border-[#e2dacf] rounded-lg p-0.5 text-[10px]">
                                        <button type="button" onclick="toggleSectionView('as', '{{ $section->id }}', 'groups')" id="as_toggle_btn_{{ $section->id }}_groups" class="px-2.5 py-1 rounded-md font-semibold text-[#b88d3a] bg-white shadow-sm transition-all focus:outline-none">
                                            <i class="fas fa-layer-group mr-1"></i> Groups ({{ $groupsInSection->count() }})
                                        </button>
                                        <button type="button" onclick="toggleSectionView('as', '{{ $section->id }}', 'students')" id="as_toggle_btn_{{ $section->id }}_students" class="px-2.5 py-1 rounded-md font-semibold text-[#5b6375] hover:text-[#0a1428] transition-all focus:outline-none">
                                            <i class="fas fa-user-graduate mr-1"></i> Students ({{ $sectionStudents->count() }})
                                        </button>
                                    </div>
                                </div>
                                <select id="as_group_filter_{{ $section->id }}" class="form-select text-xs w-44 as-group-filter" data-section-id="{{ $section->id }}">
                                    <option value="All">Filter by Group: All</option>
                                    @foreach($groupsInSection as $gfg)
                                        <option value="{{ $gfg->group_name }}">{{ $gfg->group_name }}</option>
                                    @endforeach
                                    <option value="Unassigned">Unassigned</option>
                                </select>
                            </div>

                            <!-- Groups View -->
                            <div id="as_section_groups_view_{{ $section->id }}" class="space-y-3">
                               @forelse($groupsInSection as $g)
                                    <div class="p-3 bg-[#faf8f4] rounded-lg border border-[#e2dacf]">
                                        <div class="flex justify-between items-center">
                                            <div><p class="font-semibold text-sm text-[#0a1428]">{{ $g->group_name }}</p><p class="text-xs text-[#5b6375]">{{ Str::limit($g->capstone_title,25) }}</p></div>
                                            <div class="text-right flex flex-col items-end gap-1">
                                                <span class="text-xs text-[#5b6375]">{{ $g->students->count()??0 }} students</span>
                                                <div class="flex gap-2">
                                                    <button onclick="openRubricScoresModal({{ $g->id }}, '{{ addslashes($g->group_name) }}')" class="text-[#b88d3a] hover:text-[#8b6914] text-xs font-semibold transition">
                                                        <i class="fas fa-star mr-1"></i>Rubric Scores
                                                    </button>
                                                    <button onclick="openViewModal({{ $g->id }})" class="text-[#5b6375] hover:text-[#0a1428] text-xs font-medium transition">
                                                        <i class="fa-regular fa-eye mr-1"></i>{{ $g->adviser_id == $teacher->id ? 'Check' : 'Show' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        @if($g->students->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-2 pt-2 border-t border-dashed border-[#e2dacf]">
                                            @foreach($g->students as $mem)
                                                <span class="badge badge-muted text-[9px]"><i class="fa-regular fa-user text-[8px] mr-1"></i>{{ $mem->student_first_name }} {{ $mem->student_last_name }}</span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-[#5b6375] text-sm bg-[#faf8f4]/50 border border-[#e2dacf] border-dashed rounded-lg"><i class="fa-regular fa-folder-open mr-1.5"></i> No groups in this section</div>
                                @endforelse
                            </div>

                            <!-- Students View (Table of Students) -->
                            <div id="as_section_students_view_{{ $section->id }}" class="hidden overflow-x-auto border border-[#e2dacf] rounded-lg bg-white">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-[11px] border-b border-[#e2dacf]">
                                            <th class="p-2 pl-3">Student Name</th>
                                            <th class="p-2">User ID</th>
                                            <th class="p-2">Group</th>
                                            <th class="p-2">Contact</th>
                                            <th class="p-2 pr-3 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#faf1e0] text-[11px]">
                                        @forelse($sectionStudents as $st)
                                            @php
                                                $stGroup = $groupsInSection->first(fn($g) => $g->students->contains($st->id));
                                            @endphp
                                              <tr class="hover:bg-[#faf8f4]/30 as-student-row" data-group="{{ $stGroup->group_name ?? 'Unassigned' }}">
                                                <td class="p-2 pl-3 font-semibold text-[#0a1428]">{{ $st->student_first_name }} {{ $st->student_last_name }}</td>
                                                <td class="p-2 text-[#5b6375] font-mono">{{ $st->user->user_id ?? 'N/A' }}</td>
                                                <td class="p-2">
                                                    @if($stGroup)
                                                        <span class="badge badge-navy text-[9px] px-1.5 py-0.5"><i class="fas fa-layer-group text-[8px] mr-0.5"></i> {{ $stGroup->group_name }}</span>
                                                    @else
                                                        <span class="badge badge-muted text-[9px] px-1.5 py-0.5">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td class="p-2 text-[#5b6375]">
                                                    <div class="flex flex-col text-[9px]">
                                                        <span>{{ $st->student_email }}</span>
                                                        @if($st->contact_number)
                                                            <span class="text-gray-400 mt-0.5">{{ $st->contact_number }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="p-2 pr-3 text-right">
                                                    @if($stGroup)
                                                        <button onclick="openRubricScoresModal({{ $stGroup->id }}, '{{ addslashes($stGroup->group_name) }}')" class="text-[#b88d3a] hover:text-[#8b6914] text-[11px] font-semibold transition">
                                                            <i class="fas fa-star mr-1"></i>Rubric Scores
                                                        </button>
                                                    @else
                                                        <span class="text-gray-400 italic text-[10px]">No Group</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="p-6 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-xl mb-1 block"></i> No students registered in this section.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="content-card col-span-2 p-8 text-center"><i class="fa-regular fa-folder-open text-4xl text-[#b8b0a0] mb-3"></i><h3>No Section Assigned</h3><p class="text-[#5b6375] text-sm">Contact the admin to get sections assigned.</p></div>
            @endforelse
        </div>
    </div>

<!-- ==================== ASSIGNED GROUPS ==================== -->
    <div id="sections-section" class="section-container hidden section-card max-w-7xl mx-auto">
        <div class="mb-8">
            
            <h1>Assigned Groups</h1>
            <div class="gold-accent-line"></div>
            <p class="text-[#5b6375] mt-2 text-sm">Manage handled sections</p>
            
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($sectionsWithGroups ?? [] as $section)
                @php $groupsInSection = $adviserGroups->where('section_id', $section->id); @endphp
                <div class="content-card">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3>{{ $section->section_name }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-[#5b6375]">{{ $groupsInSection->count() }} groups</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @forelse($groupsInSection as $g)
                                <div class="p-3 bg-[#faf8f4] rounded-lg border border-[#e2dacf]">
                                    <div class="flex justify-between items-center">
                                        <div><p class="font-semibold text-sm text-[#0a1428]">{{ $g->group_name }}</p><p class="text-xs text-[#5b6375]">{{ Str::limit($g->capstone_title,25) }}</p></div>
                                        <div class="text-right flex flex-col items-end gap-1">
                                            <span class="text-xs text-[#5b6375]">{{ $g->students->count()??0 }} students</span>
                                            <div class="flex gap-2">
                                                @if($g->adviser_id == $teacher->id)
                                                    <button onclick="openViewModal({{ $g->id }})" class="btn-check-primary !py-1.5 !px-3 !text-[11px]">
                                                        <i class="fas fa-chart-line"></i> Check
                                                    </button>
                                                @else
                                                    <button onclick="openViewModal({{ $g->id }})" class="text-[#5b6375] hover:text-[#0a1428] text-xs font-medium transition">
                                                        <i class="fa-regular fa-eye mr-1"></i>Show
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($g->students->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mt-2 pt-2 border-t border-dashed border-[#e2dacf]">
                                        @foreach($g->students as $mem)
                                            <span class="badge badge-muted text-[9px]"><i class="fa-regular fa-user text-[8px] mr-1"></i>{{ $mem->student_first_name }} {{ $mem->student_last_name }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                        @empty
                                            <div class="text-center py-4 text-[#5b6375] text-sm"><i class="fa-regular fa-folder-open mr-1"></i> No groups in this section</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="content-card col-span-2 p-8 text-center"><i class="fa-regular fa-folder-open text-4xl text-[#b8b0a0] mb-3"></i><h3>No Groups Assigned</h3><p class="text-[#5b6375] text-sm">Contact the admin to get sections assigned.</p></div>
                        @endforelse
                    </div>
    </div>
<!-- ==================== CLASSROOMS ==================== -->
<div id="evaluate-section" class="section-container hidden section-card max-w-7xl mx-auto">
    <div class="mb-8">
        <h1>Classrooms</h1>
        <div class="gold-accent-line"></div>
        <p class="text-[#5b6375] mt-2 text-sm">Your assigned evaluation classroom, and other classrooms you can join with a code from the admin.</p>
    </div>

    @php
        $assignedRoomIds = $assignedRooms->pluck('id')->toArray();
        $unassignedRooms = $allRooms->reject(fn($r) => in_array($r->id, $assignedRoomIds));
    @endphp

    @if($assignedRooms->isNotEmpty())
        <h3 class="text-lg font-bold text-[#0a1428] mb-4 flex items-center gap-2">
            <i class="fas fa-check-circle text-[#1e6b3a]"></i> Your Classrooms
        </h3>
        <div class="space-y-4 mb-8">
            @foreach($assignedRooms as $room)
                <div class="content-card w-full">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);">
                                    <i class="fas fa-door-open text-[#d6b15c]"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-[#0a1428]">{{ $room->room_name }}</h3>
                                    <p class="text-xs text-[#5b6375]">
                                        {{ $room->groups->count() }} group(s) assigned
                                    </p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($room->panelists as $p)
                                            <span class="badge badge-navy">{{ $p->teacher_first_name }} {{ $p->teacher_last_name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <span class="badge badge-green"><i class="fas fa-check-circle mr-1"></i> Joined</span>
                        </div>

                        <div class="mt-4 pt-4 border-t border-[#e2dacf]">
                            @php
                                $revisedGroups = $room->groups->where('revision_status', 'revised');
                                $normalGroups = $room->groups->where('revision_status', '!=', 'revised');
                            @endphp

                            <!-- REVISED GROUPS SECTION -->
                            @if($revisedGroups->isNotEmpty())
                                <div class="mb-4 p-4 bg-green-50/50 border border-green-200 rounded-xl">
                                    <h4 class="text-sm font-bold text-green-800 mb-3 flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-600"></i> Revised Groups (Taps to Evaluate)
                                    </h4>
                                    <div class="flex flex-col gap-3 w-full">
                                        @foreach($revisedGroups as $rg)
                                            @php
                                                $rgSection = $rg->students->first()->section ?? 'No Section';
                                                $searchData = strtolower($rg->group_name . ' ' . ($rg->capstone_title ?? '') . ' ' . $rgSection);
                                                $teacher = \App\Models\Teacher::where('user_id', Auth::user()->user_id)->first();
                                                $rgHasEvaluated = $teacher ? \App\Models\Evaluation::where('group_id', $rg->id)
                                                    ->where('milestone_id', $room->required_milestone_id)
                                                    ->where('teacher_id', $teacher->id)
                                                    ->exists() : false;
                                            @endphp
                                            <div onclick="window.openEvaluationModal({{ $rg->id }})" class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-white border border-green-200 rounded-xl text-sm group-item transition hover:shadow-md cursor-pointer hover:border-green-400 w-full" data-search="{{ $searchData }}">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="font-bold text-[#0a1428] text-base">{{ $rg->group_name }}</span>
                                                        <span class="badge badge-green text-[9px]">{{ $rgSection }}</span>
                                                        <span class="badge badge-navy text-[9px]">Revised</span>
                                                        @if($rgHasEvaluated)
                                                            <span class="badge badge-green text-[9px]"><i class="fas fa-check-circle mr-1"></i> Already Evaluated</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-[#3d4450]">{{ $rg->capstone_title ?? 'No title' }}</span>
                                                    @if($rg->revision_description)
                                                        <span class="text-xs text-[#5b6375] italic block mt-1"><span class="font-semibold">Revision Instructions:</span> "{{ $rg->revision_description }}"</span>
                                                    @endif
                                                    @if($rg->students->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                                        @foreach($rg->students as $mem)
                                                            <span class="badge badge-muted text-[9px]"><i class="fa-regular fa-user text-[8px] mr-1"></i>{{ $mem->student_first_name }} {{ $mem->student_last_name }}</span>
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="mt-3 sm:mt-0 flex gap-2">
                                                    @if($rgHasEvaluated)
                                                        <button class="btn-outline text-xs px-4 py-2 rounded-lg flex items-center gap-1.5 focus:outline-none transition shadow-sm text-green-700 hover:text-green-800 hover:border-green-800" style="border-color: #15803d; color: #15803d; background: transparent;">
                                                            <i class="fas fa-check-double"></i> Re-evaluate Revised Group
                                                        </button>
                                                    @else
                                                        <button class="btn-primary text-xs px-4 py-2 rounded-lg flex items-center gap-1.5 focus:outline-none transition shadow-sm bg-green-700 hover:bg-green-800" style="background-color: #15803d; border-color: #15803d;">
                                                            <i class="fa-regular fa-pen-to-square"></i> Evaluate Revised Group
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-between items-center mb-2">
                                <p class="text-xs font-semibold text-[#5b6375] uppercase tracking-wide">Groups in this room</p>
                                <input type="text" class="room-group-filter form-input text-xs py-1 px-2 w-48"
                                       placeholder="Search by group, title, or section…" data-room-id="{{ $room->id }}">
                            </div>
                            <div class="flex flex-col gap-3 w-full room-group-list" data-room-id="{{ $room->id }}">
                                @forelse($normalGroups as $g)
                                    @php
                                    $section = $g->students->first()->section ?? 'No Section';

                                    $searchData = strtolower(
                                        $g->group_name . ' ' .
                                        ($g->capstone_title ?? '') . ' ' .
                                        $section
                                    );

                                    $teacher = \App\Models\Teacher::where(
                                        'user_id',
                                        Auth::user()->user_id
                                    )->first();

                                    $hasEvaluated = $teacher
                                        ? \App\Models\Evaluation::where(
                                            'group_id',
                                            $g->id
                                        )
                                        ->where(
                                            'milestone_id',
                                            $room->required_milestone_id
                                        )
                                        ->where(
                                            'teacher_id',
                                            $teacher->id
                                        )
                                        ->exists()
                                        : false;

                                    $teacherRevision = $teacher
                                        ? \App\Models\Revision::with([
                                            'documentation',
                                            'enhancements',
                                            'objectives'
                                        ])
                                        ->where('group_id', $g->id)
                                        ->where('panelist_id', $teacher->id)
                                        ->first()
                                        : null;

                                    $revisionComplete = false;

                                    if ($teacherRevision) {

                                        $allDocumentationComplete =
                                            $teacherRevision->documentation
                                                ->every(function ($item) {

                                                    return strtolower(
                                                        trim($item->remarks ?? '')
                                                    ) === 'completed';

                                                });

                                        $allEnhancementsComplete =
                                            $teacherRevision->enhancements
                                                ->every(function ($item) {

                                                    return strtolower(
                                                        trim($item->remarks ?? '')
                                                    ) === 'completed';

                                                });

                                        $allObjectivesComplete =
                                            $teacherRevision->objectives
                                                ->every(function ($item) {

                                                    return strtolower(
                                                        trim($item->remarks ?? '')
                                                    ) === 'completed';

                                                });

                                        $hasRevisionItems =
                                            $teacherRevision->documentation->isNotEmpty() ||
                                            $teacherRevision->enhancements->isNotEmpty() ||
                                            $teacherRevision->objectives->isNotEmpty();

                                        $revisionComplete =
                                            $hasRevisionItems &&
                                            $allDocumentationComplete &&
                                            $allEnhancementsComplete &&
                                            $allObjectivesComplete;
                                    }
                                @endphp
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-[#faf8f4] border border-[#e2dacf] rounded-xl text-sm group-item transition hover:shadow-sm w-full" data-search="{{ $searchData }}"
                                             data-group-id="{{ $g->id }}"
                                              data-group-name="{{ addslashes($g->group_name) }}">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-[#0a1428] text-base">{{ $g->group_name }}</span>
                                                <span class="badge badge-navy text-[9px]">{{ $section }}</span>
                                                <span class="text-xs text-[#5b6375]">• {{ $g->students->count() ?? 0 }} members</span>
                                                @if($g->revision_status == 'needs_revision')
                                                    <span class="badge badge-yellow text-[9px]"><i class="fas fa-spinner fa-spin mr-1"></i> Awaiting Revision</span>
                                                @endif
                                                @if($hasEvaluated)
                                                    <span class="badge badge-green text-[9px]"><i class="fas fa-check-circle mr-1"></i> Already Evaluated</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-[#3d4450]">{{ $g->capstone_title ?? 'No title' }}</span>
                                            @if($g->revision_status == 'needs_revision' && $g->revision_description)
                                                <span class="text-xs text-[#5b6375] italic block mt-1"><span class="font-semibold">Revision Instructions:</span> "{{ $g->revision_description }}"</span>
                                            @endif
                                            @if($g->students->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mt-1.5">
                                                @foreach($g->students as $mem)
                                                    <span class="badge badge-muted text-[9px]"><i class="fa-regular fa-user text-[8px] mr-1"></i>{{ $mem->student_first_name }} {{ $mem->student_last_name }}</span>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                        <div class="mt-3 sm:mt-0 flex gap-2">
                                            <div class="mt-3 sm:mt-0 flex gap-2">

    @if($teacherRevision)

        @if($revisionComplete)

           @if($hasEvaluated)
                <button type="button" onclick="window.openMyEvaluationModal({{ $g->id }})"
                    class="btn-outline text-xs px-4 py-2 rounded-lg flex items-center gap-1.5"
                    style="border-color:#15803d; color:#15803d;">
                    <i class="fas fa-eye"></i> View Evaluation
                </button>

                <button onclick="window.openViewRevisionModal({{ $g->id }})"
                        class="btn-outline text-xs px-3 py-1.5 rounded-lg border-[#d6b15c] text-[#8b6914]">
                    <i class="fas fa-file-alt mr-1"></i> View Revision
                </button>

            @else
                <button type="button" onclick="window.openEvaluationModal({{ $g->id }})"
                    class="btn-primary text-xs px-4 py-2 rounded-lg flex items-center gap-1.5"
                    style="background-color:#15803d; border-color:#15803d;">
                    <i class="fa-regular fa-pen-to-square"></i> Evaluate Group
                </button>
            @endif

        @else

            <button
                type="button"
                onclick="openRevisionCheckModal(
                    {{ $g->id }},
                    '{{ addslashes($g->group_name) }}',
                    '{{ addslashes($g->capstone_title) }}'
                )"
                class="
                    btn-outline
                    text-xs
                    px-3
                    py-1.5
                    rounded-lg
                    flex
                    items-center
                    gap-1
                    focus:outline-none
                    transition
                    shadow-sm
                "
                style="
                    border-color:#d97706;
                    color:#d97706;
                "
            >
                <i class="fas fa-edit"></i>

                Check Revision Notes
            </button>

        @endif

    @else

        @if($hasEvaluated)

            <button
                type="button"
                onclick="window.openEvaluationModal({{ $g->id }})"
                class="
                    btn-outline
                    text-xs
                    px-4
                    py-2
                    rounded-lg
                    flex
                    items-center
                    gap-1.5
                    evaluate-btn
                "
                style="
                    border-color:#15803d;
                    color:#15803d;
                "
                data-group="{{ $g->id }}"
            >
                <i class="fas fa-check-double"></i>

                show evaluation Group
            </button>

        @else

            <button
                type="button"
                onclick="window.openEvaluationModal({{ $g->id }})"
                class="
                    btn-primary
                    text-xs
                    px-4
                    py-2
                    rounded-lg
                    flex
                    items-center
                    gap-1.5
                    evaluate-btn
                "
                data-group="{{ $g->id }}"
            >
                <i class="fa-regular fa-pen-to-square"></i>

                Evaluate Group
            </button>
                    <button
            type="button"
            onclick="window.openRevisionModal(
                {{ $g->id }},
                '{{ addslashes($g->group_name) }}',
                '{{ addslashes($g->capstone_title) }}'
            )"
            class="
                btn-outline
                text-xs
                px-4
                py-2
                rounded-lg
                flex
                items-center
                gap-1.5
            "
            style="
                border-color:#d97706;
                color:#d97706;
            "
        >
            <i class="fas fa-undo-alt"></i>

            Revision
        </button>
        @endif

    @endif

    </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-[#5b6375] col-span-full text-center py-2">No groups assigned to this room yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($unassignedRooms->isNotEmpty())
        <h3 class="text-lg font-bold text-[#0a1428] mb-4 flex items-center gap-2">
            <i class="fas fa-lock text-[#8a5d0b]"></i> Available Classrooms
        </h3>
        <div class="space-y-4">
            @foreach($unassignedRooms as $room)
                <div class="content-card w-full opacity-80 hover:opacity-100 transition">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:#f0ece4;">
                                    <i class="fas fa-door-closed text-[#b8b0a0]"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-[#0a1428]">{{ $room->room_name }}</h3>
                                    <p class="text-xs text-[#5b6375]">
                                        {{ $room->groups->count() }} group(s) assigned
                                    </p>
                                    @if($room->panelists->isNotEmpty())
                                        <p class="text-[11px] text-[#5b6375] mt-1">
                                            <strong>Panelists:</strong>
                                            {{ $room->panelists->map(fn($p) => $p->teacher_first_name . ' ' . $p->teacher_last_name)->join(', ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @php
                                $isTeacherAlreadyAssigned = $assignedRooms->isNotEmpty();
                                $roomIsFull = $room->panelists->count() >= 5;
                                $isDisabled = $isTeacherAlreadyAssigned || $roomIsFull;
                            @endphp
                            @if($isDisabled)
                                <div class="text-right text-xs font-semibold uppercase tracking-wider text-[#5b6375] bg-[#f5f1e8] border border-[#e2dacf] px-4 py-2.5 rounded-lg flex items-center gap-1.5">
                                    <i class="fas fa-ban text-red-500"></i>
                                    @if($roomIsFull)
                                        Classroom Full
                                    @elseif($isTeacherAlreadyAssigned)
                                        Already Assigned
                                    @endif
                                </div>
                            @else
                                <form class="join-room-form flex gap-2" data-room-id="{{ $room->id }}" action="{{ route('teacher.join_room') }}" method="POST">
                                    @csrf
                                    <input type="text" name="join_code" class="form-input join-code-input flex-1 text-sm" placeholder="6-char code" maxlength="6" required style="text-transform:uppercase; min-width:120px;">
                                    <button type="submit" class="btn-primary text-xs px-4 whitespace-nowrap">
                                        <i class="fas fa-key mr-1"></i> Join
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($allRooms->isEmpty())
        <div class="content-card col-span-3 p-8 text-center">
            <i class="fa-regular fa-folder-open text-4xl text-[#b8b0a0] mb-3"></i>
            <h3>No Classrooms Created Yet</h3>
            <p class="text-[#5b6375] text-sm">Ask the admin to create evaluation classrooms.</p>
        </div>
    @endif


    <div  class="section-container hidden section-card max-w-7xl mx-auto">
        <div class="mb-8 flex flex-wrap justify-between items-center gap-4">
            <div><h1>Assigned Groups</h1><div class="gold-accent-line"></div><p class="text-[#5b6375] mt-2 text-sm">Manage handled sections</p></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($sectionsWithGroups ?? [] as $section)
                @php $groupsInSection = $groups->where('section_id', $section->id);
                @endphp
                <div class="content-card">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3>{{ $section->section_name }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-[#5b6375]">{{ $groupsInSection->count() }} groups</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @forelse($groupsInSection as $g)
                                <div class="p-3 bg-[#faf8f4] rounded-lg border border-[#e2dacf]">
                                    <div class="flex justify-between items-center">
                                        <div><p class="font-semibold text-sm text-[#0a1428]">{{ $g->group_name }}</p><p class="text-xs text-[#5b6375]">{{ Str::limit($g->capstone_title,25) }}</p></div>
                                        <div class="text-right flex flex-col items-end gap-1">
                                            <span class="text-xs text-[#5b6375]">{{ $g->students->count()??0 }} students</span>
                                            <div class="flex gap-2">
                                                <button onclick="openViewModal({{ $g->id }})" class="text-[#5b6375] hover:text-[#0a1428] text-xs font-medium transition">
                                                    <i class="fa-regular fa-eye mr-1"></i>{{ $g->adviser_id == $teacher->id ? 'Check' : 'Show' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                        @empty
                                            <div class="text-center py-4 text-[#5b6375] text-sm"><i class="fa-regular fa-folder-open mr-1"></i> No groups in this section</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="content-card col-span-2 p-8 text-center"><i class="fa-regular fa-folder-open text-4xl text-[#b8b0a0] mb-3"></i><h3>No Groups Assigned</h3><p class="text-[#5b6375] text-sm">Contact the admin to get sections assigned.</p></div>
                        @endforelse
                    </div>
    </div>

</div>
    <!-- ==================== PROFILE ==================== -->
<div id="profile-section" class="section-container hidden section-card max-w-7xl mx-auto">
    <div class="mb-8"><h1>Profile</h1><div class="gold-accent-line"></div><p class="text-[#5b6375] mt-2 text-sm">Manage your personal information and contact details</p></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Profile summary / identity card -->
        <div class="content-card lg:col-span-1 overflow-hidden">
            <div class="profile-banner"></div>
            <div class="p-6 -mt-12 flex flex-col items-center text-center">
                <div class="profile-avatar-ring">
                    <div class="profile-avatar-inner">
                        {{ strtoupper(substr($teacher->teacher_first_name ?? $user->name ?? 'T', 0, 2)) }}
                    </div>
                </div>
                <h2 class="mt-4">{{ $teacher->teacher_first_name ?? '' }} {{ $teacher->teacher_last_name ?? '' }}</h2>
                <p class="text-[#5b6375] text-xs font-medium tracking-wide uppercase mt-0.5">Capstone Adviser</p>
                <p class="text-[#5b6375] text-sm mt-1">ID: {{ $teacher->user_id ?? $user->user_id ?? '—' }}</p>

                <div class="mt-4 flex flex-wrap gap-2 justify-center">
                    <span class="badge badge-gold"><i class="fas fa-layer-group mr-1"></i>{{ $totalGroups ?? 0 }} Groups</span>
                    <span class="badge badge-navy"><i class="fa-regular fa-user mr-1"></i>{{ $totalStudents ?? 0 }} Students</span>
                </div>

                <div class="mt-5 w-full pt-5 border-t border-[#e2dacf] space-y-1">
                    <div class="info-row">
                        <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="text-left">
                            <p class="info-label">Contact</p>
                            <p class="info-value">{{ $teacher->contact_number ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fa-regular fa-envelope"></i></div>
                        <div class="text-left">
                            <p class="info-label">Email</p>
                            <p class="info-value break-all">{{ $teacher->teacher_email ?? $user->email ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit profile form -->
        <div class="content-card lg:col-span-2">
            <div class="card-accent"></div>
            <div class="p-6">
                <h3 class="mb-5 flex items-center gap-2"><i class="fa-regular fa-pen-to-square text-[#d6b15c]"></i> Edit Profile</h3>
                <form action="{{ route('teacher.profile_update') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <p class="form-fieldset-title"><i class="fa-regular fa-user"></i> Personal Information</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="form-label">First Name</label><input type="text" name="teacher_first_name" value="{{ old('teacher_first_name', $teacher->teacher_first_name ?? '') }}" class="form-input"></div>
                            <div><label class="form-label">Last Name</label><input type="text" name="teacher_last_name" value="{{ old('teacher_last_name', $teacher->teacher_last_name ?? '') }}" class="form-input"></div>
                            <div><label class="form-label">Middle Name</label><input type="text" name="teacher_middle_name" value="{{ old('teacher_middle_name', $teacher->teacher_middle_name ?? '') }}" class="form-input"></div>
                            <div><label class="form-label">Teacher ID</label><input type="text" disabled value="{{ $teacher->user_id ?? $user->user_id ?? '' }}" class="form-input opacity-70 cursor-not-allowed"></div>
                        </div>
                    </div>

                    <div>
                        <p class="form-fieldset-title"><i class="fa-regular fa-address-card"></i> Contact Details</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="form-label">Contact Number</label><input type="text" name="contact_number" value="{{ old('contact_number', $teacher->contact_number ?? '') }}" class="form-input" placeholder="e.g. 09XX XXX XXXX"></div>
                            <div><label class="form-label">Email</label><input type="email" name="teacher_email" value="{{ old('teacher_email', $teacher->teacher_email ?? $user->email ?? '') }}" class="form-input"></div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2 border-t border-[#e2dacf]">
                        <button type="submit" class="btn-primary"><i class="fa-regular fa-floppy-disk mr-2"></i>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Security -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="content-card lg:col-span-2">
            <div class="card-accent"></div>
            <div class="p-6">
                <h3 class="mb-5 flex items-center gap-2"><i class="fa-solid fa-key text-[#d6b15c]"></i> Change Password</h3>
                <form action="{{ route('teacher.update_password') }}" method="POST" class="space-y-4" id="passwordForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="form-label">Username</label>
                            <input type="text" value="{{ $user->name ?? '' }}" class="form-input bg-[#e8e3d7] cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="form-label">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" class="form-input pr-10" placeholder="Enter current password" required>
                                <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" data-target="current_password"><i class="fa-regular fa-eye"></i></button>
                            </div>
                            @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">New Password</label>
                            <div class="relative">
                                <input type="password" name="new_password" id="new_password" class="form-input pr-10" placeholder="Min 6 characters" required>
                                <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" data-target="new_password"><i class="fa-regular fa-eye"></i></button>
                            </div>
                            <div class="mt-2 h-1.5 rounded-full bg-[#e8e3d7] overflow-hidden"><div id="password_strength_bar" class="h-full w-0 rounded-full"></div></div>
                            @error('new_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Confirm New Password</label>
                            <div class="relative">
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-input pr-10" placeholder="Re-enter new password" required>
                                <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" data-target="new_password_confirmation"><i class="fa-regular fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2 border-t border-[#e2dacf]"><button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk mr-2"></i>Update Password</button></div>
                </form>
            </div>
        </div>

        <!-- Security tips -->
        <div class="content-card lg:col-span-1">
            <div class="card-accent"></div>
            <div class="p-6">
                <h3 class="mb-4 flex items-center gap-2"><i class="fa-solid fa-shield-halved text-[#d6b15c]"></i> Account Security</h3>
                <div class="security-note">
                    <ul class="space-y-2.5 text-xs text-[#5b6375]">
                        <li><i class="fa-solid fa-circle-check"></i> Use at least 8 characters with a mix of letters and numbers.</li>
                        <li><i class="fa-solid fa-circle-check"></i> Avoid reusing passwords from other accounts.</li>
                        <li><i class="fa-solid fa-circle-check"></i> Update your password periodically.</li>
                        <li><i class="fa-solid fa-circle-check"></i> Never share your login details with students or colleagues.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>



</main>

<!-- ==================== MODALS ==================== -->
<!-- ═══════════ TEACHER: RECOMMENDATION SHEET MODAL ═══════════ -->
<div id="teacherRecommendationSheetModal" class="modal-overlay">
    <div class="modal-box wide recommendation-sheet-modal" style="max-width: 52rem;">
        <div class="recommendation-document">
            <div class="rec-header-image">
                <img src="{{ asset('pictures/mccheader.jpg') }}" alt="MCC Header">
            </div>
            <h2 class="rec-title">Recommendation Sheet</h2>
            <p class="rec-serial" id="tchRecommendationSerial">&nbsp;</p>

            <div class="rec-body" id="tchRecommendationContent">
                <div id="tchRecommendationLoading"
                     class="modal-loading-box absolute inset-0 bg-white/90 backdrop-blur-sm z-10 rounded-lg"
                     style="display:flex;">
                    <div class="spinner-sm"></div>
                    <p>Loading recommendation sheet…</p>
                </div>
                <p>This <strong>Capstone Project</strong> hereto entitled:</p>
                <p class="rec-capstone-title" id="tchRecommendationTitle">—</p>
                <p>prepared and submitted by
                    <span class="rec-members" id="tchRecommendationProponents">—</span>
                </p>
                <p>in partial fulfillment of the requirements for the degree of
                    <strong>Bachelor of Science in Information Technology</strong>
                    has been examined, accepted, and recommended for Oral Presentation.
                </p>
                <div class="rec-signature">
                    <span class="rec-sig-name" id="tchRecommendationAdviser">—</span>
                    <span class="rec-sig-label">Capstone Adviser</span>
                </div>
            </div>

            <div class="rec-footer">
                <div class="rec-footer-block">
                    <div class="rec-footer-value" id="tchRecommendationDate">—</div>
                    <div class="rec-footer-label">Date Issued</div>
                </div>
                <div class="rec-footer-block">
                    <div class="rec-footer-value" id="tchRecommendationGroup">—</div>
                    <div class="rec-footer-label">Group</div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-[#e2dacf] mt-4">
            <button type="button" onclick="closeModal('teacherRecommendationSheetModal')" class="btn-primary text-xs py-2 px-4">Close</button>
        </div>
    </div>
</div>

<!-- ═══════════ TEACHER: APPROVAL SHEET MODAL ═══════════ -->
<div id="teacherApprovalSheetModal" class="modal-overlay">
    <div class="modal-box wide" style="max-width: 52rem; padding: 1.5rem;">
        <div class="text-center mb-4">
            <img src="{{ asset('pictures/mccheader.jpg') }}" alt="MCC Header" class="w-full max-h-24 object-contain">
        </div>
        <h2 id="tchApprovalHeading" class="text-center text-2xl font-bold tracking-widest text-[#0a1428] mb-4" style="font-family:'Cormorant Garamond',serif;">APPROVAL SHEET</h2>
        <p class="text-center text-xs tracking-widest mt-1" id="tchApprovalSerial"
           style="font-family:'Courier New', monospace; color:#8b6914;">&nbsp;</p>

        <div id="tchApprovalContent" class="approval-sheet-doc relative">
            <div id="tchApprovalLoading"
                 class="modal-loading-box absolute inset-0 bg-white/90 backdrop-blur-sm z-10 rounded-lg"
                 style="display:flex;">
                <div class="spinner-sm"></div>
                <p>Loading approval sheet…</p>
            </div>
            <p class="approval-intro">This Capstone Project 2 hereto entitled:</p>
            <p id="tchApprovalTitle" class="approval-title">—</p>
            <p class="approval-body">
                prepared and submitted by <span id="tchApprovalProponents">—</span>
                in partial fulfillment of the requirements for the degree of
                <strong>Bachelor of Science in Information Technology</strong>
                has been examined, accepted and recommended for Oral Presentation.
            </p>
            <div class="approval-signature">
                <span id="tchApprovalAdviser" class="approval-sig-line">—</span>
                <div class="approval-sig-role">Adviser</div>
            </div>
            <p class="approval-panel-heading">Panel of Examiners</p>
            <div id="tchApprovalPanelists" class="approval-panel-grid">
                <div class="approval-signature"><span class="approval-sig-line">Loading...</span><div class="approval-sig-role">Member</div></div>
            </div>
            <div id="tchApprovalChairmanBlock" class="approval-signature" style="display:none;">
                <span id="tchApprovalChairman" class="approval-sig-line">—</span>
                <div class="approval-sig-role">Chairman, Board of Panels</div>
            </div>
            <p class="approval-accepted">
                <strong>ACCEPTED AND APPROVED</strong> in partial fulfillment of the requirements for the degree of
                <strong>BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY</strong>.
            </p>
            <div class="approval-oral-results">
                <span><span class="oral-label">Oral Examination:</span><span id="tchApprovalOralResult" class="oral-value">—</span></span>
                <span><span class="oral-label">Date of Oral Examination:</span><span id="tchApprovalOralDate" class="oral-value">—</span></span>
            </div>
            <p class="approval-approved-label">Approved:</p>
            <div class="approval-signature">
                <span id="tchApprovalPresident" class="approval-sig-line">DR. FLORIPIS A. MONTECILLO, Ed.D.</span>
                <div class="approval-sig-role">School President</div>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-[#e2dacf] mt-4">
            <button type="button" onclick="closeModal('teacherApprovalSheetModal')" class="btn-primary text-xs py-2 px-4">Close</button>
        </div>
    </div>
</div>
<!-- VIEW-ONLY REVISION SHEET MODAL -->
<div id="viewRevisionModal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent" style="background-color: #d6b15c;"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">
                Revision Sheet
            </h2>
            <button type="button" onclick="closeModal('viewRevisionModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>

        <div id="viewRevisionContent">
            <p class="text-sm text-[#5b6375]">Loading…</p>
        </div>

        <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-[#e2dacf]">
            <button type="button" onclick="closeModal('viewRevisionModal')" class="btn-ghost">Close</button>
        </div>
    </div>
</div>
<!-- REVISION CHECKING MODAL (verification) -->
<div id="revisionCheckModal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent" style="background-color: #15803d;"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Revision Verification</h2>
            <button type="button" onclick="closeModal('revisionCheckModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>

        <form id="revision_check_form" onsubmit="submitRevisionCheck(event)" class="space-y-5">
            @csrf
            <input type="hidden" id="check_group_id">

            <!-- Header: Proponents & Project -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Name of Proponents</label>
                    <div id="check_proponents_list" class="flex flex-wrap gap-2 mt-1">
                        <!-- Populated via JS -->
                    </div>
                </div>
                <div>
                    <label class="form-label">Name of Capstone Project</label>
                    <input type="text" id="check_capstone_title" class="form-input" readonly>
                </div>
            </div>

            <!-- Chapter / Document Findings -->
            <div class="border-t border-[#e2dacf] pt-4">
                <p class="form-fieldset-title"><i class="fa-solid fa-book"></i> Chapter / Document Findings</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                                <th class="p-2 pl-3" style="width:20%">Chapter</th>
                                <th class="p-2" style="width:45%">Document Findings</th>
                                <th class="p-2 text-center" style="width:15%">Completed?</th>
                                <th class="p-2 pr-3 text-center" style="width:20%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="check_chapters_tbody" class="divide-y divide-[#faf1e0] text-xs">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- System / IoT Findings -->
            <div class="border-t border-[#e2dacf] pt-4">
                <p class="form-fieldset-title"><i class="fa-solid fa-microchip"></i> System / IoT Findings / Enhancements / Recommendations</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                                <th class="p-2 pl-3" style="width:65%">Finding / Enhancement</th>
                                <th class="p-2 text-center" style="width:15%">Completed?</th>
                                <th class="p-2 pr-3 text-center" style="width:20%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="check_iot_tbody" class="divide-y divide-[#faf1e0] text-xs">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Additional Objectives -->
<div class="border-t border-[#e2dacf] pt-3">
    <p class="form-fieldset-title mb-2">
        <i class="fa-solid fa-list-check"></i>
        Additional Objectives (if any)
    </p>

    <div class="border border-[#e2dacf] rounded-lg overflow-hidden">
        <div id="check_objectives_list"></div>
    </div>
</div>

            <!-- Overall Remarks (read-only) -->
            <div>
                <label class="form-label">Overall Remarks / Instructions</label>
                <p id="check_overall_remarks" class="p-3 bg-[#faf8f4] border border-[#e2dacf] rounded-lg text-sm text-[#5b6375] italic"></p>
            </div>

            <!-- Approved by -->
            <div class="border-t border-[#e2dacf] pt-4">
                <label class="form-label">Approved by (Panelist Name / Signature)</label>
                <input type="text" name="approved_by" class="form-input" placeholder="Enter your name" value="{{ strtoupper(($teacher->teacher_first_name)) . ' . ' . strtoupper($teacher->teacher_last_name) }}" readonly>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#e2dacf]">
                <button type="button" onclick="closeModal('revisionCheckModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="background-color: #15803d; border-color: #15803d;">Submit Verification</button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW PROGRESS MODAL -->
<div id="viewModal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; font-weight:600; color:var(--navy);" id="view_modal_title">Group Progress</h2>
            <button type="button" onclick="closeModal('viewModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-2xl leading-none">&times;</button>
        </div>

        <div id="view_modal_content">
            <!-- In-modal loading state (only inside the modal) -->
            <div id="view_loading" class="flex flex-col items-center justify-center py-16" role="status" aria-live="polite">
                <div class="spinner-sm"></div>
                <p class="mt-4 text-sm text-[#5b6375] font-medium">Loading group progress…</p>
                <p class="mt-1 text-[11px] text-[#b8b0a0]">Fetching milestones and evaluation records</p>
            </div>

            <!-- Dynamic content -->
            <div id="view_data" class="hidden">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-[#0a1428]">Capstone Progress</h3>
                        <span class="text-xs font-medium text-[#5b6375] bg-[#f0ece4] px-3 py-1 rounded-full">
                            <span id="view_progress_label">0</span>% complete
                        </span>
                    </div>
                    <div class="progress-bar-bg h-2.5 w-full mb-6">
                        <div id="view_overall_progress" class="progress-fill h-full" style="width:0%;"></div>
                    </div>
                    <div id="view_milestones_container" class="overflow-x-auto">
                    <table class="timeline-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tasks / Requirements</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="view_milestones_tbody"></tbody>
                    </table>
                </div>

                <div id="view_documents_container" class="mt-6 pt-4 border-t border-[#e2dacf]">
    <h4 class="text-sm font-bold text-[#0a1428] mb-3 flex items-center gap-2">
        <i class="fas fa-folder-open text-[#d6b15c]"></i> Group Documents
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

        <!-- Recommendation Sheet -->
        <div class="content-card">
            <div class="card-accent"></div>
            <div class="p-4 flex flex-col h-full">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                        <i class="fas fa-file-signature text-white text-xs"></i>
                    </div>
                    <h5 class="font-bold text-sm text-[#0a1428]">Recommendation Sheet</h5>
                </div>
                <p class="text-[11px] text-[#5b6375] mb-3 flex-1" id="view_rec_status">
                    View the recommendation sheet issued to this group.
                </p>
                <button type="button" id="view_rec_btn"
                        onclick="openTeacherRecommendationSheet(window.__viewModalGroupId)"
                        class="btn-outline text-xs py-1.5 px-3" disabled>
                    <i class="fas fa-eye mr-1"></i> View
                </button>
            </div>
        </div>

        <!-- Revision Sheet -->
        <div class="content-card">
            <div class="card-accent"></div>
            <div class="p-4 flex flex-col h-full">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                        <i class="fas fa-file-alt text-white text-xs"></i>
                    </div>
                    <h5 class="font-bold text-sm text-[#0a1428]">Revision Sheet</h5>
                </div>
                <p class="text-[11px] text-[#5b6375] mb-3 flex-1" id="view_rev_status">
                    View the revision sheet submitted for this group.
                </p>
                <button type="button" id="view_rev_btn"
                        onclick="openTeacherRevisionSheet(window.__viewModalGroupId)"
                        class="btn-outline text-xs py-1.5 px-3" disabled>
                    <i class="fas fa-eye mr-1"></i> View
                </button>
            </div>
        </div>

        <!-- Approval Sheet -->
        <div class="content-card">
            <div class="card-accent"></div>
            <div class="p-4 flex flex-col h-full">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                        <i class="fas fa-file-contract text-white text-xs"></i>
                    </div>
                    <h5 class="font-bold text-sm text-[#0a1428]">Approval Sheet</h5>
                </div>
                <p class="text-[11px] text-[#5b6375] mb-3 flex-1" id="view_apr_status">
                    View the approval sheet issued to this group.
                </p>
                <button type="button" id="view_apr_btn"
                        onclick="openTeacherApprovalSheet(window.__viewModalGroupId)"
                        class="btn-outline text-xs py-1.5 px-3" disabled>
                    <i class="fas fa-eye mr-1"></i> View
                </button>
            </div>
        </div>

    </div>
</div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- EVALUATION MODAL ON GOING -->
<div id="evaluationModal" class="modal-overlay">
    <div class="modal-box wide" style="max-width: 82rem;">

        <!-- Header -->
        <div class="eval-modal-header">
            <div class="flex justify-between items-start gap-4">
                <div>
                    <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; font-weight:600; color:var(--navy); line-height:1.1;">
                        Evaluate Group
                    </h2>
                    <p class="text-xs text-[#5b6375] mt-1">Score the group's milestone deliverables against the assigned rubric.</p>
                </div>
                <button type="button" onclick="closeModal('evaluationModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-xl leading-none flex-shrink-0 mt-1">&times;</button>
            </div>

        </div>

        <div class="eval-modal-body">

            <!-- ═══ LEFT: EVALUATION FORM ═══ -->
            <div class="eval-panel eval-panel-left">
                <div class="eval-panel-heading">
                    <div class="eval-panel-title">
                        <span class="icon-badge"><i class="fa-regular fa-pen-to-square"></i></span>
                        Evaluation Form
                    </div>
                </div>

                <form id="evaluation_form" action="/teacher/submit-evaluation" method="POST" class="space-y-5">
    @csrf
    <input type="hidden" name="form_type" value="evaluation">
    <div id="evalErrors" class="hidden p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"></div>
    <input type="hidden" name="group_id" id="eval_group_id">
    <input type="hidden" name="milestone_id" id="eval_milestone_id">
    <input type="hidden" name="score" id="eval_total_score">
    <input type="hidden" name="max_score" id="eval_max_score">

    <!-- ═══ EVALUATE MODE FIELDS ═══ -->
    <div id="eval_mode_evaluate_fields" class="space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="form-label">Group</label><input type="text" id="eval_group_name" class="form-input" readonly></div>
            <div>
                <label class="form-label">Milestone</label>
                <select id="milestone_select" class="form-select" disabled>
                    <option value="">Loading milestone…</option>
                </select>
                <p class="text-[10px] text-[#9a9385] mt-1">Set automatically by this group's evaluation room.</p>
            </div>
        </div>

        <!-- Attendance -->
        <div>
            <label class="form-label">Attendance</label>
            <div class="flex gap-4 mt-1.5">
                <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input type="radio" name="attendance" value="present" checked class="form-radio text-[#d6b15c] focus:ring-[#d6b15c]">
                    <span>All members present</span>
                </label>
                <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input type="radio" name="attendance" value="absent" class="form-radio text-[#d6b15c] focus:ring-[#d6b15c]">
                    <span>Some members absent</span>
                </label>
            </div>
        </div>

        <!-- Absent Students Checklist -->
        <div id="absent_students_container" class="hidden">
            <label class="form-label">Select Absent Student(s)</label>
            <div id="student_checklist" class="grid grid-cols-2 gap-2 mt-1.5 p-3 border border-[#e2dacf] rounded-xl bg-[#faf8f4] max-h-36 overflow-y-auto">
                <!-- Loaded dynamically via JS -->
            </div>
        </div>

        <div id="rubric_container" class="hidden">
            <label class="form-label">Rubric: <span id="rubric_name_display" class="text-[#b88d3a]"></span></label>
            <p class="text-[10px] text-[#9a9385] mb-1.5">Max score per criteria: 4</p>
            <div class="overflow-x-auto rounded-lg border border-[#e2dacf]">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[#5b6375] bg-[#faf8f4] border-b border-[#e2dacf]">
                            <th class="text-left py-2 px-3 text-xs">Criteria</th>
                            <th class="text-center py-2 text-xs">1</th>
                            <th class="text-center py-2 text-xs">2</th>
                            <th class="text-center py-2 px-3 text-xs">3</th>
                            <th class="text-center py-2 px-3 text-xs">4</th>
                        </tr>
                    </thead>
                    <tbody id="criteria_tbody"></tbody>
                    <tfoot>
                        <tr class="border-t border-[#e2dacf] font-semibold bg-[#faf8f4]">
                            <td class="py-2 px-3">Total</td>
                            <td colspan="4" class="text-center py-2">
                                <span id="total_score_display">0</span> / <span id="total_max">0</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div>
            <label class="form-label">Feedback</label>
            <textarea name="feedback" rows="3" class="form-input" placeholder="Overall feedback for this milestone..."></textarea>
        </div>
    </div>

    <!-- ═══ REVISE MODE FIELDS ═══ -->
    <div id="eval_mode_revise_fields" class="hidden space-y-5">
        <div>
            <label class="form-label">Proponents</label>
            <div id="eval_rev_proponents_list" class="flex flex-wrap gap-2 mt-1">
                <span class="text-xs text-[#5b6375] italic">Loading team members…</span>
            </div>
        </div>

        <div class="border-t border-[#e2dacf] pt-4">
            <p class="form-fieldset-title"><i class="fa-solid fa-book"></i> Chapter / Document Findings</p>
            <div id="eval_rev_chapter_rows" class="space-y-2"></div>
            <button type="button" onclick="addEvalRevChapterRow()" class="btn-outline text-xs mt-2">
                <i class="fas fa-plus mr-1"></i> Add Chapter Finding
            </button>
        </div>

        <div class="border-t border-[#e2dacf] pt-4">
            <p class="form-fieldset-title"><i class="fa-solid fa-microchip"></i> System / IoT Findings</p>
            <div id="eval_rev_iot_rows" class="space-y-2"></div>
            <button type="button" onclick="addEvalRevIotRow()" class="btn-outline text-xs mt-2">
                <i class="fas fa-plus mr-1"></i> Add System / IoT Finding
            </button>
        </div>

        <div class="border-t border-[#e2dacf] pt-4">
            <p class="form-fieldset-title"><i class="fa-solid fa-list-check"></i> Additional Objectives</p>
            <div id="eval_rev_objectives_list" class="space-y-2"></div>
            <button type="button" onclick="addEvalRevObjective()" class="btn-outline text-xs mt-2">
                <i class="fas fa-plus mr-1"></i> Add Objective
            </button>
        </div>

        <div>
            <label class="form-label">Overall Remarks / Instructions</label>
            <textarea id="eval_rev_description_input" class="form-input h-24" placeholder="Provide clear instructions for the group and their adviser..."></textarea>
        </div>
    </div>
    </form>
            </div>

            <!-- ═══ RIGHT: REVISION SHEET (VIEW ONLY) ═══ -->
            <div class="eval-panel eval-panel-right">
                <div class="eval-panel-heading">
                    <div class="eval-panel-title">
                        <span class="icon-badge"><i class="fa-solid fa-file-lines"></i></span>
                        Revision Sheet
                    </div>
                    <span id="eval_revision_access_label" class="readonly-pill"><i class="fa-solid fa-pen"></i> Editable if no revision exists</span>
                </div>
                <div id="eval_revision_sheet_content" class="text-sm">
                    <p class="text-sm text-[#5b6375]">Loading…</p>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="eval-modal-footer">
            <button type="button" onclick="closeModal('evaluationModal')" class="btn-ghost">Cancel</button>
            <button type="button" id="eval_submit_btn" onclick="submitEvalModal()" class="btn-primary">
            <i class="fa-regular fa-floppy-disk mr-1"></i> Submit Evaluation
            </button>
        </div>

    </div>
</div>


{{-- edit group modal --}}
<div id="editGroupModal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Group Members</h2>
            <button type="button" onclick="closeModal('editGroupModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <div id="editGroupErrors" class="hidden mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"></div>
        <form id="editGroupForm" method="POST" class="space-y-4">
            @csrf
            <div><label class="form-label">Group Name</label><input type="text" name="group_name" id="edit_group_name" class="form-input" required></div>
            <div><label class="form-label">Capstone Title</label><input type="text" name="capstone_title" id="edit_capstone_title" class="form-input" required></div>
            <div>
                <label class="form-label">Add Student to Team</label>
                <div class="flex gap-2">
                    <select id="editStudentSelect" class="form-select flex-1" multiple style="height:100px;"><option disabled>Loading section students...</option></select>
                    <button type="button" id="editAddStudentsBtn" class="btn-primary whitespace-nowrap"><i class="fas fa-plus mr-1"></i> Add</button>
                </div>
            </div>
            <div>
                <label class="form-label">Current Team</label>
                <div id="editSelectedStudentsContainer" class="space-y-2 max-h-56 overflow-y-auto"></div>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('editGroupModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@php
    $uniqueStudents = $groups->flatMap(fn($g) => $g->students)->unique('id');
@endphp

<!-- TEACHER DIRECTORY DETAIL MODAL (TABBED OVERVIEW) -->
<div id="dashboard_detail_modal" class="modal-overlay">
    <div class="modal-box wide" style="max-width: 68rem;">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-6 p-1">
            <div>
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; font-weight:600; color:var(--navy);" id="ddm_modal_title">Teacher System Directory Details</h2>
                <p class="text-xs text-[#5b6375] mt-1">Detailed directory of all entities handled or assigned to you.</p>
            </div>
            <button type="button" onclick="closeModal('dashboard_detail_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-2xl focus:outline-none">&times;</button>
        </div>

        <!-- Tab Buttons -->
        <div class="flex gap-2 border-b border-[#e2dacf] pb-3 mb-6 overflow-x-auto">
            <button type="button" onclick="switchDdmTab('groups')" id="ddm_tab_groups" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                <i class="fas fa-layer-group mr-1.5 text-[#d6b15c]"></i> Handled Groups (<span id="ddm_count_groups">{{ count($groups ?? []) }}</span>)
            </button>
            <button type="button" onclick="switchDdmTab('students')" id="ddm_tab_students" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                <i class="fas fa-user-graduate mr-1.5 text-[#d6b15c]"></i> My Students (<span id="ddm_count_students">{{ count($uniqueStudents ?? []) }}</span>)
            </button>
            <button type="button" onclick="switchDdmTab('evaluations')" id="ddm_tab_evaluations" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                <i class="fa-regular fa-circle-check mr-1.5 text-[#d6b15c]"></i> My Evaluations (<span id="ddm_count_evaluations">{{ count($evaluations ?? []) }}</span>)
            </button>
            <button type="button" onclick="switchDdmTab('sections')" id="ddm_tab_sections" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                <i class="fas fa-columns mr-1.5 text-[#d6b15c]"></i> My Sections (<span id="ddm_count_sections">{{ count($teacherSections ?? []) }}</span>)
            </button>
        </div>

        <!-- Search Filter -->
        <div class="mb-4">
            <div class="relative">
                <input type="text" id="ddm_search_input" oninput="filterDdmTable()" class="form-input text-xs w-full pl-9 py-2 border border-[#e2dacf] rounded-xl bg-[#faf8f4]" placeholder="Search by name, title, section, or details...">
                <i class="fas fa-search absolute left-3 top-3 text-[#b8b0a0] text-xs"></i>
            </div>
        </div>

        <!-- Tabs Content Container -->
        <div class="overflow-y-auto max-h-[50vh] border border-[#e2dacf] bg-white rounded-xl">

            <!-- Handled Groups Tab Content -->
            <div id="ddm_content_groups" class="ddm-tab-content hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                            <th class="p-3 pl-4">Group Name</th>
                            <th class="p-3">Capstone Title</th>
                            <th class="p-3">Section</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Room</th>
                            <th class="p-3 pr-4 text-center">Members</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#faf1e0]">
                        @forelse($groups ?? [] as $gr)
                            @php
                                $isAdviser = $gr->adviser_id == $teacher->id;
                            @endphp
                            <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower($gr->group_name . ' ' . $gr->capstone_title . ' ' . ($gr->section->section_name ?? '') . ' ' . ($isAdviser ? 'Adviser' : 'Panelist') . ' ' . ($gr->room->room_name ?? '')) }}">
                                <td class="p-3 pl-4 font-semibold text-[#0a1428]">{{ $gr->group_name }}</td>
                                <td class="p-3 text-[#3d4450] max-w-xs truncate" title="{{ $gr->capstone_title }}">{{ $gr->capstone_title }}</td>
                                <td class="p-3 text-[#3d4450]">{{ $gr->section->section_name ?? 'Unassigned' }}</td>
                                <td class="p-3">
                                    @if($isAdviser)
                                        <span class="badge badge-navy text-[10px]"><i class="fas fa-user-tie text-[9px] mr-1"></i> Adviser</span>
                                    @else
                                        <span class="badge badge-gold text-[10px]" style="background-color:rgba(214,177,92,0.1); color:rgba(184,141,58,1); border:1px solid rgba(214,177,92,0.25);"><i class="fas fa-users text-[9px] mr-1"></i> Panelist</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if($gr->room)
                                        <span class="badge badge-green text-[10px]"><i class="fas fa-door-open text-[9px] mr-1"></i> {{ $gr->room->room_name }}</span>
                                    @else
                                        <span class="badge badge-muted text-[10px]">Unassigned</span>
                                    @endif
                                </td>
                                <td class="p-3 pr-4 text-center text-[#5b6375] font-semibold">{{ $gr->students->count() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No handled groups.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- My Students Tab Content -->
            <div id="ddm_content_students" class="ddm-tab-content hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                            <th class="p-3 pl-4">Student</th>
                            <th class="p-3">User ID</th>
                            <th class="p-3">Course</th>
                            <th class="p-3">Section</th>
                            <th class="p-3">Group</th>
                            <th class="p-3 pr-4">Contact</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#faf1e0]">
                        @forelse($uniqueStudents ?? [] as $st)
                            @php
                                $studentGroup = $groups->first(fn($g) => $g->students->contains($st->id));
                                $grpName = $studentGroup ? $studentGroup->group_name : 'No Group';
                            @endphp
                            <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower($st->student_first_name . ' ' . $st->student_last_name . ' ' . $st->user_id . ' ' . $st->course . ' ' . $st->section . ' ' . $grpName) }}">
                                <td class="p-3 pl-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);">
                                            {{ strtoupper(substr($st->student_first_name, 0, 1) . substr($st->student_last_name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-[#0a1428]">{{ $st->student_first_name }} {{ $st->student_last_name }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-[#3d4450] font-mono">{{ $st->user->user_id ?? 'N/A' }}</td>
                                <td class="p-3 text-[#3d4450]">{{ $st->course }}</td>
                                <td class="p-3 text-[#3d4450]">{{ $st->section }}</td>
                                <td class="p-3">
                                    @if($studentGroup)
                                        <span class="badge badge-navy text-[10px]"><i class="fas fa-layer-group text-[9px] mr-1"></i> {{ $grpName }}</span>
                                    @else
                                        <span class="badge badge-muted text-[10px]">No Group</span>
                                    @endif
                                </td>
                                <td class="p-3 pr-4 text-[#5b6375]">
                                    <div class="flex flex-col text-[10px]">
                                        <span><i class="fa-regular fa-envelope mr-1 text-[#b8b0a0]"></i> {{ $st->student_email }}</span>
                                        @if($st->contact_number)
                                            <span class="mt-0.5"><i class="fa-solid fa-phone mr-1 text-[#b8b0a0]"></i> {{ $st->contact_number }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No students registered in your handled groups.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- My Evaluations Tab Content -->
            <div id="ddm_content_evaluations" class="ddm-tab-content hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                            <th class="p-3 pl-4">Group Name</th>
                            <th class="p-3">Milestone</th>
                            <th class="p-3 text-center">Score</th>
                            <th class="p-3">Date Evaluated</th>
                            <th class="p-3 pr-4">Feedback</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#faf1e0]">
                        @forelse($evaluations ?? [] as $ev)
                            <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower(($ev->group->group_name ?? 'Unknown') . ' ' . ($ev->milestone->milestone_title ?? '') . ' ' . $ev->feedback) }}">
                                <td class="p-3 pl-4 font-semibold text-[#0a1428]">{{ $ev->group->group_name ?? 'Unknown' }}</td>
                                <td class="p-3 text-[#3d4450]">{{ $ev->milestone->milestone_title ?? '' }}</td>
                                <td class="p-3 text-center text-[#1e6b3a] font-bold">{{ $ev->score }} / {{ $ev->max_score }}</td>
                                <td class="p-3 text-[#3d4450] font-mono">{{ \Carbon\Carbon::parse($ev->evaluation_date)->format('M d, Y') }}</td>
                                <td class="p-3 pr-4 text-[#5b6375] italic max-w-sm truncate" title="{{ $ev->feedback }}">{{ $ev->feedback }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No evaluations submitted by you yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- My Sections Tab Content -->
            <div id="ddm_content_sections" class="ddm-tab-content hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                            <th class="p-3 pl-4">Section Name</th>
                            <th class="p-3">Assigned Groups Count</th>
                            <th class="p-3 pr-4">Assigned Groups</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#faf1e0]">
                        @forelse($teacherSections ?? [] as $sec)
                            @php
                                $secGroups = $groups->where('section_id', $sec->id);
                            @endphp
                            <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower($sec->section_name . ' ' . $secGroups->pluck('group_name')->join(' ')) }}">
                                <td class="p-3 pl-4 font-semibold text-[#0a1428]">{{ $sec->section_name }}</td>
                                <td class="p-3 text-[#3d4450] font-mono">{{ $secGroups->count() }} group(s)</td>
                                <td class="p-3 pr-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($secGroups as $sg)
                                            <span class="badge badge-navy text-[9px]">{{ $sg->group_name }}</span>
                                        @empty
                                            <span class="text-gray-400 italic text-[10px]">No groups in this section</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No sections assigned.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div class="flex justify-end pt-4 border-t border-[#e2dacf] mt-5">
            <button type="button" onclick="closeModal('dashboard_detail_modal')" class="btn-primary text-xs px-5 py-2">Close Details</button>
        </div>
    </div>
</div>

<!-- REVISION MODAL (matching Revision-Sheet-2026 PDF) -->
<div id="revisionModal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent" style="background-color: #d97706;"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Request Group Revision</h2>
            <button type="button" onclick="closeModal('revisionModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>

        <form id="revision_form" onsubmit="submitRevisionRequest(event)" class="space-y-5">
            @csrf
            <input type="hidden" id="revision_group_id">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Group Name</label>
                    <input type="text" id="revision_group_name" class="form-input" readonly>
                </div>
                <div>
                    <label class="form-label">Name of Capstone Project</label>
                    <input type="text" id="revision_capstone_title" class="form-input" readonly>
                </div>
            </div>

            <!-- Proponents (loaded from group members) -->
            <div>
                <label class="form-label">Proponents</label>
                <div id="revision_proponents_list" class="flex flex-wrap gap-2 mt-1">
                    <span class="text-xs text-[#5b6375] italic">Loading team members…</span>
                </div>
            </div>

            <!-- Chapter Findings & Remarks -->
            <div class="border-t border-[#e2dacf] pt-4">
                <p class="form-fieldset-title"><i class="fa-solid fa-book"></i> Chapter / Document Findings & Remarks</p>
                <div id="revision_chapter_rows" class="space-y-2">
                    <!-- Dynamic rows -->
                </div>
                <button type="button" onclick="addRevisionChapterRow()" class="btn-outline text-xs mt-2">
                    <i class="fas fa-plus mr-1"></i> Add Chapter Finding
                </button>
            </div>

            <!-- System / IoT Findings -->
            <div class="border-t border-[#e2dacf] pt-4">
                <p class="form-fieldset-title"><i class="fa-solid fa-microchip"></i> System / IoT Findings / Enhancements / Recommendations</p>
                <div id="revision_iot_rows" class="space-y-2">
                    <!-- Dynamic rows -->
                </div>
                <button type="button" onclick="addRevisionIotRow()" class="btn-outline text-xs mt-2">
                    <i class="fas fa-plus mr-1"></i> Add System / IoT Finding
                </button>
            </div>

            <!-- Additional Objectives for Capstone 2 -->
            <div class="border-t border-[#e2dacf] pt-4">
                <p class="form-fieldset-title"><i class="fa-solid fa-list-check"></i> Additional Objectives ( If Any)</p>
                <div id="revision_objectives_list" class="space-y-2">
                    <!-- Dynamic list -->
                </div>
                <button type="button" onclick="addRevisionObjective()" class="btn-outline text-xs mt-2">
                    <i class="fas fa-plus mr-1"></i> Add Objective
                </button>
            </div>

            <!-- Overall Remarks -->
            <div>
                <label class="form-label">Overall Remarks / Instructions</label>
                <textarea id="revision_description_input" class="form-input h-24" placeholder="Provide clear instructions for the group and their adviser..." required></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#e2dacf]">
                <button type="button" onclick="closeModal('revisionModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="background-color: #d97706; border-color: #d97706;">Submit Revision Request</button>
            </div>
        </form>
    </div>
</div>
{{-- documents modal --}}
{{-- revision sheet  modal--}}

<div id="myEvaluationModal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent" style="background-color:#15803d;"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">My Evaluation</h2>
            <button type="button" onclick="closeModal('myEvaluationModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <div id="myEvaluationContent">
            <p class="text-sm text-[#5b6375]">Loading…</p>
        </div>
    </div>
</div>



<!-- RUBRIC SCORES MODAL (panelist scores only) -->
<div id="rubricScoresModal" class="modal-overlay">
    <div class="modal-box wide" style="max-width: 46rem;">
        <div class="modal-accent" style="background: linear-gradient(90deg, var(--gold), var(--gold-dark));"></div>
        <div class="flex justify-between items-center mb-1">
            <div>
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:600; color:var(--navy);" id="rubricScoresTitle">
                    Rubric Scores
                </h2>
                <p class="text-xs text-[#5b6375] mt-0.5" id="rubricScoresSubtitle">Panelist evaluation summary</p>
            </div>
            <button type="button" onclick="closeModal('rubricScoresModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-xl leading-none">&times;</button>
        </div>

        <div id="rubricScoresContent" class="mt-5 max-h-[65vh] overflow-y-auto pr-1 space-y-4">
            <p class="text-sm text-[#5b6375] text-center py-8">Loading…</p>
        </div>

        <div class="flex justify-end pt-4 mt-4 border-t border-[#e2dacf]">
            <button type="button" onclick="closeModal('rubricScoresModal')" class="btn-ghost">Close</button>
        </div>
    </div>
</div>
<!-- ALL-PANELISTS REVISIONS MODAL -->
<div id="groupRevisionsModal" class="modal-overlay">
    <div class="modal-box wide" style="max-width: 56rem;">
        <div class="modal-accent" style="background-color: #d6b15c;"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);" id="grm_title">
                All Revisions
            </h2>
            <button type="button" onclick="closeModal('groupRevisionsModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>

        <div id="grm_content" class="space-y-5 max-h-[65vh] overflow-y-auto pr-1">
            <p class="text-sm text-[#5b6375] text-center py-8">Loading…</p>
        </div>

        <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-[#e2dacf]">
            <button type="button" onclick="closeModal('groupRevisionsModal')" class="btn-ghost">Close</button>
        </div>
    </div>
</div>

<script>
// ══════════════════════════════════════════════════════════════
// PROFESSIONAL LOADING SYSTEM  — splash · top bar · overlay · skeleton
// ══════════════════════════════════════════════════════════════
(function () {
    'use strict';

    /* ---------------- Configuration ---------------- */
    const SPLASH_MIN_MS  = 750;    // keep splash visible at least this long
    const SPLASH_MAX_MS  = 4000;   // hard safety net
    const OVERLAY_MIN_MS = 350;    // prevents flicker on fast requests
    const WATCHDOG_MS    = 25000;  // force-close overlay if a request hangs

    /* ---------------- 1. Splash ---------------- */
    const splashEl     = document.getElementById('app-splash');
    const splashFill   = document.getElementById('splash_bar_fill');
    const splashStatus = document.getElementById('splash_status');
    const splashStart  = performance.now();
    const SPLASH_MSGS  = [
        'Initializing workspace…',
        'Loading your sections…',
        'Syncing capstone records…',
        'Preparing your dashboard…'
    ];
    let splashMsgIdx = 0, splashPct = 6, splashTimer = null, splashDone = false;

    if (splashEl) {
        if (splashFill) splashFill.style.width = splashPct + '%';
        splashTimer = setInterval(function () {
            splashPct = Math.min(splashPct + Math.random() * 14 + 5, 88);
            if (splashFill) splashFill.style.width = splashPct + '%';
            splashMsgIdx = (splashMsgIdx + 1) % SPLASH_MSGS.length;
            if (splashStatus) splashStatus.textContent = SPLASH_MSGS[splashMsgIdx];
        }, 450);
    }

    function hideSplash() {
        if (!splashEl || splashDone) return;
        splashDone = true;
        clearInterval(splashTimer);
        if (splashFill)   splashFill.style.width = '100%';
        if (splashStatus) splashStatus.textContent = 'Ready';

        const wait = Math.max(0, SPLASH_MIN_MS - (performance.now() - splashStart));
        setTimeout(function () {
            splashEl.classList.add('is-hidden');
            setTimeout(function () { if (splashEl.parentNode) splashEl.remove(); }, 620);
        }, wait + 100);
    }
    window.hideSplash = hideSplash;

    if (document.readyState === 'complete') hideSplash();
    else window.addEventListener('load', hideSplash);
    setTimeout(hideSplash, SPLASH_MAX_MS);

    /* ---------------- 2. Top route progress bar ---------------- */
    const barEl   = document.getElementById('route-progress');
    const barFill = document.getElementById('route-progress-fill');
    let barValue = 0, barTimer = null, barActive = false, barRefs = 0;

    function barStart() {
        if (!barEl || !barFill) return;
        barRefs++;
        if (barActive) return;
        barActive = true;
        barValue = 10;
        barEl.classList.add('active');
        barFill.style.width = '10%';
        clearInterval(barTimer);
        barTimer = setInterval(function () {
            if (barValue < 92) {
                barValue += (92 - barValue) * 0.09;
                barFill.style.width = barValue + '%';
            }
        }, 180);
    }

    function barFinish() {
        if (!barEl || !barFill) return;
        barRefs = Math.max(0, barRefs - 1);
        if (barRefs > 0 || !barActive) return;
        clearInterval(barTimer);
        barFill.style.width = '100%';
        setTimeout(function () {
            barEl.classList.remove('active');
            barActive = false;
            setTimeout(function () { barFill.style.width = '0%'; }, 320);
        }, 230);
    }

    /* ---------------- 3. Overlay loader ---------------- */
    const overlayEl = document.getElementById('page-loader');
    const overlayLabel = overlayEl ? overlayEl.querySelector('.loader-label') : null;
    let overlayRefs = 0, overlayTimer = null, overlayShownAt = 0, watchdogTimer = null;

    window.showPageLoader = function (label) {
        if (!overlayEl) return;
        overlayRefs++;

        if (label && overlayLabel) overlayLabel.textContent = label;
        else if (overlayLabel && overlayRefs === 1) overlayLabel.textContent = 'Loading…';

        clearTimeout(overlayTimer);
        clearTimeout(watchdogTimer);

        if (overlayRefs === 1) {
            overlayShownAt = performance.now();
            overlayEl.classList.add('active');
        }

        // Watchdog: never let the overlay get stuck forever
        watchdogTimer = setTimeout(function () {
            overlayRefs = 0;
            overlayEl.classList.remove('active');
        }, WATCHDOG_MS);

        barStart();
    };

    window.hidePageLoader = function (force) {
        if (!overlayEl) return;
        overlayRefs = force ? 0 : Math.max(0, overlayRefs - 1);
        if (overlayRefs > 0) return;

        clearTimeout(watchdogTimer);

        const elapsed = performance.now() - overlayShownAt;
        const wait = Math.max(0, OVERLAY_MIN_MS - elapsed);

        clearTimeout(overlayTimer);
        overlayTimer = setTimeout(function () {
            if (overlayRefs === 0) overlayEl.classList.remove('active');
        }, wait);

        barFinish();
    };

    window.softReload = function (delay) {
        window.showPageLoader('Refreshing…');
        setTimeout(function () { window.location.reload(); }, delay || 200);
    };

    /* ---------------- 4. Fetch instrumentation ---------------- */
    const nativeFetch = window.fetch ? window.fetch.bind(window) : null;
    if (nativeFetch) {
        window.fetch = function (input, init) {
            const opts = init || {};
            const silent = opts.__silent === true;

            if (silent) return nativeFetch(input, init);

            barStart();
            return nativeFetch(input, init).then(
                function (res) { barFinish(); return res; },
                function (err) { barFinish(); throw err; }
            );
        };
    }

    /* ---------------- 5. Helpers ---------------- */
    // Wrap a promise so the overlay shows while it runs.
    window.trackPromise = function (promise, label) {
        window.showPageLoader(label);
        return promise.then(
            function (value) { window.hidePageLoader(); return value; },
            function (error) { window.hidePageLoader(); throw error; }
        );
    };

    // Professional button loading state.
    window.setButtonLoading = function (btn, label) {
        if (!btn) return function () {};
        if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.innerHTML = '<i class="fas fa-circle-notch"></i> ' + (label || 'Processing…');

        return function restore() {
            btn.disabled = false;
            btn.classList.remove('is-loading');
            btn.innerHTML = btn.dataset.originalHtml;
        };
    };

    // Skeleton building blocks.
    window.skeletonLines = function (count, widths) {
        widths = widths || [90, 70, 55, 80];
        let html = '<div class="space-y-2.5">';
        for (let i = 0; i < (count || 3); i++) {
            html += '<div class="skeleton" style="height:12px;width:' + widths[i % widths.length] + '%;"></div>';
        }
        return html + '</div>';
    };

    window.skeletonCards = function (count) {
        let html = '<div class="space-y-4">';
        for (let i = 0; i < (count || 3); i++) {
            html += ''
              + '<div class="rs-card">'
              +   '<div class="rs-card-head">'
              +     '<div style="flex:1;">'
              +       '<div class="skeleton" style="height:10px;width:90px;margin-bottom:10px;"></div>'
              +       '<div class="skeleton" style="height:14px;width:150px;"></div>'
              +     '</div>'
              +     '<div class="skeleton" style="height:26px;width:76px;"></div>'
              +   '</div>'
              +   '<div class="skeleton" style="height:38px;width:100%;margin-top:14px;"></div>'
              + '</div>';
        }
        return html + '</div>';
    };

    window.skeletonBlock = function (rows) {
        let html = '<div class="space-y-3">';
        for (let i = 0; i < (rows || 3); i++) {
            html += ''
              + '<div class="skeleton" style="height:14px;width:100%;"></div>'
              + '<div class="skeleton" style="height:14px;width:' + (60 + (i % 3) * 12) + '%;"></div>';
        }
        return html + '</div>';
    };

    /* ---------------- 6. bfcache restore ---------------- */
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            hideSplash();
            window.hidePageLoader(true);
        }
    });
})();

// ══════════════════════════════════════════════
// MODAL HELPERS
// ══════════════════════════════════════════════
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('active');
}
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('active');
}

window.toggleSectionCollapse = function(sectionId) {
    const content = document.getElementById(`section_collapsible_content_${sectionId}`);
    const icon = document.getElementById(`collapse_icon_${sectionId}`);
    if (!content || !icon) return;

    const isCollapsed = content.classList.toggle('hidden');
    if (isCollapsed) {
        icon.style.transform = 'rotate(-90deg)';
    } else {
        icon.style.transform = 'rotate(0deg)';
    }
};

window.toggleSectionView = function(prefix, sectionId, viewType) {
    const groupsView = document.getElementById(`${prefix}_section_groups_view_${sectionId}`);
    const studentsView = document.getElementById(`${prefix}_section_students_view_${sectionId}`);
    const groupsBtn = document.getElementById(`${prefix}_toggle_btn_${sectionId}_groups`);
    const studentsBtn = document.getElementById(`${prefix}_toggle_btn_${sectionId}_students`);

    if (!groupsView || !studentsView || !groupsBtn || !studentsBtn) return;

    if (viewType === 'groups') {
        groupsView.classList.remove('hidden');
        studentsView.classList.add('hidden');
        groupsBtn.className = 'px-2.5 py-1 rounded-md font-semibold text-[#b88d3a] bg-white shadow-sm transition-all focus:outline-none';
        studentsBtn.className = 'px-2.5 py-1 rounded-md font-semibold text-[#5b6375] hover:text-[#0a1428] transition-all focus:outline-none';
    } else {
        groupsView.classList.add('hidden');
        studentsView.classList.remove('hidden');
        studentsBtn.className = 'px-2.5 py-1 rounded-md font-semibold text-[#b88d3a] bg-white shadow-sm transition-all focus:outline-none';
        groupsBtn.className = 'px-2.5 py-1 rounded-md font-semibold text-[#5b6375] hover:text-[#0a1428] transition-all focus:outline-none';
    }
};

window.openDashboardDetailModal = function(tabName) {
    openModal('dashboard_detail_modal');
    switchDdmTab(tabName);
    const sInput = document.getElementById('ddm_search_input');
    if (sInput) {
        sInput.value = '';
        filterDdmTable();
    }
};

window.switchDdmTab = function(tabName) {
    document.querySelectorAll('.ddm-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.ddm-tab-btn').forEach(btn => {
        btn.style.borderColor = '#e2dacf';
        btn.style.color = '#5b6375';
        btn.style.backgroundColor = 'transparent';
        btn.classList.remove('active');
    });
    const content = document.getElementById(`ddm_content_${tabName}`);
    if (content) content.classList.remove('hidden');
    const activeBtn = document.getElementById(`ddm_tab_${tabName}`);
    if (activeBtn) {
        activeBtn.style.borderColor = 'var(--gold)';
        activeBtn.style.color = 'var(--gold)';
        activeBtn.style.backgroundColor = '#faf8f4';
        activeBtn.classList.add('active');
    }
    const modalTitle = document.getElementById('ddm_modal_title');
    if (modalTitle) {
        const titleMap = {
            students: 'My Students Directory',
            groups: 'Handled Groups Directory',
            evaluations: 'Submitted Evaluations',
            sections: 'My Sections Directory'
        };
        modalTitle.textContent = titleMap[tabName] || 'Teacher System Directory Details';
    }
    filterDdmTable();
};

window.filterDdmTable = function() {
    const query = document.getElementById('ddm_search_input').value.trim().toLowerCase();
    const activeTab = document.querySelector('.ddm-tab-btn.active');
    if (!activeTab) return;
    const tabIdName = activeTab.id.replace('ddm_tab_', '');
    const activeContent = document.getElementById(`ddm_content_${tabIdName}`);
    if (!activeContent) return;
    const rows = activeContent.querySelectorAll('.ddm-row-item');
    let visibleCount = 0;
    rows.forEach(row => {
        const searchVal = row.dataset.searchText || '';
        if (!query || searchVal.includes(query)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    const counterEl = document.getElementById(`ddm_count_${tabIdName}`);
    if (counterEl) {
        const originalTotal = rows.length;
        if (query) {
            counterEl.textContent = `${visibleCount}/${originalTotal}`;
        } else {
            counterEl.textContent = originalTotal;
        }
    }
};

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay') && e.target.classList.contains('active')) {
        e.target.classList.remove('active');
    }
});

// ══════════════════════════════════════════════
// TOAST NOTIFICATIONS
// ══════════════════════════════════════════════
const toastEl = document.getElementById('toast');
const toastMessageEl = document.getElementById('toastMessage');
let toastTimeout = null;

function showToast(msg, isError = false) {
    toastMessageEl.textContent = msg;
    const iconEl = toastEl.querySelector('.toast-content i');
    const contentEl = toastEl.querySelector('.toast-content');
    if (iconEl && contentEl) {
        if (isError) {
            iconEl.className = 'fas fa-exclamation-circle text-red-500';
            contentEl.style.borderLeftColor = '#a12b2b';
        } else {
            iconEl.className = 'fas fa-check-circle text-gold';
            contentEl.style.borderLeftColor = 'var(--gold)';
        }
    }
    toastEl.classList.add('show');
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(hideToast, 3000);
}
function hideToast() {
    toastEl.classList.remove('show');
    if (toastTimeout) {
        clearTimeout(toastTimeout);
        toastTimeout = null;
    }
}

// ══════════════════════════════════════════════
// MAIN
// ══════════════════════════════════════════════
function fmtDate(d) {
    return d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
}

// ── VIEW PROGRESS MODAL ────────────────────────
// NOTE: Loading indicator for this modal is scoped INSIDE the modal only.
//       The global overlay loader and top progress bar are NOT triggered here.
function openViewModal(groupId) {
    const modal = document.getElementById('viewModal');
    const loading = document.getElementById('view_loading');
    const dataDiv = document.getElementById('view_data');
     window.__viewModalGroupId = groupId;
    modal.classList.add('active');
    loading.classList.remove('hidden');
    dataDiv.classList.add('hidden');

    // __silent: true → suppress global top progress bar for in-modal requests
    Promise.all([
        fetch(`/teacher/get-group-progress/${groupId}`, { __silent: true }).then(r => r.json()),
        fetch(`/teacher/get-group/${groupId}`, { __silent: true }).then(r => r.json())
    ])
    .then(([data, groupData]) => {
        loading.classList.add('hidden');
        dataDiv.classList.remove('hidden');
        const members = groupData.members || [];

        document.getElementById('view_modal_title').textContent = `Progress: ${data.group_name}`;
        document.getElementById('view_progress_label').textContent = data.overall_progress;
        document.getElementById('view_overall_progress').style.width = data.overall_progress + '%';

        const tbody = document.getElementById('view_milestones_tbody');
        tbody.innerHTML = '';
        let currentStage = null;

        data.milestones.forEach(m => {
            if (m.capstone_stage_id != currentStage) {
                currentStage = m.capstone_stage_id;
                const divider = document.createElement('tr');
                divider.className = 'stage-divider';
                divider.innerHTML = `<td colspan="3">${m.capstone_stage_title}</td>`;
                tbody.appendChild(divider);
            }

            const row = document.createElement('tr');
            row.className = m.is_completed ? 'row-completed' : (m.is_next ? 'row-next' : '');

            const dateHtml = `${fmtDate(m.start_date)}${m.due_date ? ' – ' + fmtDate(m.due_date) : ''}`;

            let statusBadge = m.is_completed
                ? `<span class="task-status completed">Completed ${fmtDate(m.completion_date)}</span>`
                : (m.is_next ? `<span class="task-status next">Next Step</span>` : `<span class="task-status pending">Pending</span>`);
            const taskHtml = `
                <span class="task-title">${m.title}</span>
                ${m.description ? `<span class="task-desc">${m.description}</span>` : ''}
                ${statusBadge}
            `;

            // ── Rubric milestone evaluations for THIS milestone ──
            const evaluations   = data.evaluations || [];
            const milestoneEvals = evaluations.filter(e => e.milestone_id == m.id);

            let remarksHtml = '';

            if (m.remarks) {
                const r = m.remarks;
                const statusText = r.remarks_status || (r.compiled ? 'On Time Compliance' : 'Late Submission');

                let statusClass = 'on-time', statusIcon = 'fa-circle-check';
                if (/late/i.test(statusText)) { statusClass = 'late'; statusIcon = 'fa-triangle-exclamation'; }
                else if (/early/i.test(statusText)) { statusClass = 'early'; statusIcon = 'fa-clock'; }
                else if (/considered/i.test(statusText)) { statusClass = 'on-time'; statusIcon = 'fa-circle-check'; }

                const absentNames = (m.absent_students && m.absent_students.length) ? m.absent_students : [];

                const absenceTableHtml = (!r.all_present && absentNames.length)
                    ? `<table class="absence-table">
                        <thead><tr><th>#</th><th>Absent Student</th></tr></thead>
                        <tbody>
                            ${absentNames.map((name, i) => `<tr><td>${i + 1}</td><td>${name}</td></tr>`).join('')}
                        </tbody>
                    </table>`
                    : '';

                const feedbackHtml = r.feedback ? `<div class="remark-feedback">"${r.feedback}"</div>` : '';

                // ── Detect issuance milestones ──
                const isRecommendationMilestone =
                    /issuance of recommendation/i.test(m.title || '')
                    || m.id === 5
                    || m.id === 17;

            

                const issueButtonHtml = (isRecommendationMilestone && data.is_adviser)
                    ? `<button type="button"
                            class="issue-rec-btn text-[#b88d3a] hover:text-[#8b6914] text-[10px]
                                    font-semibold mt-2 ml-3 focus:outline-none inline-flex items-center"
                            data-milestone-id="${m.id}"
                            data-doc-type="recommendation">
                            <i class="fas fa-award mr-1"></i> Issue Recommendation Sheet
                    </button>`
                    : '';

                

                const editButtonHtml = data.is_adviser
                    ? `<button type="button"
                            class="edit-remark-btn text-[#b88d3a] hover:text-[#8b6914] text-[10px]
                                    font-semibold mt-2 focus:outline-none block"
                            data-milestone-id="${m.id}">
                            <i class="fas fa-edit mr-1"></i>Edit Remark
                    </button>`
                    : '';

                const isLate     = /late/i.test(statusText);
                const isEarly    = /early/i.test(statusText);
                const isOnTime   = /on time/i.test(statusText);
                const isConsidered = /considered/i.test(statusText);

                remarksHtml = `
                    <div class="remark-summary" data-milestone-id="${m.id}">
                        <div class="remark-view-mode">
                            <span class="remark-status-badge ${statusClass}">
                                <i class="fa-solid ${statusIcon}"></i> ${statusText}
                            </span>
                            ${r.deduction_points ? `<span class="remark-deduction"><i class="fa-solid fa-minus"></i> ${r.deduction_points} pts deduction</span>` : ''}
                            <span class="remark-attendance"><i class="fa-solid fa-user-group"></i> ${r.all_present ? 'All members present' : `${absentNames.length} member(s) absent`}</span>
                            ${absenceTableHtml}
                            ${feedbackHtml}
                            ${editButtonHtml}
                             ${issueButtonHtml}
                        </div>

                        <div class="remark-edit-mode hidden mt-2 p-3 bg-[#faf8f4] border border-[#e2dacf] rounded-lg space-y-2">
                            <div>
                                <label class="form-label text-[10px]">Remarks Status</label>
                                <select class="form-select text-xs remark-edit-status">
                                    <option value="On Time Compliance" ${isOnTime ? 'selected' : ''}>On Time Compliance</option>
                                    <option value="Early Submission"  ${isEarly ? 'selected' : ''}>Early Submission</option>
                                    <option value="Late Submission"   ${isLate ? 'selected' : ''}>Late Submission</option>
                                    <option value="Considered"        ${isConsidered ? 'selected' : ''}>Considered</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label text-[10px]">Deduction Points</label>
                                <input type="number" min="0" step="1"
                                    class="form-input text-xs remark-edit-deduction"
                                    value="${r.deduction_points || 0}">
                            </div>

                            <div>
                                <label class="form-label text-[10px]">Compiled</label>
                                <select class="form-select text-xs remark-edit-compiled">
                                    <option value="1" ${r.compiled ? 'selected' : ''}>Yes — Compiled</option>
                                    <option value="0" ${!r.compiled ? 'selected' : ''}>No — Not Compiled</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label text-[10px]">Feedback (optional)</label>
                                <textarea rows="2" class="form-input text-xs remark-edit-feedback">${r.feedback || ''}</textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-1">
                                <button type="button" class="btn-ghost text-xs remark-edit-cancel">Cancel</button>
                                <button type="button" class="btn-primary text-xs remark-edit-save">
                                    <i class="fas fa-save mr-1"></i> Save Override
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
            else if (m.has_rubric) {
                if (milestoneEvals.length > 0) {
                    // There IS an evaluation — the card is rendered below via evaluationHtml.
                    remarksHtml = '';
                } else if (data.is_adviser || data.is_panelist) {
                    remarksHtml = `
                        <div class="flex flex-col gap-1.5 items-start">
                            <span class="task-status next"
                                style="background-color: rgba(10,20,40,0.08);
                                        color: var(--navy);
                                        border: 1px solid rgba(10,20,40,0.15);">
                                <i class="fa-solid fa-circle-info mr-1"></i> Panelist Evaluation Only
                            </span>
                        </div>`;
                } else {
                    remarksHtml = `<span class="task-status pending">
                        <i class="fa-solid fa-clock mr-1"></i> Awaiting Panelist Evaluation
                    </span>`;
                }
            }
            else if (m.is_next && data.is_adviser) {
                remarksHtml = `
                    <div class="mb-2">
                        <label class="form-label text-[10px]">Attendance</label>
                        <div class="flex flex-col gap-1 mt-1">
                            <label class="flex items-center gap-2 text-xs cursor-pointer">
                                <input type="radio" name="attendance_${m.id}" value="present" class="attendance-radio" checked> All present
                            </label>
                            <label class="flex items-center gap-2 text-xs cursor-pointer">
                                <input type="radio" name="attendance_${m.id}" value="absent" class="attendance-radio"> Some absent
                            </label>
                        </div>
                    </div>
                    <div id="absent_container_${m.id}" class="mb-2 hidden">
                        <label class="form-label text-[10px]">Absent Students</label>
                        <div id="absent_list_${m.id}" class="grid grid-cols-1 gap-1 mt-1 p-2 border border-[#e2dacf] rounded-lg bg-[#faf8f4] max-h-28 overflow-y-auto text-xs"></div>
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-input text-xs remark-feedback-input" placeholder="Optional remarks...">
                    </div>
                    <button type="button" class="btn-primary text-xs submit-remark-btn" data-milestone-id="${m.id}">
                        <i class="fas fa-check mr-1"></i> Evaluate
                    </button>
                `;
            } else if (m.is_next && !data.is_adviser) {
                remarksHtml = `<span class="task-status next">Next Step — awaiting adviser evaluation</span>`;
            } else {
                remarksHtml = `<span class="remark-empty">Not yet available</span>`;
            }

            // ── Panelist evaluation summary (score + feedback, no rubric breakdown) ──
let evaluationHtml = '';

if (milestoneEvals.length > 0) {
   
} else if (m.has_rubric) {
    evaluationHtml = `
        <div class="mt-3 text-xs text-[#5b6375] italic bg-[#faf8f4] p-2 border border-[#e2dacf] rounded-lg">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1.5"></i>
            This group has not been evaluated yet for this milestone.
        </div>`;
}

row.innerHTML = `<td>${dateHtml}</td><td>${taskHtml}</td><td>${remarksHtml}${evaluationHtml}</td>`;
tbody.appendChild(row);

            // ── Adviser-only remark override wiring ──
            if (m.remarks && data.is_adviser) {
                const summary   = row.querySelector('.remark-summary');
                if (summary) {
                    const viewMode   = summary.querySelector('.remark-view-mode');
                    const editMode   = summary.querySelector('.remark-edit-mode');
                    const editBtn    = summary.querySelector('.edit-remark-btn');
                    const cancelBtn  = summary.querySelector('.remark-edit-cancel');
                    const saveBtn    = summary.querySelector('.remark-edit-save');

                    editBtn?.addEventListener('click', () => {
                        viewMode.classList.add('hidden');
                        editMode.classList.remove('hidden');
                    });

                    cancelBtn?.addEventListener('click', () => {
                        editMode.classList.add('hidden');
                        viewMode.classList.remove('hidden');
                    });

                    saveBtn?.addEventListener('click', () => {
                        const status      = summary.querySelector('.remark-edit-status').value;
                        const deduction   = parseInt(summary.querySelector('.remark-edit-deduction').value, 10) || 0;
                        const compiled    = summary.querySelector('.remark-edit-compiled').value === '1';
                        const feedback    = summary.querySelector('.remark-edit-feedback').value || '';

                        const originalHtml = saveBtn.innerHTML;
                        saveBtn.disabled = true;
                        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';

                        fetch('/teacher/update-remark', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                group_id:         groupId,
                                milestone_id:     m.id,
                                remarks_status:   status,
                                deduction_points: deduction,
                                compiled:         compiled,
                                feedback:         feedback,
                            }),
                        })
                        .then(async res => {
                            const data = await res.json().catch(() => ({}));
                            if (!res.ok) throw data;
                            return data;
                        })
                        .then(() => {
                            showToast('Remark updated successfully.');
                            openViewModal(groupId);
                        })
                        .catch(err => {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalHtml;
                            showToast((err && err.error) || 'Failed to update remark.', true);
                        });
                    });
                }
            }

            // ── Adviser-only "Next Step" evaluation wiring ──
            if (!m.remarks && m.is_next && data.is_adviser) {
                const submitBtn = row.querySelector('.submit-remark-btn');
                if (submitBtn) {
                    const absentList = row.querySelector(`#absent_list_${m.id}`);
                    if (absentList) {
                        absentList.innerHTML = members.length
                            ? members.map(mem => `
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" value="${mem.user_id}" class="absent-checkbox">
                                    ${mem.name} <span class="text-[#5b6375]">(${mem.user_id})</span>
                                </label>`).join('')
                            : '<span class="text-[#5b6375]">No members found.</span>';
                    }
                    row.querySelectorAll(`input[name="attendance_${m.id}"]`).forEach(radio => {
                        radio.addEventListener('change', function () {
                            row.querySelector(`#absent_container_${m.id}`).classList.toggle('hidden', this.value !== 'absent');
                        });
                    });
                    submitBtn.addEventListener('click', function () {
                        const attendance = row.querySelector(`input[name="attendance_${m.id}"]:checked`)?.value || 'present';
                        const checkedAbsent = row.querySelectorAll('.absent-checkbox:checked');
                        if (attendance === 'absent' && checkedAbsent.length === 0) {
                            showToast('Please select at least one absent student.', true);
                            return;
                        }
                        submitRemarkEvaluation(groupId, m.id, this, row);
                    });
                }
            }


            // ══════════════════════════════════════════════════════════════
            // Issuance wiring (recommendation / approval) — stays inside the loop
            // ══════════════════════════════════════════════════════════════
            row.querySelectorAll('.issue-rec-btn').forEach(issueBtn => {
                const docType = issueBtn.dataset.docType || 'recommendation';
                const labelMap = {
                    recommendation: 'Recommendation Sheet',
                    approval:       'Approval Sheet',
                };
                const label = labelMap[docType] || 'Document';

                // Reflect any prior issuance
                fetch(`/teacher/get-sheet-status/${groupId}?type=${docType}`, { __silent: true })
                    .then(r => r.json())
                    .then(status => {
                        if (status.issued) {
                            issueBtn.outerHTML = `
                                <span class="inline-flex items-center gap-1 mt-2 ml-3 text-[10px]
                                             font-semibold text-[#1e6b3a]">
                                    <i class="fas fa-check-circle"></i>
                                    ${label} Issued (${fmtDate(status.issued_date)})
                                </span>`;
                        }
                    })
                    .catch(() => {});

                issueBtn.addEventListener('click', function () {
                    issueCertificateSheet(groupId, this.dataset.milestoneId, docType, this);
                });
            });
        }); // ← end of forEach(m => { ... })

               // ── Document cards: Recommendation / Revision / Approval ──
        const recBtn = document.getElementById('view_rec_btn');
        const revBtn = document.getElementById('view_rev_btn');
        const aprBtn = document.getElementById('view_apr_btn');
        const recStatus = document.getElementById('view_rec_status');
        const revStatus = document.getElementById('view_rev_status');
        const aprStatus = document.getElementById('view_apr_status');

        // Reset to default disabled state
        [recBtn, revBtn, aprBtn].forEach(b => { if (b) b.disabled = true; });
        if (recStatus) recStatus.textContent = 'View the recommendation sheet issued to this group.';
        if (revStatus) revStatus.textContent = 'View the revision sheet submitted for this group.';
        if (aprStatus) aprStatus.textContent = 'View the approval sheet issued to this group.';

        // Recommendation
        fetch(`/teacher/get-sheet-status/${groupId}?type=recommendation`, { __silent: true })
            .then(r => r.json())
            .then(s => {
                if (s.issued) {
                    if (recBtn) recBtn.disabled = false;
                    if (recStatus) recStatus.innerHTML =
                        '<span style="color:#1e6b3a;"><i class="fas fa-check-circle mr-1"></i>Issued ' +
                        fmtDate(s.issued_date) + '</span>';
                } else if (recStatus) {
                    recStatus.innerHTML =
                        '<span style="color:#8a5d0b;"><i class="fas fa-hourglass-half mr-1"></i>Not yet issued</span>';
                }
            })
            .catch(() => {});

        // Revision
        fetch(`/teacher/get-revision-details/${groupId}`, { __silent: true })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (data && (data.chapters?.length || data.iot_findings?.length || data.objectives?.length)) {
                    if (revBtn) revBtn.disabled = false;
                    if (revStatus) revStatus.innerHTML =
                        '<span style="color:#1e6b3a;"><i class="fas fa-check-circle mr-1"></i>Revision available</span>';
                } else if (revStatus) {
                    revStatus.innerHTML =
                        '<span style="color:#8a5d0b;"><i class="fas fa-hourglass-half mr-1"></i>No revision submitted yet</span>';
                }
            })
            .catch(() => {});

        // Approval
        fetch(`/teacher/get-sheet-status/${groupId}?type=approval`, { __silent: true })
            .then(r => r.json())
            .then(s => {
                if (s.issued) {
                    if (aprBtn) aprBtn.disabled = false;
                    if (aprStatus) aprStatus.innerHTML =
                        '<span style="color:#1e6b3a;"><i class="fas fa-check-circle mr-1"></i>Issued ' +
                        fmtDate(s.issued_date) + '</span>';
                } else if (aprStatus) {
                    aprStatus.innerHTML =
                        '<span style="color:#8a5d0b;"><i class="fas fa-hourglass-half mr-1"></i>Not yet issued</span>';
                }
            })
            .catch(() => {});
    })
    .catch(() => {
        loading.classList.add('hidden');
        dataDiv.classList.add('hidden');
        showToast('Failed to load group progress.', true);
    });
}

function submitRemarkEvaluation(groupId, milestoneId, btn, row) {
    const attendance = row.querySelector(`input[name="attendance_${milestoneId}"]:checked`)?.value || 'present';
    const absentIds = Array.from(row.querySelectorAll('.absent-checkbox:checked')).map(cb => cb.value);
    const feedback = row.querySelector('.remark-feedback-input')?.value || '';

    const restore = setButtonLoading(btn, 'Saving…');

    fetch('/teacher/evaluate-remark', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ group_id: groupId, milestone_id: milestoneId, attendance, absent_students: absentIds, feedback })
    })
    .then(async r => {
        const data = await r.json();
        if (!r.ok) throw data;
        return data;
    })
    .then(data => {
        showToast('Milestone evaluated — ' + data.remarks.remarks_status);
        const pctEl = document.querySelector(`.progress-complete-label[data-group-id="${groupId}"] .progress-pct`);
        if (pctEl) pctEl.textContent = data.overall_progress;
        openViewModal(groupId);
    })
    .catch(err => {
        restore();
        showToast((err && err.error) || 'Failed to save evaluation.', true);
    });
}

/**
 * Issue the Recommendation Sheet for a group.
 * Only called from the issuance-milestone row in the view modal.
 */
function issueRecommendationSheet(groupId, milestoneId, btn) {
    if (!confirm('Issue the Recommendation Sheet to this group?\n\nStudents will then be able to view and print it.')) {
        return;
    }

    const restore = setButtonLoading(btn, 'Issuing…');

    fetch('/teacher/issue-recommendation-sheet', {
        method: 'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
            'Accept':        'application/json',
        },
        body: JSON.stringify({
            group_id:     groupId,
            milestone_id: milestoneId,
        }),
    })
    .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok) throw data;
        return data;
    })
    .then(data => {
        showToast(data.message || 'Recommendation sheet issued successfully.');
        openViewModal(groupId);   // refresh the modal state
    })
    .catch(err => {
        restore();
        showToast((err && err.error) || 'Failed to issue recommendation sheet.', true);
    });
}
/**
 * Unified issuance of recommendation / approval / revision sheet.
 */
function issueCertificateSheet(groupId, milestoneId, docType, btn) {
    const labelMap = {
        recommendation: 'Recommendation Sheet',
        approval:       'Approval Sheet',
        revision:       'Revision Sheet',
    };
    const label = labelMap[docType] || 'Document';

    if (!confirm(`Issue the ${label} to this group?\n\nStudents will then be able to view and print it.`)) {
        return;
    }

    const restore = setButtonLoading(btn, 'Issuing…');

    fetch('/teacher/issue-sheet', {
        method: 'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
            'Accept':        'application/json',
        },
        body: JSON.stringify({
            group_id:      groupId,
            milestone_id:  milestoneId,
            document_type: docType,
        }),
    })
    .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok) throw data;
        return data;
    })
    .then(data => {
        showToast(data.message || `${label} issued successfully.`);
        openViewModal(groupId);   // refresh modal state
    })
    .catch(err => {
        restore();
        showToast((err && err.error) || `Failed to issue ${label}.`, true);
    });
}
window.issueCertificateSheet = issueCertificateSheet;

document.addEventListener('DOMContentLoaded', function () {

    @if(session('success'))
        showToast('{{ session('success') }}');
    @endif
    @if(session('error'))
        showToast('{{ session('error') }}', true);
    @endif
    @if($errors->any())
        showToast('{{ $errors->first() }}', true);
    @endif

    // ── ATTENDANCE TOGGLE LOGIC ──────────────────────
    const attendanceRadios = document.querySelectorAll('input[name="attendance"]');
    if (attendanceRadios.length) {
        attendanceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const container = document.getElementById('absent_students_container');
                if (this.value === 'absent') {
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                    document.querySelectorAll('#student_checklist input[type="checkbox"]').forEach(cb => cb.checked = false);
                }
            });
        });
    }

    // ── SECTION SWITCHING ──────────────────────
    const sections = {
        dashboard: document.getElementById('dashboard-section'),
        assignedsections: document.getElementById('assignedsections-section'),
        sections: document.getElementById('sections-section'),
        evaluate: document.getElementById('evaluate-section'),
        profile: document.getElementById('profile-section'),
    };
    const navLinks = document.querySelectorAll('.nav-link');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

    function activateSection(sectionId) {
        Object.values(sections).forEach(s => s && s.classList.add('hidden'));
        if (sections[sectionId]) sections[sectionId].classList.remove('hidden');
        navLinks.forEach(link => {
            const isActive = link.dataset.section === sectionId;
            link.classList.toggle('active-link', isActive);
            link.style.color = isActive ? 'var(--gold)' : 'rgba(255,255,255,0.65)';
        });
        mobileNavLinks.forEach(link => {
            link.style.color = link.dataset.section === sectionId ? 'var(--gold)' : 'rgba(255,255,255,0.55)';
        });
    }

    [...navLinks, ...mobileNavLinks].forEach(link =>
        link.addEventListener('click', e => {
            e.preventDefault();
            const s = link.dataset.section;
            if (s && sections[s]) activateSection(s);
        })
    );
    activateSection('dashboard');

    // ── EVALUATION FORM SUBMIT — show loader ──
    const evalForm = document.getElementById('evaluation_form');
    if (evalForm) {
        evalForm.addEventListener('submit', function () {
            showPageLoader('Submitting evaluation…');
        });
    }

    // ── CREATE GROUP MODAL ─────────────────────
    const sectionSelect = document.getElementById('sectionSelect');
    const studentSelect = document.getElementById('studentSelect');
    const addBtn = document.getElementById('addStudentsBtn');
    const container = document.getElementById('selectedStudentsContainer');
    const noMsg = document.getElementById('noStudentsMsg');
    let idx = 0;

    window.openCreateGroupModal = function (sectionName = null) {
        const modal = document.getElementById('createGroupModal');
        if (!modal) return;
        modal.classList.add('active');
        if (sectionName) {
            sectionSelect.value = sectionName;
            sectionSelect.dispatchEvent(new Event('change'));
        } else {
            sectionSelect.value = '';
            studentSelect.innerHTML = '<option disabled>Select a section first</option>';
        }
    };

    if (sectionSelect) {
        sectionSelect.addEventListener('change', function () {
            const section = this.value;
            studentSelect.innerHTML = '<option disabled>Loading...</option>';
            if (!section) {
                studentSelect.innerHTML = '<option disabled>Select a section first</option>';
                return;
            }
            fetch(`/teacher/get-students/${encodeURIComponent(section)}`)
                .then(r => r.json())
                .then(students => {
                    studentSelect.innerHTML = '';
                    if (students.length === 0) {
                        studentSelect.innerHTML = '<option disabled>No students in this section</option>';
                        return;
                    }
                    students.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.user_id;
                        opt.textContent = `${s.student_first_name} ${s.student_last_name} (${s.user_id})`;
                        studentSelect.appendChild(opt);
                    });
                })
                .catch(() => studentSelect.innerHTML = '<option disabled>Error loading students</option>');
        });
    }

    function addRow(user_id, name) {
        if (container.querySelector(`input[value="${user_id}"]`)) {
            showToast('Student already added.');
            return;
        }
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded border border-[#e2dacf] flex-nowrap whitespace-nowrap';
        row.innerHTML = `<input type="hidden" name="students[${idx}][user_id]" value="${user_id}">
            <span class="flex-1 text-sm">${name}</span>
            <select name="students[${idx}][role]" class="form-select w-32" required>
                <option disabled selected>Role</option>
                <option value="programmer">Programmer</option>
                <option value="designer">Designer</option>
                <option value="researcher">Researcher</option>
            </select>
            <button type="button" class="remove-student text-[#5b6375] hover:text-red-500 transition"><i class="fas fa-times"></i></button>`;
        container.appendChild(row);
        idx++;
        noMsg.style.display = 'none';
        row.querySelector('.remove-student').addEventListener('click', () => {
            row.remove();
            if (!container.children.length) noMsg.style.display = 'block';
        });
    }

    if (addBtn && studentSelect) {
        addBtn.addEventListener('click', () => {
            Array.from(studentSelect.selectedOptions).forEach(o => {
                if (o.value) addRow(o.value, o.textContent);
            });
            studentSelect.selectedIndex = -1;
        });
    }

    // ── REVISION HELPER FUNCTIONS ────────────────
    window.openRevisionModal = function (groupId, groupName, capstoneTitle) {
        document.getElementById('revision_group_id').value = groupId;
        document.getElementById('revision_group_name').value = groupName || '';
        document.getElementById('revision_capstone_title').value = capstoneTitle || '';
        document.getElementById('revision_description_input').value = '';

        document.getElementById('revision_chapter_rows').innerHTML = '';
        document.getElementById('revision_iot_rows').innerHTML = '';
        document.getElementById('revision_objectives_list').innerHTML = '';

        const proponentsContainer = document.getElementById('revision_proponents_list');
        proponentsContainer.innerHTML = '<span class="text-xs text-[#5b6375] italic">Loading team members…</span>';

        openModal('revisionModal');

        fetch(`/teacher/get-group/${groupId}`)
            .then(r => r.json())
            .then(data => {
                proponentsContainer.innerHTML = '';
                if (data.error || !data.members || data.members.length === 0) {
                    proponentsContainer.innerHTML = '<span class="text-xs text-[#5b6375] italic">No team members found.</span>';
                    return;
                }
                data.members.forEach(m => {
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-navy';
                    badge.innerHTML = `<i class="fa-regular fa-user mr-1"></i> ${m.name}${m.role ? ` (${m.role})` : ''}`;
                    proponentsContainer.appendChild(badge);
                });
            })
            .catch(() => {
                proponentsContainer.innerHTML = '<span class="text-xs text-red-500 italic">Failed to load team members.</span>';
            });
    };

    window.submitRevisionCheck = function (event) {
        event.preventDefault();
        const groupId = document.getElementById('check_group_id').value;
        const approvedBy = document.querySelector('#revision_check_form input[name="approved_by"]').value;

        const chapters = [];
        document.querySelectorAll('#check_chapters_tbody tr').forEach(row => {
            if (!row.dataset.chapter) return;
            chapters.push({
                chapter: row.dataset.chapter,
                findings: row.dataset.findings,
                completed: row.querySelector('[data-role="chapter-completed"]')?.checked || false,
                remarks: row.querySelector('[data-role="chapter-remarks"]')?.value || '',
            });
        });

        const iot = [];
        document.querySelectorAll('#check_iot_tbody tr').forEach(row => {
            if (!row.dataset.finding) return;
            iot.push({
                finding: row.dataset.finding,
                completed: row.querySelector('[data-role="iot-completed"]')?.checked || false,
                remarks: row.querySelector('[data-role="iot-remarks"]')?.value || '',
            });
        });

        const objectives = [];
        document.querySelectorAll('#check_objectives_list tbody tr').forEach(row => {
            if (!row.dataset.objective) return;
            objectives.push({
                objective: row.dataset.objective,
                completed: row.querySelector('[data-role="objective-completed"]')?.checked || false,
                remarks: row.querySelector('[data-role="objective-remarks"]')?.value || '',
            });
        });

        const submitBtn = event.target.querySelector('button[type="submit"]');
        const restore = setButtonLoading(submitBtn, 'Submitting…');

        fetch(`/teacher/group/${groupId}/verify-revision`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ approved_by: approvedBy, chapters, iot, objectives })
        })
        .then(async r => {
            const data = await r.json().catch(() => null);
            if (!r.ok) throw new Error(data?.error || 'Failed to submit verification.');
            return data;
        })
        .then(data => {
            closeModal('revisionCheckModal');
            showToast(data.message || 'Revision verified successfully!');
            softReload(1000);
        })
        .catch(err => {
            restore();
            showToast(err.message || 'Failed to submit verification.', true);
        });
    };

    window.submitRevisionRequest = function (event) {
        event.preventDefault();
        const groupId = document.getElementById('revision_group_id').value;
        const description = document.getElementById('revision_description_input').value;

        const chapters = [];
        document.querySelectorAll('#revision_chapter_rows > div').forEach(row => {
            const inputs = row.querySelectorAll('input[type="text"]');
            if (inputs[0]?.value) {
                chapters.push({ chapter: inputs[0].value, findings: inputs[1]?.value || '' });
            }
        });

        const iotFindings = [];
        document.querySelectorAll('#revision_iot_rows > div').forEach(row => {
            const input = row.querySelector('input[type="text"]');
            if (input?.value) {
                iotFindings.push({ finding: input.value });
            }
        });

        const objectives = [];
        document.querySelectorAll('#revision_objectives_list > div').forEach(row => {
            const input = row.querySelector('input[type="text"]');
            if (input?.value) {
                objectives.push(input.value);
            }
        });

        fetch(`/teacher/group/${groupId}/request-revision`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                revision_description: description,
                chapters: chapters,
                iot_findings: iotFindings,
                additional_objectives: objectives
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('revisionModal');
                showToast(data.message || 'Revision request submitted successfully!');
                softReload(1000);
            } else {
                showToast(data.error || 'Failed to submit revision request.', true);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('An error occurred while submitting revision request.', true);
        });
    };

    window.markGroupAsRevised = function (groupId) {
        if (!confirm('Are you sure you have addressed all revisions and want to mark this group as revised?')) {
            return;
        }

        fetch(`/teacher/group/${groupId}/mark-revised`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Group marked as revised!');
                softReload(1000);
            } else {
                showToast(data.error || 'Failed to mark group as revised.', true);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('An error occurred.', true);
        });
    };

    // ══════════════════════════════════════════════
    // NEW HELPER FUNCTIONS FOR EVAL MODAL
    // ══════════════════════════════════════════════
    function escHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function populateReviseFields(data, readOnly) {
        const chaptersContainer = document.getElementById('eval_rev_chapter_rows');
        const iotContainer = document.getElementById('eval_rev_iot_rows');
        const objectivesContainer = document.getElementById('eval_rev_objectives_list');
        const descriptionInput = document.getElementById('eval_rev_description_input');

        chaptersContainer.innerHTML = '';
        iotContainer.innerHTML = '';
        objectivesContainer.innerHTML = '';

        if (!data) {
            if (!readOnly) {
                addEvalRevChapterRow();
                addEvalRevIotRow();
                addEvalRevObjective();
            }
            descriptionInput.value = '';
            return;
        }

        // Chapters
        if (data.chapters && data.chapters.length) {
            data.chapters.forEach(ch => {
                const row = document.createElement('div');
                row.className = 'flex flex-col md:flex-row gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
                row.innerHTML = `
                    <input type="text" class="form-input flex-1 text-sm eval-rev-chapter" value="${escHtml(ch.chapter)}" placeholder="Chapter" ${readOnly ? 'disabled' : ''}>
                    <input type="text" class="form-input flex-1 text-sm eval-rev-findings" value="${escHtml(ch.findings)}" placeholder="Document Findings" ${readOnly ? 'disabled' : ''}>
                    <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2" ${readOnly ? 'disabled' : ''}><i class="fas fa-times"></i></button>
                `;
                chaptersContainer.appendChild(row);
            });
        } else if (!readOnly) {
            addEvalRevChapterRow();
        }

        // IoT
        if (data.iot_findings && data.iot_findings.length) {
            data.iot_findings.forEach(iot => {
                const row = document.createElement('div');
                row.className = 'flex flex-col md:flex-row gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
                row.innerHTML = `
                    <input type="text" class="form-input flex-1 text-sm eval-rev-iot" value="${escHtml(iot.finding)}" placeholder="Finding / Enhancement" ${readOnly ? 'disabled' : ''}>
                    <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2" ${readOnly ? 'disabled' : ''}><i class="fas fa-times"></i></button>
                `;
                iotContainer.appendChild(row);
            });
        } else if (!readOnly) {
            addEvalRevIotRow();
        }

        // Objectives
        if (data.additional_objectives && data.additional_objectives.length) {
            data.additional_objectives.forEach(obj => {
                const text = typeof obj === 'object' ? obj.objective : obj;
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
                row.innerHTML = `
                    <input type="text" class="form-input flex-1 text-sm eval-rev-objective" value="${escHtml(text)}" placeholder="Additional objective" ${readOnly ? 'disabled' : ''}>
                    <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2" ${readOnly ? 'disabled' : ''}><i class="fas fa-times"></i></button>
                `;
                objectivesContainer.appendChild(row);
            });
        } else if (!readOnly) {
            addEvalRevObjective();
        }

        if (data.overall_remarks) {
            descriptionInput.value = data.overall_remarks;
        } else {
            descriptionInput.value = '';
        }
        if (readOnly) {
            descriptionInput.disabled = true;
            descriptionInput.classList.add('bg-[#f0ece4]');
        } else {
            descriptionInput.disabled = false;
            descriptionInput.classList.remove('bg-[#f0ece4]');
        }
    }

    function setEvalModeReadOnly(message) {
        document.querySelectorAll('#evaluation_form input, #evaluation_form select, #evaluation_form textarea').forEach(el => {
            if (el.type !== 'hidden') el.disabled = true;
        });
        document.querySelectorAll('#eval_revision_sheet_content input, #eval_revision_sheet_content select, #eval_revision_sheet_content textarea, #eval_revision_sheet_content button').forEach(el => {
            el.disabled = true;
        });
        document.getElementById('eval_submit_btn').style.display = 'none';
        const footer = document.querySelector('.eval-modal-footer');
        const msg = document.createElement('p');
        msg.className = 'text-sm text-[#5b6375] italic flex-1';
        msg.textContent = message;
        footer.insertBefore(msg, footer.firstChild);
    }

    function displayEvaluation(evalData) {
        const rubricContainer = document.getElementById('rubric_container');
        const tbody = document.getElementById('criteria_tbody');
        const criteria = Array.isArray(evalData.criteria) ? evalData.criteria : [];
        const score = evalData.score ?? 0;
        const maxScore = evalData.max_score ?? (criteria.length * 4);

        rubricContainer.classList.remove('hidden');
        const rubricName = document.getElementById('rubric_name_display');
        if (rubricName) rubricName.textContent = evalData.rubric_name || evalData.milestone_title || 'Submitted Rubric';

        if (criteria.length) {
            tbody.innerHTML = criteria.map((criterion, index) => {
                const criterionId = criterion.id ?? criterion.criteria_id ?? index;
                const givenScore = Number(criterion.given_score ?? criterion.score ?? 0);
                return `<tr class="border-b border-[#e2dacf]" data-crit-id="${escHtml(criterionId)}">
                    <td class="py-2">${escHtml(criterion.criteria_name || criterion.name || `Criterion ${index + 1}`)}</td>
                    ${[1, 2, 3, 4].map(value => `<td class="text-center"><input type="radio" name="readonly_rubric_scores_${escHtml(criterionId)}" value="${value}" class="criteria-score" ${givenScore === value ? 'checked' : ''} disabled></td>`).join('')}
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-[#1e6b3a]">Evaluation already submitted: Score ${escHtml(score)} / ${escHtml(maxScore)}</td></tr>`;
        }

        document.getElementById('total_score_display').textContent = score;
        document.getElementById('total_max').textContent = maxScore;
        document.getElementById('eval_total_score').value = score;
        document.getElementById('eval_max_score').value = maxScore;
        const feedback = document.querySelector('#evaluation_form textarea[name="feedback"]');
        if (feedback) {
            feedback.value = evalData.feedback || '';
            feedback.disabled = true;
        }
        document.querySelectorAll('#evaluation_form input[name="attendance"]').forEach(r => r.disabled = true);
    }

    // ── EVALUATION MODAL ────────────────────────
    window.openGroupRevisionsModal = function (groupId, groupName) {
    document.getElementById('grm_title').textContent = `Revisions — ${groupName}`;
    const content = document.getElementById('grm_content');
    content.innerHTML = window.skeletonCards(2);

    openModal('groupRevisionsModal');
    showPageLoader('Loading revisions…');

    fetch(`/teacher/get-all-revisions/${groupId}`)
        .then(async r => {
            const data = await r.json();
            if (!r.ok) throw new Error(data.error || 'Failed to load revisions.');
            return data;
        })
        .then(data => {
            const revisions = data.revisions || [];

            if (revisions.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-10 text-[#5b6375]">
                        <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>
                        No panelist has submitted a revision for this group.
                    </div>
                `;
                hidePageLoader();
                return;
            }

            content.innerHTML = revisions.map(rev => {
                const chapterRows = (rev.chapters && rev.chapters.length)
                    ? rev.chapters.map(ch => `
                        <tr class="border-b border-[#e2dacf]">
                            <td class="p-2 pl-3 font-semibold">${escHtml(ch.chapter)}</td>
                            <td class="p-2">${escHtml(ch.findings)}</td>
                            <td class="p-2 pr-3 text-center">
                                <span class="badge ${String(ch.remarks).toLowerCase() === 'completed' ? 'badge-green' : 'badge-amber'}">${escHtml(ch.remarks)}</span>
                            </td>
                        </tr>
                    `).join('')
                    : `<tr><td colspan="3" class="p-3 text-center text-[#5b6375]">No chapter findings.</td></tr>`;

                const iotRows = (rev.iot_findings && rev.iot_findings.length)
                    ? rev.iot_findings.map(iot => `
                        <tr class="border-b border-[#e2dacf]">
                            <td class="p-2 pl-3">${escHtml(iot.finding)}</td>
                            <td class="p-2 pr-3 text-center">
                                <span class="badge ${String(iot.remarks).toLowerCase() === 'completed' ? 'badge-green' : 'badge-amber'}">${escHtml(iot.remarks)}</span>
                            </td>
                        </tr>
                    `).join('')
                    : `<tr><td colspan="2" class="p-3 text-center text-[#5b6375]">No IoT findings.</td></tr>`;

                const objectiveRows = (rev.additional_objectives && rev.additional_objectives.length)
                    ? rev.additional_objectives.map(obj => `
                        <tr class="border-b border-[#e2dacf]">
                            <td class="p-2 pl-3">${escHtml(obj.objective)}</td>
                            <td class="p-2 pr-3 text-center">
                                <span class="badge ${String(obj.remarks).toLowerCase() === 'completed' ? 'badge-green' : 'badge-amber'}">${escHtml(obj.remarks)}</span>
                            </td>
                        </tr>
                    `).join('')
                    : `<tr><td colspan="2" class="p-3 text-center text-[#5b6375]">No additional objectives.</td></tr>`;

                return `
                    <div class="content-card">
                        <div class="card-accent"></div>
                        <div class="p-5">
                            <div class="flex justify-between items-start gap-3 mb-3">
                                <div>
                                    <p class="font-bold text-sm text-[#0a1428]"><i class="fa-regular fa-user mr-1 text-[#d6b15c]"></i> ${escHtml(rev.panelist_name)}</p>
                                    <p class="text-xs text-[#5b6375]">${fmtDate(rev.created_at)}</p>
                                </div>
                            </div>

                            ${rev.overall_remarks ? `<div class="mb-3 p-3 bg-[#faf8f4] border border-[#e2dacf] rounded-lg text-sm italic text-[#5b6375]">"${escHtml(rev.overall_remarks)}"</div>` : ''}

                            <p class="form-fieldset-title mt-2"><i class="fa-solid fa-book"></i> Chapter / Document Findings</p>
                            <div class="overflow-x-auto mb-3">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold border-b border-[#e2dacf]">
                                            <th class="p-2 pl-3">Chapter</th><th class="p-2">Findings</th><th class="p-2 pr-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>${chapterRows}</tbody>
                                </table>
                            </div>

                            <p class="form-fieldset-title"><i class="fa-solid fa-microchip"></i> System / IoT Findings</p>
                            <div class="overflow-x-auto mb-3">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold border-b border-[#e2dacf]">
                                            <th class="p-2 pl-3">Finding</th><th class="p-2 pr-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>${iotRows}</tbody>
                                </table>
                            </div>

                            <p class="form-fieldset-title"><i class="fa-solid fa-list-check"></i> Additional Objectives</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold border-b border-[#e2dacf]">
                                            <th class="p-2 pl-3">Objective</th><th class="p-2 pr-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>${objectiveRows}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            hidePageLoader();
        })
        .catch(err => {
            content.innerHTML = `<p class="text-sm text-red-500 text-center py-8">❌ ${err.message}</p>`;
            hidePageLoader();
        });
};

    window.openEvaluationModal = function (groupId, milestoneId = null) {
        document.getElementById('eval_group_id').value = groupId;
        revisionSheetDirty = false;
        revisionEditorReadOnly = false;

        const container = document.querySelector(`.group-item[data-group-id="${groupId}"]`);
        const groupNameField = document.getElementById('eval_group_name');
        if (container) {
            const name = container.dataset.groupName;
            if (name) groupNameField.value = name;
            else {
                const nameEl = container.querySelector('.font-bold, .font-semibold, h4');
                if (nameEl) groupNameField.value = nameEl.textContent.trim();
            }
        }

        const milestoneSelect = document.getElementById('milestone_select');
        milestoneSelect.innerHTML = '<option value="">Loading milestone…</option>';
        milestoneSelect.disabled = true;
        document.getElementById('rubric_container').classList.add('hidden');
        document.getElementById('criteria_tbody').innerHTML = '';
        document.getElementById('eval_total_score').value = '';
        document.getElementById('eval_max_score').value = '';

        openModal('evaluationModal');
        showPageLoader('Preparing evaluation form…');

        // ── Fetch all needed data ──
        Promise.all([
            fetch(`/teacher/get-group/${groupId}`).then(r => r.json()),
            fetch(`/teacher/get-revision-details/${groupId}`).then(r => r.json()).catch(() => null),
            fetch(`/teacher/get-my-evaluation/${groupId}`).then(r => r.json()).catch(() => null)
        ])
        .then(([groupData, revisionData, evalData]) => {
            window.currentGroupData = groupData;
            window.currentRevisionData = revisionData;
            window.currentEvaluationData = evalData;
            // ── Lock the milestone select to this group's room's required milestone ──
            const requiredMilestoneId = groupData.required_milestone_id;
            const requiredMilestoneTitle = groupData.required_milestone_title || 'Milestone';
            milestoneSelect.innerHTML = requiredMilestoneId
                ? `<option value="${requiredMilestoneId}" selected>${escHtml(requiredMilestoneTitle)}</option>`
                : `<option value="">No milestone assigned to this room</option>`;
            milestoneSelect.value = requiredMilestoneId ? String(requiredMilestoneId) : '';
            milestoneSelect.disabled = true;
            document.getElementById('eval_milestone_id').value = requiredMilestoneId || '';

            // ── Check if evaluation already exists ──
            if (evalData && evalData.score !== undefined) {
                setEvalModeReadOnly('This group has already been evaluated. All fields are read-only.');
                displayEvaluation(evalData);
                const readOnlyRevisionSheet = document.getElementById('eval_revision_sheet_content');
                if (readOnlyRevisionSheet) renderRevisionSheet(readOnlyRevisionSheet, groupId, true);
                hidePageLoader();
                return;
            }

            // ── Load group members for attendance checklist ──
            const checklist = document.getElementById('student_checklist');
            checklist.innerHTML = '<p class="text-xs text-[#5b6375] col-span-2 text-center py-2">Loading students…</p>';
            fetch(`/teacher/get-group/${groupId}`)
                .then(r => r.json())
                .then(data => {
                    checklist.innerHTML = '';
                    if (data.error || !data.members || data.members.length === 0) {
                        checklist.innerHTML = '<p class="text-xs text-[#5b6375] col-span-2 text-center py-2">No members found.</p>';
                        return;
                    }
                    data.members.forEach(m => {
                        const label = document.createElement('label');
                        label.className = 'flex items-center gap-2 cursor-pointer text-sm text-[#171e2c]';
                        label.innerHTML = `<input type="checkbox" name="absent_students[]" value="${m.user_id}" class="form-checkbox text-[#d6b15c] focus:ring-[#d6b15c]">
                            <span>${m.name} <span class="text-[#5b6375] text-xs">(${m.user_id})</span></span>`;
                        checklist.appendChild(label);
                    });
                })
                .catch(() => checklist.innerHTML = '<p class="text-xs text-red-500 col-span-2 text-center py-2">Failed to load students.</p>');

            // ── Disable already evaluated milestones ──
            Array.from(milestoneSelect.options).forEach(opt => {
                opt.disabled = false;
                opt.textContent = opt.textContent.replace(' (Already Evaluated)', '');
            });
            fetch(`/teacher/get-evaluated-milestones/${groupId}`)
                .then(r => r.json())
                .then(evaluatedIds => {
                    evaluatedIds.forEach(id => {
                        if (id == milestoneId) return;
                        const opt = milestoneSelect.querySelector(`option[value="${id}"]`);
                        if (opt) {
                            opt.disabled = true;
                            opt.textContent += ' (Already Evaluated)';
                        }
                    });
                })
                .catch(() => {});
            // Load the rubric for the locked milestone (not-yet-evaluated path)
            if (requiredMilestoneId) {
                milestoneSelect.dispatchEvent(new Event('change'));
            }
            // Reset attendance radio
            const presentRadio = document.querySelector('input[name="attendance"][value="present"]');
            if (presentRadio) presentRadio.checked = true;
            const absentContainer = document.getElementById('absent_students_container');
            if (absentContainer) absentContainer.classList.add('hidden');

            // ── Load revision sheet on the right ──
            const revisionSheetEl = document.getElementById('eval_revision_sheet_content');
            if (revisionSheetEl) renderRevisionSheet(revisionSheetEl, groupId);

            hidePageLoader();
        })
        .catch(err => {
            hidePageLoader();
            showToast('Failed to load data.', true);
            console.error(err);
        });
    };

    // ── Dynamic row adders (global) ──
    window.addEvalRevChapterRow = function() {
        const container = document.getElementById('eval_rev_chapter_rows');
        const row = document.createElement('div');
        row.className = 'flex flex-col md:flex-row gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
        row.innerHTML = `
            <input type="text" class="form-input flex-1 text-sm eval-rev-chapter" placeholder="Chapter (e.g., Chapter 1, Chapter 2)">
            <input type="text" class="form-input flex-1 text-sm eval-rev-findings" placeholder="Document Findings">
            <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    };

    window.addEvalRevIotRow = function() {
        const container = document.getElementById('eval_rev_iot_rows');
        const row = document.createElement('div');
        row.className = 'flex flex-col md:flex-row gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
        row.innerHTML = `
            <input type="text" class="form-input flex-1 text-sm eval-rev-iot" placeholder="Findings / Enhancements / Recommendations">
            <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    };

    window.addEvalRevObjective = function() {
        const container = document.getElementById('eval_rev_objectives_list');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
        row.innerHTML = `
            <input type="text" class="form-input flex-1 text-sm eval-rev-objective" placeholder="Enter objective">
            <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    };

    // ── Open Rubric Scores Modal ──
    window.openRubricScoresModal = function (groupId, groupName = null) {
        openModal('rubricScoresModal');
        showPageLoader('Loading rubric scores…');
        const content = document.getElementById('rubricScoresContent');
        const titleEl = document.getElementById('rubricScoresTitle');
        const subtitleEl = document.getElementById('rubricScoresSubtitle');

        titleEl.textContent = groupName ? `Rubric Scores — ${groupName}` : 'Rubric Scores';
        subtitleEl.textContent = 'Panelist evaluation summary';
        content.innerHTML = window.skeletonCards(3);

        fetch(`/teacher/get-group-progress/${groupId}`)
            .then(r => r.json())
            .then(data => {
                if (!groupName) titleEl.textContent = `Rubric Scores — ${data.group_name}`;

            const evaluations = data.evaluations || [];

                if (evaluations.length === 0) {
                    content.innerHTML = `
                        <div class="rs-empty">
                            <i class="fa-regular fa-folder-open"></i>
                            No panelist evaluations have been submitted for this group yet.
                        </div>
                    `;
                    hidePageLoader();
                    return;
                }

                content.innerHTML = evaluations.map((ev, idx) => {
                    const initials = (ev.teacher_name || 'T')
                        .split(' ')
                        .map(w => w[0])
                        .join('')
                        .substring(0, 2)
                        .toUpperCase();

                    let criteriaHtml = '';
                    if (ev.criteria && ev.criteria.length > 0) {
                        criteriaHtml = `
                            <div id="rs_criteria_${idx}" class="hidden">
                                <table class="rs-criteria-table">
                                    <thead>
                                        <tr>
                                            <th>Criterion</th>
                                            <th style="text-align:center;">Max</th>
                                            <th style="text-align:center;">Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${ev.criteria.map(c => `
                                            <tr>
                                                <td>${c.criteria_name}</td>
                                                <td style="text-align:center;">${c.max_score}</td>
                                                <td style="text-align:center; font-weight:700; color:#1e6b3a;">${c.given_score}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                            <span class="rs-criteria-toggle" onclick="document.getElementById('rs_criteria_${idx}').classList.toggle('hidden'); this.querySelector('i').classList.toggle('fa-chevron-down'); this.querySelector('i').classList.toggle('fa-chevron-up');">
                                <i class="fas fa-chevron-down"></i> View criteria breakdown
                            </span>
                        `;
                    }

                    return `
                        <div class="rs-card">
                            <div class="rs-card-head">
                                <div>
                                    <p class="rs-milestone">${ev.milestone_title || 'Milestone'}</p>
                                    <div class="rs-panelist mt-1.5">
                                        <div class="rs-panelist-avatar">${initials}</div>
                                        <span class="rs-panelist-name">${ev.teacher_name || 'Panelist'}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="rs-score">${ev.score} <small>/ ${ev.max_score}</small></div>
                                    <p class="rs-date">${fmtDate(ev.evaluation_date)}</p>
                                </div>
                            </div>
                            ${ev.feedback ? `<div class="rs-feedback">"${ev.feedback}"</div>` : ''}
                            ${criteriaHtml}
                        </div>
                    `;
                }).join('');
                hidePageLoader();
            })
            .catch(() => {
                content.innerHTML = `
                    <div class="rs-empty">
                        <i class="fa-solid fa-triangle-exclamation" style="color:#a12b2b;"></i>
                        Failed to load rubric scores. Please try again.
                    </div>
                `;
                hidePageLoader();
            });
    };

    document.querySelectorAll('.evaluate-btn').forEach(btn =>
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const groupId = this.dataset.group;
            if (groupId) window.openEvaluationModal(groupId);
        })
    );

    // ── Milestone Select Change ──
    const milestoneSelect = document.getElementById('milestone_select');
    if (milestoneSelect) {
        milestoneSelect.addEventListener('change', function () {
            const milestoneId = this.value;
            const rubricContainer = document.getElementById('rubric_container');
            document.getElementById('eval_milestone_id').value = milestoneId;
            const tbody = document.getElementById('criteria_tbody');
            const rubricName = document.getElementById('rubric_name_display');
            if (!milestoneId) {
                rubricContainer.classList.add('hidden');
                tbody.innerHTML = '';
                return;
            }
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-[#5b6375]">Loading rubric...</td></tr>';
            rubricContainer.classList.remove('hidden');
            fetch(`/teacher/get-rubric/${milestoneId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">${data.error}</td></tr>`;
                        return;
                    }
                    rubricName.textContent = data.rubric_name;
                    let html = '';
                    data.criteria.forEach((c) => {
                        html += `<tr class="border-b border-[#e2dacf]" data-crit-id="${c.id}">
                            <td class="py-2">${c.criteria_name}</td>
                            ${[1, 2, 3, 4].map(v => `
                                <td class="text-center">
                                    <input type="radio" name="rubric_scores[${c.id}]" value="${v}" class="criteria-score">
                                </td>
                            `).join('')}
                        </tr>`;
                    });
                    tbody.innerHTML = html;
                    document.querySelectorAll('.criteria-score').forEach(inp => inp.addEventListener('change', recalcTotals));
                    recalcTotals();
                })
                .catch(() => tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Failed to load rubric.</td></tr>');
        });
    }

    function recalcTotals() {
        let totalScore = 0;
        let critCount = 0;
        document.querySelectorAll('#criteria_tbody tr[data-crit-id]').forEach(row => {
            critCount++;
            const checked = row.querySelector('.criteria-score:checked');
            totalScore += checked ? parseInt(checked.value, 10) : 0;
        });
        const totalMax = critCount * 4;
        document.getElementById('total_score_display').textContent = totalScore;
        document.getElementById('total_max').textContent = totalMax;
        document.getElementById('eval_total_score').value = totalScore;
        document.getElementById('eval_max_score').value = totalMax;
    }

    window.submitEvalModal = function() {
        const submitEvaluation = () => {
            const form = document.getElementById('evaluation_form');
            form.requestSubmit ? form.requestSubmit() : form.submit();
        };

        if (revisionSheetDirty) {
            saveRevisionSheet({ silent: true })
                .then(submitEvaluation)
                .catch(error => showToast(error.message || 'Save the revision notes before evaluating.', true));
        } else {
            submitEvaluation();
        }
    };

    // ── EDIT TEAM MEMBERS ───────────────────────
    let editIdx = 0;

    function editRow(name, userId, role) {
        const editContainer = document.getElementById('editSelectedStudentsContainer');
        if (editContainer.querySelector(`input[value="${userId}"]`)) {
            showToast('Student already in team.', true);
            return;
        }
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded border border-[#e2dacf] flex-nowrap whitespace-nowrap';
        row.innerHTML = `<input type="hidden" name="students[${editIdx}][user_id]" value="${userId}">
            <span class="flex-1 text-sm">${name} <span class="text-[#5b6375]">(${userId})</span></span>
            <select name="students[${editIdx}][role]" class="form-select w-32" required>
                <option value="programmer" ${role === 'programmer' ? 'selected' : ''}>Programmer</option>
                <option value="designer" ${role === 'designer' ? 'selected' : ''}>Designer</option>
                <option value="researcher" ${role === 'researcher' ? 'selected' : ''}>Researcher</option>
            </select>
            <button type="button" class="edit-remove-student text-[#5b6375] hover:text-red-500 transition"><i class="fas fa-times"></i></button>`;
        editContainer.appendChild(row);
        editIdx++;
        row.querySelector('.edit-remove-student').addEventListener('click', () => row.remove());
    }

    window.openEditGroupModal = function (groupId) {
        fetch(`/teacher/get-group/${groupId}`)
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    showToast(data.error, true);
                    return;
                }
                document.getElementById('editGroupErrors').classList.add('hidden');
                document.getElementById('editGroupForm').action = `/teacher/update-group/${groupId}`;
                document.getElementById('edit_group_name').value = data.group_name;
                document.getElementById('edit_capstone_title').value = data.capstone_title;

                editIdx = 0;
                const editContainer = document.getElementById('editSelectedStudentsContainer');
                editContainer.innerHTML = '';
                data.members.forEach(m => editRow(m.name, m.user_id, m.role));

                const editStudentSelect = document.getElementById('editStudentSelect');
                editStudentSelect.innerHTML = '<option disabled>Loading...</option>';
                fetch(`/teacher/get-students/${encodeURIComponent(data.section)}`)
                    .then(r => r.json())
                    .then(students => {
                        editStudentSelect.innerHTML = '';
                        if (!students.length) {
                            editStudentSelect.innerHTML = '<option disabled>No unassigned students in this section</option>';
                            return;
                        }
                        students.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.user_id;
                            opt.textContent = `${s.student_first_name} ${s.student_last_name} (${s.user_id})`;
                            editStudentSelect.appendChild(opt);
                        });
                    });

                openModal('editGroupModal');
            })
            .catch(() => showToast('Failed to load group.', true));
    };

    document.getElementById('editGroupForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = this;
        const errorsBox = document.getElementById('editGroupErrors');
        errorsBox.classList.add('hidden');
        errorsBox.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(async r => {
            if (r.redirected) {
                window.location.href = r.url;
                return;
            }
            const data = await r.json().catch(() => null);
            if (data?.errors) {
                const messages = Object.values(data.errors).flat();
                errorsBox.innerHTML = messages.join('<br>');
                errorsBox.classList.remove('hidden');
            } else {
                showToast('Failed to update team.', true);
            }
        })
        .catch(() => showToast('Failed to update team.', true));
    });

    document.getElementById('editAddStudentsBtn').addEventListener('click', () => {
        const sel = document.getElementById('editStudentSelect');
        Array.from(sel.selectedOptions).forEach(o => {
            if (o.value) editRow(o.textContent.split(' (')[0].trim(), o.value, 'programmer');
        });
        sel.selectedIndex = -1;
    });

    document.querySelectorAll('.edit-team-btn').forEach(btn =>
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const groupId = this.dataset.group;
            if (groupId) window.openEditGroupModal(groupId);
        })
    );

    // ── FILTER GROUPS IN ROOMS ──
    document.querySelectorAll('.room-group-filter').forEach(input => {
        input.addEventListener('input', function () {
            const roomId = this.dataset.roomId;
            const searchTerm = this.value.toLowerCase().trim();
            const list = document.querySelector(`.room-group-list[data-room-id="${roomId}"]`);
            if (!list) return;
            const items = list.querySelectorAll('.group-item');
            let visibleCount = 0;
            items.forEach(item => {
                const searchData = item.dataset.search || '';
                const match = !searchTerm || searchData.includes(searchTerm);
                item.style.display = match ? 'flex' : 'none';
                if (match) visibleCount++;
            });
            let noResult = list.querySelector('.no-result-msg');
            if (visibleCount === 0) {
                if (!noResult) {
                    noResult = document.createElement('p');
                    noResult.className = 'text-xs text-[#5b6375] col-span-full text-center py-2 no-result-msg';
                    noResult.textContent = 'No groups match your filter.';
                    list.appendChild(noResult);
                }
                noResult.style.display = 'block';
            } else if (noResult) {
                noResult.style.display = 'none';
            }
        });
    });

    // ── ASSIGNED SECTIONS: group filter for the Students table ──
    document.querySelectorAll('.as-group-filter').forEach(select => {
        select.addEventListener('change', function () {
            const sectionId = this.dataset.sectionId;
            const value = this.value;
            const table = document.getElementById(`as_section_students_view_${sectionId}`);
            if (!table) return;
            const rows = table.querySelectorAll('.as-student-row');
            let visibleCount = 0;
            rows.forEach(row => {
                const match = value === 'All' || row.dataset.group === value;
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            let noResult = table.querySelector('.as-no-result');
            if (visibleCount === 0) {
                if (!noResult) {
                    const tbody = table.querySelector('tbody');
                    noResult = document.createElement('tr');
                    noResult.className = 'as-no-result';
                    noResult.innerHTML = `<td colspan="5" class="p-6 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-xl mb-1 block"></i> No students in this group.</td>`;
                    tbody.appendChild(noResult);
                }
                noResult.style.display = '';
            } else if (noResult) {
                noResult.style.display = 'none';
            }
        });
    });

    // ── ALL ASSIGNED GROUPS: search + section filter + pagination ──
    (function () {
        const searchInput = document.getElementById('ag_search_input');
        const sectionFilter = document.getElementById('ag_section_filter');
        const listEl = document.getElementById('ag_group_list');
        const noResultsEl = document.getElementById('ag_no_results');
        const paginationEl = document.getElementById('ag_pagination');
        const countEl = document.getElementById('ag_group_count');
        if (!listEl) return;

        const PAGE_SIZE = 5;
        let currentPage = 1;

        function getFilteredRows() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            const section = sectionFilter?.value || 'All';
            return Array.from(listEl.querySelectorAll('.ag-row')).filter(row => {
                const matchesSearch = !query || (row.dataset.search || '').includes(query);
                const matchesSection = section === 'All' || row.dataset.agSection === section;
                return matchesSearch && matchesSection;
            });
        }

        function render() {
            const allRows = Array.from(listEl.querySelectorAll('.ag-row'));
            const filtered = getFilteredRows();

            allRows.forEach(r => r.style.display = 'none');

            const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * PAGE_SIZE;
            const pageRows = filtered.slice(start, start + PAGE_SIZE);
            pageRows.forEach(r => r.style.display = '');

            noResultsEl.classList.toggle('hidden', filtered.length !== 0);
            if (countEl) countEl.textContent = `${filtered.length} group${filtered.length === 1 ? '' : 's'}`;

            renderPagination(totalPages, filtered.length);
        }

        function renderPagination(totalPages, totalItems) {
            paginationEl.innerHTML = '';
            if (totalItems === 0 || totalPages <= 1) return;

            const makeBtn = (label, page, opts = {}) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = label;
                btn.className = opts.active
                    ? 'px-3 py-1.5 rounded-lg text-xs font-bold text-white'
                    : 'px-3 py-1.5 rounded-lg text-xs font-medium text-[#5b6375] hover:bg-[#faf8f4] border border-[#e2dacf]';
                if (opts.active) btn.style.background = 'var(--navy)';
                if (opts.disabled) {
                    btn.disabled = true;
                    btn.classList.add('opacity-40', 'cursor-not-allowed');
                }
                btn.addEventListener('click', () => {
                    currentPage = page;
                    render();
                });
                return btn;
            };

            paginationEl.appendChild(makeBtn('‹ Prev', currentPage - 1, { disabled: currentPage === 1 }));

            for (let p = 1; p <= totalPages; p++) {
                paginationEl.appendChild(makeBtn(String(p), p, { active: p === currentPage }));
            }

            paginationEl.appendChild(makeBtn('Next ›', currentPage + 1, { disabled: currentPage === totalPages }));
        }

        if (searchInput) searchInput.addEventListener('input', () => { currentPage = 1; render(); });
        if (sectionFilter) sectionFilter.addEventListener('change', () => { currentPage = 1; render(); });

        render();
    })();

    // ── PASSWORD TOGGLE ────────────────────────────
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // ── PASSWORD STRENGTH METER ─────────────────────
    const newPasswordInput = document.getElementById('new_password');
    const strengthBar = document.getElementById('password_strength_bar');
    if (newPasswordInput && strengthBar) {
        newPasswordInput.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { width: '0%', color: '#e8e3d7' },
                { width: '20%', color: '#d9534f' },
                { width: '40%', color: '#e8935a' },
                { width: '60%', color: '#e8c25a' },
                { width: '80%', color: '#9bc27a' },
                { width: '100%', color: '#1e6b3a' }
            ];
            const level = levels[Math.min(score, 5)];
            strengthBar.style.width = val.length ? level.width : '0%';
            strengthBar.style.background = level.color;
        });
    }

    // ── Dynamic row adders for revision modal ──
    window.addRevisionChapterRow = function() {
        const container = document.getElementById('revision_chapter_rows');
        const row = document.createElement('div');
        row.className = 'flex flex-col md:flex-row gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
        row.innerHTML = `
            <input type="text" name="chapters[][chapter]" class="form-input flex-1 text-sm" placeholder="Chapter (e.g., Chapter 1, Chapter 2)" required>
            <input type="text" name="chapters[][findings]" class="form-input flex-1 text-sm" placeholder="Document Findings" required>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    };

    window.addRevisionIotRow = function() {
        const container = document.getElementById('revision_iot_rows');
        const row = document.createElement('div');
        row.className = 'flex flex-col md:flex-row gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
        row.innerHTML = `
            <input type="text" name="iot[][finding]" class="form-input flex-1 text-sm" placeholder="Findings / Enhancements / Recommendations" required>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    };

    window.addRevisionObjective = function() {
        const container = document.getElementById('revision_objectives_list');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded-lg border border-[#e2dacf]';
        row.innerHTML = `
            <input type="text" name="objectives[]" class="form-input flex-1 text-sm" placeholder="Enter objective" required>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#5b6375] hover:text-red-500 px-2"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    };

    // ── Revision Check Modal ──
    window.openRevisionCheckModal = function(groupId, groupName, capstoneTitle) {
        document.getElementById('check_group_id').value = groupId;
        document.getElementById('check_capstone_title').value = capstoneTitle || '';

        document.getElementById('check_chapters_tbody').innerHTML = '';
        document.getElementById('check_iot_tbody').innerHTML = '';
        document.getElementById('check_objectives_list').innerHTML = '';
        document.getElementById('check_overall_remarks').textContent = '';

        const proponentsEl = document.getElementById('check_proponents_list');
        proponentsEl.innerHTML = '<span class="text-xs text-[#5b6375] italic">Loading...</span>';

        openModal('revisionCheckModal');
        showPageLoader('Loading revision details…');

        Promise.all([
            fetch(`/teacher/get-group/${groupId}`).then(r => r.json()),
            fetch(`/teacher/get-revision-details/${groupId}`).then(async r => {
                const data = await r.json();
                if (!r.ok) throw new Error(data.error || 'Failed to load revision.');
                return data;
            })
        ])
        .then(([groupData, revData]) => {
            // Proponents
            if (groupData.members && groupData.members.length) {
                proponentsEl.innerHTML = groupData.members.map(m => `
                    <span class="badge badge-navy"><i class="fa-regular fa-user mr-1"></i> ${m.name}</span>
                `).join('');
            } else {
                proponentsEl.innerHTML = '<span class="text-xs text-[#5b6375]">No members</span>';
            }

            document.getElementById('check_overall_remarks').textContent = revData.overall_remarks || 'No overall remarks.';

            // Chapters
            const chaptersTbody = document.getElementById('check_chapters_tbody');
            if (revData.chapters && revData.chapters.length) {
                revData.chapters.forEach((ch, idx) => {
                    const remarks = ch.remarks || 'Pending';
                    const isCompleted = remarks.toLowerCase() === 'completed';
                    const tr = document.createElement('tr');
                    tr.dataset.chapter = ch.chapter;
                    tr.dataset.findings = ch.findings;
                    tr.innerHTML = `
                        <td class="p-2 pl-3 font-semibold">${ch.chapter}</td>
                        <td class="p-2">${ch.findings}</td>
                        <td class="p-2 text-center">
                            <input type="checkbox" data-role="chapter-completed" class="form-checkbox text-green-600" value="1" ${isCompleted ? 'checked' : ''}>
                        </td>
                        <td class="p-2 pr-3">
                            <input type="text" data-role="chapter-remarks" class="form-input text-xs py-1 ${isCompleted ? 'bg-green-50 text-green-700' : 'bg-[#f0ece4]'} cursor-not-allowed" value="${isCompleted ? 'Completed' : 'Pending'}" readonly>
                        </td>
                    `;
                    chaptersTbody.appendChild(tr);
                });
            } else {
                chaptersTbody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-[#5b6375]">No chapter findings.</td></tr>`;
            }

            // IoT
            const iotTbody = document.getElementById('check_iot_tbody');
            if (revData.iot_findings && revData.iot_findings.length) {
                revData.iot_findings.forEach((iot, idx) => {
                    const remarks = iot.remarks || 'Pending';
                    const isCompleted = remarks.toLowerCase() === 'completed';
                    const tr = document.createElement('tr');
                    tr.dataset.finding = iot.finding;
                    tr.innerHTML = `
                        <td class="p-2 pl-3">${iot.finding}</td>
                        <td class="p-2 text-center">
                            <input type="checkbox" data-role="iot-completed" class="form-checkbox text-green-600" value="1" ${isCompleted ? 'checked' : ''}>
                        </td>
                        <td class="p-2 pr-3">
                            <input type="text" data-role="iot-remarks" class="form-input text-xs py-1 ${isCompleted ? 'bg-green-50 text-green-700' : 'bg-[#f0ece4]'} cursor-not-allowed" value="${isCompleted ? 'Completed' : 'Pending'}" readonly>
                        </td>
                    `;
                    iotTbody.appendChild(tr);
                });
            } else {
                iotTbody.innerHTML = `<tr><td colspan="3" class="p-4 text-center text-[#5b6375]">No IoT findings.</td></tr>`;
            }

            // Objectives
            const objectivesList = document.getElementById('check_objectives_list');
            objectivesList.innerHTML = '';
            const table = document.createElement('table');
            table.className = 'w-full text-left border-collapse';
            const thead = document.createElement('thead');
            thead.innerHTML = `
                <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                    <th class="p-2 pl-3" style="width:65%">Objective</th>
                    <th class="p-2 text-center" style="width:15%">Completed?</th>
                    <th class="p-2 pr-3 text-center" style="width:20%">Remarks</th>
                </tr>
            `;
            table.appendChild(thead);
            const tbodyObj = document.createElement('tbody');
            tbodyObj.className = 'divide-y divide-[#faf1e0] text-xs';
            if (revData.additional_objectives && revData.additional_objectives.length > 0) {
                revData.additional_objectives.forEach((obj) => {
                    const objectiveText = typeof obj === 'object' ? obj.objective : obj;
                    const remarks = typeof obj === 'object' ? (obj.remarks || 'Pending') : 'Pending';
                    const isCompleted = String(remarks).toLowerCase() === 'completed';
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-[#faf8f4]/50';
                    tr.dataset.objective = objectiveText;
                    tr.innerHTML = `
                        <td class="p-2 pl-3 font-medium text-[#171e2c]">${objectiveText}</td>
                        <td class="p-2 text-center">
                            <input type="checkbox" data-role="objective-completed" class="form-checkbox text-green-600" value="1" ${isCompleted ? 'checked' : ''}>
                        </td>
                        <td class="p-2 pr-3">
                            <input type="text" data-role="objective-remarks" class="form-input text-xs py-1 text-center ${isCompleted ? 'bg-green-50 text-green-700' : 'bg-[#f0ece4]'} cursor-not-allowed w-full" value="${isCompleted ? 'Completed' : 'Pending'}" readonly>
                        </td>
                    `;
                    tbodyObj.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="3" class="p-4 text-center text-[#5b6375]">No objectives.</td>`;
                tbodyObj.appendChild(tr);
            }
            table.appendChild(tbodyObj);
            objectivesList.appendChild(table);

            // Toggle completed checkboxes
            document.querySelectorAll(
                '#check_chapters_tbody [data-role="chapter-completed"], ' +
                '#check_iot_tbody [data-role="iot-completed"], ' +
                '#check_objectives_list [data-role="objective-completed"]'
            )
            .forEach(cb => {
                cb.addEventListener('change', function () {
                    const row = this.closest('tr, div');
                    const remarksField = row?.querySelector('[data-role$="-remarks"]');
                    if (!remarksField) return;
                    if (this.checked) {
                        remarksField.value = 'Completed';
                        remarksField.classList.remove('bg-[#f0ece4]');
                        remarksField.classList.add('bg-green-50', 'text-green-700');
                    } else {
                        remarksField.value = 'Pending';
                        remarksField.classList.remove('bg-green-50', 'text-green-700');
                        remarksField.classList.add('bg-[#f0ece4]');
                    }
                });
            });
            hidePageLoader();
        })
        .catch(error => {
            console.error(error);
            proponentsEl.innerHTML = `<span class="text-red-500">${error.message || 'Failed to load revision data.'}</span>`;
            hidePageLoader();
        });
    };

    // ── My Evaluation Modal ──
    window.openMyEvaluationModal = function (groupId) {
        openModal('myEvaluationModal');
        const content = document.getElementById('myEvaluationContent');
        content.innerHTML = window.skeletonBlock(3);

        fetch(`/teacher/get-my-evaluation/${groupId}`)
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`Server returned ${response.status} (not JSON). First 100 chars: ${text.slice(0,100)}`);
                }
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Failed to load evaluation.');
                return data;
            })
            .then(data => {
                const criteriaRows = (data.criteria || []).map(c => `
                    <tr class="border-b border-[#e2dacf]">
                        <td class="py-2">${c.criteria_name}</td>
                        <td class="text-center">${c.weight}%</td>
                        <td class="text-center">${c.max_score}</td>
                        <td class="text-center font-bold text-[#1e6b3a]">${c.given_score}</td>
                    </tr>`).join('');

                content.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-[#faf8f4] border border-[#e2dacf] rounded-lg">
                            <div>
                                <p class="font-semibold text-sm text-[#0a1428]">${data.group_name}</p>
                                <p class="text-xs text-[#5b6375]">${data.milestone_title}</p>
                            </div>
                            <span class="text-lg font-bold text-[#1e6b3a]">${data.score} / ${data.max_score}</span>
                        </div>
                        ${criteriaRows ? `
                            <table class="w-full text-sm">
                                <thead><tr class="text-[#5b6375] border-b border-[#e2dacf]"><th class="text-left py-2">Criteria</th><th class="text-center py-2">Weight</th><th class="text-center py-2">Max</th><th class="text-center py-2">Score</th></tr></thead>
                                <tbody>${criteriaRows}</tbody>
                            </table>` : ''}
                        ${data.feedback ? `<div class="p-3 bg-[#faf8f4] border border-[#e2dacf] rounded-lg text-sm italic text-[#5b6375]">"${data.feedback}"</div>` : ''}
                    </div>
                `;
            })
            .catch(err => {
                content.innerHTML = `<p class="text-sm text-red-500">❌ ${err.message}</p>`;
                console.error('MyEvaluation error:', err);
            });
    };

    // ── Revision Sheet HTML / editor ──
    let revisionSheetDirty = false;
    let revisionEditorReadOnly = false;

    function escHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function revisionHasContent(data) {
        return Boolean(
            String(data?.overall_remarks || '').trim() ||
            (Array.isArray(data?.chapters) && data.chapters.length) ||
            (Array.isArray(data?.iot_findings) && data.iot_findings.length) ||
            (Array.isArray(data?.additional_objectives) && data.additional_objectives.length)
        );
    }

    function editableTextInput(value, className, placeholder) {
        return `<input type="text" class="form-input text-sm ${className}" value="${escHtml(value)}" placeholder="${escHtml(placeholder)}" oninput="markRevisionSheetDirty()">`;
    }

    function buildRevisionSheetHtml(data = {}, readOnly = false) {
        const chapters = Array.isArray(data?.chapters) ? data.chapters : [];
        const iotFindings = Array.isArray(data?.iot_findings) ? data.iot_findings : [];
        const objectives = Array.isArray(data?.additional_objectives) ? data.additional_objectives : [];
        const hasExistingRevision = revisionHasContent(data);
        const editable = !readOnly && !hasExistingRevision;

        const chapterRows = chapters.length
            ? chapters.map(ch => editable
                ? `<tr class="border-b border-[#e2dacf] eval-sheet-row">
                        <td class="p-2 pl-3">${editableTextInput(ch.chapter, 'eval-sheet-chapter', 'Chapter')}</td>
                        <td class="p-2">${editableTextInput(ch.findings, 'eval-sheet-findings', 'Document findings / required revision')}</td>
                        <td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove chapter finding"><i class="fas fa-times"></i></button></td>
                   </tr>`
                : `<tr class="border-b border-[#e2dacf]">
                        <td class="p-2 pl-3 font-semibold">${escHtml(ch.chapter)}</td>
                        <td class="p-2">${escHtml(ch.findings)}</td>
                        <td class="p-2 pr-3 text-center"><span class="badge ${String(ch.remarks || '').toLowerCase() === 'completed' ? 'badge-green' : 'badge-muted'}">${escHtml(ch.remarks || 'Pending')}</span></td>
                   </tr>`
            ).join('')
            : editable
                ? `<tr class="border-b border-[#e2dacf] eval-sheet-row">
                        <td class="p-2 pl-3">${editableTextInput('', 'eval-sheet-chapter', 'Chapter')}</td>
                        <td class="p-2">${editableTextInput('', 'eval-sheet-findings', 'Document findings / required revision')}</td>
                        <td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove chapter finding"><i class="fas fa-times"></i></button></td>
                   </tr>`
                : `<tr><td colspan="3" class="p-4 text-center text-[#5b6375]">No chapter findings.</td></tr>`;

        const iotRows = iotFindings.length
            ? iotFindings.map(iot => editable
                ? `<tr class="border-b border-[#e2dacf] eval-sheet-row">
                        <td class="p-2 pl-3">${editableTextInput(iot.finding, 'eval-sheet-iot', 'Finding / enhancement / recommendation')}</td>
                        <td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove IoT finding"><i class="fas fa-times"></i></button></td>
                   </tr>`
                : `<tr class="border-b border-[#e2dacf]">
                        <td class="p-2 pl-3">${escHtml(iot.finding)}</td>
                        <td class="p-2 pr-3 text-center"><span class="badge ${String(iot.remarks || '').toLowerCase() === 'completed' ? 'badge-green' : 'badge-muted'}">${escHtml(iot.remarks || 'Pending')}</span></td>
                   </tr>`
            ).join('')
            : editable
                ? `<tr class="border-b border-[#e2dacf] eval-sheet-row">
                        <td class="p-2 pl-3">${editableTextInput('', 'eval-sheet-iot', 'Finding / enhancement / recommendation')}</td>
                        <td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove IoT finding"><i class="fas fa-times"></i></button></td>
                   </tr>`
                : `<tr><td colspan="2" class="p-4 text-center text-[#5b6375]">No IoT findings.</td></tr>`;

        const objectiveRows = objectives.length
            ? objectives.map(obj => {
                const objective = typeof obj === 'object' ? obj.objective : obj;
                const remarks = typeof obj === 'object' ? (obj.remarks || 'Pending') : 'Pending';
                return editable
                    ? `<tr class="border-b border-[#e2dacf] eval-sheet-row">
                            <td class="p-2 pl-3">${editableTextInput(objective, 'eval-sheet-objective', 'Additional objective')}</td>
                            <td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove objective"><i class="fas fa-times"></i></button></td>
                       </tr>`
                    : `<tr class="border-b border-[#e2dacf]">
                            <td class="p-2 pl-3">${escHtml(objective)}</td>
                            <td class="p-2 pr-3 text-center"><span class="badge ${String(remarks).toLowerCase() === 'completed' ? 'badge-green' : 'badge-muted'}">${escHtml(remarks)}</span></td>
                       </tr>`;
            }).join('')
            : editable
                ? `<tr class="border-b border-[#e2dacf] eval-sheet-row">
                        <td class="p-2 pl-3">${editableTextInput('', 'eval-sheet-objective', 'Additional objective')}</td>
                        <td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove objective"><i class="fas fa-times"></i></button></td>
                   </tr>`
                : `<tr><td colspan="2" class="p-4 text-center text-[#5b6375]">No additional objectives.</td></tr>`;

        const readOnlyAttr = editable ? '' : 'readonly';
        return `
            <div id="eval_revision_editor" class="space-y-4" data-editable="${editable ? 'true' : 'false'}">
                <div class="mb-4">
                    <label class="form-label">Overall Remarks / Instructions</label>
                    <textarea id="eval_sheet_overall_remarks" class="form-input text-sm min-h-24" placeholder="No remarks provided." oninput="markRevisionSheetDirty()" ${readOnlyAttr}>${escHtml(data?.overall_remarks || '')}</textarea>
                </div>

                <div class="border-t border-[#e2dacf] pt-4 mt-4">
                    <p class="form-fieldset-title"><i class="fa-solid fa-book"></i> Chapter / Document Findings</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead><tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]"><th class="p-2 pl-3" style="width:20%">Chapter</th><th class="p-2" style="width:45%">Findings</th><th class="p-2 pr-3 text-center" style="width:35%">${editable ? 'Actions' : 'Remarks'}</th></tr></thead>
                            <tbody id="eval_sheet_chapter_rows" class="divide-y divide-[#faf1e0]">${chapterRows}</tbody>
                        </table>
                    </div>
                    ${editable ? '<button type="button" onclick="addRevisionSheetChapterRow()" class="btn-outline text-xs mt-2"><i class="fas fa-plus mr-1"></i> Add Chapter Finding</button>' : ''}
                </div>

                <div class="border-t border-[#e2dacf] pt-4 mt-4">
                    <p class="form-fieldset-title"><i class="fa-solid fa-microchip"></i> System / IoT Findings / Enhancements</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead><tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]"><th class="p-2 pl-3" style="width:65%">Finding / Enhancement</th><th class="p-2 pr-3 text-center" style="width:35%">${editable ? 'Actions' : 'Remarks'}</th></tr></thead>
                            <tbody id="eval_sheet_iot_rows" class="divide-y divide-[#faf1e0]">${iotRows}</tbody>
                        </table>
                    </div>
                    ${editable ? '<button type="button" onclick="addRevisionSheetIotRow()" class="btn-outline text-xs mt-2"><i class="fas fa-plus mr-1"></i> Add IoT Finding</button>' : ''}
                </div>

                <div class="border-t border-[#e2dacf] pt-4 mt-4">
                    <p class="form-fieldset-title"><i class="fa-solid fa-list-check"></i> Additional Objectives (if any)</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead><tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]"><th class="p-2 pl-3" style="width:65%">Objective</th><th class="p-2 pr-3 text-center" style="width:35%">${editable ? 'Actions' : 'Remarks'}</th></tr></thead>
                            <tbody id="eval_sheet_objective_rows" class="divide-y divide-[#faf1e0]">${objectiveRows}</tbody>
                        </table>
                    </div>
                    ${editable ? '<button type="button" onclick="addRevisionSheetObjectiveRow()" class="btn-outline text-xs mt-2"><i class="fas fa-plus mr-1"></i> Add Objective</button>' : ''}
                </div>

                ${editable ? `
                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-[#e2dacf]">
                        <span id="eval_revision_save_status" class="text-xs text-[#5b6375]"></span>
                        <button type="button" id="eval_revision_save_btn" onclick="saveRevisionSheet()" class="btn-primary text-xs"><i class="fa-solid fa-floppy-disk mr-1"></i> Save Revision Notes</button>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function markRevisionSheetDirty() {
        if (revisionEditorReadOnly) return;
        revisionSheetDirty = true;
        const status = document.getElementById('eval_revision_save_status');
        if (status) status.textContent = 'Unsaved changes';
    }
    window.markRevisionSheetDirty = markRevisionSheetDirty;

    window.addRevisionSheetChapterRow = function() {
        const tbody = document.getElementById('eval_sheet_chapter_rows');
        if (!tbody) return;
        const row = document.createElement('tr');
        row.className = 'border-b border-[#e2dacf] eval-sheet-row';
        row.innerHTML = `<td class="p-2 pl-3">${editableTextInput('', 'eval-sheet-chapter', 'Chapter')}</td><td class="p-2">${editableTextInput('', 'eval-sheet-findings', 'Document findings / required revision')}</td><td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove chapter finding"><i class="fas fa-times"></i></button></td>`;
        tbody.appendChild(row);
        markRevisionSheetDirty();
    };

    window.addRevisionSheetIotRow = function() {
        const tbody = document.getElementById('eval_sheet_iot_rows');
        if (!tbody) return;
        const row = document.createElement('tr');
        row.className = 'border-b border-[#e2dacf] eval-sheet-row';
        row.innerHTML = `<td class="p-2 pl-3">${editableTextInput('', 'eval-sheet-iot', 'Finding / enhancement / recommendation')}</td><td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove IoT finding"><i class="fas fa-times"></i></button></td>`;
        tbody.appendChild(row);
        markRevisionSheetDirty();
    };

    window.addRevisionSheetObjectiveRow = function() {
        const tbody = document.getElementById('eval_sheet_objective_rows');
        if (!tbody) return;
        const row = document.createElement('tr');
        row.className = 'border-b border-[#e2dacf] eval-sheet-row';
        row.innerHTML = `<td class="p-2 pl-3">${editableTextInput('', 'eval-sheet-objective', 'Additional objective')}</td><td class="p-2 pr-3 text-center"><button type="button" onclick="this.closest('tr').remove(); markRevisionSheetDirty()" class="text-[#5b6375] hover:text-red-500 px-2" aria-label="Remove objective"><i class="fas fa-times"></i></button></td>`;
        tbody.appendChild(row);
        markRevisionSheetDirty();
    };

    window.saveRevisionSheet = function(options = {}) {
        const groupId = document.getElementById('eval_group_id')?.value;
        const saveBtn = document.getElementById('eval_revision_save_btn');
        const status = document.getElementById('eval_revision_save_status');
        if (!groupId) return Promise.reject(new Error('Group not selected.'));

        const description = document.getElementById('eval_sheet_overall_remarks')?.value.trim() || '';
        const chapters = Array.from(document.querySelectorAll('#eval_sheet_chapter_rows .eval-sheet-row')).map(row => ({
            chapter: row.querySelector('.eval-sheet-chapter')?.value.trim() || '',
            findings: row.querySelector('.eval-sheet-findings')?.value.trim() || ''
        })).filter(item => item.chapter || item.findings);
        const iotFindings = Array.from(document.querySelectorAll('#eval_sheet_iot_rows .eval-sheet-row')).map(row => ({
            finding: row.querySelector('.eval-sheet-iot')?.value.trim() || ''
        })).filter(item => item.finding);
        const additionalObjectives = Array.from(document.querySelectorAll('#eval_sheet_objective_rows .eval-sheet-row'))
            .map(row => row.querySelector('.eval-sheet-objective')?.value.trim() || '')
            .filter(Boolean);

        if (!description && !chapters.length && !iotFindings.length && !additionalObjectives.length) {
            if (!options.silent) showToast('Add at least one revision note before saving.', true);
            return Promise.resolve({ skipped: true });
        }

        const originalHtml = saveBtn?.innerHTML;
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.classList.add('is-loading');
            saveBtn.innerHTML = '<i class="fas fa-circle-notch"></i> Saving…';
        }
        if (status) status.textContent = 'Saving…';

        return fetch(`/teacher/group/${groupId}/request-revision`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                revision_description: description,
                chapters,
                iot_findings: iotFindings,
                additional_objectives: additionalObjectives
            })
        })
            .then(async response => {
                const result = await response.json().catch(() => ({}));
                if (!response.ok || !result.success) throw new Error(result.error || 'Failed to save revision notes.');
                return result;
            })
            .then(result => {
                revisionSheetDirty = false;
                if (status) status.textContent = 'Saved';
                if (!options.silent) showToast(result.message || 'Revision notes saved successfully.');
                return result;
            })
            .catch(error => {
                if (status) status.textContent = 'Save failed';
                if (!options.silent) showToast(error.message || 'Failed to save revision notes.', true);
                throw error;
            })
            .finally(() => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.classList.remove('is-loading');
                    saveBtn.innerHTML = originalHtml || '<i class="fa-solid fa-floppy-disk mr-1"></i> Save Revision Notes';
                }
            });
    };

    function setRevisionAccessLabel(editable) {
        const label = document.getElementById('eval_revision_access_label');
        if (!label) return;
        label.innerHTML = editable
            ? '<i class="fa-solid fa-pen"></i> Editable'
            : '<i class="fa-solid fa-lock"></i> View Only';
    }

    function renderRevisionSheet(container, groupId, forceReadOnly = false) {
        container.innerHTML = window.skeletonBlock(4);
        fetch(`/teacher/get-revision-details/${groupId}`)
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (response.status === 404) return {};
                if (!response.ok) throw new Error(data.error || `Server returned ${response.status}`);
                return data;
            })
            .then(data => {
                if (data.error) {
                    container.innerHTML = `<p class="text-sm text-red-500">${escHtml(data.error)}</p>`;
                    setRevisionAccessLabel(false);
                    return;
                }
                const editable = !forceReadOnly && !revisionHasContent(data);
                revisionEditorReadOnly = !editable;
                revisionSheetDirty = false;
                setRevisionAccessLabel(editable);
                container.innerHTML = buildRevisionSheetHtml(data, !editable);
                container.querySelectorAll('input, textarea').forEach(field => {
                    field.addEventListener('input', markRevisionSheetDirty);
                    field.addEventListener('change', markRevisionSheetDirty);
                });
            })
            .catch(err => {
                container.innerHTML = `<p class="text-sm text-red-500">❌ ${escHtml(err.message)}</p>`;
                setRevisionAccessLabel(false);
                console.error('RevisionSheet error:', err);
            });
    }

    window.openViewRevisionModal = function (groupId) {
        const modal = document.getElementById('viewRevisionModal');
        const content = document.getElementById('viewRevisionContent');
        if (!modal || !content) return;
        modal.classList.add('active');
        renderRevisionSheet(content, groupId, true);
    };
    // ── TEACHER: open Revision Sheet (uses existing viewRevisionModal) ──
window.openTeacherRevisionSheet = function (groupId) {
    if (!groupId) return;
    // Reuse the existing read-only revision modal
    window.openViewRevisionModal(groupId);
};

// ── TEACHER: open Recommendation Sheet ──
window.openTeacherRecommendationSheet = function (groupId) {
    if (!groupId) return;

    const loading = document.getElementById('tchRecommendationLoading');
    const title   = document.getElementById('tchRecommendationTitle');
    const props   = document.getElementById('tchRecommendationProponents');
    const adviser = document.getElementById('tchRecommendationAdviser');
    const dateEl  = document.getElementById('tchRecommendationDate');
    const grpEl   = document.getElementById('tchRecommendationGroup');
    const serial  = document.getElementById('tchRecommendationSerial');

    if (loading) loading.style.display = 'flex';
    title.textContent   = '—';
    props.textContent   = 'Loading...';
    adviser.textContent = '—';
    dateEl.textContent  = '—';
    grpEl.textContent   = '—';
    serial.textContent  = ' ';

    openModal('teacherRecommendationSheetModal');

    fetch(`/teacher/get-recommendation-sheet/${groupId}`, { __silent: true })
        .then(r => r.json())
        .then(data => {
            title.textContent = data.capstone_title || '—';

            const members = data.members || [];
            if (members.length === 0) {
                props.textContent = '—';
            } else if (members.length === 1) {
                props.textContent = members[0];
            } else if (members.length === 2) {
                props.textContent = `${members[0]} and ${members[1]}`;
            } else {
                props.textContent =
                    `${members.slice(0, -1).join(', ')}, and ${members[members.length - 1]}`;
            }

            adviser.textContent = data.adviser || '—';
            grpEl.textContent   = data.group_name || '—';

            if (data.date_issued) {
                const d = new Date(data.date_issued);
                dateEl.textContent = d.toLocaleDateString('en-US',
                    { month: 'long', day: 'numeric', year: 'numeric' });
            }

            serial.textContent = data.serial_number ? 'Serial No. ' + data.serial_number : ' ';
        })
        .catch(() => {
            props.textContent = 'Unable to load data.';
            adviser.textContent = '—';
        })
        .finally(() => { if (loading) loading.style.display = 'none'; });
};

// ── TEACHER: open Approval Sheet ──
window.openTeacherApprovalSheet = function (groupId) {
    if (!groupId) return;

    const loading      = document.getElementById('tchApprovalLoading');
    const title        = document.getElementById('tchApprovalTitle');
    const proponents   = document.getElementById('tchApprovalProponents');
    const adviser      = document.getElementById('tchApprovalAdviser');
    const panelists    = document.getElementById('tchApprovalPanelists');
    const chairmanBlk  = document.getElementById('tchApprovalChairmanBlock');
    const chairman     = document.getElementById('tchApprovalChairman');
    const oralResult   = document.getElementById('tchApprovalOralResult');
    const oralDate     = document.getElementById('tchApprovalOralDate');
    const president    = document.getElementById('tchApprovalPresident');
    const serial       = document.getElementById('tchApprovalSerial');

    const esc = v => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;')
                                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const personName = p => typeof p === 'string' ? p : (p?.name || 'Panelist');
    const joinNames = names => {
        const list = (names || []).map(personName).filter(Boolean);
        if (!list.length) return '—';
        if (list.length === 1) return list[0];
        if (list.length === 2) return `${list[0]} and ${list[1]}`;
        return `${list.slice(0, -1).join(', ')}, and ${list[list.length - 1]}`;
    };

    title.textContent      = '—';
    proponents.textContent = 'Loading...';
    adviser.textContent    = '—';
    panelists.innerHTML    = '<div class="approval-signature"><span class="approval-sig-line">Loading...</span><div class="approval-sig-role">Member</div></div>';
    if (chairmanBlk) chairmanBlk.style.display = 'none';
    if (chairman) chairman.textContent = '—';
    oralResult.textContent = '—';
    oralDate.textContent   = '—';
    president.textContent  = 'DR. FLORIPIS A. MONTECILLO, Ed.D.';
    serial.textContent     = ' ';

    if (loading) loading.style.display = 'flex';
    openModal('teacherApprovalSheetModal');

    fetch(`/teacher/get-approval-sheet/${groupId}`, { __silent: true })
        .then(r => r.json())
        .then(data => {
            title.textContent      = data.capstone_title || '—';
            proponents.textContent = joinNames(data.members);
            adviser.textContent    = data.adviser || '—';

            const all = Array.isArray(data.panelists) ? data.panelists : [];
            const chairmanEntry = all.find(p => /chair/i.test(String(p?.role || p?.type || '')))
                || (data.chairman ? { name: data.chairman } : null);
            const membersOnly = chairmanEntry ? all.filter(p => p !== chairmanEntry) : all;

            panelists.innerHTML = membersOnly.length
                ? membersOnly.map(p => `<div class="approval-signature"><span class="approval-sig-line">${esc(personName(p))}</span><div class="approval-sig-role">Member</div></div>`).join('')
                : '<div class="approval-signature"><span class="approval-sig-line">No panelists assigned</span><div class="approval-sig-role">Member</div></div>';

            if (chairmanEntry && chairmanBlk && chairman) {
                chairmanBlk.style.display = 'block';
                chairman.textContent = personName(chairmanEntry);
            }
            oralResult.textContent = data.oral_exam_result || '—';
            oralDate.textContent   = data.oral_exam_date || '—';
            president.textContent  = data.school_president || 'DR. FLORIPIS A. MONTECILLO, Ed.D.';
            serial.textContent     = data.serial_number ? 'Serial No. ' + data.serial_number : ' ';
        })
        .catch(err => {
            console.error('Teacher approval sheet error:', err);
            proponents.textContent = 'Unable to load approval data.';
        })
        .finally(() => { if (loading) loading.style.display = 'none'; });
};

    window.openViewEvaluationModal = function (groupId) {
        window.openEvaluationModal(groupId);
    };

    window.openReadOnlyEvaluationModal = function (groupId) {
        window.openEvaluationModal(groupId);
    };

    // ── Universal submit spinner ──
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.id === 'logout-form') return;
        if (e.defaultPrevented) return; // forms that own their submit handler

        // Skip forms we already handle manually
        if (['revision_check_form', 'revision_form'].includes(form.id)) return;

        showPageLoader('Saving changes…');
    }, false);

});
</script>
</body>
    </html> 