<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3" style="box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('home') }}" style="color: #0f766e; font-size: 1.25rem; letter-spacing: -0.025em;">
            <i class="fas fa-heartbeat me-2 text-teal-600" style="color: #0d9488;"></i>
            <span>Sistem Pakar <span style="font-weight: 800; color: #111827;">Ibu Hamil</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link px-3 fw-medium {{ Route::is('home') ? 'active text-teal-600' : 'text-gray-600' }}" href="{{ route('home') }}" style="color: {{ Route::is('home') ? '#0d9488' : '#4b5563' }};">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-medium {{ Route::is('konsultasi') ? 'active text-teal-600' : 'text-gray-600' }}" href="{{ route('konsultasi') }}" style="color: {{ Route::is('konsultasi') ? '#0d9488' : '#4b5563' }};">Konsultasi</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm me-3 border-0 fw-medium text-gray-700">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                    <span class="text-gray-600 fw-medium me-3 text-sm d-none d-lg-inline">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-teal btn-sm text-white px-3 fw-semibold" style="background-color: #0f766e; border: none; border-radius: 8px; transition: all 0.2s;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn px-4 py-2 fw-semibold text-white btn-sm shadow-sm" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none; border-radius: 8px; transition: all 0.2s;">
                        <i class="fas fa-sign-in-alt me-1"></i> Log in
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
