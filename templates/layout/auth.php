<?php
/** @var \App\View\AppView $this */
$pageTitle = $this->fetch('title');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle ?: 'Entrar') ?> · History Creator</title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css(['normalize.min', 'app', 'tailwind']) ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body class="auth-page min-h-screen">
    <div class="auth-shell flex min-h-screen flex-col">
        <img class="auth-deco auth-tree" src="/img/reference/tree.png" alt="" aria-hidden="true">
        <img class="auth-deco auth-islands" src="/img/reference/islands.png" alt="" aria-hidden="true">
        <img class="auth-deco auth-foreground" src="/img/reference/foreground.png" alt="" aria-hidden="true">
        <header class="auth-header">
            <a class="story-brand" href="<?= $this->Url->build('/') ?>" aria-label="History Creator, início">
                <svg width="58" height="58" viewBox="0 0 64 64" aria-hidden="true">
                    <path d="M32 2l4.6 25.4L62 32l-25.4 4.6L32 62l-4.6-25.4L2 32l25.4-4.6z" fill="none" stroke="currentColor" stroke-width="2"></path>
                    <path d="M32 14l2.6 15.4L50 32l-15.4 2.6L32 50l-2.6-15.4L14 32l15.4-2.6z" fill="currentColor"></path>
                </svg>
                <span><strong>History Creator</strong><small>Arquivo de Mundos e Histórias</small></span>
            </a>
        </header>
        <main class="auth-main flex flex-1 items-center justify-center">
            <div class="auth-flash"><?= $this->Flash->render() ?></div>
            <?= $this->fetch('content') ?>
        </main>
        <footer class="auth-footer"><span></span>History Creator · Arquivo de Mundos e Histórias<span></span></footer>
    </div>
    <?= $this->fetch('script') ?>
</body>
</html>
