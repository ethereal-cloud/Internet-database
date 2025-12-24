# 部署脚本（Windows PowerShell）
# 功能：从 data\team\yii-advanced-app-2.0.32-test\advanced 复制项目到 htdocs，生成 main-local.php，建库导入 SQL，运行 init/migrate。
# 使用：右键“以 PowerShell 运行”或终端执行：powershell -ExecutionPolicy Bypass -File .\deploy.ps1
# 前置：助教已启动 XAMPP 的 Apache/MySQL。

# 0) 收集输入（仅输入 XAMPP 根路径）
$defaultXampp = "D:\xampp"
$defaultTargetSuffix = "yii-advanced-app-2.0.32-test\advanced"
$defaultDbHost = "127.0.0.1"
$defaultDbPort = 3307
$defaultDbName = "pet_boarding_test"
$defaultDbUser = "root"
$defaultDbPass = ""

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$xamppRoot = Read-Host "请输入 XAMPP 根路径（回车默认 $defaultXampp）"
if (-not $xamppRoot) { $xamppRoot = $defaultXampp }
$dataRoot = $ScriptDir

$htdocsRoot = Join-Path $xamppRoot "htdocs"
$targetDir = Join-Path $htdocsRoot $defaultTargetSuffix
$mysqlPath = Join-Path $xamppRoot "mysql\bin\mysql.exe"
$phpExe = Join-Path $xamppRoot "php\php.exe"
$dbHost = $defaultDbHost
$dbPortStr = Read-Host "请输入数据库端口（回车默认 $defaultDbPort）"
if (-not $dbPortStr) { $dbPortStr = $defaultDbPort }
$dbPort = [int]$dbPortStr
$dbName = $defaultDbName
$dbUser = Read-Host "请输入数据库用户名（回车默认 $defaultDbUser）"
if (-not $dbUser) { $dbUser = $defaultDbUser }
$dbPass = Read-Host "请输入数据库密码（回车默认空）"
if (-not $dbPass) { $dbPass = $defaultDbPass }

# 1) 源/目标路径
$sourceDir = Join-Path $dataRoot "team\yii-advanced-app-2.0.32-test\advanced"
if (-not (Test-Path $sourceDir)) {
    Write-Warning "未找到源目录: $sourceDir"
    exit 1
}
Write-Host "源目录: $sourceDir"
Write-Host "目标目录: $targetDir"

# 如目标已存在，提示覆盖
if (Test-Path $targetDir) {
    $resp = Read-Host "目标已存在，是否覆盖？(y/N)"
    if ($resp -notin @('y','Y')) {
        Write-Host "已取消。"
        exit 0
    }
    Write-Host "清理目标目录..."
    Remove-Item -Recurse -Force -ErrorAction SilentlyContinue "$targetDir\*"
} else {
    New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
}

# 2) 复制项目文件
Write-Host "复制项目文件..."
Copy-Item -Path "$sourceDir\*" -Destination $targetDir -Recurse -Force

# 3) 使用 XAMPP 下的 mysql.exe
if (-not (Test-Path $mysqlPath)) {
    Write-Warning "未找到 mysql.exe: $mysqlPath"
    exit 1
}
Write-Host "使用 MySQL 客户端: $mysqlPath"
# 检查 php.exe
if (-not (Test-Path $phpExe)) {
    Write-Warning "未找到 php.exe: $phpExe，请确认 XAMPP 路径。"
}

# 4) 生成 main-local.php
$configPath = Join-Path $targetDir "common\config\main-local.php"
$configContent = @"
<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=$dbHost;port=$dbPort;dbname=$dbName',
            'username' => '$dbUser',
            'password' => '$dbPass',
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
    ],
];
"@
Write-Host "写入配置: $configPath"
Set-Content -Path $configPath -Value $configContent -Encoding UTF8

# 5) 创建数据库并导入 install.sql
$installSql = Join-Path $dataRoot "install.sql"
if (-not (Test-Path $installSql)) {
    Write-Warning "未找到 SQL 文件: $installSql"
    exit 1
}

Write-Host "创建数据库 $dbName（若不存在）..."
& $mysqlPath "-h$dbHost" "-P$dbPort" "-u$dbUser" "--password=$dbPass" -e "CREATE DATABASE IF NOT EXISTS ``$dbName`` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if ($LASTEXITCODE -ne 0) {
    Write-Warning "创建数据库失败，请检查账号/端口/权限。"
    exit 1
}

Write-Host "导入 SQL: $installSql"
cmd /c """$mysqlPath"" -h$dbHost -P$dbPort -u$dbUser --password=$dbPass $dbName < ""$installSql"""
if ($LASTEXITCODE -ne 0) {
    Write-Warning "导入 SQL 失败，请检查路径或权限。"
    exit 1
}

# 6) 运行 init 和迁移
Push-Location $targetDir
try {
    if (Test-Path ".\init.bat") {
        Write-Host "执行 init.bat..."
        cmd /c init.bat
    }
    # 根据需求，migrate 已包含在 install.sql 中，这里不再执行 php yii / migrate
    Write-Host "已跳过 php yii / migrate（install.sql 已包含结构和数据）。"
} finally {
    Pop-Location
}

Write-Host "部署完成。前台: http://localhost/yii-advanced-app-2.0.32-test/advanced/frontend/web/"
Write-Host "后台:   http://localhost/yii-advanced-app-2.0.32-test/advanced/backend/web/"
Read-Host "按回车键退出"
