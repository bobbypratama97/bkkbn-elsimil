@php
$appName = "ELSIMIL";
@endphp

<header>
    <div class="container">
        <div class="header__img">
            <img style="padding-bottom: .5rem;color: #000;" src="{{ asset('assets/media/logos/logo-new.png') }}" alt="logo {{ $appName }}" width="250">
        </div>
        <div class="header__caption">
            <h3 style="margin-top: 3rem;color: #000;">Hai {{ $data->name }},</h3>
            <p style="margin-top: 1.5rem;margin-bottom: 0;color: #000;">Kami telah menerima pengajuan Anda untuk pengubahan password.</p>
            <p style="margin-bottom: 2rem;color: #000;">Ubah password Anda dengan mengklik tombol di bawah ini</p>
            <a href="{{ url($data->link) }}" id="header__link">Ubah Password</a>
            <p></p> 
            <p>Jika kamu tidak meminta pengubahan password, abaikan <i>email</i> ini</p>
        </div>
    </div>
</header>
<div class="container">
    <footer>
        <small>Harap tidak membalas ke e-mail ini karena kami tidak mengawasi e-mail yang dikirimkan ke alamat ini. Pesan ini adalah pesan layanan sehubungan dengan penggunaan Anda di {{ $appName }}</small>
    </footer>
</div>
<div class="container">
    <footer>
        <small>Copyright © <time>{{ date('Y') }}</time> {{ $appName }}.</small>
    </footer>
</div>