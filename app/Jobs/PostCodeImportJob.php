<?php

namespace App\Jobs;

use App\Models\Postcode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PostCodeImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Collection $postcodes) {}


    public function handle(): void
    {
        try {
            Postcode::upsert(
                $this->postcodes->toArray(),
                ['postcode', 'country_id'],
                ['latitude', 'longitude', 'country_id']
            );
        } catch (\Exception $exception) {

            Log::error('Error during postcode upsert operation: ' . $exception->getMessage(), [
                'exception' => $exception,
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }
}
