<?php

namespace App\Enums;

enum Role: string
{
    case OPERATOR = 'operator';
    case APPROVAL = 'approval';
    case PEMOHON = 'pemohon';
}