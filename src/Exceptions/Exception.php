<?php

namespace Chenshuai1993\SciUploader\Exceptions;

class Exception extends \Exception
{
    public function __construct(int $code = 0, string $message = '', $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
