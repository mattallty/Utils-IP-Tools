<?php
/**
 * Function to determine if an IP is located in a specific range as
 * specified via several alternative formats.
 *
 * Network ranges can be specified as:
 * 1. Wildcard format:     1.2.3.*
 * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
 * 3. Start-End IP format: 1.2.3.0-1.2.3.255
 *
 * Copyright 2017 by Armando Lüscher <armando@noplanman.ch>
 * Changes: Split up code and add tests.
 *
 * Copyright 2012 by Matthias ETIENNE <matt@allty.com>
 * Changes : package this function in a namespace/class.
 *
 * Copyright 2008: Paul Gregg <pgregg@pgregg.com>
 * 10 January 2008
 * Version: 1.2
 *
 * Source website: http://www.pgregg.com/projects/php/ip_in_range/
 * Version 1.2
 *
 * This software is Donationware - if you feel you have benefited from
 * the use of this tool then please consider a donation. The value of
 * which is entirely left up to your discretion.
 * http://www.pgregg.com/donate/
 *
 * Please do not remove this header, or source attibution from this file.
 *
 */

namespace Allty\Utils;

/**
 * IpTools class
 *
 * @author Matthias ETIENNE <matt@allty.com>
 *
 */
final class IpTools
{
    /**
     * Initialisation is futile.
     */
    private function __construct()
    {
    }

    /**
     * Checks if an IP is part of an IP range.
     *
     * @param string $ip    IPv4
     * @param string $range IP range specified in one of the following formats:
     *                      Wildcard format:     1.2.3.*
     *                      CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     *                      Start-End IP format: 1.2.3.0-1.2.3.255
     *
     * @return boolean true if IP is part of range, otherwise false.
     */
    public static function ipInRange($ip, $range)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        if ($ip === $range) {
            return true;
        }

        if (strpos($range, '/') !== false) {
            return self::ipInCidr($ip, $range);
        }

        if (strpos($range, '*') !== false) {
            return self::ipInWildcard($ip, $range);
        }

        if (strpos($range, '-') !== false) {
            return self::ipInStartEnd($ip, $range);
        }

        return false;
    }

    /**
     * Check if IP is in a CIDR range.
     *
     * @param $ip
     * @param $range
     *
     * @return bool
     */
    private static function ipInCidr($ip, $range)
    {
        list($range, $netmask) = explode('/', $range, 2);

        $range = self::fillIpv4RangeWith($range, '0');

        if (strpos($netmask, '.') !== false) {
            $netmask     = str_replace('*', '0', $netmask);
            $netmask_dec = ip2long($netmask);
        } else {
            $wildcard_dec = pow(2, 32 - $netmask) - 1;
            $netmask_dec  = ~$wildcard_dec;
        }

        return (ip2long($ip) & $netmask_dec) === (ip2long($range) & $netmask_dec);
    }

    /**
     * Check if IP is in a wildcard range.
     *
     * @param $ip
     * @param $range
     *
     * @return bool
     */
    private static function ipInWildcard($ip, $range)
    {
        $range = self::fillIpv4RangeWith($range, '*');

        $lower = str_replace('*', '0', $range);
        $upper = str_replace('*', '255', $range);
        $range = "$lower-$upper";

        return self::ipInStartEnd($ip, $range);
    }

    /**
     * Check if IP is in a start-end range.
     *
     * @param $ip
     * @param $range
     *
     * @return bool
     */
    private static function ipInStartEnd($ip, $range)
    {
        list($lower, $upper) = explode('-', $range, 2);
        $lower_dec = (float) sprintf('%u', ip2long($lower));
        $upper_dec = (float) sprintf('%u', ip2long($upper));
        $ip_dec    = (float) sprintf('%u', ip2long($ip));

        return ($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec);
    }

    /**
     * Fill IP with given filler.
     *
     * e.g. $filler = *
     * 1.2.3 -> 1.2.3.*
     * 1     -> 1.*.*.*
     *
     * @param $range
     * @param $filler
     *
     * @return string
     */
    private static function fillIpv4RangeWith($range, $filler)
    {
        return implode('.', array_pad(explode('.', $range), 4, $filler));
    }
}
