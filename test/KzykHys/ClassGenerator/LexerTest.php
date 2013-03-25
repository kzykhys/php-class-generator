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
            /* line01: */ "\\Node\\Node < Node\\Node << Node \\Node\\Node\\Node > COMMENT\n" .
            /* line02: */ "> TOKEN INSIDE COMMENT < << : () [] \n" .
            /* line03: */ "+ PUBLIC:TYPE[set get is bind] > COMMENT\n" .
            /* line04: */ "# PROTECTED[set get] > COMMENT\n" .
            /* line05: */ "> COMMENT\n" .
            /* line06: */ "- PRIVATE:TYPE\n" .
            /* line07: */ "\n" .
            /* line08: */ "+ PUBLIC(ARG:TYPE ARG:TYPE):TYPE > COMMENT\n" .
            /* line09: */ "# PROTECTED(ARG:TYPE) > COMMENT\n" .
            /* line10: */ "- PRIVATE():TYPE\n"
        );

        $className = 'KzykHys\\ClassGenerator\\Token\\Token';
        $tokenList = array(
            1 => array(
                Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP, Lexer::TOKEN_EXTD, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE,
                Lexer::TOKEN_WTSP, Lexer::TOKEN_IMPL, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP,
                Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP, Lexer::TOKEN_CMNT, Lexer::TOKEN_EOL
            ),
            2 => array(
                Lexer::TOKEN_CMNT, Lexer::TOKEN_EOL
            ),
            3 => array(
                Lexer::TOKEN_PUBL, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_TYPE, Lexer::TOKEN_NODE,
                Lexer::TOKEN_BRCO, Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP,
                Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_BRCC, Lexer::TOKEN_WTSP,
                Lexer::TOKEN_CMNT, Lexer::TOKEN_EOL
            ),
            4 => array(
                Lexer::TOKEN_PROT, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_BRCO, Lexer::TOKEN_NODE,
                Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_BRCC, Lexer::TOKEN_WTSP, Lexer::TOKEN_CMNT,
                Lexer::TOKEN_EOL
            ),
            5 => array(
                Lexer::TOKEN_CMNT, Lexer::TOKEN_EOL
            ),
            6 => array(
                Lexer::TOKEN_PRIV, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_TYPE, Lexer::TOKEN_NODE,
                Lexer::TOKEN_EOL
            ),
            7 => array(
                Lexer::TOKEN_EOL
            ),
            8 => array(
                Lexer::TOKEN_PUBL, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_METO, Lexer::TOKEN_NODE,
                Lexer::TOKEN_TYPE, Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_TYPE,
                Lexer::TOKEN_NODE, Lexer::TOKEN_METC, Lexer::TOKEN_TYPE, Lexer::TOKEN_NODE, Lexer::TOKEN_WTSP,
                Lexer::TOKEN_CMNT, Lexer::TOKEN_EOL
            ),
            9 => array(
                Lexer::TOKEN_PROT, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_METO, Lexer::TOKEN_NODE,
                Lexer::TOKEN_TYPE, Lexer::TOKEN_NODE, Lexer::TOKEN_METC, Lexer::TOKEN_WTSP, Lexer::TOKEN_CMNT,
                Lexer::TOKEN_EOL
            ),
            10 => array(
                Lexer::TOKEN_PRIV, Lexer::TOKEN_WTSP, Lexer::TOKEN_NODE, Lexer::TOKEN_METO, Lexer::TOKEN_METC,
                Lexer::TOKEN_TYPE, Lexer::TOKEN_NODE, Lexer::TOKEN_EOL
            )
        );

        foreach ($tokenList as $line => $tokens) {
            foreach ($tokens as $key => $value) {
                $this->assertInstanceOf($className, $stream->expect($value));
            }
        }

        $this->assertInstanceOf($className, $stream->expect(Lexer::TOKEN_EOL));
        $this->assertTrue($stream->is(Lexer::TOKEN_EOF));
    }

}
