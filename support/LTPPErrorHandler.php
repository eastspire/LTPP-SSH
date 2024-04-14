<?php

namespace support;

use Throwable;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;
use support\exception\BusinessException;
use app\controller\SSH;

/**
 * Class Handler
 * @package support\exception
 */
class LTPPErrorHandler extends ExceptionHandler
{
    public $dontReport = [
        BusinessException::class,
    ];

    public function report(Throwable $exception)
    {
    }

    public function render(Request $request, Throwable $exception): Response
    {
        $json = [];
        try {
            if (($exception instanceof BusinessException) && ($response = $exception->render($request))) {
                return $response;
            }
            $json = ['code' => -1, 'title' => SSH::$title, 'content' => SSH::$server_error];
        } catch (Throwable $e) {
        }
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(
                $json,
                JSON_UNESCAPED_UNICODE  | JSON_UNESCAPED_SLASHES
            )
        );
    }
}
