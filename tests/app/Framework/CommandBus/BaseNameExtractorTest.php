<?php

namespace IntelligenceFusion\Actor\Framework\CommandBus;

use PHPUnit\Framework\TestCase;

class BaseNameExtractorTest extends TestCase
{
    public function test_extract_returns_a_string_respresentation_of_a_class_name()
    {
        $name = (new BaseNameExtractor())->extract($this);

        $this->assertEquals('BaseNameExtractorTest', $name);
    }
}
