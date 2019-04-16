<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Request;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
		if($this->isHttpException($e)){
            /*if ($e instanceof MethodNotAllowedHttpException) {
				 Log::info('MethodNotAllowedHttpException Route - '.Request::method().': (' . Request::url().') from '.$_SERVER['HTTP_REFERER'].' - '.Request::getClientIp().(Session::has('franchisee_go_to') ? ' - Redirected from :'.Session::get('franchisee_go_to') : '') );
			}
			else */
			if (view()->exists('errors.'.$e->getStatusCode()))
            {
                return response()->view('errors.'.$e->getStatusCode(), [], $e->getStatusCode());
            }
        }
        return parent::render($request, $e);
    }
}
