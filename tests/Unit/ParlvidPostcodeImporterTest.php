<?php


namespace Tests\Unit;

use App\Models\Country;
use App\PostCodeImporter\ParlvidPostcodeImporter;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class ParlvidPostcodeImporterTest extends TestCase
{
    use DatabaseMigrations;

    public function testFetchData()
    {
        Storage::fake('local');
        $this->createExtractedDirectory();

        $zipFilePath = $this->createMockZipFile();

        $mockHttpClient = Mockery::mock('alias:Illuminate\Support\Facades\Http');
        $mockHttpClient->shouldReceive('withOptions')
            ->andReturnSelf();
        $mockHttpClient->shouldReceive('get')
            ->andReturn(Mockery::mock(ResponseInterface::class, [
                'failed' => false,
                'getBody' => Mockery::mock('Stream', [
                    'eof' => false,
                    'read' => 'file content chunk'
                ])
            ]));


        $mockCountry = Mockery::mock(Country::class);
        $mockCountry->shouldReceive('getByCodeWithCache')
            ->andReturn((object)['id' => 1]);

        $this->app->instance(Country::class, $mockCountry);

        $importer = new ParlvidPostcodeImporter($zipFilePath, $mockHttpClient);
        $importer->setExtractionDirectory('tests/Unit/temp');
        $importer->setCsvPath('tests/Unit/temp/extracted/Data/multi_csv/');
        $chunks = iterator_to_array($importer->fetchData());

        $this->assertCount(1, $chunks);

        $this->assertArrayHasKey('pcd', $chunks[0][0]);
        $this->assertArrayHasKey('long', $chunks[0][0]);
        $this->assertArrayHasKey('lat', $chunks[0][0]);
        $this->assertArrayHasKey('country_id', $chunks[0][0]);
        unlink($zipFilePath);
    }

    protected function createExtractedDirectory()
    {
        $extractedDirectory = storage_path('tests/Unit/temp/extracted/Data/multi_csv/');
        if (!is_dir($extractedDirectory)) {
            mkdir($extractedDirectory, 0777, true);
        }
    }

    protected function createMockZipFile()
    {
        $csvContent = [
            ['pcd', 'long', 'lat', 'country_id'],
            ['AB1 0AA', '-0.12345', '51.12345', '1'],
            ['AB1 0AB', '-0.12346', '51.12346', '1'],
            ['AB1 0AC', '-0.12347', '51.12347', '1'],
        ];

        $csvFilePath = storage_path('tests/Unit/temp/mock_data.csv');
        $file = fopen($csvFilePath, 'w');
        foreach ($csvContent as $line) {
            fputcsv($file, $line);
        }
        fclose($file);

        $extractedDirectory = storage_path('tests/Unit/temp/extracted/Data/multi_csv/');
        $mockCsvFilePath = $extractedDirectory . 'mock_data.csv';
        copy($csvFilePath, $mockCsvFilePath);

        $zipFilePath = storage_path('tests/Unit/temp/mock_parlvid.zip');
        $zip = new \ZipArchive();

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
            $zip->addFile($mockCsvFilePath, 'mock_data.csv');
            $zip->close();
        } else {
            throw new \Exception("Failed to create the ZIP file.");
        }

        return $zipFilePath;
    }

}
