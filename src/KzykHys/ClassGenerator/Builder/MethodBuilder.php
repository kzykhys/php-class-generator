<?php
/**
 * This software is licensed under MIT License
 * 
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Builder;

/**
 * Represents a method
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class MethodBuilder
{

    private $name;
    private $visibility;
    private $type;
    private $arguments;
    private $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->arguments = array();
    }

    /**
     * Sets name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets visibility
     *
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * Gets visibility
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Sets type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * Adds an argument
     *
     * @param array $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * Gets arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Sets comments
     *
     * @param array $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * Gets comments
     *
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

}
