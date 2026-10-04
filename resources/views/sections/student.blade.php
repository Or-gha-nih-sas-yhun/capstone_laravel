<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>Capstone Tracker | Student Dashboard</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="icon" type="image/jpeg" href="{{ asset('pictures/favicon.jpg') }}">
    <script src="/js/app.js" defer></script>
    <style>
        /* ── ROOT ── */
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

        * {
            font-family: 'DM Sans', sans-serif;
        }

        body {
            background: linear-gradient(145deg, var(--cream) 0%, #f2ede2 100%);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: var(--cream-dark);
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-dark);
        }
        ::selection {
            background: var(--gold);
            color: var(--navy);
        }

        .transition-smooth {
            transition: var(--transition);
        }
        .section-card {
            animation: fadeInUp 0.35s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            position: relative;
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
        .content-card:hover .card-accent {
            opacity: 1;
        }

        .progress-bar-bg {
            background: #e8e3d7;
            border-radius: 999px;
            overflow: hidden;
        }
        .progress-fill {
            border-radius: 999px;
            transition: width 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .badge {
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.02em;
        }
        .badge-gold {
            background-color: rgba(214, 177, 92, 0.15);
            color: #8b6914;
            border: 1px solid rgba(214, 177, 92, 0.3);
        }
        .badge-navy {
            background-color: rgba(10, 20, 40, 0.08);
            color: var(--navy);
            border: 1px solid rgba(10, 20, 40, 0.15);
        }
        .badge-muted {
            background-color: #f0ece4;
            color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .badge-green {
            background-color: #e6f4ea;
            color: #1e6b3a;
            border: 1px solid #b7dfc5;
        }
        .badge-amber {
            background-color: #fef7e6;
            color: #8a5d0b;
            border: 1px solid #f5d78a;
        }

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
            box-shadow: 0 2px 8px rgba(10, 20, 40, 0.12);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-primary:hover {
            background: var(--navy-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(10, 20, 40, 0.2);
            color: #fff;
        }
        .btn-primary:active {
            transform: scale(0.97);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
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
        .btn-outline:hover {
            background: var(--gold);
            color: var(--navy);
            box-shadow: 0 4px 14px rgba(214, 177, 92, 0.3);
        }
        .btn-outline:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: transparent;
            color: var(--navy);
            box-shadow: none;
        }

        input,
        select {
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
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
        .form-input {
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
        .form-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-glow);
            background: var(--white);
        }
        .form-input:disabled {
            background: #f0ece4;
            color: var(--text-muted);
        }

        .locked-card {
            filter: grayscale(0.35);
            opacity: 0.75;
        }

        /* ── MOBILE BOTTOM NAV ── */
        .mobile-bottom-nav {
            display: none !important;
        }
        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: flex !important;
                background: rgba(10, 20, 40, 0.96);
                backdrop-filter: blur(12px);
                border-top: 1px solid rgba(214, 177, 92, 0.3);
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
                padding-bottom: env(safe-area-inset-bottom, 0px);
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
            .mobile-bottom-nav::-webkit-scrollbar {
                display: none; /* Safari and Chrome */
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

        h1,
        h2,
        h3,
        h4,
        .serif-heading {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            letter-spacing: -0.01em;
        }
        h1 {
            font-size: 2rem;
            color: var(--navy);
        }
        h2 {
            font-size: 1.5rem;
            color: var(--navy);
        }
        h3 {
            font-size: 1.2rem;
            color: var(--navy);
        }
        .gold-accent-line {
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-dark));
            border-radius: 3px;
            margin-top: 0.4rem;
        }

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
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
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
            border-bottom: 1px solid rgba(226, 218, 207, 0.6);
        }
        .info-row:last-child {
            border-bottom: none;
        }
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
        .security-note li {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }
        .security-note li i {
            color: var(--gold-dark);
            margin-top: 0.2rem;
            font-size: 0.75rem;
        }
        #password_strength_bar {
            transition: width 0.3s ease, background 0.3s ease;
        }

        /* ── TOAST ── */
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
        .toast-container.show {
            transform: translateX(0);
            opacity: 1;
        }
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
        .toast-content i {
            color: var(--gold);
            font-size: 1.25rem;
        }
        .toast-content .toast-message {
            font-size: 0.9rem;
            color: var(--text);
            font-weight: 500;
            flex: 1;
        }
        .toast-content .toast-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.2rem;
            transition: color 0.2s;
        }
        .toast-content .toast-close:hover {
            color: var(--navy);
        }

        /* ── MILESTONE CARD (for reference) ── */
        .milestone-card {
            background: var(--white);
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            padding: 1rem 1.25rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
        }
        .milestone-card:hover {
            border-color: var(--gold);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .milestone-card .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .milestone-card .status-icon.completed {
            background: #e6f4ea;
            color: #1e6b3a;
            border: 2px solid #1e6b3a;
        }
        .milestone-card .status-icon.next {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(214, 177, 92, 0.3);
        }
        .milestone-card .status-icon.pending {
            background: #f0ece4;
            color: #b8b0a0;
            border: 2px solid #d6cfc2;
        }
        .milestone-card .milestone-info {
            flex: 1;
            margin: 0 1rem;
        }
        .milestone-card .milestone-info h5 {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }
        .milestone-card .milestone-info p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0.2rem 0 0;
        }
        .milestone-card .badge-next {
            background: var(--gold);
            color: var(--navy);
            font-weight: 600;
            font-size: 0.7rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            letter-spacing: 0.03em;
        }
        .next-milestone-box {
            background: linear-gradient(135deg, #faf8f4 0%, #f5f0e8 100%);
            border: 1px solid var(--gold);
            border-radius: 0.85rem;
            padding: 1.25rem;
            box-shadow: 0 8px 24px rgba(214, 177, 92, 0.12);
        }
        .next-milestone-box .icon-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gold);
            color: var(--navy);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            margin-bottom: 0.75rem;
        }
        .next-milestone-box .icon-badge i {
            font-size: 0.8rem;
        }
        .next-milestone-box h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 0.25rem;
        }
        .next-milestone-box p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0 0 0.75rem;
        }
        .next-milestone-box .dates {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            font-size: 0.8rem;
            color: var(--gold-dark);
        }
        .next-milestone-box .dates span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .eval-item {
            background: var(--white);
            border-radius: 0.65rem;
            border: 1px solid var(--border);
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .eval-item:hover {
            border-color: var(--gold);
            box-shadow: var(--shadow-sm);
        }
        .eval-item .eval-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .eval-item .eval-score {
            font-weight: 700;
            color: #1e6b3a;
            background: #e6f4ea;
            padding: 0.2rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
        }
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

        /* ── TIMELINE TABLE ── */
        .timeline-wrapper {
            background: var(--white);
            border-radius: 1rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .timeline-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            table-layout: fixed;
        }

        .timeline-table th,
        .timeline-table td {
            padding: 0.85rem 1rem;
            vertical-align: top;
            text-align: left;
            border-bottom: 1px solid rgba(226, 218, 207, 0.6);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .timeline-table thead th {
            background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
            color: var(--gold-light);
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 0.68rem;
            padding: 0.9rem 1rem;
            border-bottom: none;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        /* column widths */
        .timeline-table td:first-child {
            white-space: nowrap;
            font-weight: 600;
            color: var(--navy);
            width: 16%;
            font-size: 0.75rem;
        }
        .timeline-table td:nth-child(2) {
            width: 44%;
        }
        .timeline-table td:nth-child(3) {
            width: 40%;
        }

        .timeline-table tbody tr:last-child td {
            border-bottom: none;
        }
        .timeline-table tbody tr.milestone-row {
            transition: background 0.15s ease;
        }
        .timeline-table tbody tr.milestone-row:hover td {
            background: rgba(214, 177, 92, 0.05);
        }
        .timeline-table tr.row-completed td {
            background: #f4faf6;
        }
        .timeline-table tr.row-completed:hover td {
            background: #eef7f1;
        }
        .timeline-table tr.row-next td {
            background: #fdf9ef;
        }
        .timeline-table tr.row-next:hover td {
            background: #fbf3de;
        }

        /* Stage divider */
        .timeline-table .stage-divider td {
            background: linear-gradient(90deg, var(--gold-dark) 0%, var(--gold) 100%);
            color: var(--navy);
            padding: 0;
            border-bottom: none;
        }
        .stage-divider-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 1.1rem;
            cursor: pointer;
            user-select: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 0.75rem;
        }
        .stage-divider-inner span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .stage-divider-inner .stage-count {
            font-weight: 500;
            text-transform: none;
            letter-spacing: normal;
            font-size: 0.68rem;
            opacity: 0.85;
            margin-left: 0.4rem;
        }
        .stage-toggle-btn {
            background: rgba(10, 20, 40, 0.12);
            border: none;
            color: var(--navy);
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.25s ease;
        }
        .stage-divider-inner:hover .stage-toggle-btn {
            background: rgba(10, 20, 40, 0.22);
        }
        .stage-toggle-btn i {
            transition: transform 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .stage-toggle-btn.collapsed i {
            transform: rotate(180deg);
        }

        .task-title {
            font-weight: 600;
            color: var(--text);
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.83rem;
        }
        .task-desc {
            font-size: 0.74rem;
            color: var(--text-muted);
            display: block;
            line-height: 1.45;
        }
        .task-status {
            display: inline-block;
            margin-top: 0.5rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
        }
        .task-status.completed {
            background: #e6f4ea;
            color: #1e6b3a;
            border: 1px solid #b7dfc5;
        }
        .task-status.next {
            background: var(--gold);
            color: var(--navy);
        }
        .task-status.pending {
            background: #f0ece4;
            color: #8b8477;
            border: 1px solid var(--border);
        }

        .remark-summary {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .remark-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            width: fit-content;
        }
        .remark-status-badge.on-time {
            background: #e6f4ea;
            color: #1e6b3a;
            border: 1px solid #b7dfc5;
        }
        .remark-status-badge.late {
            background: #fbe9e7;
            color: #a12b2b;
            border: 1px solid #f3c1ba;
        }
        .remark-status-badge.early {
            background: #eaf1fb;
            color: #1e4e8b;
            border: 1px solid #c1d6f3;
        }
        .remark-deduction {
            font-size: 0.7rem;
            color: #a12b2b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .remark-attendance {
            font-size: 0.76rem;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .remark-attendance i {
            color: var(--gold-dark);
        }
        .absence-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.4rem;
            font-size: 0.7rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .absence-table thead th {
            background: #faf2df;
            color: var(--gold-dark);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 0.4rem 0.6rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .absence-table td {
            padding: 0.4rem 0.6rem;
            border-bottom: 1px solid rgba(226, 218, 207, 0.5);
            color: var(--text);
        }
        .absence-table tr:last-child td {
            border-bottom: none;
        }
        .absence-table td:first-child {
            width: 1.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }
        .remark-feedback {
            background: #faf8f4;
            border: 1px dashed var(--border);
            border-radius: 0.5rem;
            padding: 0.5rem 0.65rem;
            margin-top: 0.4rem;
            font-style: italic;
            font-size: 0.74rem;
            color: var(--text);
            line-height: 1.4;
        }
        .remark-empty {
            color: #b8b0a0;
            font-style: italic;
            font-size: 0.75rem;
        }

        /* ── MOBILE RESPONSIVE OVERRIDES ── */
        @media (max-width: 768px) {
            .timeline-wrapper {
                border-radius: 0.75rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .timeline-table {
                font-size: 0.7rem;
                table-layout: fixed;
                min-width: 0; /* allow shrinking */
                width: 100%;
            }

            .timeline-table th,
            .timeline-table td {
                padding: 0.4rem 0.4rem;
            }

            .timeline-table thead th {
                font-size: 0.55rem;
                padding: 0.4rem 0.4rem;
            }

            /* narrower columns to fit all three */
            .timeline-table td:first-child {
                width: 18%;
                min-width: 60px;
                font-size: 0.6rem;
                white-space: nowrap;
            }
            .timeline-table td:nth-child(2) {
                width: 40%;
                min-width: 100px;
            }
            .timeline-table td:nth-child(3) {
                width: 42%;
                min-width: 80px;
            }

            .stage-divider-inner {
                font-size: 0.6rem;
                padding: 0.4rem 0.5rem;
            }
            .stage-divider-inner .stage-count {
                font-size: 0.55rem;
            }
            .task-title {
                font-size: 0.7rem;
            }
            .task-desc {
                font-size: 0.6rem;
            }
            .task-status {
                font-size: 0.5rem;
                padding: 0.1rem 0.4rem;
                margin-top: 0.2rem;
            }
            .remark-status-badge {
                font-size: 0.55rem;
                padding: 0.1rem 0.4rem;
            }
            .remark-deduction {
                font-size: 0.55rem;
            }
            .remark-attendance {
                font-size: 0.6rem;
            }
            .absence-table {
                font-size: 0.5rem;
            }
            .absence-table thead th {
                font-size: 0.45rem;
                padding: 0.2rem 0.3rem;
            }
            .absence-table td {
                padding: 0.2rem 0.3rem;
            }
            .remark-feedback {
                font-size: 0.55rem;
                padding: 0.2rem 0.3rem;
            }
            .remark-empty {
                font-size: 0.55rem;
            }
        }

        @media (max-width: 480px) {
            .timeline-table {
                font-size: 0.6rem;
                min-width: 0;
            }
            .timeline-table th,
            .timeline-table td {
                padding: 0.25rem 0.25rem;
            }
            .timeline-table td:first-child {
                min-width: 50px;
                font-size: 0.5rem;
            }
            .timeline-table td:nth-child(2) {
                min-width: 80px;
            }
            .timeline-table td:nth-child(3) {
                min-width: 70px;
            }
            .task-title {
                font-size: 0.6rem;
            }
            .task-desc {
                font-size: 0.5rem;
            }
            .task-status {
                font-size: 0.45rem;
                padding: 0.05rem 0.3rem;
            }
            .remark-status-badge {
                font-size: 0.45rem;
                padding: 0.05rem 0.3rem;
            }
            .remark-deduction {
                font-size: 0.45rem;
            }
            .remark-attendance {
                font-size: 0.5rem;
            }
            .remark-empty {
                font-size: 0.5rem;
            }
            /* hide detailed tables on very small screens to save space */
            .absence-table,
            .remark-feedback {
                display: none !important;
            }
        }

        /* ── MODALS ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5,16,33,0.55);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 1rem;
        }
        .modal-overlay.active {
            display: flex !important;
        }
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
        .modal-box.wide {
            max-width: 44rem;
        }
        .modal-box .modal-accent {
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            border-radius: 3px;
            margin-bottom: 1.25rem;
        }


/* ═══════════════════════════════════════════════════════════════
   RECOMMENDATION SHEET — SCREEN (mirrors print layout, same as teacher)
   ═══════════════════════════════════════════════════════════════ */
@media screen {
    .recommendation-sheet-modal {
        container-type: inline-size;
        box-sizing: border-box;
        display: block;
        width: 100%;
        max-width: calc(8.5in + 3rem) !important;
        min-height: 0;
        max-height: 92vh;
        padding: 1.5rem !important;
        background: #e9e5db;
    }

    .recommendation-sheet-modal .recommendation-document {
        --in: calc(100cqw / 8.5);
        --pt: calc(var(--in) / 72);

        box-sizing: border-box;
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        aspect-ratio: 8.5 / 11;
        overflow: hidden;
        padding: calc(var(--in) * 1.1) calc(var(--in) * 1.25) calc(var(--in) * 1.15);
        background: #fff;
        border: 0;
        border-radius: 0;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
        text-align: center;
        font-family: 'Times New Roman', serif;
        color: #1a1a1a;
    }

    .recommendation-sheet-modal .rec-header-image {
        margin: 0 0 calc(var(--in) * 0.22);
        text-align: center;
    }
    .recommendation-sheet-modal .rec-header-image img {
        display: block;
        margin: 0 auto;
        width: auto;
        max-width: calc(var(--in) * 5.9);
        max-height: calc(var(--in) * 0.85);
        object-fit: contain;
    }

    .recommendation-sheet-modal .rec-title {
        font-family: 'Times New Roman', serif;
        font-size: calc(var(--pt) * 18);
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0.22em;
        text-align: center;
        color: #0a1428;
        border-bottom: 2px solid #0a1428;
        padding-bottom: calc(var(--in) * 0.08);
        margin: 0 auto calc(var(--in) * 0.36);
        max-width: 90%;
    }

    .recommendation-sheet-modal .rec-body {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0;
        max-width: calc(var(--in) * 6.4);
        margin: 0 auto;
        font-family: 'Times New Roman', serif;
    }
    .recommendation-sheet-modal .rec-body p {
        font-size: calc(var(--pt) * 12);
        line-height: 1.75;
        margin: 0 0 calc(var(--in) * 0.16);
        text-align: center;
        color: #1a1a1a;
    }

    .recommendation-sheet-modal .rec-capstone-title {
        font-family: 'Times New Roman', serif;
        font-size: calc(var(--pt) * 15.5);
        font-weight: 700;
        font-style: italic;
        line-height: 1.32;
        letter-spacing: 0.01em;
        max-width: calc(var(--in) * 6);
        margin: calc(var(--in) * 0.14) auto calc(var(--in) * 0.2);
        padding: calc(var(--in) * 0.1) 0;
        color: #0a1428;
    }
    .recommendation-sheet-modal .rec-capstone-title::before,
    .recommendation-sheet-modal .rec-capstone-title::after {
        content: '';
        display: block;
        width: calc(var(--in) * 0.75);
        height: 1px;
        background: #d9cda6;
        margin: calc(var(--in) * 0.1) auto;
    }

    .recommendation-sheet-modal .rec-members {
        font-size: calc(var(--pt) * 12);
        line-height: 1.6;
        font-weight: 700;
        color: #0a1428;
    }

    .recommendation-sheet-modal .rec-signature {
        margin: auto 0 calc(var(--in) * 0.35);
        padding-top: calc(var(--in) * 0.35);
        min-width: 0;
        text-align: center;
    }
    .recommendation-sheet-modal .rec-sig-name {
        display: inline-block;
        min-width: calc(var(--in) * 3);
        padding: 0 calc(var(--in) * 0.1) calc(var(--in) * 0.04);
        border-bottom: 1px solid #222;
        font-size: calc(var(--pt) * 12);
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #0a1428;
    }
    .recommendation-sheet-modal .rec-sig-label {
        display: block;
        margin-top: calc(var(--in) * 0.06);
        font-size: calc(var(--pt) * 9);
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #5b6375;
    }

    .recommendation-sheet-modal .rec-footer {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-end;
        gap: calc(var(--in) * 1);
        margin-top: 0;
        padding-top: calc(var(--in) * 0.12);
        border-top: 1px solid #e2dacf;
    }
    .recommendation-sheet-modal .rec-footer-block {
        min-width: calc(var(--in) * 1.4);
        text-align: center;
    }
    .recommendation-sheet-modal .rec-footer-value {
        font-size: calc(var(--pt) * 10);
        font-weight: 700;
        color: #171e2c;
    }
    .recommendation-sheet-modal .rec-footer-label {
        margin-top: calc(var(--in) * 0.03);
        font-size: calc(var(--pt) * 8.5);
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #9a9385;
    }
}

/* ── APPROVAL SHEET — SCREEN (mirrors print layout) ── */
@media screen {
    #approvalSheetModal .approval-sheet-modal-box {
        container-type: inline-size;
        box-sizing: border-box;
        width: 100%;
        max-width: calc(8.5in + 3rem) !important;
        max-height: 92vh;
        padding: 1.5rem !important;
        background: #e9e5db;
    }

    #approvalSheetModal .approval-paper {
        --in: calc(100cqw / 8.5);
        --pt: calc(var(--in) / 72);

        box-sizing: border-box;
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        min-height: calc(var(--in) * 11);
        padding: calc(var(--in) * 0.75) calc(var(--in) * 0.85) calc(var(--in) * 0.8);
        background: #fff;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
        text-align: center;
        font-family: 'Times New Roman', serif;
        color: #1a1a1a;
    }

    /* Header image */
    #approvalSheetModal .approval-header-image {
        margin: 0 0 calc(var(--in) * 0.05);
        text-align: center;
    }
    #approvalSheetModal .approval-header-image img {
        display: block;
        margin: 0 auto;
        width: auto;
        max-width: calc(var(--in) * 6.2);
        max-height: calc(var(--in) * 0.85);
        object-fit: contain;
    }

    /* Title + gold bar */
    #approvalSheetModal #approvalSheetHeading {
        font-family: 'Times New Roman', serif;
        font-size: calc(var(--pt) * 17);
        line-height: 1;
        font-weight: 700;
        letter-spacing: 0.24em;
        margin: calc(var(--in) * 0.08) 0 calc(var(--in) * 0.02);
        text-align: center;
        color: #0a1428;
    }
    #approvalSheetModal #approvalSheetHeading::after {
        content: '';
        display: block;
        width: calc(var(--in) * 1.8);
        height: 2px;
        background: #b88d3a;
        margin: calc(var(--in) * 0.08) auto calc(var(--in) * 0.14);
    }

    #approvalSheetModal .print-serial {
        font-family: 'Courier New', monospace;
        font-size: calc(var(--pt) * 9);
        letter-spacing: 0.08em;
        color: #8b6914;
        margin: 0 0 calc(var(--in) * 0.14);
    }

    /* Content column */
    #approvalSheetModal #approvalSheetContent {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        font-family: 'Times New Roman', serif;
        font-size: calc(var(--pt) * 10);
        line-height: 1.28;
        color: #1a1a1a;
    }
    #approvalSheetModal #approvalSheetContent > * { width: 100%; }

    #approvalSheetModal .approval-intro {
        font-size: calc(var(--pt) * 10.5);
        margin: 0 0 calc(var(--in) * 0.06);
    }
    #approvalSheetModal .approval-title {
        font-size: calc(var(--pt) * 12.5);
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        max-width: calc(var(--in) * 6.1);
        margin: 0 auto calc(var(--in) * 0.14);
        padding: calc(var(--in) * 0.06) 0;
        border-top: 1px solid #d9cda6;
        border-bottom: 1px solid #d9cda6;
        color: #0a1428;
    }
    #approvalSheetModal .approval-body {
        font-size: calc(var(--pt) * 10.5);
        line-height: 1.5;
        max-width: calc(var(--in) * 6.4);
        margin: 0 auto calc(var(--in) * 0.2);
    }

    /* Signatures */
    #approvalSheetModal .approval-signature {
        margin: 0 auto calc(var(--in) * 0.16);
    }
    #approvalSheetModal .approval-signature .approval-sig-line {
        display: inline-block;
        min-width: calc(var(--in) * 2.55);
        padding: 0 calc(var(--in) * 0.06) calc(var(--in) * 0.03);
        margin-bottom: calc(var(--in) * 0.04);
        border-bottom: 1px solid #222;
        font-size: calc(var(--pt) * 10);
        font-weight: 700;
        line-height: 1.05;
        text-transform: uppercase;
        color: #0a1428;
    }
    #approvalSheetModal .approval-signature .approval-sig-role {
        font-size: calc(var(--pt) * 8.5);
        line-height: 1;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #5b6375;
    }

    /* Panel */
    #approvalSheetModal .approval-panel-heading {
        font-size: calc(var(--pt) * 10.5);
        line-height: 1;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin: calc(var(--in) * 0.05) 0 calc(var(--in) * 0.13);
        color: #0a1428;
    }
    #approvalSheetModal .approval-panel-grid {
        gap: calc(var(--in) * 0.22) calc(var(--in) * 0.55);
        max-width: calc(var(--in) * 6);
        margin: 0 auto calc(var(--in) * 0.16);
    }
    #approvalSheetModal .approval-panel-grid .approval-signature { margin: 0; }
    #approvalSheetModal .approval-panel-grid .approval-sig-line {
        width: 100%;
        min-width: 0;
        font-size: calc(var(--pt) * 9.5);
    }

    /* ── LOWERED BLOCK: Accepted → Oral → Approved → President ──
       margin-top:auto pushes the whole group to the bottom, so the space
       above it is reserved for the panel of examiners. */
    #approvalSheetModal .approval-accepted {
        font-size: calc(var(--pt) * 10);
        line-height: 1.4;
        max-width: calc(var(--in) * 6.2);
        margin: auto auto calc(var(--in) * 0.16);
        padding-top: calc(var(--in) * 0.3);
    }
    #approvalSheetModal .approval-oral-results {
        gap: calc(var(--in) * 0.06);
        font-size: calc(var(--pt) * 10);
        line-height: 1.15;
        margin: 0 0 calc(var(--in) * 0.14);
    }
    #approvalSheetModal .approval-oral-results .oral-value {
        display: inline-block;
        min-width: calc(var(--in) * 1.5);
        padding: 0 calc(var(--in) * 0.1) calc(var(--in) * 0.02);
        text-align: center;
        color: #0a1428;
    }
    #approvalSheetModal .approval-approved-label {
        margin: 0 0 calc(var(--in) * 0.05);
        font-size: calc(var(--pt) * 10);
        line-height: 1;
    }
    #approvalSheetModal #approvalSheetContent > .approval-signature:last-child {
        margin-bottom: 0;
    }
    #approvalSheetModal #approvalPresident {
        min-width: calc(var(--in) * 2.9);
        font-size: calc(var(--pt) * 10.5);
        letter-spacing: 0.03em;
    }

    /* Used by the print script to measure at true print size */
    #approvalSheetModal .approval-sheet-modal-box.print-measure { padding: 0 !important; }
    #approvalSheetModal .approval-sheet-modal-box.print-measure .approval-paper {
        --in: 1in !important;
        --pt: 1pt !important;
        min-height: 0 !important;
        padding: 0.2in 0.25in 0.05in !important;
        box-shadow: none;
    }
}



        /* ── APPROVAL SHEET DOCUMENT (screen) ── */
        .approval-sheet-doc {
            color: #171e2c;
            background: #fff;
            font-family: 'Times New Roman', serif;
            text-align: center;
            line-height: 1.35;
        }
        .approval-sheet-doc .approval-intro { font-size: .9rem; margin: 0 0 .35rem; }
        .approval-sheet-doc .approval-title {
            font-size: 1.05rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .025em; margin: 0 auto 1rem; max-width: 42rem;
        }
        .approval-sheet-doc .approval-body {
            font-size: .9rem; max-width: 42rem; margin: 0 auto 1.35rem;
        }
        .approval-sheet-doc .approval-body strong { font-weight: 700; }
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
        .approval-oral-results { display: flex; flex-direction: column; align-items: center; gap: .25rem; margin-bottom: 1rem; font-size: .86rem; }
        .approval-oral-results .oral-label { margin-right: .35rem; }
        .approval-oral-results .oral-value { border-bottom: 1px solid #222; font-weight: 700; padding-bottom: .05rem; }
        .approval-approved-label { margin: 0 0 .25rem; font-size: .86rem; }

        /* ══════════════════════════════════════════════════════════════
           PROFESSIONAL LOADING SYSTEM (matches Teacher Dashboard)
           ══════════════════════════════════════════════════════════════ */

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
        .btn-outline.is-loading {
            pointer-events: none; opacity: .78; cursor: progress;
        }
        .btn-primary.is-loading i,
        .btn-outline.is-loading i { animation: loader-spin .7s linear infinite; }

        /* ── 6. In-modal loading box (for sheet modals) ── */
        .modal-loading-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: .75rem; padding: 3rem 1.5rem; min-height: 220px;
        }
        .modal-loading-box p {
            font-size: .82rem; color: var(--text-muted); font-weight: 500;
        }
        .modal-loading-box .hint {
            font-size: .7rem; color: #b8b0a0;
        }

        /* ── Reduced motion ── */
        @media (prefers-reduced-motion: reduce) {
            .splash-ring, .splash-ring::after, .splash-mark, .spinner, .spinner-sm,
            .skeleton::after, .btn-primary.is-loading i { animation: none !important; }
        }

       /* ══════════════════════════════════════════════════════════════════
   PRINT — SHARED SHELL
   Only ONE document prints at a time. The body class set by
   printModalContent() decides which modal is revealed.
   ══════════════════════════════════════════════════════════════════ */
