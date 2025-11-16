<?php

namespace App\Enums;

enum JenisKeperluan: string
{
    case JURUSAN = 'Jurusan';
    case PROGRAM_STUDI = 'Program_Studi';
    case LABORATORIUM = 'Laboratorium';
    case BENGKEL = 'Bengkel';
    case BAGIAN = 'Bagian';
    case SUB_BAGIAN = 'Sub_Bagian';
    case UNIT = 'Unit';
    case URUSAN = 'Urusan';
}