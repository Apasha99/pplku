<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Mahasiswa([
            'nama' => $row['nama'],
            'nim' => $row['nim'],
            'angkatan' => $row['angkatan'],
            'status' => 'active',
            'jalur_masuk' => $row['jalur_masuk'],
            'nip' => $row['nip']
        ]);
    }
}

