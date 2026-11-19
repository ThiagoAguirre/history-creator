<?php
/**
 * @var \App\View\AppView $this
 * @var string $name
 * @var string $link
 * @var int $ttl
 */
?>
<p>Olá, <?= h($name) ?>!</p>
<p>Recebemos um pedido para redefinir a senha da sua conta no History Creator.</p>
<p><a href="<?= h($link) ?>">Clique aqui para criar uma nova senha</a></p>
<p>O link é válido por <?= (int)$ttl ?> minutos. Se você não fez esse pedido, ignore este e-mail.</p>
