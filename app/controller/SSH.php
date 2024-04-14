<?php
/*
 * @Author: 18855190718 1491579574@qq.com
 * @Date: 2023-07-09 20:33:57
 * @LastEditors: wmzn-ltpp 1491579574@qq.com
 * @LastEditTime: 2023-10-14 14:30:49
 * @FilePath: \LTPP-SSH\app\controller\SSH.php
 * @Description: Email:1491579574@qq.com
 * QQ:1491579574
 * Copyright (c) 2023 by SQS, All Rights Reserved. 
 */

namespace app\controller;

use Exception;
use support\Request;

class SSH
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
     *  服务指定服务器关机失败
     * @var string $failed_to_shutdown_service_msg
     */
    static $failed_to_shutdown_service_msg = '【LTPP-SSH】服务指定服务器关机失败！';

    /**
     *  服务指定服务器关机成功
     * @var string $success_to_shutdown_service_msg
     */
    static $success_to_shutdown_service_msg = '【LTPP-SSH】服务指定服务器关机成功！';

    /**
     *  服务指定服务器开机失败
     * @var string $failed_to_poweron_service_msg
     */
    static $failed_to_poweron_service_msg = '【LTPP-SSH】服务指定服务器开机失败！';

    /**
     *  服务指定服务器开机成功
     * @var string $success_to_poweron_service_msg
     */
    static $success_to_poweron_service_msg = '【LTPP-SSH】服务指定服务器开机成功！';

    /**
     *  服务指定服务器重启失败
     * @var string $failed_to_reboot_service_msg
     */
    static $failed_to_reboot_service_msg = '【LTPP-SSH】服务指定服务器重启失败！';

    /**
     *  服务指定服务器重启成功
     * @var string $success_to_reboot_service_msg
     */
    static $success_to_reboot_service_msg = '【LTPP-SSH】服务指定服务器重启成功！';

    /**
     *  服务指定服务器删除失败
     * @var string $failed_to_delete_service_msg
     */
    static $failed_to_delete_service_msg = '【LTPP-SSH】服务指定服务器删除失败！';

    /**
     *  服务指定服务器删除成功
     * @var string $success_to_delete_service_msg
     */
    static $success_to_delete_service_msg = '【LTPP-SSH】服务指定服务器删除成功！';

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
    static $title = '【LTPP-SSH】运行结果';

    /**
     * 判断是否只包含数字和字母
     */
    private function isEnglishAlphabet($str)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $str);
    }

    /**
     * 运行shell命令
     */
    private function runExec($command = '', &$out = '', &$run_exec_code = 0)
    {
        try {
            $run_exec_code = 0;
            $pipes = [];
            $descriptorspec = [
                0 => ['pipe', 'r'],  // 标准输入
                1 => ['pipe', 'w'],  // 标准输出
                2 => ['pipe', 'w']   // 标准错误输出
            ];
            $process = proc_open($command, $descriptorspec, $pipes);
            if (is_resource($process)) {
                // 关闭标准输入管道
                fclose($pipes[0]);
                // 读取标准输出和标准错误输出
                $stdout = stream_get_contents($pipes[1]);
                $stderr = stream_get_contents($pipes[2]);
                // 关闭标准输出和标准错误输出管道
                fclose($pipes[1]);
                fclose($pipes[2]);
                // 注册信号处理程序                
                pcntl_signal(SIGTERM, function ($signo) {
                    $pid = getmypid();
                    posix_kill(-$pid, SIGKILL);
                });
                $pid = intval(proc_get_status($process)['pid']);
                // 等待进程终止
                pcntl_waitpid($pid, $run_exec_code);
                // 输出结果或错误信息
                if (!empty($stdout)) {
                    $out = $stdout;
                }
                if (!empty($stderr)) {
                    $out =  $stderr;
                }
                // 取消注册信号处理程序
                pcntl_signal(SIGTERM, SIG_DFL);
                // 关闭进程
                proc_close($process);
            }
        } catch (Exception $e) {
        }
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
            if ($i < SSH::$start_port || $i > SSH::$end_port) {
                return [
                    'code' => -1,
                    'title' => SSH::$title,
                    'content' => SSH::$port_error_msg
                ];
            }
        }
        $begin_port = $port;
        $code_sever_port = $port + 1;
        $no_use_port_begin = $port + 2;
        $no_use_end_port = max($code_sever_port, $port + $port_num - 1);
        $shell = "echo -e '$password\\n$password' | sudo passwd ltpp;sudo service ssh restart;rm -rf /path;mkdir -p /path/to;touch /path/to/config.yaml;echo password: $password >> /path/to/config.yaml;nohup code-server --bind-addr=0.0.0.0:80 --config /path/to/config.yaml > /dev/null 2>&1 & tail -f /dev/null";
        $docker_cmd = 'docker run --name ' . $name . ' -itd -p ' . $begin_port . ':22 -p ' . $code_sever_port . ':80 -p ' . $no_use_port_begin . '-' . $no_use_end_port . ':' .  $no_use_port_begin . '-' . $no_use_end_port . ' --restart=always --init --memory=2g --cpus=0.2 ccr.ccs.tencentyun.com/linux_environment/debian:1.0.0 /bin/bash -c "' . $shell . '" 2>&1';
        $out = '';
        $this->runExec($docker_cmd, $out);
        if (!$out || !$this->isEnglishAlphabet($out)) {
            $this->runExec('docker rm -f ' . $name);
            return [
                'code' => 0,
                'title' => SSH::$title,
                'content' => SSH::$failed_to_create_service_msg
            ];
        }
        return [
            'code' => 1,
            'title' => SSH::$title,
            'content' => SSH::$success_to_create_service_msg
        ];
    }

    /**
     * SSH购买
     * @param Request $request
     */
    public function buy(Request $request)
    {
        $port = (int)($request->post('port') ?? 0);
        $name = (string)($request->post('name') ?? '');
        $port_num = (int)($request->post('port_num') ?? 0);
        $password = (string)($request->post('password') ?? '');
        if (!$port || !$password || !$name || !$port_num) {
            return json([
                'code' => -1,
                'title' => SSH::$title,
                'content' => SSH::$parameter_error_msg
            ]);
        }
        $res = $this->creat($port, $password, $port_num, $name);
        return json($res);
    }

    /**
     * 关机
     */
    public function shutdown(Request $request)
    {
        $name = (string)($request->post('name') ?? '');
        $docker_cmd = 'docker stop ' . $name;
        $out = '';
        $this->runExec($docker_cmd, $out);
        if (!$out || !$this->isEnglishAlphabet($out)) {
            return json([
                'code' => 0,
                'title' => SSH::$title,
                'content' => SSH::$failed_to_shutdown_service_msg
            ]);
        }
        return json([
            'code' => 1,
            'title' => SSH::$title,
            'content' => SSH::$success_to_shutdown_service_msg
        ]);
    }

    /**
     * 开机
     */
    public function poweron(Request $request)
    {
        $name = (string)($request->post('name') ?? '');
        $docker_cmd = 'docker start ' . $name;
        $out = '';
        $this->runExec($docker_cmd, $out);
        if (!$out || !$this->isEnglishAlphabet($out)) {
            return json([
                'code' => 0,
                'title' => SSH::$title,
                'content' => SSH::$failed_to_poweron_service_msg
            ]);
        }
        return json([
            'code' => 1,
            'title' => SSH::$title,
            'content' => SSH::$success_to_poweron_service_msg
        ]);
    }

    /**
     * 重启
     */
    public function reboot(Request $request)
    {
        $name = (string)($request->post('name') ?? '');
        $docker_cmd = 'docker restart ' . $name;
        $out = '';
        $this->runExec($docker_cmd, $out);
        if (!$out || !$this->isEnglishAlphabet($out)) {
            return json([
                'code' => 0,
                'title' => SSH::$title,
                'content' => SSH::$failed_to_reboot_service_msg
            ]);
        }
        return json([
            'code' => 1,
            'title' => SSH::$title,
            'content' => SSH::$success_to_reboot_service_msg
        ]);
    }

    /**
     * 删除
     */
    public function delete(Request $request)
    {
        $name = (string)($request->post('name') ?? '');
        $docker_cmd = 'docker rm -f ' . $name;
        $out = '';
        $this->runExec($docker_cmd, $out);
        if (!$out || !$this->isEnglishAlphabet($out)) {
            return json([
                'code' => 0,
                'title' => SSH::$title,
                'content' => SSH::$failed_to_delete_service_msg
            ]);
        }
        return json([
            'code' => 1,
            'title' => SSH::$title,
            'content' => SSH::$success_to_delete_service_msg
        ]);
    }
}
