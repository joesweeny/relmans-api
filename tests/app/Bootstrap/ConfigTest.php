<?php

namespace Relmans\Bootstrap;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_get_returns_an_item_from_the_internal_config_array()
    {
        $items = [
            'app' => [
                'env' => 'development'
            ]
        ];

        $config = new Config($items);

        $this->assertEquals(['env' => 'development'], $config->get('app'));
    }

    public function test_get_returns_an_item_from_the_internal_config_array_using_dot_notation()
    {
        $items = [
            'app' => [
                'env' => 'development'
            ],
            'log' => [
                'external' => [
                    'enabled' => true
                ]
            ]
        ];

        $config = new Config($items);

        $this->assertEquals('development', $config->get('app.env'));
        $this->assertTrue($config->get('log.external.enabled'));
    }

    public function test_set_adds_a_new_items_to_the_internal_config_array()
    {
        $items = [
            'app' => [
                'env' => 'development'
            ],
        ];

        $config = new Config($items);

        $this->assertFalse($config->has('app.debug'));

        $config->set('app.debug', true);

        $this->assertTrue($config->has('app.debug'));
    }

    public function test_has_returns_true_if_internal_array_contains_value()
    {
        $items = [
            'app' => [
                'env' => 'development'
            ],
        ];

        $config = new Config($items);

        $this->assertTrue($config->has('app'));
        $this->assertTrue($config->has('app.env'));
    }

    public function test_has_returns_false_if_internal_array_does_not_contain_value()
    {
        $items = [
            'app' => [
                'env' => 'development'
            ],
        ];

        $config = new Config($items);

        $this->assertFalse($config->has('log'));
    }
}
