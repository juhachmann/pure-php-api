<?php

namespace app\views;

use JsonSerializable;

/**
 * Resposta em padrão JSend para APIs
 */
abstract class ResponseWrapper implements JsonSerializable
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

    public function jsonSerialize(): array
    {
        $attributes = [];
        $attributes['status'] = $this->status;
        $attributes['code'] = $this->code;
        if(is_array($this->data) || $this->data != null) {
            $attributes['data'] = $this->data;
        }
        if($this->message != null)
            $attributes['message'] = $this->message;
        return $attributes;
    }
}
