<?php
/** @var \App\View\AppView $this */
$this->assign('title', 'Arquivo de Mundos e Histórias');
$identity = $this->request->getAttribute('identity');
?>
<div class="landing-page min-h-screen">
    <header class="landing-header flex flex-wrap items-center justify-between">
        <a class="story-brand" href="<?= $this->Url->build('/') ?>">
            <svg width="58" height="58" viewBox="0 0 64 64" aria-hidden="true">
                <path d="M32 2l4.6 25.4L62 32l-25.4 4.6L32 62l-4.6-25.4L2 32l25.4-4.6z" fill="none" stroke="currentColor" stroke-width="2"></path>
                <path d="M32 14l2.6 15.4L50 32l-15.4 2.6L32 50l-2.6-15.4L14 32l15.4-2.6z" fill="currentColor"></path>
            </svg>
            <span><strong>History Creator</strong><small>Arquivo de Mundos e Histórias</small></span>
        </a>
        <nav class="landing-nav" aria-label="Conta">
            <?php if ($identity) : ?>
                <span>Olá, <?= h($identity->get('name')) ?></span>
                <?= $this->Form->postLink('Sair', ['controller' => 'Users', 'action' => 'logout'], ['class' => 'landing-nav-fill']) ?>
            <?php else : ?>
                <?= $this->Html->link('Entrar', ['controller' => 'Users', 'action' => 'login'], ['class' => 'landing-nav-outline']) ?>
                <?= $this->Html->link('Criar conta', ['controller' => 'Users', 'action' => 'register'], ['class' => 'landing-nav-fill']) ?>
            <?php endif; ?>
        </nav>
    </header>
    <main class="landing-main mx-auto w-full max-w-7xl">
        <section class="landing-hero">
            <div class="landing-copy">
                <p class="landing-eyebrow">CRIE. ORGANIZE. CONTE.</p>
                <h1>Dê vida às suas <em>histórias</em>.</h1>
                <p class="landing-lead">Crie personagens, construa mundos e organize capítulos da sua narrativa em um só lugar.</p>
                <div class="landing-actions">
                    <?= $this->Html->link('Começar agora', ['controller' => 'Users', 'action' => 'register'], ['class' => 'landing-primary']) ?>
                    <?= $this->Html->link('Já tenho conta', ['controller' => 'Users', 'action' => 'login'], ['class' => 'landing-secondary']) ?>
                </div>
            </div>
            <div class="landing-art">
                <img src="/img/reference/story-world.png" alt="Livro de histórias, mapa e árvore fantástica" width="1200" height="900">
            </div>
        </section>
        <section class="landing-features grid grid-cols-1 gap-6 md:grid-cols-3" aria-label="Recursos">
            <article class="landing-feature"><span aria-hidden="true">▤</span><h2>Capítulos</h2><p>Escreva e reorganize capítulos sem perder o fio da narrativa.</p></article>
            <article class="landing-feature"><span aria-hidden="true">♧</span><h2>Personagens</h2><p>Guarde perfis, motivações e relações de cada personagem.</p></article>
            <article class="landing-feature"><span aria-hidden="true">✦</span><h2>Mundos</h2><p>Mapeie lugares, linhas do tempo e as regras do seu universo.</p></article>
        </section>
    </main>
</div>
