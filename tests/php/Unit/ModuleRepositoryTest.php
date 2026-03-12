<?php

declare(strict_types=1);

namespace BS\ModularFramework\Tests\Unit;

use BS\ModularFramework\Data\ModuleRepository;
use BS\ModularFramework\Domain\Module;
use PHPUnit\Framework\TestCase;
use wpdb;

class ModuleRepositoryTest extends TestCase
{
    public function test_find_by_id_maps_row_to_module(): void
    {
        $db = $this->createMock(wpdb::class);
        $db->prefix = 'wp_';

        $row              = new \stdClass();
        $row->id          = 5;
        $row->name        = 'Einrichtungen';
        $row->slug        = 'einrichtungen';
        $row->description = 'Beschreibung';
        $row->status      = 'active';
        $row->sort_order  = 10;
        $row->created_at  = '2026-03-12 10:00:00';
        $row->updated_at  = '2026-03-12 11:00:00';

        $db->method('prepare')->willReturnCallback(
            static fn (string $query, int $id): string => sprintf($query, $id)
        );
        $db->method('get_row')->willReturn($row);

        $repo   = new ModuleRepository($db);
        $module = $repo->find_by_id(5);

        $this->assertInstanceOf(Module::class, $module);
        $this->assertSame(5, $module->id);
        $this->assertSame('Einrichtungen', $module->name);
        $this->assertSame('einrichtungen', $module->slug);
        $this->assertSame('Beschreibung', $module->description);
        $this->assertSame('active', $module->status);
        $this->assertSame(10, $module->sort_order);
    }

    public function test_save_inserts_new_module_and_returns_id(): void
    {
        $db = $this->createMock(wpdb::class);
        $db->prefix = 'wp_';

        $db->expects($this->once())
            ->method('insert')
            ->with(
                'wp_bs_mf_modules',
                $this->arrayHasKey('name'),
                $this->isType('array')
            )
            ->willReturn(1);

        $db->insert_id = 7;

        $repo = new ModuleRepository($db);

        $module = new Module(
            null,
            'Termine',
            'termine',
            null,
            'active',
            0,
            '2026-03-12 10:00:00',
            '2026-03-12 10:00:00'
        );

        $id = $repo->save($module);

        $this->assertSame(7, $id);
    }
}

