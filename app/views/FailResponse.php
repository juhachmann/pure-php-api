<?php

namespace app\views;

use app\views\ResponseWrapper;

class FailResponse extends ResponseWrapper
{

    public function __construct(int $code, mixed $data = null, mixed $message = null)
    {
        parent::__construct("fail", $code, $data, $message);
    }

}