<?php
/**
 * This software is licensed under MIT License
 * 
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Parser;

use KzykHys\ClassGenerator\Lexer;
use KzykHys\ClassGenerator\Token\TokenStream;
use KzykHys\ClassGenerator\Node\Node;
use KzykHys\ClassGenerator\Node\NodeStream;

/**
 * Parses tokens and build nodes
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class TokenParser
{

    const NODE_CLASS              = 'N_CLASS';
    const NODE_EXTENDS            = 'N_EXTENDS';
    const NODE_IMPLEMENTS         = 'N_IMPLEMENTS';
    const NODE_CLASS_COMMENT      = 'N_CLASS_COMMENT';
    const NODE_PUBLIC_PROPERTY    = 'N_PUBLIC_PROPERTY';
    const NODE_PROTECTED_PROPERTY = 'N_PROTECTED_PROPERTY';
    const NODE_PRIVATE_PROPERTY   = 'N_PRIVATE_PROPERTY';
    const NODE_ACCESSOR           = 'N_ACCESSOR';
    const NODE_PROPERTY_TYPE      = 'N_PROPERTY_TYPE';
    const NODE_PROPERTY_COMMENT   = 'N_PROPERTY_COMMENT';
    const NODE_PUBLIC_METHOD      = 'N_PUBLIC_METHOD';
    const NODE_PROTECTED_METHOD   = 'N_PROTECTED_METHOD';
    const NODE_PRIVATE_METHOD     = 'N_PRIVATE_METHOD';
    const NODE_ARGUMENT           = 'N_ARGUMENT';
    const NODE_ARGUMENT_TYPE      = 'N_ARGUMENT_TYPE';
    const NODE_METHOD_COMMENT     = 'N_METHOD_COMMENT';
    const NODE_RETURN_TYPE        = 'N_RETURN_TYPE';
    const NODE_EON                = 'N_EON';

    const STAT_NONE          = 1;
    const STAT_CLASS_DEFINED = 2;

    /**
     * @var integer $status
     */
    private $status = self::STAT_NONE;

    /**
     * @var array $nodes
     */
    private $nodes = array();

    /**
     * Parses tokens and build nodes
     *
     * @param TokenStream $stream
     *
     * @return NodeStream
     */
    public function parse(TokenStream $stream)
    {
        while (!$stream->is(Lexer::TOKEN_EOF)) {
            if ($stream->is(Lexer::TOKEN_WTSP)) {
                $stream->next();
                continue;
            }

            switch ($this->status) {
                case self::STAT_NONE:
                    $this->parseClassDefinition($stream);
                    break;
                case self::STAT_CLASS_DEFINED;
                    $this->parseBody($stream);
                    break;
                default:
                    $stream->next();
                    break;
            }
        }
        $this->nodes[] = new Node(self::NODE_EON, null);

        return new NodeStream($this->nodes);
    }

    /**
     * Skips white spaces and line delimiter
     *
     * @param TokenStream $stream
     *
     * @return TokenStream
     */
    protected function skip(TokenStream $stream)
    {
        return $stream->skipType(array(Lexer::TOKEN_EOL, Lexer::TOKEN_WTSP));
    }

    /**
     * Parses class tokens
     *
     * @param TokenStream $stream
     */
    protected function parseClassDefinition(TokenStream $stream)
    {
        $token = $stream->expect(Lexer::TOKEN_NODE);
        $this->nodes[] = new Node(self::NODE_CLASS, $token->getValue());
        if ($this->skip($stream)->is(Lexer::TOKEN_EXTD)) {
            $stream->next();
            $token = $stream->skipType(Lexer::TOKEN_WTSP)->expect(Lexer::TOKEN_NODE);
            $this->nodes[] = new Node(self::NODE_EXTENDS, $token->getValue());
        }
        if ($this->skip($stream)->is(Lexer::TOKEN_IMPL)) {
            $stream->next();
            while (!$stream->is(Lexer::TOKEN_EOL)) {
                if ($stream->is(Lexer::TOKEN_NODE)) {
                    $this->nodes[] = new Node(self::NODE_IMPLEMENTS, $stream->current()->getValue());
                }
                $stream->next();
            }
        }
        $this->parseComment($stream, self::NODE_CLASS_COMMENT);
        $this->status = self::STAT_CLASS_DEFINED;
    }

    /**
     * Parses field and method tokens
     *
     * @param TokenStream $stream
     */
    protected function parseBody(TokenStream $stream)
    {
        $visibility = $this->skip($stream)->expect(array(Lexer::TOKEN_PUBL, Lexer::TOKEN_PROT, Lexer::TOKEN_PRIV));
        $name = $this->skip($stream)->expect(array(Lexer::TOKEN_NODE));

        if ($this->skip($stream)->is(Lexer::TOKEN_METO)) {
            $this->parseMethod($stream, $visibility, $name);
        } else {
            $this->parseProperty($stream, $visibility, $name);
        }
    }

    /**
     * Parses property tokens
     *
     * @param TokenStream $stream     TokenStream object
     * @param Token       $visibility Token <TOKEN_PUBL> or <TOKEN_PROT> or <TOKEN_PRIV>
     * @param Token       $name       Token <TOKEN_NODE>
     */
    protected function parseProperty(TokenStream $stream, $visibility, $name)
    {
        switch ($visibility->getValue()) {
            case '+':
                $this->nodes[] = new Node(self::NODE_PUBLIC_PROPERTY, $name->getValue());
                break;
            case '#':
                $this->nodes[] = new Node(self::NODE_PROTECTED_PROPERTY, $name->getValue());
                break;
            case '-':
                $this->nodes[] = new Node(self::NODE_PRIVATE_PROPERTY, $name->getValue());
                break;
        }
        if ($this->skip($stream)->is(Lexer::TOKEN_TYPE)) {
            $stream->next();
            $token = $this->skip($stream)->expect(Lexer::TOKEN_NODE);
            $this->nodes[] = new Node(self::NODE_PROPERTY_TYPE, $token->getValue());
        }

        if ($this->skip($stream)->is(Lexer::TOKEN_BRCO)) {
            $stream->next();
            while (!$this->skip($stream)->is(Lexer::TOKEN_BRCC)) {
                if ($this->skip($stream)->is(array(Lexer::TOKEN_PUBL, Lexer::TOKEN_PROT, Lexer::TOKEN_PRIV))) {
                    throw new \Exception('Unexpected token: ' . $stream->current()->getType());
                }

                $token = $stream->skipType(array(Lexer::TOKEN_EOL, Lexer::TOKEN_WTSP))->expect(Lexer::TOKEN_NODE);
                $this->nodes[] = new Node(self::NODE_ACCESSOR, $token->getValue());
            }
            $stream->next();
        }

        $this->parseComment($stream, self::NODE_PROPERTY_COMMENT);
    }

    /**
     * Parses method tokens
     *
     * @param TokenStream $stream     TokenStream object
     * @param Token       $visibility Token <TOKEN_PUBL> or <TOKEN_PROT> or <TOKEN_PRIV>
     * @param Token       $name       Token <TOKEN_NODE>
     */
    protected function parseMethod(TokenStream $stream, $visibility, $name)
    {
        switch ($visibility->getValue()) {
            case '+':
                $this->nodes[] = new Node(self::NODE_PUBLIC_METHOD, $name->getValue());
                break;
            case '#':
                $this->nodes[] = new Node(self::NODE_PROTECTED_METHOD, $name->getValue());
                break;
            case '-':
                $this->nodes[] = new Node(self::NODE_PRIVATE_METHOD, $name->getValue());
                break;
        }

        $stream->next();
        while (!$this->skip($stream)->is(Lexer::TOKEN_METC)) {
            $token = $stream->expect(Lexer::TOKEN_NODE);
            $this->nodes[] = new Node(self::NODE_ARGUMENT, $token->getValue());
            if ($this->skip($stream)->is(Lexer::TOKEN_TYPE)) {
                $stream->next();
                $token = $stream->expect(Lexer::TOKEN_NODE);
                $this->nodes[] = new Node(self::NODE_ARGUMENT_TYPE, $token->getValue());
            }
        }

        $stream->next();
        if ($this->skip($stream)->is(Lexer::TOKEN_TYPE)) {
            $stream->next();
            $token = $this->skip($stream)->expect(Lexer::TOKEN_NODE);
            $this->nodes[] = new Node(self::NODE_RETURN_TYPE, $token->getValue());
        }

        $this->parseComment($stream, self::NODE_METHOD_COMMENT);
    }

    /**
     * Parses comment tokens
     *
     * @param TokenStream $stream TokenStream object
     * @param string      $type   Type of Node
     */
    protected function parseComment(TokenStream $stream, $type)
    {
        while ($stream->skipType(array(Lexer::TOKEN_EOL, Lexer::TOKEN_WTSP))->is(Lexer::TOKEN_CMNT)) {
            $this->nodes[] = new Node($type, $stream->current()->getValue());
            $stream->next();
        }
    }

}
