<?php
/**
 * This software is licensed under MIT License
 * 
 * Copyright (c) 2012, Kazuyuki Hayashi
 */

namespace KzykHys\ClassGenerator\Compiler;

/**
 * Help build a string
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class StreamWriter
{

    private $buffer;
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
        $this->lines = array();
    }

    /**
     * Writes a string
     *
     * @param string $str
     *
     * @return StreamWriter
     */
    public function write($str)
    {
        $this->buffer .= $str;

        return $this;
    }

    /**
     * Writes a string with format
     *
     * @param string $str
     * @param mixed  $args ...
     *
     * {@see http://jp2.php.net/manual/en/function.sprintf.php}
     *
     * @return StreamWriter
     */
    public function writeF($str, $args)
    {
        $args = func_get_args();
        if (count($args) < 2) {
            throw new \OutOfBoundsException('Missing argument #2');
        }

        return $this->write(vsprintf(array_shift($args), $args));
    }

    /**
     * Writes a line
     *
     * @param string $line
     *
     * @return StreamWriter
     */
    public function writeLine($line = null)
    {
        $this->write($line . $this->options['lineFeed']);

        return $this;
    }

    /**
     * Writes a line with format
     *
     * @param string $str
     * @param mixed  $args ...
     *
     * {@see http://jp2.php.net/manual/en/function.sprintf.php}
     *
     * @return StreamWriter
     */
    public function writeLineF($line, $args)
    {
        $args = func_get_args();
        if (count($args) < 2) {
            throw new \OutOfBoundsException('Missing argument #2');
        }

        return $this->writeLine(vsprintf(array_shift($args), $args));
    }

    /**
     * Inserts a indent
     *
     * @return StreamWriter
     */
    public function indent()
    {
        $this->write(str_repeat(' ', $this->options['indent_spaces']));

        return $this;
    }

    /**
     * Writes contents as a file
     *
     * @param string $filename
     *
     * @return integer|boolean Bytes written
     */
    public function save($filename)
    {
        return file_put_contents($filename, $this->buffer);
    }

    /**
     * Returns contents as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->buffer;
    }

}
