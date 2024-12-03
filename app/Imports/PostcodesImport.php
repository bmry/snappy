<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;


class PostcodesImport implements OnEachRow, WithChunkReading, WithHeadingRow
{
    public $chunks = [];

    public function onRow(Row $row)
    {
        $this->chunks[] = $row->toArray();
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getChunks()
    {
        return $this->chunks;
    }
}
