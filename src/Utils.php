<?php

namespace NPM\ServiceWebhookHandler\Webhooks;

class Utils
{
    /**
     * Check if an IP lies within a CIDR range.
     *
     * @link http://stackoverflow.com/a/594134/124529
     *
     * @param $ip
     * @param $range
     *
     * @return bool
     */
    public static function cidrMatch($ip, $range): bool
    {
        [$subnet, $bits] = explode('/', $range);
        $ip     = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask   = -1 << (32 - $bits);
        $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned

        return ($ip & $mask) === $subnet;
    }
}
