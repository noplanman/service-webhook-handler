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

    /**
     * Get the contents of a file and cache it.
     *
     * @param string $url
     * @param string $file
     * @param int    $cache_time
     *
     * @return string
     */
    public static function fetchCacheableFile($url, $file, $cache_time = 60): string
    {
        if (file_exists($file)) {
            if (filemtime($file) + $cache_time > time()) {
                $url = $file;
            } else {
                unlink($file);
            }
        }

        if ($url === $file) {
            $contents = (string) file_get_contents($url);
        } elseif (filter_var($url, FILTER_VALIDATE_URL)) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-Service-Webhook-Handler');
            $contents = curl_exec($ch);
            curl_close($ch);
        } else {
            $contents = file_get_contents($url);
        }

        if ($contents && is_writable(dirname($file))) {
            file_put_contents($file, $contents);
        }

        return $contents;
    }
}
