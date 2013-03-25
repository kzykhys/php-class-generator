<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Token;

/**
 * Help iteration of tokens
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class TokenStream implements \IteratorAggregate
{

    private $tokens;
    private $position = 0;

    /**
     * Construct
     *
     * @param array $tokens
     */
    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Returns current token
     *
     * @return Token
     */
    public function current()
    {
        return $this->tokens[$this->position];
    }

    /**
     * Returns next token
     *
     * @return Token
     */
    public function next()
    {
        return $this->tokens[++$this->position];
    }

    /**
     * Skips current node if type is matched
     *
     * @param string $type
     *
     * @return TokenStream
     */
    public function skipType($type)
    {
        while ($this->is($type)) {
            $this->next();
        }

        return $this;
    }

    /**
     * Returns true if type is matched
     *
     * @param string $type Type of token
     *
     * @return boolean
     */
    public function is($type)
    {
        $token = $this->current();

        return $this->compareType($token, $type);
    }

    /**
     * Returns a Token if type is matched, otherwise fails the parser
     *
     * @param string $type Type of token
     *
     * @throws \LogicException
     *
     * @return Token
     */
    public function expect($type)
    {
        $token = $this->current();
        if (!$this->is($type)) {
            if (is_array($type)) {
                $type = implode(' or ', $type);
            }
            $actual = sprintf('<%s:%s>', $token->getType(), $token->getValue());
            throw new \LogicException(sprintf('Syntax Error: %s expected %s given at line %d', $type, $actual, $token->getLine()));
        }
        $this->next();

        return $token;
    }

    /**
     * Returns an array of tokens
     *
     * @return array
     */
    public function toArray()
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->tokens);
    }

    /**
     * @internal
     */
    protected function compareType(Token $token, $type)
    {
        if (!is_array($type)) {
            $type = array($type);
        }

        foreach ($type as $name) {
            if ($token->is($name)) {
                return true;
            }
        }

        return false;
    }

}
