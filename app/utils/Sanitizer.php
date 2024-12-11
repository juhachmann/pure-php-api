<?php

namespace app\utils;

class Sanitizer {

    public function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

}
