<?php

use KzykHys\ClassGenerator\Token\Token;
use KzykHys\ClassGenerator\Token\TokenStream;

/**
 * @author Kazuyuki Hayashi <hayashi@siance.co.jp>
 */
class TokenStreamTest extends \PHPUnit_Framework_TestCase
{

    private $tokens;
    private $stream;

    public function setUp()
    {
        $this->tokens = array(
            new Token('T_FUNCTION', 'function', 1),
            new Token('T_WHITESPACE', ' ', 1),
            new Token('T_STRING', 'getValue', 1),
            new Token('T_OPEN_BRACE', '(', 1),
            new Token('T_CLOSE_BRACE', ')', 1),
        );
        $this->stream = new TokenStream($this->tokens);
    }

    public function testCurrent()
    {
        $this->assertTrue($this->stream->current()->is('T_FUNCTION'));
    }

    public function testNext()
    {
        $this->assertTrue($this->stream->next()->is('T_WHITESPACE'));
    }

    public function testSkip()
    {
        $this->assertTrue($this->stream->skipType('T_FUNCTION')->is('T_WHITESPACE'));
    }

    public function testIs()
    {
        $this->assertTrue($this->stream->is('T_FUNCTION'));
        $this->assertFalse($this->stream->is('T_WHITESPACE'));
    }

    public function testExpect()
    {
        $this->assertInstanceOf('KzykHys\\ClassGenerator\\Token\\Token', $this->stream->expect('T_FUNCTION'));
        $this->assertInstanceOf('KzykHys\\ClassGenerator\\Token\\Token', $this->stream->expect(array('T_WHITESPACE', 'T_TOKEN')));
    }

    public function testExpectFails()
    {
        try {
            $this->stream->expect('T_INVALID_TYPE');
            $this->fail('Exception expected');
        } catch (\Exception $e) {}

        try {
            $this->stream->expect(array('T_INVALID_TYPE', 'T_FOO_BAR'));
            $this->fail('Exception expected');
        } catch (\Exception $e) {}
    }

    public function testToArray()
    {
        $this->assertEquals($this->tokens, $this->stream->toArray());
    }

    public function testIteration()
    {
        $tokens = array();
        foreach ($this->stream as $token) {
            $tokens[] = $token;
        }

        $this->assertEquals($this->tokens, $tokens);
    }

}
