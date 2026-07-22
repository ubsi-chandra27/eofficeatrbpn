<form method="POST" action="{{ route('profile.password.update') }}">

    @csrf
    @method('PUT')

    <div class="row">

        <div class="col-md-12 mb-3">

            <label class="form-label fw-bold">

                Password Lama

            </label>

            <input
                type="password"
                name="current_password"
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                required>

            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label fw-bold">

                Password Baru

            </label>

            <input
                type="password"
                name="password"
                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                required>

            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label fw-bold">

                Konfirmasi Password

            </label>

            <input
                type="password"
                name="password_confirmation"
                class="form-control"
                required>

        </div>

    </div>

    <button class="btn btn-warning">

        <i class="fas fa-key"></i>

        Ganti Password

    </button>

</form>
