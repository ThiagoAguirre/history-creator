<?php
/**
 * @var \App\View\AppView $this
 */
$identity = $this->request->getAttribute('identity');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History Creator<?= $this->fetch('title') ? ' · ' . h($this->fetch('title')) : '' ?></title>
    <?= $this->Html->meta('icon') ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <?= $this->Html->css(['normalize.min', 'app', 'tailwind']) ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="<?= $this->Url->build('/') ?>">
            <span class="brand-mark" aria-hidden="true">✦</span> History Creator
        </a>
        <nav class="topbar-links">
            <?php if ($identity) : ?>
                <span class="topbar-user">Olá, <?= h($identity->get('name')) ?></span>
                <?= $this->Form->postLink('Sair', ['controller' => 'Users', 'action' => 'logout'], ['class' => 'btn btn-ghost']) ?>
            <?php else : ?>
                <?= $this->Html->link('Entrar', ['controller' => 'Users', 'action' => 'login'], ['class' => 'btn btn-ghost']) ?>
                <?= $this->Html->link('Criar conta', ['controller' => 'Users', 'action' => 'register'], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?>
        </nav>
    </header>
    <main class="main">
        <div class="flash-area"><?= $this->Flash->render() ?></div>
        <?= $this->fetch('content') ?>
    </main>
    <footer class="footer">© <?= date('Y') ?> History Creator</footer>
</body>
</html>
