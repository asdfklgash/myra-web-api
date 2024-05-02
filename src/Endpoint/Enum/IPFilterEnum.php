<?php declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint\Enum;

enum IPFilterEnum: string
{
    use EnumValuesTrait;

    case Whitelist = 'WHITELIST';
    case Blacklist = 'BLACKLIST';
    case AliasWhitelist = 'wl';
    case AliasBlacklist = 'bl';

    public function realType(): IPFilterEnum
    {
        return match ($this) {
            self::Whitelist, self::Blacklist => $this,
            self::AliasWhitelist => self::Whitelist,
            self::AliasBlacklist => self::Blacklist,
        };
    }
}
