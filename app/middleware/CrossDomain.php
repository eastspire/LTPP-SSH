<?php
/*
 * @Author: 18855190718 1491579574@qq.com
 * @Date: 2023-07-09 22:40:56
 * @LastEditors: 18855190718 1491579574@qq.com
 * @LastEditTime: 2023-09-15 23:27:06
 * @FilePath: \LTPP-SSH\app\middleware\CrossDomain.php
 * @Description: Email:1491579574@qq.com
 * QQ:1491579574
 * Copyright (c) 2023 by SQS, All Rights Reserved. 
 */
declare(strict_types=1);
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 全局跨域请求处理
 * Class CrossDomain
 * @package app\middleware
 */

class CrossDomain implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 6666');
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('Origin', '*'),
            'Access-Control-Allow-Methods' => 'GET, POST, PATCH',
            'Access-Control-Allow-Headers' => 'Content-Type , If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With',
            'Access-Control-Max-Age' => '6666'
        ]);
        return $response;
    }
}