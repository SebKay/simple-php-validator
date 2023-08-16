<?php

namespace SebKay\SPV\Exceptions;

class ValidationException extends \InvalidArgumentException
{
    protected $message = 'The given data was invalid.';

    protected $code = 422;
}
