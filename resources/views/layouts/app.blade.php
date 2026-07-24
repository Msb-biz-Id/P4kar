<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/" class="brand-link text-decoration-none">
            <span class="brand-text font-weight-light">Sistem Pakar</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <!-- Data Master -->
                    <li class="nav-header">DATA MASTER</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.penyakit.index') }}" class="nav-link {{ request()->routeIs('admin.penyakit.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-virus"></i>
                            <p>Data Penyakit</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.gejala.index') }}" class="nav-link {{ request()->routeIs('admin.gejala.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-stethoscope"></i>
                            <p>Data Gejala</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.relasi.index') }}" class="nav-link {{ request()->routeIs('admin.relasi.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>Data Relasi</p>
                        </a>
                    </li>
                    <!-- Hasil -->
                    <li class="nav-header">LAPORAN</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.hasil.index') }}" class="nav-link {{ request()->routeIs('admin.hasil.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Hasil Diagnosa</p>
                        </a>
                    </li>
                    <!-- Logout -->
                    <li class="nav-header">AKUN</li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn text-left" style="background: none; border: none; width: 100%;">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @isset($header)
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-0">{{ $header }}</h1>
                    </div>
                </div>
            </div>
        </div>
        @endisset

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                {{ $slot }}
            </div>
        </section>
    </div>

</div>

<!-- REQUIRED SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
