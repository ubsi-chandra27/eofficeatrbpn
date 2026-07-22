<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>

        @yield('title','Dashboard')

        | {{ \App\Models\Setting::getValue('app_name','E-Office') }}

    </title>

    {{-- ========================================================= --}}
    {{-- Bootstrap --}}
    {{-- ========================================================= --}}


    {{-- ========================================================= --}}
    {{-- Bootstrap Icons --}}
    {{-- ========================================================= --}}


    {{-- ========================================================= --}}
    {{-- Google Font --}}
    {{-- ========================================================= --}}


    {{-- ========================================================= --}}
    {{-- Laravel Assets --}}
    {{-- ========================================================= --}}

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- ========================================================= --}}
    {{-- Dashboard CSS --}}
    {{-- ========================================================= --}}

    <link rel="stylesheet"
          href="{{ asset('css/dashboard-pegawai.css') }}">


    <style>
        .choices { margin-bottom: 0; }
        .choices__inner {
            min-height: 48px;
            padding: 7px 12px;
            border: 1px solid #dbe2ea;
            border-radius: 12px;
            background: #fff;
        }
        .is-focused .choices__inner,
        .is-open .choices__inner {
            border-color: #0F4C81;
            box-shadow: 0 0 0 .2rem rgba(15, 76, 129, .15);
        }
        .choices__list--dropdown,
        .choices__list[aria-expanded] { z-index: 1060; }
        .choices__list--dropdown .choices__list,
        .choices__list[aria-expanded] .choices__list {
            max-height: 260px;
            overflow-y: auto;
        }
        .choices__input { border-radius: 8px; }

        .choices {
            width: 100%;
            font-size: 1rem;
        }
        .choices__inner {
            width: 100%;
            min-height: 48px !important;
            height: auto !important;
            padding: 7px 12px !important;
            overflow: hidden;
        }
        .choices[data-type*="select-one"] .choices__inner {
            padding-bottom: 7px !important;
        }
        .choices__list--single {
            padding: 5px 28px 5px 2px !important;
            line-height: 1.35;
        }
        .choices__list--multiple {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            padding: 0 !important;
        }
        .choices__list--multiple .choices__item {
            display: inline-flex !important;
            align-items: center;
            max-width: 100%;
            min-height: 32px !important;
            height: auto !important;
            margin: 0 !important;
            padding: 5px 10px !important;
            border-radius: 8px !important;
            font-size: .85rem !important;
            line-height: 1.25 !important;
            white-space: normal;
            word-break: break-word;
        }
        .choices__list--multiple .choices__button {
            width: 20px !important;
            min-width: 20px !important;
            height: 20px !important;
            margin: 0 0 0 6px !important;
            padding: 0 !important;
            border-left: 0 !important;
            background-size: 8px !important;
        }
        .choices__input {
            min-width: 120px;
            min-height: 34px !important;
            height: 34px !important;
            margin: 0 !important;
            padding: 5px 8px !important;
            background: #f8fafc !important;
        }
        .choices__list--dropdown,
        .choices__list[aria-expanded] {
            border-radius: 10px;
            overflow: hidden;
        }
        .choices__list--dropdown .choices__item,
        .choices__list[aria-expanded] .choices__item {
            min-height: auto !important;
            padding: 10px 14px !important;
            line-height: 1.35 !important;
        }
        .employee-avatar-image { width:100%; height:100%; object-fit:cover; border-radius:50%; }
    </style>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/pegawai-refinement.css') }}">

</head>

<body>

<div class="pegawai-layout">

    {{-- ========================================================= --}}
    {{-- SIDEBAR --}}
    {{-- ========================================================= --}}

    <aside
        class="sidebar"
        id="sidebar">

        {{-- ===================================================== --}}
        {{-- LOGO --}}
        {{-- ===================================================== --}}

        <div class="sidebar-logo">

            <div class="logo-icon">

                <i class="bi bi-buildings-fill"></i>

            </div>

            <div class="logo-text">

                <h3>

                    {{ \App\Models\Setting::getValue('app_name','E-Office') }}

                </h3>

                <small>

                    {{ \App\Models\Setting::getValue('app_subtitle','Administrasi Digital') }}

                </small>

            </div>

        </div>

        {{-- ===================================================== --}}
        {{-- USER --}}
        {{-- ===================================================== --}}

        <div class="sidebar-user">

            <div class="avatar">@if(auth()->user()->profile_photo_path)<img src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="Foto profil" class="employee-avatar-image">@else{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}@endif</div>

            <div>

                <h6 class="mb-0">

                    {{ auth()->user()->name }}

                </h6>

                <small>

                    Pegawai

                </small>

            </div>

        </div>

        {{-- ===================================================== --}}
        {{-- MENU --}}
        {{-- ===================================================== --}}

        <nav class="sidebar-menu">

            <span class="menu-title">

                MENU UTAMA

            </span>

            <a
                href="{{ route('pegawai.dashboard') }}"
                class="{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">

                <i class="bi bi-speedometer2"></i>

                <span>

                    Dashboard

                </span>

            </a>


            

           {{-- ======================================================
     PERSURATAN
====================================================== --}}

