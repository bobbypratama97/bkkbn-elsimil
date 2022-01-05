<!DOCTYPE html>
<html lang="en">
    <!--begin::Head-->
    <head>
        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>ELSIMIL</title>
        <meta name="description" content="Updates and statistics" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <!--<link rel="canonical" href="https://keenthemes.com/metronic" />-->

        <!--begin::Global Theme Styles(used by all pages)-->
        <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
        <!--end::Global Theme Styles-->
        <script>var baseurl = "<?php echo env('APP_URL'); ?>"</script>
        <!--begin::Layout Themes(used by all pages)-->
        @stack('css')
        <!--end::Layout Themes-->
        <link rel="shortcut icon" href="{{ asset('assets/media/logos/icon-med.png') }}" />

        <style>.unclick { pointer-events: none; cursor: default; }</style>
    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body id="kt_body" class="header-fixed header-mobile-fixed sidebar-enabled page-loading">
        <!--begin::Main-->
        <!--begin::Header Mobile-->
        <div id="kt_header_mobile" class="header-mobile header-mobile-fixed">
            <!--begin::Logo-->
            <a href="#">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-letter-1.png') }}" class="logo-default max-h-30px" />
            </a>
            <!--end::Logo-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <button class="btn rounded-0 p-0 burger-icon burger-icon-left" id="kt_header_mobile_toggle">
                    <span></span>
                </button>
                <button class="btn btn-hover-icon-primary p-0 ml-2" id="kt_aside_mobile_toggle">
                    <span class="svg-icon svg-icon-xl">
                        <!--begin::Svg Icon | path:dist/assets/media/svg/icons/General/User.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24" />
                                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>
                </button>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header Mobile-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Page-->
            <div class="d-flex flex-row flex-column-fluid page">
                <!--begin::Aside-->
                <div class="aside aside-left d-flex flex-column" id="kt_aside">
                    <!--begin::Brand-->
                    <div class="aside-brand d-flex flex-column align-items-center flex-column-auto pt-5 pt-lg-18 pb-10">
                        <!--begin::Logo-->
                        <div class="btn p-0 symbol symbol-60 symbol-light-primary" id="kt_quick_user_toggle">
                            <div class="symbol-label mb-3">
                                <img alt="Logo" src="{{ asset('assets/media/svg/avatars/001-boy.svg') }}" class="h-75 align-self-end" />
                            </div>
                        </div>
                        <span class="text-center">{{ Auth::user()->name }}</span>
                        <!--end::Logo-->
                    </div>
                    <!--end::Brand-->
                    <!--begin::Nav Wrapper-->
                    <div class="aside-nav d-flex flex-column align-items-center flex-column-fluid pb-10">
                    </div>
                    <!--end::Nav Wrapper-->
                    <!--begin::Footer-->
                    <div class="aside-footer d-flex flex-column align-items-center flex-column-auto py-8">
                        <!--begin::Notifications-->
                        <div class="dropdown" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Ubah Password">
                            <a href="{{ route('admin.profile.index') }}" class="btn btn-icon btn-hover-text-primary btn-lg" id="kt_quick_actions_toggle" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="" data-original-title="Ubah Password">
                                <span class="svg-icon svg-icon-primary svg-icon-3x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <mask fill="white">
                                                <use xlink:href="#path-1"/>
                                            </mask>
                                            <g/>
                                            <path d="M15.6274517,4.55882251 L14.4693753,6.2959371 C13.9280401,5.51296885 13.0239252,5 12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L14,10 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C13.4280904,3 14.7163444,3.59871093 15.6274517,4.55882251 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                </span>
                            </a>
                        </div>
                        <span class="text-center">Ubah<br />Password</span>
                        <div class="dropdown mt-7" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Logout">
                            <a href="{{ route('logout') }}" class="btn btn-icon btn-hover-text-primary btn-lg" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                <span class="svg-icon svg-icon-primary svg-icon-3x">
                                    <!--begin::Svg Icon | path:dist/assets/media/svg/icons/General/Shield-check.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3" />
                                            <path d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z" fill="#000000" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            </a>
                        </div>
                        Logout
                        <!--end::Languages-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Aside-->
                <!--begin::Wrapper-->
                @if (Route::getCurrentRoute()->getActionMethod() == 'index')
                <div class="d-flex flex-column flex-row-fluid wrapper mr-10" id="kt_wrapper">
                @else
                <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                @endif
                    <!--begin::Header-->
                    <div id="kt_header" class="header header-fixed">
                        <!--begin::Header Wrapper-->
                        <div class="header-wrapper rounded-top-xl d-flex flex-grow-1 align-items-center">
                            <!--begin::Container-->
                            <div class="container-fluid d-flex align-items-center justify-content-end justify-content-lg-between flex-wrap">
                                <!--begin::Menu Wrapper-->
                                <div class="header-menu-wrapper header-menu-wrapper-left py-lg-2" id="kt_header_menu_wrapper">
                                    <!--begin::Menu-->
                                    <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                        <!--begin::Nav-->
                                        <ul class="menu-nav">
                                            <li class="menu-item  {{ (\Request::route()->getName() == 'dashboard') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                                <a href="{{ route('dashboard') }}" class="menu-link">
                                                    <span class="menu-text">Dashboard</span>
                                                </a>
                                            </li>
                                            @if (in_array('Kuesioner', Session::get('role.menu')))
                                            <li class="menu-item menu-item-submenu menu-item-rel {{ (
                                                    \Request::route()->getName() == 'admin.widget.index' ||
                                                    \Request::route()->getName() == 'admin.kuis.index' ||
                                                    \Request::route()->getName() == 'admin.kuis.approve' ||
                                                    \Request::route()->getName() == 'admin.widget.edit'
                                                ) ? 'menu-item-active' : '' }}" data-menu-toggle="click" aria-haspopup="true">
                                                <a href="javascript:;" class="menu-link menu-toggle">
                                                    <span class="menu-text">Kuesioner</span>
                                                    <span class="menu-desc"></span>
                                                    <i class="menu-arrow"></i>
                                                </a>
                                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                                    <ul class="menu-subnav">
                                                        @if (in_array('admin.widget.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.widget.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#000000" />
                                                                            <rect fill="#000000" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519)" x="16.3255682" y="2.94551858" width="3" height="18" rx="1" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Widget</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.kuis.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.kuis.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z" fill="#000000" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Kuesioner</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.kuis.approve', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.kuis.approve') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.3740377,19.9389434 L22.2226499,11.1660251 C22.4524142,10.8213786 22.3592838,10.3557266 22.0146373,10.1259623 C21.8914367,10.0438285 21.7466809,10 21.5986122,10 L17,10 L17,4.47708173 C17,4.06286817 16.6642136,3.72708173 16.25,3.72708173 C15.9992351,3.72708173 15.7650616,3.85240758 15.6259623,4.06105658 L9.7773501,12.8339749 C9.54758575,13.1786214 9.64071616,13.6442734 9.98536267,13.8740377 C10.1085633,13.9561715 10.2533191,14 10.4013878,14 L15,14 L15,19.5229183 C15,19.9371318 15.3357864,20.2729183 15.75,20.2729183 C16.0007649,20.2729183 16.2349384,20.1475924 16.3740377,19.9389434 Z" fill="#000000"></path>
                                                                            <path d="M4.5,5 L9.5,5 C10.3284271,5 11,5.67157288 11,6.5 C11,7.32842712 10.3284271,8 9.5,8 L4.5,8 C3.67157288,8 3,7.32842712 3,6.5 C3,5.67157288 3.67157288,5 4.5,5 Z M4.5,17 L9.5,17 C10.3284271,17 11,17.6715729 11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L4.5,20 C3.67157288,20 3,19.3284271 3,18.5 C3,17.6715729 3.67157288,17 4.5,17 Z M2.5,11 L6.5,11 C7.32842712,11 8,11.6715729 8,12.5 C8,13.3284271 7.32842712,14 6.5,14 L2.5,14 C1.67157288,14 1,13.3284271 1,12.5 C1,11.6715729 1.67157288,11 2.5,11 Z" fill="#000000" opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Approval</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (in_array('Artikel', Session::get('role.menu')))
                                            <li class="menu-item menu-item-submenu menu-item-rel {{ (\Request::route()->getName() == 'admin.kategori.index' || \Request::route()->getName() == 'admin.artikel.index') ? 'menu-item-active' : '' }}" data-menu-toggle="click" aria-haspopup="true">
                                                <a href="javascript:;" class="menu-link menu-toggle">
                                                    <span class="menu-text">Artikel</span>
                                                    <span class="menu-desc"></span>
                                                    <i class="menu-arrow"></i>
                                                </a>
                                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                                    <ul class="menu-subnav">
                                                        @if (in_array('admin.kategori.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.kategori.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#000000" />
                                                                            <rect fill="#000000" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519)" x="16.3255682" y="2.94551858" width="3" height="18" rx="1" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Kategori</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.artikel.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.artikel.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z" fill="#000000" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Artikel</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (in_array('Inbox', Session::get('role.menu')))
                                            <li class="menu-item menu-item-submenu menu-item-rel {{ (\Request::route()->getName() == 'admin.chat.index') ? 'menu-item-active' : '' }}">
                                                <a href="{{ route('admin.chat.index') }}" class="menu-link">
                                                    <span class="menu-text">Inbox</span>
                                                    <span class="menu-desc"></span>
                                                    <i class="menu-arrow"></i>
                                                </a>
                                            </li>
                                            @endif
                                            @if (in_array('User Management', Session::get('role.menu')))
                                            <li class="menu-item menu-item-submenu menu-item-rel {{ (\Request::route()->getName() == 'admin.member.index' || \Request::route()->getName() == 'admin.user.index' || \Request::route()->getName() == 'admin.role.index') ? 'menu-item-active' : '' }}" data-menu-toggle="click" aria-haspopup="true">
                                                <a href="javascript:;" class="menu-link menu-toggle">
                                                    <span class="menu-text">User Management</span>
                                                    <span class="menu-desc"></span>
                                                    <i class="menu-arrow"></i>
                                                </a>
                                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                                    <ul class="menu-subnav">
                                                        @if (in_array('admin.member.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.member.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3"></path>
                                                                            <path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Catin</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.user.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.user.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                                            <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                                            <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Petugas</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.role.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.role.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.3740377,19.9389434 L22.2226499,11.1660251 C22.4524142,10.8213786 22.3592838,10.3557266 22.0146373,10.1259623 C21.8914367,10.0438285 21.7466809,10 21.5986122,10 L17,10 L17,4.47708173 C17,4.06286817 16.6642136,3.72708173 16.25,3.72708173 C15.9992351,3.72708173 15.7650616,3.85240758 15.6259623,4.06105658 L9.7773501,12.8339749 C9.54758575,13.1786214 9.64071616,13.6442734 9.98536267,13.8740377 C10.1085633,13.9561715 10.2533191,14 10.4013878,14 L15,14 L15,19.5229183 C15,19.9371318 15.3357864,20.2729183 15.75,20.2729183 C16.0007649,20.2729183 16.2349384,20.1475924 16.3740377,19.9389434 Z" fill="#000000"></path>
                                                                            <path d="M4.5,5 L9.5,5 C10.3284271,5 11,5.67157288 11,6.5 C11,7.32842712 10.3284271,8 9.5,8 L4.5,8 C3.67157288,8 3,7.32842712 3,6.5 C3,5.67157288 3.67157288,5 4.5,5 Z M4.5,17 L9.5,17 C10.3284271,17 11,17.6715729 11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L4.5,20 C3.67157288,20 3,19.3284271 3,18.5 C3,17.6715729 3.67157288,17 4.5,17 Z M2.5,11 L6.5,11 C7.32842712,11 8,11.6715729 8,12.5 C8,13.3284271 7.32842712,14 6.5,14 L2.5,14 C1.67157288,14 1,13.3284271 1,12.5 C1,11.6715729 1.67157288,11 2.5,11 Z" fill="#000000" opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Role</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (in_array('Page', Session::get('role.menu')))
                                            <li class="menu-item {{ (\Request::route()->getName() == 'admin.page.index') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                                <a href="{{ route('admin.page.index') }}" class="menu-link">
                                                    <span class="menu-text">Page</span>
                                                </a>
                                            </li>
                                            @endif
                                            @if (in_array('Notifikasi', Session::get('role.menu')))
                                            <li class="menu-item {{ (\Request::route()->getName() == 'admin.notifikasi.index') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                                <a href="{{ route('admin.notifikasi.index') }}" class="menu-link">
                                                    <span class="menu-text">Notifikasi</span>
                                                </a>
                                            </li>
                                            @endif
                                            @if (in_array('Reporting', Session::get('role.menu')))
                                            <li class="menu-item menu-item-submenu menu-item-rel {{ (\Request::route()->getName() == 'admin.repkuis.index') ? 'menu-item-active' : '' }}" data-menu-toggle="click" aria-haspopup="true">
                                                <a href="javascript:;" class="menu-link menu-toggle">
                                                    <span class="menu-text">Reporting</span>
                                                    <span class="menu-desc"></span>
                                                    <i class="menu-arrow"></i>
                                                </a>
                                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                                    <ul class="menu-subnav">
                                                        @if (in_array('admin.repkuis.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.repkuis.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"></path>
                                                                            <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"></path>
                                                                            <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"></rect>
                                                                            <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"></rect>
                                                                            <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"></rect>
                                                                            <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"></rect>
                                                                            <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"></rect>
                                                                            <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"></rect>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Kuesioner</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (in_array('Master Data', Session::get('role.menu')))
                                            <li class="menu-item menu-item-submenu menu-item-rel {{ (\Request::route()->getName() == 'admin.provinsi.index' || \Request::route()->getName() == 'admin.kota.index' || \Request::route()->getName() == 'admin.kecamatan.index' || \Request::route()->getName() == 'admin.kelurahan.index' || \Request::route()->getName() == 'admin.penduduk.index') ? 'menu-item-active' : '' }}" data-menu-toggle="click" aria-haspopup="true">
                                                <a href="javascript:;" class="menu-link menu-toggle">
                                                    <span class="menu-text">Master Data</span>
                                                    <span class="menu-desc"></span>
                                                    <i class="menu-arrow"></i>
                                                </a>
                                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                                    <ul class="menu-subnav">
                                                        @if (in_array('admin.provinsi.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.provinsi.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M22,17 L22,21 C22,22.1045695 21.1045695,23 20,23 L4,23 C2.8954305,23 2,22.1045695 2,21 L2,17 L6.27924078,17 L6.82339262,18.6324555 C7.09562072,19.4491398 7.8598984,20 8.72075922,20 L15.381966,20 C16.1395101,20 16.8320364,19.5719952 17.1708204,18.8944272 L18.118034,17 L22,17 Z" fill="#000000"></path>
                                                                            <path d="M2.5625,15 L5.92654389,9.01947752 C6.2807805,8.38972356 6.94714834,8 7.66969497,8 L16.330305,8 C17.0528517,8 17.7192195,8.38972356 18.0734561,9.01947752 L21.4375,15 L18.118034,15 C17.3604899,15 16.6679636,15.4280048 16.3291796,16.1055728 L15.381966,18 L8.72075922,18 L8.17660738,16.3675445 C7.90437928,15.5508602 7.1401016,15 6.27924078,15 L2.5625,15 Z" fill="#000000" opacity="0.3"></path>
                                                                            <path d="M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.959697, 3.661508) rotate(-270.000000) translate(-11.959697, -3.661508)"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Provinsi</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.kota.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.kota.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M22,17 L22,21 C22,22.1045695 21.1045695,23 20,23 L4,23 C2.8954305,23 2,22.1045695 2,21 L2,17 L6.27924078,17 L6.82339262,18.6324555 C7.09562072,19.4491398 7.8598984,20 8.72075922,20 L15.381966,20 C16.1395101,20 16.8320364,19.5719952 17.1708204,18.8944272 L18.118034,17 L22,17 Z" fill="#000000"></path>
                                                                            <path d="M2.5625,15 L5.92654389,9.01947752 C6.2807805,8.38972356 6.94714834,8 7.66969497,8 L16.330305,8 C17.0528517,8 17.7192195,8.38972356 18.0734561,9.01947752 L21.4375,15 L18.118034,15 C17.3604899,15 16.6679636,15.4280048 16.3291796,16.1055728 L15.381966,18 L8.72075922,18 L8.17660738,16.3675445 C7.90437928,15.5508602 7.1401016,15 6.27924078,15 L2.5625,15 Z" fill="#000000" opacity="0.3"></path>
                                                                            <path d="M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.959697, 3.661508) rotate(-270.000000) translate(-11.959697, -3.661508)"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Kabupaten</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.kecamatan.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.kecamatan.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M22,17 L22,21 C22,22.1045695 21.1045695,23 20,23 L4,23 C2.8954305,23 2,22.1045695 2,21 L2,17 L6.27924078,17 L6.82339262,18.6324555 C7.09562072,19.4491398 7.8598984,20 8.72075922,20 L15.381966,20 C16.1395101,20 16.8320364,19.5719952 17.1708204,18.8944272 L18.118034,17 L22,17 Z" fill="#000000"></path>
                                                                            <path d="M2.5625,15 L5.92654389,9.01947752 C6.2807805,8.38972356 6.94714834,8 7.66969497,8 L16.330305,8 C17.0528517,8 17.7192195,8.38972356 18.0734561,9.01947752 L21.4375,15 L18.118034,15 C17.3604899,15 16.6679636,15.4280048 16.3291796,16.1055728 L15.381966,18 L8.72075922,18 L8.17660738,16.3675445 C7.90437928,15.5508602 7.1401016,15 6.27924078,15 L2.5625,15 Z" fill="#000000" opacity="0.3"></path>
                                                                            <path d="M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.959697, 3.661508) rotate(-270.000000) translate(-11.959697, -3.661508)"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Kecamatan</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.kelurahan.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.kelurahan.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M22,17 L22,21 C22,22.1045695 21.1045695,23 20,23 L4,23 C2.8954305,23 2,22.1045695 2,21 L2,17 L6.27924078,17 L6.82339262,18.6324555 C7.09562072,19.4491398 7.8598984,20 8.72075922,20 L15.381966,20 C16.1395101,20 16.8320364,19.5719952 17.1708204,18.8944272 L18.118034,17 L22,17 Z" fill="#000000"></path>
                                                                            <path d="M2.5625,15 L5.92654389,9.01947752 C6.2807805,8.38972356 6.94714834,8 7.66969497,8 L16.330305,8 C17.0528517,8 17.7192195,8.38972356 18.0734561,9.01947752 L21.4375,15 L18.118034,15 C17.3604899,15 16.6679636,15.4280048 16.3291796,16.1055728 L15.381966,18 L8.72075922,18 L8.17660738,16.3675445 C7.90437928,15.5508602 7.1401016,15 6.27924078,15 L2.5625,15 Z" fill="#000000" opacity="0.3"></path>
                                                                            <path d="M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.959697, 3.661508) rotate(-270.000000) translate(-11.959697, -3.661508)"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Kelurahan</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (in_array('admin.penduduk.index', Session::get('role.submenu')))
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
                                                            <a href="{{ route('admin.penduduk.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M22,17 L22,21 C22,22.1045695 21.1045695,23 20,23 L4,23 C2.8954305,23 2,22.1045695 2,21 L2,17 L6.27924078,17 L6.82339262,18.6324555 C7.09562072,19.4491398 7.8598984,20 8.72075922,20 L15.381966,20 C16.1395101,20 16.8320364,19.5719952 17.1708204,18.8944272 L18.118034,17 L22,17 Z" fill="#000000"></path>
                                                                            <path d="M2.5625,15 L5.92654389,9.01947752 C6.2807805,8.38972356 6.94714834,8 7.66969497,8 L16.330305,8 C17.0528517,8 17.7192195,8.38972356 18.0734561,9.01947752 L21.4375,15 L18.118034,15 C17.3604899,15 16.6679636,15.4280048 16.3291796,16.1055728 L15.381966,18 L8.72075922,18 L8.17660738,16.3675445 C7.90437928,15.5508602 7.1401016,15 6.27924078,15 L2.5625,15 Z" fill="#000000" opacity="0.3"></path>
                                                                            <path d="M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.959697, 3.661508) rotate(-270.000000) translate(-11.959697, -3.661508)"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Penduduk</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                        </ul>
                                        <!--end::Nav-->
                                    </div>
                                    <!--end::Menu-->
                                </div>
                                <!--end::Menu Wrapper-->
                            </div>
                            <!--end::Container-->
                        </div>
                        <!--end::Header Wrapper-->
                    </div>
                    <!--end::Header-->

                    @yield('content')

                    <!--begin::Footer-->
                    <div class="footer py-2 py-lg-0 my-5 d-flex flex-lg-column" id="kt_footer">
                        <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-muted font-weight-bold mr-2">2021©</span>
                                <a href="#" target="_blank" class="text-dark-75 text-hover-primary">ELSIMIL</a>
                            </div>
                            <!--<div class="nav nav-dark order-1 order-md-2">
                                <a href="#" target="_blank" class="nav-link pr-3 pl-0">About</a>
                                <a href="#" target="_blank" class="nav-link px-3">Team</a>
                                <a href="#" target="_blank" class="nav-link pl-3 pr-0">Contact</a>
                            </div>-->
                        </div>
                    </div>
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::Main-->
        <!-- begin::Notifications Panel-->
        <script>var HOST_URL = "/metronic/theme/html/tools/preview";</script>
        <!--begin::Global Config(global config for global JS scripts)-->
        <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#8950FC", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
        <!--end::Global Config-->
        <!--begin::Global Theme Bundle(used by all pages)-->
        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        <!--end::Global Theme Bundle-->
        <script src="{{ asset('assets/plugins/bootbox.js') }}"></script>
        <!--begin::Page Vendors(used by this page)-->
        <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
        <!--end::Page Vendors-->
        <!--begin::Page Scripts(used by this page)-->
        <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
        <!--end::Page Scripts-->
        @stack('script')
    </body>
    <!--end::Body-->
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Page Vendors Styles(used by this page)-->
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Page Vendors Styles-->
</html>
