<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use KzykHys\ClassGenerator\ClassGenerator;
use KzykHys\ClassGenerator\Compiler\Compiler;

/**
 * Generates the PHP Classes
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class GenerateCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generates the PHP Classes')
            ->setDefinition(array(
                new InputOption('from', null, InputOption::VALUE_REQUIRED, 'The path where documents (*.pcg files) are read from', './doc'),
                new InputOption('to', null, InputOption::VALUE_REQUIRED, 'The path where to generate classes (*.php files)', './src')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = rtrim($input->getOption('from'), '/') . '/';
        $to = rtrim($input->getOption('to'), '/') . '/';

        $finder = new Finder();
        $finder->files()->name('*.pcg')->in($from);

        foreach ($finder as $file) {
            /* @var \Symfony\Component\Finder\SplFileInfo $file */
            $this->generate($to, $file->getRealPath());
        }
    }

    /**
     * Generates the PHP Classes from a pcg file
     *
     * @param string $path
     * @param string $filename
     */
    protected function generate($path, $filename)
    {
        $generator = new ClassGenerator();
        $generator->generate(file_get_contents($filename));
        $fqcn = $generator->getClassBuilder()->getClass();
        $compiler = new Compiler();
        $class = $compiler->parseClassName($fqcn);
        $path = $this->createDirFromNamespace($path, $class['namespace']);
        $output = $path . '/' . $class['classname'] . '.php';
        $generator->write($output);
    }

    /**
     * Determines directory from long class name
     *
     * Creates directory if not exists
     *
     * @param string $path      The path to generate classes
     * @param string $namespace The namespace of the class
     *
     * @throws \Exception
     *
     * @return string Directory name
     */
    protected function createDirFromNamespace($path, $namespace = null)
    {
        if ($namespace) {
            $path .= '/' . str_replace('\\', '/', trim($namespace, '\\'));
            if (is_dir($path)) {
                return $path . '/';
            }
            if (file_exists($path)) {
                throw new \Exception(sprintf('Path %s already exists', $path));
            }
            mkdir($path, 0777, true);
        }

        return $path;
    }

}