@media print {
    @page {
        size: letter portrait;
        margin: 0.55in 0.6in 0.7in 0.6in;
    }
     :root { --print-h: 9.5in; }
    html, body {
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        overflow: visible !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }


    /* Hide absolutely everything … */
    body * { visibility: hidden !important; }

    /* … then reveal ONLY the modal that is being printed. */
    body.print-revision #revisionSheetModal,
    body.print-revision #revisionSheetModal *,
    body.print-recommendation #recommendationSheetModal,
    body.print-recommendation #recommendationSheetModal *,
    body.print-approval #approvalSheetModal,
    body.print-approval #approvalSheetModal * {
        visibility: visible !important;
    }

    /* Pull the active modal into normal flow at the top-left. */
    body.print-revision #revisionSheetModal,
    body.print-recommendation #recommendationSheetModal,
    body.print-approval #approvalSheetModal {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: auto !important;
        bottom: auto !important;
        width: 100% !important;
        height: auto !important;
        max-height: var(--print-h) !important; 
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        display: block !important;
        overflow: hidden !important;  
        z-index: 1 !important;
    }

    /* Make absolutely sure the two inactive modals never print. */
    body.print-revision #recommendationSheetModal,
    body.print-revision #approvalSheetModal,
    body.print-recommendation #revisionSheetModal,
    body.print-recommendation #approvalSheetModal,
    body.print-approval #revisionSheetModal,
    body.print-approval #recommendationSheetModal {
        display: none !important;
        visibility: hidden !important;
    }

        .desktop-sidebar,
    .mobile-bottom-nav,
    #toast,
    #app-splash,
    #route-progress,
    #page-loader,
    main > .section-container {
        display: none !important;   /* remove from layout, not just hide */
    }
    body { min-height: 0 !important; }
    main {
        margin: 0 !important;
        padding: 0 !important;
        max-height: none !important;
        overflow: visible !important;
    }

    /* Never let a table row break across the page. */
    tr, td, th, .approval-signature, .approval-panel-grid {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }

    /* Print footer text supplied by the print-modal JS */
    .print-footer {
        display: block !important;
        position: fixed;
        bottom: 0.22in;
        left: 0;
        right: 0;
        text-align: center;
        font-family: 'Times New Roman', serif;
        font-size: 8.5pt;
        color: #7a6a4a;
        letter-spacing: 0.04em;
        padding-top: 0.08in;
        border-top: 1px solid #d9cda6;
    }
        /* ── Mirror the on-screen page exactly ──
       Screen page = 8.5x11 with 0.85in side padding → 6.8in text column.
       Print area = 7.3in wide (after @page margins), so 0.25in padding each
       side gives the same 6.8in column. Top/bottom offsets are the screen
       paddings minus the @page margins. */
    body.print-approval #approvalSheetModal .approval-paper {
        box-sizing: border-box !important;
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        height: 9.7in !important;
        min-height: 0 !important;
        padding: 0.2in 0.25in 0.05in !important;
        background: #fff !important;
        box-shadow: none !important;
        overflow: hidden !important;
        text-align: center !important;
    }
    body.print-approval #approvalSheetModal #approvalSheetContent {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 auto !important;
    }
    body.print-approval #approvalSheetModal #approvalSheetContent > * {
        width: 100% !important;
    }

    /* Lowered block, same as screen */
    body.print-approval #approvalSheetModal .approval-accepted {
        margin: auto auto 0.16in !important;
        padding-top: 0.3in !important;
    }
    body.print-approval #approvalSheetModal .approval-approved-label {
        margin: 0 0 0.05in !important;
    }
    body.print-approval #approvalSheetModal #approvalSheetContent > .approval-signature:last-child {
        margin-bottom: 0 !important;
    }
}



