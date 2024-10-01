<?php

declare(strict_types=1);

namespace backend\Enums;

enum StatusEnum: int
{
    case NEW = 0;
    case APPROVED = 1;
    case DECLINED = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEW      => 'New',
            self::APPROVED => 'Approved',
            self::DECLINED => 'Declined',
        };
    }
}