<?php

declare(strict_types=1);

namespace App\PostCodeImporter;

use InvalidArgumentException;

class PostCodeImporterFactory
{
    /**
     * Creates an importer instance based on the provided importer type.
     *
     * @param string $importerType The type of the importer to create.
     *
     * @return ParlvidPostcodeImporter
     *
     * @throws \InvalidArgumentException If the provided importer type is invalid.
     */
    public function make(string $importerType): ParlvidPostcodeImporter
    {
        if ($importerType === ParlvidPostcodeImporter::IDENTIFIER) {
            return new ParlvidPostcodeImporter();
        }

        throw new InvalidArgumentException('Invalid importer type provided');
    }
}
