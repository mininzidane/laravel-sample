<?php

namespace App\Exceptions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;
use Whoops\Handler\HandlerInterface;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = []; //

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = ["password", "password_confirmation"];

	/**
	 * Report or log an exception.
	 *
	 * @param Throwable $exception
	 * @return void
	 */
	public function report(Throwable $exception)
	{
		if (app()->bound("sentry") && $this->shouldReport($exception)) {
			app("sentry")->captureException($exception);
		}

		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param Request $request
	 * @param Throwable $exception
	 * @return
	 */
	public function render($request, Throwable $exception)
	{
		if (strpos($request->getPathInfo(), "/graphql") !== false) {
			$class = get_class($exception);

			$message = $exception->getMessage();

			switch ($class) {
				case "Illuminate\Auth\AuthenticationException":
					$code = 401;
					break;
				case "Symfony\Component\HttpKernel\Exception\NotFoundHttpException":
					$code = 404;
					$message = "Not Found";
					break;
				default:
					$code = 500;
					$message = "Error Encountered";
					break;
			}

			return response()
				->json(["message" => $message])
				->setStatusCode($code);
		}

		if ($exception instanceof HippoException) {
			return response()->json(
				$exception->getMessage(),
				$exception->getCode(),
			);
		}

		return parent::render($request, $exception);
	}

	protected function whoopsHandler()
	{
		try {
			return app(HandlerInterface::class);
		} catch (BindingResolutionException $e) {
			return parent::whoopsHandler();
		}
	}
}
