@props([
    'title' => 'Backoffice',
    'admin' => null,
    'active' => 'dashboard',
    'searchPlaceholder' => 'Cari data, reservasi, atau mobil...',
])

@php
    $adminNotificationItems = collect();
    $adminNotificationSummary = [
        'reservations' => 0,
        'reviews' => 0,
        'payments' => 0,
        'failed' => 0,
    ];
    $adminNotificationCount = 0;

    if ($admin && $admin->hasNotificationsTable()) {
        $adminNotificationItems = $admin->notifications()
            ->latest()
            ->take(24)
            ->get()
            ->filter(function ($notification) {
                return ($notification->data['audience'] ?? null) === 'admin';
            })
            ->take(8)
            ->values();

        $adminUnreadNotifications = $admin->unreadNotifications()
            ->get()
            ->filter(function ($notification) {
                return ($notification->data['audience'] ?? null) === 'admin';
            })
            ->values();

        $adminNotificationSummary = [
            'reservations' => $adminUnreadNotifications->where('data.category', 'reservations')->count(),
            'reviews' => $adminUnreadNotifications->where('data.category', 'reviews')->count(),
            'payments' => $adminUnreadNotifications->where('data.category', 'payments')->count(),
            'failed' => $adminUnreadNotifications->where('data.category', 'failed')->count(),
        ];

        $adminNotificationCount = $adminUnreadNotifications->count();
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&family=space-grotesk:500,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #f4f7fb;
            --panel: #0f1d33;
            --panel-soft: #152643;
            --line: #dbe3ef;
            --card: #ffffff;
            --text: #202636;
            --muted: #7b869b;
            --blue: #3f5ed7;
            --blue-soft: #eef2ff;
            --green: #1dbb84;
            --amber: #f59e0b;
            --red: #ef4444;
            --slate: #71829d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Instrument Sans', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(63, 94, 215, 0.08), transparent 24%),
                linear-gradient(180deg, #f7f9fc 0%, #eef3f9 100%);
            color: var(--text);
        }

        .backoffice-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 240px 1fr;
            align-items: start;
        }

        .sidebar {
            background: linear-gradient(180deg, #10203a 0%, #142847 100%);
            color: #f6f8fc;
            padding: 28px 22px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overscroll-behavior: contain;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 24px 0 60px rgba(15, 29, 51, 0.16);
        }

        .sidebar.dashboard-animated {
            transform: translateX(-100%);
            animation: backofficeSidebarIn 1400ms cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .brand {
            margin-bottom: 30px;
        }

        .brand-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 34px;
            line-height: 0.95;
            letter-spacing: -0.04em;
            margin: 0;
        }

        .brand-subtitle {
            margin-top: 6px;
            color: rgba(240, 244, 255, 0.7);
            font-size: 13px;
        }

        .nav-list {
            display: grid;
            gap: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            color: #afbdd7;
            text-decoration: none;
            font-size: 14px;
            transition: transform 280ms cubic-bezier(0.22, 1, 0.36, 1), background 220ms ease, color 220ms ease, box-shadow 220ms ease;
        }

        .nav-item.dashboard-animated {
            animation: backofficeNavItemIn 640ms cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: var(--nav-delay, 0ms);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            box-shadow: inset 3px 0 0 #4f74ff;
        }

        .nav-item:hover {
            transform: translateX(4px);
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            opacity: 0.9;
        }

        .sidebar-profile {
            margin-top: auto;
            padding-top: 28px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 16px;
        }

        .sidebar-profile .profile {
            border-left: none;
            padding-left: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            transition: background 0.2s;
        }

        .sidebar-profile .profile.dashboard-animated {
            animation: backofficeNavItemIn 760ms cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 420ms;
        }

        .sidebar-profile .profile:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .sidebar-profile .profile > div:first-child {
            flex: 1;
            min-width: 0;
        }

        .sidebar-profile .profile-name {
            font-size: 14px;
            font-weight: 700;
            color: #f6f8fc;
        }

        .sidebar-profile .profile-role {
            font-size: 11px;
            color: rgba(240, 244, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-profile .avatar {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            font-size: 12px;
        }

        .logout {
            margin-top: 0;
            padding-top: 0;
        }

        .logout-form {
            margin: 0;
        }

        .logout-form button {
            width: 100%;
            border: 0;
            background: transparent;
            cursor: pointer;
            text-align: left;
        }

        .logout-form .nav-item {
            color: #ef4444;
        }

        .logout-form .nav-item.dashboard-animated {
            animation-delay: 520ms;
        }

        .main {
            padding: 20px 28px 28px;
        }

        @keyframes backofficeSidebarIn {
            0% {
                opacity: 0;
                transform: translateX(-100%);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes backofficeNavItemIn {
            0% {
                opacity: 0;
                transform: translateX(-14px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .search {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(219, 227, 239, 0.9);
            border-radius: 16px;
            padding: 14px 18px;
            min-width: min(100%, 520px);
            box-shadow: 0 10px 30px rgba(15, 29, 51, 0.04);
        }

        .search input {
            border: 0;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 14px;
            color: var(--text);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .icon-button {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(219, 227, 239, 0.9);
            position: relative;
            cursor: pointer;
        }

        .icon-button:hover {
            border-color: rgba(164, 177, 202, 0.95);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            padding: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #2563eb;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            border: 2px solid #f7f9fc;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.24);
        }

        .notification-shell {
            position: relative;
        }

        .notification-menu {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            width: min(420px, calc(100vw - 48px));
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(203, 213, 225, 0.95);
            background: #cbd5e1;
            box-shadow: 0 28px 70px rgba(15, 29, 51, 0.18);
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 35;
        }

        .notification-menu.is-open {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .notification-menu-head {
            position: relative;
            overflow: hidden;
            padding: 18px 18px 16px;
            background: linear-gradient(135deg, #123c7a 0%, #1e4e9a 55%, #2c6dd5 100%);
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        }

        .notification-menu-head::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(191, 219, 254, 0.18), transparent 36%);
            pointer-events: none;
        }

        .notification-menu-head > * {
            position: relative;
            z-index: 1;
        }

        .notification-menu-title {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .notification-menu-subtitle {
            margin: 4px 0 0;
            color: rgba(236, 244, 255, 0.86);
            font-size: 12px;
        }

        .notification-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .notification-summary-item {
            border-radius: 16px;
            padding: 12px 13px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
        }

        .notification-summary-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(219, 234, 254, 0.92);
        }

        .notification-summary-value {
            margin-top: 4px;
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        .notification-list {
            padding: 10px;
            background: #cbd5e1;
            max-height: 460px;
            overflow-y: auto;
        }

        .notification-item {
            display: block;
            text-decoration: none;
            margin-bottom: 10px;
            padding: 14px;
            border-radius: 22px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 10px 24px rgba(15, 29, 51, 0.05);
            color: inherit;
        }

        .notification-item.is-unread {
            border-color: rgba(147, 197, 253, 0.95);
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(59, 130, 246, 0.10);
        }

        .notification-item:last-child {
            margin-bottom: 0;
        }

        .notification-item-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .notification-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .notification-chip.blue {
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
        }

        .notification-chip.amber {
            background: rgba(245, 158, 11, 0.14);
            color: #b96e00;
        }

        .notification-chip.green {
            background: rgba(29, 187, 132, 0.14);
            color: #0f8f63;
        }

        .notification-chip.red {
            background: rgba(239, 68, 68, 0.14);
            color: #d03a3a;
        }

        .notification-time {
            font-size: 11px;
            color: #64748b;
            white-space: nowrap;
        }

        .notification-item-title {
            margin: 10px 0 0;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.35;
        }

        .notification-item-message {
            margin: 6px 0 0;
            font-size: 13px;
            line-height: 1.55;
            color: #475569;
        }

        .notification-item-meta {
            margin-top: 10px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
        }

        .notification-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }

        .notification-link-button,
        .notification-read-button {
            border-radius: 12px;
            padding: 9px 12px;
            font-size: 11px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .notification-link-button {
            background: #123c7a;
            color: #fff;
        }

        .notification-read-button {
            border: 1px solid rgba(203, 213, 225, 0.95);
            background: #fff;
            color: #334155;
        }

        .notification-read-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
        }

        .notification-empty {
            padding: 22px 18px;
            border-radius: 22px;
            border: 1px dashed rgba(148, 163, 184, 0.9);
            background: rgba(255, 255, 255, 0.7);
            text-align: center;
            color: #475569;
        }

        .notification-empty strong {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #0f172a;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 14px;
            border-left: 1px solid rgba(123, 134, 155, 0.18);
        }

        .profile-name {
            font-size: 14px;
            font-weight: 700;
        }

        .profile-role {
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2c4474, #6e95ff);
            color: white;
            display: grid;
            place-items: center;
            font-weight: 700;
        }

        .card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(219, 227, 239, 0.85);
            border-radius: 22px;
            padding: 18px 20px;
            box-shadow: 0 18px 45px rgba(15, 29, 51, 0.06);
            backdrop-filter: blur(14px);
        }

        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 20px;
        }

        .page-title {
            margin: 0 0 6px;
            font-size: 34px;
            font-weight: 700;
            letter-spacing: -0.04em;
        }

        .page-subtitle {
            margin: 0;
            color: #57657c;
            font-size: 14px;
        }

        .primary-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 0;
            border-radius: 18px;
            background: #06070a;
            color: #fff;
            padding: 16px 24px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 14px 34px rgba(6, 7, 10, 0.18);
        }

        .filter-bar {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 26px;
        }

        .filter-box {
            border: 1px solid rgba(219, 227, 239, 0.95);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.92);
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 0 18px;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 700;
            color: #5c6980;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .filter-value {
            font-size: 14px;
            color: #202636;
            font-weight: 600;
        }

        .table-card {
            padding: 0;
            overflow: hidden;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-table thead th {
            padding: 16px 22px;
            color: #7385a3;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: rgba(243, 246, 251, 0.9);
            text-align: left;
        }

        .user-table tbody td {
            padding: 22px;
            font-size: 14px;
            color: #202636;
            border-top: 1px solid rgba(228, 235, 244, 0.95);
            vertical-align: middle;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 10px 20px rgba(15, 29, 51, 0.12);
        }

        .user-name {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 4px;
        }

        .user-meta,
        .muted-stack {
            font-size: 13px;
            color: #667489;
            line-height: 1.5;
        }

        .money {
            text-align: right;
            font-weight: 700;
            font-size: 16px;
            line-height: 1.4;
        }

        .table-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            min-width: 76px;
        }

        .table-pill.platinum {
            color: #395886;
            background: #dfe8fb;
        }

        .table-pill.gold {
            color: #a46a00;
            background: #fff0bf;
        }

        .table-pill.silver {
            color: #5e6778;
            background: #e6ebf2;
        }

        .table-pill.active {
            color: #3149b1;
            background: #dfe2ff;
        }

        .table-pill.suspend {
            color: #c02525;
            background: #ffd8d8;
        }

        .action-list {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
        }

        .action-icon {
            width: 18px;
            height: 18px;
            color: #3f5ed7;
        }

        .action-icon.edit {
            color: #434f63;
        }

        .action-icon.block {
            color: #e64545;
        }

        .action-icon.approve {
            color: #3f5ed7;
        }

        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 22px 20px;
            border-top: 1px solid rgba(228, 235, 244, 0.95);
            color: #5f6f87;
            font-size: 14px;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-link {
            min-width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #2c3647;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(219, 227, 239, 0.9);
        }

        .page-link.active {
            background: #0a0b0d;
            color: #fff;
            border-color: #0a0b0d;
        }

        .page-link.muted {
            color: #b5bfce;
        }

        .stats-grid,
        .content-grid,
        .bottom-grid {
            display: grid;
            gap: 16px;
        }

        .stats-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .content-grid {
            grid-template-columns: 2fr 0.95fr;
            margin-bottom: 16px;
        }

        .bottom-grid {
            grid-template-columns: 1.1fr 1.55fr;
            margin-bottom: 18px;
        }

        .stat-card {
            min-height: 114px;
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: grid;
            place-items: center;
        }

        .delta {
            font-size: 11px;
            font-weight: 700;
        }

        .delta.up {
            color: var(--green);
        }

        .stat-label {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 32px;
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .chip-group {
            display: flex;
            gap: 8px;
        }

        .chip {
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            background: #f0f3f8;
            color: #6a748a;
        }

        .chip.active {
            background: var(--blue);
            color: #fff;
        }

        .chart-shell {
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(246, 249, 255, 0.9), rgba(239, 244, 252, 0.78));
            border: 1px solid rgba(219, 227, 239, 0.95);
            padding: 16px;
            min-height: 280px;
        }

        .chart-svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .status-card {
            display: flex;
            flex-direction: column;
        }

        .donut-wrap {
            display: grid;
            place-items: center;
            margin: 6px 0 12px;
        }

        .status-list {
            display: grid;
            gap: 10px;
            margin-top: auto;
        }

        .status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
        }

        .status-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4f5d74;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: left;
            font-size: 13px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(219, 227, 239, 0.75);
        }

        .table th {
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .activity-title {
            font-weight: 700;
            margin-bottom: 3px;
        }

        .activity-subtitle {
            color: var(--muted);
            font-size: 12px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .pill.green {
            background: rgba(29, 187, 132, 0.12);
            color: var(--green);
        }

        .pill.blue {
            background: rgba(63, 94, 215, 0.12);
            color: var(--blue);
        }

        .pill.amber {
            background: rgba(245, 158, 11, 0.14);
            color: #b96e00;
        }

        .featured-card {
            background: linear-gradient(135deg, #101f37 0%, #12294b 58%, #243a66 100%);
            color: white;
            overflow: hidden;
            position: relative;
            min-height: 292px;
        }

        .featured-card::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            right: -80px;
            bottom: -190px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12), transparent 60%);
        }

        .featured-layout {
            display: grid;
            grid-template-columns: 1.15fr 0.95fr;
            gap: 18px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: rgba(89, 118, 212, 0.22);
            color: #cbd8ff;
            margin-bottom: 18px;
        }

        .featured-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 38px;
            line-height: 1;
            letter-spacing: -0.05em;
            margin: 0 0 12px;
        }

        .featured-text {
            color: rgba(238, 243, 255, 0.72);
            max-width: 560px;
            line-height: 1.6;
        }

        .featured-metrics {
            display: flex;
            gap: 24px;
            margin-top: 24px;
        }

        .featured-metrics strong {
            display: block;
            font-size: 26px;
            font-family: 'Space Grotesk', sans-serif;
        }

        .featured-label {
            color: rgba(238, 243, 255, 0.62);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .vehicle-stage {
            position: relative;
            min-height: 220px;
            border-radius: 20px;
            background: radial-gradient(circle at 50% 24%, rgba(255, 255, 255, 0.94), rgba(183, 196, 220, 0.34) 55%, rgba(16, 31, 55, 0.6) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .vehicle-stage svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .empty-text {
            color: var(--muted);
            font-size: 13px;
        }

        .fleet-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .fleet-stat-card {
            min-height: 120px;
        }

        .fleet-stat-card .stat-value {
            font-size: 28px;
        }

        .fleet-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 20px;
        }

        .fleet-card {
            overflow: visible;
            padding: 0;
        }

        .fleet-media {
            aspect-ratio: 1.52 / 1;
            overflow: hidden;
            position: relative;
            background:
                linear-gradient(135deg, rgba(17, 24, 39, 0.88), rgba(59, 130, 246, 0.18)),
                radial-gradient(circle at top center, rgba(255, 255, 255, 0.22), transparent 42%);
        }

        .fleet-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .fleet-status-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            background: rgba(255, 255, 255, 0.95);
        }

        .fleet-status-badge.green {
            color: #0b8c61;
        }

        .fleet-status-badge.blue {
            color: #3f5ed7;
        }

        .fleet-status-badge.red {
            color: #d03a3a;
        }

        .fleet-image-fallback {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: rgba(255, 255, 255, 0.92);
        }

        .fleet-image-fallback svg {
            width: 74px;
            height: 74px;
        }

        .fleet-body {
            padding: 18px 16px 16px;
        }

        .fleet-brand-row,
        .fleet-price-row,
        .fleet-specs,
        .fleet-card-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .fleet-brand-row {
            margin-bottom: 6px;
        }

        .fleet-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .fleet-title-actions {
            position: relative;
            flex-shrink: 0;
        }

        .fleet-menu-button {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(219, 227, 239, 0.95);
            background: #ffffff;
            display: grid;
            place-items: center;
            color: #44526a;
            cursor: pointer;
        }

        .fleet-menu-button svg {
            width: 16px;
            height: 16px;
        }

        .fleet-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 160px;
            padding: 8px;
            border-radius: 14px;
            border: 1px solid rgba(219, 227, 239, 0.96);
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(15, 29, 51, 0.12);
            z-index: 5;
        }

        .fleet-menu[hidden] {
            display: none;
        }

        .fleet-menu-item {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: flex-start;
            border: 0;
            background: transparent;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            color: #d03a3a;
            text-decoration: none;
            cursor: pointer;
            text-align: left;
        }

        .fleet-menu-item:hover {
            background: rgba(239, 68, 68, 0.08);
        }

        .fleet-brand {
            color: #4362d9;
            font-size: 13px;
            letter-spacing: 0.05em;
        }

        .fleet-rating {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            font-weight: 700;
        }

        .fleet-name {
            font-size: 28px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            letter-spacing: -0.04em;
            line-height: 1.05;
            margin: 0 0 14px;
        }

        .fleet-specs {
            padding: 12px 0 14px;
            border-top: 1px solid rgba(228, 235, 244, 0.9);
            border-bottom: 1px solid rgba(228, 235, 244, 0.9);
            color: #6b778d;
            font-size: 11px;
        }

        .fleet-spec-item {
            display: grid;
            gap: 6px;
            justify-items: center;
            text-align: center;
            flex: 1;
        }

        .fleet-spec-item svg {
            width: 14px;
            height: 14px;
        }

        .fleet-price-meta {
            font-size: 11px;
            color: #6b778d;
        }

        .fleet-price {
            font-size: 16px;
            font-weight: 700;
        }

        .fleet-price span {
            color: #6b778d;
            font-size: 12px;
            font-weight: 500;
        }

        .fleet-card-actions {
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .text-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            border-radius: 12px;
            border: 1px solid transparent;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1;
            text-decoration: none;
            cursor: pointer;
        }

        .text-action.edit {
            background: #f9b24a;
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(249, 178, 74, 0.22);
        }

        .text-action.detail {
            background: #ffffff;
            color: #3166ff;
            border-color: #3166ff;
        }

        .text-action.danger {
            background: #ef3429;
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(239, 52, 41, 0.18);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .detail-item {
            border: 1px solid rgba(219, 227, 239, 0.9);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.88);
        }

        .detail-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #74829a;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: #202636;
            line-height: 1.5;
            word-break: break-word;
        }

        .detail-image-shell {
            border: 1px solid rgba(219, 227, 239, 0.9);
            border-radius: 18px;
            overflow: hidden;
            background: #f8fbff;
            aspect-ratio: 16 / 10;
        }

        .detail-image-shell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .detail-gallery {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .detail-gallery-item {
            border-radius: 14px;
            overflow: hidden;
            background: #eef2f8;
            aspect-ratio: 4 / 3;
        }

        .detail-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .fleet-add-card {
            min-height: 100%;
            border: 2px dashed rgba(207, 216, 232, 0.92);
            background: rgba(255, 255, 255, 0.55);
            display: grid;
            place-items: center;
            text-align: center;
            color: #67758b;
            min-height: 280px;
            cursor: pointer;
        }

        .flash-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            padding: 14px 18px;
            border-radius: 18px;
            background: rgba(29, 187, 132, 0.12);
            border: 1px solid rgba(29, 187, 132, 0.18);
            color: #127b58;
            font-size: 14px;
            font-weight: 600;
        }

        .fleet-add-card .plus {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(226, 232, 240, 0.95);
            display: grid;
            place-items: center;
            margin: 0 auto 16px;
            color: #4b5563;
            font-size: 28px;
        }

        .maintenance-card {
            padding: 0;
            overflow: hidden;
        }

        .maintenance-card .section-head {
            padding: 18px 20px 0;
        }

        .maintenance-table-wrap {
            padding: 0 20px 18px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0 0 0 240px;
            display: grid;
            place-items: center;
            padding: 28px;
            background: rgba(10, 15, 26, 0.26);
            backdrop-filter: blur(12px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.22s ease;
            z-index: 40;
        }

        .modal-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-panel {
            width: min(1120px, 100%);
            max-height: calc(100vh - 56px);
            overflow: auto;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(219, 227, 239, 0.95);
            box-shadow: 0 28px 90px rgba(15, 29, 51, 0.22);
            transform: translateY(16px) scale(0.98);
            transition: transform 0.22s ease;
        }

        .modal-overlay.is-open .modal-panel {
            transform: translateY(0) scale(1);
        }

        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 24px 0;
        }

        .modal-title {
            margin: 0 0 6px;
            font-size: 24px;
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.04em;
        }

        .modal-subtitle {
            margin: 0;
            color: #67758b;
            font-size: 14px;
            line-height: 1.6;
        }

        .modal-close {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: 1px solid rgba(219, 227, 239, 0.95);
            background: rgba(255, 255, 255, 0.92);
            display: grid;
            place-items: center;
            color: #415066;
            cursor: pointer;
        }

        .modal-body {
            display: block;
            padding: 20px 24px 24px;
        }

        .car-form {
            display: grid;
            gap: 18px;
        }

        .modal-card {
            background: rgba(249, 251, 255, 0.96);
            border: 1px solid rgba(219, 227, 239, 0.82);
            border-radius: 22px;
            padding: 18px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .form-field {
            display: grid;
            gap: 8px;
        }

        .form-field.full {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 12px;
            font-weight: 700;
            color: #5c6980;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(219, 227, 239, 0.95);
            background: rgba(255, 255, 255, 0.92);
            color: #202636;
            font: inherit;
            padding: 14px 16px;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .form-textarea {
            min-height: 132px;
            resize: vertical;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: rgba(63, 94, 215, 0.5);
            box-shadow: 0 0 0 4px rgba(63, 94, 215, 0.08);
        }

        .form-hint {
            font-size: 12px;
            color: #728099;
            line-height: 1.5;
        }

        .error-text {
            color: #c53d3d;
            font-size: 12px;
            line-height: 1.5;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .secondary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-radius: 16px;
            border: 1px solid rgba(219, 227, 239, 0.95);
            background: rgba(255, 255, 255, 0.96);
            color: #202636;
            padding: 13px 18px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .status-action {
            box-shadow: none;
            border-radius: 999px;
            min-height: 36px;
            padding: 9px 14px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-action-maintenance {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.22);
        }

        .status-action-available {
            background: rgba(29, 187, 132, 0.12);
            color: #0f7f5c;
            border: 1px solid rgba(29, 187, 132, 0.18);
        }

        .status-action-verification {
            background: rgba(99, 102, 241, 0.12);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.18);
        }

        .status-action-payment {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.22);
        }

        .status-action-rented {
            background: rgba(59, 130, 246, 0.12);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, 0.18);
        }

        .modal-summary {
            display: grid;
            gap: 12px;
        }

        .summary-box {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, rgba(15, 29, 51, 0.96), rgba(20, 40, 71, 0.96));
            color: #eef4ff;
        }

        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(238, 244, 255, 0.66);
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 20px;
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.04em;
        }

        .summary-note {
            color: rgba(238, 244, 255, 0.72);
            font-size: 13px;
            line-height: 1.6;
            margin-top: 12px;
        }

        .upload-stack {
            display: grid;
            gap: 16px;
        }

        .upload-box {
            display: grid;
            gap: 14px;
            cursor: pointer;
        }

        .upload-input {
            display: none;
        }

        .upload-preview,
        .upload-gallery {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            border: 1px dashed rgba(197, 207, 224, 0.95);
            background: linear-gradient(180deg, rgba(244, 247, 252, 0.98), rgba(238, 243, 251, 0.96));
        }

        .upload-preview-main {
            min-height: 290px;
        }

        .upload-preview-main.has-image {
            min-height: 320px;
        }

        .upload-placeholder {
            min-height: inherit;
            display: grid;
            place-items: center;
            text-align: center;
            gap: 10px;
            padding: 32px;
            color: #67758b;
        }

        .upload-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #3f5ed7;
            background: #e8edff;
        }

        .upload-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: rgba(63, 94, 215, 0.08);
            color: #3f5ed7;
        }

        .upload-icon svg {
            width: 30px;
            height: 30px;
        }

        .upload-title {
            font-size: 18px;
            font-weight: 700;
            color: #202636;
        }

        .upload-text {
            font-size: 13px;
            line-height: 1.6;
            color: #67758b;
            max-width: 460px;
        }

        .upload-preview-main img {
            width: 100%;
            height: 100%;
            min-height: 290px;
            object-fit: cover;
            display: block;
        }

        .upload-remove-image {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.96);
            color: #202636;
            box-shadow: 0 8px 20px rgba(15, 29, 51, 0.16);
            display: grid;
            place-items: center;
            cursor: pointer;
            z-index: 2;
        }

        .upload-remove-image svg {
            width: 16px;
            height: 16px;
        }

        .upload-image-label {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(15, 29, 51, 0.78);
            color: #fff;
            backdrop-filter: blur(8px);
        }

        .upload-image-label span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(238, 244, 255, 0.72);
        }

        .upload-image-label strong {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-top: 4px;
        }

        .upload-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .gallery-count {
            white-space: nowrap;
            padding: 8px 10px;
            border-radius: 999px;
            background: #e8edff;
            color: #3f5ed7;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .upload-gallery {
            min-height: 180px;
            padding: 14px;
        }

        .gallery-empty {
            min-height: 150px;
            display: grid;
            place-items: center;
            text-align: center;
            gap: 10px;
            color: #67758b;
        }

        .gallery-empty svg {
            width: 42px;
            height: 42px;
        }

        .gallery-empty p {
            margin: 0;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1 / 1;
            border-radius: 16px;
            overflow: hidden;
            background: #dfe6f2;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .gallery-order {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(15, 29, 51, 0.8);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }

        @media (max-width: 1180px) {
            .stats-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .content-grid,
            .bottom-grid,
            .featured-layout,
            .filter-bar,
            .fleet-grid,
            .modal-body {
                grid-template-columns: 1fr;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .fleet-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .table-footer,
            .page-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 860px) {
            .backoffice-shell {
                grid-template-columns: 1fr;
            }

            .modal-overlay {
                inset: 0;
                padding: 18px;
            }

            .sidebar {
                position: relative;
                top: auto;
                height: auto;
                overflow: visible;
                padding-bottom: 18px;
            }

            .logout {
                margin-top: 16px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .fleet-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .topbar-right {
                justify-content: space-between;
            }

            .notification-menu {
                width: min(100vw - 32px, 420px);
                right: 0;
            }
        }

        @media (max-width: 560px) {
            .main {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .fleet-stats-grid {
                grid-template-columns: 1fr;
            }

            .profile {
                border-left: 0;
                padding-left: 0;
            }

            .modal-header {
                padding: 18px 18px 0;
            }

            .modal-body {
                padding: 16px 18px 18px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .user-table thead {
                display: none;
            }

            .user-table,
            .user-table tbody,
            .user-table tr,
            .user-table td {
                display: block;
                width: 100%;
            }

            .user-table tbody td {
                padding: 14px 18px;
            }

            .action-list {
                justify-content: flex-start;
            }

            .money {
                text-align: left;
            }
        }
    </style>
</head>
<body>
@php
    $initial = strtoupper(substr($admin->name ?? 'A', 0, 1));
@endphp
<div class="backoffice-shell">
    <aside class="sidebar {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}">
        <div class="brand">
            <h1 class="brand-title">MD CAR RENTAL</h1>
            <div class="brand-subtitle">Fleet Management</div>
        </div>

        <nav class="nav-list">
            <a href="{{ route('dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }} {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}" style="--nav-delay: 90ms">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 13h7V4H4v9Zm9 7h7v-7h-7v7Zm0-16v5h7V4h-7ZM4 20h7v-5H4v5Z"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('backoffice.users') }}" class="nav-item {{ $active === 'users' ? 'active' : '' }} {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}" style="--nav-delay: 150ms">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Manajemen User</span>
            </a>
            <a href="{{ route('backoffice.cars') }}" class="nav-item {{ $active === 'cars' ? 'active' : '' }} {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}" style="--nav-delay: 210ms">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M14 16H9m10 0h2m-7 0h1m-9 0h1m0 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0m8 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0M3 12l2-5h13l3 5"/>
                    <path d="M5 12v4m14-4v4M7 7V5h10v2"/>
                </svg>
                <span>Manajemen Mobil</span>
            </a>
            <a href="{{ route('backoffice.reservations') }}" class="nav-item {{ $active === 'reservations' ? 'active' : '' }} {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}" style="--nav-delay: 270ms">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M8 2v4m8-4v4M3 10h18"/>
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                </svg>
                <span>Reservasi</span>
            </a>
            <a href="{{ route('backoffice.reports') }}" class="nav-item {{ $active === 'reports' ? 'active' : '' }} {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}" style="--nav-delay: 330ms">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 3v18h18"/>
                    <path d="M7 14l4-4 3 3 5-7"/>
                </svg>
                <span>Laporan</span>
            </a>
            <a href="{{ route('backoffice.settings') }}" class="nav-item {{ $active === 'settings' ? 'active' : '' }} {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}" style="--nav-delay: 390ms">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8.91 4.6H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .67.39 1.28 1 1.51.16.06.33.09.5.09H21a2 2 0 1 1 0 4h-.09c-.67 0-1.28.39-1.51 1Z"/>
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>

        <div class="sidebar-profile">
            <div class="profile {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}">
                <div>
                    <div class="profile-name">{{ $admin->name }}</div>
                    <div class="profile-role">{{ strtoupper($admin->role) }}</div>
                </div>
                <div class="avatar">{{ $initial }}</div>
            </div>
        </div>

        <div class="logout">
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit">
                    <span class="nav-item {{ $active === 'dashboard' ? 'dashboard-animated' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <path d="m16 17 5-5-5-5"/>
                            <path d="M21 12H9"/>
                        </svg>
                        <span>Logout</span>
                    </span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        <div class="topbar {{ $active === 'reports' ? 'page-top-reveal opacity-0 -translate-y-4 transition-all duration-700 ease-out' : '' }}">
            <form method="GET" action="" style="margin: 0; display: block; flex: 1; max-width: min(100%, 520px);">
                @foreach (request()->except('search', 'page') as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $val)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label class="search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b869b" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input type="text" name="search" placeholder="{{ $searchPlaceholder }}" value="{{ request('search') }}" />
                </label>
            </form>

            <div class="topbar-right">
                <div class="notification-shell">
                    <button type="button" class="icon-button" id="backoffice-notification-button" aria-label="Notifikasi admin" aria-expanded="false">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f5d74" stroke-width="1.8">
                            <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5"/>
                            <path d="M10 21a2 2 0 0 0 4 0"/>
                        </svg>
                        @if ($adminNotificationCount > 0)
                            <span class="notification-badge">{{ $adminNotificationCount > 99 ? '99+' : $adminNotificationCount }}</span>
                        @endif
                    </button>

                    <div class="notification-menu" id="backoffice-notification-menu">
                        <div class="notification-menu-head">
                            <p class="notification-menu-title">Notifikasi Operasional</p>
                            <p class="notification-menu-subtitle">Reservasi masuk, butuh review, pembayaran masuk, dan pembayaran gagal.</p>

                            @if ($adminNotificationCount > 0)
                                <form method="POST" action="{{ route('backoffice.notifications.read-all') }}" style="margin-top: 12px;">
                                    @csrf
                                    <button type="submit" class="notification-read-button" style="border-color: rgba(255, 255, 255, 0.18); background: rgba(255, 255, 255, 0.92); color: #0f172a;">
                                        Tandai semua dibaca
                                    </button>
                                </form>
                            @endif

                            <div class="notification-summary-grid">
                                <div class="notification-summary-item">
                                    <div class="notification-summary-label">Reservasi Masuk</div>
                                    <div class="notification-summary-value">{{ $adminNotificationSummary['reservations'] }}</div>
                                </div>
                                <div class="notification-summary-item">
                                    <div class="notification-summary-label">Butuh Review</div>
                                    <div class="notification-summary-value">{{ $adminNotificationSummary['reviews'] }}</div>
                                </div>
                                <div class="notification-summary-item">
                                    <div class="notification-summary-label">Pembayaran Masuk</div>
                                    <div class="notification-summary-value">{{ $adminNotificationSummary['payments'] }}</div>
                                </div>
                                <div class="notification-summary-item">
                                    <div class="notification-summary-label">Gagal</div>
                                    <div class="notification-summary-value">{{ $adminNotificationSummary['failed'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="notification-list">
                            @forelse ($adminNotificationItems as $item)
                                @php
                                    $data = $item->data ?? [];
                                    $category = (string) ($data['category'] ?? 'reservations');
                                    $isUnread = is_null($item->read_at);
                                    $tone = match ($category) {
                                        'reviews' => 'amber',
                                        'payments' => 'green',
                                        'failed' => 'red',
                                        default => 'blue',
                                    };
                                    $label = match ($category) {
                                        'reviews' => 'Review',
                                        'payments' => 'Pembayaran',
                                        'failed' => 'Gagal',
                                        default => 'Reservasi',
                                    };
                                @endphp
                                <div class="notification-item {{ $isUnread ? 'is-unread' : '' }}">
                                    <div class="notification-item-top">
                                        <span class="notification-chip {{ $tone }}">{{ $label }}</span>
                                        <span class="notification-time">{{ $item->created_at?->locale('id')->diffForHumans() ?? '' }}</span>
                                    </div>
                                    <div class="notification-item-title">{{ $data['title'] ?? 'Notifikasi Admin' }}</div>
                                    <p class="notification-item-message">{{ $data['message'] ?? '' }}</p>
                                    <div class="notification-item-meta">{{ $data['meta'] ?? ('Booking #'.($data['rental_id'] ?? '-')) }}</div>
                                    <div class="notification-actions">
                                        <a href="{{ route('backoffice.notifications.open', $item->id) }}" class="notification-link-button">
                                            Buka
                                        </a>
                                        @if ($isUnread)
                                            <form method="POST" action="{{ route('backoffice.notifications.read', $item->id) }}">
                                                @csrf
                                                <button type="submit" class="notification-read-button">Tandai dibaca</button>
                                            </form>
                                        @else
                                            <span class="notification-read-label">Sudah dibaca</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="notification-empty">
                                    <strong>Belum ada update operasional</strong>
                                    Semua aktivitas reservasi dan pembayaran penting akan tampil di sini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{ $slot }}
    </main>
</div>
<script>
    (function () {
        const notificationButton = document.getElementById('backoffice-notification-button');
        const notificationMenu = document.getElementById('backoffice-notification-menu');

        if (!notificationButton || !notificationMenu) {
            return;
        }

        function closeNotificationMenu() {
            notificationMenu.classList.remove('is-open');
            notificationButton.setAttribute('aria-expanded', 'false');
        }

        notificationButton.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const isOpen = notificationMenu.classList.contains('is-open');
            if (isOpen) {
                closeNotificationMenu();
                return;
            }

            notificationMenu.classList.add('is-open');
            notificationButton.setAttribute('aria-expanded', 'true');
        });

        document.addEventListener('click', function (event) {
            if (!notificationButton.contains(event.target) && !notificationMenu.contains(event.target)) {
                closeNotificationMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeNotificationMenu();
            }
        });
    })();
</script>
@stack('scripts')
</body>
</html>
