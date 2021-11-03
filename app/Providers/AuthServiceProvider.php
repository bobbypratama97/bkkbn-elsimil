<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Policies\ArtikelPolicy;
use App\Policies\ChatPolicy;
use App\Policies\KategoriPolicy;
use App\Policies\KecamatanPolicy;
use App\Policies\KelurahanPolicy;
use App\Policies\KotaPolicy;
use App\Policies\KuisPolicy;
use App\Policies\MemberPolicy;
use App\Policies\NotifikasiPolicy;
use App\Policies\PagePolicy;
use App\Policies\PendudukPolicy;
use App\Policies\PertanyaanPolicy;
use App\Policies\ProvinsiPolicy;
use App\Policies\RepkuisPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\WidgetPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Artikel::class => ArtikelPolicy::class,
        Chat::class => ChatPolicy::class,
        Kategori::class => KategoriPolicy::class,
        Kecamatan::class => KecamatanPolicy::class,
        Kelurahan::class => KelurahanPolicy::class,
        Kota::class => KotaPolicy::class,
        Kuis::class => KuisPolicy::class,
        Member::class => MemberPolicy::class,
        Notifikasi::class => NotifikasiPolicy::class,
        Page::class => PagePolicy::class,
        Penduduk::class => PendudukPolicy::class,
        Pertanyaan::class => PertanyaanPolicy::class,
        Provinsi::class => ProvinsiPolicy::class,
        Repkuis::class => RepkuisPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        Widget::class => WidgetPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
