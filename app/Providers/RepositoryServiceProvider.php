<?php

namespace App\Providers;

use App\Repositories\BatchRepo;
use App\Repositories\ClaimRepo;
use App\Contracts\BatchContract;
use App\Contracts\ClaimContract;
use App\Repositories\InsurerRepo;
use App\Contracts\InsurerContract;
use App\Repositories\SpecialtyRepo;
use App\Contracts\SpecialtyContract;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $repositories = [
        InsurerContract::class => InsurerRepo::class,
        SpecialtyContract::class => SpecialtyRepo::class,
        ClaimContract::class => ClaimRepo::class,
        BatchContract::class => BatchRepo::class,
    ];
    
    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}