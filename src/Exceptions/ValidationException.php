<?php

namespace SebKay\SPV;

class ValidationException extends \InvalidArgumentException
{
    protected $message = 'The given data was invalid.';

    protected $code = 422;
}
