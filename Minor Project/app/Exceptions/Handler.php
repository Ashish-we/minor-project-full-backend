<?php

namespace App\Exceptions;
use Illuminate\Auth\Access\AuthorizationException;
use App\Exceptions\SanctumAbilitiesExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\HttpResponses;
class Handler extends ExceptionHandler
{
    use HttpResponses;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function report(Throwable $exception) {
        parent::report($exception);
    }
    public function render($request, \Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            $handler = new SanctumAbilitiesExceptionHandler(app());
            return $handler->render($request, $exception);
        }

        return parent::render($request, $exception);
    }

}
