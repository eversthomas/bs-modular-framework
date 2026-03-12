<?php

declare(strict_types=1);

namespace BS\ModularFramework\Tests\Unit;

use BS\ModularFramework\Core\Plugin;
use PHPUnit\Framework\TestCase;

/**
 * Einfache Smoke-Tests für den Plugin-Bootstrap.
 */
class PluginBootstrapTest extends TestCase
{
    public function test_plugin_class_exists(): void
    {
        $this->assertTrue(class_exists(Plugin::class));
    }

    public function test_plugin_constants_are_defined(): void
    {
        $this->assertIsString(Plugin::VERSION);
        $this->assertIsString(Plugin::DB_VERSION);
        $this->assertIsString(Plugin::OPTION_DB_VERSION);
    }
}

