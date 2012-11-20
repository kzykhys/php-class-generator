PHP Class Generator [![Build Status](https://travis-ci.org/kzykhys/php-class-generator.png)](https://travis-ci.org/kzykhys/php-class-generator)
===================

Generate PSR compliant classes from plain text document

Requirements
------------

* PHP 5.3.3 +

Installation
------------

### Download phar

[Download php-cg.phar](https://github.com/downloads/kzykhys/php-class-generator/php-cg.phar) and store anywhere.

### via Composer

```
{
    "require": {
        "kzykhys/php-class-generator": "dev-master"
    }
}
```

Usage
-----

Write your class and save the text file to *.pcg (Syntax is described below)
For example ``./doc/myclass.pcg``

```
KzykHys\ClassGenerator\Sample < KzykHys\ClassGenerator\Container << \IteratorAggrigate \Countable
> The sample of PHP Class Generator
> Generates PHP classes from plain text document (*.pcg)
+ iterator:\ArrayIterator
# container:array
# length:integer[get set] > The length of code
# compiled:boolean[is set] > Whether this class is compiled or not
- options:array
- generator:Generator[get set]
+ __construct(options:array)
+ generate(document:string version:string):\KzykHys\ClassGenerator\Compiler\StreamWriter
+ getString():string > Returns the code as a string
+ write(filename:string) > Write the code to file
+ count():integer
# traverseContainer():Container
```

Run the command

```
$ php php-pcg.phar --from ./doc --to ./src
```

PHP file will be generated to ``./src/Full/Qualified/ClassName.php``

``` php
<?php

namespace KzykHys\ClassGenerator;

/**
 * The sample of PHP Class Generator
 * Generates PHP classes from plain text document (*.pcg)
 */
class Sample extends Container implements \IteratorAggrigate, \Countable {

    /**
     * @var \ArrayIterator $iterator
     */
    public $iterator;

    /**
     * @var array $container
     */
    protected $container;

    /**
     * The length of code
     * 
     * @var integer $length
     */
    protected $length;

    /**
     * Whether this class is compiled or not
     * 
     * @var boolean $compiled
     */
    protected $compiled;

    /**
     * @var array $options
     */
    private $options;

    /**
     * @var Generator $generator
     */
    private $generator;


    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        
    }

    /**
     * @param string $document
     * @param string $version
     *
     * @return \KzykHys\ClassGenerator\Compiler\StreamWriter
     */
    public function generate($document, $version)
    {
        
    }

    /**
     * Returns the code as a string
     * 
     *
     * @return string
     */
    public function getString()
    {
        
    }

    /**
     * Write the code to file
     * 
     * @param string $filename
     */
    public function write($filename)
    {
        
    }

    /**
     *
     * @return integer
     */
    public function count()
    {
        
    }

    /**
     *
     * @return Container
     */
    protected function traverseContainer()
    {
        
    }

}
```

The Syntax
----------

### Overview

* Class Definition (required)
* Field Definition (optionai)
* Method Definition (optional)

### Class Definition (required)

```
%ClassName% < %BaseClassName% << %InterfaceName% %InterfaceName% > %Comment%
> %Comment%
```

* ``%ClassName%`` is required
** If your class is in the namespace, ``%ClassName%`` will be like this ``\\Namespace\\Package\\ClassName``
* ``<`` represents ``extends``
* ``%BaseClassName%`` is optional
* ``<<`` represents ``implements``
* ``%InterfaceName%`` is optional
* ``>`` starts comment to end of the line
* You can place comments to same line and next line of class definition

### Field Definition (optional)

```
%FieldVisibility% %FieldName% : %FieldType% \[%FieldAccessor%\] > %Comment%
> %Comment%
```

* ``%FieldVisibility%`` is required
* ``%FieldVisibility%`` takes ``+`` as public ``#`` as protected ``-`` as private
* ``%FieldName%`` is required
* ``%FieldType%`` is optional
* ``%FieldAccessor%`` takes ``set``, ``bind`` as setter, ``get``, ``is`` as getter
* ``>`` starts comment to end of the line
* You can place comments to same line and next line of field definition

### Method Definition (optionai)

```
%MethodVisibility% %MethodName% (%Argument% : %ArgumentType%) : %ReturnType%
```

* ``%MethodVisibility%`` is required
* ``%MethodVisibility%`` takes ``+`` as public ``#`` as protected ``-`` as private
* ``%MethodName%`` is required
* Braces ``()`` are required (even if there is no argument)
* ``%Argument%`` and ``%ArgumentType%`` is optional
* ``%ReturnType%`` is optional

Write a Readable Document
-------------------------

You can use any white spaces to write a readable document

Following code is same as the code used in Usage

```
KzykHys\ClassGenerator\Sample < KzykHys\ClassGenerator\Container << \IteratorAggrigate \Countable
    > The sample of PHP Class Generator
    > Generates PHP classes from plain text document (*.pcg)

+  iterator : \ArrayIterator
# container : array
#    length : integer[get set]
            > The length of code
#  compiled : boolean[is set]
            > Whether this class is compiled or not
-   options : array
- generator : Generator[get set]

+   __construct(options:array)
+    generate(document:string
               version:string) : \KzykHys\ClassGenerator\Compiler\StreamWriter
+                  getString() : string
                               > Returns the code as a string
+       write(filename:string)
                               > Write the code to file
+                      count() : integer
#          traverseContainer() : Container
```

Build Phar Archive
------------------

You can build phar archive from source code by following command

```
php-cg build
```

If fails like this

```
  [UnexpectedValueException]
  creating archive "php-cg.phar" disabled by the php.ini setting phar.readonly
```

Run following

```
php -d phar.readonly=0 php-cg build
```


Notes
-----

* Class constant is not supported yet.
* Implementation of interface is not generated automatically.

Author
------
Kazuyuki Hayashi <hayashi@valnur.net>