/* ══════════════════════════════════════════════════════════════════
   PRINT — RECOMMENDATION SHEET  (1 page, letter)
   ══════════════════════════════════════════════════════════════════ */
@media print {
    body.print-recommendation #recommendationSheetModal .modal-box {
         box-sizing: border-box !important;
        width: 100% !important;
        max-width: 100% !important;
        min-height: var(--print-h) !important;
        height: var(--print-h) !important;
        max-height: var(--print-h) !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        animation: none !important;
        background: #fff !important;
        display: block !important;
        font-family: 'Times New Roman', serif !important;
        color: #1a1a1a !important;
    }
    body.print-recommendation #recommendationSheetModal br { display: none !important; }

    /* Paper document becomes full page */
    body.print-recommendation #recommendationSheetModal .recommendation-document {
         box-sizing: border-box !important;
        width: 100% !important;
        min-height: var(--print-h) !important;
        height: var(--print-h) !important;
        max-height: var(--print-h) !important;
        margin: 0 !important;
        padding: 0.55in 0.65in 0.45in !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        background: #fff !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        text-align: center !important;
    }

    /* Header image */
    body.print-recommendation .recommendation-document .rec-header-image {
        margin: 0 0 0.22in !important;
        text-align: center !important;
    }
    body.print-recommendation .recommendation-document .rec-header-image img {
        display: block !important;
        margin: 0 auto !important;
        width: auto !important;
        max-width: 5.9in !important;
        max-height: 0.85in !important;
        object-fit: contain !important;
    }

    /* Title */
    body.print-recommendation .recommendation-document .rec-title {
        font-family: 'Times New Roman', serif !important;
        font-size: 18pt !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: 0.22em !important;
        text-align: center !important;
        color: #0a1428 !important;
        border-bottom: 2px solid #0a1428 !important;
        padding-bottom: 0.08in !important;
        margin: 0 auto 0.06in !important;
        max-width: 90% !important;
    }

    /* Serial */
    body.print-recommendation .recommendation-document .rec-serial {
        display: block !important;
        text-align: center !important;
        font-family: 'Courier New', monospace !important;
        font-size: 9pt !important;
        letter-spacing: 0.1em !important;
        color: #8b6914 !important;
        margin: 0 0 0.3in !important;
    }

    /* Body */
    body.print-recommendation #recommendationSheetContent {
        flex: 1 1 auto !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 0 auto !important;
        border: 0 !important;
        max-width: 6.4in !important;
        font-family: 'Times New Roman', serif !important;
        overflow: visible !important;
    }
    body.print-recommendation #recommendationSheetContent p {
        font-size: 12pt !important;
        line-height: 1.75 !important;
        margin: 0 0 0.16in !important;
        text-align: center !important;
        color: #1a1a1a !important;
    }
    body.print-recommendation #recommendationSheetContent p strong {
        font-weight: 700 !important;
    }

    /* Capstone title — italic serif with dividing rules */
    body.print-recommendation #recommendationTitle {
        font-family: 'Times New Roman', serif !important;
        font-size: 15.5pt !important;
        font-weight: 700 !important;
        font-style: italic !important;
        line-height: 1.32 !important;
        letter-spacing: 0.01em !important;
        max-width: 6in !important;
        margin: 0.14in auto 0.2in !important;
        padding: 0.1in 0 !important;
        color: #0a1428 !important;
    }
    body.print-recommendation #recommendationTitle::before,
    body.print-recommendation #recommendationTitle::after {
        content: '' !important;
        display: block !important;
        width: 0.75in !important;
        height: 1px !important;
        background: #d9cda6 !important;
        margin: 0.1in auto !important;
    }

    /* Members */
    body.print-recommendation #recommendationProponents {
        font-size: 12pt !important;
        line-height: 1.6 !important;
        font-weight: 700 !important;
        color: #0a1428 !important;
    }

    /* Adviser signature */
    body.print-recommendation .recommendation-document .rec-signature {
        margin-top: auto !important;
        margin-bottom: 0.35in !important;
        padding-top: 0.35in !important;
        text-align: center !important;
        min-width: 0 !important;
    }
    body.print-recommendation .recommendation-document .rec-sig-name {
        display: inline-block !important;
        min-width: 3in !important;
        padding: 0 0.1in 0.04in !important;
        border-bottom: 1px solid #222 !important;
        font-size: 12pt !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        color: #0a1428 !important;
    }
    body.print-recommendation .recommendation-document .rec-sig-label {
        display: block !important;
        margin-top: 0.06in !important;
        font-size: 9pt !important;
        letter-spacing: 0.14em !important;
        text-transform: uppercase !important;
        color: #5b6375 !important;
    }

    /* Footer: date + group */
    body.print-recommendation .recommendation-document .rec-footer {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-end !important;
        gap: 1in !important;
        margin-top: 0 !important;
        padding-top: 0.12in !important;
        border-top: 1px solid #e2dacf !important;
    }
    body.print-recommendation .recommendation-document .rec-footer-block {
        text-align: center !important;
        min-width: 1.4in !important;
    }
    body.print-recommendation .recommendation-document .rec-footer-value {
        font-size: 10pt !important;
        font-weight: 700 !important;
        color: #171e2c !important;
    }
    body.print-recommendation .recommendation-document .rec-footer-label {
        font-size: 8.5pt !important;
        letter-spacing: 0.12em !important;
        text-transform: uppercase !important;
        color: #9a9385 !important;
        margin-top: 0.03in !important;
    }

    /* Hide interactive controls */
    body.print-recommendation #recommendationSheetModal .btn-outline,
    body.print-recommendation #recommendationSheetModal .btn-primary,
    body.print-recommendation #recommendationSheetModal .btn-ghost,
    body.print-recommendation #recommendationSheetModal .flex.justify-end {
        display: none !important;
    }
}

/* ══════════════════════════════════════════════════════════════════
   PRINT — APPROVAL SHEET  (1 page, letter)
   ══════════════════════════════════════════════════════════════════ */
