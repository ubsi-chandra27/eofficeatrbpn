<section class="quick-info-nav">
    <div>
        <span class="section-label">INFORMASI LAINNYA</span>
        <h3>Jelajahi informasi resmi</h3>
        <p>Pilih halaman informasi lain yang ingin dibuka.</p>
    </div>
    <nav aria-label="Navigasi informasi umum">
        <a href="{{ route('umum.profil-instansi') }}" class="{{ request()->routeIs('umum.profil-instansi') ? 'active' : '' }}">Profil Instansi</a>
        <a href="{{ route('umum.menteri') }}" class="{{ request()->routeIs('umum.menteri') ? 'active' : '' }}">Menteri</a>
        <a href="{{ route('umum.wakil') }}" class="{{ request()->routeIs('umum.wakil') ? 'active' : '' }}">Wakil Menteri</a>
        <a href="{{ route('umum.struktur') }}" class="{{ request()->routeIs('umum.struktur') ? 'active' : '' }}">Struktur Organisasi</a>
        <a href="{{ route('umum.visi') }}" class="{{ request()->routeIs('umum.visi') ? 'active' : '' }}">Visi</a>
        <a href="{{ route('umum.misi') }}" class="{{ request()->routeIs('umum.misi') ? 'active' : '' }}">Misi</a>
        <a href="{{ route('umum.makna-logo') }}" class="{{ request()->routeIs('umum.makna-logo') ? 'active' : '' }}">Makna Logo</a>
    </nav>
</section>
