<?php
declare(strict_types=1);
$app=require __DIR__.'/config/app.php'; $db=require __DIR__.'/config/database.php';
date_default_timezone_set($app['timezone']);
$lock=__DIR__.'/storage/installed.lock'; $error='';
if(is_file($lock)){ header('Location: login.php'); exit; }

// Impede uma nova instalação caso apenas o arquivo de controle tenha sido
// removido durante uma atualização ou envio por FTP.
try{
 $installedPdo=new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',$db['host'],$db['port'],$db['database']),$db['username'],$db['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $adminCount=(int)$installedPdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
 if($adminCount>0){
  if(!is_dir(dirname($lock))) @mkdir(dirname($lock),0775,true);
  @file_put_contents($lock,date('c'),LOCK_EX);
  header('Location: login.php');exit;
 }
}catch(Throwable){}

if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  if(!preg_match('/^[a-zA-Z0-9_]+$/',$db['database'])) throw new RuntimeException('Nome do banco inválido.');
  $pdo=new PDO(sprintf('mysql:host=%s;port=%d;charset=utf8mb4',$db['host'],$db['port']),$db['username'],$db['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
  $pdo->exec("USE `{$db['database']}`");
  $sql=file_get_contents(__DIR__.'/database/schema.sql');
  foreach(preg_split('/;\\s*(?:\\r?\\n|$)/',$sql,-1,PREG_SPLIT_NO_EMPTY) as $statement){
   if(trim($statement)!=='') $pdo->exec($statement);
  }
  $nome=trim($_POST['nome']??'');$usuario=trim($_POST['usuario']??'');$senha=$_POST['senha']??'';
  if($nome===''||!preg_match('/^[a-zA-Z0-9._-]{3,60}$/',$usuario)||strlen($senha)<8) throw new RuntimeException('Informe nome, usuário válido e senha com no mínimo 8 caracteres.');
  $s=$pdo->prepare("INSERT INTO usuarios(nome,usuario,senha_hash,nivel) VALUES(?,?,?,'admin')");
  $s->execute([$nome,$usuario,password_hash($senha,PASSWORD_DEFAULT)]);
  if(!is_dir(__DIR__.'/storage')) mkdir(__DIR__.'/storage',0775,true);
  if(file_put_contents($lock,date('c'),LOCK_EX)===false) throw new RuntimeException('Não foi possível gravar o controle de instalação na pasta storage. Verifique a permissão da pasta.');
  header('Location: login.php?installed=1');exit;
 }catch(Throwable $e){$error=$e->getMessage();}
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalar</title>
<style>body{margin:0;background:#f4f6fa;font:15px Arial;color:#253047}.box{max-width:520px;margin:7vh auto;background:#fff;padding:32px;border-radius:18px;box-shadow:0 15px 45px #17213a18}input{width:100%;box-sizing:border-box;padding:12px;margin:6px 0 16px;border:1px solid #d7ddea;border-radius:9px}button{width:100%;padding:13px;border:0;border-radius:9px;background:#6d4aff;color:#fff;font-weight:700}.err{background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px}</style></head><body><main class="box"><h1><?=htmlspecialchars($app['app_name'])?></h1><p>Crie o primeiro administrador para concluir a instalação.</p>
<?php if($error):?><p class="err"><?=htmlspecialchars($error)?></p><?php endif?><form method="post"><label>Nome completo</label><input name="nome" required><label>Usuário</label><input name="usuario" required minlength="3"><label>Senha</label><input type="password" name="senha" required minlength="8"><button>Instalar sistema</button></form></main></body></html>
