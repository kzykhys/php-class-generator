<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Compiler;

use KzykHys\ClassGenerator\Builder\ClassBuilder;
use KzykHys\ClassGenerator\Builder\MethodBuilder;
use KzykHys\ClassGenerator\Builder\PropertyBuilder;

/**
 * Compiles ClassBuilder instance to PHP class
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Compiler
{

    private $options;

    /**
     * Constructor
     *
     * @param array $options Options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge(array(
            'indent_spaces' => '4',
            'lineFeed' => "\n"
        ), $options);
    }

    /**
     * Compiles a builder
     *
     * @param ClassBuilder $builder
     *
     * @return StreamWriter
     */
    public function compile(ClassBuilder $builder)
    {
        $writer = new StreamWriter($this->options);
        $writer->writeLine('<?php');
        $writer->writeLine();

        $this->compileClassDefinition($builder, $writer);
        $this->compilePropertyDefinitions($builder, $writer);
        $this->compileMethodDefinitions($builder, $writer);

        $writer->writeLine('}');

        return $writer;
    }

    /**
     * Compiles class definition
     *
     * @param ClassBuilder $builder
     * @param StreamWriter $writer
     */
    protected function compileClassDefinition(ClassBuilder $builder, StreamWriter $writer)
    {
        $namespace = null;
        $class = $this->parseClassName($builder->getClass());
        if ($class['namespace']) {
            $namespace = $class['namespace'];
            $writer->writeLineF('namespace %s;', trim(ltrim($namespace, '\\')));
            $writer->writeLine();
        }

        $docblock = $builder->getDocblock();
        if ($docblock) {
            $writer->writeLine('/**');
            foreach ($docblock as $docline) {
                $docline = trim(ltrim($docline, '>'));
                $writer->writeLine(' * ' . $docline);
            }
            $writer->writeLine(' */');
        }

        $writer->writeF('class %s', $class['classname']);

        if ($extends = $builder->getExtends()) {
            $extends = $this->parseClassName($extends);
            if ($extends['namespace'] == $namespace) {
                $writer->writeF(' extends %s', $extends['classname']);
            } else {
                $writer->writeF(' extends %s', $extends['fqcn']);
            }
        }

        $interfaces = $builder->getInterfaces();
        if (count($interfaces)) {
            $writer->write(' implements ');
            $implements = array();
            foreach ($interfaces as $interface) {
                $interface = $this->parseClassName($interface);
                if ($interface['namespace'] == $namespace) {
                    $implements[] = $interface['classname'];
                } else {
                    $implements[] = $interface['fqcn'];
                }
            }
            $writer->write(implode(', ', $implements));
        }

        $writer->writeLine(' {');
        $writer->writeLine();
    }

    /**
     * Compiles property definition
     *
     * @param ClassBuilder $builder
     * @param StreamWriter $writer
     */
    protected function compilePropertyDefinitions(ClassBuilder $builder, StreamWriter $writer)
    {
        foreach ($builder->getProperties() as $property) {
            /* @var PropertyBuilder $property */

            $writer->indent()->writeLine('/**');
            $comments = $property->getComments();
            if (count($comments)) {
                foreach ($comments as $comment) {
                    $comment = trim(ltrim($comment, '>'));
                    $writer->indent()->write(' * ')->writeLine($comment);
                }
                $writer->indent()
                    ->write(' * ')
                    ->writeLine();
            }
            $writer->indent()->write(' * @var');
            if ($type = $property->getType()) {
                $writer->write(' ' . $property->getType());
            }
            $writer->write(' $' . $property->getName())
                ->writeLine()
                ->indent()->writeLine(' */');

            $writer->indent()
                ->write($property->getVisibility())
                ->write(' ')
                ->writeLine('$'.$property->getName().';')
                ->writeLine();
        }

        $writer->writeLine();
    }

    /**
     * Compiles method definition
     *
     * @param ClassBuilder $builder
     * @param StreamWriter $writer
     */
    protected function compileMethodDefinitions(ClassBuilder $builder, StreamWriter $writer)
    {

        foreach ($builder->getMethods() as $method) {
            /* @var MethodBuilder $method */

            $writer->indent()->writeLine('/**');

            $comments = $method->getComments();
            if (count($comments)) {
                foreach ($comments as $comment) {
                    $comment = trim(ltrim($comment, '>'));
                    $writer->indent()->write(' * ')->writeLine($comment);
                }
                $writer->indent()->write(' * ')->writeLine();
            }

            $arguments = $method->getArguments();
            $argumentTypeMaxLen = 0;
            if (count($arguments)) {
                foreach ($arguments as $argument) {
                    $argumentTypeMaxLen = max($argumentTypeMaxLen, strlen($argument[1]));
                }
            }

            foreach ($arguments as $argument) {
                $writer->indent()
                    ->write(' * @param ' . sprintf('%-' . $argumentTypeMaxLen . 's', $argument[1]))
                    ->writeLine(' $' . $argument[0]);
            }

            if ($method->getType()) {
                $writer->indent()->writeLine(' *');
                $writer->indent()->writeLine(' * @return ' . $method->getType());
            }

            $writer->indent()->writeLine(' */');

            $writer->indent()
                ->write($method->getVisibility())
                ->write(' function ')
                ->write($method->getName())
                ->write('(');

            $args = array();
            foreach ($arguments as $argument) {
                $type = $this->getType($argument[1]);
                $item = '';
                if ($type['hint']) {
                    $item .= $type['name'] . ' ';
                }
                $item .= '$'.$argument[0];
                $args[] = $item;
            }

            if (count($args)) {
                $writer->write(implode(', ', $args));
            }

            $writer->writeLine(')')
                ->indent()->writeLine('{')
                ->indent()->indent()->writeLine()
                ->indent()->writeLine('}')->writeLine();
        }
    }

    /**
     * Split long class name to namespace and class name
     *
     * @param string $className
     *
     * @throws \Exception
     *
     * @return array An array includes keys below [namespace, classname, fqcn]
     */
    public function parseClassName($className)
    {
        if (!preg_match('/^\\\\?(.*?)\\\\?([a-zA-Z0-9_]+)$/', $className, $matches)) {
            throw new \Exception('Invalid class name "' . $className . '" given');
        }

        return array(
            'namespace' => $matches[1],
            'classname' => $matches[2],
            'fqcn'      => $className
        );
    }

    /**
     * Normalize PHP type
     *
     * @param string $type PHP type
     *
     * @return array An array includes key below [name, hint]
     */
    public function getType($type)
    {
        $aliases = array(
            'int'    => 'integer',
            'bool'   => 'boolean',
            'double' => 'float',
            'object' => '\\stdClass'
        );
        $default = array(
            'integer', 'boolean', 'float', '\\stdClass', 'callable', 'string', 'resource', 'void', 'mixed'
        );
        $typeHintEnabled = false;

        if (isset($aliases[$type])) {
            $type = $aliases[$type];
        }

        if (!in_array($type, $default)) {
            $typeHintEnabled = true;
        }

        return array(
            'name' => $type,
            'hint' => $typeHintEnabled
        );
    }

}
