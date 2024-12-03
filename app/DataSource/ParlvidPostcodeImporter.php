<?php

namespace App\DataSource;

use App\Contract\AbstractPostcodeImporter;
use App\Imports\PostcodesImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ParlvidPostcodeImporter extends AbstractPostcodeImporter
{
    const IDENTIFIER = 'parlvid';
    const DATASOURCE = 'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip';


    /**
     * Fetch data from the external source by downloading and extracting a ZIP file.
     * The data is then processed and returned as chunks.
     *
     * @return \Generator The data chunks after importing from the CSV.
     * @throws \Exception If the download, extraction, or import fails.
     */
    public function fetchData()
    {
        $zipFilePath = storage_path('app/temp/parlvid.zip');

        try {

            $response = Http::withOptions(['stream' => true])->get(self::DATASOURCE);

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
                    $chunk[] = $rowAssoc;

                    if (count($chunk) >= $chunkSize) {
                        yield $chunk;
                        exit;
                        $chunk = [];
                    }
                }

                if (count($chunk) > 0) {
                    yield $chunk;
                }
            }
        } catch (\Exception $e) {
            // Log the error
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
     * Extracts the ZIP file and returns a list of CSV file paths.
     *
     * @param string $zipFilePath
     * @return array
     * @throws \Exception
     */
    protected function extractZip($zipFilePath)
    {
        $zip = new \ZipArchive();

       // if ($zip->open($zipFilePath) === true) {
            $extractPath = storage_path('app/temp/extracted/');
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0777, true);
            }

//            $zip->extractTo($extractPath);
//            $zip->close();

            $csvPath = storage_path('app/temp/extracted/Data/multi_csv');
            // Get all extracted files
            $extractedFiles = scandir($csvPath);

            $csvFiles = [];

            foreach ($extractedFiles as $file) {
                // If the file is a CSV file, add it to the list
                if (strpos($file, '.csv') !== false) {
                    $csvFiles[] = $csvPath . DIRECTORY_SEPARATOR . $file;
                }
            }

            if (empty($csvFiles)) {
                throw new \Exception('No CSV files found in the ZIP archive.');
            }

            return $csvFiles;
       // }

        //throw new \Exception('Failed to open the ZIP file.');
    }


    protected function getMapper()
    {
        return [
            'postcode' => 'pcd',
            'longitude' => 'long',
            'latitude' => 'lat'
        ];
    }
}
