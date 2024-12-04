<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\PostCodeImporter\PostCodeImporterFactory;
use Illuminate\Console\Command;
use Exception;

class ImportPostcodeCommand extends Command
{
    protected $signature = 'postcodes:import {importer?}';

    protected $description = 'Import postcodes from a data source';

    /**
     * Execute the console command.
     *
     * @param  PostCodeImporterFactory  $importerFactory
     * @return void
     */
    public function handle(PostCodeImporterFactory $importerFactory)
    {
        $importerType = $this->argument('importer') ?? $this->askForImporterType();

        try {
            $importer = $importerFactory->make($importerType);
            $this->info('Starting postcode import using ' . get_class($importer));
            $importer->import();
            $this->info('Postcodes imported successfully.');
        } catch (Exception $e) {
            $this->error('Error during postcode import: ' . $e->getMessage());
        }
    }

    /**
     * Ask the user to choose an importer if not provided.
     *
     * @return string
     */
    private function askForImporterType(): string
    {
        $importers = [
            'parlvid' => 'PalidPostCodeImporter',
        ];

        return $this->choice('Please choose an importer:', array_keys($importers), 0);
    }
}