<span class="menu-title">
    PERSURATAN
</span>


{{-- SURAT MASUK --}}
<a href="{{ route('pegawai.surat-masuk.index') }}"
   class="{{ request()->routeIs('pegawai.surat-masuk.*') ? 'active' : '' }}">

    <i class="bi bi-envelope-arrow-down-fill"></i>

    <span>
        Surat Masuk
    </span>

</a>



{{-- SURAT KELUAR --}}
<a href="{{ route('pegawai.surat-keluar.index') }}"
   class="{{ request()->routeIs('pegawai.surat-keluar.*') ? 'active' : '' }}">

    <i class="bi bi-envelope-paper-fill"></i>

    <span>
        Surat Keluar
    </span>

</a>



{{-- DISPOSISI --}}
<a href="{{ route('pegawai.disposisi.index') }}"
   class="{{ request()->routeIs('pegawai.disposisi.*') ? 'active' : '' }}">

    <i class="bi bi-send-check-fill"></i>

    <span>
        Disposisi
    </span>

</a>
            
           

            {{-- ===================================================== --}}
            {{-- AKUN --}}
            {{-- ===================================================== --}}

            <span class="menu-title">

                AKUN

            </span>

            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">

                <i class="bi bi-person-circle"></i>

                <span>

                    Profil Saya

                </span>

            </a>

            <div class="sidebar-footer">

                <form
                    action="{{ route('logout') }}"
                    method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="btn-logout">

                        <i class="bi bi-box-arrow-right"></i>

                        <span>

                            Logout

                        </span>

                    </button>

                </form>

            </div>

        </nav>

    </aside>

    {{-- ========================================================= --}}
    {{-- MAIN WRAPPER --}}
    {{-- ========================================================= --}}

    <div class="main-wrapper">
                {{-- ========================================================= --}}
        {{-- TOPBAR --}}
        {{-- ========================================================= --}}

        <header class="topbar">

            {{-- ========================= --}}
            {{-- LEFT --}}
            {{-- ========================= --}}

            <div class="topbar-left">

                <button
                    class="toggle-sidebar"
                    id="toggleSidebar"
                    type="button">

                    <i class="bi bi-list"></i>

                </button>

                <div class="page-title">

                    <h4>

                        @yield('title','Dashboard')

                    </h4>

                    <small>

                        Selamat datang kembali,

                        <strong>

                            {{ auth()->user()->name }}

                        </strong>

                    </small>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- RIGHT --}}
            {{-- ========================= --}}

            <div class="topbar-right">

                {{-- Tanggal --}}

                <div class="topbar-date">

                    <i class="bi bi-calendar-event"></i>

                    <span>

                        {{ now()->translatedFormat('l, d F Y') }}

                    </span>

                </div>

                {{-- Notifikasi --}}

                <button
                    class="topbar-icon"
                    type="button">

                    <i class="bi bi-bell-fill"></i>

                </button>

                {{-- User Dropdown --}}

                <div class="dropdown">

                    <button
                        class="topbar-user border-0 bg-transparent"
                        data-bs-toggle="dropdown"
                        type="button">

                        <div class="avatar">@if(auth()->user()->profile_photo_path)<img src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="Foto profil" class="employee-avatar-image">@else{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}@endif</div>

                        <div class="text-start">

                            <strong>

                                {{ auth()->user()->name }}

                            </strong>

                            <small>

                                Pegawai

                            </small>

                        </div>

                        <i class="bi bi-chevron-down ms-2"></i>

                    </button>

                    <ul class="dropdown-menu dropdown-menu-end user-dropdown">

                        <li class="user-dropdown-header">

                            <div class="user-dropdown-avatar">@if(auth()->user()->profile_photo_path)<img src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="Foto profil" class="employee-avatar-image">@else{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}@endif</div>

                            <h6>

                                {{ auth()->user()->name }}

                            </h6>

                            <small>

                                {{ auth()->user()->email }}

                            </small>

                        </li>

                        <li>

                            <a
                                class="dropdown-item"
                                href="{{ route('profile.edit') }}">

                                <i class="bi bi-person-circle"></i>

                                Profil Saya

                            </a>

                        </li>

                        <li>

                            <hr class="dropdown-divider">

                        </li>

                        <li>

                            <form
                                action="{{ route('logout') }}"
                                method="POST">

                                @csrf

                                <button
                                    class="dropdown-item"
                                    type="submit">

                                    <i class="bi bi-box-arrow-right"></i>

                                    Logout

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            </div>

        </header>

        {{-- ========================================================= --}}
        {{-- CONTENT --}}
        {{-- ========================================================= --}}

        <main class="content">

            <div class="content-wrapper">
                                {{-- ========================================================= --}}
                {{-- BREADCRUMB --}}
                {{-- ========================================================= --}}

                @hasSection('breadcrumb')

                    <nav
                        aria-label="breadcrumb"
                        class="mb-4">

                        <ol class="breadcrumb">

                            @yield('breadcrumb')

                        </ol>

                    </nav>

                @endif

                {{-- ========================================================= --}}
                {{-- SUCCESS --}}
                {{-- ========================================================= --}}

                @if(session('success'))

                    <div
                        class="alert alert-success alert-dismissible fade show">

                        <i class="bi bi-check-circle-fill"></i>

                        <div class="flex-grow-1">

                            {{ session('success') }}

                        </div>

                        <button
                            class="btn-close"
                            data-bs-dismiss="alert"
                            type="button">

                        </button>

                    </div>

                @endif

                {{-- ========================================================= --}}
                {{-- ERROR --}}
                {{-- ========================================================= --}}

                @if(session('error'))

                    <div
                        class="alert alert-danger alert-dismissible fade show">

                        <i class="bi bi-x-circle-fill"></i>

                        <div class="flex-grow-1">

                            {{ session('error') }}

                        </div>

                        <button
                            class="btn-close"
                            data-bs-dismiss="alert"
                            type="button">

                        </button>

                    </div>

                @endif

                {{-- ========================================================= --}}
                {{-- WARNING --}}
                {{-- ========================================================= --}}

                @if(session('warning'))

                    <div
                        class="alert alert-warning alert-dismissible fade show">

                        <i class="bi bi-exclamation-triangle-fill"></i>

                        <div class="flex-grow-1">

                            {{ session('warning') }}

                        </div>

                        <button
                            class="btn-close"
                            data-bs-dismiss="alert"
                            type="button">

                        </button>

                    </div>

                @endif

                {{-- ========================================================= --}}
                {{-- INFO --}}
                {{-- ========================================================= --}}

                @if(session('info'))

                    <div
                        class="alert alert-info alert-dismissible fade show">

                        <i class="bi bi-info-circle-fill"></i>

                        <div class="flex-grow-1">

                            {{ session('info') }}

                        </div>

                        <button
                            class="btn-close"
                            data-bs-dismiss="alert"
                            type="button">

                        </button>

                    </div>

                @endif

                {{-- ========================================================= --}}
                {{-- VALIDATION ERROR --}}
                {{-- ========================================================= --}}

                @if($errors->any())

                    <div class="alert alert-warning">

                        <i class="bi bi-exclamation-circle-fill"></i>

                        <div class="flex-grow-1">

                            <strong>

                                Terdapat kesalahan input.

                            </strong>

                            <ul class="mt-2 mb-0">

                                @foreach($errors->all() as $error)

                                    <li>

                                        {{ $error }}

                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    </div>

                @endif

                {{-- ========================================================= --}}
                {{-- PAGE CONTENT --}}
                {{-- ========================================================= --}}

                <div class="fade-up">

                    @yield('content')

                </div>

            </div>

        </main>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select:not([data-native-select])').forEach((select) => {
        if (select.dataset.choicesEnhanced === 'true') return;
        select.dataset.choicesEnhanced = 'true';

        new Choices(select, {
            searchEnabled: true,
            searchPlaceholderValue: 'Ketik untuk mencari...',
            noResultsText: 'Data tidak ditemukan',
            noChoicesText: 'Tidak ada pilihan',
            itemSelectText: 'Pilih',
            shouldSort: false,
            searchResultLimit: 100,
            renderChoiceLimit: -1,
            removeItemButton: select.multiple,
            allowHTML: false,
        });
    });
});
</script>

@stack('scripts')

</body>
</html>
