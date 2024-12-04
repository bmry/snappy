<?php

declare(strict_types=1);

namespace App\PostCodeImporter;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ParlvidPostcodeImporter extends AbstractPostcodeImporter
{
    const IDENTIFIER = 'parlvid';
    const DATASOURCE = 'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip';

    protected $csvPath = 'app/temp/extracted/Data/multi_csv';

    protected $extractionDirectory = 'app/temp/extracted/';

    protected $dataSource;

    /**
     * Constructor
     *
     * @param mixed $dataSource Either a URL or a local file path to the ZIP file
     */
    public function __construct(string $dataSource = null)
    {
        $this->dataSource = $dataSource ?: self::DATASOURCE;
    }

    /**
     * Fetch data from the external source by downloading and extracting a ZIP file.
     * The data is then processed and returned as chunks.
     *
     * @return \Generator The data chunks after importing from the CSV.
     * @throws \Exception If the download, extraction, or import fails.
     */
    public function fetchData():mixed
    {
        $zipFilePath = storage_path('app/temp/parlvid.zip');

        try {
            // If the data source is a URL, use HTTP to fetch the file. If it's a path, use the file directly.
            if (filter_var($this->dataSource, FILTER_VALIDATE_URL)) {
                $this->downloadZipFile($zipFilePath);
            } else {
                // Assume it's a local file path for testing purposes
                copy($this->dataSource, $zipFilePath);
            }

            $csvFiles = $this->extractZip($zipFilePath);

            foreach ($csvFiles as $csvFilePath) {
                $file = new \SplFileObject($csvFilePath);
                $file->setFlags(\SplFileObject::READ_CSV);

                $headers = null;
                $chunkSize = 1000;
                $chunk = [];

                foreach ($file as $row) {
                    if ($row === [null]) {
                        continue;
                    }

                    if ($headers === null) {
                        $headers = $row;
                        continue;
                    }

                    $rowAssoc = array_combine($headers, $row);
                    $rowAssoc['country_id'] = Country::getByCodeWithCache('GB')->id;
                    $chunk[] = $rowAssoc;

                    if (count($chunk) >= $chunkSize) {
                        yield $chunk;
                        $chunk = [];
                    }
                }

                if (count($chunk) > 0) {
                    yield $chunk;
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch or process data", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        } finally {
            if (file_exists($zipFilePath)) {
                unlink($zipFilePath);
            }
        }
    }

    /**
     * Downloads the ZIP file from the specified URL.
     *
     * @param string $zipFilePath Path where the file should be saved
     * @throws \Exception If download fails
     */
    protected function downloadZipFile($zipFilePath)
    {
        $response = Http::withOptions(['stream' => true])->get($this->dataSource);

        if ($response->failed()) {
            throw new \Exception('Failed to download the ZIP file.');
        }

        $fileStream = fopen($zipFilePath, 'wb');

        if ($fileStream === false) {
            throw new \Exception('Failed to open file for writing.');
        }

        while (!$response->getBody()->eof()) {
            fwrite($fileStream, $response->getBody()->read(1024 * 1024));
        }

        fclose($fileStream);
    }

    /**
     * Extracts the ZIP file and returns a list of CSV file paths.
     *
     * @param string $zipFilePath
     * @return array
     * @throws \Exception
     */
    protected function extractZip($zipFilePath)
    {
        $zip = new \ZipArchive();

        if ($zip->open($zipFilePath) === true) {
            $extractPath = storage_path($this->extractionDirectory);
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0777, true);
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $csvPath = storage_path($this->csvPath);
            $extractedFiles = scandir($csvPath);

            $csvFiles = [];

            foreach ($extractedFiles as $file) {
                if (strpos($file, '.csv') !== false) {
                    $csvFiles[] = $csvPath . DIRECTORY_SEPARATOR . $file;
                }
            }

            if (empty($csvFiles)) {
                throw new \Exception('No CSV files found in the ZIP archive.');
            }

            return $csvFiles;
        }

        throw new \Exception('Failed to open the ZIP file.');
    }

    protected function getMapper(): array
    {
        return [
            'postcode' => 'pcd',
            'longitude' => 'long',
            'latitude' => 'lat',
            'country_id' => 'country_id'
        ];
    }

    public function setExtractionDirectory(string $extractionDir): void
    {
        $this->extractionDirectory = $extractionDir;
    }

    public function setCsvPath(string $csvPath): void
    {
        $this->csvPath = $csvPath;
    }
}
