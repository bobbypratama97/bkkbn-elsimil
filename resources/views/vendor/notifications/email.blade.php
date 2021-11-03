@php
$appName = "ELSIMIL";
@endphp

<header>
    <div class="container">
        <div class="header__img">
            <img style="padding-bottom: .5rem;" src="{{ asset('assets/media/logos/logo.png') }}" alt="logo {{ $appName }}" width="250">
        </div>
        <div class="header__caption">
            <h1 style="margin-top: 3rem;">Hallo!</h1>
            <p style="margin-top: 1.5rem;margin-bottom: 0;">Untuk keamanan data dan penggunaan akun,</p>
            <p style="margin-bottom: 2rem;">mohon untuk melakukan verifikasi email dengan menekan tombol "Verifikasi Email" dibawah ini ya</p>
            <a href="{{ $actionUrl }}" id="header__link">Verifikasi Email</a>
            <p></p> 
        </div>
    </div>
</header>
<div class="container">
    <footer>
        <small>Copyright © <time>{{ date('Y') }}</time> {{ $appName }}.</small>
    </footer>
</div>