@media print {
        body.print-approval #approvalSheetModal .approval-sheet-modal-box {
        display: block !important;
        box-sizing: border-box !important;
        width: 100% !important;
        max-width: none !important;
        height: auto !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        animation: none !important;
        background: #fff !important;
        font-family: 'Times New Roman', serif !important;
        color: #1a1a1a !important;
    }

    /* Header image */
    body.print-approval #approvalSheetModal .approval-header-image {
        text-align: center !important;
        margin: 0 0 0.05in !important;
    }
    body.print-approval #approvalSheetModal .approval-header-image img {
        display: block !important;
        margin: 0 auto !important;
        width: auto !important;
        max-width: 6.2in !important;
        max-height: 0.85in !important;
        object-fit: contain !important;
    }

    /* Title */
    body.print-approval #approvalSheetHeading {
        font-family: 'Times New Roman', serif !important;
        font-size: 17pt !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: 0.24em !important;
        margin: 0.08in 0 0.02in !important;
        text-align: center !important;
        color: #0a1428 !important;
    }
    body.print-approval #approvalSheetHeading::after {
        content: '' !important;
        display: block !important;
        width: 1.8in !important;
        height: 2px !important;
        background: #b88d3a !important;
        margin: 0.08in auto 0.14in !important;
    }

    /* Serial under the title */
    body.print-approval #approvalSheetModal .print-serial {
        display: block !important;
        text-align: center !important;
        font-family: 'Courier New', monospace !important;
        font-size: 9pt !important;
        letter-spacing: 0.08em !important;
        color: #8b6914 !important;
        margin: 0 0 0.14in !important;
    }

    /* Content */
    body.print-approval #approvalSheetContent {
        border: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
        flex: 1 1 auto !important;
    }
    body.print-approval .approval-sheet-doc {
        font-family: 'Times New Roman', serif !important;
        line-height: 1.28 !important;
        font-size: 10pt !important;
        color: #1a1a1a !important;
    }
    body.print-approval .approval-intro {
        font-size: 10.5pt !important;
        margin: 0 0 0.06in !important;
        text-align: center !important;
        color: #1a1a1a !important;
    }
    body.print-approval .approval-title {
        font-size: 12.5pt !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        margin: 0 auto 0.14in !important;
        max-width: 6.1in !important;
        padding: 0.06in 0 !important;
        border-top: 1px solid #d9cda6 !important;
        border-bottom: 1px solid #d9cda6 !important;
        text-align: center !important;
        text-transform: uppercase !important;
        color: #0a1428 !important;
        letter-spacing: 0.02em !important;
    }
    body.print-approval .approval-body {
        font-size: 10.5pt !important;
        line-height: 1.5 !important;
        max-width: 6.4in !important;
        margin: 0 auto 0.2in !important;
        text-align: center !important;
        color: #1a1a1a !important;
    }

    /* Signature blocks */
    body.print-approval .approval-signature {
        margin: 0 auto 0.16in !important;
        text-align: center !important;
    }
    body.print-approval .approval-signature .approval-sig-line {
        display: inline-block !important;
        min-width: 2.55in !important;
        border-bottom: 1px solid #222 !important;
        font-size: 10pt !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        line-height: 1.05 !important;
        padding: 0 0.06in 0.03in !important;
        margin-bottom: 0.04in !important;
        color: #0a1428 !important;
    }
    body.print-approval .approval-signature .approval-sig-role {
        font-size: 8.5pt !important;
        line-height: 1 !important;
        color: #5b6375 !important;
        letter-spacing: 0.06em !important;
        text-transform: uppercase !important;
    }

    /* Panel of examiners grid */
    body.print-approval .approval-panel-heading {
        font-size: 10.5pt !important;
        line-height: 1 !important;
        margin: 0.05in 0 0.13in !important;
        text-align: center !important;
        font-weight: 700 !important;
        letter-spacing: 0.06em !important;
        text-transform: uppercase !important;
        color: #0a1428 !important;
    }
    body.print-approval .approval-panel-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 0.22in 0.55in !important;
        max-width: 6in !important;
        margin: 0 auto 0.16in !important;
    }
    body.print-approval .approval-panel-grid .approval-signature { margin: 0 !important; }
    body.print-approval .approval-panel-grid .approval-sig-line {
        width: 100% !important;
        min-width: 0 !important;
        font-size: 9.5pt !important;
    }

    /* Accepted / Approved statement */
    body.print-approval .approval-accepted {
        font-size: 10pt !important;
        line-height: 1.4 !important;
        max-width: 6.2in !important;
        margin: 0 auto 0.16in !important;
        text-align: center !important;
        color: #1a1a1a !important;
    }
    body.print-approval .approval-accepted strong { font-weight: 700 !important; }

    /* Oral exam result line */
    body.print-approval .approval-oral-results {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 0.06in !important;
        font-size: 10pt !important;
        line-height: 1.15 !important;
        margin: 0 0 0.14in !important;
    }
    body.print-approval .approval-oral-results .oral-label {
        margin-right: 0.35rem !important;
        color: #1a1a1a !important;
    }
    body.print-approval .approval-oral-results .oral-value {
        border-bottom: 1px solid #222 !important;
        font-weight: 700 !important;
        padding: 0 0.1in 0.02in !important;
        min-width: 1.5in !important;
        display: inline-block !important;
        text-align: center !important;
        color: #0a1428 !important;
    }

    /* Approved by line */
    body.print-approval .approval-approved-label {
        font-size: 10pt !important;
        line-height: 1 !important;
        margin: 0 0 0.05in !important;
        text-align: center !important;
        color: #1a1a1a !important;
    }

    /* President's name — final signature */
    body.print-approval .approval-signature:last-of-type .approval-sig-line {
        min-width: 2.9in !important;
        font-size: 10.5pt !important;
        letter-spacing: 0.03em !important;
    }

    /* Hide interactive controls */
    body.print-approval #approvalSheetModal button,
    body.print-approval #approvalSheetModal .btn-outline,
    body.print-approval #approvalSheetModal .btn-primary,
    body.print-approval #approvalSheetModal .btn-ghost,
    body.print-approval #approvalSheetModal .flex.justify-end {
        display: none !important;
    }
}

/* ═══════ REVISION SHEET — matches MCC form (screen + print) ═══════ */
@media screen {
    #revisionSheetModal .revision-sheet-modal-box {
        container-type: inline-size;
        box-sizing: border-box;
        width: 100%;
        max-width: calc(8.5in + 3rem) !important;
        max-height: 92vh;
        padding: 1.5rem !important;
        background: #e9e5db;
        overflow-y: auto;
    }
    #revisionSheetModal .revision-paper {
        --in: calc(100cqw / 8.5);
        --pt: calc(var(--in) / 72);
        min-height: calc(var(--in) * 11);
        padding: calc(var(--in) * 0.6) calc(var(--in) * 0.7) calc(var(--in) * 0.55);
        background: #fff;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
    }
}
@media print {
    body.print-revision #revisionSheetModal .revision-sheet-modal-box {
        display: block !important;
        width: 100% !important;
        max-width: none !important;
        height: auto !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        animation: none !important;
        background: #fff !important;
    }

    /* True print size: 1in = 1in, 1pt = 1pt — all calc()s resolve to real units. */
    body.print-revision #revisionSheetModal .revision-paper {
        --in: 1in;
        --pt: 1pt;
        padding: 0.5in 0.55in 0.5in !important;
        min-height: 0 !important;
        box-shadow: none !important;
    }

    /* Hide interactive controls and loader. */
    body.print-revision #revisionSheetModal .rv-actions,
    body.print-revision #revisionSheetModal #revisionSheetLoading {
        display: none !important;
    }

    /* Keep the header image inside the printable column. */
    body.print-revision #revisionSheetModal .rv-header {
        margin: 0 0 0.1in !important;
    }
    body.print-revision #revisionSheetModal .rv-header img {
        max-width: 100% !important;
        max-height: 0.9in !important;
        width: auto !important;
    }

    /* Never split a table row across pages. */
    body.print-revision #revisionSheetModal .rv-table tr,
    body.print-revision #revisionSheetModal .rv-table td,
    body.print-revision #revisionSheetModal .rv-table th {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
}

/* shared (uses --in / --pt so screen == print) */
#revisionSheetModal .revision-paper,
#revisionSheetModal .revision-paper * {
    font-family: 'Courier New', Courier, monospace;
}
#revisionSheetModal .revision-paper {
    box-sizing: border-box;
    position: relative;
    color: #000;
}
#revisionSheetModal .rv-header {
    text-align: center;
    margin: 0 0 calc(var(--in) * 0.1);
}
#revisionSheetModal .rv-header img {
    display: block;
    margin: 0 auto;
    width: auto;
    max-width: calc(var(--in) * 6.2);
    max-height: calc(var(--in) * 0.9);
    object-fit: contain;
}
#revisionSheetModal .rv-title {
    font-size: calc(var(--pt) * 10);
    font-weight: 700;
    letter-spacing: 0.04em;
    line-height: 1.2;
    text-align: center;
    color: #000;
    margin: calc(var(--in) * 0.05) 0 calc(var(--in) * 0.08);
    padding: 0;
    border: 0;
}
#revisionSheetModal .rv-serial {
    text-align: center;
    font-size: calc(var(--pt) * 7.5);
    letter-spacing: 0.08em;
    color: #000;
    margin: 0 0 calc(var(--in) * 0.08);
}
#revisionSheetModal .rv-content { position: relative; }
#revisionSheetModal .rv-loading {
    position: absolute;
    inset: 0;
    z-index: 10;
    background: rgba(255, 255, 255, 0.92);
}

#revisionSheetModal .rv-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin: 0 0 calc(var(--in) * 0.2);
    font-size: calc(var(--pt) * 8.5);
    color: #000;
}
#revisionSheetModal .rv-table th,
#revisionSheetModal .rv-table td {
    border: 1px solid #000;
    padding: calc(var(--in) * 0.03) calc(var(--in) * 0.07);
    height: calc(var(--in) * 0.21);
    vertical-align: middle;
    text-align: left;
    line-height: 1.25;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: pre-line;
    background: #fff;
}
#revisionSheetModal .rv-table th {
    font-weight: 700;
    text-align: center;
    font-size: calc(var(--pt) * 8.5);
}
#revisionSheetModal .rv-table th.rv-muted {
    font-weight: 400;
    color: #777;
}
#revisionSheetModal .rv-table td.rv-project { vertical-align: top; }
#revisionSheetModal .rv-table td.rv-c {
    text-align: center;
    font-size: calc(var(--pt) * 7.5);
}
#revisionSheetModal .rv-table tr.rv-tall td { height: calc(var(--in) * 0.27); }

