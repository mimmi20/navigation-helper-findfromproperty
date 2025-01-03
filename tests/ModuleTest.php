<?php

/**
 * This file is part of the mimmi20/navigation-helper-findfromproperty package.
 *
 * Copyright (c) 2021-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\NavigationHelper\FindFromProperty;

use Mimmi20\NavigationHelper\FindFromProperty\FindFromProperty;
use Mimmi20\NavigationHelper\FindFromProperty\FindFromPropertyInterface;
use Mimmi20\NavigationHelper\FindFromProperty\Module;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class ModuleTest extends TestCase
{
    /** @throws Exception */
    public function testGetConfig(): void
    {
        $module = new Module();

        $config = $module->getConfig();

        self::assertIsArray($config);
        self::assertArrayHasKey('service_manager', $config);

        $dependencies = $config['service_manager'];
        self::assertIsArray($dependencies);
        self::assertCount(2, $dependencies);
        self::assertArrayHasKey('factories', $dependencies);
        self::assertArrayHasKey('aliases', $dependencies);

        $factories = $dependencies['factories'];
        self::assertIsArray($factories);
        self::assertCount(1, $factories);
        self::assertArrayHasKey(FindFromProperty::class, $factories);

        $aliases = $dependencies['aliases'];
        self::assertIsArray($aliases);
        self::assertCount(1, $aliases);
        self::assertArrayHasKey(FindFromPropertyInterface::class, $aliases);
    }

    /** @throws Exception */
    public function testGetModuleDependencies(): void
    {
        $module = new Module();

        $config = $module->getModuleDependencies();

        self::assertIsArray($config);
        self::assertCount(3, $config);
        self::assertArrayHasKey(0, $config);
        self::assertContains('Laminas\Navigation', $config);
        self::assertContains('Mimmi20\NavigationHelper\Accept', $config);
        self::assertContains('Mimmi20\NavigationHelper\ConvertToPages', $config);
    }
}
