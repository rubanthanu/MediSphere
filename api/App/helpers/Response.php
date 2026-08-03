<?php

class Response
{
    
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

   
}
