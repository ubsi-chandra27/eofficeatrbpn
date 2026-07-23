{{-- resources/views/partials/admin-topbar.blade.php --}}

<header class="topbar">

    {{-- =========================
        LEFT
    ========================== --}}
    <div class="topbar-left">

        {{-- Toggle Sidebar --}}
        <button class="btn btn-light d-lg-none"
                id="sidebarToggle">

            <i class="bi bi-list fs-4"></i>

        </button>

        <div>

            <h4 class="mb-0 fw-bold">

                @yield('title','Dashboard')

            </h4>

            <small class="text-muted">

                Sistem Informasi Persuratan ATR/BPN

            </small>

        </div>

    </div>


    {{-- =========================
        RIGHT
    ========================== --}}

    <div class="topbar-right">

        {{-- Tanggal --}}
        <div class="d-none d-md-block text-end me-4">

            <div class="fw-semibold">

                <i class="bi bi-calendar-event text-primary"></i>

                {{ now()->translatedFormat('l') }}

            </div>

            <small class="text-muted">

                {{ now()->translatedFormat('d F Y') }}

            </small>

        </div>


        {{-- User Dropdown --}}
        <div class="dropdown">

            <a href="#"
               class="d-flex align-items-center text-decoration-none dropdown-toggle"
               data-bs-toggle="dropdown"
               aria-expanded="false">

                <span class="rounded-circle shadow d-inline-grid bg-primary text-white fw-bold"
                      style="width:45px;height:45px;place-items:center"
                      role="img" aria-label="Inisial {{ Auth::user()->name }}">
                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                </span>

                <div class="ms-3 d-none d-md-block">

                    <div class="fw-bold">

                        {{ Auth::user()->name }}

                    </div>

                    <small class="text-muted">

                        {{ ucfirst(Auth::user()->role) }}

                    </small>

                </div>

            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0">

                <li class="dropdown-header">

                    <strong>

                        {{ Auth::user()->name }}

                    </strong>

                    <br>

                    <small class="text-muted">

                        {{ Auth::user()->email }}

                    </small>

                </li>

                <li>

                    <hr class="dropdown-divider">

                </li>

                <li>

                    <a class="dropdown-item"
                       href="{{ route('profile.edit') }}">

                        <i class="bi bi-person-circle me-2"></i>

                        Profil

                    </a>

                </li>

                <li>

                    <a class="dropdown-item"
                       href="{{ route('admin.settings.index') }}">

                        <i class="bi bi-gear me-2"></i>

                        Pengaturan

                    </a>

                </li>

                <li>

                    <hr class="dropdown-divider">

                </li>

                <li>

                    <form action="{{ route('logout') }}"
                          method="POST">

                        @csrf

                        <button type="submit"
                                class="dropdown-item text-danger">

                            <i class="bi bi-box-arrow-right me-2"></i>

                            Logout

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</header>

{{-- Script Toggle Sidebar --}}
<script>

document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.querySelector(".sidebar");
    const toggle = document.getElementById("sidebarToggle");

    if (toggle) {

        toggle.addEventListener("click", function () {

            sidebar.classList.toggle("show");

        });

    }

});

</script>
