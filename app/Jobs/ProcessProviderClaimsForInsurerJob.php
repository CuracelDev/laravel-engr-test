<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Insurer;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessProviderClaimsForInsurerJob implements ShouldQueue
{
    use Queueable, Batchable;

    protected $provider;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Insurer $insurer, protected Collection $claims, protected int $provider_id)
    {
        $this->provider = User::findOrFail($provider_id);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->insurer->batchClaimsForProcessing($this->provider, $this->claims);
        Cache::forget($this->provider->id . '_claims_summary');
    }
}
