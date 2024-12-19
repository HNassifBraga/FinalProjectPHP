<?php
abstract class ErrorHandler //abstract class
{
    abstract public function handleException($exception);
    abstract public function handleError($errno, $errstr, $errfile, $errline);
}


class SimpleErrorHandler extends ErrorHandler //inherit class to handle error
{
    public function handleException($exception) 
    {
        error_log($exception->getMessage());
        echo "error occurred, please, try again later.";
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        error_log("Error [$errno]: $errstr in $errfile on line $errline");
        echo "error occurred. please, try again later.";
        return true;
    }
}
?>