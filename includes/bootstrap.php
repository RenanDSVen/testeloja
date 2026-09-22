<?php
declare(strict_types=1);
$app=require __DIR__.'/../config/app.php';
$db=require __DIR__.'/../config/database.php';
date_default_timezone_set($app['timezone']);
ini_set('session.use_strict_mode','1');
session_name($app['session_name']);
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax','path'=>'/']);
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
require_once __DIR__.'/functions.php';
$installLock=__DIR__.'/../storage/installed.lock';
$currentScript=basename($_SERVER['SCRIPT_NAME']??'');
$databaseUnavailable=function(PDOException $e): never {
    error_log('Falha no banco de dados da loja: '.$e->getMessage());
    http_response_code(503);
    header('Retry-After: 10');
    header('Content-Type: text/html; charset=UTF-8');
    exit('<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sistema temporariamente indisponível</title><style>body{margin:0;display:grid;min-height:100vh;place-items:center;background:#f4f6fa;font:15px Arial;color:#253047}.box{width:min(460px,calc(100% - 40px));box-sizing:border-box;padding:32px;background:#fff;border-radius:18px;box-shadow:0 15px 45px #17213a18}.icon{display:grid;width:48px;height:48px;place-items:center;border-radius:14px;background:#fff1db;color:#a85b00;font-size:25px}h1{font-size:22px;margin:18px 0 8px}p{color:#647089;line-height:1.5}.btn{display:inline-block;margin-top:10px;padding:11px 17px;border-radius:9px;background:#6d4aff;color:#fff;text-decoration:none;font-weight:700}</style></head><body><main class="box"><div class="icon">!</div><h1>Não foi possível acessar o banco de dados</h1><p>O MySQL pode estar iniciando ou temporariamente indisponível. Seus dados não foram alterados. Aguarde alguns segundos e tente novamente.</p><a class="btn" href="">Tentar novamente</a></main></body></html>');
};
$dsn=sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',$db['host'],$db['port'],$db['database'],$db['charset']);
try {
    $pdo=new PDO($dsn,$db['username'],$db['password'],[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false,
    ]);
} catch(PDOException $e) {
    $driverCode=(int)($e->errorInfo[1]??$e->getCode());
    if($currentScript!=='install.php'&&!is_file($installLock)&&$driverCode===1049) redirect(url('install.php'));
    $databaseUnavailable($e);
}

// O banco é a fonte de verdade. Se o arquivo de controle sumir em uma atualização
// por FTP, ele é recriado automaticamente sem abrir o instalador novamente.
if($currentScript!=='install.php'&&!is_file($installLock)){
    try {
        $pdo->query('SELECT id FROM usuarios LIMIT 1');
        if(!is_dir(dirname($installLock))) @mkdir(dirname($installLock),0775,true);
        @file_put_contents($installLock,date('c'),LOCK_EX);
    } catch(PDOException $e) {
        $driverCode=(int)($e->errorInfo[1]??$e->getCode());
        if($driverCode===1146) redirect(url('install.php'));
        $databaseUnavailable($e);
    }
}
