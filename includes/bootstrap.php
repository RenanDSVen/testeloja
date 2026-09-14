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
$dsn=sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',$db['host'],$db['port'],$db['database'],$db['charset']);
try {
    $pdo=new PDO($dsn,$db['username'],$db['password'],[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false,
    ]);
} catch(PDOException $e) {
    if(basename($_SERVER['SCRIPT_NAME']??'')!=='install.php') redirect(url('install.php'));
    throw $e;
}