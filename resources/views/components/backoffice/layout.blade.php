@props([
    'title' => 'Backoffice',
    'admin' => null,
    'active' => 'dashboard',
    'searchPlaceholder' => 'Cari data, reservasi, atau mobil...',
])

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
        }

        .sidebar {
            background: linear-gradient(180deg, #10203a 0%, #142847 100%);
            color: #f6f8fc;
            padding: 28px 22px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 24px 0 60px rgba(15, 29, 51, 0.16);
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
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            box-shadow: inset 3px 0 0 #4f74ff;
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            opacity: 0.9;
        }

        .logout {
            margin-top: auto;
            padding-top: 28px;
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

        .main {
            padding: 20px 28px 28px;
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
            overflow: hidden;
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
        }

        .mini-action {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(219, 227, 239, 0.9);
            color: #4a5568;
            background: rgba(255, 255, 255, 0.9);
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

        @media (max-width: 1180px) {
            .stats-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .content-grid,
            .bottom-grid,
            .featured-layout,
            .filter-bar,
            .fleet-grid {
                grid-template-columns: 1fr;
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

            .sidebar {
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
    <aside class="sidebar">
        <div class="brand">
            <h1 class="brand-title">MD RENTAL CAR</h1>
            <div class="brand-subtitle">Fleet Management</div>
        </div>

        <nav class="nav-list">
            <a href="{{ route('dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 13h7V4H4v9Zm9 7h7v-7h-7v7Zm0-16v5h7V4h-7ZM4 20h7v-5H4v5Z"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('backoffice.users') }}" class="nav-item {{ $active === 'users' ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Manajemen User</span>
            </a>
            <a href="{{ route('backoffice.cars') }}" class="nav-item {{ $active === 'cars' ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M14 16H9m10 0h2m-7 0h1m-9 0h1m0 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0m8 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0M3 12l2-5h13l3 5"/>
                    <path d="M5 12v4m14-4v4M7 7V5h10v2"/>
                </svg>
                <span>Manajemen Mobil</span>
            </a>
            <a href="#" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M8 2v4m8-4v4M3 10h18"/>
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                </svg>
                <span>Reservasi</span>
            </a>
            <a href="#" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 3v18h18"/>
                    <path d="M7 14l4-4 3 3 5-7"/>
                </svg>
                <span>Laporan</span>
            </a>
            <a href="#" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8.91 4.6H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .67.39 1.28 1 1.51.16.06.33.09.5.09H21a2 2 0 1 1 0 4h-.09c-.67 0-1.28.39-1.51 1Z"/>
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>

        <div class="logout">
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit">
                    <span class="nav-item">
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
        <div class="topbar">
            <label class="search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b869b" stroke-width="2">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>
                <input type="text" placeholder="{{ $searchPlaceholder }}" />
            </label>

            <div class="topbar-right">
                <div class="icon-button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f5d74" stroke-width="1.8">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5"/>
                        <path d="M10 21a2 2 0 0 0 4 0"/>
                    </svg>
                </div>
                <div class="icon-button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f5d74" stroke-width="1.8">
                        <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/>
                    </svg>
                </div>
                <div class="profile">
                    <div>
                        <div class="profile-name">{{ $admin->name }}</div>
                        <div class="profile-role">{{ strtoupper($admin->role) }}</div>
                    </div>
                    <div class="avatar">{{ $initial }}</div>
                </div>
            </div>
        </div>

        {{ $slot }}
    </main>
</div>
</body>
</html>
