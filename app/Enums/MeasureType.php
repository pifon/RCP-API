<?php

declare(strict_types=1);

namespace App\Enums;

enum MeasureType: string
{
    case MASS = 'M';
    case VOLUME = 'V';
    case TIME = 'T';
    case LENGTH = 'L';
    case CUSTOMARY = 'C';
    case OTHER = 'O';
}
