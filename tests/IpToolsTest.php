<?php

namespace Allty\Utils\Tests;

use Allty\Utils\IpTools;

/**
 * IpToolsTest class
 *
 * @author Armando Lüscher <armando@noplanman.ch>
 */
class IpToolsTest extends \PHPUnit\Framework\TestCase
{
    public function testIpEqual()
    {
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.4'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.2.3.3'));

        // Invalid IPv4 format.
        self::assertFalse(IpTools::ipInRange('1.2.3', '1.2.3'));
    }

    public function testIpInCidr()
    {
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.0/24'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.0.0/16'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.0.0.0/8'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3/24'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2/16'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1/8'));

        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.2.4.0/24'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.3.0.0/16'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '2.0.0.0/8'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.2.4/24'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.3/16'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '2/8'));

        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.5/255.255.0.0'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.4/255.255.255.255'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.0/255.255.255.0'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.255/255.255.255.0'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.0.0/255.255.0.0'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.255.255/255.255.0.0'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.0.0.0/255.0.0.0'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.255.255.255/255.0.0.0'));
    }

    public function testIpInWildcard()
    {
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.*'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.*.*'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.*.*.*'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '*.*.*.*'));

        // Short-hands.
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.*'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.*'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '*'));

        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.2.2.*'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.2.4.*'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.1.*'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.3.*'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '0.*'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '2.*'));
    }

    public function testIpInStartEnd()
    {
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.2.3.2-1.2.3.3'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.3-1.2.3.4'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.3-1.2.3.5'));
        self::assertTrue(IpTools::ipInRange('1.2.3.4', '1.2.3.4-1.2.3.5'));
        self::assertFalse(IpTools::ipInRange('1.2.3.4', '1.2.3.5-1.2.3.6'));
    }
}
