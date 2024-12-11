<?php

namespace app\views;

use app\views\ResponseWrapper;

class SuccessResponse extends ResponseWrapper
{
    public function __construct(int $code, mixed $data = null)
    {
        parent::__construct("sucesso", $code, $data);
    }

}