<?php

class MyException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}

?>