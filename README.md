# LTPP-SSH

> **LTPP 内网穿透组件** —— 配合 [主服务 eastspire/LTPP](https://github.com/eastspire/LTPP) 使用
>
> 基于 [PHP webman](https://www.workerman.net/doc/webman/) 框架，通过 SSH 隧道把本地 LTPP 服务暴露到公网。

---

## 📖 项目定位

`LTPP-SSH` 是 LTPP 生态中的**内网穿透组件**，解决"开发机 / 家用机没有公网 IP / 80 端口"的问题：

```
┌──────────┐    SSH 反向隧道     ┌────────────────────┐
│ 本地 LTPP │  ◀───────────────▶ │ 公网 VPS (ltpp.vip) │
│ :47272   │    LTPP-SSH 守护    │  :40022 → 公网入口   │
└──────────┘                    └────────────────────┘
```

典型场景：开发者在家里 / 公司内网跑 LTPP 主服务，本组件在公网 VPS 上维持一条 SSH 反向隧道，让学员可以通过公网域名访问到开发者本地的 LTPP 平台。

部署、启动本组件的方式见 [`eastspire/LTPP`](https://github.com/eastspire/LTPP) 主仓的 `install.sh` —— `--component all` 会一并拉起主服务 + 判题机 + SSH 隧道。

---

## 🛠️ 技术栈

- **语言**：PHP ≥ 7.2
- **Web 框架**：[`workerman/webman-framework ^1.5.0`](https://github.com/walkor/webman)
- **核心依赖**：
  - `monolog/monolog ^2.9.2` —— 日志
  - `webman/console ^1.3` —— webman 命令行
- **可选扩展**：`ext-event`（建议安装，高并发下显著降低 CPU 占用）
- **打包工具**：`webman/console` 内置的 `build:bin`（基于 [`php-micro`](https://github.com/easysoft/php-micro)），打成单文件二进制

---

## 📦 仓库结构

```
LTPP-SSH/
├── start.php                # webman 启动入口（开发模式）
├── windows.bat              # Windows 启动脚本
├── windows.php              # Windows 启动包装
├── composer.json            # 依赖声明（name: ltpp/ltpp-ssh, keywords: ltpp, ssh）
├── composer.lock
├── webman                   # webman CLI 入口
├── app/
│   ├── controller/
│   │   └── SSH.php          #   SSH 隧道业务控制器（21 KB）
│   ├── middleware/
│   │   ├── CrossDomain.php  #   跨域中间件
│   │   └── StaticFile.php   #   静态文件服务
│   ├── view/                #   视图组件（app\View\Components）
│   └── functions.php
├── process/
│   └── Monitor.php          #   看门狗进程（FileMonitor，监控文件热重载）
├── config/                  # webman 17 个配置文件
│   ├── app.php / autoload.php / bootstrap.php
│   ├── container.php / database.php / dependence.php
│   ├── exception.php / log.php / middleware.php
│   ├── plugin/ / process.php / redis.php / route.php
│   ├── server.php / session.php / static.php
│   ├── translation.php / view.php
├── support/                 # 自定义支持层
│   ├── bootstrap.php
│   ├── helpers.php          #   全局辅助函数
│   ├── LTPPErrorHandler.php
│   ├── Request.php / Response.php
├── sh/                      # 运维脚本
│   ├── init.sh              #   初始化（首次部署）
│   ├── bin_build.sh         #   编译单文件二进制（php webman build:bin 8.2）
│   ├── bin_up.sh            #   scp 推到 root@ltpp.vip
│   └── push.sh
├── build/                   # 编译产物（不入版本控制，但提供下载）
│   ├── LTPP-SSH             #   Linux 单文件二进制（≈ 31 MB）
│   ├── LTPP-SSH.phar
│   ├── php8.2.micro.sfx
│   └── php8.2.micro.sfx.zip
├── public/                  # webman 静态资源
├── vendor/                  # composer 依赖
├── LICENSE                  # MIT
└── README.md
```

---

## 🚀 快速开始

### 开发模式

```bash
composer install
php start.php start
```

### 生产模式（单文件二进制）

```bash
composer install
php webman build:bin 8.2
# 产物在 build/LTPP-SSH（≈ 31 MB）

./build/LTPP-SSH start -d
./build/LTPP-SSH stop
./build/LTPP-SSH status
```

### 集成部署（推荐）

```bash
curl -fLO https://raw.githubusercontent.com/eastspire/LTPP/master/install.sh
sudo bash install.sh --component all --yes
```

> `install.sh` 启动 LTPP-SSH 时，需要把 SSH 公钥加到公网 VPS（默认 `root@ltpp.vip`）的 `authorized_keys`，
> 具体方式见 [docs.ltpp.vip/LTPP-SHARE/LTPP-SSH](https://docs.ltpp.vip/LTPP-SHARE/LTPP-SSH)。

---

## ⚙️ 配置

主控制器 `app/controller/SSH.php` —— SSH 隧道的业务逻辑入口。
守护进程 `process/Monitor.php` —— webman 自带 FileMonitor，负责文件变更热重载。

具体配置项、SSH 密钥注册流程、隧道转发规则，参见文档站 [docs.ltpp.vip/LTPP-SHARE/LTPP-SSH](https://docs.ltpp.vip/LTPP-SHARE/LTPP-SSH)。

---

## 🩺 常见问题

- **隧道断了不重连**：`LTPP-SSH` 是 webman 长驻进程，建议用 `systemd` / `supervisor` 守护；非守护情况下意外退出需手动重启。
- **`ext-event` 未安装**：高并发下 CPU 占用偏高；Ubuntu 装 `php8.2-event`，CentOS 装对应 `event` 扩展后重启即可。
- **`php webman build:bin` 编译失败**：要求 PHP 8.2 + `phar.readonly=0`。
- **公网访问 502**：检查 `app/controller/SSH.php` 的本地端口配置与 LTPP 主服务实际监听端口（默认 47272）是否一致。

---

## 🧩 与 LTPP 生态的关系

- 主服务：[`eastspire/LTPP`](https://github.com/eastspire/LTPP) —— 业务核心
- 判题机：[`eastspire/LTPP-CODE-RUN`](https://github.com/eastspire/LTPP-CODE-RUN)
- 内网穿透：**本仓 `LTPP-SSH`**
- 桌面客户端：[`eastspire/VUE-EXE`](https://github.com/eastspire/VUE-EXE)
- 文档站：[docs.ltpp.vip/LTPP-SHARE/LTPP-SSH](https://docs.ltpp.vip/LTPP-SHARE/LTPP-SSH)

---

## 📜 版权

主程序、配置与文档版权归原作者 [eastspire](https://github.com/eastspire) 所有。许可证见 [LICENSE](./LICENSE)。
