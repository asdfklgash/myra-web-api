<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command\Enum;

use Myracloud\WebApi\Endpoint\Enum\EnumValuesTrait;

enum ExecutionTypeEnum: string
{
    use EnumValuesTrait;

    case List = 'list';
    case Delete = 'delete';
    case Create = 'create';
    case Update = 'update';
    case Export = 'export';
}
