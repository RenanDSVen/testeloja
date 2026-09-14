<?php
function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $path=''): string {
    $base = rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return ($base==='/'?'':$base).'/'.ltrim($path,'/');
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf']??'', $_POST['csrf']??'')) { http_response_code(419); exit('Sessão expirada. Volte e tente novamente.'); }
}
function flash(string $type,string $message): void { $_SESSION['flash']=[$type,$message]; }
function redirect(string $to): never { header('Location: '.$to); exit; }
function money(float|string|null $v): string { return 'R$ '.number_format((float)$v,2,',','.'); }
function decimal(string|float|int|null $v): float {
    if (is_numeric($v)) return (float)$v;
    return (float)str_replace(',','.',str_replace('.','',(string)$v));
}
function app_setting(string $key, mixed $default=null): mixed {
    global $pdo;
    static $cache=[];
    if (array_key_exists($key,$cache)) return $cache[$key];
    try { $s=$pdo->prepare('SELECT valor FROM configuracoes WHERE chave=?'); $s->execute([$key]); $v=$s->fetchColumn(); }
    catch(Throwable){ return $default; }
    return $cache[$key]=$v!==false?$v:$default;
}
function audit(string $action,string $table='',?int $id=null,mixed $before=null,mixed $after=null): void {
    global $pdo;
    try {
        $s=$pdo->prepare('INSERT INTO auditoria(usuario_id,acao,tabela,registro_id,dados_anteriores,dados_posteriores,ip,user_agent) VALUES(?,?,?,?,?,?,?,?)');
        $s->execute([$_SESSION['user']['id']??null,$action,$table,$id,$before?json_encode($before,JSON_UNESCAPED_UNICODE):null,$after?json_encode($after,JSON_UNESCAPED_UNICODE):null,$_SERVER['REMOTE_ADDR']??null,substr($_SERVER['HTTP_USER_AGENT']??'',0,255)]);
    } catch(Throwable){}
}
function require_login(): void { if (empty($_SESSION['user'])) redirect(url('login.php')); }
function is_admin(): bool { return ($_SESSION['user']['nivel']??'')==='admin'; }
function require_admin(): void { if (!is_admin()) { http_response_code(403); exit('Acesso negado.'); } }
function paginate_count(string $sql,array $params=[]): int { global $pdo; $s=$pdo->prepare($sql);$s->execute($params);return (int)$s->fetchColumn(); }