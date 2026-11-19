<?php
/**
 * @var string $name
 * @var string $link
 * @var int $ttl
 */
?>
Olá, <?= $name ?>!

Recebemos um pedido para redefinir a senha da sua conta no History Creator.

Acesse o link abaixo para criar uma nova senha:
<?= $link ?>


O link é válido por <?= (int)$ttl ?> minutos. Se você não fez esse pedido, ignore este e-mail.
