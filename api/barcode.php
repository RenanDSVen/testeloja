<?php
require __DIR__.'/../includes/bootstrap.php';require_login();
header('Content-Type: application/json; charset=utf-8');
$code=trim($_GET['code']??'');
if($code===''){echo json_encode(['exists'=>false]);exit;}
$s=$pdo->prepare("SELECT pv.id,p.nome,c.nome cor,t.nome tamanho,pv.estoque_atual FROM produto_variacoes pv JOIN produtos p ON p.id=pv.produto_id LEFT JOIN cores c ON c.id=pv.cor_id LEFT JOIN tamanhos t ON t.id=pv.tamanho_id WHERE pv.codigo_barras=? LIMIT 1");
$s->execute([$code]);$r=$s->fetch();
echo json_encode(['exists'=>(bool)$r,'item'=>$r?:null],JSON_UNESCAPED_UNICODE);