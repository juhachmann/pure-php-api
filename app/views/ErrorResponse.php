<?php

namespace app\views;

use app\views\ResponseWrapper;

class ErrorResponse extends ResponseWrapper
{

    public function __construct(int $code, mixed $data = null, mixed $message = null)
    {
        parent::__construct("error", $code, $data, $message);
    }

}
