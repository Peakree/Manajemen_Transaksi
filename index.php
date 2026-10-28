<?php
session_start();
require_once 'includes/config.php';

if (empty($_SESSION['is_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --primary: #b07d62;
    --primary-dark: #946652;
    --primary-light: #f3e8df;
    --success: #5a8f6a;
    --danger: #c46b6b;
    --warning: #c49a4a;
    --bg: #f7f3ee;
    --bg-warm: #faf6f1;
    --card: #fffcf8;
    --text: #3d3530;
    --muted: #8a7f76;
    --border: #e8dfd4;
    --nav-h: 72px;
    --max-w: 1180px;
    --shadow-sm: 0 2px 8px rgba(61, 53, 48, 0.06);
    --shadow-md: 0 8px 24px rgba(61, 53, 48, 0.08);
    --shadow-lg: 0 16px 40px rgba(61, 53, 48, 0.12);
    --radius: 14px;
    --radius-sm: 10px;
    --ease: cubic-bezier(0.4, 0, 0.2, 1);
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes slideInModal {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
  }
  @keyframes growBar {
    from { transform: scaleY(0); }
    to { transform: scaleY(1); }
  }
  @keyframes gentlePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(176, 125, 98, 0.3); }
    50% { box-shadow: 0 0 0 6px rgba(176, 125, 98, 0); }
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
    background: var(--bg);
    background-image:
      radial-gradient(ellipse at 15% 10%, rgba(176, 125, 98, 0.1) 0%, transparent 45%),
      radial-gradient(ellipse at 85% 90%, rgba(90, 143, 106, 0.08) 0%, transparent 45%);
    color: var(--text);
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
  }

  /* APP SHELL — header atas + konten tengah, tanpa sidebar */
  .app { min-height: 100vh; display: flex; flex-direction: column; }

  .app-header {
    position: sticky;
    top: 12px;
    z-index: 100;
    margin: 12px auto 0;
    width: calc(100% - 32px);
    max-width: var(--max-w);
    height: var(--nav-h);
    padding: 0 10px 0 18px;
    display: flex;
    align-items: center;
    gap: 16px;
    background: rgba(255, 252, 248, 0.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: 20px;
    box-shadow: var(--shadow-md);
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
  }
  .brand-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(176, 125, 98, 0.35);
  }
  .brand-text strong {
    display: block;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: -0.3px;
    line-height: 1.2;
  }
  .brand-text span {
    font-size: 10px;
    color: var(--muted);
    font-weight: 500;
  }

  .nav-pills {
    flex: 1;
    display: flex;
    justify-content: center;
    gap: 6px;
    padding: 5px;
    background: var(--bg-warm);
    border-radius: 14px;
    border: 1px solid var(--border);
  }
  .nav-item {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border: none;
    background: transparent;
    color: var(--muted);
    cursor: pointer;
    transition: all 0.3s var(--ease);
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    border-radius: 10px;
    white-space: nowrap;
  }
  .nav-item:hover {
    color: var(--text);
    background: rgba(255,255,255,0.7);
  }
  .nav-item.active {
    background: var(--card);
    color: var(--primary-dark);
    box-shadow: var(--shadow-sm);
  }
  .nav-icon { font-size: 15px; }

  .header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
  }
  .date-badge {
    font-size: 12px;
    color: var(--muted);
    font-weight: 500;
    padding: 7px 12px;
    background: var(--bg-warm);
    border-radius: 10px;
    border: 1px solid var(--border);
  }
  .badge-user {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
    border-radius: 12px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    box-shadow: var(--shadow-sm);
    animation: gentlePulse 3s ease-in-out infinite;
  }

  .app-main {
    flex: 1;
    width: 100%;
    max-width: var(--max-w);
    margin: 0 auto;
    padding: 28px 16px 48px;
  }

  .page-heading {
    margin-bottom: 28px;
    animation: fadeInUp 0.45s var(--ease) both;
  }
  .page-heading h1 {
    font-size: 32px;
    font-weight: 800;
    letter-spacing: -0.8px;
    line-height: 1.15;
    margin-bottom: 6px;
  }
  .page-subtitle {
    font-size: 15px;
    color: var(--muted);
    font-weight: 500;
  }

  .page { display: none; }
  .page.active {
    display: block;
    animation: fadeInUp 0.4s var(--ease) both;
  }

  /* DASHBOARD — hero saldo + bento grid */
  .saldo-hero {
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    padding: 32px 36px;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #3d3530 0%, #5c4a3e 50%, #6b5344 100%);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 20px 50px rgba(61, 53, 48, 0.25);
    animation: fadeInUp 0.45s var(--ease) 0.05s both;
  }
  .saldo-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(232,196,168,0.18) 0%, transparent 70%);
    border-radius: 50%;
  }
  .saldo-hero-label {
    font-size: 13px;
    font-weight: 600;
    opacity: 0.75;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
  }
  .saldo-hero .stat-value {
    font-size: 42px;
    font-weight: 800;
    letter-spacing: -1.5px;
    color: #fff;
    line-height: 1;
    margin-bottom: 10px;
  }
  .saldo-hero-meta {
    font-size: 13px;
    opacity: 0.65;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .saldo-hero-meta::before {
    content: '';
    width: 8px; height: 8px;
    background: #7ec896;
    border-radius: 50%;
    animation: gentlePulse 2s ease-in-out infinite;
  }
  .saldo-hero-visual {
    font-size: 72px;
    opacity: 0.15;
    line-height: 1;
    user-select: none;
  }

  .metrics-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 20px;
  }

 .dashboard-bento {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
  .bento-chart { min-height: 320px; }
  .bento-recent {
    display: flex;
    flex-direction: column;
    max-height: 420px;
  }
  .bento-recent .section-title { flex-shrink: 0; }
  #recent-list {
    flex: 1;
    overflow-y: auto;
    padding-right: 4px;
  }
  #recent-list::-webkit-scrollbar { width: 4px; }
  #recent-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

  .recent-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 14px;
    margin-bottom: 8px;
    background: var(--bg-warm);
    border-radius: 12px;
    border: 1px solid var(--border);
    transition: transform 0.2s var(--ease), box-shadow 0.2s var(--ease);
    animation: fadeInUp 0.35s var(--ease) both;
  }
  .recent-item:hover {
    transform: translateX(4px);
    box-shadow: var(--shadow-sm);
  }
  .recent-kode { font-size: 13px; font-weight: 700; }
  .recent-date { font-size: 11px; color: var(--muted); margin-top: 2px; }
  .recent-amount { font-weight: 800; font-size: 14px; margin-bottom: 4px; }

  .chart-legend {
    display: flex;
    gap: 20px;
    margin-top: 14px;
    font-size: 12px;
    color: var(--muted);
    font-weight: 500;
  }
  .legend-dot {
    display: inline-block;
    width: 10px; height: 10px;
    border-radius: 50%;
    margin-right: 6px;
    vertical-align: middle;
  }

  /* HALAMAN DATA — layout split panel */
  .page-split {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 16px;
    align-items: start;
  }

  .side-panel {
    position: sticky;
    top: calc(var(--nav-h) + 28px);
    padding: 22px !important;
  }
  .side-panel-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--muted);
    margin-bottom: 18px;
  }
  .side-panel .form-group { margin-bottom: 14px; }
  .side-panel .form-group label { font-size: 12px; margin-bottom: 5px; }
  .side-panel .search-input { width: 100%; }
  .side-panel .btn-primary { width: 100%; justify-content: center; margin-top: 8px; padding: 12px; }

  .data-panel { padding: 0 !important; overflow: hidden; }
  .data-panel-head {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .data-panel-head h3 { font-size: 15px; font-weight: 700; }
  .data-panel-head span { font-size: 12px; color: var(--muted); }
  .data-panel .table-wrap { padding: 8px 12px 16px; }

  /* LAPORAN — filter bar horizontal */
  .laporan-filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 16px;
    padding: 22px 26px !important;
    margin-bottom: 16px;
  }
  .laporan-filter-bar .form-group { margin-bottom: 0; }
  .laporan-filter-bar label { font-size: 12px; }
  .laporan-filter-bar select,
  .laporan-filter-bar input[type="number"] { width: auto; min-width: 130px; }

  @media (max-width: 960px) {
    .app-header { flex-wrap: wrap; height: auto; padding: 12px; gap: 10px; }
    .nav-pills { order: 3; width: 100%; overflow-x: auto; justify-content: flex-start; }
    .nav-item { padding: 8px 12px; font-size: 12px; }
    .date-badge { display: none; }
    .metrics-strip { grid-template-columns: 1fr; }
    .dashboard-bento { grid-template-columns: 1fr; }
    .page-split { grid-template-columns: 1fr; }
    .side-panel { position: static; }
    .saldo-hero { flex-direction: column; align-items: flex-start; gap: 12px; }
    .saldo-hero-visual { display: none; }
    .page-heading h1 { font-size: 26px; }
  }

  /* CARDS */
  .card {
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    padding: 22px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow 0.3s var(--ease), transform 0.3s var(--ease);
  }
  .card:hover { box-shadow: var(--shadow-md); }

  .grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: var(--card);
    border-radius: var(--radius);
    padding: 20px 22px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    transition: transform 0.3s var(--ease), box-shadow 0.3s var(--ease);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .stat-card::after {
    content: attr(data-icon);
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 28px;
    opacity: 0.12;
  }
  .metrics-strip .stat-card:nth-child(1) { animation: fadeInUp 0.4s var(--ease) 0.1s both; }
  .metrics-strip .stat-card:nth-child(2) { animation: fadeInUp 0.4s var(--ease) 0.15s both; }
  .metrics-strip .stat-card:nth-child(3) { animation: fadeInUp 0.4s var(--ease) 0.2s both; }
  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
  }

  .stat-label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .stat-value { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
  .stat-sub { font-size: 12px; color: var(--muted); margin-top: 6px; }
  .stat-green .stat-value { color: var(--success); }
  .stat-red .stat-value { color: var(--danger); }
  .stat-blue .stat-value { color: var(--primary); }
  .stat-orange .stat-value { color: var(--warning); }
  .stat-green { border-left: 3px solid var(--success); }
  .stat-red { border-left: 3px solid var(--danger); }
  .stat-orange { border-left: 3px solid var(--warning); }

  /* TABLE */
  .table-wrap { overflow-x: auto; border-radius: var(--radius-sm); }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th {
    background: var(--bg-warm);
    padding: 12px 14px;
    text-align: left;
    font-weight: 600;
    color: var(--muted);
    border-bottom: 1px solid var(--border);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  td {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(232, 223, 212, 0.6);
    transition: background 0.2s var(--ease);
  }
  tr:hover td { background: rgba(247, 243, 238, 0.7); }
  tr:last-child td { border-bottom: none; }

  .badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.2px;
  }
  .badge-green { background: #e4f0e7; color: #3d6b4a; }
  .badge-red { background: #f5e4e4; color: #8f4545; }
  .badge-yellow { background: #f5eed9; color: #8a6d2e; }
  .badge-blue { background: var(--primary-light); color: var(--primary-dark); }
  .badge-gray { background: #ede8e2; color: #5c534c; }

  /* BUTTONS */
  .btn {
    padding: 9px 18px;
    border-radius: var(--radius-sm);
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    transition: all 0.25s var(--ease);
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .btn:active { transform: scale(0.97); }
  .btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
    box-shadow: 0 4px 12px rgba(176, 125, 98, 0.3);
  }
  .btn-primary:hover {
    box-shadow: 0 6px 18px rgba(176, 125, 98, 0.4);
    transform: translateY(-1px);
  }
  .btn-success {
    background: linear-gradient(135deg, #6a9f7a, var(--success));
    color: #fff;
    box-shadow: 0 2px 8px rgba(90, 143, 106, 0.25);
  }
  .btn-success:hover { transform: translateY(-1px); }
  .btn-danger {
    background: linear-gradient(135deg, #d07f7f, var(--danger));
    color: #fff;
    box-shadow: 0 2px 8px rgba(196, 107, 107, 0.25);
  }
  .btn-danger:hover { transform: translateY(-1px); }
  .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 8px; }
  .btn-outline {
    background: var(--card);
    border: 1px solid var(--border);
    color: var(--text);
  }
  .btn-outline:hover {
    background: var(--bg-warm);
    border-color: var(--primary);
    color: var(--primary-dark);
  }

  /* FORM */
  .form-group { margin-bottom: 18px; }
  label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 7px;
    color: var(--text);
  }
  input, select, textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-family: inherit;
    transition: all 0.25s var(--ease);
    background: var(--bg-warm);
    color: var(--text);
  }
  input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(176, 125, 98, 0.12);
  }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
  .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

  /* MODAL */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(61, 53, 48, 0.45);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 200;
    align-items: center;
    justify-content: center;
  }
  .modal-overlay.open {
    display: flex;
    animation: fadeIn 0.25s var(--ease);
  }
  .modal-overlay.open .modal {
    animation: slideInModal 0.35s var(--ease) both;
  }
  .modal {
    background: var(--card);
    border-radius: 18px;
    width: 90%;
    max-width: 620px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border);
  }
  .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .modal-header h3 { font-size: 17px; font-weight: 700; letter-spacing: -0.2px; }
  .modal-close {
    background: var(--bg-warm);
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: var(--muted);
    line-height: 1;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    transition: all 0.2s var(--ease);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .modal-close:hover { background: var(--border); color: var(--text); }
  .modal-body { padding: 24px; }
  .modal-footer {
    padding: 18px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: var(--bg-warm);
    border-radius: 0 0 18px 18px;
  }

  /* TOOLBAR — tetap ada untuk kompatibilitas */
  .toolbar {
    display: none;
  }
  .search-input {
    padding: 9px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: inherit;
    width: 240px;
    background: var(--card);
    transition: all 0.25s var(--ease);
  }
  .search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(176, 125, 98, 0.1);
  }

  /* CHART */
  .chart-bars {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    height: 130px;
    padding: 12px 0;
  }
  .bar-group {
    display: flex;
    gap: 4px;
    align-items: flex-end;
    flex: 1;
  }
  .bar {
    border-radius: 6px 6px 0 0;
    transition: opacity 0.2s, transform 0.2s var(--ease);
    transform-origin: bottom;
    animation: growBar 0.6s var(--ease) both;
  }
  .bar-group:nth-child(1) .bar { animation-delay: 0.05s; }
  .bar-group:nth-child(2) .bar { animation-delay: 0.1s; }
  .bar-group:nth-child(3) .bar { animation-delay: 0.15s; }
  .bar-group:nth-child(4) .bar { animation-delay: 0.2s; }
  .bar-group:nth-child(5) .bar { animation-delay: 0.25s; }
  .bar-group:nth-child(6) .bar { animation-delay: 0.3s; }
  .bar-group:nth-child(7) .bar { animation-delay: 0.35s; }
  .bar:hover { opacity: 0.85; transform: scaleY(1.05); }
  .chart-labels { display: flex; gap: 10px; margin-top: 8px; }
  .chart-label { flex: 1; text-align: center; font-size: 10px; color: var(--muted); font-weight: 500; }

  /* ALERT */
  .alert {
    padding: 14px 18px;
    border-radius: var(--radius-sm);
    margin-bottom: 16px;
    font-size: 14px;
  }
  .alert-success { background: #e4f0e7; color: #3d6b4a; border: 1px solid #c5ddcb; }
  .alert-danger { background: #f5e4e4; color: #8f4545; border: 1px solid #e8c4c4; }

  /* ITEM PRODUK DI FORM */
  .item-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 10px;
    align-items: center;
    margin-bottom: 10px;
    padding: 10px;
    background: var(--bg-warm);
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    animation: fadeInUp 0.3s var(--ease) both;
  }
  .item-subtotal { font-weight: 700; color: var(--primary); font-size: 13px; }

  .section-title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
    letter-spacing: -0.2px;
  }
  .empty-state {
    text-align: center;
    padding: 48px 40px;
    color: var(--muted);
  }
  .empty-state .empty-icon {
    font-size: 48px;
    margin-bottom: 14px;
    opacity: 0.7;
    animation: fadeInUp 0.5s var(--ease);
  }

  code {
    background: var(--primary-light);
    color: var(--primary-dark);
    padding: 2px 7px;
    border-radius: 6px;
    font-size: 12px;
    font-family: inherit;
    font-weight: 600;
  }

  .top-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 0;
    border-bottom:1px solid #eee;
}

