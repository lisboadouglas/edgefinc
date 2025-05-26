<?php

namespace App\Exceptions;

use Exception;

class GosatApiException extends Exception
{
    protected $code;
    protected $message;

    public function __construct(string $message = "", int $code = 500)
    {
        parent::__construct($message, $code);
        $this->code = $code;
        $this->message = $message;
    }

    public function render($request) 
    {
        return response()->json([
            'error' => 'gosat_api_error',
            'message' => $this->message
        ], $this->code);
    }
}
