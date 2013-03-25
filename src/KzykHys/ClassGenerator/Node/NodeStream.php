<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Node;

/**
 * Help iteration of nodes
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class NodeStream implements \IteratorAggregate
{

    private $nodes;
    private $position = 0;

    /**
     * Constructor
     *
     * @param array $nodes An array of Node objects
     */
    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * Returns current node
     *
     * @return Node
     */
    public function current()
    {
        return $this->nodes[$this->position];
    }

    /**
     * Returns next node
     *
     * @return Node
     */
    public function next()
    {
        return $this->nodes[++$this->position];
    }

    /**
     * Returns true if type is matched
     *
     * @param string $type Type of node
     *
     * @return boolean
     */
    public function is($type)
    {
        $node = $this->current();

        return $this->compareType($node, $type);
    }

    /**
     * Returns a Node if type is matched, otherwise fails the parser
     *
     * @param string $type Type of node
     *
     * @throws \LogicException
     *
     * @return Node
     */
    public function expect($type)
    {
        $node = $this->current();
        if (!$this->is($type)) {
            if (is_array($type)) {
                $type = implode(' or ', $type);
            }
            $actual = sprintf('<%s:%s>', $node->getType(), $node->getValue());
            throw new \LogicException(sprintf('Syntax Error: %s expected %s given', $type, $actual));
        }
        $this->next();

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->nodes);
    }

    /**
     * @internal
     */
    protected function compareType(Node $node, $type)
    {
        if (!is_array($type)) {
            $type = array($type);
        }

        foreach ($type as $name) {
            if ($node->is($name)) {
                return true;
            }
        }

        return false;
    }

}
