<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint\Enum;

trait EnumValuesTrait
{
    public static function values(): array
    {
        $valueList = [];
        foreach (self::cases() as $case) {
            $valueList[] = $case->value;
        }

        return $valueList;
    }
}
