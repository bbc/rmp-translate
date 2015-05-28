<?php

namespace RMP\Translate;

use ReflectionClass;

/**
 * Class TranslateEvent
 *
 * To facilitate logging of translate issues
 *
 * @package RMP\Translate
 * @author Programmes Developers <programmes-devel@lists.forge.bbc.co.uk>
 */

class TranslateEvent
{
    /**
     * Event type constants, use as an enumeration
     */
    const FALLBACK = 'Fallback translation used';
    const MISSING = 'Missing translation';

    /**
     * @var string $eventTYpe
     */
    protected $eventType;

    /**
     * Additional data for logging etc
     * @var mixed $data
     */
    protected $data;

    public function __construct($eventType, $data)
    {
        $reflection = new ReflectionClass($this);
        $constants  = $reflection->getConstants();

        if (!in_array($eventType, $constants)) {
            throw new InvalidArgumentException('Invalid eventType value ('.$eventType.')');
        }
        $this->eventType = $eventType;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->eventType;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
