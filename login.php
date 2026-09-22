<?php
require __DIR__.'/includes/bootstrap.php';
if(!empty($_SESSION['user'])) redirect(url('index.php'));
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $usuario=trim($_POST['usuario']??'');$senha=$_POST['senha']??'';
 $s=$pdo->prepare('SELECT * FROM usuarios WHERE usuario=? AND ativo=1 LIMIT 1');$s->execute([$usuario]);$u=$s->fetch();
 if($u&&password_verify($senha,$u['senha_hash'])){
  session_regenerate_id(true);$_SESSION['user']=['id'=>(int)$u['id'],'nome'=>$u['nome'],'usuario'=>$u['usuario'],'nivel'=>$u['nivel']];
  $pdo->prepare('UPDATE usuarios SET ultimo_acesso=NOW() WHERE id=?')->execute([$u['id']]);audit('login','usuarios',(int)$u['id']);redirect(url('index.php'));
 }
 usleep(500000);$error='Usuário ou senha inválidos.';
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar</title><link rel="stylesheet" href="assets/app.css"></head><body class="login-bg"><main class="login-card"><div class="brand-mark">L</div><h1><?=e(app_setting('nome_loja',$app['app_name']))?></h1><p>Acesse o sistema da loja</p><?php if(isset($_GET['installed'])):?><div class="alert success">Instalação concluída.</div><?php endif?><?php if($error):?><div class="alert danger"><?=e($error)?></div><?php endif?><form method="post"><?=csrf_field()?><label>Usuário</label><input name="usuario" autocomplete="username" required autofocus><label>Senha</label><input type="password" name="senha" autocomplete="current-password" required><button class="btn primary block">Entrar</button></form></main></body></html>
