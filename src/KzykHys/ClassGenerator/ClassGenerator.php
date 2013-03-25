<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator;

use KzykHys\ClassGenerator\Compiler\StreamWriter;
use KzykHys\ClassGenerator\Lexer;
use KzykHys\ClassGenerator\Parser\TokenParser;
use KzykHys\ClassGenerator\Parser\NodeParser;
use KzykHys\ClassGenerator\Compiler\Compiler;

/**
 * Generates PHP classes from plain text document (*.pcg)
 *
 * @author    Kazuyuki Hayashi <hayashi@valnur.net>
 * @copyright Copyright (c) 2012, Kazuyuki Hayashi
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
class ClassGenerator
{

    const VERSION = '1.0.0';

    private $options;
    private $classBuilder;
    private $streamWriter;

    /**
     * Constructor
     *
     * @param array $options Options
     */
    public function __construct($options = array())
    {
        $this->options = array_merge(array(
            'indent_spaces' => '4',
            'lineFeed' => "\n"
        ), $options);
    }

    /**
     * Generate a PHP class from .pcg content
     *
     * @param string $document
     *
     * @return ClassGenerator
     */
    public function generate($document)
    {
        $lexer = new Lexer();
        $tokenStream = $lexer->tokenize($document);
        $tokenParser = new TokenParser();
        $nodeStream = $tokenParser->parse($tokenStream);
        $nodeParser = new NodeParser();
        $this->classBuilder = $nodeParser->parse($nodeStream);
        $compiler = new Compiler($this->options);
        $this->streamWriter = $compiler->compile($this->classBuilder);

        return $this;
    }

    /**
     * Returns ClassBuilder
     *
     * @return \KzykHys\ClassGenerator\Builder\ClassBuilder
     */
    public function getClassBuilder()
    {
        return $this->classBuilder;
    }

    /**
     * Returns StreamWriter
     *
     * @return StreamWriter
     */
    public function getStreamWriter()
    {
        return $this->streamWriter;
    }

    /**
     * Returns PHP class as a string
     *
     * @return string
     */
    public function getString()
    {
        return (string) $this->streamWriter;
    }

    /**
     * Writes PHP class as a file
     */
    public function write($filename)
    {
        $this->streamWriter->save($filename);
    }

}
