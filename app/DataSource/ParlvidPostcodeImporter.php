<?php

namespace App\DataSource;

use App\Contract\AbstractPostcodeImporter;
use App\Imports\PostcodesImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ParlvidPostcodeImporter extends  AbstractPostcodeImporter
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
        $extractPath = storage_path('app/temp/extracted');


        try {
            // Fetch data from external URL
            $response = Http::withOptions(['stream' => true])->get(self::DATASOURCE);

            // Check if the response was successful
            if ($response->failed()) {
                throw new \Exception('Failed to download the ZIP file.');
            }

            // Open the file for writing in binary mode
            $fileStream = fopen($zipFilePath, 'wb');
            if ($fileStream === false) {
                throw new \Exception('Failed to open file for writing.');
            }

            while (!$response->getBody()->eof()) {
                fwrite($fileStream, $response->getBody()->read(1024 * 8));
            }
            fclose($fileStream);
            $csvFiles = $this->extractZip($zipFilePath);

            foreach ($csvFiles as $csvFilePath) {

                $import = new PostcodesImport();
                Excel::import($import, $csvFilePath);

                foreach ($import->getChunks() as $chunk) {
                    yield $chunk;
                }

                unlink($csvFilePath);
                gc_collect_cycles();
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

//            if (file_exists($extractPath)) {
//                unlink($extractPath);
//            }
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

        if ($zip->open($zipFilePath) === true) {
            $extractPath= storage_path('app/temp/extracted/');
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0777, true);
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $csvPath = storage_path('app/temp/extracted/Data/multi_csv/');
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
        }

        throw new \Exception('Failed to open the ZIP file.');
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
