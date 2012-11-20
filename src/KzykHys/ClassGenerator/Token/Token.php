<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Token;

/**
 * Represents a token
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Token
{

    public $type;
    public $value;
    public $line;

    /**
     * Constructor
     *
     * @param string  $type
     * @param string  $value
     * @param integer $line
     */
    public function __construct($type, $value, $line)
    {
        $this->type = $type;
        $this->value = $value;
        $this->line = $line;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Gets line
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Gets length of value
     *
     * @return integer
     */
    public function getLength()
    {
        return strlen($this->value);
    }

    /**
     * Returns true if type is matched
     *
     * @return boolean
     */
    public function is($type)
    {
        return $this->type == $type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

}
