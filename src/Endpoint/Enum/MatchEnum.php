<?php

namespace Myracloud\WebApi\Endpoint\Enum;

enum MatchEnum: string
{
    use EnumValuesTrait;

    case Prefix = 'prefix';
    case Suffix = 'suffix';
    case Exact = 'exact';
}
