<?php

use Symfony\Component\HttpFoundation\JsonResponse;

class Errors
{
    public static function getErrors($errors)
    {

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $errorMessages;
        }

        return [];
    }
}