#revisionSheetModal .rv-approved {
    font-weight: 700;
    font-size: calc(var(--pt) * 9);
    margin: calc(var(--in) * 0.05) 0 0;
}
#revisionSheetModal .rv-signature {
    display: inline-block;
    min-width: calc(var(--in) * 2.4);
    margin-top: calc(var(--in) * 0.3);
}
#revisionSheetModal #sheetApprovedBy {
    display: block;
    min-height: calc(var(--in) * 0.2);
    border-bottom: 1px solid #000;
    padding: 0 calc(var(--in) * 0.05) calc(var(--in) * 0.02);
    font-size: calc(var(--pt) * 9);
    font-weight: 700;
    text-transform: uppercase;
    color: #000;
}
#revisionSheetModal .rv-sig-label {
    font-weight: 700;
    font-size: calc(var(--pt) * 8.5);
    margin-top: calc(var(--in) * 0.04);
}
#revisionSheetModal .rv-footer-note {
    margin-top: calc(var(--in) * 0.3);
    font-size: calc(var(--pt) * 6.5);
    color: #333;
}
#revisionSheetModal .rv-footer-note strong { font-weight: 700; }

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
            <p class="splash-sub">Student Portal</p>
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

    <!-- ═══════════════ TOAST ═══════════════ -->
    <div id="toast" class="toast-container">
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span id="toastMessage" class="toast-message">Operation successful!</span>
            <button type="button" class="toast-close" onclick="hideToast()">&times;</button>
        </div>
    </div>

    <!-- ═══════════════ SIDEBAR (DESKTOP) ═══════════════ -->
    <aside class="desktop-sidebar fixed left-0 top-0 h-full w-64 flex flex-col justify-between z-20 hidden md:flex">
        <div>
            <div class="p-6 flex items-center space-x-3 border-b border-[rgba(214,177,92,0.12)]">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md flex-shrink-0"
                     style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-sm text-white tracking-[0.5px]">Capstone Tracker</span>
                    <small class="text-[10px] font-semibold tracking-[1px] uppercase mt-0.5" style="color: var(--gold-light); opacity: 0.85;">Student</small>
                </div>
            </div>
            <nav class="mt-6 px-4 space-y-1">
                <a href="#" data-section="dashboard" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-th-large w-4"></i> <span>Dashboard</span>
                </a>
                <a href="#" data-section="profile" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fa-regular fa-user w-4"></i> <span>Profile</span>
                </a>
                <a href="#" data-section="certificates" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fa-regular fa-file-lines w-4"></i> <span>Certificates</span>
                </a>
                <a href="#" data-section="revisions" class="nav-link flex items-center space-x-3 px-4 py-3 text-sm font-medium text-[rgba(255,255,255,0.65)]">
                    <i class="fas fa-sync-alt w-4"></i> <span>Revisions </span>
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-[rgba(214,177,92,0.15)]">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-lg" style="background:linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                    {{ strtoupper(substr($user->name ?? '', 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ $user->name ?? 'User' }}</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.5);">
                        {{ $student?->course ?? 'not assigned' }}-{{ $student?->section ?? 'not assigned' }} | {{$groups?->group_name ?? 'no group'}} #{{ $groups?->id ?? '—' }}
                    </p>
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

    <!-- ═══════════════ MOBILE BOTTOM NAV ═══════════════ -->
    <div class="mobile-bottom-nav fixed bottom-0 left-0 right-0 py-2 px-2 justify-around items-center z-30 flex md:hidden">
        <a href="#" data-section="dashboard" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-th-large text-lg"></i><span class="text-[10px] mt-1">Home</span>
        </a>
        <a href="#" data-section="profile" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fa-regular fa-user text-lg"></i><span class="text-[10px] mt-1">Profile</span>
        </a>
        <a href="#" data-section="certificates" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fa-regular fa-file-lines text-lg"></i><span class="text-[10px] mt-1">Docs</span>
        </a>
        <a href="#" data-section="revisions" class="mobile-nav-link flex flex-col items-center text-[rgba(255,255,255,0.55)] text-xs py-1">
            <i class="fas fa-sync-alt text-lg"></i><span class="text-[10px] mt-1">Revisions</span>
        </a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="mobile-nav-link flex flex-col items-center text-red-400 hover:text-red-300 text-xs py-1">
            <i class="fas fa-sign-out-alt text-lg"></i><span class="text-[10px] mt-1">Sign Out</span>
        </a>
    </div>

    <!-- ═══════════════ MAIN CONTENT ═══════════════ -->
    <main class="ml-0 md:ml-64 p-4 md:p-8 overflow-y-auto max-h-screen pb-20">

        <!-- ═══════ DASHBOARD ═══════ -->
        <div id="dashboard-section" class="section-container section-card max-w-7xl mx-auto">
            <div class="mb-8">
                <h1>Student Dashboard</h1>
                <div class="gold-accent-line"></div>
                <p class="text-[#5b6375] mt-2 text-sm">Welcome back, {{ $user->name ?? 'User' }}. Here's your capstone progress.</p>
            </div>

            @if($groups)
            <!-- Team Card -->
            <div class="content-card mb-8">
                <div class="card-accent"></div>
                <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex flex-col space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="icon-circle text-[#d6b15c]">
                                {{ strtoupper(substr($groups->group_name, 0, 2)) }}
                            </div>
                            <div>
                                <h2 class="flex items-center gap-2">
                                    {{ $groups->group_name ?? 'Group' }}
                                   
                                </h2>
                                <p class="text-sm text-[#3d4450]">{{ $groups->capstone_title ?? '—' }}</p>
                                <p class="text-xs text-[#5b6375] mt-1">Adviser: {{ $adviser?->teacher_last_name . ', ' . $adviser?->teacher_first_name ?? 'Not assigned' }}</p>
                                @if($groups->room)
                                <p class="text-xs text-[#8b6914] mt-1.5 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-door-open"></i> Presentation Room: <strong>{{ $groups->room->room_name }}</strong>
                                </p>
                                @else
                                <p class="text-xs text-[#5b6375] mt-1.5 italic">
                                    Presentation Room: Not assigned yet
                                </p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="text-xs text-[#5b6375] font-semibold mb-2 uppercase tracking-wide">Group Members</p>
                            <div class="flex flex-wrap gap-4">
                                @if($members->isNotEmpty())
                                @foreach($members as $member)
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold text-white" style="background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);">
                                        @if($member->student && $member->student->user)
                                        {{ strtoupper(substr($member->student->student_first_name, 0, 1)) }} {{ strtoupper(substr($member->student->student_last_name, 0, 1)) }}
                                        @else
                                        ?
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium text-[#0a1428]">
                                            {{ $member->student->student_first_name ?? '—' }}
                                        </p>
                                        <p class="text-[9px] text-[#5b6375]">{{ $member->role }}</p>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <p class="text-[#5b6375] text-xs">No members found.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-[#5b6375]">Overall Progress</p>
                        <h2 class="text-4xl font-bold" style="color:var(--gold-dark); font-family:'Cormorant Garamond',serif;">{{ $overallProgress ?? '0' }}%</h2>
                    </div>
                </div>
            </div>

            <!-- Progress + Sidebar -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left: Full progress with stages -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-4">
                        <h3>Capstone Progress</h3>
                        <span class="text-xs font-medium text-[#5b6375] bg-[#f0ece4] px-3 py-1 rounded-full">
                            {{ $overallProgress ?? 0 }}% complete
                        </span>
                    </div>
                    <div class="progress-bar-bg h-2.5 w-full mb-6">
                        <div class="progress-fill h-full" style="width: {{ $overallProgress ?? '0' }}%; background: var(--gold);"></div>
                    </div>

                    <!-- ═══ TIMELINE TABLE ═══ -->
                    <div class="timeline-wrapper">
                        <table class="timeline-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Tasks / Requirements</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $currentStage = null;
                                $stageCounts = $milestones->countBy('capstone_stage_id');
                                @endphp
                                @foreach($milestones as $milestone)
                                @php
                                $isCompleted = in_array($milestone->id, $completedMilestoneIds);
                                $isNext = $milestone->id === $nextMilestone?->id && !$isCompleted;
                                $remarks = $remarksByMilestone->get($milestone->id);
                                $absentStudents = $absencesByMilestone->get($milestone->id, collect());
                                $absentNames = $absentStudents->map(function($absence) {
                                $student = \App\Models\Student::where('user_id', $absence->user_id)->first();
                                return $student ? trim($student->student_first_name.' '.$student->student_last_name) : $absence->user_id;
                                })->filter()->values();
                                @endphp

                                @if ($milestone->capstone_stage_id != $currentStage)
                                @php $currentStage = $milestone->capstone_stage_id; @endphp
                                <tr class="stage-divider" data-stage-toggle="{{ $currentStage }}">
                                    <td colspan="3">
                                        <div class="stage-divider-inner">
                                            <span>
                                                <i class="fa-solid fa-flag"></i> {{ $milestone->capstoneStage->stage_title ?? 'Capstone' }}
                                                <span class="stage-count">({{ $stageCounts[$currentStage] ?? 0 }} milestones)</span>
                                            </span>
                                            <button type="button" class="stage-toggle-btn" aria-label="Toggle {{ $milestone->capstoneStage->stage_title ?? 'Capstone' }}">
                                                <i class="fa-solid fa-chevron-up"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endif

                                <tr class="milestone-row {{ $isCompleted ? 'row-completed' : ($isNext ? 'row-next' : '') }}" data-stage="{{ $milestone->capstone_stage_id }}">
                                    <!-- Date -->
                                    <td>
                                        {{ $milestone->start_date ? \Carbon\Carbon::parse($milestone->start_date)->format('M d, Y') : '—' }}
                                        @if($milestone->due_date)
                                        – {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                                        @endif
                                    </td>

                                    <!-- Tasks -->
                                    <td>
                                        <span class="task-title">{{ $milestone->milestone_title }}</span>
                                        @if($milestone->milestone_description)
                                        <span class="task-desc">{{ $milestone->milestone_description }}</span>
                                        @endif

                                        @if($isCompleted)
                                        <span class="task-status completed">
                                            Completed
                                            @php
                                            $gm = $groups?->groupMilestones->firstWhere('milestone_id', $milestone->id);
                                            @endphp
                                            @if($gm && $gm->completion_date)
                                            {{ \Carbon\Carbon::parse($gm->completion_date)->format('M d, Y') }}
                                            @endif
                                        </span>
                                        @elseif($isNext)
                                        <span class="task-status next">Next Step</span>
                                        @else
                                        <span class="task-status pending">Pending</span>
                                        @endif
                                    </td>

                                    <!-- Remarks -->
                                    <td>
                                        @if($remarks)
                                        @php
                                        $statusText = $remarks->remarks ?? ($remarks->compiled ? 'On Time Compliance' : 'Late Submission');
                                        $statusClass = stripos($statusText, 'late') !== false ? 'late' :
                                        (stripos($statusText, 'early') !== false ? 'early' : 'on-time');
                                        $statusIcon = $statusClass === 'late' ? 'fa-triangle-exclamation' :
                                        ($statusClass === 'early' ? 'fa-clock' : 'fa-circle-check');
                                        @endphp
                                        <div class="remark-summary">
                                            <span class="remark-status-badge {{ $statusClass }}">
                                                <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
                                            </span>
                                            @if($remarks->deduction_points)
                                            <span class="remark-deduction">
                                                <i class="fa-solid fa-minus"></i> {{ $remarks->deduction_points }} pts deduction
                                            </span> 
                                            @endif
                                            <span class="remark-attendance">
                                                <i class="fa-solid fa-user-group"></i>
                                                {{ $remarks->all_present ? 'All members present' : $absentNames->count().' member(s) absent' }}
                                            </span>
                                            @if(!$remarks->all_present && $absentNames->isNotEmpty())
                                            <table class="absence-table">
                                                <thead>
                                                    <tr><th>#</th><th>Absent Student</th></tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($absentNames as $i => $name)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ $name }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                            @if($remarks->feedback)
                                            <div class="remark-feedback">"{{ $remarks->feedback }}"</div>
                                            @endif
                                        </div>
                                        @else
                                        <span class="remark-empty">No remarks yet</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- ═══ END TIMELINE TABLE ═══ -->
                </div>

                <!-- Right sidebar -->
                <div class="w-full lg:w-80 space-y-4 flex-shrink-0">
                    @if($nextMilestone)
                    <div class="next-milestone-box">
                        <div class="icon-badge">
                            <i class="fa-regular fa-compass"></i> Next Step
                        </div>
                        <h3>{{ $nextMilestone->milestone_title }}</h3>
                        <p>{{ $nextMilestone->milestone_description }}</p>
                        <div class="dates">
                            <span><i class="fa-regular fa-calendar mr-1"></i> Start: {{ $nextMilestone->start_date ? \Carbon\Carbon::parse($nextMilestone->start_date)->format('M d, Y') : '—' }}</span>
                            <span><i class="fa-regular fa-calendar-check mr-1"></i> Due: {{ $nextMilestone->due_date ? \Carbon\Carbon::parse($nextMilestone->due_date)->format('M d, Y') : '—' }}</span>
                        </div>
                    </div>
                    @elseif(!$nextMilestone)
                    <div class="next-milestone-box">
                        <div class="icon-badge">
                            <i class="fa-regular fa-compass"></i>You Caught Up!
                        </div>
                        <h3>You Have Completed All Steps</h3>
                        <p>Congratulations on reaching this milestone!</p>
                        
                    </div>
                    @endif

                    
                </div>
            </div>

            @else
            <!-- No Group Assigned -->
            <div class="content-card text-center">
                <div class="card-accent"></div>
                <div class="p-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background:#f0ece4;">
                        <i class="fa-regular fa-users text-2xl text-[#b8b0a0]"></i>
                    </div>
                    <h2 class="mb-2">Not in a Group</h2>
                    <p class="text-[#5b6375] text-sm max-w-md mx-auto">
                        You haven't been assigned to a capstone group yet. Please contact your adviser or capstone coordinator.
                    </p>
                </div>
            </div>
            @endif
        </div>

        <!-- ═══════ PROFILE ═══════ -->
        <div id="profile-section" class="section-container hidden section-card max-w-7xl mx-auto">
            <div class="mb-8">
                <h1>Profile</h1>
                <div class="gold-accent-line"></div>
                <p class="text-[#5b6375] mt-2 text-sm">Manage your personal information and contact details</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Profile summary -->
                <div class="content-card lg:col-span-1 overflow-hidden">
                    <div class="profile-banner"></div>
                    <div class="p-6 -mt-12 flex flex-col items-center text-center">
                        <div class="profile-avatar-ring">
                            <div class="profile-avatar-inner">
                                {{ strtoupper(substr($student->student_first_name, 0, 1)) }}{{ strtoupper(substr($student->student_last_name, 0, 1)) }}
                            </div>
                        </div>
                        <h2 class="mt-4">{{ $student->student_first_name }} {{ $student->student_middle_name }} {{ $student->student_last_name }}</h2>
                        <p class="text-[#5b6375] text-xs font-medium tracking-wide uppercase mt-0.5">{{ $student->course ?? '—' }}-{{ $student->section ?? '—' }}</p>
                        <p class="text-[#5b6375] text-sm mt-1">ID: {{ $student->user_id ?? $user->user_id }}</p>

                        <div class="mt-4 flex flex-wrap gap-2 justify-center">
                            <span class="badge badge-gold"><i class="fa-solid fa-layer-group mr-1"></i>{{ $groups?->group_name ?? 'No Group' }} </span>
                        </div>

                        <div class="mt-5 w-full pt-5 border-t border-[#e2dacf] space-y-1">
                            <div class="info-row">
                                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                                <div class="text-left">
                                    <p class="info-label">Contact</p>
                                    <p class="info-value">{{ $student->contact_number ?? 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-icon"><i class="fa-regular fa-envelope"></i></div>
                                <div class="text-left">
                                    <p class="info-label">Email</p>
                                    <p class="info-value break-all">{{ $user->email ?? 'Not provided' }}</p>
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
                        <form id="profileForm" class="space-y-6" action="{{ route('student.profile.update') }}" method="POST">
                            @csrf

                            <div>
                                <p class="form-fieldset-title"><i class="fa-regular fa-user"></i> Personal Information</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="first_name" value="{{ old('first_name',  $student->student_first_name) }}" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" value="{{ old('last_name',  $student->student_last_name) }}" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Student ID</label>
                                        <input type="text" disabled value="{{ $user->user_id ?? $student->user_id }}" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Group</label>
                                        <input type="text" disabled value="{{ $groups->group_name ?? '—' }} (#{{ $groups->id ?? '—' }})" class="form-input">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="form-fieldset-title"><i class="fa-regular fa-address-card"></i> Contact Details</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" value="{{ old('email', $student->student_email) }}" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone', $student->contact_number ?? '') }}" class="form-input" placeholder="e.g. 09XX XXX XXXX">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="form-fieldset-title"><i class="fa-regular fa-id-card"></i> Academic Information</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="form-label">Course</label>
                                        <input type="text" disabled value="{{ $student->course ?? '—' }}" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Section</label>
                                        <input type="text" disabled value="{{ $student->section ?? '—' }}" class="form-input">
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-[#e2dacf]">
                                @if(session('success'))
                                <p class="text-xs" style="color:#1e6b3a;"><i class="fa-regular fa-circle-check mr-1"></i>{{ session('success') }}</p>
                                @else
                                <span></span>
                                @endif
                                <button type="submit" class="btn-primary">
                                    <i class="fa-regular fa-floppy-disk mr-2"></i>Save Changes
                                </button>
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
                        <h3 class="mb-5 flex items-center gap-2">
                            <i class="fa-solid fa-key text-[#d6b15c]"></i> Change Password
                        </h3>
                        <form action="{{ route('student.profile.update_password') }}" method="POST" class="space-y-4" id="passwordForm">
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
                                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428] transition" data-target="current_password">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label">New Password</label>
                                    <div class="relative">
                                        <input type="password" name="new_password" id="new_password" class="form-input pr-10" placeholder="Min 6 characters" required>
                                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428] transition" data-target="new_password">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2 h-1.5 rounded-full bg-[#e8e3d7] overflow-hidden">
                                        <div id="password_strength_bar" class="h-full w-0 rounded-full"></div>
                                    </div>
                                    @error('new_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="relative">
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-input pr-10" placeholder="Re-enter new password" required>
                                        <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#5b6375] hover:text-[#0a1428] transition" data-target="new_password_confirmation">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2 border-t border-[#e2dacf]">
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-floppy-disk mr-2"></i>Update Password
                                </button>
                            </div>
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
                                <li><i class="fa-solid fa-circle-check"></i> Never share your login details with classmates.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Capstone 1 is intentionally hard-coded below. Resolve the active
            // stage type from the controller value first, then the first milestone.
            $firstMilestone = $milestones->first();
            $activeCapstoneStage = data_get($firstMilestone, 'capstoneStage');
            $activeCapstoneStageType = $capstoneStageType
                ?? $capstone_stage_type
                ?? data_get($activeCapstoneStage, 'stage_type')
                ?? data_get($activeCapstoneStage, 'capstone_stage_type')
                ?? data_get($activeCapstoneStage, 'type');
            $normalizedStageType = strtolower(trim((string) $activeCapstoneStageType));
            $isCapstone1Stage = in_array($normalizedStageType, ['1', 'capstone 1', 'capstone1'], true);
            $isCapstone2Stage = in_array($normalizedStageType, ['2', 'capstone 2', 'capstone2'], true);

            $revisionItems = collect();
            foreach (collect($revisions ?? []) as $revision) {
                foreach (['documentation', 'enhancements', 'objectives'] as $relation) {
                    $items = data_get($revision, $relation, []);
                    if ($items instanceof \Illuminate\Support\Collection) {
                        $revisionItems = $revisionItems->merge($items);
                    } elseif (is_iterable($items)) {
                        $revisionItems = $revisionItems->merge(collect($items));
                    }
                }
            }
            $allRevisionSheetsComplete = $revisionItems->isEmpty()
                || $revisionItems->every(fn ($item) => strtolower(trim((string) data_get($item, 'remarks', ''))) === 'completed');
            $approvalLetterUnlocked = $isApprovalSheetUnlocked ?? false;
            $capstone1Certificate = collect($certificatesCap1 ?? [])->first();
            $capstone1CertificateUnlocked = $capstone1Certificate && (bool) $capstone1Certificate->unlocked;
        @endphp

        <!-- ═══════ CERTIFICATES ═══════ -->
        <div id="certificates-section" class="section-container hidden section-card max-w-7xl mx-auto">
            <div class="mb-8">
                <h1>Certificates & Documents</h1>
                <div class="gold-accent-line"></div>
                <p class="text-[#5b6375] mt-2 text-sm">Download certificates, evaluation results, and progress reports</p>
            </div>
            <br>
        @if($isCapstone1Stage)
        <h2 class="text-xl font-bold text-[#0a1428] mb-4 flex items-center gap-2" style="font-family:'Cormorant Garamond',serif;">
            <i class="fas fa-scroll w-4"></i> Capstone 1
        </h2>
        <br>
        <div class="flex gap-3 mb-6 text-sm">
            <span class="badge badge-green">
                Available: <strong>{{ $capstone1CertificateUnlocked ? '1/1' : '0/1' }}</strong>
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Capstone 1 is intentionally one hard-coded document, not a loop. -->
            <div class="content-card {{ !$capstone1CertificateUnlocked ? 'locked-card' : '' }}">
                <div class="card-accent"></div>
                <div class="p-5">
                    @if(!$capstone1CertificateUnlocked)
                    <div class="absolute top-3 right-3 badge badge-muted">
                        <i class="fa-solid fa-lock"></i> Disabled
                    </div>
                    @endif
                    <h3 class="mt-3 text-lg {{ !$capstone1CertificateUnlocked ? 'text-[#5b6375]' : 'text-[#0a1428]' }}">Capstone 1 Certificate</h3>
                    <p class="text-[#5b6375] text-xs my-2">Official completion certificate for Capstone 1.</p>
                    @if($capstone1CertificateUnlocked)
                    <p class="text-[11px] text-[#1e6b3a]"><i class="fa-regular fa-circle-check"></i> Available</p>
                    @else
                    <p class="text-xs" style="color:var(--gold-dark);"><i class="fa-solid fa-lock"></i> Disabled</p>
                    <p class="text-[10px] text-[#5b6375] mt-1">Capstone 1 is available only during capstone stage type 1 after completion.</p>
                    @endif
                    <div class="flex gap-3 mt-4">
                        @if($capstone1CertificateUnlocked && $groups && $capstone1Certificate)
                        <a href="{{ route('certificate.show', ['groupId' => $groups->id, 'certificateId' => $capstone1Certificate->id]) }}"
                           target="_blank"
                           class="btn-outline text-xs py-1.5 px-3">
                            <i class="fas fa-print mr-1"></i> Print / Download
                        </a>
                        @else
                        <button class="btn-outline text-xs py-1.5 px-3" disabled>
                            <i class="fas fa-lock mr-1"></i> Disabled
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval Letter: available only after evaluation and all revision items are completed. -->
            <div class="content-card {{ !$approvalLetterUnlocked ? 'locked-card' : '' }}">
                <div class="card-accent"></div>
                <div class="p-5 relative">
                    @if(!$approvalLetterUnlocked)
                    <div class="absolute top-3 right-3 badge badge-muted">
                        <i class="fa-solid fa-lock"></i> Locked
                    </div>
                    @endif
                    <h3 class="mt-3 text-lg {{ !$approvalLetterUnlocked ? 'text-[#5b6375]' : 'text-[#0a1428]' }}">Approval Letter</h3>
                    <p class="text-[#5b6375] text-xs my-2">View and print the official approval letter for your capstone project.</p>
                    @if($approvalLetterUnlocked)
                    <p class="text-[11px] text-[#1e6b3a]"><i class="fa-regular fa-circle-check"></i> Issued</p>
                    @else
                    <p class="text-xs" style="color:var(--gold-dark);"><i class="fa-regular fa-hourglass-half"></i> Not yet available</p>
                    <p class="text-[10px] text-[#5b6375] mt-1">Complete the evaluation and all revision-sheet items first.</p>
                    @endif
                    <div class="flex gap-3 mt-4">
                        @if($approvalLetterUnlocked && $groups)
                        <button onclick="openApprovalSheet({{ $groups->id }})" class="btn-outline text-xs py-1.5 px-4 mt-1">
                            <i class="fas fa-eye mr-1"></i> View Approval Letter
                        </button>
                        @else
                        <button class="btn-outline text-xs py-1.5 px-3" disabled>
                            <i class="fa-solid fa-lock mr-1"></i> Locked
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Capstone 2: Recommendation & Approval Sheets -->
<br>
<h2 class="text-xl font-bold text-[#0a1428] mb-4 flex items-center gap-2" style="font-family:'Cormorant Garamond',serif;">
    <i class="fas fa-scroll w-4"></i> Capstone 2
</h2>
<br>
<div class="flex gap-3 mb-6 text-sm">
    @php
        // Count each Capstone 2 document independently:
        //   • Recommendation Sheet unlocks via $isRecommendationUnlocked
        //   • Approval Sheet unlocks via $approvalLetterUnlocked
        $capstone2AvailableCount =
            (int) ($isRecommendationUnlocked ?? false) +
            (int) ($approvalLetterUnlocked   ?? false);
    @endphp
    <span class="badge badge-green">
        Available: <strong>{{ $capstone2AvailableCount }}/2</strong>
    </span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Recommendation Sheet (standalone document — unlocked by its own milestone) -->
    <div class="content-card {{ !$isRecommendationUnlocked ? 'locked-card' : '' }}">
        <div class="card-accent"></div>
        <div class="p-5">
            @if(!$isRecommendationUnlocked)
            <div class="absolute top-3 right-3 badge badge-muted">
                <i class="fa-solid fa-lock"></i> Locked
            </div>
            @endif

            <h3 class="mt-3 text-lg {{ !$isRecommendationUnlocked ? 'text-[#5b6375]' : 'text-[#0a1428]' }}">Recommendation Sheet</h3>
            <p class="text-[#5b6375] text-xs my-2">View the recommendation sheet for your capstone project.</p>
            @if($isRecommendationUnlocked)
            <p class="text-[11px] text-[#5b6375]"><i class="fa-regular fa-circle-check"></i> Issued</p>
            @else
            <p class="text-xs" style="color:var(--gold-dark);"><i class="fa-regular fa-hourglass-half"></i> Not yet available</p>
            <p class="text-[10px] text-[#5b6375] mt-1">Complete the required milestone to unlock</p>
            @endif
            <div class="flex gap-3 mt-4">
                @if($isRecommendationUnlocked)
                <button onclick="openRecommendationSheet({{ $groups->id }})" class="btn-outline text-xs py-1.5 px-3">
                    <i class="fas fa-eye mr-1"></i> View Recommendation
                </button>
                @else
                <button class="btn-outline text-xs py-1.5 px-3" disabled>
                    <i class="fas fa-lock mr-1"></i> View Recommendation
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Approval Sheet -->
    <div class="content-card {{ !$approvalLetterUnlocked ? 'locked-card' : '' }}">
        <div class="card-accent"></div>
        <div class="p-5">
            @if(!$approvalLetterUnlocked)
            <div class="absolute top-3 right-3 badge badge-muted">
                <i class="fa-solid fa-lock"></i> Locked
            </div>
            @endif

            <h3 class="mt-3 text-lg {{ !$approvalLetterUnlocked ? 'text-[#5b6375]' : 'text-[#0a1428]' }}">Approval Sheet</h3>
            <p class="text-[#5b6375] text-xs my-2">View the final approval sheet for your capstone project.</p>
            @if($approvalLetterUnlocked)
            <p class="text-[11px] text-[#5b6375]"><i class="fa-regular fa-circle-check"></i> Issued</p>
            @else
            <p class="text-xs" style="color:var(--gold-dark);"><i class="fa-regular fa-hourglass-half"></i> Not yet available</p>
            <p class="text-[10px] text-[#5b6375] mt-1">Complete the required milestone to unlock</p>
            @endif
            <div class="flex gap-3 mt-4">
                @if($approvalLetterUnlocked)
                <button onclick="openApprovalSheet({{ $groups->id }})" class="btn-outline text-xs py-1.5 px-3">
                    <i class="fas fa-eye mr-1"></i> View Approval
                </button>
                @else
                <button class="btn-outline text-xs py-1.5 px-3" disabled>
                    <i class="fas fa-lock mr-1"></i> View Approval
                </button>
                @endif
            </div>
        </div>
    </div>

</div>

        </div>

<!-- ═══════ REVISIONS & PANEL FEEDBACK ═══════ -->
<div id="revisions-section" class="section-container hidden section-card max-w-7xl mx-auto">
    <div class="mb-8">
        <h1>Revisions & Panel Feedback</h1>
        <div class="gold-accent-line"></div>
        <p class="text-[#5b6375] mt-2 text-sm">View critical feedback, revision instructions, and full evaluation details from your panel and adviser</p>
    </div>

    @if($groups)
        <!-- Active Revision Status Card -->
        <div class="mb-8">
            @if($groups->revision_status == 'needs_revision')
                <div class="rounded-xl border shadow-sm p-6 flex flex-col md:flex-row gap-5" style="background-color: #fffbeb; border-color: #fef3c7;">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #fef3c7; color: #d97706;">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-bold" style="color: #92400e;">Active Revision Request</h3>
                        <p class="text-sm mt-1" style="color: #b45309;">
                            The panel has requested revisions for your group. Review the instructions below, implement the changes, and inform your adviser <strong>{{ $adviser ? $adviser->teacher_first_name . ' ' . $adviser->teacher_last_name : 'your adviser' }}</strong> when ready.
                        </p>

                    </div>
                </div>
            @elseif($groups->revision_status == 'revised')
                <div class="rounded-xl border shadow-sm p-6 flex flex-col md:flex-row gap-5" style="background-color: #eff6ff; border-color: #dbeafe;">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #dbeafe; color: #2563eb;">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-bold" style="color: #1e40af;">Revisions Submitted</h3>
                        <p class="text-sm mt-1" style="color: #1d4ed8;">
                            Your group has addressed the requested revisions! Your adviser has marked this as revised. The panel is currently reviewing your updates.
                        </p>        
                    </div>
                </div>
            @else
                <div class="rounded-xl border shadow-sm p-6 flex flex-col md:flex-row gap-5" style="background-color: #f0fdf4; border-color: #dcfce7;">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #dcfce7; color: #16a34a;">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-bold" style="color: #166534;">All Revisions Clear</h3>
                        <p class="text-sm mt-1" style="color: #15803d;">
                            Your group has no pending revision requests at the moment. Keep up the excellent work!
                        </p>
                    </div>
                </div>
            @endif

            @if($revisions->count() > 0)
            <div class="mt-4">
                <h4 class="font-semibold text-sm text-[#5b6375] mb-4 flex items-center gap-2">
                    <i class="fas fa-file-alt"></i> Revision Sheets from Panelists
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($revisions as $rev)
                        @php
                            $panelistName = $rev->panelist
                                ? $rev->panelist->teacher_first_name . ' ' . $rev->panelist->teacher_last_name
                                : 'Panelist';
                        @endphp
                        <div class="content-card">
                            <div class="card-accent"></div>
                            <div class="p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white;">
                                        <i class="fas fa-file-alt text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-[#0a1428]">Revision Sheet</h3>
                                        <p class="text-[#5b6375] text-xs my-2">
                                            From <strong>{{ $panelistName }}</strong> — {{ $rev->created_at->format('M d, Y') }}
                                        </p>
                                        <div class="flex gap-3 mt-4">
                                            <button type="button"
                                                    onclick="openRevisionSheet({{ $groups->id }}, {{ $rev->id }}, '{{ addslashes($panelistName) }}')"
                                                    class="btn-outline text-xs py-1.5 px-4">
                                                <i class="fas fa-eye mr-1"></i> View Revision Sheet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        </div>


    @else
        <!-- No group card -->
        <div class="content-card text-center">
            <div class="card-accent"></div>
            <div class="p-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background:#f0ece4;">
                    <i class="fa-regular fa-users text-2xl text-[#b8b0a0]"></i>
                </div>
                <h2 class="mb-2">Not in a Group</h2>
                <p class="text-[#5b6375] text-sm max-w-md mx-auto">
                    You haven't been assigned to a capstone group yet. Revisions and feedback are only available for groups.
                </p>
            </div>
        </div>
    @endif
</div>





<!-- ═══════════════ REVISION SHEET MODAL ═══════════════ -->
<div id="revisionSheetModal" class="modal-overlay">
    <div class="modal-box wide revision-sheet-modal-box" style="max-width: 52rem; padding: 1.5rem;">

        <div class="revision-paper">

            <div class="rv-header">
                <img src="{{ asset('pictures/mccheader.jpg') }}" alt="MCC Header">
            </div>

            <h2 class="rv-title">REVISION SHEET</h2>
            <p class="rv-serial" id="revisionSheetSerial">&nbsp;</p>

            <div id="revisionSheetContent" class="rv-content">

                <div id="revisionSheetLoading" class="modal-loading-box rv-loading">
                    <div class="spinner-sm"></div>
                    <p>Loading revision sheet…</p>
                    <span class="hint">Fetching panel feedback and group details</span>
                </div>

                <!-- Proponents / Project -->
                <table class="rv-table">
                    <colgroup><col style="width:50%"><col style="width:50%"></colgroup>
                    <thead>
                        <tr><th>Name of Proponents</th><th>Name of Capstone Project</th></tr>
                    </thead>
                    <tbody id="sheetProponents">
                        <tr><td>1.</td><td id="sheetProjectTitle" class="rv-project" rowspan="5"></td></tr>
                        <tr><td>2.</td></tr>
                        <tr><td>3.</td></tr>
                        <tr><td>4.</td></tr>
                        <tr><td>5.</td></tr>
                    </tbody>
                </table>

                <!-- Chapter / Document Findings -->
                <table class="rv-table">
                    <colgroup><col style="width:17%"><col style="width:66%"><col style="width:17%"></colgroup>
                    <thead>
                        <tr>
                            <th class="rv-muted">Chapter</th>
                            <th>Document Findings</th>
                            <th class="rv-muted">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="sheetChapterRows">
                        <tr><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td></tr>
                    </tbody>
                </table>

                <!-- System / IoT -->
                <table class="rv-table">
                    <colgroup><col style="width:83%"><col style="width:17%"></colgroup>
                    <thead>
                        <tr>
                            <th>System or IoT Findings/ Enhancements<br>/ Recommendations</th>
                            <th class="rv-muted">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="sheetIotRows">
                        <tr class="rv-tall"><td></td><td></td></tr>
                    </tbody>
                </table>

                <!-- Additional Objectives -->
                <table class="rv-table">
                    <colgroup><col style="width:50%"><col style="width:50%"></colgroup>
                    <thead>
                        <tr><th colspan="2">Additional Objectives for Capstone Project 2</th></tr>
                    </thead>
                    <tbody id="sheetObjectivesList">
                        <tr class="rv-tall"><td>1.</td><td>3.</td></tr>
                        <tr class="rv-tall"><td>2.</td><td>4.</td></tr>
                    </tbody>
                </table>

                <!-- Approved by -->
                <p class="rv-approved">Approved by:</p>
                <div class="rv-signature">
                    <span id="sheetApprovedBy"></span>
                    <div class="rv-sig-label">Panel's Name &amp; Signature</div>
                </div>

                <p class="rv-footer-note">
                    Please always bring this document during the checking of your <strong>Capstone Project 2.</strong>
                </p>
            </div>
        </div><!-- /.revision-paper -->

        <div class="rv-actions flex justify-end gap-2 pt-4 border-t border-[#e2dacf] mt-4">
            <button type="button" onclick="window.printModalContent('revisionSheetModal')" class="btn-outline text-xs py-2 px-4">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button type="button" onclick="closeModal('revisionSheetModal')" class="btn-primary text-xs py-2 px-4">Close</button>
        </div>
    </div>
</div>




<!-- ── RECOMMENDATION SHEET MODAL (Formal Document Style) ── -->
<div id="recommendationSheetModal" class="modal-overlay">
    <div class="modal-box wide recommendation-sheet-modal" style="max-width: 52rem;">

        <!-- Paper document -->
        <div class="recommendation-document">

            <!-- Header image -->
            <div class="rec-header-image">
                <img src="{{ asset('pictures/mccheader.jpg') }}" alt="MCC Header">
            </div>

            <!-- Title -->
            <h2 class="rec-title">Recommendation Sheet</h2>


            <!-- Body -->
            <div class="rec-body" id="recommendationSheetContent">

                <!-- In-modal loading overlay -->
                <div id="recommendationSheetLoading"
                     class="modal-loading-box absolute inset-0 bg-white/90 backdrop-blur-sm z-10 rounded-lg">
                    <div class="spinner-sm"></div>
                    <p>Loading recommendation sheet…</p>
                    <span class="hint">Fetching project details and proponents</span>
                </div>

                <p>This <strong>Capstone Project</strong> hereto entitled:</p>

                <p class="rec-capstone-title" id="recommendationTitle">—</p>

                <p>
                    prepared and submitted by
                    <span class="rec-members" id="recommendationProponents">—</span>
                </p>

                <p>
                    in partial fulfillment of the requirements for the degree of
                    <strong>Bachelor of Science in Information Technology</strong>
                    has been examined, accepted, and recommended for Oral Presentation.
                </p>

                <!-- Adviser signature -->
                <div class="rec-signature">
                    <span class="rec-sig-name" id="recommendationAdviser">—</span>
                    <span class="rec-sig-label">Capstone Adviser</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="rec-footer">
                <div class="rec-footer-block">
                    <div class="rec-footer-value" id="recommendationDate">
                        {{ $dateIssued ?? '—' }}
                    </div>
                    <div class="rec-footer-label">Date Issued</div>
                </div>
                <div class="rec-footer-block">
                    <div class="rec-footer-value" id="recommendationSerial">
                        {{ $groups->serial_number ?? '—' }}
                    </div>
                    <div class="rec-footer-label">Serial Number</div>
                </div>
                <div class="rec-footer-block">
                    <div class="rec-footer-value" id="recommendationGroup">
                        {{ $groups->group_name ?? '—' }}
                    </div>
                    <div class="rec-footer-label">Group</div>
                </div>
            </div>

        </div><!-- /.recommendation-document -->

        <!-- Buttons -->
        <div class="flex justify-end gap-2 pt-4 border-t border-[#e2dacf] mt-4">
            <button type="button" onclick="printModalContent('recommendationSheetModal')" class="btn-outline text-xs py-2 px-4">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button type="button" onclick="closeModal('recommendationSheetModal')" class="btn-primary text-xs py-2 px-4">
                Close
            </button>
        </div>

    </div>
</div>
<!-- ═══════════════ APPROVAL SHEET MODAL ═══════════════ -->
<div id="approvalSheetModal" class="modal-overlay">
    <div class="modal-box wide approval-sheet-modal-box" style="max-width: 52rem; padding: 1.5rem;">
  <div class="approval-paper">  
        <!-- Header Image -->
        <div class="text-center mb-4 approval-header-image">
            <img src="{{ asset('pictures/mccheader.jpg') }}" alt="MCC Header" class="w-full max-h-24 object-contain">
        </div>

        <h2 id="approvalSheetHeading" class="text-center text-2xl font-bold tracking-widest text-[#0a1428] mb-4" style="font-family:'Cormorant Garamond',serif;">APPROVAL SHEET</h2>
        <p class="print-serial text-center text-xs tracking-widest mt-1" id="approvalSerial"
   style="font-family:'Courier New', monospace; color:#8b6914;">&nbsp;</p>

        <div id="approvalSheetContent" class="approval-sheet-doc relative">
            <!-- In-modal loading overlay -->
            <div id="approvalSheetLoading" class="modal-loading-box absolute inset-0 bg-white/90 backdrop-blur-sm z-10 rounded-lg">
                <div class="spinner-sm"></div>
                <p>Loading approval sheet…</p>
                <span class="hint">Fetching panel, adviser, and oral exam results</span>
            </div>

            <p class="approval-intro">This Capstone Project 2 hereto entitled:</p>
            <p id="approvalTitle" class="approval-title">—</p>

            <p class="approval-body">
                prepared and submitted by <span id="approvalProponents">—</span>
                in partial fulfillment of the requirements for the degree of
                <strong>Bachelor of Science in Information Technology</strong>
                has been examined, accepted and recommended for Oral Presentation.
            </p>

            <div class="approval-signature">
                <span id="approvalAdviser" class="approval-sig-line">—</span>
                <div class="approval-sig-role">Adviser</div>
            </div>

            <p class="approval-panel-heading">Panel of Examiners</p>
            <div id="approvalPanelists" class="approval-panel-grid">
                <div class="approval-signature"><span class="approval-sig-line">Loading...</span><div class="approval-sig-role">Member</div></div>
            </div>

            <div id="approvalChairmanBlock" class="approval-signature" style="display:none;">
                <span id="approvalChairman" class="approval-sig-line">—</span>
                <div class="approval-sig-role">Chairman, Board of Panels</div>
            </div>

            <p class="approval-accepted">
                <strong>ACCEPTED AND APPROVED</strong> in partial fulfillment of the requirements for the degree of
                <strong>BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY</strong>.
            </p>

            <div class="approval-oral-results">
                <span><span class="oral-label">Oral Examination:</span><span id="approvalOralResult" class="oral-value">—</span></span>
                <span><span class="oral-label">Date of Oral Examination:</span><span id="approvalOralDate" class="oral-value">—</span></span>
            </div>

            <p class="approval-approved-label">Approved:</p>
            <div class="approval-signature">
                <span id="approvalPresident" class="approval-sig-line">DR. FLORIPIS A. MONTECILLO, Ed.D.</span>
                <div class="approval-sig-role">School President</div>
            </div>
        </div>
  </div>
        <!-- Buttons -->
        <div class="flex justify-end gap-2 pt-4 border-t border-[#e2dacf] mt-4">
            <button type="button" onclick="printModalContent('approvalSheetModal')" class="btn-outline text-xs py-2 px-4">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button type="button" onclick="closeModal('approvalSheetModal')" class="btn-primary text-xs py-2 px-4">Close</button>
        </div>

    </div>
</div>

    </main>
    <div class="print-footer" style="display:none;">
    Madridejos Community College &nbsp;·&nbsp; Capstone Tracker &nbsp;·&nbsp; Official Document
</div>

    <script>
        // ══════════════════════════════════════════════════════════════
        // PROFESSIONAL LOADING SYSTEM  — splash · top bar · overlay · skeleton
        // (matches Teacher Dashboard)
        // ══════════════════════════════════════════════════════════════
        (function () {
            'use strict';

            /* ---------------- Configuration ---------------- */
            const SPLASH_MIN_MS  = 750;
            const SPLASH_MAX_MS  = 4000;
            const OVERLAY_MIN_MS = 350;
            const WATCHDOG_MS    = 25000;

            /* ---------------- 1. Splash ---------------- */
            const splashEl     = document.getElementById('app-splash');
            const splashFill   = document.getElementById('splash_bar_fill');
            const splashStatus = document.getElementById('splash_status');
            const splashStart  = performance.now();
            const SPLASH_MSGS  = [
                'Initializing workspace…',
                'Loading your capstone progress…',
                'Syncing evaluation records…',
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

        // ── MODAL HELPERS ──
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('active');
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('active');
        }

        // ══════════════════════════════════════════════════════════════
        //  PRINT — one document at a time, always a single page.
        // ══════════════════════════════════════════════════════════════
        const PRINT_CLASS_MAP = {
            revisionSheetModal: 'print-revision',
            recommendationSheetModal: 'print-recommendation',
            approvalSheetModal: 'print-approval'
        };

        const PRINTABLE_PX = 9.5 * 96 * 0.9;   // 9.5in minus 10% safety for print-font differences
const PRINT_WIDTH_PX = 7.5 * 96;       // letter width minus 0.5in margins

window.printModalContent = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const printClass = PRINT_CLASS_MAP[modalId];
    const box = modal.querySelector('.modal-box');

    let previousZoom = '';
    if (box && printClass !== 'print-recommendation') {
        previousZoom = box.style.zoom || '';
        const previousWidth = box.style.width || '';

        // Measure at the width the page will actually print at
        box.classList.add('print-measure'); 
        box.style.zoom = '1';
        box.style.width = PRINT_WIDTH_PX + 'px';
        const naturalHeight = box.scrollHeight;
        box.style.width = previousWidth;
        box.classList.remove('print-measure');  

        if (naturalHeight > PRINTABLE_PX) {
            const scale = Math.max(0.45, PRINTABLE_PX / naturalHeight);
            box.style.zoom = String(Math.floor(scale * 100) / 100);
        }
    }

    Object.values(PRINT_CLASS_MAP).forEach(c => document.body.classList.remove(c));
    if (printClass) document.body.classList.add(printClass);

    const cleanup = () => {
        if (printClass) document.body.classList.remove(printClass);
        if (box) box.style.zoom = previousZoom;
        window.removeEventListener('afterprint', cleanup);
    };

    window.addEventListener('afterprint', cleanup);
    window.print();
    setTimeout(cleanup, 2000);
};

        window.printRevisionSheet = function () {
            window.printModalContent('revisionSheetModal');
        };

        // Click outside to close
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay') && e.target.classList.contains('active')) {
                e.target.classList.remove('active');
            }
        });

        // ── TOGGLE RUBRIC DETAILS ──
        window.toggleRubricDetails = function(evalId) {
            const container = document.getElementById('rubric-details-' + evalId);
            const chevron = document.getElementById('chevron-' + evalId);
            if (container) {
                const isHidden = container.classList.toggle('hidden');
                if (chevron) {
                    if (isHidden) {
                        chevron.className = 'fas fa-chevron-down';
                    } else {
                        chevron.className = 'fas fa-chevron-up';
                    }
                }
            }
        };

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

        document.addEventListener('DOMContentLoaded', function() {

            // ── Timeline: foldable capstone stages ──
            document.querySelectorAll('.timeline-table .stage-divider').forEach(divider => {
                const stage = divider.dataset.stageToggle;
                const btn = divider.querySelector('.stage-toggle-btn');
                const rows = document.querySelectorAll(`.timeline-table .milestone-row[data-stage="${stage}"]`);

                const toggle = () => {
                    const collapsed = btn.classList.toggle('collapsed');
                    rows.forEach(r => r.style.display = collapsed ? 'none' : '');
                };

                divider.querySelector('.stage-divider-inner').addEventListener('click', toggle);
            });

            // ── SECTION SWITCHING WITH PERSISTENCE ──
            const sections = {
                dashboard: document.getElementById('dashboard-section'),
                profile: document.getElementById('profile-section'),
                certificates: document.getElementById('certificates-section'),
                revisions: document.getElementById('revisions-section')
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
                    link.style.color = link.dataset.section === sectionId ? 'var(--gold)' :
                        'rgba(255,255,255,0.55)';
                });
                localStorage.setItem('studentActiveSection', sectionId);
            }

            const storedSection = localStorage.getItem('studentActiveSection');
            if (storedSection && sections[storedSection]) {
                activateSection(storedSection);
            } else {
                activateSection('dashboard');
            }

            [...navLinks, ...mobileNavLinks].forEach(link => link.addEventListener('click', e => {
                e.preventDefault();
                const s = link.dataset.section;
                if (s && sections[s]) activateSection(s);
            }));

            // ── SHOW CONFIRMATION TOAST ──
            @if(session('success'))
            showToast('{{ session('success') }}', false);
            @endif

            @if(session('error'))
            showToast('{{ session('error') }}', true);
            @endif

            @if($errors->any())
            showToast('{{ $errors->first() }}', true);
            @endif

            // ── PROFILE FORM SUBMISSION WITH LOADER ──
            const profileForm = document.getElementById('profileForm');
            if (profileForm) {
                profileForm.addEventListener('submit', function () {
                    window.showPageLoader('Saving profile…');
                });
            }

            // ── PASSWORD FORM SUBMISSION WITH LOADER ──
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function () {
                    window.showPageLoader('Updating password…');
                });
            }

            // ── PASSWORD TOGGLE ──
            document.querySelectorAll('.password-toggle').forEach(btn => {
                btn.addEventListener('click', function() {
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

            // ── PASSWORD STRENGTH METER ──
            const newPasswordInput = document.getElementById('new_password');
            const strengthBar = document.getElementById('password_strength_bar');
            if (newPasswordInput && strengthBar) {
                newPasswordInput.addEventListener('input', function() {
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
        });


        // ── Open Approval Sheet Modal ──
        // Loading is scoped INSIDE the modal — no global overlay.
        window.openApprovalSheet = function(groupId) {
            const serialEl = document.getElementById('approvalSerial');
            const modal = document.getElementById('approvalSheetModal');
            const heading = document.getElementById('approvalSheetHeading');
            const title = document.getElementById('approvalTitle');
            const proponents = document.getElementById('approvalProponents');
            const adviser = document.getElementById('approvalAdviser');
            const panelists = document.getElementById('approvalPanelists');
            const chairmanBlock = document.getElementById('approvalChairmanBlock');
            const chairman = document.getElementById('approvalChairman');
            const oralResult = document.getElementById('approvalOralResult');
            const oralDate = document.getElementById('approvalOralDate');
            const president = document.getElementById('approvalPresident');
            const loadingBox = document.getElementById('approvalSheetLoading');
            if (!modal || !heading || !title || !proponents || !adviser || !panelists) return;

            const escapeHtml = value => String(value ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            const personName = person => typeof person === 'string' ? person : (person?.name || 'Panelist');
            const joinNames = names => {
                const list = (names || []).map(personName).filter(Boolean);
                if (!list.length) return '—';
                if (list.length === 1) return list[0];
                if (list.length === 2) return `${list[0]} and ${list[1]}`;
                return `${list.slice(0, -1).join(', ')}, and ${list[list.length - 1]}`;
            };

            heading.textContent = 'APPROVAL SHEET';
            title.textContent = '—';
            proponents.textContent = 'Loading...';
            adviser.textContent = '—';
            panelists.innerHTML = '<div class="approval-signature"><span class="approval-sig-line">Loading...</span><div class="approval-sig-role">Member</div></div>';
            if (chairmanBlock) chairmanBlock.style.display = 'none';
            if (chairman) chairman.textContent = '—';
            oralResult.textContent = '—';
            oralDate.textContent = '—';
            president.textContent = 'DR. FLORIPIS A. MONTECILLO, Ed.D.';
            if (serialEl) serialEl.textContent = ' ';

            // Show in-modal loading spinner
            if (loadingBox) loadingBox.style.display = 'flex';

            closeModal('recommendationSheetModal');
            openModal('approvalSheetModal');

            fetch(`/student/get-approval-sheet/${groupId}`, { __silent: true })
                .then(async response => {
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(`Server returned ${response.status}: ${text.slice(0, 100)}`);
                    }
                    return response.json();
                })
                .then(data => {
                    title.textContent = data.capstone_title || '—';
                    proponents.textContent = joinNames(data.members);
                    adviser.textContent = data.adviser || '—';

                    const allPanelists = Array.isArray(data.panelists) ? data.panelists : [];
                    const chairmanEntry = allPanelists.find(p => /chair/i.test(String(p?.role || p?.type || ''))) ||
                        (data.chairman ? { name: data.chairman } : null);
                    const memberPanelists = chairmanEntry
                        ? allPanelists.filter(p => p !== chairmanEntry)
                        : allPanelists;

                    panelists.innerHTML = memberPanelists.length
                        ? memberPanelists.map(p => `<div class="approval-signature"><span class="approval-sig-line">${escapeHtml(personName(p))}</span><div class="approval-sig-role">Member</div></div>`).join('')
                        : '<div class="approval-signature"><span class="approval-sig-line">No panelists assigned</span><div class="approval-sig-role">Member</div></div>';

                    if (chairmanEntry && chairmanBlock && chairman) {
                        chairmanBlock.style.display = 'block';
                        chairman.textContent = personName(chairmanEntry);
                    }
                    oralResult.textContent = data.oral_exam_result || '—';
                    // Prefer the certificate issued_date; fall back to the evaluation date.
const examDateRaw = data.issued_date || data.oral_exam_date;
if (examDateRaw) {
    const d = new Date(examDateRaw);
    oralDate.textContent = isNaN(d.getTime())
        ? examDateRaw
        : d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
} else {
    oralDate.textContent = '—';
}
                    president.textContent = data.school_president || 'DR. FLORIPIS A. MONTECILLO, Ed.D.';
                    if (serialEl) {
                        serialEl.textContent = data.serial_number
                            ? data.serial_number
                            : ' ';
                    }
                })
                .catch(err => {
                    console.error('Approval sheet error:', err);
                    proponents.textContent = 'Unable to load approval data';
                    panelists.innerHTML = `<div class="approval-signature"><span class="approval-sig-line">Error loading panel</span><div class="approval-sig-role">${escapeHtml(err.message)}</div></div>`;
                })
                .finally(() => {
                    if (loadingBox) loadingBox.style.display = 'none';
                });
        };

        // ── Open Recommendation Sheet Modal ──
        window.openRecommendationSheet = function(groupId) {
                const dateEl  = document.getElementById('recommendationDate');
                const modal        = document.getElementById('recommendationSheetModal');
                const title        = document.getElementById('recommendationTitle');
                const proponents   = document.getElementById('recommendationProponents');
                const adviser      = document.getElementById('recommendationAdviser');
                const serialEl     = document.getElementById('recommendationSerial');
                const loadingBox   = document.getElementById('recommendationSheetLoading');

                title.textContent      = '—';
                proponents.textContent = 'Loading...';
                adviser.textContent    = '—';
                if (serialEl) serialEl.textContent = ' ';

                if (loadingBox) loadingBox.style.display = 'flex';

                closeModal('approvalSheetModal');
                openModal('recommendationSheetModal');

                fetch(`/student/get-recommendation-sheet/${groupId}`, { __silent: true })
                    .then(response => response.json())
                    .then(data => {
                        title.textContent = data.capstone_title || '—';

                        if (data.members && data.members.length) {
                            if (data.members.length === 1) {
                                proponents.textContent = data.members[0];
                            } else if (data.members.length === 2) {
                                proponents.textContent = `${data.members[0]} and ${data.members[1]}`;
                            } else {
                                const names = data.members.slice(0, -1).join(', ');
                                proponents.textContent = `${names}, and ${data.members[data.members.length - 1]}`;
                            }
                        } else {
                            proponents.textContent = '—';
                        }

                        adviser.textContent = data.adviser || '—';
                        if (dateEl && data.issued_date) {
    const d = new Date(data.issued_date);
    dateEl.textContent = d.toLocaleDateString('en-US', {
        month: 'long',
        day:   'numeric',
        year:  'numeric'
    });
}

                        if (serialEl) {
                            serialEl.textContent = data.serial_number
                                ?  data.serial_number
                                : ' ';
                        }
                    })
                    .catch(err => {
                        console.error('Recommendation sheet error:', err);
                        proponents.textContent = 'Error loading data';
                        adviser.textContent    = 'Error loading data';
                    })
                    .finally(() => {
                        if (loadingBox) loadingBox.style.display = 'none';
                    });
        };

    window.openRevisionSheet = function(groupId, revisionId, panelistName) {
        const $ = id => document.getElementById(id);
        const serialEl     = $('revisionSheetSerial');
        const proponents   = $('sheetProponents');
        const chapterRows  = $('sheetChapterRows');
        const iotRows      = $('sheetIotRows');
        const objectiveRows= $('sheetObjectivesList');
        const approvedBy   = $('sheetApprovedBy');
        const loadingBox   = $('revisionSheetLoading');

        if (!proponents || !chapterRows || !iotRows || !objectiveRows || !approvedBy) {
            console.error('Revision Sheet modal elements are missing.');
            return;
        }

        const esc = v => String(v ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const remarkText = v => String(v || 'Pending').toLowerCase() === 'completed' ? 'Completed' : 'Pending';
        const blankRow = cols => `<tr class="rv-tall">${'<td></td>'.repeat(cols)}</tr>`;

        const renderProponents = (members, title) => {
            const n = Math.max(5, members.length);
            let html = '';
            for (let i = 0; i < n; i++) {
                const name = members[i] ? esc(members[i].name ?? members[i]) : '';
                html += `<tr><td>${i + 1}. ${name}</td>` +
                    (i === 0 ? `<td id="sheetProjectTitle" class="rv-project" rowspan="${n}">${esc(title)}</td>` : '') +
                    `</tr>`;
            }
            proponents.innerHTML = html;
        };

        // reset + show in-modal loader
        if (loadingBox) loadingBox.style.display = 'flex';
        openModal('revisionSheetModal');
        renderProponents([], '');
        chapterRows.innerHTML = blankRow(3).repeat(3);
        iotRows.innerHTML = blankRow(2).repeat(8);
        objectiveRows.innerHTML = '<tr class="rv-tall"><td>1.</td><td>3.</td></tr><tr class="rv-tall"><td>2.</td><td>4.</td></tr>';
        approvedBy.textContent = panelistName || '';
        if (serialEl) serialEl.innerHTML = '&nbsp;';

        Promise.all([
            fetch(`/student/get-group/${groupId}`, { __silent: true }).then(r => r.json()),
            fetch(`/student/get-revision/${groupId}/${revisionId}`, { __silent: true }).then(r => r.json())
        ])
        .then(([groupData, revData]) => {
            renderProponents(groupData.members || [], groupData.capstone_title || '');

            // Chapters (min 3 rows)
            const chapters = revData.chapters || [];
            let html = chapters.map(ch =>
                `<tr><td class="rv-c">${esc(ch.chapter)}</td><td>${esc(ch.findings)}</td><td class="rv-c">${remarkText(ch.remarks)}</td></tr>`
            ).join('');
            for (let i = chapters.length; i < 3; i++) html += blankRow(3);
            chapterRows.innerHTML = html;

            // System / IoT (min 8 rows)
            const iots = revData.iot_findings || [];
            html = iots.map(iot =>
                `<tr class="rv-tall"><td>${esc(iot.finding)}</td><td class="rv-c">${remarkText(iot.remarks)}</td></tr>`
            ).join('');
            for (let i = iots.length; i < 8; i++) html += blankRow(2);
            iotRows.innerHTML = html;

            // Additional objectives: 2-column grid, numbered down the left then right
            const objs = revData.additional_objectives || [];
            const rows = Math.max(2, Math.ceil(objs.length / 2));
            html = '';
            for (let i = 0; i < rows; i++) {
                const l = objs[i], r = objs[i + rows];
                html += `<tr class="rv-tall">
                    <td>${i + 1}.${l ? ' ' + esc(l.objective) : ''}</td>
                    <td>${i + rows + 1}.${r ? ' ' + esc(r.objective) : ''}</td>
                </tr>`;
            }
            objectiveRows.innerHTML = html;

            if (serialEl) {
                const sn = revData.serial_number || groupData.serial_number;
                serialEl.textContent = sn ? sn : '\u00A0';
            }
        })
        .catch(error => {
            console.error('Revision loading error:', error);
            chapterRows.innerHTML = `<tr><td colspan="3">${esc(error.message)}</td></tr>`;
        })
        .finally(() => {
            if (loadingBox) loadingBox.style.display = 'none';
        });
    };
    </script>
<script src="{{ asset('js/idle-logout.js') }}"></script>
</body>
</html>