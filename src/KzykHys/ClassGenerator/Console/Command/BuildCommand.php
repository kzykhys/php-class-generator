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

/**
 * Build Phar archive from source
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class BuildCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build Phar archive from source')
            ->setDefinition(array(
                new InputOption('phar', null, InputOption::VALUE_REQUIRED, 'The filename of Phar archive', 'php-cg.phar')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pharFile = $input->getOption('phar');
        $this->unlink($pharFile);

        $phar = new \Phar($pharFile, 0, 'php-cg.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $finder = new Finder();
        $finder->files('*.php')->exclude(array('test', 'build'))->in(array('src', 'vendor'));
        foreach ($finder as $file) {
            $path = str_replace(dirname(realpath(__DIR__.'/../../../../../php-cg')).DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $path = str_replace('\\', '/', $path);
            $phar->addFromString($path, file_get_contents($file->getRealPath()));
        }

        $phpCg = file_get_contents(__DIR__.'/../../../../../php-cg');
        $phpCg = preg_replace('/^.*?(<\?php.*)/ms', '\1', $phpCg);
        $phar->addFromString('php-cg', $phpCg);

        $phar->setStub($this->getStub());
        $phar->stopBuffering();
        unset($phar);
        chmod($pharFile, 0777);
    }

    /**
     * Gets stub
     *
     * @return string
     */
    protected function getStub()
    {
        return "#!/usr/bin/env php\n<?php Phar::mapPhar('php-cg.phar'); require 'phar://php-cg.phar/php-cg'; __HALT_COMPILER();";
    }

    /**
     * Deletes a file if exists
     *
     * @param string $filename
     */
    protected function unlink($filename)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}