.top-item:last-child{
    border-bottom:none;
}

.top-rank{
    font-weight:700;
}

.top-sold{
    color:#666;
    font-size:13px;
}
</style>
</head>
<body>

<div class="app">

  <!-- HEADER ATAS — menggantikan sidebar + topbar -->
  <header class="app-header">
    <div class="brand">
      <div class="brand-icon">💰</div>
      <div class="brand-text">
        <strong><?= APP_NAME ?></strong>
        <span>v<?= APP_VERSION ?></span>
      </div>
    </div>
    <nav class="nav-pills">
      <button type="button" class="nav-item active" onclick="showPage('dashboard', this)"><span class="nav-icon">📊</span> Dashboard</button>
      <button type="button" class="nav-item" onclick="showPage('transaksi', this)"><span class="nav-icon">📋</span> Transaksi</button>
      <button type="button" class="nav-item" onclick="showPage('produk', this)"><span class="nav-icon">📦</span> Produk</button>
      <button type="button" class="nav-item" onclick="showPage('laporan', this)"><span class="nav-icon">📈</span> Laporan</button>
    </nav>
    <div class="header-actions">
      <span class="date-badge"><?= date('d M Y') ?></span>
      <a href="profile.php" class="badge-user" title="Lihat profil" style="text-decoration:none;">A</a>
    </div>
  </header>

  <main class="app-main">
    <div class="page-heading">
      <h1 id="page-title">Dashboard</h1>
      <p class="page-subtitle" id="page-subtitle">Ringkasan keuangan bulan ini</p>
    </div>

    <!-- ======================== DASHBOARD ======================== -->
    <div id="page-dashboard" class="page active">

      <div class="saldo-hero">
        <div>
          <div class="saldo-hero-label">Saldo Bersih Bulan Ini</div>
          <div class="stat-value" id="s-saldo">-</div>
          <div class="saldo-hero-meta" id="s-hari">- transaksi hari ini</div>
        </div>
        <div class="saldo-hero-visual">✦</div>
      </div>

      <div class="metrics-strip" id="stat-cards">
        <div class="stat-card stat-green" data-icon="↑">
          <div class="stat-label">Pemasukan</div>
          <div class="stat-value" id="s-pemasukan">-</div>
        </div>
        <div class="stat-card stat-red" data-icon="↓">
          <div class="stat-label">Pengeluaran</div>
          <div class="stat-value" id="s-pengeluaran">-</div>
        </div>
        <div class="stat-card stat-orange" data-icon="⏳">
          <div class="stat-label">Pending</div>
          <div class="stat-value" id="s-pending">-</div>
        </div>
      </div>

    <div class="dashboard-bento">

    <div class="card">
    <div class="card-header">
        <h5>⚠️ Stok Menipis</h5>
    </div>
    <div class="card-body">
        <ul id="stokMenipisList">
            <li>Memuat data...</li>
        </ul>
    </div>
