<?php

namespace App\Enums;

enum StatusPermintaan: string
{
    case PENDING = 'Pending';
    case PROCESSED = 'Processed'; // Dikerjakan Operator
    case APPROVED = 'Approved';   // Disetujui Approval
    case REJECTED = 'Rejected';
    case SELESAI = 'Selesai';     // Barang sudah diambil
}