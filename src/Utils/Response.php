<?php

use Symfony\Component\HttpFoundation\JsonResponse;

class Response
{
    public static function ko($response, $code = 400, $additional = null)
    {
        return new JsonResponse(["code" => $code, 'data' => $response, "additional" => $additional], $code);
    }

    public static function ok($response, $code = 200, $additional = null)
    {
        return new JsonResponse(["code" => $code, 'data' => $response, "additional" => $additional], $code);
    }
}