</div>

  <!-- Grafik -->
  <div class="card bento-chart">
    <div class="section-title">
      📈 Grafik Penjualan
    </div>

    <div id="chart-bars" class="chart-bars">
      <p>Memuat...</p>
    </div>

    <div id="chart-labels" class="chart-labels"></div>

    <div class="chart-legend">
      <span>
        <span class="legend-dot" style="background:#34d399"></span>
        Pemasukan
      </span>

      <span>
        <span class="legend-dot" style="background:#f87171"></span>
        Pengeluaran
      </span>
    </div>
  </div>

  <!-- Transaksi Terbaru -->
  <div class="card bento-recent">
    <div class="section-title">
      🧾 Transaksi Terbaru
    </div>

    <div id="recent-list">
      <div class="empty-state">
        Memuat data...
      </div>
    </div>
  </div>

  <!-- Top Produk -->
  <div class="card">
    <div class="section-title">
      🏆 Top Produk Terlaris
    </div>

    <div id="top-produk-list">
      <div class="empty-state">
        Belum ada data penjualan
      </div>
    </div>
  </div>

</div>
</div> <!-- TUTUP page-dashboard -->

    <!-- ======================== TRANSAKSI ======================== -->
    <div id="page-transaksi" class="page">
      <div class="page-split">
        <aside class="card side-panel">
          <div class="side-panel-title">Filter & Aksi</div>
          <div class="form-group">
            <label>Cari</label>
            <input class="search-input" type="text" placeholder="Kode / keterangan..." oninput="filterTransaksi(this.value)" />
          </div>
          <div class="form-group">
            <label>Jenis</label>
            <select onchange="filterJenis(this.value)">
              <option value="">Semua Jenis</option>
              <option value="pemasukan">Pemasukan</option>
              <option value="pengeluaran">Pengeluaran</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select onchange="filterStatus(this.value)">
              <option value="">Semua Status</option>
              <option value="selesai">Selesai</option>
              <option value="pending">Pending</option>
              <option value="batal">Batal</option>
            </select>
          </div>
          <button class="btn btn-primary" onclick="openModalTransaksi()">+ Tambah Transaksi</button>
        </aside>
        <div class="card data-panel">
          <div class="data-panel-head">
            <h3>Daftar Transaksi</h3>
            <span>Semua data transaksi</span>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>Kode</th><th>Tanggal</th><th>Jenis</th><th>Keterangan</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
              </thead>
              <tbody id="tbody-transaksi">
                <tr><td colspan="7" class="empty-state">Memuat data...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ======================== PRODUK
     ======================== -->
    <div id="page-produk" class="page">
      <div class="page-split">
        <aside class="card side-panel">
          <div class="side-panel-title">Filter & Aksi</div>
          <div class="form-group">
            <label>Cari Produk</label>
            <input class="search-input" type="text" placeholder="Nama / kode..." oninput="filterProduk(this.value)" />
          </div>
          <button class="btn btn-primary" onclick="openModalProduk()">+ Tambah Produk</button>
        </aside>
        <div class="card data-panel">
          <div class="data-panel-head">
            <h3>Daftar Produk</h3>
            <span>Manajemen stok & harga</span>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Satuan</th><th>Aksi</th></tr>
              </thead>
              <tbody id="tbody-produk">
                <tr><td colspan="7" class="empty-state">Memuat data...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ======================== LAPORAN ======================== -->
    <div id="page-laporan" class="page">
      <div class="card laporan-filter-bar">
        <div class="form-group">
          <label>Bulan</label>
          <select id="lap-bulan">
            <?php for($i=1;$i<=12;$i++) echo "<option value='$i'" . (date('m')==$i?' selected':'') . ">" . date('F', mktime(0,0,0,$i,1)) . "</option>"; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Tahun</label>
          <input type="number" id="lap-tahun" value="<?= date('Y') ?>" style="width:100px">
        </div>
        <button class="btn btn-primary" onclick="loadLaporan()">Tampilkan Laporan</button>
      </div>
      <div class="card data-panel">
        <div class="data-panel-head">
          <h3>Rekap Transaksi</h3>
          <span>Per periode yang dipilih</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Tanggal</th><th>Jenis</th><th>Jumlah Transaksi</th><th>Total Amount</th></tr></thead>
            <tbody id="tbody-laporan"><tr><td colspan="4" style="text-align:center;padding:20px;color:var(--muted)">Pilih periode dan klik Tampilkan</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- MODAL TRANSAKSI -->
