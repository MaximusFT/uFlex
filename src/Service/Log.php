<?php

namespace ptejada\uFlex\Service;

/**
 * Console to log reports and errors
 *
 * @package ptejada\uFlex
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class Log
{
    const LEVEL_LOG   = 'log';
    const LEVEL_DEBUG = 'debug';
    const LEVEL_ERROR = 'error';

    /** @var array The console to store all logs */
    protected $console = array();
    protected $currentSection = 'init';

    /**
     * Adds a log entry
     *
     * @param string $level The level to log the message at
     * @param string $message The message to log
     */
    protected function addEntry($level, $message)
    {
        $this->console[$level][] = array(
            'time'    => microtime(),
            'section' => $this->getSection(),
            'message' => (string)$message,
        );
    }

    /**
     * Get the the current section
     * @return string
     */
    public function getSection()
    {
        return $this->currentSection;
    }

    /**
     * Set a new logging section
     *
     * @param string $name New logging section name
     *
     * @return $this
     */
    public function section($name)
    {
        $this->currentSection = $name;
        return $this;
    }

    /**
     * Log a message
     * @param string $message Message to log
     */
    public function log($message)
    {
        $this->addEntry(self::LEVEL_LOG, $message);
    }

    /**
     * Log an error message
     * @param string $message
     */
    public function error($message)
    {
        $this->addEntry(self::LEVEL_ERROR, $message);
    }

    /**
     * Log a debugging message
     * @param string $message
     */
    public function debug($message)
    {
        $this->addEntry(self::LEVEL_DEBUG, $message);
    }

    /**
     * Check if there ara any errors
     * @return bool
     */
    public function hasError()
    {
        return ! empty($this->console[self::LEVEL_ERROR]);
    }

    /**
     * Check if it there are any debugging reports
     * @return bool
     */
    public function hasReports()
    {
        return ! empty($this->console[self::LEVEL_DEBUG]);
    }

    /**
     * Get logged debugging information
     * @return string[][]
     */
    public function getReports()
    {
        return $this->hasReports() ? $this->console[self::LEVEL_DEBUG] : array();
    }

    /**
     * Get logged errors
     * @return string[][]
     */
    public function getErrors()
    {
        return $this->hasError() ? $this->console[self::LEVEL_ERROR] : array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return print_r($this->console, true);
    }
}
