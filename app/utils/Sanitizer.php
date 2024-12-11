<?php

namespace app\utils;

class Sanitizer {

    /**
     * Aplica a função htmlspecialchars() em um array
     */
    public function escape_html(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

}