<div id="modal-transaksi" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">
      <h3>📋 Tambah Transaksi</h3>
      <button class="modal-close" onclick="closeModal('modal-transaksi')">✕</button>
    </div>
    <div class="modal-body">
      <div class="grid-2">
        <div class="form-group">
          <label>Tanggal *</label>
          <input type="date" id="trx-tanggal" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-group">
          <label>Jenis Transaksi *</label>
          <select id="trx-jenis">
            <option value="pemasukan">Pemasukan</option>
            <option value="pengeluaran">Pengeluaran</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Keterangan</label>
        <textarea id="trx-keterangan" rows="2" placeholder="Deskripsi transaksi..."></textarea>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select id="trx-status">
          <option value="selesai">Selesai</option>
          <option value="pending">Pending</option>
        </select>
      </div>
      <div class="section-title">🛒 Item Produk <button class="btn btn-sm btn-outline" onclick="tambahItemRow()" style="float:right">+ Tambah Item</button></div>
      <div id="item-container"></div>
      <div style="text-align:right;margin-top:12px;font-size:16px;font-weight:800">
        Total: <span id="grand-total" style="color:var(--primary)">Rp 0</span>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-transaksi')">Batal</button>
      <button class="btn btn-primary" onclick="simpanTransaksi()">💾 Simpan Transaksi</button>
    </div>
  </div>
