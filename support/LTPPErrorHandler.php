<?php

namespace support;

use Throwable;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;
use app\controller\Base;
use support\exception\BusinessException;
use app\controller\Index;

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
        try {
            parent::report($exception);
        } catch (Throwable $e) {
        }
    }

    public function render(Request $request, Throwable $exception): Response
    {
        $res = '';
        $path = '';
        try {
            if (($exception instanceof BusinessException) && ($response = $exception->render($request))) {
                return $response;
            }
            if ($request->expectsJson()) {
                $json = ['code' => -1, 'title' => Index::$title, 'content' => Index::$server_error];
                $this->debug && $json['traces'] = (string)$exception;
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(
                        $json,
                        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    )
                );
            }
            if ($this->debug) {
                return new Response(200, [], nl2br((string)$exception));
            }
            $path = $request->path();
            $res = json_encode(
                ['code' => -1, 'title' => Index::$title, 'content' => Index::$parameter_error_msg],
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );
        } catch (Throwable $e) {
        }
        return response($res, 200, [
            'Content-Type' => 'application/json;charset=utf-8',
            'Accept-Ranges' => 'bytes',
            'Content-Length' => strlen($res),
            'File-Content-Type' => 'application/json;charset=utf-8',
            'File-Path' => $path,
        ]);
    }
}
