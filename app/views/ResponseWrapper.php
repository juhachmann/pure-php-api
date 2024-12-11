<?php

namespace app\views;

class ResponseWrapper
{

    public mixed $data;
    public string $status;
    public int $code;
    public mixed $message;

    /**
     * @param mixed $data
     * @param string $status
     * @param int $code
     * @param mixed $message
     */
    public function __construct(string $status, int $code, mixed $data = null, mixed $message = null)
    {
        $this->data = $data;
        $this->status = $status;
        $this->code = $code;
        $this->message = $message;
    }

}
