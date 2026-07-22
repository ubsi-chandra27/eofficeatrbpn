{{-- resources/views/partials/admin-sidebar.blade.php --}}

<aside class="sidebar">

    {{-- =========================
        LOGO
    ========================== --}}
    <div class="sidebar-header">

        <img src="{{ asset('images/logo-atr.png') }}"
             alt="Logo E-Office">

        <div>

            <h5>E-OFFICE</h5>

            <small>Administrasi Digital</small>

        </div>

    </div>


    {{-- =========================
        MENU
    ========================== --}}

    <div class="sidebar-menu">

        {{-- Dashboard --}}
        <div class="menu-title">
            MENU UTAMA
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>



        {{-- =========================
            PERSURATAN
        ========================== --}}

        <div class="menu-title">
            PERSURATAN
        </div>

        <a href="javascript:void(0)"
           onclick="toggleMenu('persuratanMenu')">

            <i class="bi bi-envelope-paper-fill"></i>

            Persuratan

            <i class="bi bi-chevron-down ms-auto"></i>

        </a>

        <div id="persuratanMenu"
             class="sidebar-sub
             {{ request()->routeIs('admin.surat.*') || request()->routeIs('admin.disposisi.*') ? 'show' : '' }}">

            <a href="{{ route('admin.surat.masuk.index') }}"
               class="{{ request()->routeIs('admin.surat.masuk.*') ? 'active' : '' }}">

                <i class="bi bi-inbox-fill"></i>

                Surat Masuk

            </a>

            <a href="{{ route('admin.surat.keluar.index') }}"
               class="{{ request()->routeIs('admin.surat.keluar.*') ? 'active' : '' }}">

                <i class="bi bi-send-fill"></i>

                Surat Keluar

            </a>

            <a href="{{ route('admin.disposisi.index') }}"
               class="{{ request()->routeIs('admin.disposisi.*') ? 'active' : '' }}">

                <i class="bi bi-share-fill"></i>

                Disposisi

            </a>

        </div>



        {{-- =========================
            MASTER DATA
        ========================== --}}

        <div class="menu-title">
            MASTER DATA
        </div>

        <a href="javascript:void(0)"
           onclick="toggleMenu('masterMenu')">

            <i class="bi bi-database-fill"></i>

            Master Data

            <i class="bi bi-chevron-down ms-auto"></i>

        </a>

        <div id="masterMenu"
             class="sidebar-sub
             {{ request()->routeIs('admin.pegawai.*') ||
                request()->routeIs('admin.jabatan.*') ||
                request()->routeIs('admin.unitkerja.*')
                ? 'show' : '' }}">

            <a href="{{ route('admin.pegawai.index') }}"
               class="{{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">

                <i class="bi bi-people-fill"></i>

                Pegawai

            </a>

            <a href="{{ route('admin.jabatan.index') }}"
               class="{{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}">

                <i class="bi bi-award-fill"></i>

                Jabatan

            </a>

            <a href="{{ route('admin.unitkerja.index') }}"
               class="{{ request()->routeIs('admin.unitkerja.*') ? 'active' : '' }}">

                <i class="bi bi-diagram-3-fill"></i>

                Unit Kerja

            </a>

        </div>



        {{-- =========================
            LAPORAN
        ========================== --}}

        <div class="menu-title">
            LAPORAN
        </div>

        <a href="{{ route('admin.laporan.index') }}"
           class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">

            <i class="bi bi-file-earmark-bar-graph-fill"></i>

            Laporan

        </a>



        {{-- =========================
            PENGATURAN
        ========================== --}}

        <div class="menu-title">
            SISTEM
        </div>

        <a href="{{ route('admin.settings.index') }}"
           class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">

            <i class="bi bi-gear-fill"></i>

            Pengaturan

        </a>



        {{-- =========================
            LOGOUT
        ========================== --}}

        <form action="{{ route('logout') }}"
              method="POST"
              class="mt-4">

            @csrf

            <button type="submit"
                    class="sidebar-logout">

                <i class="bi bi-box-arrow-right me-2"></i>

                Logout

            </button>

        </form>

    </div>

</aside>
