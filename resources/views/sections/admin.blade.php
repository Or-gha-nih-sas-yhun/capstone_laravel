<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <script>
    (function () {
        var allowed = ['dashboard','teachers','students','rubrics','progress','evaluation','profile','capstone','documents'];
        var s = null, v = null;
        try { var h = location.hash.replace(/^#/, ''); if (allowed.indexOf(h) > -1) s = h; } catch (e) {}
        if (!s) { try { s = sessionStorage.getItem('activeSection'); } catch (e) {} }
        if (allowed.indexOf(s) === -1) s = 'dashboard';
        try { v = sessionStorage.getItem('activeSgView'); } catch (e) {}
        document.documentElement.setAttribute('data-active-section', s);
        document.documentElement.setAttribute('data-sg-view', v === 'groups' ? 'groups' : 'students');
    })();
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $flashSuccess = session('success') ?? session('status') ?? session('message');
    $flashError   = session('error');
@endphp
@if(is_string($flashSuccess) && $flashSuccess)<meta name="flash-success" content="{{ $flashSuccess }}">@endif
@if(is_string($flashError) && $flashError)<meta name="flash-error" content="{{ $flashError }}">@endif
@if($errors->any())<meta name="flash-errors" content="{{ json_encode($errors->all()) }}">@endif

    <title>Capstone Tracker | Admin Dashboard</title>
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
            --shadow-md: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.02);
            --shadow-lg: 0 30px 45px -15px rgba(0, 0, 0, 0.10);
            --transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.0);
        }

        * { font-family: 'DM Sans', sans-serif; }
        body {
            background: linear-gradient(145deg, var(--cream) 0%, #f2ede2 100%);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            font-size: 0.95rem;
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--cream-dark); border-radius: 8px; }
        ::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold-dark); }
        ::selection { background: var(--gold); color: var(--navy); }

        .transition-smooth { transition: var(--transition); }
        .section-card { animation: none; }
        .section-card.section-enter { animation: fadeInUp 0.35s cubic-bezier(0.22, 1, 0.36, 1) both; }
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
        .nav-link { border-radius: 0.75rem; transition: var(--transition); }
        .nav-link.active-link {
            background: rgba(214, 177, 92, 0.13);
            color: var(--gold) !important;
            box-shadow: inset 0 0 0 1px rgba(214, 177, 92, 0.25);
        }
        .nav-link:not(.active-link):hover {
            background: rgba(255, 255, 255, 0.04);
            color: var(--gold-light) !important;
        }
        .nav-link i.fa-chevron-right { transition: transform 0.2s ease, opacity 0.2s ease; }
        .nav-link.active-link i.fa-chevron-right { opacity: 1; transform: translateX(2px); }

        /* ── CARDS ── */
        .stat-card {
            background: var(--white);
            border-radius: 1.25rem;
            border: 1px solid rgba(214, 177, 92, 0.14);
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            overflow: hidden;
            cursor: pointer;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg), 0 0 0 1px rgba(214, 177, 92, 0.25);
            border-color: rgba(214, 177, 92, 0.35);
        }
        .stat-card .icon-circle {
            width: 48px; height: 48px; border-radius: 16px;
            background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 16px -4px rgba(10, 20, 40, 0.25);
            transition: var(--transition);
            color: var(--gold); font-size: 1.3rem;
        }
        .stat-card:hover .icon-circle {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            box-shadow: 0 12px 22px -4px rgba(214, 177, 92, 0.35);
            color: var(--navy);
        }

        /* ── CONTENT CARDS ── */
        .content-card {
            background: var(--white);
            border-radius: 1.25rem;
            border: 1px solid rgba(214, 177, 92, 0.12);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        .content-card:hover { box-shadow: var(--shadow-md); border-color: rgba(214, 177, 92, 0.22); }
        .content-card .card-accent {
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold-dark));
            border-radius: 3px 3px 0 0;
            opacity: 0; transition: opacity 0.35s ease;
        }
        .content-card:hover .card-accent { opacity: 1; }

        /* ── PROGRESS BARS ── */
        .progress-bar-bg { background: #e8e3d7; border-radius: 999px; overflow: hidden; }
        .progress-fill { border-radius: 999px; transition: width 0.8s cubic-bezier(0.22, 1, 0.36, 1); }

        /* ── BADGES ── */
        .badge {
            padding: 0.3rem 0.8rem; border-radius: 9999px;
            font-size: 0.7rem; font-weight: 500; letter-spacing: 0.02em;
            line-height: 1.2; display: inline-flex; align-items: center; white-space: nowrap;
        }
        .badge-gold { background-color: rgba(214, 177, 92, 0.15); color: #8b6914; border: 1px solid rgba(214, 177, 92, 0.3); }
        .badge-navy { background-color: rgba(10, 20, 40, 0.08); color: var(--navy); border: 1px solid rgba(10, 20, 40, 0.15); }
        .badge-muted { background-color: #f0ece4; color: var(--text-muted); border: 1px solid var(--border); }
        .badge-green { background-color: #e6f4ea; color: #1e6b3a; border: 1px solid #b7dfc5; }
        .badge-amber { background-color: #fef7e6; color: #8a5d0b; border: 1px solid #f5d78a; }
        .badge-red { background-color: #fdecea; color: #a12b2b; border: 1px solid #f2b8b8; }

        /* ── BUTTONS ── */
        .btn-primary {
            background: var(--navy); color: var(--gold-light); border: none;
            padding: 0.65rem 1.4rem; border-radius: 2rem; font-weight: 500;
            font-size: 0.85rem; letter-spacing: 0.02em; transition: var(--transition);
            cursor: pointer; box-shadow: 0 2px 8px rgba(10, 20, 40, 0.12);
            display: inline-flex; align-items: center; gap: 0.4rem;
        }
        .btn-primary:hover {
            background: var(--navy-hover); transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(10, 20, 40, 0.2); color: #fff;
        }
        .btn-primary:active { transform: scale(0.97); }
        .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .btn-outline {
            background: transparent; border: 1.5px solid var(--gold); color: var(--navy);
            padding: 0.6rem 1.3rem; border-radius: 2rem; font-weight: 500;
            font-size: 0.85rem; transition: var(--transition); cursor: pointer;
        }
        .btn-outline:hover { background: var(--gold); color: var(--navy); box-shadow: 0 4px 14px rgba(214, 177, 92, 0.3); }
        .btn-ghost {
            background: transparent; border: none; color: var(--text-muted);
            padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.8rem;
            cursor: pointer; transition: var(--transition);
        }
        .btn-ghost:hover { background: rgba(214, 177, 92, 0.08); color: var(--navy); }

        /* ── TABLES ── */
        table { border-collapse: separate; border-spacing: 0; width: 100%; }
        table th {
            font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--text-muted); font-weight: 600;
            padding: 0.9rem 0.8rem 0.7rem 0.8rem;
            border-bottom: 2px solid var(--border); text-align: left;
            background: rgba(248, 246, 240, 0.4);
        }
        table td {
            padding: 0.9rem 0.8rem;
            border-bottom: 1px solid rgba(226, 218, 207, 0.5);
            font-size: 0.875rem; color: var(--text);
        }
        table tr:last-child td { border-bottom: none; }
        table tbody tr { transition: background 0.15s ease; }
        table tbody tr:hover td { background: rgba(248, 246, 240, 0.5); }

        /* ── MODALS ── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(5, 16, 33, 0.55);
            backdrop-filter: blur(4px); display: none;
            align-items: center; justify-content: center;
            z-index: 50; padding: 1rem;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: var(--white); border-radius: 1.25rem; width: 100%;
            max-width: 30rem; max-height: 90vh; overflow-y: auto;
            padding: 1.75rem; animation: fadeInUp 0.25s ease-out both;
            box-shadow: 0 40px 60px -20px rgba(5, 16, 33, 0.3);
            border: 1px solid rgba(214, 177, 92, 0.2);
        }
        .modal-box.wide { max-width: 44rem; }
        .modal-box .modal-accent {
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            border-radius: 3px; margin-bottom: 1.25rem;
        }
        .form-label {
            display: block; font-size: 0.7rem; color: var(--text-muted);
            margin-bottom: 0.35rem; font-weight: 600;
            letter-spacing: 0.05em; text-transform: uppercase;
        }
        .form-input, .form-select {
            width: 100%; background: #faf8f4;
            border: 1.5px solid var(--border); border-radius: 0.65rem;
            padding: 0.65rem 0.9rem; font-size: 0.875rem;
            color: var(--text); outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .form-input:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-glow);
            background: var(--white);
        }
        .form-input::placeholder { color: #c8c4bc; }
        .form-select option { background: var(--white); color: var(--text); }

        /* ── PROFILE SPECIFIC ── */
        .profile-banner {
            height: 80px;
            background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
            border-radius: 1.25rem 1.25rem 0 0;
        }
        .profile-avatar-ring {
            width: 90px; height: 90px; border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            padding: 3px; margin: -45px auto 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .profile-avatar-inner {
            width: 100%; height: 100%; border-radius: 50%;
            background: var(--navy); display: flex; align-items: center; justify-content: center;
            color: var(--gold-light); font-weight: 700;
            font-size: 1.8rem; font-family: 'DM Sans', sans-serif;
        }
        .info-row {
            display: flex; align-items: center; gap: 0.8rem;
            padding: 0.6rem 0; border-bottom: 1px solid rgba(0,0,0,0.03);
        }
        .info-row:last-child { border-bottom: none; }
        .info-icon {
            width: 32px; height: 32px; border-radius: 10px;
            background: rgba(214, 177, 92, 0.1);
            display: flex; align-items: center; justify-content: center;
            color: var(--gold-dark); font-size: 0.9rem; flex-shrink: 0;
        }
        .info-label {
            font-size: 0.65rem; text-transform: uppercase;
            letter-spacing: 0.05em; color: var(--text-muted); font-weight: 500;
        }
        .info-value { font-size: 0.85rem; color: var(--text); font-weight: 500; }
        .form-fieldset-title {
            font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; color: var(--navy); font-weight: 600;
            margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;
        }
        .security-note ul li {
            display: flex; align-items: flex-start; gap: 0.4rem;
            color: var(--text-muted); line-height: 1.5;
        }
        .security-note ul li i { color: var(--gold-dark); margin-top: 0.15rem; }

        /* ── MOBILE BOTTOM NAV ── */
        .mobile-bottom-nav {
            background: rgba(10, 20, 40, 0.96);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(214, 177, 92, 0.3);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
            overflow-x: auto; white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: none; scrollbar-width: none;
        }
        .mobile-bottom-nav::-webkit-scrollbar { display: none; }
        .mobile-nav-link { transition: color 0.2s; flex-shrink: 0; min-width: 60px; }

        /* ── STAGE TABS ── */
        .stage-tab-btn { background: transparent; color: var(--text-muted); border: none; transition: var(--transition); }
        .stage-tab-btn.active { background: var(--navy); color: var(--gold-light); box-shadow: 0 2px 8px rgba(10, 20, 40, 0.15); }

        /* ── TOAST ── */
        .toast-container {
            position: fixed; top: 1.5rem; right: 1.5rem;
            z-index: 9999; max-width: 28rem; width: 100%;
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.4s ease;
            opacity: 0;
        }
        .toast-container.show { transform: translateX(0); opacity: 1; }
        .toast-content {
            background: var(--white); border-radius: 1rem;
            padding: 1rem 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-left: 5px solid var(--gold);
            display: flex; align-items: center; gap: 0.75rem;
        }
        .toast-content i { color: var(--gold); font-size: 1.25rem; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .desktop-sidebar { display: none; }
            .mobile-bottom-nav { display: flex; }
            main { padding-bottom: 6rem; padding-left: 1rem; padding-right: 1rem; }
            .stat-card { padding: 1rem !important; }
            .stat-card .icon-circle { width: 40px; height: 40px; border-radius: 12px; }
        }
        @media (min-width: 769px) {
            .mobile-bottom-nav { display: none; }
        }

        /* ── HEADING FONTS ── */
        h1, h2, h3, h4, .serif-heading {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600; letter-spacing: -0.01em;
        }
        h1 { font-size: 2rem; color: var(--navy); }
        h2 { font-size: 1.5rem; color: var(--navy); }
        h3 { font-size: 1.2rem; color: var(--navy); }

        .gold-accent-line {
            width: 50px; height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-dark));
            border-radius: 3px; margin-top: 0.4rem;
        }
        #milestones-list, #rubrics-list {
            transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
            overflow: hidden;
        }
        #milestones-list.collapsed, #rubrics-list.collapsed { display: none !important; }
        #milestones-list:not(.collapsed), #rubrics-list:not(.collapsed) { max-height: 2000px; opacity: 1; }
        .toggle-section-btn {
            background: none; border: none; padding: 0.25rem 0.5rem;
            border-radius: 0.375rem; transition: var(--transition); cursor: pointer;
        }
        .toggle-section-btn:hover { background: rgba(214, 177, 92, 0.15); }
        .group-list {
            overflow: hidden; max-height: 1000px; opacity: 1;
            transition: max-height 0.25s ease, opacity 0.2s ease;
        }
        .group-list.collapsed {
            max-height: 0 !important; opacity: 0;
            margin-top: 0 !important; padding-top: 0 !important;
            border-top: none !important;
        }
        #admin-group-section-filter {
            appearance: none; -webkit-appearance: none;
            background-color: #faf8f4;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%235b6375' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 10px 10px;
            padding-right: 2.2rem !important;
            border: 1.5px solid #e2dacf; border-radius: 0.65rem;
            font-size: 0.75rem; font-weight: 600; color: #0a1428;
            cursor: pointer; transition: border-color 0.2s, box-shadow 0.2s;
            min-width: 140px; max-width: 180px; height: 38px;
        }
        #admin-group-section-filter:hover { border-color: #d6b15c; }
        #admin-group-section-filter:focus {
            border-color: #d6b15c;
            box-shadow: 0 0 0 3px rgba(214, 177, 92, 0.15);
            outline: none;
        }
        #admin-group-section-filter option:checked { background: #0a1428; color: #f0e0b0; }
        #admin-group-section-filter option {
            background: #ffffff; color: #171e2c;
            padding: 0.4rem 0.6rem; font-weight: 500;
        }
        #admin-group-section-filter option:hover { background: #f0ece4; }
        #admin-group-section-filter:not(:focus) {
            background-color: #faf8f4; color: #0a1428; font-weight: 600;
        }
        .page-size-select {
            appearance: none; -webkit-appearance: none;
            background-color: #faf8f4;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%235b6375' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.6rem center;
            background-size: 9px 9px;
            padding: 0.32rem 1.8rem 0.32rem 0.7rem;
            border: 1.5px solid #e2dacf; border-radius: 0.5rem;
            font-size: 0.75rem; font-weight: 600; color: #0a1428;
            cursor: pointer; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .page-size-select:hover { border-color: #d6b15c; }
        .page-size-select:focus {
            border-color: #d6b15c;
            box-shadow: 0 0 0 3px rgba(214, 177, 92, 0.15);
            outline: none;
        }
        .progress-stat-card {
            cursor: pointer; border-radius: 0.75rem;
            transition: background 0.2s ease, transform 0.2s ease;
        }
        .progress-stat-card:hover { background: rgba(214, 177, 92, 0.06); transform: translateY(-1px); }
        html[data-active-section] .section-container { display: none !important; }
        html[data-active-section="dashboard"]  #dashboard-section,
        html[data-active-section="teachers"]   #teachers-section,
        html[data-active-section="students"]   #students-section,
        html[data-active-section="rubrics"]    #rubrics-section,
        html[data-active-section="progress"]   #progress-section,
        html[data-active-section="evaluation"] #evaluation-section,
        html[data-active-section="profile"]    #profile-section,
        html[data-active-section="capstone"]   #capstone-section,
        html[data-active-section="documents"]  #documents-section { display: block !important; }
        html[data-sg-view="students"] #sg-groups-view   { display: none !important; }
        html[data-sg-view="groups"]   #sg-students-view { display: none !important; }
        html[data-sg-view="students"] #sg-students-view,
        html[data-sg-view="groups"]   #sg-groups-view   { display: block !important; }
        html[data-sg-view="groups"] .sg-tab-btn[data-view="students"] { background: transparent; color: var(--text-muted); box-shadow: none; }
        html[data-sg-view="groups"] .sg-tab-btn[data-view="groups"]   { background: var(--navy); color: var(--gold-light); box-shadow: 0 2px 8px rgba(10,20,40,.15); }
        .nav-link .fa-chevron-right { display: none; }
        html[data-active-section="dashboard"]  .nav-link[data-section="dashboard"],
        html[data-active-section="teachers"]   .nav-link[data-section="teachers"],
        html[data-active-section="students"]   .nav-link[data-section="students"],
        html[data-active-section="rubrics"]    .nav-link[data-section="rubrics"],
        html[data-active-section="progress"]   .nav-link[data-section="progress"],
        html[data-active-section="evaluation"] .nav-link[data-section="evaluation"],
        html[data-active-section="profile"]    .nav-link[data-section="profile"],
        html[data-active-section="capstone"]   .nav-link[data-section="capstone"],
        html[data-active-section="documents"]  .nav-link[data-section="documents"] {
            background: rgba(214,177,92,.13); color: var(--gold) !important;
            box-shadow: inset 0 0 0 1px rgba(214,177,92,.25);
        }
        html[data-active-section="dashboard"] .nav-link[data-section="dashboard"] .fa-chevron-right { display: inline-block; }
        html { background: #f8f6f0; }

/* error toast variant */
.toast-container.is-error .toast-content { border-left-color: #a12b2b; }
.toast-container.is-error .toast-content i { color: #a12b2b; }

/* page loader shown right before reload */
#page-loader {
    position: fixed; inset: 0; z-index: 9998;
    background: rgba(248, 246, 240, 0.72); backdrop-filter: blur(3px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: opacity .15s ease;
}
#page-loader.active { opacity: 1; pointer-events: all; }
#page-loader .spinner {
    width: 38px; height: 38px; border-radius: 50%;
    border: 3px solid var(--cream-dark); border-top-color: var(--gold);
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
/* ── CAPSTONE ENABLE TOGGLE SWITCH ── */
.switch {
  --switch-width: 46px;
  --switch-height: 24px;
  --switch-bg: rgb(226, 22, 22);
  --switch-checked-bg: rgb(0, 218, 80);
  --circle-diameter: 18px;
  --circle-bg: #fff;
  --switch-offset: calc((var(--switch-height) - var(--circle-diameter)) / 2);
  --switch-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
  --circle-shadow: 1px 1px 2px rgba(146, 146, 146, 0.45);
  --circle-checked-shadow: -1px 1px 2px rgba(163, 163, 163, 0.45);
  --icon-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
  --icon-cross-color: var(--switch-bg);
  --icon-cross-size: 6px;
  --icon-checkmark-color: var(--switch-checked-bg);
  --icon-checkmark-size: 10px;
  --effect-width: calc(var(--circle-diameter) / 2);
  --effect-height: calc(var(--effect-width) / 2 - 1px);
  --effect-bg: var(--circle-bg);
  --effect-border-radius: 1px;
  --effect-transition: all .2s ease-in-out;
  display: inline-block;
  position: relative;
  vertical-align: middle;
}
.switch input { display: none; }
.switch svg { transition: var(--icon-transition); position: absolute; height: auto; }
.switch .checkmark { width: var(--icon-checkmark-size); color: var(--icon-checkmark-color); transform: scale(0); }
.switch .cross { width: var(--icon-cross-size); color: var(--icon-cross-color); }
.switch .slider {
  box-sizing: border-box;
  width: var(--switch-width);
  height: var(--switch-height);
  background: var(--switch-bg);
  border-radius: 999px;
  display: flex;
  align-items: center;
  position: relative;
  transition: var(--switch-transition);
  cursor: pointer;
}
.switch .circle {
  width: var(--circle-diameter);
  height: var(--circle-diameter);
  background: var(--circle-bg);
  border-radius: inherit;
  box-shadow: var(--circle-shadow);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--switch-transition);
  z-index: 1;
  position: absolute;
  left: var(--switch-offset);
}
.switch .slider::before {
  content: "";
  position: absolute;
  width: var(--effect-width);
  height: var(--effect-height);
  left: calc(var(--switch-offset) + (var(--effect-width) / 2));
  background: var(--effect-bg);
  border-radius: var(--effect-border-radius);
  transition: var(--effect-transition);
}
.switch input:checked + .slider { background: var(--switch-checked-bg); }
.switch input:checked + .slider .checkmark { transform: scale(1); }
.switch input:checked + .slider .cross { transform: scale(0); }
.switch input:checked + .slider::before {
  left: calc(100% - var(--effect-width) - (var(--effect-width) / 2) - var(--switch-offset));
}
.switch input:checked + .slider .circle {
  left: calc(100% - var(--circle-diameter) - var(--switch-offset));
  box-shadow: var(--circle-checked-shadow);
}
    </style>
</head>
<body class="bg-[#f8f6f0] text-[#171e2c]">
<div id="page-loader"><div class="spinner"></div></div>
    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="toast-container">
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span id="toastMessage" class="toast-message text-sm font-medium text-[#171e2c]"></span>
            <button type="button" class="toast-close text-[#5b6375] hover:text-[#0a1428] text-lg" onclick="hideToast()">&times;</button>
        </div>
    </div>

    <!-- DESKTOP SIDEBAR -->
    <aside class="desktop-sidebar fixed left-0 top-0 h-full w-64 flex flex-col justify-between z-20  hidden md:flex">
        <div>
            <div class="p-6 flex items-center space-x-3 border-b border-[rgba(214,177,92,0.12)]">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md flex-shrink-0"
                     style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-sm text-white tracking-[0.5px]">Capstone Tracker</span>
                    <small class="text-[10px] font-semibold tracking-[1px] uppercase mt-0.5" style="color: var(--gold-light); opacity: 0.85;">Admin</small>
                </div>
            </div>
            <nav class="mt-6 px-4 space-y-1">
               <a href="#" data-section="dashboard" class="nav-link flex items-center justify-between px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                <div class="flex items-center space-x-3"><i class="fas fa-th-large w-4"></i><span>Dashboard</span></div>
                <i class="fas fa-chevron-right text-xs opacity-70"></i>
                </a>
                <a href="#" data-section="teachers" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-users w-4"></i><span>Instructors</span>
                </a>
                <a href="#" data-section="students" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-user-graduate w-4"></i><span>Students & Groups</span>
                </a>
                <a href="#" data-section="rubrics" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-file-alt w-4"></i><span>Rubrics</span>
                </a>
                <a href="#" data-section="progress" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-chart-line w-4"></i><span>Progress</span>
                </a>
                <a href="#" data-section="evaluation" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-door-open w-4"></i><span>Evaluation Room</span>
                </a>
                <a href="#" data-section="capstone" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-scroll w-4"></i><span>Capstones</span>
                </a>
                <a href="#" data-section="documents" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-folder-open w-4"></i><span>Documents</span>
                </a>
                    <a href="#" data-section="profile" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-user w-4"></i><span>Profile</span>
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-[rgba(214,177,92,0.15)]">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-lg"
                     style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                    {{ strtoupper(substr($user->name ?? 'MS', 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ $user->name ?? 'Dr. Maria Santos' }}</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.5);">System Administrator</p>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-2 text-sm text-[rgba(255,255,255,0.5)] hover:text-[#f0e0b0] transition">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i><span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MOBILE BOTTOM NAV -->
    <div class="mobile-bottom-nav fixed bottom-0 left-0 right-0 py-2 px-2 justify-around items-center z-30 flex md:hidden">
        <a href="#" data-section="dashboard" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-th-large text-lg"></i><span class="text-[10px] mt-1">Home</span>
        </a>
        <a href="#" data-section="teachers" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-users text-lg"></i><span class="text-[10px] mt-1">Instructors</span>
        </a>
        <a href="#" data-section="students" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-user-graduate text-lg"></i><span class="text-[10px] mt-1">Students</span>
        </a>
        <a href="#" data-section="rubrics" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-file-alt text-lg"></i><span class="text-[10px] mt-1">Rubrics</span>
        </a>
        <a href="#" data-section="progress" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-chart-line text-lg"></i><span class="text-[10px] mt-1">Progress</span>
        </a>
        <a href="#" data-section="evaluation" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-door-open text-lg"></i><span class="text-[10px] mt-1">Rooms</span>
        </a>
        <a href="#" data-section="profile" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-user text-lg"></i><span class="text-[10px] mt-1">Profile</span>
        </a>
        <a href="#" data-section="capstone" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-scroll text-lg"></i><span class="text-[10px] mt-1">Capstones</span>
        </a>
        <a href="#" data-section="documents" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-folder-open text-lg"></i><span class="text-[10px] mt-1">Documents</span>
        </a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="mobile-nav-link flex flex-col items-center text-red-400 hover:text-red-300 text-xs py-1">
            <i class="fas fa-sign-out-alt text-lg"></i><span class="text-[10px] mt-1">Sign Out</span>
        </a>
    </div>

    <!-- MAIN CONTENT -->
    <main class="ml-0 md:ml-64 p-4 md:p-8 overflow-y-auto max-h-screen pb-20">

        <!-- ==================== DASHBOARD ==================== -->
        <div id="dashboard-section" class="section-container section-card max-w-7xl mx-auto">
            <div class="mb-8 p-6 bg-[#faf8f4] border border-[#e2dacf] rounded-2xl shadow-sm relative overflow-hidden w-full">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-[#d6b15c]/5 to-transparent pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full">
                    <div>
                        <h1 class="text-[#0a1428] text-3xl font-bold tracking-tight" style="font-family:'Cormorant Garamond', serif; font-size: 2.2rem; line-height: 1;">Dashboard</h1>
                        <div class="w-12 h-1 bg-[#d6b15c] my-2"></div>
                        <p class="text-xs text-[#5b6375]">Overview of your intelligent capstone tracking system</p>
                    </div>
                    <div class="text-left md:text-right mt-4 md:mt-0 md:pl-6 border-l md:border-l-0 md:border-r-0 border-[#e2dacf] pl-4 md:pl-0">
                        <p class="text-[#b88d3a] text-[10px] tracking-wider uppercase font-bold mb-1">Academic Configuration</p>
                        <h2 class="text-lg font-semibold flex items-center md:justify-end gap-2 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif; font-size:1.4rem;">
                            <i class="fa-solid fa-calendar-check text-[#d6b15c]"></i> Active Capstone Year: {{ $activeYear->year }}
                        </h2>
                        <p class="text-[11px] text-[#5b6375] mt-1 max-w-sm md:ml-auto">
                            Capstone 1: <strong class="{{ $activeYear->capstone_1_enabled ? 'text-green-600' : 'text-red-500' }}">{{ $activeYear->capstone_1_enabled ? 'Enabled' : 'Disabled' }}</strong> |
                            Capstone 2: <strong class="{{ $activeYear->capstone_2_enabled ? 'text-green-600' : 'text-red-500' }}">{{ $activeYear->capstone_2_enabled ? 'Enabled' : 'Disabled' }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('students')">
                    <div>
                        <p class="text-[#5b6375] text-xs font-medium">Total Students</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ $totalStudents ?? 0 }}</p>
                    </div>
                    <div class="icon-circle"><i class="fas fa-users text-lg"></i></div>
                </div>
                <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('groups')">
                    <div>
                        <p class="text-[#5b6375] text-xs font-medium">Capstone Groups</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ $totalGroups ?? 0 }}</p>
                    </div>
                    <div class="icon-circle"><i class="fas fa-layer-group text-lg"></i></div>
                </div>
                <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('teachers')">
                    <div>
                        <p class="text-[#5b6375] text-xs font-medium">Teachers</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ $totalTeachers ?? 0 }}</p>
                    </div>
                    <div class="icon-circle"><i class="fas fa-chalkboard-teacher text-lg"></i></div>
                </div>
                <div class="stat-card p-4 md:p-5 flex justify-between items-center" onclick="openDashboardDetailModal('sections')">
                    <div>
                        <p class="text-[#5b6375] text-xs font-medium">Sections</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 text-[#0a1428]" style="font-family:'Cormorant Garamond',serif;">{{ $totalSections ?? 0 }}</p>
                    </div>
                    <div class="icon-circle"><i class="fas fa-columns text-lg"></i></div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="content-card lg:col-span-2">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3>Capstone Completion Progress</h3>
                            <i class="fas fa-arrow-up-right-from-square text-[#b8b0a0] cursor-pointer hover:text-[#0a1428] transition text-sm"></i>
                        </div>
                        <div class="space-y-4">
                            @forelse($milestoneCompletion ?? [] as $m)
                            @php
                                $pct = ($m->total > 0) ? round(($m->completed / $m->total) * 100) : 0;
                                if ($pct >= 75) { $pctColor = '#1e6b3a'; }
                                elseif ($pct >= 40) { $pctColor = '#b88d3a'; }
                                else { $pctColor = '#a12b2b'; }
                            @endphp
                            <div class="milestone-row flex items-center gap-4" data-stage="{{ $m->stage }}">
                                <div class="w-28 text-sm text-[#171e2c] font-medium flex-shrink-0 truncate" title="{{ $m->name }}">{{ $m->name }}</div>
                                <div class="flex-1 progress-bar-bg h-2.5">
                                    <div class="progress-fill fill-animate h-full" data-target="{{ $pct }}" style="width:0%; background:{{ $m->color ?? $pctColor }};"></div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-xs text-[#9a9385] w-10 text-right">{{ $m->completed }}/{{ $m->total }}</span>
                                    <span class="text-xs font-bold w-9 text-right" style="color:{{ $pctColor }};">{{ $pct }}%</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-[#5b6375] text-sm text-center py-6">No milestones created yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="content-card lg:col-span-1">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3>Recent Activity</h3>
                            <i class="fas fa-clock text-[#b8b0a0] cursor-pointer hover:text-[#0a1428] transition text-sm"></i>
                        </div>
                        <div class="space-y-5">
                          @forelse($recentActivities ?? [] as $act)
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full border flex items-center justify-center flex-shrink-0 mt-0.5 text-[11px]"
                                style="border-color:{{ $act['color'] }}; color:{{ $act['color'] }};">
                                <i class="fas {{ $act['icon'] }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#171e2c]">{{ $act['title'] }}</p>
                                <p class="text-xs text-[#5b6375]">{{ $act['subtitle'] }}</p>
                                <p class="text-[10px] text-[#9a9385] mt-0.5">{{ \Carbon\Carbon::parse($act['timestamp'])->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-[#5b6375] text-sm text-center py-6">No recent activity yet.</p>
                        @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TEACHERS ==================== -->
        <div id="teachers-section" class="section-container hidden section-card">
            <div class="mb-8">
                <h1>Teachers</h1>
                <div class="gold-accent-line"></div>
                <p class="text-[#5b6375] mt-2 text-sm">Manage faculty accounts and section assignments.</p>
            </div>
            <div class="content-card">
                <div class="card-accent"></div>
                <div class="p-6">
                    <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
                        <div class="relative flex-1 min-w-[200px]">
                            <input type="text" id="teacher-search" class="form-input text-xs w-full pl-9 py-2" placeholder="Search by name, ID, email…">
                            <i class="fas fa-search absolute left-3 top-2.5 text-[#b8b0a0] text-xs"></i>
                        </div>
                        <div class="text-xs text-[#5b6375]" id="teacher-count">{{ count($allTeachers ?? []) }} teachers</div>
                    </div>
                    <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
                        <h3>All Teachers</h3>
                        <div class="flex gap-2">
                        <button onclick="openModal('assign_section_modal')" class="btn-primary text-sm"><i class="fas fa-plus mr-1"></i> Assign a section</button>
                        <button onclick="resetAssignModal(); openModal('assign_modal')" class="btn-outline text-sm"><i class="fas fa-link mr-1"></i> Change Adviser</button>
                        <button onclick="openModal('import_teacher_modal')" class="btn-outline text-sm"><i class="fas fa-file-import mr-1"></i> Import Excel</button>
                        <button onclick="openModal('teacher_modal')" class="btn-primary text-sm"><i class="fas fa-plus mr-1"></i> Add Teacher</button>
                    </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table>
                            <thead><tr><th>Teacher</th><th>Email</th><th>Assigned Groups</th><th>Assigned Sections</th><th class="pr-2">Actions</th></tr></thead>
                            <tbody>
                                @forelse($allTeachers ?? [] as $teacher)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                                 style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);">
                                                {{ strtoupper(substr($teacher->teacher_first_name, 0, 1) . substr($teacher->teacher_last_name, 0, 1)) }}
                                            </div>
                                            <div><p class="text-sm font-semibold">{{ $teacher->teacher_first_name }} {{ $teacher->teacher_last_name }}</p><p class="text-xs text-[#5b6375]">{{ $teacher->user->user_id ?? 'N/A' }}</p></div>
                                        </div>
                                    </td>
                                    <td class="text-sm text-[#3d4450]">{{ $teacher->teacher_email }}</td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($teacher->groups as $group)
                                            <span class="badge badge-navy">{{ $group->group_name }}</span>
                                            @empty
                                            <span class="badge badge-muted">No Group</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($teacher->sections as $section)
                                            <span class="badge badge-navy">{{ $section->section_name }}</span>
                                            @empty
                                            <span class="badge badge-muted">No Section Assigned</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="pr-2">
                                        <div class="flex gap-3 text-[#5b6375]">
                                            <button onclick='openEditTeacherModal(@json($teacher))' class="hover:text-[#0a1428] transition"><i class="fas fa-pen"></i></button>
                                            <button type="button" onclick="openDeleteTeacherModal('{{ $teacher->user_id }}', '{{ addslashes($teacher->teacher_first_name . ' ' . $teacher->teacher_last_name) }}')" class="hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-6 text-center text-[#5b6375]">No teachers found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== STUDENTS & GROUPS ==================== -->
<div id="students-section" class="section-container hidden section-card">
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1>Students & Groups</h1>
            <div class="gold-accent-line"></div>
            <p class="text-[#5b6375] mt-2 text-sm">Manage student accounts and group assignments.</p>
        </div>
        <div class="flex gap-1.5 bg-[#f5f1e8] p-1 rounded-full">
            <button type="button" class="stage-tab-btn sg-tab-btn text-xs font-medium py-1.5 px-4 rounded-full transition-smooth active" data-view="students">Students</button>
            <button type="button" class="stage-tab-btn sg-tab-btn text-xs font-medium py-1.5 px-4 rounded-full transition-smooth" data-view="groups">Groups</button>
        </div>
    </div>

        <div id="sg-students-view" class="content-card">
            <div class="card-accent"></div>
            <div class="p-6">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <div class="relative flex-1 min-w-[160px]">
                        <input type="text" id="student-search" class="form-input text-xs w-full pl-9 py-2" placeholder="Search by name, ID, section, group…">
                        <i class="fas fa-search absolute left-3 top-2.5 text-[#b8b0a0] text-xs"></i>
                    </div>
                </div>
                <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
                    <h3>All Students</h3>
                    <div class="flex gap-2 flex-wrap">
                                            <div class="flex items-center gap-2 text-xs text-[#5b6375]">
                        <span>Show</span>
                        <select id="students-page-size" class="page-size-select">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                        </select>
                        <span>per page</span>
                    </div>
                        <button onclick="openModal('createGroupModal')" class="btn-outline text-sm"><i class="fas fa-users mr-1"></i> Create Group</button>
                        <button onclick="openModal('import_student_modal')" class="btn-outline text-sm"><i class="fas fa-file-import mr-1"></i> Import Excel</button>
                        <button onclick="openModal('student_modal')" class="btn-primary text-sm"><i class="fas fa-plus mr-1"></i> Register Student</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>ID</th>
                                <th>Course</th>
                                <th>
                                    <select id="admin-student-section-filter" class="form-select text-xs py-1 px-2 border border-[#e2dacf] rounded-lg bg-[#faf8f4] font-semibold" style="color: var(--navy); max-width: 140px;">
                                        <option value="All">All Section</option>
                                        @forelse ($allSections as $section)
                                            <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                                        @empty
                                            <option disabled>No section available</option>
                                        @endforelse
                                    </select>
                                </th>
                                <th>
                                    <select id="admin-student-group-filter" class="form-select text-xs py-1 px-2 border border-[#e2dacf] rounded-lg bg-[#faf8f4] font-semibold" style="color: var(--navy); max-width: 140px;" disabled>
                                        <option value="All">All Groups</option>
                                    </select>
                                </th>
                                <th class="pr-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="students-tbody">
                            @forelse($allStudents ?? [] as $student)
                            <tr data-section="{{ $student->section }}" data-group="{{ $student->groups->pluck('group_name')->first() ?? 'No Group' }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                            style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);">
                                            {{ strtoupper(substr($student->student_first_name, 0, 1) . substr($student->student_last_name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm">{{ $student->student_first_name }} {{ $student->student_last_name }}</span>
                                    </div>
                                </td>
                                <td class="text-sm text-[#3d4450]">{{ $student->user->user_id ?? 'N/A' }}</td>
                                <td class="text-sm text-[#3d4450]">{{ $student->course }}</td>
                                <td class="text-sm text-[#3d4450]">{{ $student->section }}</td>
                                <td class="text-sm text-[#3d4450]">{{ $student->groups->pluck('group_name')->join(', ') ?: 'No Group' }}</td>
                                <td class="pr-2">
                                    <div class="flex gap-3 text-[#5b6375]">
                                        <button onclick='openEditStudentModal(@json($student))' class="hover:text-[#0a1428] transition"><i class="fas fa-pen"></i></button>
                                        <button type="button" onclick="openDeleteStudentModal('{{ $student->user_id }}', '{{ addslashes($student->student_first_name . ' ' . $student->student_last_name) }}')" class="hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="no-students-row">
                                <td colspan="6" class="py-6 text-center text-[#5b6375]">No students found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between mt-5 pt-4 border-t border-[#e2dacf] gap-3 flex-wrap">
                    <span class="text-xs text-[#5b6375]">
                        Showing <span id="students-start">0</span> – <span id="students-end">0</span> of <span id="students-total">0</span>
                    </span>
                    <div class="flex gap-2">
                        <button id="students-prev" class="btn-ghost text-xs px-3 py-1 border border-[#e2dacf] rounded-lg hover:bg-[#faf8f4] disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-chevron-left mr-1"></i> Previous
                        </button>
                        <span id="students-page-info" class="text-xs text-[#5b6375] self-center">Page 1</span>
                        <button id="students-next" class="btn-ghost text-xs px-3 py-1 border border-[#e2dacf] rounded-lg hover:bg-[#faf8f4] disabled:opacity-40 disabled:cursor-not-allowed">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
<div id="sg-groups-view" class="content-card hidden">
    <div class="card-accent"></div>
    <div class="p-6">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <div class="relative flex-1 min-w-[160px]">
                <input type="text" id="group-search" class="form-input text-xs w-full pl-9 py-2" placeholder="Search by group, title, adviser, room…">
                <i class="fas fa-search absolute left-3 top-2.5 text-[#b8b0a0] text-xs"></i>
            </div>
        </div>
        <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
            <h3>All Groups</h3>
            <div class="flex gap-2 flex-wrap items-center">
                                <div class="flex items-center gap-2 text-xs text-[#5b6375]">
                    <span>Show</span>
                    <select id="groups-page-size" class="page-size-select">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                    <span>per page</span>
                </div>
                <button onclick="exportGroupsToExcel()" class="btn-outline text-sm"><i class="fas fa-file-excel mr-1"></i> Export to Excel</button>
                <button onclick="openModal('createGroupModal')" class="btn-primary text-sm"><i class="fas fa-plus mr-1"></i> Create Group</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Capstone Title</th>
                        <th>                
                            <select id="admin-group-section-filter" class="form-select text-xs py-1 px-2 border border-[#e2dacf] rounded-lg bg-[#faf8f4] font-semibold" style="color: var(--navy); max-width: 140px;">
                    <option value="All">All Sections</option>
                    @forelse ($allSections as $section)
                        <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                    @empty
                        <option disabled>No section available</option>
                    @endforelse
                </select></th>
                        <th>Adviser</th>
                        <th>Members</th>
                        <th>Capstone 1</th>
                        <th>Capstone 2</th>
                        <th>Room</th>
                        <th class="pr-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="groups-tbody">
                    @forelse($groupsData ?? [] as $group)
                    <tr class="groups-row" data-section="{{ $group['section_name'] }}">
                        <td class="text-sm font-semibold text-[#171e2c]">{{ $group['name'] }}</td>
                        <td class="text-sm text-[#3d4450]">{{ $group['capstone_title'] ?? '—' }}</td>
                        <td class="text-sm text-[#3d4450]">{{ $group['section_name'] }}</td>
                        <td class="text-sm text-[#3d4450]">{{ $group['assigned_teacher_name'] ?? 'Unassigned' }}</td>
                        <td class="text-sm text-[#3d4450]">
                            <div class="space-y-1">
                                @foreach($group['members'] as $m)
                                    <div class="flex items-center gap-1">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                                        <span class="text-xs font-medium text-[#171e2c]">{{ $m['name'] }}</span>
                                    </div>
                                @endforeach
                                @if(empty($group['members']))
                                    <span class="text-[#5b6375] italic text-xs">No members</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-xs">
                            @if($group['completed_c1'])
                                <span class="badge badge-green"><i class="fa-solid fa-circle-check mr-0.5"></i> Completed</span>
                            @else
                                <span class="badge badge-amber"><i class="fa-regular fa-clock mr-0.5"></i> In Progress</span>
                            @endif
                        </td>
                        <td class="text-xs">
                            @if($group['completed_c2'])
                                <span class="badge badge-green"><i class="fa-solid fa-circle-check mr-0.5"></i> Completed</span>
                            @else
                                <span class="badge badge-amber"><i class="fa-regular fa-clock mr-0.5"></i> In Progress</span>
                            @endif
                        </td>
                        <td class="text-sm text-[#3d4450]">
                            <span class="badge {{ $group['room_name'] !== 'Unassigned' ? 'badge-gold' : 'badge-muted' }}">
                                {{ $group['room_name'] }}
                            </span>
                        </td>
                        <td class="pr-2">
                            <div class="flex gap-3 text-[#5b6375]">
                                <button onclick="openEditGroupModal({{ $group['id'] }})" class="hover:text-[#0a1428] transition"><i class="fas fa-pen"></i></button>
                                <button type="button" onclick="openDeleteGroupModal({{ $group['id'] }}, '{{ addslashes($group['name']) }}')" class="hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="no-groups-row">
                        <td colspan="9" class="py-6 text-center text-[#5b6375]">No groups found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex items-center justify-between mt-5 pt-4 border-t border-[#e2dacf] gap-3 flex-wrap">
                <span class="text-xs text-[#5b6375]">
                    Showing <span id="groups-start">0</span> – <span id="groups-end">0</span> of <span id="groups-total">0</span>
                </span>
                <div class="flex gap-2">
                    <button id="groups-prev" class="btn-ghost text-xs px-3 py-1 border border-[#e2dacf] rounded-lg hover:bg-[#faf8f4] disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left mr-1"></i> Previous
                    </button>
                    <span id="groups-page-info" class="text-xs text-[#5b6375] self-center">Page 1</span>
                    <button id="groups-next" class="btn-ghost text-xs px-3 py-1 border border-[#e2dacf] rounded-lg hover:bg-[#faf8f4] disabled:opacity-40 disabled:cursor-not-allowed">
                        Next <i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

       <!-- ==================== RUBRICS ==================== -->
<div id="rubrics-section" class="section-container hidden section-card">
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1>Rubrics</h1>
            <div class="gold-accent-line"></div>
            <p class="text-[#5b6375] mt-2 text-sm">Create and manage evaluation rubrics for each capstone stage.</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 mb-5">
        <button onclick="openModal('rearrange_milestones_modal')" class="btn-primary text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 shadow-md"><i class="fas fa-sort mr-1"></i> Rearrange Step Order</button>
        <button onclick="openModal('milestone_modal')" class="btn-primary text-sm"><i class="fas fa-plus mr-1"></i> Add Milestone</button>
    </div>

    <div class="content-card mb-6">
        <div class="card-accent"></div>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="whitespace-nowrap">All Milestones</h3>
                <button type="button" class="toggle-section-btn text-[#5b6375] hover:text-[#0a1428] transition-transform duration-200" data-target="milestones-list">
                    <i class="fa-solid fa-chevron-up"></i>
                </button>
            </div>
            <div id="milestones-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($milestones->sortBy([['capstone_stage_id','asc'],['step_order','asc']]) as $milestone)
                @php $stageType = $milestone->capstoneStage->stage_type ?? null; @endphp
                <div class="bg-[#faf8f4] border border-[#e2dacf] rounded-xl p-5 hover:border-[#d6b15c] transition cursor-pointer group milestone-item" data-stage-type="{{ $stageType }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[10px] font-bold text-[#b88d3a] uppercase tracking-wider">Step {{ $milestone->step_order }}</span>
                            <h4 class="font-semibold text-sm text-[#0a1428]">{{ $milestone->milestone_title }}</h4>
                        </div>
                        <div class="flex gap-3 text-[#5b6375] ml-2 opacity-0 group-hover:opacity-100 transition">
                            <button onclick="openEditMilestoneModal({{ $milestone->id }})" class="hover:text-[#0a1428]"><i class="fas fa-pen text-xs"></i></button>
                           <button onclick="openDeleteMilestoneModal({{$milestone->id}}, '{{ addslashes($milestone->milestone_title) }}')" class="hover:text-red-500">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider {{ $stageType == 1 ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            Capstone {{ $stageType ?? '?' }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-[#5b6375] col-span-2 text-center py-6">No milestones created yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-accent"></div>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3>All Rubrics</h3>
                <button type="button" class="toggle-section-btn text-[#5b6375] hover:text-[#0a1428] transition-transform duration-200" data-target="rubrics-list">
                    <i class="fa-solid fa-chevron-up"></i>
                </button>
            </div>
            <div id="rubrics-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($rubrics->sortBy(fn($r) => $r->milestone->step_order ?? 0) as $rubric)
                @php $stageType = $rubric->milestone->capstoneStage->stage_type ?? null; @endphp
                <div class="bg-[#faf8f4] border border-[#e2dacf] rounded-xl p-5 hover:border-[#d6b15c] transition cursor-pointer group rubric-item" data-stage-type="{{ $stageType }}">
                    <div class="flex justify-between items-start">
                        <div>
                            @if($rubric->milestone)
                            <span class="text-[10px] font-bold text-[#b88d3a] uppercase tracking-wider">{{ $rubric->milestone->milestone_title }}</span>
                            @endif
                            <h4 class="font-semibold text-sm text-[#0a1428]">{{ $rubric->rubric_name }}</h4>
                        </div>
                        <div class="flex gap-3 text-[#5b6375] ml-2 opacity-0 group-hover:opacity-100 transition">
                            <button onclick="openEditRubricModal({{ $rubric->id }})" class="hover:text-[#0a1428]"><i class="fas fa-pen text-xs"></i></button>
                            <button onclick="openDeleterubricModal({{$rubric->id}})" class="hover:text-red-500"><i class="fas fa-trash text-xs"></i></button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider {{ $stageType == 1 ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $stageType == 1 ? 'Capstone 1' : ($stageType == 2 ? 'Capstone 2' : 'No Stage') }}
                        </span>
                        <span class="badge badge-muted">{{ $rubric->criteria->count() }} Criteria</span>
                    </div>
                    <div class="flex gap-4 mt-3 text-sm text-[#5b6375]">
                        <span>Max: {{ $rubric->criteria->sum('max_score') }}</span>
                    </div>
                </div>
                @empty
                <p class="text-[#5b6375] col-span-2 text-center py-6">No rubrics created yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

        <!-- ==================== PROGRESS ==================== -->
<div id="progress-section" class="section-container hidden section-card">
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1>Progress Monitoring</h1>
            <div class="gold-accent-line"></div>
            <p class="text-[#5b6375] mt-2 text-sm">Real-time tracking of all capstone sections and groups.</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-[#5b6375]">
            <i class="fas fa-circle text-[8px]" style="color:#1e6b3a;"></i> On Track
            <i class="fas fa-circle text-[8px] ml-3" style="color:#b88d3a;"></i> At Risk
            <i class="fas fa-circle text-[8px] ml-3" style="color:#a12b2b;"></i> Needs Attention
        </div>
    </div>

    <div class="content-card mb-8">
        <div class="card-accent"></div>
        <div class="p-6 grid grid-cols-2 lg:grid-cols-4 divide-x divide-[#eee7d9]">
            @php
            $progressStats = [
                ['label'=>'On Track','count'=>$onTrackCount??48,'color'=>'#1e6b3a','icon'=>'fa-check-circle'],
                ['label'=>'At Risk','count'=>$atRiskCount??9,'color'=>'#b88d3a','icon'=>'fa-clock'],
                ['label'=>'Needs Attention','count'=>$delayedCount??5,'color'=>'#a12b2b','icon'=>'fa-exclamation-triangle'],
            ];
            $avgP = $avgProgress ?? 73;
            $ringColor = $avgP >= 75 ? '#1e6b3a' : ($avgP >= 50 ? '#b88d3a' : '#a12b2b');
            $ringDeg = round(($avgP/100)*360);
            @endphp
            @foreach($progressStats as $i => $ps)
            <div class="progress-stat-card flex items-center gap-3 {{ $i === 0 ? 'pl-0' : 'pl-6' }} pr-6"
                 onclick="openProgressDetailModal('{{ $ps['label'] }}')"
                 title="Click to view {{ $ps['label'] }} groups">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $ps['color'] }}14; color:{{ $ps['color'] }};">
                    <i class="fas {{ $ps['icon'] }} text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-[#9a9385] font-semibold">{{ $ps['label'] }}</p>
                    <p class="text-xl font-bold text-[#0a1428] leading-tight" style="font-family:'Cormorant Garamond',serif;">{{ $ps['count'] }} <span class="text-xs font-normal text-[#9a9385]" style="font-family:'DM Sans',sans-serif;">groups</span></p>
                </div>
            </div>
            @endforeach
            <div class="flex items-center gap-4 pl-6">
                <div class="relative w-14 h-14 flex-shrink-0" style="border-radius:50%; background:conic-gradient({{ $ringColor }} {{ $ringDeg }}deg, #eee7d9 0deg);">
                    <div class="absolute inset-[3px] rounded-full bg-white flex items-center justify-center"><span class="text-sm font-bold text-[#0a1428]">{{ $avgP }}%</span></div>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-[#9a9385] font-semibold">Overall</p>
                    <p class="text-sm font-semibold text-[#171e2c]">Average Progress</p>
                </div>
            </div>
        </div>
    </div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <div class="content-card">
            <div class="card-accent"></div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-[#f0ece4]">
                    <div><h3>Section Progress</h3><p class="text-xs text-[#9a9385] mt-0.5">Completion rate by student section</p></div>
                    <i class="fas fa-chart-simple text-[#c9a24a]"></i>
                </div>
                @if(count($sectionProgress ?? []) <= 0)
                <p class="text-[#5b6375] text-sm text-center py-6">No student sections found.</p>
                @else
                <div class="space-y-5">
                    @foreach($sectionProgress as $section)
                    @php
                        $avg = $section->avg ?? 0;
                        if ($avg >= 75) { $statusColor = '#1e6b3a'; $statusBg = '#e6f4ea'; }
                        elseif ($avg >= 50) { $statusColor = '#8a5d0b'; $statusBg = '#fef7e6'; }
                        else { $statusColor = '#a12b2b'; $statusBg = '#fdecea'; }
                        $total = $section->done + $section->in_progress + $section->not_started;
                    @endphp
                    <div class="pb-5 border-b border-[#f5f1e8] last:border-0 last:pb-0">
                        <div class="flex justify-between items-center mb-1.5">
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-sm text-[#0a1428] tracking-tight">{{ $section->name }}</h4>
                                <button type="button" class="toggle-section-btn text-[#5b6375] hover:text-[#0a1428] transition-transform duration-200" data-target="section-groups-{{ $loop->index }}">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <span class="badge flex items-center gap-1.5" style="background:{{ $statusBg }}; color:{{ $statusColor }}; border:1px solid {{ $statusColor }}30;">
                                <span class="inline-block w-1.5 h-1.5 rounded-full" style="background:{{ $statusColor }};"></span>{{ $avg }}%
                            </span>
                        </div>
                        <div class="progress-bar-bg h-2.5 w-full"><div class="progress-fill fill-animate h-full" data-target="{{ $avg }}" style="width:0%; background:{{ $statusColor }};"></div></div>
                        @if($total > 0)
                        <p class="text-[11px] text-[#8b8477] mt-2 font-medium flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:#1e6b3a;"></span> {{ $section->done }} done</span>
                            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:#b88d3a;"></span> {{ $section->in_progress }} in progress</span>
                            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:#9a9385;"></span> {{ $section->not_started }} not started</span>
                        </p>
                        @else
                        <p class="text-xs text-[#9a9385] mt-2">No groups yet</p>
                        @endif

                        <div id="section-groups-{{ $loop->index }}" class="group-list mt-3 pt-3 border-t border-[#e2dacf]/50 collapsed">
                            @if(!empty($section->groups))
                                <p class="text-[10px] uppercase tracking-wider font-semibold text-[#5b6375] mb-2">Groups in this section</p>
                                @foreach($section->groups as $group)
                                <div class="flex items-center justify-between py-1.5 text-sm live-group-row"
                                     data-group-id="{{ $group->id }}"
                                     data-group-name="{{ $group->name }}"
                                     data-section="{{ $section->name }}"
                                     data-progress="{{ $group->progress }}">
                                    <span class="text-[#171e2c] font-medium">{{ $group->name }}</span>
                                    <div class="flex items-center gap-3">
                                        <div class="progress-bar-bg h-1.5 w-20">
                                            <div class="progress-fill live-progress-bar h-full" data-base-color="{{ $group->color }}" style="width:{{ $group->progress }}%; background:{{ $group->color }};"></div>
                                        </div>
                                        <span class="text-xs font-bold live-progress-pct" style="color:{{ $group->color }};">{{ $group->progress }}%</span>
                                        <span class="badge text-[10px] live-progress-badge" style="background:{{ $group->color }}20; color:{{ $group->color }}; border:1px solid {{ $group->color }}40;">
                                            {{ $group->status }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-xs text-[#9a9385] py-1">No groups in this section</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div class="content-card">
            <div class="card-accent"></div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-2 pb-4 border-b border-[#f0ece4]">
                    <div><h3>Milestone Completion</h3><p class="text-xs text-[#9a9385] mt-0.5">Progress across capstone stages</p></div>
                </div>
                <div class="space-y-4 mt-5">
                    @forelse($milestoneCompletion ?? [] as $m)
                    @php
                        $pct = ($m->total > 0) ? round(($m->completed / $m->total) * 100) : 0;
                        if ($pct >= 75) { $pctColor = '#1e6b3a'; }
                        elseif ($pct >= 40) { $pctColor = '#b88d3a'; }
                        else { $pctColor = '#a12b2b'; }
                    @endphp
                    <div class="milestone-row flex items-center gap-4" data-stage="{{ $m->stage }}">
                        <div class="w-28 text-sm text-[#171e2c] font-medium flex-shrink-0 truncate" title="{{ $m->name }}">{{ $m->name }}</div>
                        <div class="flex-1 progress-bar-bg h-2.5"><div class="progress-fill fill-animate h-full" data-target="{{ $pct }}" style="width:0%; background:{{ $m->color ?? $pctColor }};"></div></div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-xs text-[#9a9385] w-10 text-right">{{ $m->completed }}/{{ $m->total }}</span>
                            <span class="text-xs font-bold w-9 text-right" style="color:{{ $pctColor }};">{{ $pct }}%</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-[#5b6375] text-sm text-center py-6">No milestones created yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
        {{-- evaluation --}}
<div id="evaluation-section" class="section-container hidden section-card">
            <div class="mb-8"><h1>Evaluation</h1><div class="gold-accent-line"></div><p class="text-[#5b6375] mt-2 text-sm">Create evaluation rooms and manage panelists.</p></div>
            <div class="content-card">
                <div class="card-accent"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
                        <h3>All Rooms</h3>
                        <button onclick="openModal('evaluation_modal')" class="btn-primary text-sm"><i class="fas fa-plus mr-1"></i> Create Room</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($evaluationRooms ?? [] as $room)
                        <div class="bg-[#faf8f4] border border-[#e2dacf] rounded-xl p-5 hover:border-[#d6b15c] transition cursor-pointer group" onclick="openEditRoomModal({{ $room->id }})">
                            <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-sm text-[#0a1428]">{{ $room->room_name }}</h4>
                                <button onclick="event.stopPropagation(); openDeleteRoomModal({{ $room->id }}, '{{ addslashes($room->room_name) }}')" class="text-[#5b6375] hover:text-red-500 opacity-0 group-hover:opacity-100 transition"><i class="fas fa-trash text-xs"></i></button>
                            </div>
                            <div class="flex items-center gap-2 mt-2" onclick="event.stopPropagation();">
                                <span class="badge badge-amber font-mono tracking-wider">{{ $room->join_code }}</span>
                                <button onclick="event.stopPropagation(); regenerateRoomCode({{ $room->id }}, this)" class="text-[#5b6375] hover:text-[#0a1428] text-xs" title="Regenerate code">
                                    <i class="fas fa-rotate"></i>
                                </button>
                            </div>
                            <div class="mt-2.5 text-xs text-[#5b6375] space-y-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-flag text-[#d6b15c] text-[10px]"></i>
                                    <span>Milestone: <strong class="text-[#171e2c] font-medium">{{ $room->requiredMilestone->milestone_title ?? 'None' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-play text-[#d6b15c] text-[10px]"></i>
                                    <span>Activity: <strong class="text-[#171e2c] font-medium">{{ $room->activity_name ?? 'N/A' }}</strong></span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1 mt-3">
                                @forelse($room->panelists as $p)
                                <span class="badge badge-navy">{{ $p->teacher_first_name }} {{ $p->teacher_last_name }}</span>
                                @empty
                                <span class="badge badge-muted">No panelists yet</span>
                                @endforelse
                            </div>
                            <div class="mt-4 pt-3 border-t border-[#e2dacf]/50">
                                <div class="text-[11px] uppercase tracking-wider font-semibold text-[#8b6914] mb-1.5">Assigned Groups</div>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($room->groups as $g)
                                    <span class="badge badge-gold">{{ $g->group_name }}</span>
                                    @empty
                                    <span class="badge badge-muted">No groups yet</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-[#5b6375] col-span-2 text-center py-6">No evaluation rooms created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <!-- ==================== DOCUMENTS ==================== -->
<div id="documents-section" class="section-container hidden section-card max-w-7xl mx-auto">
    <div class="mb-8">
        <h1>Documents</h1>
        <div class="gold-accent-line"></div>
        <p class="text-[#5b6375] mt-2 text-sm">Certificates and documents that unlock automatically when a group completes their linked milestone — each carries a unique serial number for verification.</p>
    </div>

    <div class="content-card">
        <div class="card-accent"></div>
        <div class="p-6">
            <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
                <h3>Issued Documents</h3>
                <div class="relative">
                    <input type="text" id="documents-search" class="form-input text-xs pl-9 py-2 w-64" placeholder="Search by serial, group, or document...">
                    <i class="fas fa-search absolute left-3 top-2.5 text-[#b8b0a0] text-xs"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Document</th>
                            <th>Group</th>
                            <th>Section</th>
                            <th>Issued Date</th>
                        </tr>
                    </thead>
                    <tbody id="documents-tbody">
                        @forelse($issuedDocuments ?? [] as $doc)
                        <tr class="documents-row" data-search="{{ strtolower($doc['serial_number'] . ' ' . $doc['certificate_title'] . ' ' . $doc['group_name'] . ' ' . $doc['section_name']) }}">
                            <td>
                                <span class="badge badge-gold font-mono tracking-wider">{{ $doc['serial_number'] }}</span>
                            </td>
                            <td class="text-sm font-semibold text-[#171e2c]">
                                <i class="fa-regular fa-file-lines text-[#d6b15c] mr-1.5"></i>{{ $doc['certificate_title'] }}
                            </td>
                            <td class="text-sm text-[#3d4450]">{{ $doc['group_name'] }}</td>
                            <td class="text-sm text-[#3d4450]">{{ $doc['section_name'] }}</td>
                            <td class="text-sm text-[#3d4450]">
                                {{ $doc['issued_date'] ? \Carbon\Carbon::parse($doc['issued_date'])->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr id="documents-empty-row">
                            <td colspan="5" class="py-8 text-center text-[#5b6375]">
                                <i class="fa-regular fa-folder-open text-2xl mb-2 block"></i>
                                No documents have been issued yet. They unlock automatically once a group completes the milestone linked to a document.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <p id="documents-no-match" class="hidden text-center py-8 text-[#5b6375]">
                    <i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No documents match your search.
                </p>
            </div>
        </div>
    </div>
</div>
        <!-- ==================== PROFILE ==================== -->
        <div id="profile-section" class="section-container hidden section-card max-w-7xl mx-auto">
            <div class="mb-8"><h1>Profile</h1><div class="gold-accent-line"></div><p class="text-[#5b6375] mt-2 text-sm">Manage your personal information and contact details</p></div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="content-card lg:col-span-1 overflow-hidden">
                    <div class="profile-banner"></div>
                    <div class="p-6 -mt-12 flex flex-col items-center text-center">
                        <div class="profile-avatar-ring">
                            <div class="profile-avatar-inner">{{ strtoupper(substr($admin->admin_first_name ?? $user->name ?? 'T', 0, 2)) }}</div>
                        </div>
                        <h2 class="mt-4">{{ $admin->admin_first_name ?? '' }} {{ $admin->admin_last_name ?? '' }}</h2>
                        <p class="text-[#5b6375] text-xs font-medium tracking-wide uppercase mt-0.5">Administrator</p>
                        <p class="text-[#5b6375] text-sm mt-1">ID: {{ $admin->user_id ?? $user->user_id ?? '—' }}</p>
                        <div class="mt-5 w-full pt-5 border-t border-[#e2dacf] space-y-1">
                            <div class="info-row">
                                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                                <div class="text-left"><p class="info-label">Contact</p><p class="info-value">{{ $admin->contact_number ?? 'Not provided' }}</p></div>
                            </div>
                            <div class="info-row">
                                <div class="info-icon"><i class="fa-regular fa-envelope"></i></div>
                                <div class="text-left"><p class="info-label">Email</p><p class="info-value break-all">{{ $admin->admin_email ?? $user->email ?? 'Not provided' }}</p></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-card lg:col-span-2">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <h3 class="mb-5 flex items-center gap-2"><i class="fa-regular fa-pen-to-square text-[#d6b15c]"></i> Edit Profile</h3>
                        <form action="{{ route('admin.profile_update') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <p class="form-fieldset-title"><i class="fa-regular fa-user"></i> Personal Information</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="form-label">First Name</label><input type="text" name="admin_first_name" value="{{ old('admin_first_name', $admin->admin_first_name ?? '') }}" class="form-input"></div>
                                    <div><label class="form-label">Last Name</label><input type="text" name="admin_last_name" value="{{ old('admin_last_name', $admin->admin_last_name ?? '') }}" class="form-input"></div>
                                    <div><label class="form-label">Middle Name</label><input type="text" name="admin_middle_name" value="{{ old('admin_middle_name', $admin->admin_middle_name ?? '') }}" class="form-input"></div>
                                    <div><label class="form-label">Administrator ID</label><input type="text" disabled value="{{ $admin->user_id ?? $user->user_id ?? '' }}" class="form-input opacity-70 cursor-not-allowed"></div>
                                </div>
                            </div>
                            <div>
                                <p class="form-fieldset-title"><i class="fa-regular fa-address-card"></i> Contact Details</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="form-label">Contact Number</label><input type="text" name="contact_number" value="{{ old('contact_number', $admin->contact_number ?? '') }}" class="form-input" placeholder="e.g. 09XX XXX XXXX"></div>
                                    <div><label class="form-label">Email</label><input type="email" name="admin_email" value="{{ old('admin_email', $admin->admin_email ?? $user->email ?? '') }}" class="form-input"></div>
                                </div>
                            </div>
                            <div class="flex justify-end pt-2 border-t border-[#e2dacf]"><button type="submit" class="btn-primary"><i class="fa-regular fa-floppy-disk mr-2"></i>Save Changes</button></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="content-card lg:col-span-2">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <h3 class="mb-5 flex items-center gap-2"><i class="fa-solid fa-key text-[#d6b15c]"></i> Change Password</h3>
                        <form action="{{ route('admin.profile.update_password') }}" method="POST" class="space-y-4" id="passwordForm">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2"><label class="form-label">Username</label><input type="text" value="{{ $user->name ?? '' }}" class="form-input bg-[#e8e3d7] cursor-not-allowed" disabled></div>
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

        <!-- ==================== CAPSTONES MANAGEMENT ==================== -->
        <div id="capstone-section" class="section-container hidden section-card max-w-7xl mx-auto">
            <div class="mb-8 p-6 bg-[#faf8f4] border border-[#e2dacf] rounded-2xl shadow-sm relative overflow-hidden w-full">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-[#d6b15c]/5 to-transparent pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 w-full">
                    <div class="flex-1">
                        <h1 class="text-[#0a1428] text-3xl font-bold tracking-tight" style="font-family:'Cormorant Garamond', serif; font-size: 2.2rem; line-height: 1;">Capstone Years</h1>
                        <div class="w-12 h-1 bg-[#d6b15c] my-2"></div>
                        <p class="text-xs text-[#5b6375]">Manage academic years, capstone stages, and archived capstone records.</p>
                    </div>
                    <button type="button" onclick="openModal('add_year_modal')" class="btn-primary text-xs py-2.5 px-4 rounded-lg bg-[#0a1428] hover:bg-[#12254d] text-white font-bold flex items-center gap-1.5 border-none shadow-md">
                        <i class="fas fa-plus"></i> Add Capstone Year
                    </button>
                </div>
            </div>
            <div class="content-card w-full mb-8">
                <div class="card-accent"></div>
                <div class="p-6">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-[#0a1428]" style="font-family:'Cormorant Garamond',serif; font-size:1.3rem;">
                        <i class="fa-solid fa-calendar-days text-[#d6b15c]"></i> Academic & Capstone Years
                    </h3>
                    <p class="text-xs text-[#5b6375] mb-6">Overview of all registered academic years. Only one capstone year can be active. Activating a year automatically archives the current active year and restores historical records belonging to the selected year.</p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                                    <th class="p-3 pl-4">Capstone Year</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Capstone 1</th>
                                    <th class="p-3">Capstone 2</th>
                                    <th class="p-3 text-center">Groups</th>
                                    <th class="p-3 text-center">Students</th>
                                    <th class="p-3 pr-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#faf1e0] text-sm text-[#171e2c]">
                                @forelse($capstoneYears as $yr)
                                <tr class="hover:bg-[#faf8f4]/50 transition">
                                    <td class="p-3 pl-4 font-bold text-[#0a1428]">{{ $yr->year }}</td>
                                    <td class="p-3">
                                        @if($yr->is_active)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-green-50 text-green-700 border border-yellow-200">Active</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-gray-50 text-gray-700 border border-gray-200">Archived</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if($yr->capstone_1_enabled)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-sky-50 text-sky-700 border border-green-200">Enabled</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if($yr->capstone_2_enabled)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-sky-50 text-sky-700 border border-green-200">Enabled</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center font-semibold text-[#0a1428]">{{ $yr->groups_count }}</td>
                                    <td class="p-3 text-center font-semibold text-[#0a1428]">{{ $yr->students_count }}</td>
                                    <td class="p-3 pr-4 text-right">
                                        <div class="flex gap-2 justify-end">
                                            <button type="button" onclick="openEditYearModal({{ $yr->id }}, '{{ $yr->year }}', {{ $yr->capstone_1_enabled ? 1 : 0 }}, {{ $yr->capstone_2_enabled ? 1 : 0 }})" class="px-2.5 py-1 text-xs font-semibold rounded bg-gray-100 hover:bg-gray-200 text-[#0a1428] transition" title="Edit Year Config">
                                                <i class="fas fa-pen"></i> Edit
                                            </button>
                                            @if($yr->is_active)
                                                <button type="button" onclick="confirmArchiveYear({{ $yr->id }}, '{{ $yr->year }}')" class="px-2.5 py-1 text-xs font-semibold rounded bg-amber-500 text-black hover:bg-amber-600 transition" title="Archive Year">
                                                    <i class="fas fa-box-archive"></i> Archive
                                                </button>
                                            @else
                                                <button type="button" onclick="confirmActivateYear({{ $yr->id }}, '{{ $yr->year }}')" class="px-2.5 py-1 text-xs font-semibold rounded bg-green-600 text-black hover:bg-green-700 transition" title="Activate Year">
                                                    <i class="fas fa-check-circle"></i> Activate
                                                </button>
                                            @endif
                                            <button type="button" onclick="openDeleteYearModal({{ $yr->id }}, '{{ addslashes($yr->year) }}')" class="px-2.5 py-1 text-xs font-semibold rounded bg-red-600 text-black hover:bg-red-700 transition" title="Delete Year">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No capstone years configured.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <!-- ==================== ACTIVE STAGES CONFIGURATION ==================== -->
                <div class="content-card">
                    <div class="card-accent"></div>
                    <div class="p-6">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-[#0a1428]" style="font-family:'Cormorant Garamond',serif; font-size:1.3rem;">
                            <i class="fa-solid fa-sliders text-[#d6b15c]"></i> Active Stages Configuration
                        </h3>
                        <p class="text-xs text-[#5b6375] mb-6">Enabling/disabling a stage will automatically hide or show its corresponding milestones, rubrics, and progress stats across all dashboards.</p>
                        
                        <div class="space-y-4" id="active-stages-list">
                            @foreach($capstoneStages as $stage)
                            <div class="flex items-center justify-between p-4 bg-[#faf8f4] border border-[#e2dacf] rounded-xl hover:border-[#d6b15c] transition">
                                <div class="flex-1 min-w-0 pr-4">
                                    <h4 class="font-bold text-[#0a1428] truncate">{{ $stage->stage_title }}</h4>
                                    <div class="flex flex-wrap gap-2 items-center mt-1">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider {{ $stage->stage_type == 1 ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            Capstone {{ $stage->stage_type }}
                                        </span>
                                        <p class="text-xs text-[#5b6375]">
                                            @if($stage->is_enabled)
                                                <span class="text-green-600 font-semibold uppercase tracking-wider text-[10px]">Active / Enabled</span>
                                            @else
                                                <span class="text-red-500 font-semibold uppercase tracking-wider text-[10px]">Disabled</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <form action="{{ route('admin.toggle_capstone_stage') }}" method="POST" class="inline toggle-stage-form">
                                        @csrf
                                        <input type="hidden" name="stage_id" value="{{ $stage->id }}">
                                        <label class="switch" title="{{ $stage->is_enabled ? 'Disable' : 'Enable' }} {{ $stage->stage_title }}">
                                            <input type="checkbox" {{ $stage->is_enabled ? 'checked' : '' }} onchange="this.form.submit()">
                                            <div class="slider">
                                                <div class="circle">
                                                    <svg class="cross" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                                        <g><path data-original="#000000" fill="currentColor" d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0"></path></g>
                                                    </svg>
                                                    <svg class="checkmark" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 24 24" y="0" x="0" height="10" width="10" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                                        <g><path data-original="#000000" fill="currentColor" d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z"></path></g>
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>
                                    </form>
                                    <button type="button" onclick="openEditStageModal({{ $stage->id }}, '{{ addslashes($stage->stage_title) }}', {{ $stage->stage_type }})" class="p-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-[#0a1428] transition" title="Edit Stage Title">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- ======================= MODALS ======================= -->
<div id="delete_group_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent" style="background: #a12b2b;"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Confirm Deletion</h2>
            <button type="button" onclick="closeModal('delete_group_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.delete_group') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="confirm_delete_group" value="1">
            <input type="hidden" name="group_id" id="delete_group_id">
            <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded-xl text-xs">
                <span class="font-bold"><i class="fas fa-exclamation-triangle"></i> This will permanently delete this group</span>
                <p class="mt-1">You are about to delete <strong id="delete_group_name" class="text-red-950"></strong> and all its associated members, milestones, evaluations, remarks, and certificates. This cannot be undone.</p>
            </div>
            <div>
                <label class="form-label">Confirm Your Admin Password</label>
                <div class="relative">
                    <input type="password" name="admin_password" id="delete_group_admin_password" class="form-input pr-10" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" onclick="toggleVisibility('delete_group_admin_password', this)"><i class="fa-regular fa-eye"></i></button>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('delete_group_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="background:#a12b2b;"><i class="fas fa-trash mr-1"></i> Delete Group</button>
            </div>
        </form>
    </div>
</div>

<div id="assign_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Change Adviser</h2>
            <button type="button" onclick="closeModal('assign_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.assign_group') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Section</label>
                <select id="ca_section_select" class="form-select" required>
                    <option value="" disabled selected>Select section</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->section_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Group</label>
                <select name="group_id" id="ca_group_select" class="form-select" required disabled>
                    <option value="" disabled selected>Select a section first</option>
                </select>
            </div>
            <div>
                <label class="form-label">Current Adviser</label>
                <input type="text" id="ca_current_adviser" class="form-input opacity-70" readonly placeholder="—">
            </div>
            <div>
                <label class="form-label">New Adviser</label>
                <select name="adviser_id" id="ca_adviser_select" class="form-select" required disabled>
                    <option value="" disabled selected>Select a group first</option>
                    @foreach($allTeachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->teacher_first_name }} {{ $teacher->teacher_last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('assign_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Change Adviser</button>
            </div>
        </form>
    </div>
</div>

<div id="assign_section_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Assign a Section</h2>
            <button type="button" onclick="closeModal('assign_section_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.assign_section') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Section</label>
                <select name="section_id" id="as_section_select" class="form-select" required onchange="updateCurrentSectionTeacher()">
                    <option value="" disabled selected>Select section</option>
                    @foreach($sections as $sec)
                    <option value="{{ $sec->id }}" data-teacher-user-id="{{ $sec->user_id }}">{{ $sec->section_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Current Teacher</label>
                <input type="text" id="as_current_teacher" class="form-input opacity-70" readonly placeholder="No Teacher Assigned">
            </div>
            <div>
                <label class="form-label">Assign to Teacher</label>
                <select name="teacher_user_id" id="as_teacher_select" class="form-select" required>
                    <option value="none">Unassigned (None)</option>
                    @foreach($allTeachers as $teacher)
                    <option value="{{ $teacher->user_id }}">{{ $teacher->teacher_first_name }} {{ $teacher->teacher_last_name }} ({{ $teacher->user_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('assign_section_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Assign Section</button>
            </div>
        </form>
    </div>
</div>

    <div id="teacher_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Add Teacher</h2>
                <button type="button" onclick="closeModal('teacher_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.add_teacher') }}" method="POST" class="space-y-3">
                @csrf
                <div><label class="form-label">Teacher ID</label><input type="text" name="teacher_id" class="form-input" placeholder="e.g. PJR-2026" required></div>
                <div class="grid grid-cols-3 gap-2">
                    <div><label class="form-label">First Name</label><input type="text" name="teacher_first_name" class="form-input" required></div>
                    <div><label class="form-label">Middle Name</label><input type="text" name="teacher_middle_name" class="form-input"></div>
                    <div><label class="form-label">Last Name</label><input type="text" name="teacher_last_name" class="form-input" required></div>
                </div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('teacher_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Save Teacher</button></div>
            </form>
        </div>
    </div>

    <div id="teacher_edit_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Teacher</h2>
                <button type="button" onclick="closeModal('teacher_edit_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.edit_teacher') }}" method="POST" class="space-y-3" id="teacher_edit_form">
                @csrf
                <input type="hidden" name="original_teacher_id" id="edit_original_teacher_id">
                <div><label class="form-label">Teacher ID</label><input type="text" name="teacher_id" id="edit_teacher_id" class="form-input opacity-70" readonly></div>
                <div class="grid grid-cols-3 gap-2">
                    <div><label class="form-label">First Name</label><input type="text" name="teacher_first_name" id="edit_teacher_first_name" class="form-input" required></div>
                    <div><label class="form-label">Middle Name</label><input type="text" name="teacher_middle_name" id="edit_teacher_middle_name" class="form-input"></div>
                    <div><label class="form-label">Last Name</label><input type="text" name="teacher_last_name" id="edit_teacher_last_name" class="form-input" required></div>
                </div>
                <div><label class="form-label">Email</label><input type="email" name="teacher_email" id="edit_teacher_email" class="form-input" required></div>
                <div><label class="form-label">Contact Number</label><input type="text" name="contact_number" id="edit_teacher_contact" class="form-input" pattern="09[0-9]{9}" maxlength="11" minlength="11"></div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('teacher_edit_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Save Teacher</button></div>
            </form>
        </div>
    </div>

    <div id="delete_teacher_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Confirm Deletion</h2>
                <button type="button" onclick="closeModal('delete_teacher_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.delete_teacher') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="confirm_delete_teacher" value="1">
                <input type="hidden" name="teacher_id" id="delete_teacher_id">
                <p class="text-sm text-[#5b6375]">You are about to permanently delete <strong id="delete_teacher_name" class="text-[#171e2c]"></strong>. This cannot be undone.</p>
                <div>
                    <label class="form-label">Confirm Your Admin Password</label>
                    <div class="relative">
                        <input type="password" name="admin_password" id="delete_teacher_admin_password" class="form-input pr-10" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" onclick="toggleVisibility('delete_teacher_admin_password', this)"><i class="fa-regular fa-eye"></i></button>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('delete_teacher_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary" style="background:#a12b2b;"><i class="fas fa-trash mr-1"></i> Delete Teacher</button></div>
            </form>
        </div>
    </div>

    <div id="student_edit_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Student</h2>
                <button type="button" onclick="closeModal('student_edit_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.edit_student') }}" method="POST" class="space-y-3" id="student_edit_form">
                @csrf
                <input type="hidden" name="original_student_id" id="edit_original_student_id">
                <div><label class="form-label">Student ID</label><input type="text" name="student_id" id="edit_student_id" class="form-input" readonly></div>
                <div class="grid grid-cols-3 gap-2">
                    <div><label class="form-label">First Name</label><input type="text" name="student_first_name" id="edit_first_name" class="form-input" required></div>
                    <div><label class="form-label">Middle Name</label><input type="text" name="student_middle_name" id="edit_middle_name" class="form-input"></div>
                    <div><label class="form-label">Last Name</label><input type="text" name="student_last_name" id="edit_last_name" class="form-input" required></div>
                </div>
                <div><label class="form-label">Email</label><input type="email" name="student_email" id="edit_email" class="form-input" required></div>
                <div><label class="form-label">Contact Number</label><input type="text" name="contact_number" id="edit_contact" class="form-input" pattern="09[0-9]{9}" maxlength="11" minlength="11" required></div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="form-label">Course</label><input type="text" name="course" value="BSIT" class="form-input opacity-70" readonly></div>
                    <div><label class="form-label">Section</label><select name="section" id="edit_section" class="form-select" required><option value="East">East</option><option value="West">West</option><option value="North">North</option><option value="South">South</option><option value="SouthEast">SouthEast</option><option value="SouthWest">SouthWest</option><option value="NorthEast">NorthEast</option><option value="NorthWest">NorthWest</option></select></div>
                </div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('student_edit_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Save Student</button></div>
            </form>
        </div>
    </div>

    <div id="delete_student_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Confirm Deletion</h2>
                <button type="button" onclick="closeModal('delete_student_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.delete_student') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="confirm_delete" value="1">
                <input type="hidden" name="student_id" id="delete_student_id">
                <p class="text-sm text-[#5b6375]">You are about to permanently delete <strong id="delete_student_name" class="text-[#171e2c]"></strong>. This cannot be undone.</p>
                <div>
                    <label class="form-label">Confirm Your Admin Password</label>
                    <div class="relative">
                        <input type="password" name="admin_password" id="delete_admin_password" class="form-input pr-10" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" onclick="toggleVisibility('delete_admin_password', this)"><i class="fa-regular fa-eye"></i></button>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('delete_student_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary" style="background:#a12b2b;"><i class="fas fa-trash mr-1"></i> Delete Student</button></div>
            </form>
        </div>
    </div>

    <div id="delete_room_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Confirm Deletion</h2>
                <button type="button" onclick="closeModal('delete_room_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.delete_room') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="confirm_delete_room" value="1">
                <input type="hidden" name="room_id" id="delete_room_id">
                <p class="text-sm text-[#5b6375]">You are about to permanently delete <strong id="delete_room_name" class="text-[#171e2c]"></strong>. This cannot be undone.</p>
                <div>
                    <label class="form-label">Confirm Your Admin Password</label>
                    <div class="relative">
                        <input type="password" name="admin_password" id="delete_room_admin_password" class="form-input pr-10" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" onclick="toggleVisibility('delete_room_admin_password', this)"><i class="fa-regular fa-eye"></i></button>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('delete_room_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary" style="background:#a12b2b;"><i class="fas fa-trash mr-1"></i> Delete Room</button></div>
            </form>
        </div>
    </div>

    <div id="student_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Register Student</h2>
                <button type="button" onclick="closeModal('student_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.add_student') }}" method="POST" class="space-y-3">
                @csrf
                <div><label class="form-label">Student ID</label><input type="text" name="student_id" class="form-input" placeholder="e.g. 2021-1234" required></div>
                <div class="grid grid-cols-3 gap-2">
                    <div><label class="form-label">First Name</label><input type="text" name="student_first_name" class="form-input" required></div>
                    <div><label class="form-label">Middle Name</label><input type="text" name="student_middle_name" class="form-input"></div>
                    <div><label class="form-label">Last Name</label><input type="text" name="student_last_name" class="form-input" required></div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="form-label">Course</label><input type="text" name="course" value="BSIT" class="form-input opacity-70" readonly></div>
                    <div><label class="form-label">Section</label><select name="section" class="form-select" required><option value="East">East</option><option value="West">West</option><option value="North">North</option><option value="South">South</option><option value="SouthEast">SouthEast</option><option value="SouthWest">SouthWest</option><option value="NorthEast">NorthEast</option><option value="NorthWest">NorthWest</option></select></div>
                </div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('student_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Save Student</button></div>
            </form>
        </div>
    </div>

    <div id="milestone_modal" class="modal-overlay">
        <div class="modal-box wide">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Add Milestone</h2>
                <button type="button" onclick="closeModal('milestone_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.add_milestone') }}" method="POST" class="space-y-3" onsubmit="return validateMilestoneForm()">
                @csrf
                <div><label class="form-label">Milestone Name</label><input type="text" name="milestone_title" class="form-input" placeholder="e.g. title hearing" required></div>
                <div><label class="form-label">Capstone Stage</label><select name="capstone_stage" class="form-select" required>
                    <option value="" disabled selected>Select a stage</option>
                    @foreach($capstoneStages->where('is_enabled', true) as $stageS)<option value="{{ $stageS->id }}">{{ $stageS->stage_title }}</option>@endforeach</select></div>
                <div>
                    <label class="form-label">Order Position</label>
                    @php $nextOrder = ($milestones->max('step_order') ?? 0) + 1; @endphp
                    <input type="number" name="order" value="{{ $nextOrder }}" class="form-input opacity-70" readonly>
                    <p class="text-xs text-[#9a9385] mt-1">This position is automatically calculated as the next step order.</p>
                </div>
                <div><label class="form-label">Description <span class="text-[#9a9385]">(optional)</span></label><textarea name="description" rows="3" class="form-input resize-none" placeholder="What students need to accomplish..."></textarea></div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-input" style="color-scheme:light;" required></div>
                    <div><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-input" style="color-scheme:light;" required></div>
                </div>

                <div class="mt-4 border-t border-[#e2dacf] pt-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="add_rubric" id="add_rubric_chk" class="form-checkbox rounded text-[#d6b15c] focus:ring-[#d6b15c]" onchange="toggleRubricSection(this.checked)">
                        <span class="text-sm font-semibold text-[#0a1428]">Associate an Evaluation Rubric with this Milestone (Optional)</span>
                    </label>
                </div>

                <div id="milestone_rubric_section" class="hidden space-y-4 mt-4">
                    <div>
                        <label class="form-label">Rubric Name</label>
                        <input type="text" name="rubric_name" id="milestone_rubric_name" class="form-input" placeholder="e.g. Milestone Evaluation Rubric">
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="form-label !mb-0">Criteria <span class="text-[#9a9385] font-normal normal-case">(weights must total 100%)</span></label>
                            <button type="button" onclick="addCriteriaRow('milestone-criteria-list')" class="text-xs text-[#d6b15c] hover:text-[#b88d3a] transition font-medium"><i class="fas fa-plus mr-1"></i>Add criteria</button>
                        </div>
                        <div class="grid grid-cols-12 gap-2 mb-1 text-[11px] text-[#5b6375]"><span class="col-span-6">Criteria</span><span class="col-span-2">Weight %</span><span class="col-span-3">Max score</span><span class="col-span-1"></span></div>
                        <div id="milestone-criteria-list" class="space-y-2"></div>
                        <div id="milestone-error-message" class="text-red-500 text-xs mt-1 hidden"></div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('milestone_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Save Milestone</button></div>
            </form>
        </div>
    </div>
    <div id="edit_milestone_modal" class="modal-overlay">
        <div class="modal-box wide">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Milestone</h2>
                <button type="button" onclick="closeModal('edit_milestone_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <div id="edit_milestone_errors" class="hidden mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"></div>
            <form id="edit_milestone_form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="milestone_id" id="edit_milestone_id">

                <div>
                    <label class="form-label">Milestone Name</label>
                    <input type="text" name="milestone_title" id="edit_milestone_title" class="form-input" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Capstone Stage</label>
                        <select name="capstone_stage" id="edit_milestone_stage" class="form-select" required>
                            @foreach($capstoneStages->where('is_enabled', true) as $stageS)
                            <option value="{{ $stageS->id }}">{{ $stageS->stage_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Order Position</label>
                        <input type="number" name="order" id="edit_milestone_order" min="1" class="form-input" required>
                    </div>
                </div>

                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit_milestone_description" rows="3" class="form-input resize-none" required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="edit_milestone_start" class="form-input" style="color-scheme:light;" required>
                    </div>
                    <div>
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" id="edit_milestone_due" class="form-input" style="color-scheme:light;" required>
                    </div>
                </div>

                <div class="border-t border-dashed border-[#e2dacf] pt-4">
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input type="checkbox" name="has_certificate" id="edit_milestone_has_cert" value="1"
                            class="form-checkbox text-[#d6b15c] focus:ring-[#d6b15c]">
                        <span class="text-sm font-medium text-[#0a1428]">Add a Document when this milestone is completed</span>
                    </label>
                    <p class="text-xs text-[#9a9385] mb-3">
                        The moment a group finishes this milestone (rubric evaluation or remark evaluation), this document is added to the group automatically — no extra step needed.
                    </p>

                    <div id="edit_milestone_cert_fields" class="space-y-3 hidden">
                        <div>
                            <label class="form-label">Document Type</label>
                            <select name="document_type" id="edit_document_type" class="form-select" onchange="syncDocTitle(this)">
                                <option value="">Select document type</option>
                                <option value="recommendation">Recommendation Sheet</option>
                                <option value="approval">Approval Sheet</option>
                            </select>
                            <input type="hidden" name="certificate_title" id="edit_certificate_title">
                        </div>
                        <div>
                            <label class="form-label">Document Description</label>
                            <textarea name="certificate_description" id="edit_certificate_description" rows="2" class="form-input resize-none" placeholder="Awarded for successfully completing..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4 border-t border-[#e2dacf] pt-4">
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input type="checkbox" name="add_rubric" id="edit_add_rubric_chk" class="form-checkbox rounded text-[#d6b15c] focus:ring-[#d6b15c]">
                        <span class="text-sm font-semibold text-[#0a1428]">Associate an Evaluation Rubric with this Milestone (Optional)</span>
                    </label>
                </div>

                <div id="edit_milestone_rubric_section" class="hidden space-y-4 mt-4">
                    <div>
                        <label class="form-label">Rubric Name</label>
                        <input type="text" name="rubric_name" id="edit_milestone_rubric_name" class="form-input" placeholder="e.g. Milestone Evaluation Rubric">
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="form-label !mb-0">Criteria <span class="text-[#9a9385] font-normal normal-case">(weights must total 100%)</span></label>
                            <button type="button" onclick="addCriteriaRow('edit-milestone-criteria-list')" class="text-xs text-[#d6b15c] hover:text-[#b88d3a] transition font-medium"><i class="fas fa-plus mr-1"></i>Add criteria</button>
                        </div>
                        <div class="grid grid-cols-12 gap-2 mb-1 text-[11px] text-[#5b6375]"><span class="col-span-6">Criteria</span><span class="col-span-2">Weight %</span><span class="col-span-3">Max score</span><span class="col-span-1"></span></div>
                        <div id="edit-milestone-criteria-list" class="space-y-2"></div>
                        <div id="edit-milestone-error-message" class="text-red-500 text-xs mt-1 hidden"></div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('edit_milestone_modal')" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary">Save Milestone</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rubrics_edit_modal" class="modal-overlay">
        <div class="modal-box wide">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Rubric</h2>
                <button type="button" onclick="closeModal('rubrics_edit_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <div id="edit_rubric_errors" class="hidden mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"></div>
            <form id="edit_rubric_form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="rubric_id" id="edit_rubric_id">
                <div><label class="form-label">Rubric Name</label><input type="text" name="rubric_name" id="edit_rubric_name" class="form-input" required></div>
                <div><label class="form-label">Capstone Stage</label><select name="capstone_id" id="edit_capstone_id" class="form-select" required><option value="" disabled>Select capstone stage</option>@foreach($capstoneStages->where('is_enabled', true) as $stage)<option value="{{ $stage->id }}">{{ $stage->stage_title }}</option>@endforeach</select></div>
                <div>
                    <label class="form-label">Milestone</label>
                    <select name="milestone_id" class="form-select" id="edit_milestone_select">
                        <option value="">None (Optional)</option>
                        @foreach($milestones as $milestone)
                        <option value="{{ $milestone->id }}" data-capstone-stage-id="{{ $milestone->capstone_stage_id }}">{{ $milestone->milestone_title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <label class="form-label !mb-0">Criteria <span class="text-[#9a9385] font-normal normal-case">(weights must total 100%)</span></label>
                        <button type="button" onclick="addCriteriaRow('edit_criteria_list')" class="text-xs text-[#d6b15c] hover:text-[#b88d3a] transition font-medium"><i class="fas fa-plus mr-1"></i>Add criteria</button>
                    </div>
                    <div class="grid grid-cols-12 gap-2 mb-1 text-[11px] text-[#5b6375]"><span class="col-span-6">Criteria</span><span class="col-span-2">Weight %</span><span class="col-span-3">Max score</span><span class="col-span-1"></span></div>
                    <div id="edit_criteria_list" class="space-y-2"></div>
                    <div id="edit_error_message" class="text-red-500 text-xs mt-1 hidden"></div>
                </div>
                <div class="flex justify-end gap-2 pt-2"><button type="button" onclick="closeModal('rubrics_edit_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Update Rubric</button></div>
            </form>
        </div>
    </div>

    <div id="delete_rubric_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Confirm Deletion</h2>
                <button type="button" onclick="closeModal('delete_rubric_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.delete_rubrics') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="confirm_delete" value="1">
                <input type="hidden" name="rubric_id" id="delete_rubric_id">
                <p class="text-sm text-[#5b6375]">You are about to permanently delete <strong id="delete_rubric_name" class="text-[#171e2c]"></strong>. This cannot be undone.</p>
                <div>
                    <label class="form-label">Confirm Your Admin Password</label>
                    <div class="relative">
                        <input type="password" name="admin_password" id="delete_rubric_admin_password" class="form-input pr-10" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" onclick="toggleVisibility('delete_rubric_admin_password', this)"><i class="fa-regular fa-eye"></i></button>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeModal('delete_rubric_modal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary" style="background:#a12b2b;"><i class="fas fa-trash mr-1"></i> Delete Rubric</button></div>
            </form>
        </div>
    </div>

    <div id="edit_stage_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Capstone Stage</h2>
                <button type="button" onclick="closeModal('edit_stage_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form id="edit_stage_form" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label text-xs font-semibold uppercase text-[#5b6375]">Stage Title</label>
                    <input type="text" name="stage_title" id="edit_stage_title" class="form-input mt-1" required>
                </div>
                <div>
                    <label class="form-label text-xs font-semibold uppercase text-[#5b6375]">Stage Type</label>
                    <select name="stage_type" id="edit_stage_type" class="form-select mt-1" required>
                        <option value="1">Capstone 1</option>
                        <option value="2">Capstone 2</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closeModal('edit_stage_modal')" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="add_year_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Add Capstone Year</h2>
                <button type="button" onclick="closeModal('add_year_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.add_capstone_year') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label text-xs font-semibold uppercase text-[#5b6375]">Academic / Capstone Year</label>
                    <input type="text" name="year" class="form-input mt-1" placeholder="e.g. 2025–2026" required>
                </div>
                <div class="space-y-2">
                    <label class="form-label text-xs font-semibold uppercase text-[#5b6375] block">Enable Capstone Stages</label>
                    <label class="inline-flex items-center gap-2 cursor-pointer mr-4">
                        <input type="checkbox" name="capstone_1_enabled" id="add_year_c1" value="1" checked class="rounded text-green-600 focus:ring-green-500 border-gray-300">
                        <span class="text-xs text-[#5b6375] font-semibold">Capstone 1</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="capstone_2_enabled" id="add_year_c2" value="1" class="rounded text-green-600 focus:ring-green-500 border-gray-300">
                        <span class="text-xs text-[#5b6375] font-semibold">Capstone 2</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="1" class="rounded text-green-600 focus:ring-green-500 border-gray-300" checked>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closeModal('add_year_modal')" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary">Add Capstone Year</button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit_year_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Capstone Year</h2>
                <button type="button" onclick="closeModal('edit_year_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form id="edit_year_form" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label text-xs font-semibold uppercase text-[#5b6375]">Academic / Capstone Year</label>
                    <input type="text" name="year" id="edit_year_title" class="form-input mt-1" required>
                </div>
                <div class="space-y-2">
                    <label class="form-label text-xs font-semibold uppercase text-[#5b6375] block">Enable Capstone Stages</label>
                    <label class="inline-flex items-center gap-2 cursor-pointer mr-4">
                        <input type="checkbox" name="capstone_1_enabled" id="edit_year_c1" value="1" class="rounded text-green-600 focus:ring-green-500 border-gray-300">
                        <span class="text-xs text-[#5b6375] font-semibold">Capstone 1</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="capstone_2_enabled" id="edit_year_c2" value="1" class="rounded text-green-600 focus:ring-green-500 border-gray-300">
                        <span class="text-xs text-[#5b6375] font-semibold">Capstone 2</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closeModal('edit_year_modal')" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="activate_year_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent" style="background: var(--gold);"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);" id="activate_year_title">Activate Capstone Year?</h2>
                <button type="button" onclick="closeModal('activate_year_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form id="activate_year_form" action="" method="POST" class="space-y-4">
                @csrf
                <p class="text-sm text-[#5b6375]">
                    Activating this capstone year will archive the currently active capstone year and restore the groups and students associated with <strong id="activate_year_name" class="text-[#0a1428]"></strong>.
                </p>
                <p class="text-sm text-[#5b6375] font-semibold">Continue?</p>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closeModal('activate_year_modal')" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700">Yes, Activate</button>
                </div>
            </form>
        </div>
    </div>

    <div id="archive_year_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent" style="background: #d97706;"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);" id="archive_year_title_lbl">Archive Capstone Year?</h2>
                <button type="button" onclick="closeModal('archive_year_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form id="archive_year_form" action="" method="POST" class="space-y-4">
                @csrf
                <p class="text-sm text-[#5b6375]">
                    This will remove <strong id="archive_year_name" class="text-[#0a1428]"></strong> from the active capstone system, but all students, groups, milestones, evaluations, rooms, and historical records will remain in the system.
                </p>
                <p class="text-sm text-[#5b6375] font-semibold">Continue?</p>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closeModal('archive_year_modal')" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary bg-amber-500 hover:bg-amber-600">Yes, Archive</button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete_year_modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-accent" style="background: #dc2626;"></div>
            <div class="flex justify-between items-center mb-4">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Confirm Year Deletion</h2>
                <button type="button" onclick="closeModal('delete_year_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <form id="delete_year_form" action="" method="POST" class="space-y-4">
                @csrf
                <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded-xl text-xs">
                    <span class="font-bold"><i class="fas fa-exclamation-triangle"></i> DANGER: Permanent Cascading Deletion</span>
                    <p class="mt-1">This will permanently delete the capstone year <strong id="delete_year_name" class="text-red-950"></strong> and ALL of its associated student profiles, user accounts, groups, milestones, evaluations, and progress records. This action is irreversible.</p>
                </div>
                <div>
                    <label class="form-label text-xs font-semibold uppercase text-[#5b6375]">Confirm Your Admin Password</label>
                    <div class="relative mt-1">
                        <input type="password" name="admin_password" id="delete_year_admin_password" class="form-input pr-10" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" onclick="toggleVisibility('delete_year_admin_password', this)"><i class="fa-regular fa-eye"></i></button>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closeModal('delete_year_modal')" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700">Yes, Permanently Delete</button>
                </div>
            </form>
        </div>
    </div>

<div id="rearrange_milestones_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Rearrange Milestone Step Order</h2>
            <button type="button" onclick="closeModal('rearrange_milestones_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <p class="text-xs text-[#5b6375] mb-4">Select a capstone stage, then reorder its milestones.</p>

        <div class="flex gap-1.5 bg-[#f5f1e8] p-1 rounded-full w-fit mb-4">
            <button type="button" class="reorder-stage-tab-btn stage-tab-btn text-xs font-medium py-1.5 px-4 rounded-full transition-smooth active" data-stage="1">Capstone 1</button>
            <button type="button" class="reorder-stage-tab-btn stage-tab-btn text-xs font-medium py-1.5 px-4 rounded-full transition-smooth" data-stage="2">Capstone 2</button>
        </div>

        @foreach($capstoneStages->groupBy('stage_type') as $stageType => $stagesOfType)
        @php $stageIdsOfType = $stagesOfType->pluck('id'); @endphp
        <form action="{{ route('admin.reorder_milestones') }}" method="POST"
              class="reorder-stage-form space-y-4"
              data-stage="{{ $stageType }}"
              style="{{ $stageType != 1 ? 'display:none;' : '' }}">
            @csrf
            <div class="reorder_list space-y-2 max-h-80 overflow-y-auto pr-1">
                @forelse($milestones->whereIn('capstone_stage_id', $stageIdsOfType)->sortBy('step_order') as $m)
                <div class="reorder-item flex items-center justify-between p-3 bg-[#faf8f4] border border-[#e2dacf] rounded-lg" data-id="{{ $m->id }}">
                    <input type="hidden" name="milestone_ids[]" value="{{ $m->id }}">
                    <span class="text-sm font-semibold text-[#0a1428]">{{ $m->milestone_title }}</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="moveItemUp(this)" class="p-1 text-[#5b6375] hover:text-[#0a1428] transition" title="Move Up"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" onclick="moveItemDown(this)" class="p-1 text-[#5b6375] hover:text-[#0a1428] transition" title="Move Down"><i class="fas fa-chevron-down"></i></button>
                    </div>
                </div>
                @empty
                <p class="text-[#5b6375] text-sm text-center py-6">No milestones in this stage.</p>
                @endforelse
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('rearrange_milestones_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Save Step Order</button>
            </div>
        </form>
        @endforeach
    </div>
</div>

<div id="evaluation_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Create Evaluation Rooms</h2>
            <button type="button" onclick="closeModal('evaluation_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.create_room') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Number of Rooms to Create</label>
                <input type="number" name="room_count" class="form-input" min="1" value="1" required>
                <p class="text-xs text-[#9a9385] mt-1">All qualified groups in the system will be divided evenly among the newly created rooms/classrooms.</p>
            </div>
            <div>
                <label class="form-label">Capstone Stage <span class="text-red-500">*</span></label>
                <select id="create_room_capstone_stage" class="form-select" onchange="filterMilestonesByStage(this.value)" required>
                    <option value="" selected disabled>Select Capstone Stage</option>
                    @foreach($capstoneStages->where('is_enabled', true) as $stage)
                    <option value="{{ $stage->id }}">{{ $stage->stage_title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Participate Activity (Milestone with Rubrics) <span class="text-red-500">*</span></label>
                <select id="create_room_participate_activity" name="required_milestone_id" class="form-select" required disabled>
                    <option value="" disabled selected>Please select Capstone Stage first</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('evaluation_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Create Rooms</button>
            </div>
        </form>
    </div>
</div>
<div id="edit_room_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 id="edit_room_title" style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Room</h2>
            <button type="button" onclick="closeModal('edit_room_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="form-label">Add Panelist <span class="text-[#9a9385] font-normal normal-case">(Ctrl to select multiple)</span></label>
                <div class="flex gap-2 items-start">
                    <select id="edit_room_teacher_select" class="form-select flex-1" multiple style="height:120px;">
                        @foreach($allTeachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->teacher_first_name }} {{ $teacher->teacher_last_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="edit_room_add_btn" class="btn-primary whitespace-nowrap"><i class="fas fa-plus mr-1"></i> Add</button>
                </div>
            </div>
            <div>
                <label class="form-label">Current Panelists</label>
                <div id="edit_room_panelists_container" class="space-y-2 max-h-56 overflow-y-auto"></div>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('edit_room_modal')" class="btn-ghost">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="import_student_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Import Students</h2>
            <button type="button" onclick="closeModal('import_student_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.import_students') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Excel File (.xlsx, .csv)</label>
                <input type="file" name="file" accept=".xlsx,.csv" class="form-input" required>
                <p class="text-xs text-[#9a9385] mt-1">
                    Columns: student_id, student_first_name, student_middle_name, student_last_name, course, section
                </p>
            </div>
            <a href="{{ route('admin.download_student_template') }}" class="text-xs text-[#d6b15c] hover:text-[#b88d3a] font-medium inline-flex items-center gap-1">
                <i class="fas fa-download"></i> Download template
            </a>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('import_student_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary"><i class="fas fa-file-import mr-1"></i> Import Students</button>
            </div>
        </form>
    </div>
</div>
<div id="edit_group_modal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Edit Group</h2>
            <button type="button" onclick="closeModal('edit_group_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <div id="edit_group_errors" class="hidden mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"></div>
        <form id="edit_group_form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="group_id" id="edit_group_id">
            <div><label class="form-label">Group Name</label><input type="text" name="group_name" id="edit_group_name" class="form-input" required></div>
            <div><label class="form-label">Capstone Title</label><input type="text" name="capstone_title" id="edit_group_capstone_title" class="form-input" required></div>
            <div>
                <label class="form-label">Adviser</label>
                <select name="adviser_id" id="edit_group_adviser" class="form-select" required>
                    @foreach($allTeachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->teacher_first_name }} {{ $teacher->teacher_last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Section</label>
                <input type="text" id="edit_group_section_display" class="form-input opacity-70 cursor-not-allowed" readonly>
            </div>
            <div>
                <label class="form-label">Add Student to Team</label>
                <div class="flex gap-2">
                    <select id="edit_group_student_select" class="form-select flex-1" multiple style="height:100px;"><option disabled>Loading section students...</option></select>
                    <button type="button" id="edit_group_add_students_btn" class="btn-primary whitespace-nowrap"><i class="fas fa-plus mr-1"></i> Add</button>
                </div>
            </div>
            <div>
                <label class="form-label">Current Team</label>
                <div id="edit_group_members_container" class="space-y-2 max-h-56 overflow-y-auto"></div>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('edit_group_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Save Group</button>
            </div>
        </form>
    </div>
</div>
<div id="import_teacher_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Import Teachers</h2>
            <button type="button" onclick="closeModal('import_teacher_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.import_teachers') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Excel File (.xlsx, .csv)</label>
                <input type="file" name="file" accept=".xlsx,.csv" class="form-input" required>
                <p class="text-xs text-[#9a9385] mt-1">
                    Columns: teacher_id, teacher_first_name, teacher_middle_name, teacher_last_name
                </p>
            </div>
            <a href="{{ route('admin.download_teacher_template') }}" class="text-xs text-[#d6b15c] hover:text-[#b88d3a] font-medium inline-flex items-center gap-1">
                <i class="fas fa-download"></i> Download template
            </a>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('import_teacher_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary"><i class="fas fa-file-import mr-1"></i> Import Teachers</button>
            </div>
        </form>
    </div>
</div>
<div id="createGroupModal" class="modal-overlay">
    <div class="modal-box wide">
        <div class="modal-accent"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Create New Group</h2>
            <button type="button" onclick="closeModal('createGroupModal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.create_group') }}" method="POST" class="space-y-4">
            @csrf
            <div><label class="form-label">Group Name</label><input type="text" name="group_name" class="form-input" readonly value=""></div>
            <div><label class="form-label">Capstone Title</label><input type="text" name="capstone_title" class="form-input" required></div>
            <div>
                <label class="form-label">Section</label>
              <select name="section" id="sectionSelect" class="form-select" required>
                    <option value="" disabled selected>Select a section</option>
                    @foreach($sections ?? [] as $section)
                        <option value="{{ $section->id }}" data-name="{{ $section->section_name }}">{{ $section->section_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Select Students (Ctrl to select multiple)</label>
                <input type="text" id="studentSearchInput" class="form-input mb-2" placeholder="Search student by name...">
                <div class="flex gap-2">
                    <select id="studentSelect" class="form-select flex-1" multiple style="height:100px;"><option disabled>Select a section first</option></select>
                    <button type="button" id="addStudentsBtn" class="btn-primary whitespace-nowrap"><i class="fas fa-plus mr-1"></i> Add</button>
                </div>
            </div>
            <div>
                <label class="form-label">Assigned Roles</label>
                <div id="selectedStudentsContainer" class="space-y-2 max-h-48 overflow-y-auto"></div>
                <p id="noStudentsMsg" class="text-sm text-[#5b6375] text-center py-2">No students added yet.</p>
            </div>
           <div>
    <label class="form-label">Adviser</label>
    <select name="adviser" id="adviserSelect" class="form-select" required>
        <option value="" disabled selected>Select an adviser</option>
        @foreach($allTeachers as $teacher)
            <option value="{{ $teacher->id }}">{{ $teacher->teacher_first_name }} {{ $teacher->teacher_last_name }}</option>
            @endforeach
        </select>
    </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('createGroupModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Create Group</button>
            </div>
        </form>
    </div>
</div>

    <div id="archived_progress_modal" class="modal-overlay">
        <div class="modal-box wide">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4 p-4 border-b border-[#e2dacf]">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);" id="apm_title">Archived Group Progress</h2>
                <button type="button" onclick="closeModal('archived_progress_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                <div class="flex justify-between items-center bg-[#faf8f4] p-4 border border-[#e2dacf] rounded-xl">
                    <span class="text-sm font-semibold text-[#0a1428]">Overall Completion Progress:</span>
                    <span id="apm_progress_pct" class="font-bold text-[#b88d3a]">0%</span>
                </div>
                <div class="overflow-x-auto" style="max-height: 350px;">
                    <table class="min-w-full divide-y divide-[#e2dacf]">
                        <thead>
                            <tr class="bg-[#faf8f4] text-left text-[11px] font-semibold uppercase tracking-wider text-[#5b6375]">
                                <th class="px-4 py-2">Milestone</th>
                                <th class="px-4 py-2">Completion Status</th>
                            </tr>
                        </thead>
                        <tbody id="apm_milestones_tbody" class="divide-y divide-[#e2dacf] text-xs text-[#171e2c]"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-[#e2dacf] p-4">
                <button type="button" onclick="closeModal('archived_progress_modal')" class="btn-ghost">Close</button>
            </div>
        </div>
    </div>
<div id="delete_milestone_modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-accent" style="background: #a12b2b;"></div>
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--navy);">Confirm Deletion</h2>
            <button type="button" onclick="closeModal('delete_milestone_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.delete_milestone') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="milestone_id" id="delete_milestone_id">
            <p class="text-sm text-[#5b6375]">
                You are about to permanently delete <strong id="delete_milestone_name" class="text-[#171e2c]"></strong>.
                This will also remove any associated rubrics and evaluation records. This cannot be undone.
            </p>
            <div>
                <label class="form-label">Confirm Your Admin Password</label>
                <div class="relative">
                    <input type="password" name="admin_password" id="delete_milestone_admin_password" class="form-input pr-10" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428]" onclick="toggleVisibility('delete_milestone_admin_password', this)">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeModal('delete_milestone_modal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="background:#a12b2b;"><i class="fas fa-trash mr-1"></i> Delete Milestone</button>
            </div>
        </form>
    </div>
</div>

    <div id="progress_detail_modal" class="modal-overlay">
        <div class="modal-box wide">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 id="progress_modal_title" style="font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:600; color:var(--navy);">Progress Details</h2>
                    <p id="progress_modal_summary" class="text-xs text-[#5b6375] mt-1"></p>
                </div>
                <button type="button" onclick="closeModal('progress_detail_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-2xl">&times;</button>
            </div>
            <div class="overflow-x-auto border border-[#e2dacf] rounded-xl" style="max-height: 60vh;">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf] sticky top-0">
                            <th class="p-3 pl-4">Group</th>
                            <th class="p-3">Section</th>
                            <th class="p-3">Progress</th>
                            <th class="p-3 pr-4">Status</th>
                        </tr>
                    </thead>
                    <tbody id="progress_modal_tbody" class="divide-y divide-[#f0ece4]"></tbody>
                </table>
            </div>
            <div class="flex justify-end gap-2 pt-4 mt-3 border-t border-[#e2dacf]">
                <button type="button" onclick="closeModal('progress_detail_modal')" class="btn-primary text-xs px-5 py-2">Close</button>
            </div>
        </div>
    </div>

    <div id="dashboard_detail_modal" class="modal-overlay">
        <div class="modal-box wide" style="max-width: 68rem;">
            <div class="modal-accent"></div>
            <div class="flex justify-between items-center mb-6 p-1">
                <div>
                    <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; font-weight:600; color:var(--navy);" id="ddm_modal_title">System Overview Details</h2>
                    <p class="text-xs text-[#5b6375] mt-1">Detailed directory of all registered entities across the workspace.</p>
                </div>
                <button type="button" onclick="closeModal('dashboard_detail_modal')" class="text-[#5b6375] hover:text-[#0a1428] transition text-2xl focus:outline-none">&times;</button>
            </div>
            <div class="flex gap-2 border-b border-[#e2dacf] pb-3 mb-6 overflow-x-auto">
                <button type="button" onclick="switchDdmTab('students')" id="ddm_tab_students" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                    <i class="fas fa-user-graduate mr-1.5 text-[#d6b15c]"></i> Students (<span id="ddm_count_students">{{ count($allStudents ?? []) }}</span>)
                </button>
                <button type="button" onclick="switchDdmTab('groups')" id="ddm_tab_groups" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                    <i class="fas fa-layer-group mr-1.5 text-[#d6b15c]"></i> Groups (<span id="ddm_count_groups">{{ count($groupsData ?? []) }}</span>)
                </button>
                <button type="button" onclick="switchDdmTab('teachers')" id="ddm_tab_teachers" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                    <i class="fas fa-chalkboard-teacher mr-1.5 text-[#d6b15c]"></i> Teachers (<span id="ddm_count_teachers">{{ count($allTeachers ?? []) }}</span>)
                </button>
                <button type="button" onclick="switchDdmTab('sections')" id="ddm_tab_sections" class="ddm-tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all border border-[#e2dacf] text-[#5b6375] hover:bg-[#faf8f4]">
                    <i class="fas fa-columns mr-1.5 text-[#d6b15c]"></i> Sections (<span id="ddm_count_sections">{{ count($allSections ?? []) }}</span>)
                </button>
            </div>
            <div class="mb-4">
                <div class="relative">
                    <input type="text" id="ddm_search_input" oninput="filterDdmTable()" class="form-input text-xs w-full pl-9 py-2 border border-[#e2dacf] rounded-xl bg-[#faf8f4]" placeholder="Search by name, ID, section, or other details...">
                    <i class="fas fa-search absolute left-3 top-3 text-[#b8b0a0] text-xs"></i>
                </div>
            </div>
            <div class="overflow-y-auto max-h-[50vh] border border-[#e2dacf] bg-white rounded-xl">
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
                            @forelse($allStudents ?? [] as $st)
                                <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower($st->student_first_name . ' ' . $st->student_last_name . ' ' . $st->user_id . ' ' . $st->course . ' ' . $st->section . ' ' . ($st->groups->pluck('group_name')->join(', ') ?: 'No Group')) }}">
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
                                        @php $groupNames = $st->groups->pluck('group_name')->join(', '); @endphp
                                        @if($groupNames)
                                            <span class="badge badge-navy text-[10px]"><i class="fas fa-layer-group text-[9px] mr-1"></i> {{ $groupNames }}</span>
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
                                <tr><td colspan="6" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No students registered.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="ddm_content_groups" class="ddm-tab-content hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                                <th class="p-3 pl-4">Group Name</th>
                                <th class="p-3">Capstone Title</th>
                                <th class="p-3">Section</th>
                                <th class="p-3">Adviser</th>
                                <th class="p-3">Evaluation Room</th>
                                <th class="p-3 pr-4 text-center">Members</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#faf1e0]">
                            @forelse($groupsData ?? [] as $gr)
                                <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower($gr['name'] . ' ' . $gr['capstone_title'] . ' ' . $gr['section_name'] . ' ' . ($gr['assigned_teacher_name'] ?? 'Unassigned') . ' ' . ($gr['room_name'] ?? 'Unassigned')) }}">
                                    <td class="p-3 pl-4 font-semibold text-[#0a1428]">{{ $gr['name'] }}</td>
                                    <td class="p-3 text-[#3d4450] max-w-xs truncate" title="{{ $gr['capstone_title'] ?? '' }}">{{ $gr['capstone_title'] ?? '—' }}</td>
                                    <td class="p-3 text-[#3d4450]">{{ $gr['section_name'] }}</td>
                                    <td class="p-3">
                                        @if($gr['assigned_teacher_name'])
                                            <span class="text-[#171e2c] font-medium"><i class="fas fa-chalkboard-teacher text-[#d6b15c] text-[10px] mr-1"></i> {{ $gr['assigned_teacher_name'] }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if($gr['room_name'] && $gr['room_name'] !== 'Unassigned')
                                            <span class="badge badge-green text-[10px]"><i class="fas fa-door-open text-[9px] mr-1"></i> {{ $gr['room_name'] }}</span>
                                        @else
                                            <span class="badge badge-muted text-[10px]">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="p-3 pr-4 text-center text-[#5b6375] font-semibold">{{ $gr['member_count'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No groups created.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="ddm_content_teachers" class="ddm-tab-content hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                                <th class="p-3 pl-4">Teacher</th>
                                <th class="p-3">User ID</th>
                                <th class="p-3">Adviser Groups</th>
                                <th class="p-3">Assigned Sections</th>
                                <th class="p-3 pr-4">Contact</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#faf1e0]">
                            @forelse($allTeachers ?? [] as $t)
                                @php
                                    $groupNames = $t->groups->pluck('group_name')->join(', ');
                                    $secNames = $t->sections->pluck('section_name')->join(', ');
                                @endphp
                                <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower($t->teacher_first_name . ' ' . $t->teacher_last_name . ' ' . ($t->user->user_id ?? '') . ' ' . $groupNames . ' ' . $secNames) }}">
                                    <td class="p-3 pl-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);">
                                                {{ strtoupper(substr($t->teacher_first_name, 0, 1) . substr($t->teacher_last_name, 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-[#0a1428]">{{ $t->teacher_first_name }} {{ $t->teacher_last_name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 text-[#3d4450] font-mono">{{ $t->user->user_id ?? 'N/A' }}</td>
                                    <td class="p-3 text-[#3d4450]">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @forelse($t->groups as $g)
                                                <span class="badge badge-navy text-[9px]">{{ $g->group_name }}</span>
                                            @empty
                                                <span class="text-gray-400 text-[10px] italic">None</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-3 text-[#3d4450]">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @forelse($t->sections as $sec)
                                                <span class="badge badge-purple text-[9px]" style="background-color:rgba(107,33,168,0.1); color:rgb(107,33,168); border:1px solid rgba(107,33,168,0.2);">{{ $sec->section_name }}</span>
                                            @empty
                                                <span class="text-gray-400 text-[10px] italic">None</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-3 pr-4 text-[#5b6375]">
                                        <div class="flex flex-col text-[10px]">
                                            <span><i class="fa-regular fa-envelope mr-1 text-[#b8b0a0]"></i> {{ $t->teacher_email }}</span>
                                            @if($t->contact_number)
                                                <span class="mt-0.5"><i class="fa-solid fa-phone mr-1 text-[#b8b0a0]"></i> {{ $t->contact_number }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No teachers registered.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="ddm_content_sections" class="ddm-tab-content hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#faf8f4] text-[#0a1428] font-semibold text-xs border-b border-[#e2dacf]">
                                <th class="p-3 pl-4">Section Name</th>
                                <th class="p-3">Assigned Teacher</th>
                                <th class="p-3">User ID of Teacher</th>
                                <th class="p-3 pr-4 text-center">Section ID</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#faf1e0]">
                            @forelse($sectionsData ?? [] as $sec)
                                <tr class="hover:bg-[#faf8f4]/50 text-xs ddm-row-item transition duration-150" data-search-text="{{ strtolower($sec['name'] . ' ' . ($sec['assigned_teacher_name'] ?? 'Unassigned') . ' ' . ($sec['assigned_teacher_id'] ?? '')) }}">
                                    <td class="p-3 pl-4 font-semibold text-[#0a1428]">{{ $sec['name'] }}</td>
                                    <td class="p-3">
                                        @if($sec['assigned_teacher_name'])
                                            <span class="text-[#171e2c] font-medium"><i class="fas fa-chalkboard-teacher text-[#d6b15c] text-[10px] mr-1"></i> {{ $sec['assigned_teacher_name'] }}</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium text-red-700 bg-red-50 border border-red-200"><i class="fas fa-exclamation-triangle text-[9px] mr-1"></i> Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-[#3d4450] font-mono">{{ $sec['assigned_teacher_id'] ?? '—' }}</td>
                                    <td class="p-3 pr-4 text-center text-[#5b6375] font-mono">{{ $sec['id'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-8 text-center text-[#5b6375]"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> No sections registered.</td></tr>
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

<script>
// ---- SECTION NAVIGATION ----
const sections = {
    dashboard:  document.getElementById('dashboard-section'),
    teachers:   document.getElementById('teachers-section'),
    students:   document.getElementById('students-section'),
    rubrics:    document.getElementById('rubrics-section'),
    progress:   document.getElementById('progress-section'),
    evaluation: document.getElementById('evaluation-section'),
    profile:    document.getElementById('profile-section'),
    capstone:   document.getElementById('capstone-section'),
    documents:  document.getElementById('documents-section'),
};
const navLinks = document.querySelectorAll('.nav-link');
const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

const ACTIVE_SECTION_KEY = 'activeSection';
const rootEl = document.documentElement;
const mainEl = document.querySelector('main');
let currentSectionId = rootEl.getAttribute('data-active-section') || 'dashboard';

function readActiveSection() {
    const fromUrl = window.location.hash.replace(/^#/, '');
    if (fromUrl && sections[fromUrl]) return fromUrl;
    try {
        const s = sessionStorage.getItem(ACTIVE_SECTION_KEY);
        if (s && sections[s]) return s;
    } catch (_) {}
    return 'dashboard';
}

function persistActiveSection(sectionId) {
    try { sessionStorage.setItem(ACTIVE_SECTION_KEY, sectionId); } catch (_) {}
    try { history.replaceState(null, '', '#' + encodeURIComponent(sectionId)); } catch (_) {}
}

function activateSection(sectionId, persist = true, animate = false) {
    const validSectionId = sections[sectionId] ? sectionId : 'dashboard';
    const target = sections[validSectionId];
    if (!target) return;
    currentSectionId = validSectionId;
    rootEl.setAttribute('data-active-section', validSectionId);

    Object.values(sections).forEach(section => {
        if (section) section.classList.toggle('hidden', section !== target);
    });
    navLinks.forEach(link => {
        const isActive = link.dataset.section === validSectionId;
        link.classList.toggle('active-link', isActive);
        link.style.color = isActive ? 'var(--gold)' : 'rgba(255,255,255,0.65)';
        const chevron = link.querySelector('.fa-chevron-right');
        if (chevron) chevron.style.display = isActive ? 'inline-block' : 'none';
    });
    mobileNavLinks.forEach(link => {
        if (!link.dataset.section) return;
        link.style.color = link.dataset.section === validSectionId ? 'var(--gold)' : 'rgba(255,255,255,0.55)';
    });
    if (animate) {
        target.classList.remove('section-enter');
        void target.offsetWidth;
        target.classList.add('section-enter');
    }
    if (persist) persistActiveSection(validSectionId);
}

window.addEventListener('beforeunload', () => {
    persistActiveSection(currentSectionId);
    try { sessionStorage.setItem('scroll:' + currentSectionId, String(mainEl ? mainEl.scrollTop : 0)); } catch (_) {}
});
window.addEventListener('load', () => {
    try {
        const y = parseInt(sessionStorage.getItem('scroll:' + currentSectionId), 10);
        if (mainEl && !isNaN(y)) mainEl.scrollTop = y;
    } catch (_) {}
});

function setupSectionNavigation() {
    [...navLinks, ...mobileNavLinks].forEach(link => {
        if (!link.dataset.section) return;
        link.addEventListener('click', event => {
            event.preventDefault();
            activateSection(link.dataset.section, true, true);
            try { sessionStorage.removeItem('scroll:' + link.dataset.section); } catch (_) {}
            if (mainEl) mainEl.scrollTop = 0;
        });
    });
    activateSection(readActiveSection(), false, false);
}
setupSectionNavigation();

// ---- STUDENTS / GROUPS SUB-TABS ----
function applySgView(view) {
    rootEl.setAttribute('data-sg-view', view);
    document.querySelectorAll('.sg-tab-btn').forEach(b => b.classList.toggle('active', b.dataset.view === view));
    document.getElementById('sg-students-view').classList.toggle('hidden', view !== 'students');
    document.getElementById('sg-groups-view').classList.toggle('hidden', view !== 'groups');
}
document.querySelectorAll('.sg-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const view = btn.dataset.view;
        applySgView(view);
        try { sessionStorage.setItem('activeSgView', view); } catch (_) {}
        if (view === 'students' && studentsPaginate) setTimeout(studentsPaginate, 50);
        if (view === 'groups' && groupsPaginate) groupsPaginate();
    });
});
try { applySgView(sessionStorage.getItem('activeSgView') === 'groups' ? 'groups' : 'students'); } catch (_) {}

// ---- DOM READY (main) ----
document.addEventListener('DOMContentLoaded', () => {
    navLinks.forEach(link => {
        const chevron = link.querySelector('.fa-chevron-right');
        if (chevron) chevron.style.display = link.classList.contains('active-link') ? 'inline-block' : 'none';
    });

    document.querySelectorAll('.fill-animate').forEach(bar => {
        const target = bar.dataset.target || 0;
        requestAnimationFrame(() => {
            setTimeout(() => { bar.style.width = target + '%'; }, 100);
        });
    });
    filterMilestones();
    document.querySelectorAll('.reorder-stage-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.reorder-stage-tab-btn').forEach(b => b.classList.toggle('active', b === btn));
            const stage = btn.dataset.stage;
            document.querySelectorAll('.reorder-stage-form').forEach(f => {
                f.style.display = f.dataset.stage === stage ? '' : 'none';
            });
        });
    });

    document.querySelectorAll('.toggle-section-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const target = document.getElementById(this.dataset.target);
            if (!target) return;
            target.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            if (icon) {
                icon.className = target.classList.contains('collapsed')
                    ? 'fa-solid fa-chevron-down'
                    : 'fa-solid fa-chevron-up';
            }
        });
    });

    const docSearch = document.getElementById('documents-search');
    if (docSearch) {
        docSearch.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('.documents-row');
            let visible = 0;
            rows.forEach(row => {
                const match = !q || (row.dataset.search || '').includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('documents-no-match')?.classList.toggle('hidden', visible !== 0 || rows.length === 0);
        });
    }

    const teacherSearch = document.getElementById('teacher-search');
    if (teacherSearch) {
        teacherSearch.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#teachers-section tbody tr');
            let visible = 0;
            rows.forEach(row => {
                const match = !query || row.textContent.toLowerCase().includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('teacher-count').textContent = `${visible} teachers`;
        });
    }

    const studentSearch = document.getElementById('student-search');
    if (studentSearch) studentSearch.addEventListener('input', () => applyStudentFilters());

    const groupSearch = document.getElementById('group-search');
    if (groupSearch) groupSearch.addEventListener('input', () => applyGroupFilters());
});

// ---- MODALS ----
function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');

    if (id === 'createGroupModal') {
        setTimeout(() => {
            const input = document.querySelector('#createGroupModal input[name="group_name"]');
            if (input) input.value = suggestGroupName();
        }, 100);
    }
}
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

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
            students: 'Students Directory',
            groups: 'Capstone Groups Directory',
            teachers: 'Teachers Directory',
            sections: 'Sections Directory'
        };
        modalTitle.textContent = titleMap[tabName] || 'System Overview Details';
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
        counterEl.textContent = query ? `${visibleCount}/${rows.length}` : rows.length;
    }
};

function viewArchivedProgress(groupId, groupName) {
    document.getElementById('apm_title').textContent = `Archived Progress: ${groupName}`;
    const tbody = document.getElementById('apm_milestones_tbody');
    tbody.innerHTML = '<tr><td colspan="2" class="text-center py-4">Loading progress...</td></tr>';
    openModal('archived_progress_modal');
    fetch(`/admin/get-group-progress/${groupId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('apm_progress_pct').textContent = `${data.overall_progress ?? 0}%`;
            tbody.innerHTML = '';
            if (!data.milestones || data.milestones.length === 0) {
                tbody.innerHTML = '<tr><td colspan="2" class="text-center py-4">No milestones for this stage.</td></tr>';
                return;
            }
            data.milestones.forEach(m => {
                const tr = document.createElement('tr');
                const statusBadge = m.is_completed
                    ? '<span class="px-2 py-0.5 text-[10px] font-semibold text-green-700 bg-green-50 rounded">Completed</span>'
                    : '<span class="px-2 py-0.5 text-[10px] font-semibold text-red-600 bg-red-50 rounded">Pending</span>';
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="font-bold text-[#0a1428]">${m.title}</div>
                        <div class="text-[10px] text-[#5b6375]">${m.description ?? ''}</div>
                    </td>
                    <td class="px-4 py-3">${statusBadge}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="2" class="text-center text-red-500 py-4">Failed to load archived progress data.</td></tr>';
        });
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('active'); });
});

function normText(s) { return (s ?? '').toString().trim().toUpperCase(); }

document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', function () {
        const targetId = this.dataset.target;
        if (!targetId) return;
        const input = document.getElementById(targetId);
        if (input) {
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
            } else {
                input.type = 'password';
                if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
            }
        }
    });
});
function toggleVisibility(id, btn) {
    const input = document.getElementById(id);
    if (!input) return;
    const icon = btn ? btn.querySelector('i') : null;
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.className = 'fa-regular fa-eye-slash';
    } else {
        input.type = 'password';
        if (icon) icon.className = 'fa-regular fa-eye';
    }
}

function addCriteriaRow(listId = 'criteria-list', values = null) {
    const list = document.getElementById(listId);
    if (!list) return;
    const row = document.createElement('div');
    row.className = 'criteria-row grid grid-cols-12 gap-2';
    row.innerHTML = `
        <input type="text" name="criteria_name[]" placeholder="Criteria name" class="form-input col-span-6" required value="${values?.criteria_name ?? ''}">
        <input type="number" name="weight[]" min="0" max="100" step="0.01" placeholder="Weight %" class="form-input col-span-2" required value="${values?.weight ?? ''}">
        <input type="number" name="score[]" min="0" placeholder="Max score" class="form-input col-span-3" required value="${values?.max_score ?? 4}" readonly>
        <button type="button" onclick="this.closest('.criteria-row').remove()" class="col-span-1 text-[#5b6375] hover:text-red-500 flex items-center justify-center"><i class="fas fa-trash text-xs"></i></button>`;
    list.appendChild(row);
}

function toggleRubricSection(checked) {
    const section = document.getElementById('milestone_rubric_section');
    section.classList.toggle('hidden', !checked);
    const nameInput = document.getElementById('milestone_rubric_name');
    nameInput.required = checked;
    const criteriaList = document.getElementById('milestone-criteria-list');
    if (checked && criteriaList.children.length === 0) {
        addCriteriaRow('milestone-criteria-list');
    }
}

function validateMilestoneForm() {
    const chk = document.getElementById('add_rubric_chk');
    if (chk && chk.checked) {
        const errorEl = document.getElementById('milestone-error-message');
        errorEl.classList.add('hidden');
        const weights = document.querySelectorAll('#milestone-criteria-list input[name="weight[]"]');
        let total = 0;
        weights.forEach(w => total += parseFloat(w.value) || 0);
        total = Math.round(total * 100) / 100;
        if (total !== 100) {
            errorEl.textContent = `Total weight must equal 100%. Current total: ${total}%`;
            errorEl.classList.remove('hidden');
            return false;
        }
    }
    return true;
}

// ---- EDIT MILESTONE ----
function openEditMilestoneModal(milestoneId) {
    fetch(`/admin/get-milestone/${milestoneId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit_milestone_id').value = data.id;
            document.getElementById('edit_milestone_title').value = data.milestone_title;
            document.getElementById('edit_milestone_stage').value = data.capstone_stage_id;
            document.getElementById('edit_milestone_order').value = data.step_order;
            document.getElementById('edit_milestone_description').value = data.milestone_description;
            document.getElementById('edit_milestone_start').value = data.start_date ? data.start_date.substring(0, 10) : '';
            document.getElementById('edit_milestone_due').value = data.due_date ? data.due_date.substring(0, 10) : '';

            const hasCertCheckbox = document.getElementById('edit_milestone_has_cert');
            const certFields = document.getElementById('edit_milestone_cert_fields');
            if (data.certificate) {
                hasCertCheckbox.checked = true;
                certFields.classList.remove('hidden');
                document.getElementById('edit_certificate_title').value = data.certificate.certificate_title;
                document.getElementById('edit_certificate_description').value = data.certificate.certificate_description;
            } else {
                hasCertCheckbox.checked = false;
                certFields.classList.add('hidden');
                document.getElementById('edit_certificate_title').value = '';
                document.getElementById('edit_certificate_description').value = '';
            }

            const hasRubricCheckbox = document.getElementById('edit_add_rubric_chk');
            const rubricSection = document.getElementById('edit_milestone_rubric_section');
            const criteriaList = document.getElementById('edit-milestone-criteria-list');
            criteriaList.innerHTML = '';
            if (data.rubric) {
                hasRubricCheckbox.checked = true;
                rubricSection.classList.remove('hidden');
                document.getElementById('edit_milestone_rubric_name').value = data.rubric.rubric_name;
                document.getElementById('edit_milestone_rubric_name').required = true;
                data.rubric.criteria.forEach(c => addCriteriaRow('edit-milestone-criteria-list', c));
            } else {
                hasRubricCheckbox.checked = false;
                rubricSection.classList.add('hidden');
                document.getElementById('edit_milestone_rubric_name').value = '';
                document.getElementById('edit_milestone_rubric_name').required = false;
            }

            document.getElementById('edit_milestone_form').action = `/admin/update-milestone/${data.id}`;
            document.getElementById('edit_milestone_errors').classList.add('hidden');
            openModal('edit_milestone_modal');
        })
        .catch(() => showToast('Failed to load milestone.', true));
}

document.getElementById('edit_milestone_has_cert').addEventListener('change', function () {
    document.getElementById('edit_milestone_cert_fields').classList.toggle('hidden', !this.checked);
});

document.getElementById('edit_add_rubric_chk').addEventListener('change', function () {
    const section = document.getElementById('edit_milestone_rubric_section');
    section.classList.toggle('hidden', !this.checked);
    document.getElementById('edit_milestone_rubric_name').required = this.checked;
    const criteriaList = document.getElementById('edit-milestone-criteria-list');
    if (this.checked && criteriaList.children.length === 0) {
        addCriteriaRow('edit-milestone-criteria-list');
    }
});

document.getElementById('edit_milestone_form').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = this;
    const errorsBox = document.getElementById('edit_milestone_errors');
    errorsBox.classList.add('hidden');
    errorsBox.innerHTML = '';
    const rubricChk = document.getElementById('edit_add_rubric_chk');
    if (rubricChk && rubricChk.checked) {
        const errorEl = document.getElementById('edit-milestone-error-message');
        errorEl.classList.add('hidden');
        const weights = document.querySelectorAll('#edit-milestone-criteria-list input[name="weight[]"]');
        let total = 0;
        weights.forEach(w => total += parseFloat(w.value) || 0);
        total = Math.round(total * 100) / 100;
        if (total !== 100) {
            errorEl.textContent = `Total weight must equal 100%. Current total: ${total}%`;
            errorEl.classList.remove('hidden');
            return;
        }
    }
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

        if (data?.success) {
            toastAfterReload(data.message || 'Milestone updated successfully.', false);
            closeModal('edit_milestone_modal');
           softReload();

        } else if (data?.errors) {
            errorsBox.innerHTML = Object.values(data.errors).flat().join('<br>');
            errorsBox.classList.remove('hidden');
        } else {
            showToast(data?.message || data?.error || 'Failed to update milestone.', true);
        }
    })
    .catch(() => showToast('Failed to update milestone.', true));
});

// ---- EDIT RUBRIC ----
function openEditRubricModal(rubricId) {
    fetch(`/admin/get-rubric/${rubricId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit_rubric_id').value = data.id;
            document.getElementById('edit_rubric_name').value = data.rubric_name;
            document.getElementById('edit_capstone_id').value = data.capstone_id;
            document.getElementById('edit_rubric_form').action = `/admin/update-rubric/${data.id}`;
            const milestoneSelect = document.getElementById('edit_milestone_select');
            Array.from(milestoneSelect.options).forEach(opt => {
                if (!opt.value) return;
                opt.style.display = (opt.dataset.capstoneStageId == data.capstone_id) ? '' : 'none';
            });
            milestoneSelect.value = data.milestone_id;
            const list = document.getElementById('edit_criteria_list');
            list.innerHTML = '';
            data.criteria.forEach(c => addCriteriaRow('edit_criteria_list', c));
            if (data.criteria.length === 0) addCriteriaRow('edit_criteria_list');
            document.getElementById('edit_rubric_errors').classList.add('hidden');
            openModal('rubrics_edit_modal');
        })
        .catch(() => showToast('Failed to load rubric.', true));
}

document.getElementById('edit_capstone_id').addEventListener('change', function () {
    const stageId = this.value;
    const ms = document.getElementById('edit_milestone_select');
    Array.from(ms.options).forEach(opt => {
        if (!opt.value) return;
        opt.style.display = (!stageId || opt.dataset.capstoneStageId == stageId) ? '' : 'none';
    });
    ms.value = '';
});

function syncDocTitle(sel) {
    const map = { recommendation: 'Recommendation Sheet', approval: 'Approval Sheet' };
    document.getElementById('edit_certificate_title').value = map[sel.value] || '';
}

document.getElementById('edit_rubric_form').addEventListener('submit', function (e) {
    e.preventDefault();
    const weights = document.querySelectorAll('#edit_criteria_list input[name="weight[]"]');
    let total = 0; weights.forEach(w => total += parseFloat(w.value) || 0);
    total = Math.round(total * 100) / 100;
    const errorEl = document.getElementById('edit_error_message');
    if (total !== 100) {
        errorEl.textContent = `Total weight must equal 100%. Current total: ${total}%`;
        errorEl.classList.remove('hidden');
        return;
    }
    errorEl.classList.add('hidden');
    const form = this;
    const errorsBox = document.getElementById('edit_rubric_errors');
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

        if (data?.success) {
            toastAfterReload(data.message || 'Rubric updated successfully.', false);
            closeModal('rubrics_edit_modal');
            softReload();

        } else if (data?.errors) {
            errorsBox.innerHTML = Object.values(data.errors).flat().join('<br>');
            errorsBox.classList.remove('hidden');
        } else {
            showToast('Something went wrong.', true);
        }
    })
    .catch(() => showToast('Failed to update rubric.', true));
});

function openDeleterubricModal(rubricId) {
    document.getElementById('delete_rubric_id').value = rubricId;
    document.getElementById('delete_rubric_name').textContent = 'this rubric';
    openModal('delete_rubric_modal');
}
function openDeleteMilestoneModal(milestoneId, milestoneTitle) {
    document.getElementById('delete_milestone_id').value = milestoneId;
    document.getElementById('delete_milestone_name').textContent = milestoneTitle || 'this milestone';
    document.getElementById('delete_milestone_admin_password').value = '';
    openModal('delete_milestone_modal');
}
function openDeleteGroupModal(id, name) {
    document.getElementById('delete_group_id').value = id;
    document.getElementById('delete_group_name').textContent = name;
    document.getElementById('delete_group_admin_password').value = '';
    openModal('delete_group_modal');
}

// ---- ACTIVE STAGES TOGGLE (no reload) ----
function setStatusBadge(el, enabled) {
    el.className = 'text-xs text-[#5b6375] stage-status';
    el.innerHTML = enabled
        ? '<span class="text-green-600 font-semibold uppercase tracking-wider text-[10px]">Active / Enabled</span>'
        : '<span class="text-red-500 font-semibold uppercase tracking-wider text-[10px]">Disabled</span>';
}

function applyStageStates(stages, year) {
    (stages || []).forEach(s => {
        const row = document.querySelector(`[data-stage-row="${s.id}"]`);
        if (!row) return;
        const cb = row.querySelector('.stage-toggle');
        if (cb) cb.checked = s.is_enabled;
        const status = row.querySelector('.stage-status');
        if (status) setStatusBadge(status, s.is_enabled);
    });

    if (year) {
        [['dash-c1-status', year.c1], ['dash-c2-status', year.c2]].forEach(([id, on]) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = on ? 'Enabled' : 'Disabled';
            el.classList.toggle('text-green-600', on);
            el.classList.toggle('text-red-500', !on);
        });
    }
}

async function toggleCapstoneStage(input) {
    const form = input.closest('form');
    const wanted = input.checked;
    document.querySelectorAll('.stage-toggle').forEach(cb => cb.disabled = true);

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form),
            credentials: 'same-origin'
        });
        const data = await res.json().catch(() => null);
        if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed to update stage.');

        applyStageStates(data.stages, data.year);
        showToast(data.message);
    } catch (err) {
        input.checked = !wanted;   // revert the switch
        showToast(err.message || 'Failed to update stage.', true);
    } finally {
        document.querySelectorAll('.stage-toggle').forEach(cb => cb.disabled = false);
    }
}
window.toggleCapstoneStage = toggleCapstoneStage;
// ---- EVALUATION ROOMS ----
let currentEditRoomId = null;

function fillRoomTeacherSelect(availableTeachers) {
    const select = document.getElementById('edit_room_teacher_select');
    select.innerHTML = '';
    if (availableTeachers && availableTeachers.length > 0) {
        availableTeachers.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.name;
            select.appendChild(opt);
        });
    } else {
        const opt = document.createElement('option');
        opt.disabled = true;
        opt.textContent = 'No available unassigned teachers';
        select.appendChild(opt);
    }
}

function openEditRoomModal(roomId) {
    fetch(`/admin/get-room/${roomId}`)
        .then(r => r.json())
        .then(data => {
            currentEditRoomId = data.id;
            document.getElementById('edit_room_title').textContent = data.room_name;
            renderRoomPanelists(data.panelists);
            fillRoomTeacherSelect(data.available_teachers);
            openModal('edit_room_modal');
        })
        .catch(() => showToast('Failed to load room.', true));
}

function renderRoomPanelists(panelists) {
    const container = document.getElementById('edit_room_panelists_container');
    container.innerHTML = '';
    if (!panelists.length) {
        container.innerHTML = '<p class="text-sm text-[#5b6375] text-center py-2">No panelists yet.</p>';
        return;
    }
    panelists.forEach(p => {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded border border-[#e2dacf]';
        row.innerHTML = `<span class="flex-1 text-sm">${p.name}</span>
            <button type="button" class="text-[#5b6375] hover:text-red-500 transition remove-panelist-btn"><i class="fas fa-times"></i></button>`;
        row.querySelector('.remove-panelist-btn').addEventListener('click', () => removePanelistFromRoom(p.id, row));
        container.appendChild(row);
    });
}

function removePanelistFromRoom(teacherId, row) {
    fetch(`/admin/evaluation-rooms/${currentEditRoomId}/panelists/${teacherId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            row.remove();
            fetchRoomAvailableTeachers(currentEditRoomId);
        }
    })
    .catch(() => showToast('Failed to remove panelist.', true));
}

function fetchRoomAvailableTeachers(roomId) {
    fetch(`/admin/get-room/${roomId}`)
        .then(r => r.json())
        .then(data => fillRoomTeacherSelect(data.available_teachers));
}

document.getElementById('edit_room_add_btn').addEventListener('click', () => {
    const select = document.getElementById('edit_room_teacher_select');
    const selectedOptions = Array.from(select.selectedOptions);
    if (selectedOptions.length === 0) {
        showToast('Please select at least one teacher.', true);
        return;
    }
    selectedOptions.forEach(option => {
        const teacherId = option.value;
        if (!teacherId) return;
        fetch(`/admin/evaluation-rooms/${currentEditRoomId}/panelists`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ teacher_id: teacherId })
        })
        .then(async r => {
            const data = await r.json();
            if (data.error) {
                showToast(data.error, true);
                return;
            }
            const container = document.getElementById('edit_room_panelists_container');
            const emptyMsg = container.querySelector('p');
            if (emptyMsg) emptyMsg.remove();
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded border border-[#e2dacf]';
            row.innerHTML = `<span class="flex-1 text-sm">${data.panelist.name}</span>
                <button type="button" class="text-[#5b6375] hover:text-red-500 transition remove-panelist-btn"><i class="fas fa-times"></i></button>`;
            row.querySelector('.remove-panelist-btn').addEventListener('click', () => removePanelistFromRoom(data.panelist.id, row));
            container.appendChild(row);
            fetchRoomAvailableTeachers(currentEditRoomId);
        })
        .catch(() => showToast('Failed to add panelist.', true));
    });
    select.selectedIndex = -1;
});

function openDeleteRoomModal(roomId, roomName) {
    document.getElementById('delete_room_id').value = roomId;
    document.getElementById('delete_room_name').textContent = roomName;
    openModal('delete_room_modal');
}

// ---- CHANGE ADVISER MODAL ----
const groupsDataJs = @json($groupsData);
const milestonesDataJs = @json($milestones);

function filterMilestonesByStage(stageId) {
    const activitySelect = document.getElementById('create_room_participate_activity');
    const filteredMilestones = milestonesDataJs.filter(m => String(m.capstone_stage_id) === String(stageId));
    activitySelect.innerHTML = '';
    if (filteredMilestones.length === 0) {
        activitySelect.innerHTML = '<option value="" disabled selected>No milestones for this stage</option>';
        activitySelect.disabled = true;
    } else {
        activitySelect.innerHTML = '<option value="" disabled selected>Select Participate Activity</option>';
        let hasRubrics = false;
        filteredMilestones.forEach(m => {
            if (m.rubrics && m.rubrics.length > 0) {
                hasRubrics = true;
                const optActivity = document.createElement('option');
                optActivity.value = m.id;
                optActivity.textContent = m.milestone_title;
                activitySelect.appendChild(optActivity);
            }
        });
        if (!hasRubrics) {
            activitySelect.innerHTML = '<option value="" disabled selected>No milestones with rubrics for this stage</option>';
            activitySelect.disabled = true;
        } else {
            activitySelect.disabled = false;
        }
    }
}

function resetAssignModal() {
    const sectionSel = document.getElementById('ca_section_select');
    const groupSel = document.getElementById('ca_group_select');
    const adviserSel = document.getElementById('ca_adviser_select');
    sectionSel.value = '';
    groupSel.innerHTML = '<option value="" disabled selected>Select a section first</option>';
    groupSel.disabled = true;
    adviserSel.value = '';
    adviserSel.disabled = true;
    document.getElementById('ca_current_adviser').value = '';
}

document.getElementById('ca_section_select').addEventListener('change', function () {
    const sectionId = this.value;
    const groupSelect = document.getElementById('ca_group_select');
    const filtered = groupsDataJs.filter(g => String(g.section_id) === String(sectionId));
    groupSelect.innerHTML = '';
    if (filtered.length === 0) {
        groupSelect.innerHTML = '<option value="" disabled selected>No groups in this section</option>';
        groupSelect.disabled = true;
    } else {
        groupSelect.innerHTML = '<option value="" disabled selected>Select group</option>';
        filtered.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.name;
            groupSelect.appendChild(opt);
        });
        groupSelect.disabled = false;
    }
    document.getElementById('ca_current_adviser').value = '';
    const adviserSelect = document.getElementById('ca_adviser_select');
    adviserSelect.value = '';
    adviserSelect.disabled = true;
});

document.getElementById('ca_group_select').addEventListener('change', function () {
    const group = groupsDataJs.find(g => String(g.id) === String(this.value));
    document.getElementById('ca_current_adviser').value = group?.assigned_teacher_name ?? 'Unassigned';
    const adviserSelect = document.getElementById('ca_adviser_select');
    adviserSelect.disabled = false;
    adviserSelect.value = '';
});

// ---- MILESTONE FILTER ----
const capstoneSelect = document.querySelector('select[name="capstone_id"]');
const milestoneSelect = document.getElementById('milestone-select');
function filterMilestones() {
    if (!capstoneSelect || !milestoneSelect) return;
    const selected = capstoneSelect.value;
    milestoneSelect.querySelectorAll('option').forEach(opt => {
        if (opt.value === '') return;
        opt.style.display = (!selected || opt.dataset.capstoneStageId == selected) ? '' : 'none';
    });
    const sel = milestoneSelect.options[milestoneSelect.selectedIndex];
    if (sel && sel.style.display === 'none') milestoneSelect.value = '';
}
if (capstoneSelect) capstoneSelect.addEventListener('change', filterMilestones);

const milestoneRows = document.querySelectorAll('.milestone-row');
milestoneRows.forEach(row => row.style.display = 'flex');
document.querySelectorAll('.fill-animate').forEach(bar => {
    bar.style.width = '0%';
    requestAnimationFrame(() => { setTimeout(() => { bar.style.width = (bar.dataset.target || 0) + '%'; }, 50); });
});

// ---- STUDENT/TEACHER EDIT MODALS ----
function openEditStudentModal(student) {
    document.getElementById('edit_original_student_id').value = student.user_id ?? '';
    document.getElementById('edit_student_id').value = student.user_id ?? '';
    document.getElementById('edit_first_name').value = student.student_first_name ?? '';
    document.getElementById('edit_middle_name').value = student.student_middle_name ?? '';
    document.getElementById('edit_last_name').value = student.student_last_name ?? '';
    document.getElementById('edit_email').value = student.student_email ?? '';
    document.getElementById('edit_contact').value = student.contact_number ?? '';
    document.getElementById('edit_section').value = student.section ?? '';
    openModal('student_edit_modal');
}
function openDeleteStudentModal(id, name) {
    document.getElementById('delete_student_id').value = id;
    document.getElementById('delete_student_name').textContent = name;
    openModal('delete_student_modal');
}
function openEditTeacherModal(teacher) {
    document.getElementById('edit_original_teacher_id').value = teacher.user_id ?? '';
    document.getElementById('edit_teacher_id').value = teacher.user_id ?? '';
    document.getElementById('edit_teacher_first_name').value = teacher.teacher_first_name ?? '';
    document.getElementById('edit_teacher_middle_name').value = teacher.teacher_middle_name ?? '';
    document.getElementById('edit_teacher_last_name').value = teacher.teacher_last_name ?? '';
    document.getElementById('edit_teacher_email').value = teacher.teacher_email ?? '';
    document.getElementById('edit_teacher_contact').value = teacher.contact_number ?? '';
    openModal('teacher_edit_modal');
}
function openDeleteTeacherModal(id, name) {
    document.getElementById('delete_teacher_id').value = id;
    document.getElementById('delete_teacher_name').textContent = name;
    openModal('delete_teacher_modal');
}

// ----- CAPSTONE STAGE / YEAR MODALS -----
window.openEditStageModal = function (id, title, type) {
    document.getElementById('edit_stage_form').action = `/admin/capstone/update-stage/${id}`;
    document.getElementById('edit_stage_title').value = title;
    document.getElementById('edit_stage_type').value = type;
    openModal('edit_stage_modal');
};

window.openEditYearModal = function (id, year, c1, c2) {
    document.getElementById('edit_year_form').action = `/admin/capstone/update-year/${id}`;
    document.getElementById('edit_year_title').value = year;
    document.getElementById('edit_year_c1').checked = !!c1;
    document.getElementById('edit_year_c2').checked = !!c2;
    document.getElementById('edit_year_form').dataset.originalYear = year;
    if (typeof window.syncEditYearCheckboxes === 'function') window.syncEditYearCheckboxes();
    openModal('edit_year_modal');
};

window.confirmActivateYear = function (id, year) {
    document.getElementById('activate_year_form').action = `/admin/capstone/activate-year/${id}`;
    document.getElementById('activate_year_name').textContent = year;
    openModal('activate_year_modal');
};

window.confirmArchiveYear = function (id, year) {
    document.getElementById('archive_year_form').action = `/admin/capstone/archive-year/${id}`;
    document.getElementById('archive_year_name').textContent = year;
    openModal('archive_year_modal');
};

window.openDeleteYearModal = function (id, year) {
    document.getElementById('delete_year_form').action = `/admin/capstone/delete-year/${id}`;
    document.getElementById('delete_year_name').textContent = year;
    document.getElementById('delete_year_admin_password').value = '';
    openModal('delete_year_modal');
};

document.addEventListener('DOMContentLoaded', function () {
    const addC1 = document.getElementById('add_year_c1');
    const addC2 = document.getElementById('add_year_c2');
    const editC1 = document.getElementById('edit_year_c1');
    const editC2 = document.getElementById('edit_year_c2');

    function syncCheckboxStyles(c1, c2) {
        if (c1 && c2) {
            if (c1.checked) {
                c1.classList.add('opacity-70', 'cursor-not-allowed');
                c1.parentElement.classList.add('opacity-70', 'cursor-not-allowed');
                c2.classList.remove('opacity-70', 'cursor-not-allowed');
                c2.parentElement.classList.remove('opacity-70', 'cursor-not-allowed');
            } else if (c2.checked) {
                c2.classList.add('opacity-70', 'cursor-not-allowed');
                c2.parentElement.classList.add('opacity-70', 'cursor-not-allowed');
                c1.classList.remove('opacity-70', 'cursor-not-allowed');
                c1.parentElement.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        }
    }

    if (addC1 && addC2) {
        addC1.addEventListener('click', function () {
            if (addC1.checked) addC2.checked = false; else addC1.checked = true;
            syncCheckboxStyles(addC1, addC2);
        });
        addC2.addEventListener('click', function () {
            if (addC2.checked) addC1.checked = false; else addC2.checked = true;
            syncCheckboxStyles(addC1, addC2);
        });
        syncCheckboxStyles(addC1, addC2);
    }

    if (editC1 && editC2) {
        editC1.addEventListener('click', function () {
            if (editC1.checked) editC2.checked = false; else editC1.checked = true;
            syncCheckboxStyles(editC1, editC2);
        });
        editC2.addEventListener('click', function () {
            if (editC2.checked) editC1.checked = false; else editC2.checked = true;
            syncCheckboxStyles(editC1, editC2);
        });
    }

    window.syncEditYearCheckboxes = function () {
        syncCheckboxStyles(editC1, editC2);
    };

    const existingYears = @json($capstoneYears->pluck('year'));

    const addForm = document.querySelector('#add_year_modal form');
    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            const yearInput = addForm.querySelector('input[name="year"]');
            const normalizedYear = (yearInput ? yearInput.value : '').replace(/-/g, '–').trim();
            if (existingYears.includes(normalizedYear)) {
                e.preventDefault();
                showToast(`Capstone year ${normalizedYear} already exists.`, true);
                return false;
            }
            if (!document.getElementById('add_year_c1').checked && !document.getElementById('add_year_c2').checked) {
                e.preventDefault();
                showToast('Please enable at least one capstone stage.', true);
                return false;
            }
        });
    }

    const editForm = document.getElementById('edit_year_form');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            const yearInput = editForm.querySelector('#edit_year_title');
            const normalizedYear = (yearInput ? yearInput.value : '').replace(/-/g, '–').trim();
            const originalYear = editForm.dataset.originalYear;
            if (normalizedYear !== originalYear && existingYears.includes(normalizedYear)) {
                e.preventDefault();
                showToast(`Capstone year ${normalizedYear} already exists.`, true);
                return false;
            }
            if (!document.getElementById('edit_year_c1').checked && !document.getElementById('edit_year_c2').checked) {
                e.preventDefault();
                showToast('Please enable at least one capstone stage.', true);
                return false;
            }
        });
    }
});

// ----- CREATE GROUP MODAL (ADMIN) -----
const sectionSelect = document.getElementById('sectionSelect');
const studentSelect = document.getElementById('studentSelect');
const addBtn = document.getElementById('addStudentsBtn');
const container = document.getElementById('selectedStudentsContainer');
const noMsg = document.getElementById('noStudentsMsg');
let idx = 0;

const studentSearchInput = document.getElementById('studentSearchInput');
if (studentSearchInput && studentSelect) {
    studentSearchInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        Array.from(studentSelect.options).forEach(opt => {
            if (opt.disabled) return;
            opt.style.display = (!query || opt.textContent.toLowerCase().includes(query)) ? '' : 'none';
        });
    });
}

if (sectionSelect) {
    sectionSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const sectionId = this.value;
        const sectionName = selectedOption ? selectedOption.dataset.name : '';
        studentSelect.innerHTML = '<option disabled>Loading...</option>';

        if (studentSearchInput) studentSearchInput.value = '';

        container.innerHTML = '';
        idx = 0;
        noMsg.style.display = 'block';

        if (!sectionId) {
            studentSelect.innerHTML = '<option disabled>Select a section first</option>';
            return;
        }
        fetch(`/admin/get-students/${encodeURIComponent(sectionName)}`)
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
        showToast('Student already added.', true);
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

if (addBtn) {
    addBtn.addEventListener('click', () => {
        Array.from(studentSelect.selectedOptions).forEach(o => {
            if (o.value) addRow(o.value, o.textContent);
        });
        studentSelect.selectedIndex = -1;
    });
}

// ---- EDIT GROUP ----
let editGroupIdx = 0;

function editGroupMemberRow(name, userId, role) {
    const membersContainer = document.getElementById('edit_group_members_container');
    if (membersContainer.querySelector(`input[value="${userId}"]`)) {
        showToast('Student already in team.', true);
        return;
    }
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 p-2 bg-[#faf8f4] rounded border border-[#e2dacf] flex-nowrap whitespace-nowrap';
    row.innerHTML = `<input type="hidden" name="students[${editGroupIdx}][user_id]" value="${userId}">
        <span class="flex-1 text-sm">${name} <span class="text-[#5b6375]">(${userId})</span></span>
        <select name="students[${editGroupIdx}][role]" class="form-select w-32" required>
            <option value="programmer" ${role === 'programmer' ? 'selected' : ''}>Programmer</option>
            <option value="designer" ${role === 'designer' ? 'selected' : ''}>Designer</option>
            <option value="researcher" ${role === 'researcher' ? 'selected' : ''}>Researcher</option>
        </select>
        <button type="button" class="edit-group-remove-student text-[#5b6375] hover:text-red-500 transition"><i class="fas fa-times"></i></button>`;
    membersContainer.appendChild(row);
    editGroupIdx++;
    row.querySelector('.edit-group-remove-student').addEventListener('click', () => row.remove());
}

function openEditGroupModal(groupId) {
    fetch(`/admin/get-group/${groupId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit_group_id').value = data.id;
            document.getElementById('edit_group_name').value = data.group_name;
            document.getElementById('edit_group_capstone_title').value = data.capstone_title ?? '';
            document.getElementById('edit_group_adviser').value = data.adviser_id;
            document.getElementById('edit_group_form').action = `/admin/update-group/${data.id}`;
            document.getElementById('edit_group_errors').classList.add('hidden');
            document.getElementById('edit_group_section_display').value = data.section_name ?? '—';

            editGroupIdx = 0;
            const membersContainer = document.getElementById('edit_group_members_container');
            membersContainer.innerHTML = '';
            (data.members || []).forEach(m => editGroupMemberRow(m.name, m.user_id, m.role));

            const editStudentSelect = document.getElementById('edit_group_student_select');
            editStudentSelect.innerHTML = '<option disabled>Loading...</option>';
            if (data.section_name) {
                fetch(`/admin/get-students/${encodeURIComponent(data.section_name)}`)
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
                    })
                    .catch(() => editStudentSelect.innerHTML = '<option disabled>Error loading students</option>');
            } else {
                editStudentSelect.innerHTML = '<option disabled>No section on file for this group</option>';
            }

            openModal('edit_group_modal');
        })
        .catch(() => showToast('Failed to load group.', true));
}

document.getElementById('edit_group_add_students_btn').addEventListener('click', () => {
    const sel = document.getElementById('edit_group_student_select');
    Array.from(sel.selectedOptions).forEach(o => {
        if (o.value) editGroupMemberRow(o.textContent.split(' (')[0].trim(), o.value, 'programmer');
    });
    sel.selectedIndex = -1;
});

document.getElementById('edit_group_form').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = this;
    document.getElementById('page-loader')?.classList.add('active');

    const errorsBox = document.getElementById('edit_group_errors');
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
            errorsBox.innerHTML = Object.values(data.errors).flat().join('<br>');
            errorsBox.classList.remove('hidden');
            document.getElementById('page-loader')?.classList.remove('active');
        } else if (data?.success) {
            toastAfterReload(data.message || 'Group updated successfully.', false);
            softReload();

        } else {
            showToast(data?.message || data?.error || 'Failed to update group.', true);
            document.getElementById('page-loader')?.classList.remove('active');
        }
    })
    .catch(() => {
        showToast('Failed to update group.', true);
        document.getElementById('page-loader')?.classList.remove('active');
    });
});

function regenerateRoomCode(roomId, btn) {
    if (!confirm('Regenerate this classroom\'s join code? The old code will stop working.')) return;
    fetch(`/admin/evaluation-rooms/${roomId}/regenerate-code`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = btn.previousElementSibling;
            badge.textContent = data.join_code;
            showToast('Code regenerated.');
        }
    })
    .catch(() => showToast('Failed to regenerate code.', true));
}

function updateCurrentSectionTeacher() {
    const select = document.getElementById('as_section_select');
    const selectedOption = select.options[select.selectedIndex];
    const teacherUserId = selectedOption.getAttribute('data-teacher-user-id');
    const teacherSelect = document.getElementById('as_teacher_select');
    const currentTeacherInput = document.getElementById('as_current_teacher');
    if (teacherUserId) {
        teacherSelect.value = teacherUserId;
        const matchingTeacherOption = Array.from(teacherSelect.options).find(opt => opt.value === teacherUserId);
        currentTeacherInput.value = matchingTeacherOption ? matchingTeacherOption.textContent : 'Assigned (ID: ' + teacherUserId + ')';
    } else {
        teacherSelect.value = 'none';
        currentTeacherInput.value = 'No Teacher Assigned';
    }
}

// ---- STUDENTS: PAGINATION + FILTER ----
let studentPageSize = 20;
let studentsPaginate = null;

function getVisibleStudentRows() {
    const tbody = document.getElementById('students-tbody');
    if (!tbody) return [];
    return Array.from(tbody.querySelectorAll('tr:not(.no-students-row)'))
        .filter(row => !row.classList.contains('filter-hidden'));
}

function initStudentsPagination() {
    const tbody = document.getElementById('students-tbody');
    if (!tbody) return null;

    let currentPage = 1;

    function update() {
        const visibleRows = getVisibleStudentRows();
        const totalVisible = visibleRows.length;
        const totalPages = Math.ceil(totalVisible / studentPageSize) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * studentPageSize;
        const end = Math.min(start + studentPageSize, totalVisible);

        visibleRows.forEach(row => row.style.display = 'none');
        for (let i = start; i < end; i++) visibleRows[i].style.display = '';

        document.getElementById('students-start').textContent = totalVisible === 0 ? 0 : start + 1;
        document.getElementById('students-end').textContent = end;
        document.getElementById('students-total').textContent = totalVisible;
        document.getElementById('students-prev').disabled = currentPage === 1;
        document.getElementById('students-next').disabled = currentPage === totalPages;
        document.getElementById('students-page-info').textContent = `Page ${currentPage} of ${totalPages}`;
    }

    function goTo(page) {
        const totalPages = Math.ceil(getVisibleStudentRows().length / studentPageSize) || 1;
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        update();
    }

    document.getElementById('students-prev').addEventListener('click', () => goTo(currentPage - 1));
    document.getElementById('students-next').addEventListener('click', () => goTo(currentPage + 1));

    update();
    return function rePaginate() { update(); };
}

// ---- GROUPS: PAGINATION + SECTION FILTER ----
let groupPageSize = 10;
let groupsPaginate = null;

function getVisibleGroupRows() {
    const tbody = document.getElementById('groups-tbody');
    if (!tbody) return [];
    return Array.from(tbody.querySelectorAll('tr.groups-row'))
        .filter(row => !row.classList.contains('filter-hidden'));
}

function initGroupsPagination() {
    const tbody = document.getElementById('groups-tbody');
    if (!tbody) return null;

    let currentPage = 1;

    function update() {
        const visibleRows = getVisibleGroupRows();
        const totalVisible = visibleRows.length;
        const totalPages = Math.ceil(totalVisible / groupPageSize) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * groupPageSize;
        const end = Math.min(start + groupPageSize, totalVisible);

        visibleRows.forEach(row => row.style.display = 'none');
        for (let i = start; i < end; i++) visibleRows[i].style.display = '';

        document.getElementById('groups-start').textContent = totalVisible === 0 ? 0 : start + 1;
        document.getElementById('groups-end').textContent = end;
        document.getElementById('groups-total').textContent = totalVisible;
        document.getElementById('groups-prev').disabled = currentPage === 1;
        document.getElementById('groups-next').disabled = currentPage === totalPages;
        document.getElementById('groups-page-info').textContent = `Page ${currentPage} of ${totalPages}`;
    }

    function goTo(page) {
        const totalPages = Math.ceil(getVisibleGroupRows().length / groupPageSize) || 1;
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        update();
    }

    document.getElementById('groups-prev').addEventListener('click', () => goTo(currentPage - 1));
    document.getElementById('groups-next').addEventListener('click', () => goTo(currentPage + 1));

    update();
    return function rePaginate() { update(); };
}

function applyGroupFilters() {
    const sectionFilter = document.getElementById('admin-group-section-filter');
    const searchInput = document.getElementById('group-search');
    const selectedSection = sectionFilter.value;
    const query = searchInput ? searchInput.value.trim().toLowerCase() : '';

    const rows = document.querySelectorAll('#groups-tbody tr.groups-row');
    let anyVisible = false;

    rows.forEach(row => {
        const rowSection = row.dataset.section || '';
        const rowText = row.textContent.toLowerCase();
        let show = true;

        if (selectedSection !== 'All' && normText(rowSection) !== normText(selectedSection)) show = false;
        if (show && query && !rowText.includes(query)) show = false;

        if (show) {
            row.classList.remove('filter-hidden');
            anyVisible = true;
        } else {
            row.classList.add('filter-hidden');
            row.style.display = 'none';
        }
    });

    let noRow = document.querySelector('#groups-tbody .no-groups-match-row');
    if (!anyVisible && rows.length > 0) {
        if (!noRow) {
            noRow = document.createElement('tr');
            noRow.className = 'no-groups-match-row';
            noRow.innerHTML = '<td colspan="9" class="py-6 text-center text-[#5b6375]">No groups match the selected section.</td>';
            document.querySelector('#groups-tbody').appendChild(noRow);
        }
        noRow.style.display = '';
    } else if (noRow) {
        noRow.style.display = 'none';
    }

    if (groupsPaginate) groupsPaginate();
}

function populateGroupFilter(selectedSection) {
    const groupSelect = document.getElementById('admin-student-group-filter');
    groupSelect.innerHTML = '<option value="All">All Groups</option>';
    if (selectedSection === 'All') {
        groupSelect.disabled = true;
        return;
    }
    const groupsInSection = groupsDataJs.filter(g => normText(g.section_name) === normText(selectedSection));
    if (groupsInSection.length === 0) {
        groupSelect.disabled = true;
        return;
    }
    groupsInSection.forEach(g => {
        const opt = document.createElement('option');
        opt.value = g.name;
        opt.textContent = g.name;
        groupSelect.appendChild(opt);
    });
    groupSelect.disabled = false;
}

function applyStudentFilters() {
    const sectionFilter = document.getElementById('admin-student-section-filter');
    const groupFilter = document.getElementById('admin-student-group-filter');
    const searchInput = document.getElementById('student-search');
    const selectedSection = sectionFilter.value;
    const selectedGroup = groupFilter.value;
    const query = searchInput ? searchInput.value.trim().toLowerCase() : '';

    const rows = document.querySelectorAll('#students-tbody tr:not(.no-students-row)');
    let anyVisible = false;

    rows.forEach(row => {
        const rowSection = row.dataset.section || '';
        const rowGroup = row.dataset.group || '';
        const rowText = row.textContent.toLowerCase();
        let show = true;

        if (selectedSection !== 'All' && normText(rowSection) !== normText(selectedSection)) show = false;
        if (show && selectedGroup !== 'All' && normText(rowGroup) !== normText(selectedGroup)) show = false;
        if (show && query && !rowText.includes(query)) show = false;

        if (show) {
            row.classList.remove('filter-hidden');
            anyVisible = true;
        } else {
            row.classList.add('filter-hidden');
            row.style.display = 'none';
        }
    });

    let noRow = document.querySelector('#students-tbody .no-students-row');
    if (!anyVisible) {
        if (!noRow) {
            noRow = document.createElement('tr');
            noRow.className = 'no-students-row';
            noRow.innerHTML = '<td colspan="6" class="py-6 text-center text-[#5b6375]">No students match the selected filters.</td>';
            document.querySelector('#students-tbody').appendChild(noRow);
        }
        noRow.style.display = '';
    } else if (noRow) {
        noRow.style.display = 'none';
    }

    if (studentsPaginate) studentsPaginate();
}

document.addEventListener('DOMContentLoaded', function () {
    studentsPaginate = initStudentsPagination();

    const studentsPageSizeSel = document.getElementById('students-page-size');
    if (studentsPageSizeSel) {
        studentsPageSizeSel.addEventListener('change', function () {
            studentPageSize = parseInt(this.value, 10) || 20;
            if (studentsPaginate) studentsPaginate();
        });
    }

    const sectionFilter = document.getElementById('admin-student-section-filter');
    if (sectionFilter) {
        sectionFilter.addEventListener('change', function () {
            populateGroupFilter(this.value);
            applyStudentFilters();
        });
        populateGroupFilter(sectionFilter.value);
    }

    const groupFilter = document.getElementById('admin-student-group-filter');
    if (groupFilter) groupFilter.addEventListener('change', applyStudentFilters);

    document.querySelectorAll('.sg-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            setTimeout(() => {
                if (this.dataset.view === 'students') {
                    if (studentsPaginate) studentsPaginate();
                    applyStudentFilters();
                }
            }, 50);
        });
    });

    applyStudentFilters();

    groupsPaginate = initGroupsPagination();

    const groupsPageSizeSel = document.getElementById('groups-page-size');
    if (groupsPageSizeSel) {
        groupsPageSizeSel.addEventListener('change', function () {
            groupPageSize = parseInt(this.value, 10) || 10;
            if (groupsPaginate) groupsPaginate();
        });
    }

    const groupSectionFilter = document.getElementById('admin-group-section-filter');
    if (groupSectionFilter) groupSectionFilter.addEventListener('change', applyGroupFilters);
    applyGroupFilters();
});

window.moveItemUp = function (btn) {
    const item = btn.closest('.reorder-item');
    const prev = item.previousElementSibling;
    if (prev) item.parentNode.insertBefore(item, prev);
};

window.moveItemDown = function (btn) {
    const item = btn.closest('.reorder-item');
    const next = item.nextElementSibling;
    if (next) item.parentNode.insertBefore(next, item);
};

window.showStage = function (stageType) {
    document.querySelectorAll('.milestone-item, .rubric-item').forEach(el => {
        const type = el.dataset.stageType;
        el.style.display = (stageType === 'all' || String(type) === String(stageType)) ? '' : 'none';
    });
};

function suggestGroupName() {
    const usedNumbers = groupsDataJs
        .map(g => g.name)
        .map(name => {
            const match = name.match(/^Group (\d+)$/i);
            return match ? parseInt(match[1], 10) : null;
        })
        .filter(num => num !== null && !isNaN(num));

    let candidate = 1;
    while (usedNumbers.includes(candidate)) candidate++;
    return `Group ${candidate}`;
}

function refreshLiveGroupProgress() {
    document.querySelectorAll('.live-group-row[data-group-id]').forEach(row => {
        const groupId = row.dataset.groupId;
        if (!groupId) return;

        fetch(`/admin/get-group-progress/${groupId}`)
            .then(r => r.json())
            .then(data => {
                const pct = data.overall_progress ?? 0;
                const color = pct >= 70 ? '#1e6b3a' : (pct >= 40 ? '#b88d3a' : '#a12b2b');
                const status = pct >= 70 ? 'On Track' : (pct >= 40 ? 'At Risk' : 'Delayed');

                row.dataset.progress = pct;

                const bar = row.querySelector('.live-progress-bar');
                const pctEl = row.querySelector('.live-progress-pct');
                const badge = row.querySelector('.live-progress-badge');

                if (bar) { bar.style.width = pct + '%'; bar.style.background = color; }
                if (pctEl) { pctEl.textContent = pct + '%'; pctEl.style.color = color; }
                if (badge) {
                    badge.textContent = status;
                    badge.style.background = color + '20';
                    badge.style.color = color;
                    badge.style.border = '1px solid ' + color + '40';
                }
            })
            .catch(() => {});
    });
}
document.addEventListener('DOMContentLoaded', refreshLiveGroupProgress);

function escapeXml(s) {
    return String(s ?? '').replace(/[<>&"']/g, c => ({
        '<': '&lt;', '>': '&gt;', '&': '&amp;', '"': '&quot;', "'": '&apos;'
    })[c]);
}

function exportGroupsToExcel() {
    const rows = document.querySelectorAll('#groups-tbody tr.groups-row');
    if (!rows.length) {
        showToast('No groups to export.', true);
        return;
    }

    let html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head><body>';
    html += '<table border="1" cellspacing="0" cellpadding="6">';
    html += '<thead><tr>';
    html += '<th style="background:#0a1428;color:#f0e0b0;font-weight:bold;">Capstone Title</th>';
    html += '<th style="background:#0a1428;color:#f0e0b0;font-weight:bold;">Section</th>';
    html += '<th style="background:#0a1428;color:#f0e0b0;font-weight:bold;">Adviser</th>';
    html += '<th style="background:#0a1428;color:#f0e0b0;font-weight:bold;">Team Members</th>';
    html += '</tr></thead><tbody>';

    let exported = 0;
    rows.forEach(row => {
        if (row.classList.contains('filter-hidden')) return;

        const cells = row.querySelectorAll('td');
        const capstoneTitle = (cells[1]?.textContent || '').trim();
        const section = (cells[2]?.textContent || '').trim();
        const adviser = (cells[3]?.textContent || '').trim();

        const memberSpans = cells[4]?.querySelectorAll('.text-xs.font-medium') || [];
        const members = Array.from(memberSpans).map(el => el.textContent.trim()).filter(Boolean).join(', ');

        html += '<tr>';
        html += `<td>${escapeXml(capstoneTitle)}</td>`;
        html += `<td>${escapeXml(section)}</td>`;
        html += `<td>${escapeXml(adviser)}</td>`;
        html += `<td>${escapeXml(members)}</td>`;
        html += '</tr>';
        exported++;
    });

    html += '</tbody></table></body></html>';

    if (exported === 0) {
        showToast('No groups match the current filter.', true);
        return;
    }

    const blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    const stamp = new Date().toISOString().slice(0, 10);
    a.href = url;
    a.download = `capstone_groups_${stamp}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(() => URL.revokeObjectURL(url), 500);

    showToast(`${exported} group(s) exported successfully.`);
}

function openProgressDetailModal(statusLabel) {
    const thresholds = {
        'On Track':        (p) => p >= 70,
        'At Risk':         (p) => p >= 40 && p < 70,
        'Needs Attention': (p) => p < 40
    };
    const colors = {
        'On Track':        '#1e6b3a',
        'At Risk':         '#b88d3a',
        'Needs Attention': '#a12b2b'
    };

    const check = thresholds[statusLabel];
    if (!check) return;
    const color = colors[statusLabel];

    const rows = document.querySelectorAll('#progress-section .live-group-row');
    const groups = [];
    rows.forEach(row => {
        const pct = parseInt(row.dataset.progress, 10) || 0;
        if (check(pct)) {
            groups.push({
                name: row.dataset.groupName || row.querySelector('span')?.textContent.trim() || '—',
                section: row.dataset.section || '—',
                progress: pct
            });
        }
    });

    groups.sort((a, b) => statusLabel === 'On Track' ? b.progress - a.progress : a.progress - b.progress);

    document.getElementById('progress_modal_title').textContent = `${statusLabel} Groups`;
    document.getElementById('progress_modal_summary').innerHTML =
        `<strong class="text-[#0a1428]">${groups.length}</strong> group(s) currently in this category.` +
        (statusLabel === 'On Track' ? ' Keep up the good work!' :
         statusLabel === 'At Risk'  ? ' These groups may need a nudge.' :
                                      ' These groups require immediate attention.');

    const tbody = document.getElementById('progress_modal_tbody');
    tbody.innerHTML = '';

    if (groups.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="p-8 text-center text-[#5b6375]">
            <i class="fa-regular fa-folder-open text-2xl mb-2 block"></i>
            No groups in this category.</td></tr>`;
    } else {
        groups.forEach(g => {
            const tr = document.createElement('tr');
            tr.className = 'text-sm hover:bg-[#faf8f4]/60 transition';
            tr.innerHTML = `
                <td class="p-3 pl-4 font-semibold text-[#0a1428]">${g.name}</td>
                <td class="p-3 text-[#3d4450]">${g.section}</td>
                <td class="p-3">
                    <div class="flex items-center gap-2">
                        <div class="progress-bar-bg h-1.5 w-20">
                            <div class="progress-fill h-full" style="width:${g.progress}%; background:${color};"></div>
                        </div>
                        <span class="text-xs font-bold" style="color:${color};">${g.progress}%</span>
                    </div>
                </td>
                <td class="p-3 pr-4">
                    <span class="badge" style="background:${color}20; color:${color}; border:1px solid ${color}40;">
                        ${statusLabel}
                    </span>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    openModal('progress_detail_modal');
}

window.exportGroupsToExcel = exportGroupsToExcel;
window.openProgressDetailModal = openProgressDetailModal;

// ═══════════════════════════════════════════════════════════════════════
// TOAST + SOFT RELOAD + GLOBAL AJAX FORM HANDLER
// ═══════════════════════════════════════════════════════════════════════
(function () {
    const PENDING_KEY = 'pendingToast';
    let hideTimer = null;

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const msg = document.getElementById('toastMessage');
        if (!toast || !msg) return;
        const icon = toast.querySelector('.toast-content > i');
        msg.textContent = message;
        toast.classList.toggle('is-error', !!isError);
        if (icon) icon.className = isError ? 'fas fa-circle-exclamation' : 'fas fa-check-circle';
        toast.classList.add('show');
        clearTimeout(hideTimer);
        hideTimer = setTimeout(hideToast, isError ? 6000 : 3500);
    }
    function hideToast() {
        document.getElementById('toast')?.classList.remove('show');
    }
    function toastAfterReload(message, isError = false) {
        try { sessionStorage.setItem(PENDING_KEY, JSON.stringify({ message, isError })); } catch (_) {}
    }
    function softReload() {
        document.getElementById('page-loader')?.classList.add('active');
        setTimeout(() => window.location.reload(), 150);
    }
    function readFlash(doc) {
        const get = n => doc.querySelector(`meta[name="flash-${n}"]`)?.content || '';
        let errors = [];
        try { errors = JSON.parse(get('errors') || '[]'); } catch (_) {}
        return { success: get('success'), error: get('error'), errors };
    }

    function install() {
        // assigned here so they win over any older versions defined in app.js
        window.showToast = showToast;
        window.hideToast = hideToast;
        window.toastAfterReload = toastAfterReload;
        window.softReload = softReload;
    }
    install();
window.addEventListener('pageshow', e => {
    if (e.persisted) document.getElementById('page-loader')?.classList.remove('active');
});
    document.addEventListener('DOMContentLoaded', () => {
        install();
        let shown = false;
        try {
            const raw = sessionStorage.getItem(PENDING_KEY);
            if (raw) {
                sessionStorage.removeItem(PENDING_KEY);
                const p = JSON.parse(raw);
                setTimeout(() => showToast(p.message, p.isError), 150);
                shown = true;
            }
        } catch (_) {}
        // normal (non-AJAX) page loads that carry Laravel flash data
        if (!shown) {
            const f = readFlash(document);
            if (f.errors.length) setTimeout(() => showToast(f.errors[0], true), 150);
            else if (f.error) setTimeout(() => showToast(f.error, true), 150);
            else if (f.success) setTimeout(() => showToast(f.success, false), 150);
        }
    });

    // ---------- form errors ----------
    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[c]);
    }
    function clearErrors(form) {
        form.querySelectorAll('.ajax-form-errors').forEach(el => el.remove());
    }
    function showErrors(form, messages) {
        const list = (Array.isArray(messages) ? messages : [messages]).filter(Boolean);
        if (!list.length) return;
        const box = document.createElement('div');
        box.className = 'ajax-form-errors bg-red-50 border border-red-300 text-red-700 p-3 rounded-lg text-sm mb-3';
        box.innerHTML = '<div class="font-semibold mb-1"><i class="fas fa-exclamation-circle mr-1"></i>Please fix the following:</div>' +
            '<ul class="list-disc list-inside space-y-0.5">' + list.map(m => `<li>${esc(m)}</li>`).join('') + '</ul>';
        const accent = form.querySelector('.modal-accent');
        if (accent) accent.insertAdjacentElement('afterend', box);
        else form.insertBefore(box, form.firstChild);
    }

    // ---------- global submit interceptor ----------
    document.addEventListener('submit', async function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (e.defaultPrevented) return;                 // forms with their own handler
        if (form.id === 'logout-form') return;
        if (form.dataset.ajax === 'false') return;
        if (form.hasAttribute('data-ajax-handled')) return;

        e.preventDefault();
        clearErrors(form);

        // Keep the submit button unchanged; show the existing page-level loading modal
        // while the request writes the form data to the database.
        document.getElementById('page-loader')?.classList.add('active');

        let succeeded = false;
        const fail = (msg, fieldErrors) => {
            if (fieldErrors && fieldErrors.length) showErrors(form, fieldErrors);
            showToast(msg || 'Something went wrong. Please try again.', true);
        };
        const success = (msg) => {
            succeeded = true;
            toastAfterReload(msg || 'Changes saved successfully.', false);
            softReload();
        };

        try {
            const csrf = form.querySelector('input[name="_token"]')?.value
                || document.querySelector('meta[name="csrf-token"]')?.content;

            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form),
                credentials: 'same-origin'
            });

            // session expired / not logged in
            if (res.status === 401 || (res.redirected && /\/login\b/.test(new URL(res.url).pathname))) {
                window.location.href = res.redirected ? res.url : '/login';
                succeeded = true;
                return;
            }
            if (res.status === 419) {
                fail('Your session expired. Please refresh the page and try again.');
                return;
            }
            if (res.status === 403) {
                fail('You are not allowed to do that.');
                return;
            }

            const ct = res.headers.get('content-type') || '';

            if (ct.includes('application/json')) {
                const data = await res.json().catch(() => null);
                if (res.status === 422 && data?.errors) {
                    const list = Object.values(data.errors).flat();
                    fail(list[0] || data.message, list);
                } else if (res.ok && data && !data.errors && !data.error && data.success !== false) {
                    success(data.message);
                } else {
                    fail(data?.message || data?.error);
                }
                return;
            }

            // HTML response = Laravel redirect was followed; read the flash data from the page
            if (!res.ok) {
                fail(`Server error (${res.status}). Please try again.`);
                return;
            }
            const html = await res.text();
            const flash = readFlash(new DOMParser().parseFromString(html, 'text/html'));

            if (flash.errors.length) fail(flash.errors[0], flash.errors);
            else if (flash.error)    fail(flash.error);
            else                     success(flash.success);

        } catch (err) {
            fail('Network error. Please check your connection and try again.');
        } finally {
            if (!succeeded) document.getElementById('page-loader')?.classList.remove('active');
        }
    }, false);
})();



</script>
 
</body>
</html> 
