<?php

use KzykHys\ClassGenerator\Node\Node;

/**
 * @author Kazuyuki Hayashi <hayashi@siance.co.jp>
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{

    public function testNode()
    {
        $node = new Node('N_TEST', 'DoTest');

        $this->assertEquals('N_TEST', $node->getType());
        $this->assertEquals('DoTest', $node->getValue());
        $this->assertTrue($node->is('N_TEST'));
        $this->assertFalse($node->is('N_FOO'));
        $this->assertEquals('DoTest', (string) $node);
    }

}
