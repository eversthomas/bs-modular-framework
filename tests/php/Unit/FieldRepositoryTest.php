<?php

declare(strict_types=1);

namespace BS\ModularFramework\Tests\Unit;

use BS\ModularFramework\Data\FieldRepository;
use BS\ModularFramework\Domain\FieldDefinition;
use PHPUnit\Framework\TestCase;
use wpdb;

class FieldRepositoryTest extends TestCase
{
    public function test_find_by_id_maps_row_to_field_definition(): void
    {
        $db = $this->createMock(wpdb::class);
        $db->prefix = 'wp_';

        $row               = new \stdClass();
        $row->id           = 3;
        $row->module_id    = 1;
        $row->label        = 'E-Mail';
        $row->field_key    = 'email';
        $row->field_type   = 'email';
        $row->is_required  = 1;
        $row->config_json  = '{"placeholder":"Ihre E-Mail"}';
        $row->sort_order   = 1;
        $row->status       = 'active';
        $row->created_at   = '2026-03-12 10:00:00';
        $row->updated_at   = '2026-03-12 10:00:00';

        $db->method('prepare')->willReturnCallback(
            static fn (string $query, int $id): string => sprintf($query, $id)
        );
        $db->method('get_row')->willReturn($row);

        $repo  = new FieldRepository($db);
        $field = $repo->find_by_id(3);

        $this->assertInstanceOf(FieldDefinition::class, $field);
        $this->assertSame(3, $field->id);
        $this->assertSame(1, $field->module_id);
        $this->assertSame('E-Mail', $field->label);
        $this->assertSame('email', $field->field_key);
        $this->assertSame('email', $field->field_type);
        $this->assertTrue($field->is_required);
        $this->assertIsArray($field->config);
        $this->assertSame('Ihre E-Mail', $field->config['placeholder']);
    }

    public function test_save_inserts_new_field_and_returns_id(): void
    {
        $db = $this->createMock(wpdb::class);
        $db->prefix = 'wp_';

        $db->expects($this->once())
            ->method('insert')
            ->with(
                'wp_bs_mf_fields',
                $this->arrayHasKey('module_id'),
                $this->isType('array')
            )
            ->willReturn(1);

        $db->insert_id = 9;

        $repo = new FieldRepository($db);

        $field = new FieldDefinition(
            null,
            1,
            'Titel',
            'title',
            'text',
            true,
            array('placeholder' => 'Titel eingeben'),
            0,
            'active',
            '2026-03-12 10:00:00',
            '2026-03-12 10:00:00'
        );

        $id = $repo->save($field);

        $this->assertSame(9, $id);
    }
}

