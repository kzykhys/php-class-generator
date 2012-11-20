<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Builder;

/**
 * Represents a property
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class PropertyBuilder
{

    private $name;
    private $visibility;
    private $type;
    private $getters;
    private $setters;
    private $comments;

    private $accessorDefaults = array(
        'getter' => array('get', 'is'),
        'setter' => array('set', 'bind')
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->getters = array();
        $this->setters = array();
        $this->comments = array();
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
     * Adds getter
     *
     * @param string $getter
     */
    public function addGetter($getter)
    {
        $this->getters[] = $getter;
    }

    /**
     * Adds setter
     *
     * @param string $setter
     */
    public function addSetter($setter)
    {
        $this->setters[] = $setter;
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
     * @return array $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Adds accessor
     *
     * @param string $accessor
     */
    public function addAccessor($accessor)
    {
        if (in_array($accessor, $this->accessorDefaults['getter'])) {
            $this->addGetter($accessor);
        } elseif (in_array($accessor, $this->accessorDefaults['setter'])) {
            $this->addSetter($accessor);
        } else {
            throw new \Exception(sprintf('Unexpected property accessor "%s" available accessors are [get,is,bind,set]', $accessor));
        }

    }

}
