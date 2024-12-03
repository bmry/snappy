<?php

namespace Tests\Unit;

namespace Tests\Unit;

use App\DataSource\ParlvidPostcodeImporter;
use App\Imports\PostcodesImport;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Storage;

class ParlvidPostcodeImporterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Mocking the HTTP request to return a fake response
        Http::fake([
            'http://parlvid.mysociety.org/os/' => Http::response('fake zip content', 200)
        ]);

        // Mocking Excel import
        Excel::fake();
    }

    public function testFetchDataHandlesSuccessfulImport()
    {
        // Create a mock of the ParlvidPostcodeImporter class
        $importer = $this->getMockBuilder(ParlvidPostcodeImporter::class)
            ->onlyMethods(['extractZip'])
            ->getMock();


        $importer->expects($this->once())
            ->method('extractZip')
            ->willReturn('/path/to/fake/csv/file.csv');

        // Continue with the test setup and assertions
        $response = $importer->fetchData();
        dd($response);
//
//        $this->assertNotEmpty($response);
    }

    public function testFetchDataHandlesDownloadFailure()
    {
        $importer = new ParlvidPostcodeImporter();

        Http::fake([
            'http://parlvid.mysociety.org/os/' => Http::response(null, 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to download the ZIP file.');

        $importer->fetchData();
    }

    public function testHandlesExceptionDuringDataExtraction()
    {
        $importer = new ParlvidPostcodeImporter();

        $importer = Mockery::mock(ParlvidPostcodeImporter::class)->makePartial();
        $importer->shouldReceive('extractZip')
            ->once()
            ->andThrow(new \Exception('Failed to extract ZIP file.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to extract ZIP file.');

        $importer->fetchData();
    }

    public function testCleanupOnFailure()
    {
        $importer = Mockery::mock(ParlvidPostcodeImporter::class)->makePartial();
        $importer->shouldReceive('extractZip')->andThrow(new \Exception('Test error'));

        Storage::shouldReceive('exists')->andReturn(true);

        $this->expectException(\Exception::class);
        $importer->fetchData();
        Storage::assertMissing('app/temp/parlvid.zip');
        Storage::assertMissing('app/temp/parlvid.csv');
    }
}

