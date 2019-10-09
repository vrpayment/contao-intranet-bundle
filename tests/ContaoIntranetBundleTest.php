<?php
/**
 * contao-intranet-bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 47GradNord - Agentur für Internetlösungen
 *
 * @license    commercial
 * @author     Holger Neuner
 */


namespace Vrpayment\ContaoIntranetBundle\Tests;


use PHPUnit\Framework\TestCase;
use Vrpayment\ContaoIntranetBundle\ContaoIntranetBundle;

class ContaoIntranetBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new ContaoIntranetBundle();

        $this->assertInstanceOf('Vrpayment\ContaoIntranetBundle\ContaoIntranetBundle', $bundle);
    }

}
