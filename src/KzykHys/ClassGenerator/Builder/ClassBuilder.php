<?php
/**
 * This software is licensed under MIT License
 * 
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Builder;

/**
 * Represents a PHP class
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class ClassBuilder
{

    private $class;
    private $extends = null;
    private $interfaces;
    private $docblock;
    private $properties;
    private $methods;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->interfaces = array();
        $this->properties = array();
        $this->methods = array();
    }

    /**
     * Sets class name
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Gets class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    public function setExtends($extends)
    {
        $this->extends = $extends;
    }

    public function getExtends()
    {
        return $this->extends;
    }

    public function addInterface($interface)
    {
        $this->interfaces[] = $interface;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    public function setDocblock($docblock)
    {
        $this->docblock = $docblock;
    }

    public function getDocblock()
    {
        return $this->docblock;
    }

    public function addProperty(PropertyBuilder $builder)
    {
        $this->properties[] = $builder;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function addMethod(MethodBuilder $builder)
    {
        $this->methods[] = $builder;
    }

    public function getMethods()
    {
        return $this->methods;
    }

}
