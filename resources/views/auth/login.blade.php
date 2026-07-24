<x-guest-layout>
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h4"><b>SISTEM</b> PAKAR</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg small text-muted">
                DIAGNOSA PENYAKIT DAN KELAINAN PADA IBU HAMIL<br>
                MENGGUNAKAN METODE NAIVE BAYES CLASSIFIER
            </p>

            <x-auth-session-status class="mb-3" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group mb-3">
                    <input id="email" type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input id="password" type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember_me" name="remember">
                            <label for="remember_me">Ingat saya</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </div>
            </form>

            <p class="mb-1 mt-3">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa password?</a>
                @endif
            </p>

            <div class="alert alert-info mt-3 small p-2 mb-0">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-info-circle mr-2 text-info"></i>
                    <strong class="text-dark">Bantuan Login Admin:</strong>
                </div>
                <div class="text-muted">
                    <span class="d-block"><strong>Email:</strong> <code>admin@admin.com</code></span>
                    <span class="d-block"><strong>Password:</strong> <code>password</code></span>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
