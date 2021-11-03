@php
$appName = "ELSIMIL";
@endphp

<header>
    <div class="container">
        <div class="header__caption">
            <h1 style="margin-top: 3rem;color: #000;">Hallo!</h1>
            <p style="margin-top: 1.5rem;margin-bottom: 0;color: #000;">Kirimkan kode OTP berikut kepada pasangan Anda untuk menghubungkan data pasangan</p>
            <h1 style="margin-top: 3rem;color: #000;">{{ $data->kodeotp }}</h1>
            <p></p> 
        </div>
    </div>
</header>
<div class="container">
    <footer>
        <small>Copyright © <time>{{ date('Y') }}</time> {{ $appName }}.</small>
    </footer>
</div>