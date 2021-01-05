<?php

/*
 * This file is part of the monolog-parser package.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Monolog\Reader;

use Dubture\Monolog\Reader\AbstractReader;

/**
 * @author Robert Gruendler <r.gruendler@gmail.com>
 */
class LogReader extends AbstractReader implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * @var integer
     */
    protected $lineCount;

    /**
     * @var \Dubture\Monolog\Parser\LogParserInterface
     */
    protected $parser;

    /**
     * @param string $file
     */
    public function __construct($file, $defaultPatternPattern = null)
    {
        parent::__construct($defaultPatternPattern);
        
        $this->file = new \SplFileObject(realpath($file), 'r');        
        $i = 0;
        while (!$this->file->eof()) {
            $this->file->current();
            $this->file->next();
            $i++;
        }
        $this->lineCount = $i;       
        $this->parser = $this->getDefaultParser();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->lineCount < $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $key = $this->file->key();
        $this->file->seek($offset);
        $log = $this->current();       
        $this->file->seek($key);
        $this->file->current();

        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException("LogReader is read-only.");
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException("LogReader is read-only.");
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->file->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->file->next();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {        
        return $this->parser->parse($this->file->current());
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->file->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->file->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->lineCount;
    }
}
