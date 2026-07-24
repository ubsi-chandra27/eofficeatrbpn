@extends('layouts.umum')
@section('title', 'Informasi Wakil Menteri')
@section('content')
<div class="public-info-detail public-detail-shell">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="profile-detail-card">
        <div class="profile-detail-photo">
            <img src="{{ asset('images/wakil-menteri.jpg') }}" alt="Foto Wakil Menteri">
        </div>
        <div class="profile-detail-copy">
            <span class="section-label">WAKIL PIMPINAN ORGANISASI</span>
            <h1>H. Ossy Dermawan, B.S., M.Sc.</h1>
            <p>H. Ossy Dermawan, B.S., M.Sc. adalah Wakil Menteri Agraria dan Tata Ruang/Wakil Kepala Badan Pertanahan Nasional, politisi muda berlatar belakang purnawirawan Tentara Nasional Indonesia Angkatan Darat (TNI AD).</p>
            <div class="info-pill-grid">
                <span><i class="bi bi-geo-alt"></i>Jakarta</span>
                <span><i class="bi bi-calendar-event"></i>9 November 1976</span>
                <span><i class="bi bi-award"></i>Dilantik 21 Oktober 2024</span>
            </div>
            <div class="detail-note">
                <i class="bi bi-info-circle"></i>
                <span>Profil ini merangkum perjalanan pendidikan, pengabdian militer, kiprah sosial, politik, dan pelayanan publik Wakil Menteri ATR/Wakil Kepala BPN.</span>
            </div>
        </div>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel"><i class="bi bi-mortarboard"></i><h3>Pendidikan</h3><p>Menempuh pendidikan di Norwich University, Military School of Vermont, Amerika Serikat, serta meraih gelar Master of Science bidang Strategic Studies dari RSIS, Nanyang Technological University, Singapura pada tahun 2014.</p></article>
        <article class="detail-panel"><i class="bi bi-shield-check"></i><h3>Pengabdian militer</h3><p>Mengabdi selama 17 tahun di TNI AD dan terlibat dalam operasi di dalam maupun luar negeri, termasuk Operasi Pemulihan Keamanan di Ambon dan Operasi Perdamaian PBB di Lebanon.</p></article>
        <article class="detail-panel"><i class="bi bi-people"></i><h3>Sosial dan politik</h3><p>Berpengalaman dalam komunikasi strategis Partai Demokrat, mengelola Klub Bola Voli LavAni serta Museum dan Galeri Seni SBY-Ani, dan pernah menjadi Staf Pribadi Presiden RI ke-6.</p></article>
    </section>

    <section class="biography-panel">
        <div class="biography-heading">
            <span class="section-label">PROFIL LENGKAP</span>
            <h2>Perjalanan Ossy Dermawan</h2>
            <p>Dari pengabdian militer hingga dipercaya membantu memimpin Kementerian ATR/BPN.</p>
        </div>

        <div class="biography-body">
            <p>H. Ossy Dermawan, B.S., M.Sc. adalah politisi muda berlatar belakang purnawirawan Tentara Nasional Indonesia Angkatan Darat (TNI AD). Sejak dini, Ossy telah menunjukkan bakat kepemimpinan.</p>
            <p>Lahir di Jakarta, 9 November 1976, ia menempuh pendidikan dasar dan menengah di Jakarta. Saat menempuh pendidikan di SMAN 8 Jakarta, Ossy berkesempatan mengikuti program pertukaran pelajar American Field Service (AFS) dan bersekolah di Templestowe College, Melbourne, Australia selama satu tahun.</p>
            <p>Sebelum menjalani pengabdian di dunia militer, pada tahun 1997, Ossy mendapatkan beasiswa tugas belajar di Norwich University, Military School of Vermont di Amerika Serikat. Program ini diinisiasi oleh Presiden RI, Prabowo Subianto, yang saat itu menjabat sebagai Danjen Kopassus.</p>
            <p>Ossy kemudian meraih gelar Master of Science di bidang Strategic Studies dari S. Rajaratnam School of International Studies (RSIS), Nanyang Technological University (NTU), Singapura pada tahun 2014.</p>
            <p>Selaras dengan pendidikan formalnya, Ossy juga terus menempuh berbagai pelatihan militer yang mendukung kariernya di TNI AD. Ia juga terlibat dalam operasi militer di dalam dan luar negeri, mulai dari Operasi Pemulihan Keamanan di Ambon hingga Operasi Perdamaian PBB di Lebanon.</p>
            <p>Namun, setelah 17 tahun mengabdi di TNI AD, Ossy memutuskan untuk pensiun dini. Ia memilih jalur baru di bidang politik dan pelayanan sipil. Prinsipnya jelas: pengabdian bagi nusa dan bangsa tidak mengenal batas medan.</p>
            <p>Pada tahun 2014, Ossy mendapat kehormatan sebagai Staf Pribadi Presiden RI ke-6, Susilo Bambang Yudhoyono. Pengalamannya bekerja dekat dengan SBY memberikan kesempatan bagi Ossy untuk menyerap pemikiran-pemikiran strategis, khususnya terkait keadilan dan akses tanah yang lebih merata bagi rakyat.</p>
            <p>Di bidang sosial kemasyarakatan, Ossy adalah Manajer Klub Bola Voli LavAni, salah satu klub bola voli papan atas di Indonesia. Sejumlah pemain profesional tingkat nasional telah lahir dari klub ini. SBY juga mengamanahkan Ossy untuk membangun dan mengelola Museum dan Galeri Seni SBY-Ani di Pacitan, Jawa Timur.</p>
            <p>Sementara itu, di bidang politik, Ossy mengemban amanah sebagai Wakil Sekretaris Jenderal DPP Partai Demokrat. Sebelumnya, Ossy menjadi motor bidang komunikasi strategis Partai Demokrat dengan berkiprah sebagai Kepala Badan Komunikasi Strategis DPP Partai Demokrat.</p>
            <p>Berkat dedikasi dan kiprahnya, Ossy Dermawan dipercaya oleh Presiden Republik Indonesia, Prabowo Subianto, untuk membantu Menteri Agraria dan Tata Ruang (ATR)/Kepala Badan Pertanahan Nasional (BPN), Nusron Wahid, dalam melaksanakan tugas-tugas strategis. Resmi dilantik pada 21 Oktober 2024, Ossy kini memiliki ruang untuk mengaplikasikan ilmu, gagasan, serta inovasi-inovasinya di sektor agraria dan tata ruang.</p>
        </div>
    </section>

    <section class="timeline-panel">
        <article><b>1976</b><span>Lahir di Jakarta pada 9 November.</span></article>
        <article><b>1997</b><span>Menerima beasiswa tugas belajar di Norwich University, Amerika Serikat.</span></article>
        <article><b>2014</b><span>Meraih gelar Master of Science dari NTU Singapura dan menjadi Staf Pribadi Presiden RI ke-6.</span></article>
        <article><b>17 Tahun</b><span>Mengabdi di TNI AD sebelum memilih jalur politik dan pelayanan sipil.</span></article>
        <article><b>2024</b><span>Dilantik sebagai Wakil Menteri ATR/Wakil Kepala BPN.</span></article>
    </section>

    @include('umum.partials.info-navigation')
</div>
@endsection
