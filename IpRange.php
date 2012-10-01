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
 *
 * Copyright 2012 by Matthias ETIENNE <matt@allty.com>
 * Changes : package this function in a namespace/class.
 *
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
class IpTools
{
    /**
     * Checks if an IP is part of ans IP range.
     * 
     * @param string $ip IPv4
     * @param string $range IP range specified in one of the following formats:
     * Wildcard format:     1.2.3.*
     * CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * Start-End IP format: 1.2.3.0-1.2.3.255
     * @return boolean true if IP is part of range, otherwise false.
     */
    static public function ipInRange($ip, $range)
    {
        if (strpos($range, '/') !== false)
        {
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false)
            {
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            }
            else
            {
                $x = explode('.', $range);
                while (count($x) < 4)
                    $x[] = '0';
                list($a, $b, $c, $d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);
                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        }
        else
        {
            if (strpos($range, '*') !== false)
            {
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }
            if (strpos($range, '-') !== false)
            {
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u", ip2long($lower));
                $upper_dec = (float)sprintf("%u", ip2long($upper));
                $ip_dec = (float)sprintf("%u", ip2long($ip));
                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }

            throw new RangeException('Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format');
            return false;
        }

    }

}