</div>

<!-- MODAL PRODUK -->
<div id="modal-produk" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">
      <h3>📦 <span id="produk-modal-title">Tambah Produk</span></h3>
      <button class="modal-close" onclick="closeModal('modal-produk')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="produk-id">
      <div class="form-group">
        <label>Nama Produk *</label>
        <input type="text" id="produk-nama" placeholder="Nama produk">
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label>Kategori</label>
          <select id="produk-kategori"><option value="">-- Pilih Kategori --</option></select>
        </div>
        <div class="form-group">
          <label>Satuan</label>
          <input type="text" id="produk-satuan" value="pcs" placeholder="pcs, kg, liter...">
        </div>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label>Harga (Rp) *</label>
          <input type="number" id="produk-harga" placeholder="0" min="0">
        </div>
        <div class="form-group">
          <label>Stok *</label>
          <input type="number" id="produk-stok" placeholder="0" min="0">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-produk')">Batal</button>
      <button class="btn btn-primary" onclick="simpanProduk()">💾 Simpan</button>

      
    </div>
  </div>
</div>

<script>
const API = 'api.php?action=';
let allTransaksi = [], allProduk = [], allKategori = [];
let filterJ = '', filterS = '', filterCari = '';

// ======================== NAVIGATION ========================
const PAGE_LABELS = { dashboard: 'Dashboard', transaksi: 'Transaksi', produk: 'Produk', laporan: 'Laporan' };
const PAGE_SUBTITLES = {
  dashboard: 'Ringkasan keuangan bulan ini',
  transaksi: 'Kelola semua transaksi masuk & keluar',
  produk: 'Manajemen produk, stok, dan harga',
  laporan: 'Rekap laporan per periode waktu'
};

