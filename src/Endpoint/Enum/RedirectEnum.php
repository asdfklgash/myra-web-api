<?php

namespace Myracloud\WebApi\Endpoint\Enum;

enum RedirectEnum: string
{
    use EnumValuesTrait;

    case Permanent = 'permanent';
    case Redirect = 'redirect';
}
