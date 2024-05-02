<?php

namespace Myracloud\WebApi\Endpoint\Enum;

enum ObjectVOEnum: string
{
    case SslCertVO = 'SslCertVO';
    case QueryVO = 'QueryVO';
    case ResultVO = 'ResultVO';
    case ViolationVO = 'ViolationVO';
    case StatisticVO = 'StatisticVO';
    case StatisticQueryVO = 'StatisticQueryVO';
    case DomainVO = 'DomainVO';
    case DomainBindVO = 'DomainBindVO';
    case DnsRecordVO = 'DnsRecordVO';
    case CacheSettingVO = 'CacheSettingVO';
    case CacheClearVO = 'CacheClearVO';
    case SslIntermediateVO = 'SslIntermediateVO';
}
