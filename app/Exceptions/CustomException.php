<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    protected $message;

    protected $code;

    protected $parameters;

    public function __construct($message ,$code, $parameters = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->parameters = $parameters;
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['result' => false, 'error' => $this->message], $this->code);
    }

    public function getParameters()
    {
        return $this->parameters;
    }
    
}
