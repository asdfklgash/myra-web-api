<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command\Enum;

enum WhitelistFormatEnum: string
{
    case IPTables = 'iptables';
    case IP6Tables = 'ip6tables';
    case IPSet = 'ipset';
    case NFTables = 'nftables';
}
