<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Node;

/**
 * Represents a node
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Node
{

    public $type;
    public $value;

    /**
     * Construct
     *
     * @param string $type  Node type
     * @param string $value Node value
     */
    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
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