function showPage(page, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('page-' + page).classList.add('active');
  el.classList.add('active');
  document.getElementById('page-title').textContent = PAGE_LABELS[page] || page;
  document.getElementById('page-subtitle').textContent = PAGE_SUBTITLES[page] || '';
  if (page === 'dashboard') loadDashboard();
  if (page === 'transaksi') loadTransaksi();
  if (page === 'produk') loadProduk();
}

// ======================== DASHBOARD ========================
async function loadDashboard() {
    const res = await fetch(API + 'dashboard');
    const j = await res.json();

    console.log(j);

    if (!j.success) return;

    const d = j.data;

    document.getElementById('s-pemasukan').textContent =
        formatRp(d.pemasukan_bulan);

    document.getElementById('s-pengeluaran').textContent =
        formatRp(d.pengeluaran_bulan);

    document.getElementById('s-saldo').textContent =
        formatRp(d.saldo);

    document.getElementById('s-pending').textContent =
        d.transaksi_pending;

    document.getElementById('s-hari').textContent =
        d.transaksi_hari + ' transaksi hari ini';

    // STOK MENIPIS
    const stokList = document.getElementById('stokMenipisList');
    const stokMenipis = d.stok_menipis || [];

    console.log(stokList);
    console.log(stokMenipis);

    stokList.innerHTML = '';

    stokMenipis.forEach(item => {
        stokList.innerHTML += `
            <li>${item.nama_produk} (Stok: ${item.stok})</li>
        `;
    });

    renderChart(d.grafik);
    renderRecent(d.transaksi_terbaru);
    renderTopProduk(d.top_produk || []);
}
function renderChart(data) {
  if (!data.length) { document.getElementById('chart-bars').innerHTML = '<p style="color:var(--muted);font-size:13px">Belum ada data</p>'; return; }
  const maxVal = Math.max(...data.map(d => Math.max(+d.pemasukan||0, +d.pengeluaran||0)), 1);
  const bars = data.map(d => {
    const ph = Math.round((+d.pemasukan||0) / maxVal * 100);
    const pn = Math.round((+d.pengeluaran||0) / maxVal * 100);
    return `<div class="bar-group">
      <div class="bar" style="height:${ph}px;width:50%;background:#34d399" title="Pemasukan: ${formatRp(d.pemasukan||0)}"></div>
      <div class="bar" style="height:${pn}px;width:50%;background:#f87171" title="Pengeluaran: ${formatRp(d.pengeluaran||0)}"></div>
    </div>`;
  }).join('');
  const labels = data.map(d => `<div class="chart-label">${d.tgl}</div>`).join('');
  document.getElementById('chart-bars').innerHTML = bars;
  document.getElementById('chart-labels').innerHTML = labels;
}

function renderRecent(list) {
  if (!list.length) { document.getElementById('recent-list').innerHTML = '<p style="color:var(--muted);font-size:13px;padding:20px;text-align:center">Belum ada transaksi</p>'; return; }
  document.getElementById('recent-list').innerHTML = list.map(t => `
    <div class="recent-item">
      <div>
        <div class="recent-kode">${t.kode_transaksi}</div>
        <div class="recent-date">${t.tanggal_transaksi}</div>
      </div>
      <div style="text-align:right">
        <div class="recent-amount" style="color:${t.jenis_transaksi==='pemasukan'?'var(--success)':'var(--danger)'}">${formatRp(t.total_amount)}</div>
        <span class="badge badge-${t.status==='selesai'?'green':t.status==='pending'?'yellow':'red'}">${t.status}</span>
      </div>
    </div>`).join('');
}

function renderTopProduk(data) {
    const el = document.getElementById('top-produk-list');

    if (!el) return;

    if (!data || data.length === 0) {
        el.innerHTML = `
            <div class="empty-state">
                Belum ada data penjualan
            </div>
        `;
        return;
    }

    el.innerHTML = data.map((p, i) => `
        <div class="top-item">
            <div class="top-rank">
                #${i + 1} ${p.nama_produk}
            </div>

            <div class="top-sold">
                ${p.total_terjual} Terjual
            </div>
        </div>
    `).join('');
}

// ======================== TRANSAKSI ========================
async function loadTransaksi() {
  const res = await fetch(API + 'get_transaksi');
  const j = await res.json();
  allTransaksi = j.data || [];
  renderTransaksi();
}

