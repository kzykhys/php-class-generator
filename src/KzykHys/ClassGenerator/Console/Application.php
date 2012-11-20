<?php
/**
 * This software is licensed under MIT License
 *
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Console;

use KzykHys\ClassGenerator\ClassGenerator;

/**
 * Command line interface
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Application extends \Symfony\Component\Console\Application
{

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct('PHP Class Generator', ClassGenerator::VERSION);

        $this->add(new Command\GenerateCommand());
        $this->add(new Command\BuildCommand());
    }

}
