<?php

use KzykHys\ClassGenerator\Token\Token;

/**
 * @author Kazuyuki Hayashi <hayashi@siance.co.jp>
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{

    public function testToken()
    {
        $token = new Token('T_STRING', 'The string value', 100);

        $this->assertEquals('T_STRING', $token->getType());
        $this->assertEquals('The string value', $token->getValue());
        $this->assertEquals(100, $token->getLine());
        $this->assertEquals(16, $token->getLength());
        $this->assertTrue($token->is('T_STRING'));
        $this->assertFalse($token->is('T_ARRAY'));
        $this->assertEquals('The string value', (string) $token);
    }

}
