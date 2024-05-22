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
     * 开机脚本位置
     */
    static $start_shell_path = '/shell/start.sh';

    /**
     * 起始端口
     */
    static $start_port = 0;

    /**
     * 终止端口
     */
    static $end_port = 65535;

    /**
     * 最小使用端口
     */
    static $min_use_port_num = 2;

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
     *  服务指定服务器重置失败
     * @var string $failed_to_reset_service_msg
     */
    static $failed_to_reset_service_msg = '【LTPP-SSH】服务指定服务器重置失败！';

    /**
     *  服务指定服务器重置成功
     * @var string $success_to_reset_service_msg
     */
    static $success_to_reset_service_msg = '【LTPP-SSH】服务指定服务器重置成功！';

    /**
     *  服务指定服务器回滚失败
     * @var string $failed_to_back_image_service_msg
     */
    static $failed_to_back_image_service_msg = '【LTPP-SSH】服务指定服务器回滚失败！';

    /**
     *  服务指定服务器回滚快照文件不存在
     * @var string $no_back_image_file_service_msg
     */
    static $no_back_image_file_service_msg = '【LTPP-SSH】服务指定服务器快照文件不存在！';

    /**
     *  服务指定服务器回滚成功
     * @var string $success_to_back_image_service_msg
     */
    static $success_to_back_image_service_msg = '【LTPP-SSH】服务指定服务器回滚成功！';

    /**
     *  服务指定服务器快照创建成功
     * @var string $success_to_create_image_service_msg
     */
    static $success_to_create_image_service_msg = '【LTPP-SSH】服务指定服务器快照创建成功！';

    /**
     *  服务指定服务器快照创建失败
     * @var string $fail_to_create_image_service_msg
     */
    static $fail_to_create_image_service_msg = '【LTPP-SSH】服务指定服务器快照创建失败！';

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
     * 快照保存目录
     * @var string $image_save_path
     */
    static $image_save_path = '/home/IMAGE/';

    /**
     * 默认快照
     */
    static $default_image_name = 'ccr.ccs.tencentyun.com/linux_environment/debian:1.0.0';

    /**
     * 版本
     */
    static $default_image_version = '1.0.0';

    /**
     * 判断路径是否存在（路径以/开头），不存在创建路径中的文件夹
     * @param string $path 路径
     * @param int $grade 权限
     */
    static public function judgeCreatPath($path, $grade = 0666)
    {
        if (file_exists($path)) {
            return true;
        }
        $name = [];
        $length = strlen($path);
        // 获取全部名称
        for ($i = 0; $i < $length; ++$i) {
            if ($path[$i] == '/') {
                $tem = '';
                for ($j = $i + 1; $j < $length; ++$j) {
                    if ($path[$j] == '/') {
                        $i = $j - 1;
                        break;
                    }
                    $tem .= $path[$j];
                    if ($j == $length - 1) {
                        $i = $j;
                        break;
                    }
                }
                if ($tem != '') {
                    $name[] = $tem;
                }
            }
        }
        $now_path = '/';
        foreach ($name as &$tem) {
            $now_path .= $tem . '/';
            $isfile = strripos($now_path, '.');
            if (!file_exists($now_path) && $isfile === false && !is_dir($now_path)) {
                try {
                    @mkdir($now_path, $grade, true);
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        return false;
    }

    /**
     * 文件（夹）删除
     * @param string $dir 文件路径
     * @return bool $res 删除是否成功
     */
    private function deleteAllFile($dir)
    {
        //其他文件夹不可删除
        if (strripos($dir, SSH::$image_save_path) === false) {
            return false;
        }
        try {
            if (!file_exists($dir)) {
                return false;
            }
            if ($dir == '.' || $dir == '..') {
                return false;
            }
            if (!is_dir($dir)) {
                @unlink("$dir");
                return true;
            }
            $handle = opendir($dir);
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    SSH::deleteAllFile("$dir/$file");
                }
            }
            closedir($handle);
            @rmdir($dir);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

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
     * @param int $cpu
     * @param int $memory
     * @param string $name
     * @param string $success_msg
     * @param string $fail_msg
     * @param string $image_name
     * @return array $res
     */
    private function creat($port = 0, $password = '', $port_num = 2, $cpu = 0, $memory = 0, $name = '',  $success_msg = '', $fail_msg = '', $image_name = '')
    {
        if (!$fail_msg) {
            $fail_msg = SSH::$failed_to_create_service_msg;
        }
        if (
            !$port || !is_numeric($port) ||
            !$port_num || !is_numeric($port_num) ||
            !$cpu || !is_numeric($cpu)  ||
            !$memory || !is_numeric($memory)
        ) {
            return [
                'code' => -1,
                'title' => SSH::$title,
                'content' => $fail_msg
            ];
        }
        $port_num = max(SSH::$min_use_port_num, $port_num);
        if (!$success_msg) {
            $success_msg = SSH::$success_to_create_service_msg;
        }
        if (!$image_name) {
            $image_name = SSH::$default_image_name;
        }
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
        $shell = 'echo -e \'' . $password . '\n' . $password . '\' | sudo passwd ltpp;' .
            'sudo service ssh restart;' .
            'rm -rf /path;' .
            'mkdir -p /path/to;' .
            'touch /path/to/config.yaml;' .
            'echo password: ' . $password . ' >> /path/to/config.yaml;' .
            'nohup code-server --bind-addr=0.0.0.0:80 --config /path/to/config.yaml > /dev/null 2>&1 & ' .
            'nohup chmod 777 ' . SSH::$start_shell_path . ' > /dev/null 2>&1 & ' .
            'nohup ' . SSH::$start_shell_path . ' > /dev/null 2>&1 & ' .
            'tail -f /dev/null';
        $docker_cmd = 'docker run --name ' . $name . ' -itd -p ' . $begin_port . ':22 -p ' . $code_sever_port . ':80 -p ' . $no_use_port_begin . '-' . $no_use_end_port . ':' .  $no_use_port_begin . '-' . $no_use_end_port . ' --restart=always --init --memory=' . $memory . 'g --cpus=' . $cpu . ' ' . $image_name . ' /bin/bash -c "' . $shell . '" 2>&1';
        $out = '';
        $this->runExec($docker_cmd, $out);
        if (!$out || !$this->isEnglishAlphabet($out)) {
            $this->runExec('docker rm -f ' . $name);
            return [
                'code' => 0,
                'title' => SSH::$title,
                'content' => $fail_msg
            ];
        }
        return [
            'code' => 1,
            'title' => SSH::$title,
            'content' => $success_msg
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
        $cpu = (string)($request->post('cpu') ?? 0);
        $memory = (string)($request->post('memory') ?? 0);
        if (!$port || !$password || !$name || !$port_num || !$cpu || !$memory || !is_numeric($port) || !is_numeric($port_num) || !is_numeric($cpu) || !is_numeric($memory)) {
            return json([
                'code' => -1,
                'title' => SSH::$title,
                'content' => SSH::$parameter_error_msg
            ]);
        }
        $port_num = max(SSH::$min_use_port_num, $port_num);
        $res = $this->creat($port, $password, $port_num, $cpu, $memory, $name, SSH::$success_to_create_service_msg, SSH::$failed_to_create_service_msg, SSH::$default_image_name);
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
                'code' => -1,
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
                'code' => -1,
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
                'code' => -1,
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
        $image_name = $name . ':' . SSH::$default_image_version;
        // 删除容器
        $docker_cmd = 'docker rm -f ' . $name;
        $out = '';
        $this->runExec($docker_cmd, $out);
        // 删除快照
        $out = '';
        $docker_cmd = 'docker rmi -f ' . $image_name;
        $this->runExec($docker_cmd, $out);
        // 删除快照文件
        $out = '';
        $image_path = SSH::$image_save_path . $name . '.tar';
        $this->deleteAllFile($image_path);
        return json([
            'code' => 1,
            'title' => SSH::$title,
            'content' => SSH::$success_to_delete_service_msg
        ]);
    }

    /**
     * 创建快照
     */
    public function creatImage(Request $request)
    {
        $name = (string)($request->post('name') ?? '');
        // 创建快照目录
        SSH::judgeCreatPath(SSH::$image_save_path);
        // 删除快照文件
        $image_path = SSH::$image_save_path . $name . '.tar';
        $this->deleteAllFile($image_path);
        // 打包当前镜像
        $docker_cmd = 'docker export ' . $name . ' > ' . $image_path;
        $out = '';
        $this->runExec($docker_cmd, $out);
        return json([
            'code' => 1,
            'title' => SSH::$title,
            'content' => SSH::$success_to_create_image_service_msg
        ]);
    }

    /**
     * 回滚快照
     */
    public function backLastImage(Request $request)
    {
        $port = (int)($request->post('port') ?? 0);
        $name = (string)($request->post('name') ?? '');
        $port_num = (int)($request->post('port_num') ?? 0);
        $password = (string)($request->post('password') ?? '');
        $cpu = (string)($request->post('cpu') ?? 0);
        $memory = (string)($request->post('memory') ?? 0);
        if (!$port || !$password || !$name || !$port_num || !$cpu || !$memory || !is_numeric($port) || !is_numeric($port_num) || !is_numeric($cpu) || !is_numeric($memory)) {
            return json([
                'code' => -1,
                'title' => SSH::$title,
                'content' => SSH::$parameter_error_msg
            ]);
        }
        $port_num = max(SSH::$min_use_port_num, $port_num);
        // 无历史快照则返回
        $image_path = SSH::$image_save_path . $name . '.tar';
        if (!file_exists($image_path)) {
            return json([
                'code' => -1,
                'title' => SSH::$title,
                'content' => SSH::$no_back_image_file_service_msg
            ]);
        }
        // 删除容器
        $out = '';
        $docker_cmd = 'docker rm -f ' . $name;
        $this->runExec($docker_cmd, $out);
        // 删除快照
        $out = '';
        $docker_cmd = 'docker rmi -f ' . $name . ':' . SSH::$default_image_version;
        $this->runExec($docker_cmd, $out);
        // 重新加载历史快照
        $out = '';
        $new_image_name = $name . ':' . SSH::$default_image_version;
        $docker_cmd = 'docker import ' . SSH::$image_save_path . $name . '.tar ' .  $new_image_name;
        $this->runExec($docker_cmd, $out);
        // 创建容器
        $res = $this->creat($port, $password, $port_num, $cpu, $memory, $name, SSH::$success_to_back_image_service_msg, SSH::$failed_to_back_image_service_msg, $new_image_name);
        return json($res);
    }

    /**
     * 重置快照
     */
    public function resetImage(Request $request)
    {
        $port = (int)($request->post('port') ?? 0);
        $name = (string)($request->post('name') ?? '');
        $port_num = (int)($request->post('port_num') ?? 0);
        $password = (string)($request->post('password') ?? '');
        $cpu = (float)($request->post('cpu') ?? 0);
        $memory = (float)($request->post('memory') ?? 0);

        if (!$port || !$password || !$name || !$port_num || !$cpu || !$memory || !is_numeric($port) || !is_numeric($port_num) || !is_numeric($cpu) || !is_numeric($memory)) {
            return json([
                'code' => -1,
                'title' => SSH::$title,
                'content' => SSH::$parameter_error_msg
            ]);
        }
        $port_num = max(SSH::$min_use_port_num, $port_num);
        // 重置快照
        $docker_cmd = 'docker rm -f ' . $name;
        $out = '';
        $this->runExec($docker_cmd, $out);
        if (!$out || !$this->isEnglishAlphabet($out)) {
            return json([
                'code' => -1,
                'title' => SSH::$title,
                'content' => SSH::$failed_to_reset_service_msg
            ]);
        }
        $res = $this->creat($port, $password, $port_num, $cpu, $memory, $name, SSH::$success_to_reset_service_msg, SSH::$failed_to_reset_service_msg, SSH::$default_image_name);
        return json($res);
    }
}
