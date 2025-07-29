<?php

namespace E02Bundle\Entity;

class Message
{
    private $message;
    private $includeTimestamp;

    public function __construct($message, $includeTimestamp)
    {
        $this->message = $message;
        $this->includeTimestamp = $includeTimestamp;
    }

    public function validateMessage(): bool
    {
        if (empty($this->message))
            return false;
        return true;
    }
}

