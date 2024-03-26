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

use support\Request;

class Index
{
    /**
     * APP名称
     */
    static $app_name = 'LTPP-SSH';

    /**
     * 起始端口
     */
    static $start_port = 0;

    /**
     * 终止端口
     */
    static $end_port = 65535;

    /**
     * 参数错误信息
     * @var string $parameter_error_msg
     */
    static $parameter_error_msg = '【LTPP-SSH】参数错误！';

    /**
     * 端口错误信息
     * @var string $port_error_msg
     */
    static $port_error_msg = '【LTPP-SSH】端口错误！';

    /**
     * 端口已占用错误信息
     * @var string $port_already_in_use_msg
     */
    static $port_already_in_use_msg = '【LTPP-SSH】端口已占用！';

    /**
     *  服务创建成功
     * @var string $success_to_create_service_msg
     */
    static $success_to_create_service_msg = '【LTPP-SSH】服务创建成功！';

    /**
     *  服务创建失败
     * @var string $failed_to_create_service_msg
     */
    static $failed_to_create_service_msg = '【LTPP-SSH】服务创建失败！';

    /**
     * 服务创建/运行失败
     * @var string $failed_to_create_or_run_service_msg
     */
    static $failed_to_create_or_run_service_msg = '【LTPP-SSH】服务创建/运行失败！';

    /**
     * 服务运行异常
     * @var string $server_error
     */
    static $server_error = '【LTPP-SSH】服务运行异常！';

    /**
     * 等待时间
     */
    static $wait_time = 8;

    /**
     * SSH通知标题
     * @var int $title
     */
    static $title = '【LTPP-SSH】创建结果通知';

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
     * @param string $name
     * @return array $res
     */
    private function creat($port = 0, $password = '', $port_num = 2, $name = '')
    {
        for ($i = $port; $i < $port + $port_num; ++$i) {
            if ($i < Index::$start_port || $i > Index::$end_port) {
                return [
                    'code' => -1,
                    'title' => Index::$title,
                    'content' => Index::$port_error_msg
                ];
            }
        }
        $begin_port = $port;
        $code_sever_port = $port + 1;
        $no_use_port_begin = $port + 2;
        $no_use_end_port = max($code_sever_port, $port + $port_num - 1);
        $shell = "echo -e '$password\\n$password' | sudo passwd ltpp;sudo service ssh restart;rm -rf /path;mkdir -p /path/to;touch /path/to/config.yaml;echo password: $password >> /path/to/config.yaml;nohup code-server --bind-addr=0.0.0.0:80 --config /path/to/config.yaml > /dev/null 2>&1 & tail -f /dev/null";
        $docker_cmd = 'docker run --name ' . $name . ' -itd -p ' . $begin_port . ':22 -p ' . $code_sever_port . ':80 -p ' . $no_use_port_begin . '-' . $no_use_end_port . ':' .  $no_use_port_begin . '-' . $no_use_end_port . ' --restart=always --memory=2g --cpus=0.2 ccr.ccs.tencentyun.com/linux_environment/debian:1.0.0 /bin/bash -c "' . $shell . '" 2>&1';
        exec($docker_cmd, $out);
        if (empty($out) || sizeof($out) != 1 || !$this->isEnglishAlphabet($out[0])) {
            exec('docker rm -f ' . $name . ' 2>&1');
            return [
                'code' => 0,
                'title' => Index::$title,
                'content' => Index::$failed_to_create_service_msg
            ];
        }
        return [
            'code' => 1,
            'title' => Index::$title,
            'content' => Index::$success_to_create_service_msg
        ];
    }

    /**
     * SSH购买
     * @param Request $request
     */
    public function index(Request $request)
    {
        $port = (int)($request->post('port') ?? 0);
        $name = (string)($request->post('name') ?? '');
        $port_num = (int)($request->post('port_num') ?? 0);
        $password = (string)($request->post('password') ?? '');
        if (!$port || !$password || !$name || !$port_num) {
            return json([
                'code' => -1,
                'title' => Index::$title,
                'content' => Index::$parameter_error_msg
            ]);
        }
        $res = $this->creat($port, $password, $port_num, $name);
        return json($res);
    }
}
