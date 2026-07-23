<nav class="pegawai-quick-nav" aria-label="Navigasi Pegawai">
    <a href="{{ route('pegawai.dashboard') }}" class="{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('pegawai.surat-masuk.index') }}" class="{{ request()->routeIs('pegawai.surat-masuk.*') ? 'active' : '' }}">
        <i class="bi bi-inbox"></i><span>Surat Masuk</span>
    </a>
    <a href="{{ route('pegawai.surat-keluar.index') }}" class="{{ request()->routeIs('pegawai.surat-keluar.*') ? 'active' : '' }}">
        <i class="bi bi-send"></i><span>Surat Keluar</span>
    </a>
    <a href="{{ route('pegawai.disposisi.index') }}" class="{{ request()->routeIs('pegawai.disposisi.*') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i><span>Disposisi</span>
    </a>
    <a href="{{ route('pegawai.profile.index') }}" class="{{ request()->routeIs('pegawai.profile.*', 'profile.*') ? 'active' : '' }}">
        <i class="bi bi-person-circle"></i><span>Profil</span>
    </a>
</nav>
