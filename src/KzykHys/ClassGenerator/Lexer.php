<?php
/**
 * This software is licensed under MIT License
 * 
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator;

use KzykHys\ClassGenerator\Token\Token;
use KzykHys\ClassGenerator\Token\TokenStream;

/**
 * Lexical Analyzer for tokenize *.pcd contents
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Lexer
{

    const TOKEN_WTSP = 'T_WHITESPACE';
    const TOKEN_CMNT = 'T_COMMENT';
    const TOKEN_NODE = 'T_NODE';
    const TOKEN_EXTD = 'T_OPERATOR_EXTENDS';
    const TOKEN_IMPL = 'T_OPERATOR_IMPLEMENTS';
    const TOKEN_PUBL = 'T_OPERATOR_PUBLIC';
    const TOKEN_PROT = 'T_OPERATOR_PROTECTED';
    const TOKEN_PRIV = 'T_OPERATOR_PRIVATE';
    const TOKEN_TYPE = 'T_OPERATOR_TYPE';
    const TOKEN_BRCO = 'T_PROPERTY_OPEN';
    const TOKEN_BRCC = 'T_PROPERTY_CLOSE';
    const TOKEN_METO = 'T_METHOD_OPEN';
    const TOKEN_METC = 'T_METHOD_CLOSE';
    const TOKEN_EOL  = 'T_EOL';
    const TOKEN_EOF  = 'T_EOF';

    /**
     * @var array $patterns
     */
    protected $patterns;

    /**
     * @var array $tokens
     */
    protected $tokens;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->patterns = array(
            self::TOKEN_WTSP => '/^(\s+)/',
            self::TOKEN_CMNT => '/^(>.*)$/',
            self::TOKEN_EXTD => '/^(<)[^<]/',
            self::TOKEN_IMPL => '/^(<<)/',
            self::TOKEN_PUBL => '/^(\+)/',
            self::TOKEN_PROT => '/^(#)/',
            self::TOKEN_PRIV => '/^(-)/',
            self::TOKEN_TYPE => '/^(:)/',
            self::TOKEN_BRCO => '/^(\[)/',
            self::TOKEN_BRCC => '/^(\])/',
            self::TOKEN_METO => '/^(\()/',
            self::TOKEN_METC => '/^(\))/',
            self::TOKEN_NODE => '/^([^\s.:<>\(\)\[\]#\-\+]+)/',
        );
    }

    /**
     * Tokenize the code
     *
     * @param string $code The content of pcd file
     *
     * @return TokenStream
     */
    public function tokenize($code)
    {
        $this->tokens = array();
        $lines = preg_split('/\r?\n/', $code);

        foreach ($lines as $number => $fragment) {
            $this->processLine($number + 1, $fragment);
            $this->tokens[] = new Token(self::TOKEN_EOL, null, $number + 1);
        }
        $this->tokens[] = new Token(self::TOKEN_EOF, null, $number + 1);

        return new TokenStream($this->tokens);
    }

    /**
     * Parses a line
     *
     * @param integer $linenum  Number of line
     * @param string  $fragment A piece of code
     */
    protected function processLine($linenum, $fragment)
    {
        $code   = $fragment;
        $offset = 0;
        while ($offset < strlen($fragment)) {
            if (($token = $this->match(substr($fragment, $offset), $linenum)) === false) {
                throw new \Exception('Parse error at line ' . $linenum . ' offset ' . $offset);
            }
            $this->tokens[] = $token;
            $offset += $token->getLength();
        }
    }

    /**
     * Returns matched token or false
     *
     * @param string  $code
     * @param integer $line
     *
     * @return Token|boolean Token object otherwise false
     */
    protected function match($code, $line)
    {
        foreach ($this->patterns as $token => $pattern) {
            if (!preg_match($pattern, $code, $matches)) {
                continue;
            }

            return new Token($token, $matches[1], $line);
        }

        return false;
    }

}
