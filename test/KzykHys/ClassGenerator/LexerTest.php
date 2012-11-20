<?php

use KzykHys\ClassGenerator\Lexer;

/**
 * @author Kazuyuki Hayashi <hayashi@siance.co.jp>
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testTokenizeReturnsStream()
    {
        $lexer = new Lexer();
        $stream = $lexer->tokenize('Class < Extends << Implements');
        $this->assertInstanceOf('KzykHys\\ClassGenerator\\Token\\TokenStream', $stream);
    }
    
    public function testTokenizeAllTokenType()
    {
        $lexer = new Lexer();
        $stream = $lexer->tokenize(
<<<EOF
\Node\Node < Node\Node << Node \Node\Node\Node > COMMENT
> TOKEN INSIDE COMMENT < << : () [] 
+ PUBLIC:TYPE[set get is bind] > COMMENT
# PROTECTED[set get] > COMMENT
> COMMENT
- PRIVATE:TYPE

+ PUBLIC(ARG:TYPE ARG:TYPE):TYPE > COMMENT
# PROTECTED(ARG:TYPE) > COMMENT
- PRIVATE():TYPE
EOF
        );
        
        $className = 'KzykHys\\ClassGenerator\\Token\\Token';
        
        // line 1
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_NODE)); // \Node\Node
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_WTSP)); // 
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_EXTD)); // <
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_WTSP)); // 
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_NODE)); // Node\Node
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_WTSP)); // 
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_IMPL)); // <<
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_WTSP)); // 
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_NODE)); // Node
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_WTSP)); // 
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_NODE)); // \Node\Node\Node
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_WTSP)); //
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_CMNT)); // > COMMENT
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_EOL));
        
        // line 2
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_CMNT)); // > COMMENT
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_EOL));
        
        // line 3
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_PUBL)); // +
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_WTSP)); // 
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_NODE)); // PUBLIC
        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_TYPE)); // :
    }
    
}