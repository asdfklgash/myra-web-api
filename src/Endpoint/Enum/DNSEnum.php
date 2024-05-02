<?php declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint\Enum;

enum DNSEnum: string
{
    use EnumValuesTrait;

    case A = 'A';
    case AAAA = 'AAAA';
    case MX = 'MX';
    case CNAME = 'CNAME';
    case TXT = 'TXT';
    case NS = 'NS';
    case SRV = 'SRV';
    case CAA = 'CAA';
}
