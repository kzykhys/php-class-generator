<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Parser;

use KzykHys\ClassGenerator\Node\NodeStream;
use KzykHys\ClassGenerator\Builder\ClassBuilder;
use KzykHys\ClassGenerator\Builder\PropertyBuilder;
use KzykHys\ClassGenerator\Builder\MethodBuilder;

/**
 * Parses nodes and build PHP class
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class NodeParser
{

    const STAT_NONE = 1;
    const STAT_BODY = 2;

    private $status = self::STAT_NONE;

    /**
     * Parses nodes and build PHP class
     *
     * @param NodeStream $stream
     *
     * @return ClassBuilder
     */
    public function parse(NodeStream $stream)
    {
        $builder = new ClassBuilder();

        while (!$stream->is(TokenParser::NODE_EON)) {
            switch ($this->status) {
                case self::STAT_NONE:
                    $this->parseClass($builder, $stream);
                    break;
                case self::STAT_BODY:
                    $methodNodes = array(
                        TokenParser::NODE_PUBLIC_METHOD,
                        TokenParser::NODE_PROTECTED_METHOD,
                        TokenParser::NODE_PRIVATE_METHOD
                    );
                    if ($stream->is($methodNodes)) {
                        $this->parseMethod($builder, $stream);
                    } else {
                        $this->parseProperty($builder, $stream);
                    }
                    break;
                default:
                    $stream->next();
                    break;
            }
        }

        return $builder;
    }

    /**
     * Parses class nodes
     *
     * @param ClassBuilder $builder ClassBuilder object
     * @param NodeStream   $stream  NodeStream object
     */
    protected function parseClass(ClassBuilder $builder, NodeStream $stream)
    {
        $node = $stream->expect(TokenParser::NODE_CLASS);
        $docblock = array();
        $builder->setClass($node->getValue());
        if ($stream->is(TokenParser::NODE_EXTENDS)) {
            $builder->setExtends($stream->current()->getValue());
            $stream->next();
        }
        while ($stream->is(TokenParser::NODE_IMPLEMENTS)) {
            $builder->addInterface($stream->current()->getValue());
            $stream->next();
        }
        while ($stream->is(TokenParser::NODE_CLASS_COMMENT)) {
            $docblock[] = $stream->current()->getValue();
            $stream->next();
        }

        if (count($docblock) > 0) {
            $builder->setDocblock($docblock);
        }

        $this->status = self::STAT_BODY;
    }

    /**
     * Parses property nodes
     *
     * @param ClassBuilder $builder ClassBuilder object
     * @param NodeStream   $stream  NodeStream object
     */
    protected function parseProperty(ClassBuilder $classBuilder, NodeStream $stream)
    {
        $builder = new PropertyBuilder();
        $comments = array();

        $propertyNodes = array(
            TokenParser::NODE_PUBLIC_PROPERTY => 'public',
            TokenParser::NODE_PROTECTED_PROPERTY => 'protected',
            TokenParser::NODE_PRIVATE_PROPERTY => 'private'
        );

        $node = $stream->expect(array_keys($propertyNodes));
        $builder->setName($node->getValue());
        $builder->setVisibility($propertyNodes[$node->getType()]);

        if ($stream->is(TokenParser::NODE_PROPERTY_TYPE)) {
            $builder->setType($stream->current()->getValue());
            $stream->next();
        }

        while ($stream->is(TokenParser::NODE_ACCESSOR)) {
            $builder->addAccessor($stream->current()->getValue());
            $stream->next();
        }

        while ($stream->is(TokenParser::NODE_PROPERTY_COMMENT)) {
            $comments[] = $stream->current()->getValue();
            $stream->next();
        }

        if (count($comments) > 0) {
            $builder->setComments($comments);
        }

        $classBuilder->addProperty($builder);
    }

    /**
     * Parses method nodes
     *
     * @param ClassBuilder $builder ClassBuilder object
     * @param NodeStream   $stream  NodeStream object
     */
    protected function parseMethod(ClassBuilder $classBuilder, NodeStream $stream)
    {
        $builder = new MethodBuilder();
        $comments = array();
        $methodNodes = array(
            TokenParser::NODE_PUBLIC_METHOD => 'public',
            TokenParser::NODE_PROTECTED_METHOD => 'protected',
            TokenParser::NODE_PRIVATE_METHOD => 'private'
        );

        $node = $stream->expect(array_keys($methodNodes));
        $builder->setName($node->getValue());
        $builder->setVisibility($methodNodes[$node->getType()]);

        while ($stream->is(TokenParser::NODE_ARGUMENT)) {
            $argument = array();
            $argument[] = $stream->current()->getValue();
            $stream->next();
            if ($stream->is(TokenParser::NODE_ARGUMENT_TYPE)) {
                $argument[] = $stream->current()->getValue();
                $stream->next();
            } else {
                $argument[] = 'mixed';
            }
            $builder->addArgument($argument);
        }

        if ($stream->is(TokenParser::NODE_RETURN_TYPE)) {
            $builder->setType($stream->current()->getValue());
            $stream->next();
        }

        while ($stream->is(TokenParser::NODE_METHOD_COMMENT)) {
            $comments[] = $stream->current()->getValue();
            $stream->next();
        }

        if (count($comments) > 0) {
            $builder->setComments($comments);
        }

        $classBuilder->addMethod($builder);
    }

}
