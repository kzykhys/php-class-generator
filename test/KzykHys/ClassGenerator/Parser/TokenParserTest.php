<?php

use KzykHys\ClassGenerator\Lexer;
use KzykHys\ClassGenerator\Token\Token;
use KzykHys\ClassGenerator\Parser\TokenParser;

/**
 * @author Kazuyuki Hayashi <hayashi@siance.co.jp>
 */
class TokenParserTest extends \PHPUnit_Framework_TestCase
{

    public function testParse()
    {
        $lexer = new Lexer();
        $stream = $lexer->tokenize(
            /* line01: */ " ClassName < BaseClass << Traversable > The Test Class\n" .
            /* line02: */ "> Comment \n" .
            /* line03: */ "+ prop1:integer[set get is bind] > The property 1\n" .
            /* line04: */ "# prop2[set get] > The property 2\n" .
            /* line05: */ "> Comment for prop2\n" .
            /* line06: */ "- prop3:ArrayObject\n" .
            /* line07: */ "\n" .
            /* line08: */ "+ method1(name:string obj:object options:array):boolean > The method 1\n" .
            /* line09: */ "# method2(type:int) > The method 2\n" .
            /* line10: */ "- method3():IteratorAggrigate\n"
        );

        $parser = new TokenParser();
        $nodeStream = $parser->parse($stream);

        $this->assertInstanceOf('KzykHys\\ClassGenerator\\Node\\NodeStream', $nodeStream);
    }

}
