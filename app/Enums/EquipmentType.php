<?php

namespace App\Enums;

enum EquipmentType: string
{
    case Barbell = 'barbell';
    case Dumbbell = 'dumbbell';
    case Machine = 'machine';
    case Cable = 'cable';
    case Bodyweight = 'bodyweight';
    case Kettlebell = 'kettlebell';
    case Band = 'band';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Barbell => 'Barbell',
            self::Dumbbell => 'Dumbbell',
            self::Machine => 'Machine',
            self::Cable => 'Cable',
            self::Bodyweight => 'Bodyweight',
            self::Kettlebell => 'Kettlebell',
            self::Band => 'Resistance Band',
            self::Other => 'Other',
        };
    }
}