function renderTransaksi() {
  let data = allTransaksi;
  if (filterJ) data = data.filter(t => t.jenis_transaksi === filterJ);
  if (filterS) data = data.filter(t => t.status === filterS);
  if (filterCari) data = data.filter(t => t.kode_transaksi.toLowerCase().includes(filterCari) || (t.keterangan||'').toLowerCase().includes(filterCari));
  const tbody = document.getElementById('tbody-transaksi');
  if (!data.length) { tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📭</div>Tidak ada data transaksi</div></td></tr>'; return; }
  tbody.innerHTML = data.map(t => `
    <tr>
      <td><strong>${t.kode_transaksi}</strong></td>
      <td>${t.tanggal_transaksi}</td>
      <td><span class="badge badge-${t.jenis_transaksi==='pemasukan'?'green':'red'}">${t.jenis_transaksi}</span></td>
      <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${t.keterangan||'-'}</td>
      <td><strong>${formatRp(t.total_amount)}</strong></td>
      <td><span class="badge badge-${t.status==='selesai'?'green':t.status==='pending'?'yellow':'red'}">${t.status}</span></td>
      <td>
        ${t.status==='pending' ? `<button class="btn btn-sm btn-success" onclick="updateStatus(${t.id},'selesai')">✓</button> ` : ''}
        <button class="btn btn-sm btn-danger" onclick="hapusTransaksi(${t.id},'${t.kode_transaksi}')">🗑</button>
      </td>
    </tr>`).join('');
}

function filterTransaksi(v) { filterCari = v.toLowerCase(); renderTransaksi(); }
function filterJenis(v) { filterJ = v; renderTransaksi(); }
function filterStatus(v) { filterS = v; renderTransaksi(); }

async function updateStatus(id, status) {
  await fetch(API + 'update_status', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id, status}) });
  loadTransaksi();
}

async function hapusTransaksi(id, kode) {
  if (!confirm(`Hapus transaksi ${kode}?`)) return;
  await fetch(API + 'hapus_transaksi', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id}) });
  loadTransaksi();
}

// ======================== MODAL TRANSAKSI ========================
let itemRows = [];
async function openModalTransaksi() {
  itemRows = [];
  document.getElementById('item-container').innerHTML = '';
  document.getElementById('grand-total').textContent = 'Rp 0';
  document.getElementById('trx-tanggal').value = '<?= date('Y-m-d') ?>';
  document.getElementById('trx-keterangan').value = '';
  if (!allProduk.length) await loadProduk(true);
  tambahItemRow();
  document.getElementById('modal-transaksi').classList.add('open');
}

function tambahItemRow() {
  const idx = itemRows.length;
  itemRows.push({ produk_id: '', jumlah: 1, harga: 0 });
  const opts = allProduk.map(p => `<option value="${p.id}" data-harga="${p.harga}">${p.nama_produk} (${formatRp(p.harga)})</option>`).join('');
  const row = document.createElement('div');
  row.className = 'item-row';
  row.id = 'item-row-' + idx;
  row.innerHTML = `
    <select onchange="pilihProduk(${idx},this)"><option value="">-- Pilih Produk --</option>${opts}</select>
    <input type="number" min="1" value="1" placeholder="Qty" oninput="updateQty(${idx},this.value)">
    <div class="item-subtotal" id="sub-${idx}">Rp 0</div>
    <button class="btn btn-sm btn-danger" onclick="hapusItemRow(${idx})">✕</button>`;
  document.getElementById('item-container').appendChild(row);
}

function pilihProduk(idx, sel) {
  const opt = sel.options[sel.selectedIndex];
  itemRows[idx].produk_id = sel.value;
  itemRows[idx].harga = parseFloat(opt.dataset.harga || 0);
  hitungSubtotal(idx);
}

function updateQty(idx, v) {
  itemRows[idx].jumlah = parseInt(v) || 0;
  hitungSubtotal(idx);
}

function hitungSubtotal(idx) {
  const sub = itemRows[idx].jumlah * itemRows[idx].harga;
  document.getElementById('sub-' + idx).textContent = formatRp(sub);
  const total = itemRows.reduce((a, r) => a + r.jumlah * r.harga, 0);
  document.getElementById('grand-total').textContent = formatRp(total);
}

function hapusItemRow(idx) {
  const el = document.getElementById('item-row-' + idx);
  if (el) { el.remove(); itemRows[idx] = { produk_id:'', jumlah:0, harga:0 }; }
  const total = itemRows.reduce((a, r) => a + r.jumlah * r.harga, 0);
  document.getElementById('grand-total').textContent = formatRp(total);
}

async function simpanTransaksi() {
  const items = itemRows.filter(r => r.produk_id);
  const total = items.reduce((a, r) => a + r.jumlah * r.harga, 0);
  const body = {
    tanggal: document.getElementById('trx-tanggal').value,
    jenis: document.getElementById('trx-jenis').value,
    keterangan: document.getElementById('trx-keterangan').value,
    status: document.getElementById('trx-status').value,
    total, items
  };
  if (!body.tanggal) { alert('Tanggal wajib diisi!'); return; }
  const res = await fetch(API + 'simpan_transaksi', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) });
  const j = await res.json();
  if (j.success) { closeModal('modal-transaksi'); loadTransaksi(); alert('✅ ' + j.message + '\nKode: ' + j.kode); }
  else alert('❌ ' + j.message);
}

