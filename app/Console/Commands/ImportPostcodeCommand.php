<?php

namespace App\Console\Commands;

use App\Contract\AbstractPostcodeImporter;
use Illuminate\Console\Command;
use Exception;

class ImportPostcodeCommand extends Command
{
    protected $signature = 'postcodes:import {importer?}';
    protected $description = 'Import postcodes from a data source';

    public function handle(AbstractPostcodeImporter $importer)
    {
        $importerClass = $this->argument('importer') ?? get_class($importer);

        try {
            $this->info('Starting postcode import using ' . $importerClass);
            $importer->import();
            $this->info('Postcodes imported successfully.');
        } catch (Exception $e) {
            $this->error('Error during postcode import: ' . $e->getMessage());
        }
    }
}
