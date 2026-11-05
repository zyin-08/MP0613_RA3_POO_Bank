<?php namespace ComBank\Exceptions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 2:29 PM
 */

// Operación no permitida por el estado de la cuenta (cerrada, ya abierta...)
class BankAccountException extends BaseExceptions
{
    protected $errorCode = 500;
    protected $errorLabel = 'BankAccountException';
}
