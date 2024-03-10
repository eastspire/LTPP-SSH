<?php

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;
use Webman\Http\Request;
use app\controller\Index;

Route::fallback(function (Request $request) {
    $path = $request->path();
    $res = json_encode(
        ['code' => -1, 'title' => Index::$title, 'content' => Index::$parameter_error_msg],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    );
    return response($res, 200, [
        'Content-Type' => 'application/json;charset=utf-8',
        'Accept-Ranges' => 'bytes',
        'Content-Length' => strlen($res),
        'File-Content-Type' => 'application/json;charset=utf-8',
        'File-Path' => $path,
    ]);
});
