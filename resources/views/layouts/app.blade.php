<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Facilon')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --primary: #3e6f7c;
      --primary-dark: #2f5560;
      --primary-light: #e8f1f3;
      --primary-mid: #5a8f9e;
      --danger: #c0392b;
      --warn-bg: #fef6e8;
      --warn-border: #f5d08a;
      --success: #27ae60;
      --gray50: #f7f9fa;
      --gray100: #eef1f3;
      --gray200: #dde2e6;
      --gray300: #c4cdd5;
      --gray500: #7a8a96;
      --gray600: #5c6b78;
      --gray700: #3d4f5e;
      --gray900: #1a2633;
      --card-radius: 7px;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
      --shadow: 0 2px 8px rgba(0, 0, 0, .08);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      font-size: 15px;
      background: #eef1f4;
      color: var(--gray900);
      -webkit-font-smoothing: antialiased;
    }

    /* BRAND TOPBAR */
    .app-topbar {
      height: 56px;
      background: #fff;
      border-bottom: 1px solid var(--gray200);
      box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
      display: flex;
      align-items: center;
      padding: 0 18px;
      gap: 12px;
      position: sticky;
      top: 0;
      z-index: 20;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 11px;
    }

    .brand-logo {
      height: 34px;
      width: auto;
      object-fit: contain;
      display: block;
    }

    .brand-divider {
      width: 1px;
      height: 26px;
      background: var(--gray200);
      margin: 0 4px;
    }

    .brand-tag {
      font-size: 12.5px;
      font-weight: 600;
      color: var(--primary);
      letter-spacing: .2px;
    }

    .topbar-right {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .topbar-bell {
      color: var(--gray500);
      cursor: pointer;
      display: flex;
    }

    .avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      letter-spacing: .3px;
    }

    /* PAGE HERO HEADER */
    .page-head {
      background: linear-gradient(110deg, var(--primary-dark) 0%, var(--primary) 100%);
      color: #fff;
      padding: 20px 18px;
    }

    .page-head-inner {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .page-head-icon {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: rgba(255, 255, 255, .15);
      border: 1px solid rgba(255, 255, 255, .25);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .page-head h1 {
      font-size: 22px;
      font-weight: 700;
      letter-spacing: -.2px;
      color: #fff;
    }

    .page-head p {
      font-size: 14px;
      color: rgba(255, 255, 255, .85);
      margin-top: 3px;
      max-width: 760px;
    }

    .page-wrap {
      padding: 16px 18px 40px;
    }

    .flash-success {
      background: #d1e7dd;
      color: #0f5132;
      border: 1px solid #badbcc;
      border-radius: 6px;
      padding: 10px 14px;
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 16px;
    }

    /* BUTTONS */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      padding: 0 12px;
      height: 30px;
      font-size: 12.5px;
      font-weight: 500;
      border-radius: 4px;
      border: 1px solid transparent;
      cursor: pointer;
      transition: filter .12s;
      white-space: nowrap;
      font-family: inherit;
      letter-spacing: .1px;
    }
    .btn:hover { filter: brightness(.93); }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-outline { background: #fff; border-color: var(--primary); color: var(--primary); }
    .btn-ghost { background: var(--gray100); color: var(--gray700); border-color: var(--gray200); }
    .btn-full { width: 100%; }
    .btn-sm { height: 28px; font-size: 12px; padding: 0 10px; }
  </style>
  @stack('styles')
</head>
<body>
  <header class="app-topbar">
    <div class="brand">
      <img src="{{ asset('logo.png') }}" alt="Facilon" class="brand-logo">
      <div class="brand-divider"></div>
      <span class="brand-tag">Investor Console</span>
    </div>
  </header>

  <div class="page-head">
    <div class="page-head-inner">
      <div class="page-head-icon">
        @yield('page_icon')
      </div>
      <div>
        <h1>@yield('page_title', 'Facilon')</h1>
        <p>@yield('page_desc')</p>
      </div>
    </div>
  </div>

  <div class="page-wrap">
    @yield('content')
  </div>
  @stack('scripts')

  {{-- Success / error feedback via SweetAlert2 --}}
  @if (session('status'))
    <script>
      document.addEventListener('DOMContentLoaded', () => Swal.fire({
        icon: 'success', title: 'Saved', text: @json(session('status')),
        timer: 2200, showConfirmButton: false, timerProgressBar: true,
      }));
    </script>
  @endif
  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', () => Swal.fire({
        icon: 'error', title: 'Please fix the highlighted fields',
        text: @json($errors->first()), confirmButtonColor: '#3e6f7c',
      }));
    </script>
  @endif
</body>
</html>
