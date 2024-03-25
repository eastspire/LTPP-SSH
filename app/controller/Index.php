<?php
/*
 * @Author: 18855190718 1491579574@qq.com
 * @Date: 2023-07-09 20:33:57
 * @LastEditors: wmzn-ltpp 1491579574@qq.com
 * @LastEditTime: 2023-10-14 14:30:49
 * @FilePath: \LTPP-SSH\app\controller\Index.php
 * @Description: Email:1491579574@qq.com
 * QQ:1491579574
 * Copyright (c) 2023 by SQS, All Rights Reserved. 
 */

namespace app\controller;

use Exception;
use support\Request;

class Index
{
    /**
     * 参数错误信息
     * @var string $parameter_error_msg
     */
    static $parameter_error_msg = '参数错误！';

    /**
     * 端口错误信息
     * @var string $port_error_msg
     */
    static $port_error_msg = '端口错误！';

    /**
     * 端口已占用错误信息
     * @var string $port_already_in_use_msg
     */
    static $port_already_in_use_msg = '端口已占用！';

    /**
     *  服务创建失败
     * @var string $failed_to_create_service_msg
     */
    static $failed_to_create_service_msg = '服务创建失败！';

    /**
     * 服务创建/运行失败
     * @var string $failed_to_create_or_run_service_msg
     */
    static $failed_to_create_or_run_service_msg = '服务创建/运行失败！';

    /**
     * 服务运行异常
     * @var string $failed_to_create_or_run_service_msg
     */
    static $server_error = '服务运行异常！';

    /**
     * 服务创建/运行失败最大重新检测次数
     * @var int $failed_to_create_or_run_max_check_times
     */
    static $failed_to_create_or_run_max_check_times = 6;

    /**
     * SSH通知标题
     * @var int $title
     */
    static $title = 'LTPP-SSH创建结果通知';

    /**
     * SSH最大限制
     * @var int $max_num
     */
    static $max_num = 36;

    /**
     * SSH起始端口
     * @var int $begin_port
     */
    static $begin_port = 50000;

    /**
     * 判断端口是否占用
     * @param int $port
     * @return bool $res
     */
    public function judgePortIsUse($port = 0)
    {
        $res = false;
        $errno = null;
        $errstr = null;
        try {
            $socket = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
            if ($socket) {
                // 端口被占用
                $res = true;
                fclose($socket);
            } else {
                $res = false;
            }
        } catch (Exception $e) {
            return $res;
        }
        return $res;
    }

    /**
     * 判断是否只包含数字和字母
     */
    private function isEnglishAlphabet($str)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $str);
    }

    /**
     * 创建SSH
     * @param int $port
     * @param string $password
     * @param int $port_num
     * @return array $res
     */
    private function creat($port = 0, $password = '', $port_num)
    {
        if (
            !$port ||
            $port < Index::$begin_port ||
            $port >= (Index::$begin_port + Index::$max_num) ||
            $port_num <= 0
        ) {
            return [
                'code' => -1,
                'title' => Index::$title,
                'content' => Index::$port_error_msg
            ];
        }
        try {
            for ($i = $port; $i < $port + $port_num; ++$i) {
                if ($this->judgePortIsUse($i)) {
                    return [
                        'code' => 0,
                        'title' => Index::$title,
                        'content' => Index::$port_already_in_use_msg
                    ];
                }
            }
            $end_port =  max($port + 2, $port + $port_num - 1);
            $shell = "echo -e '$password\\n$password' | sudo passwd ltpp;sudo service ssh restart;rm -rf /path;mkdir -p /path/to;touch /path/to/config.yaml;echo password: $password >> /path/to/config.yaml;nohup code-server --bind-addr=0.0.0.0:80 --config /path/to/config.yaml > /dev/null 2>&1 & tail -f /dev/null";
            $docker_cmd = 'docker run -itd -p ' . $port . ':22 -p ' . ($port + 1) . ':80 -p ' . ($port + 2) . '-' . $end_port . ':' .  ($port + 2) . '-' . $end_port . ' --restart=always --shm-size 1g --memory=2g --cpus=0.2 ccr.ccs.tencentyun.com/linux_environment/debian:1.0.0 /bin/bash -c "' . $shell . '" 2>&1';
            exec($docker_cmd, $out);
            if (empty($out) || sizeof($out) != 1 || !$this->isEnglishAlphabet($out[0])) {
                return [
                    'code' => -1,
                    'title' => Index::$title,
                    'content' => Index::$failed_to_create_service_msg
                ];
            }
            // 容器运行检测
            $is_fail = false;
            $times = 0;
            while (!$this->judgePortIsUse($port)) {
                if ($times >= Index::$failed_to_create_or_run_max_check_times) {
                    $is_fail = true;
                    break;
                }
                ++$times;
                sleep(1);
            }
            if ($is_fail) {
                return [
                    'code' => -1,
                    'title' => Index::$title,
                    'content' => Index::$failed_to_create_or_run_service_msg
                ];
            }
        } catch (Exception $e) {
            return [
                'code' => -1,
                'title' => Index::$title,
                'content' => Index::$failed_to_create_or_run_service_msg . "\n" . $e->getMessage()
            ];
        }
        return [
            'code' => 1,
            'title' => Index::$title,
            'port' => $port
        ];
    }

    /**
     * SSH购买
     * @param Request $request
     */
    public function index(Request $request)
    {
        $user_id = (int) $request->post('user_id');
        $port = (int) $request->post('port');
        $password = $request->post('password');
        $port_num = (int)$request->post('port_num');
        if (
            !$user_id || !is_numeric($user_id) ||
            !$port || !is_numeric($port) ||
            !$password || !$port_num || !is_numeric($port_num)
        ) {
            return json(['code' => -1, 'title' => Index::$title, 'content' => Index::$parameter_error_msg]);
        }
        $res = $this->creat($port, $password, $port_num);
        return json($res);
    }
}
