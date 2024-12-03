<?php

namespace App\Jobs;

use App\Models\Postcode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class PostCodeImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Collection $postcodes) {}


    public function handle(): void
    {
        Postcode::upsert(
            $this->postcodes->toArray(),
            ['postcode'],
            ['latitude', 'longitude']
        );
    }
}
