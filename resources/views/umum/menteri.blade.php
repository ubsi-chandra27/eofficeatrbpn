@extends('layouts.umum')
@section('title', 'Informasi Menteri')
@section('content')
<div class="public-info-detail public-detail-shell">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="profile-detail-card">
        <div class="profile-detail-photo">
            <img src="{{ asset('images/menteri.jpg') }}" alt="Foto Menteri">
        </div>
        <div class="profile-detail-copy">
            <span class="section-label">PIMPINAN ORGANISASI</span>
            <h1>Nusron Wahid, S.S., M.Si.</h1>
            <p>Nusron Wahid merupakan Menteri Agraria dan Tata Ruang/Kepala Badan Pertanahan Nasional yang memiliki latar belakang kuat di bidang politik, agama, dan organisasi kemasyarakatan.</p>
            <div class="info-pill-grid">
                <span><i class="bi bi-geo-alt"></i>Kudus, Jawa Tengah</span>
                <span><i class="bi bi-calendar-event"></i>12 Oktober 1973</span>
                <span><i class="bi bi-award"></i>Dilantik 21 Oktober 2024</span>
            </div>
            <div class="detail-note">
                <i class="bi bi-info-circle"></i>
                <span>Profil ini berisi ringkasan perjalanan pendidikan, organisasi, dan pengabdian publik Menteri ATR/Kepala BPN.</span>
            </div>
        </div>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel"><i class="bi bi-mortarboard"></i><h3>Pendidikan</h3><p>Menempuh pendidikan tinggi di Fakultas Ilmu Budaya Universitas Indonesia, kemudian meraih gelar Magister dari Fakultas Ekonomi dan Manajemen Institut Pertanian Bogor pada tahun 2011.</p></article>
        <article class="detail-panel"><i class="bi bi-briefcase"></i><h3>Karier publik</h3><p>Memulai karier politik pada tahun 2004 sebagai Anggota DPR RI dari Partai Golongan Karya dan berperan dalam pengawasan kebijakan perdagangan, industri, investasi, koperasi, UKM, BUMN, serta standardisasi nasional.</p></article>
        <article class="detail-panel"><i class="bi bi-people"></i><h3>Organisasi</h3><p>Memiliki pengalaman luas di organisasi keagamaan dan kemasyarakatan, termasuk sebagai Ketua Umum GP Ansor periode 2011&ndash;2016 dan Wakil Ketua Umum PBNU periode 2022&ndash;2024.</p></article>
    </section>

    <section class="biography-panel">
        <div class="biography-heading">
            <span class="section-label">PROFIL LENGKAP</span>
            <h2>Perjalanan Nusron Wahid</h2>
            <p>Dari Kota Santri Kudus hingga dipercaya memimpin Kementerian ATR/BPN.</p>
        </div>

        <div class="biography-body">
            <p>Nusron Wahid, S.S., M.Si. merupakan seorang tokoh yang memiliki latar belakang kuat di bidang politik, agama, dan organisasi kemasyarakatan. Ia memulai mimpi besarnya untuk membangun Indonesia dari Kabupaten Kudus, Jawa Tengah, daerah yang dikenal sebagai Kota Santri.</p>
            <p>Lahir pada tanggal 12 Oktober 1973, ia menempuh pendidikan dasar hingga menengah di Kabupaten Kudus dan melanjutkan pendidikan tinggi di Fakultas Ilmu Budaya, Universitas Indonesia. Pada masa perkuliahan, ia aktif dalam berbagai organisasi, termasuk Senat Mahasiswa, menjadi Ketua Majalah Kampus Suara Mahasiswa UI, serta turut mendirikan Forum Ilmiah Kajian Islam.</p>
            <p>Nusron kemudian melanjutkan pendidikan pascasarjana di Fakultas Ekonomi dan Manajemen, Institut Pertanian Bogor, dan memperoleh gelar Magister pada tahun 2011.</p>
            <p>Karier politiknya dimulai pada tahun 2004 ketika dipercaya rakyat menjadi Anggota DPR RI dari Partai Golongan Karya. Melalui Komisi VI, ia mengawasi kebijakan yang berkaitan dengan perdagangan, perindustrian, investasi, koperasi, UKM, BUMN, dan Standardisasi Nasional.</p>
            <p>Pada tahun 2014 hingga 2019, Nusron menjabat sebagai Kepala Badan Nasional Penempatan dan Perlindungan Tenaga Kerja Indonesia (BNP2TKI). Melalui lembaga tersebut, ia mengelola kebijakan penempatan dan perlindungan tenaga kerja Indonesia yang bekerja di luar negeri.</p>
            <p>Berkat upaya dan dedikasinya dalam memperjuangkan hak-hak rakyat, Nusron ditetapkan menjadi Ketua Pansus DPR RI terkait Penyelenggaraan Haji Tahun 2024. Ia memberikan rekomendasi revisi terhadap UU Nomor 8 Tahun 2019 tentang Penyelenggaraan Ibadah Haji dan Umrah serta UU Nomor 34 Tahun 2014 tentang Pengelolaan Keuangan Haji.</p>
            <p>Nusron juga memiliki pengalaman luas dalam organisasi keagamaan. Pada tahun 2011&ndash;2016, ia menjabat sebagai Ketua Umum Gerakan Pemuda Ansor, organisasi pemuda di bawah naungan Nahdlatul Ulama (NU). Kemudian pada tahun 2022&ndash;2024, ia menjabat sebagai Wakil Ketua Umum Pengurus Besar Nahdlatul Ulama (PBNU).</p>
            <p>Kini, Nusron Wahid dipercaya oleh Presiden Prabowo Subianto menjadi Menteri Agraria dan Tata Ruang/Kepala Badan Pertanahan Nasional setelah dilantik pada 21 Oktober 2024.</p>
        </div>
    </section>

    <section class="timeline-panel">
        <article><b>1973</b><span>Lahir di Kudus, Jawa Tengah.</span></article>
        <article><b>2004</b><span>Menjadi Anggota DPR RI dari Partai Golongan Karya.</span></article>
        <article><b>2011</b><span>Meraih gelar Magister dari Institut Pertanian Bogor.</span></article>
        <article><b>2014&ndash;2019</b><span>Menjabat Kepala BNP2TKI.</span></article>
        <article><b>2024</b><span>Dilantik sebagai Menteri ATR/Kepala BPN.</span></article>
    </section>

    @include('umum.partials.info-navigation')
</div>
@endsection
