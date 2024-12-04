<?php

declare(strict_types=1);

namespace App\PostCodeImporter;

use App\Jobs\PostCodeImportJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

abstract class AbstractPostcodeImporter
{
    protected string $countryCode;

    /**
     * Imports the data by processing each chunk, normalizing it, and dispatching a job.
     * Logs any errors encountered during the import process.
     *
     * @return void
     */
    public function import(): void
    {
        foreach ($this->fetchData() as $chunk) {
            try {
                $chunkedData = collect($chunk);
                $validatedData = $this->normalizeData($chunkedData);
                PostCodeImportJob::dispatch($validatedData);
            } catch (\Exception $e) {
                Log::error('Failed to dispatch job for a chunk of data', [
                    'error' => $e->getMessage(),
                    'chunk' => $chunk,
                ]);
            }
        }
    }

    /**
     * Fetch the raw data from the external source.
     * This should be implemented in child classes.
     *
     * @return mixed
     */
    abstract protected function fetchData(): mixed;

    /**
     * Normalize the raw data by mapping the values to a standard format.
     * This method converts the postcode to uppercase, and ensures that
     * latitude and longitude are cast to float values.
     *
     * @param \Illuminate\Support\Collection $rawData The raw data to normalize.
     *
     * @return \Illuminate\Support\Collection A collection of normalized data, with postcode, latitude, and longitude.
     */
    public function normalizeData(Collection $rawData): Collection
    {
        $mapper = $this->getMapper();

        return $rawData->map(function ($item) use ($mapper) {
            return [
                'postcode'  => strtoupper(str_replace(' ', '', $item[$mapper['postcode']])),
                'latitude'  => (float) $item[$mapper['latitude']],
                'longitude' => (float) $item[$mapper['longitude']],
                'country_id' => (int) $item[$mapper['country_id']],
            ];
        });
    }

    /**
     * Abstract method to retrieve the mapper for converting
     * geographical data (postcode, longitude, latitude) to their respective keys in the dataset.
     *
     * The returned mapper should be an associative array in the format:
     * [
     *      'postcode' => 'postcode',
     *      'longitude' => 'lat',
     *      'latitude' => 'lon',
     *      'country_code' => 'country_code' // e.g Alpha-2 codes US, GB
     * ]
     *
     * Implementing classes must define this method to provide the specific key mapping
     * for the geographical data.
     *
     * @return array The mapping array that associates 'postcode', 'longitude', and 'latitude'
     *               to their respective keys in the data.
     */
    abstract protected function getMapper(): array;
}
