<?php

namespace App\Enums;

enum PackageType: string
{
    case WEDDING = 'Wedding';
    case ENGAGEMENT = 'Engagement';
    case CORPORATE = 'Corporate';
    case OTHER = 'Other';

    public function label(): string
    {
        return match ($this) {
            self::WEDDING => 'Wedding',
            self::ENGAGEMENT => 'Engagement / Lamaran',
            self::CORPORATE => 'Corporate Event',
            self::OTHER => 'Lainnya',
        };
    }
}