// ======================== PRODUK ========================
async function loadProduk(silent = false) {
  const [rP, rK] = await Promise.all([fetch(API + 'get_produk'), fetch(API + 'get_kategori')]);
  const [jP, jK] = await Promise.all([rP.json(), rK.json()]);
  allProduk = jP.data || [];
  allKategori = jK.data || [];
  if (!silent) renderProduk(allProduk);
  // Isi select kategori di modal
  const sel = document.getElementById('produk-kategori');
  sel.innerHTML = '<option value="">-- Pilih Kategori --</option>' + allKategori.map(k => `<option value="${k.id}">${k.nama_kategori}</option>`).join('');
}

function renderProduk(data) {
  const tbody = document.getElementById('tbody-produk');
  if (!data.length) { tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📦</div>Belum ada produk</div></td></tr>'; return; }
  tbody.innerHTML = data.map(p => `
    <tr>
      <td><code>${p.kode_produk}</code></td>
      <td><strong>${p.nama_produk}</strong></td>
      <td>${p.nama_kategori || '-'}</td>
      <td>${formatRp(p.harga)}</td>
      <td><span class="badge ${p.stok < 10 ? 'badge-red' : 'badge-green'}">${p.stok}</span></td>
      <td>${p.satuan}</td>
      <td>
        <button class="btn btn-sm btn-outline" onclick="editProduk(${p.id})">✏️</button>
        <button class="btn btn-sm btn-danger" onclick="hapusProduk(${p.id},'${p.nama_produk}')">🗑</button>
      </td>
    </tr>`).join('');
}

function filterProduk(v) {
  const q = v.toLowerCase();
  renderProduk(allProduk.filter(p => p.nama_produk.toLowerCase().includes(q) || p.kode_produk.toLowerCase().includes(q)));
}

function openModalProduk() {
  document.getElementById('produk-id').value = '';
  document.getElementById('produk-nama').value = '';
  document.getElementById('produk-harga').value = '';
  document.getElementById('produk-stok').value = '';
  document.getElementById('produk-satuan').value = 'pcs';
  document.getElementById('produk-kategori').value = '';
  document.getElementById('produk-modal-title').textContent = 'Tambah Produk';
  document.getElementById('modal-produk').classList.add('open');
}

function editProduk(id) {
  const p = allProduk.find(x => x.id == id);
  if (!p) return;
  document.getElementById('produk-id').value = p.id;
  document.getElementById('produk-nama').value = p.nama_produk;
  document.getElementById('produk-harga').value = p.harga;
  document.getElementById('produk-stok').value = p.stok;
  document.getElementById('produk-satuan').value = p.satuan;
  document.getElementById('produk-kategori').value = p.kategori_id || '';
  document.getElementById('produk-modal-title').textContent = 'Edit Produk';
  document.getElementById('modal-produk').classList.add('open');
}

async function simpanProduk() {
  const body = {
    id: document.getElementById('produk-id').value,
    nama: document.getElementById('produk-nama').value,
    kategori_id: document.getElementById('produk-kategori').value || null,
    harga: document.getElementById('produk-harga').value,
    stok: document.getElementById('produk-stok').value,
    satuan: document.getElementById('produk-satuan').value
  };
  if (!body.nama || !body.harga) { alert('Nama dan Harga wajib diisi!'); return; }
  const res = await fetch(API + 'simpan_produk', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) });
  const j = await res.json();
  if (j.success) { closeModal('modal-produk'); loadProduk(); }
  else alert('❌ ' + j.message);
}

async function hapusProduk(id, nama) {
  if (!confirm(`Hapus produk "${nama}"?`)) return;
  await fetch(API + 'hapus_produk', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id}) });
  loadProduk();
}

// ======================== LAPORAN ========================
async function loadLaporan() {
  const bulan = document.getElementById('lap-bulan').value;
  const tahun = document.getElementById('lap-tahun').value;
  const res = await fetch(`${API}laporan&bulan=${bulan}&tahun=${tahun}`);
  const j = await res.json();
  const tbody = document.getElementById('tbody-laporan');
  if (!j.data.length) { tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:var(--muted)">Tidak ada data untuk periode ini</td></tr>'; return; }
  let totalIn = 0, totalOut = 0;
  let rows = j.data.map(r => {
    if (r.jenis_transaksi==='pemasukan') totalIn += +r.total;
    else totalOut += +r.total;
    return `<tr>
      <td>${r.tgl}</td>
      <td><span class="badge badge-${r.jenis_transaksi==='pemasukan'?'green':'red'}">${r.jenis_transaksi}</span></td>
      <td>${r.jml} transaksi</td>
      <td><strong>${formatRp(r.total)}</strong></td>
    </tr>`;
  }).join('');
  rows += `<tr style="background:var(--bg-warm);font-weight:700">
    <td colspan="2">TOTAL</td>
    <td><span style="color:var(--success)">+${formatRp(totalIn)}</span> / <span style="color:var(--danger)">-${formatRp(totalOut)}</span></td>
    <td style="color:${totalIn-totalOut>=0?'var(--success)':'var(--danger)'}">Saldo: ${formatRp(totalIn-totalOut)}</td>
  </tr>`;
  tbody.innerHTML = rows;
}

// ======================== UTILS ========================
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function formatRp(n) { return 'Rp ' + parseInt(n||0).toLocaleString('id-ID'); }

// Close modal on backdrop click
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// Init
loadDashboard();
loadProduk(true);
</script>
</body>
</html>
