<?php

namespace App\Exceptions;

use App\Logging\Logger;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use Logger;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = ["password", "password_confirmation"];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $this->logDebug($e->getMessage());
        });
    }

    function report(Throwable $e)
    {
        parent::report($e);
    }
}
