<?php
/**
 * Tela inicial.
 *
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Início');
$identity = $this->request->getAttribute('identity');
?>
<section class="hero">
    <div class="hero-text">
        <p class="eyebrow">Escreva. Organize. Conte.</p>
        <h1>Dê vida às suas <em>histórias</em>.</h1>
        <p class="lead">
            Crie personagens, construa mundos e acompanhe cada capítulo em um só lugar.
            O History Creator é o seu caderno de escritor, sempre à mão.
        </p>
        <div class="hero-actions">
            <?php if ($identity) : ?>
                <p class="welcome">Bem-vindo(a) de volta, <strong><?= h($identity->get('name')) ?></strong>!</p>
            <?php else : ?>
                <?= $this->Html->link('Começar agora', ['controller' => 'Users', 'action' => 'register'], ['class' => 'btn btn-primary btn-lg']) ?>
                <?= $this->Html->link('Já tenho conta', ['controller' => 'Users', 'action' => 'login'], ['class' => 'btn btn-ghost btn-lg']) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="page page-back"></div>
        <div class="page page-front">
            <span class="page-chapter">Capítulo I</span>
            <span class="line w90"></span>
            <span class="line w75"></span>
            <span class="line w85"></span>
            <span class="line w60"></span>
            <span class="line w80"></span>
        </div>
    </div>
</section>

<section class="features">
    <article class="feature">
        <span class="feature-icon">✎</span>
        <h3>Capítulos</h3>
        <p>Escreva e reorganize capítulos sem perder o fio da narrativa.</p>
    </article>
    <article class="feature">
        <span class="feature-icon">☺</span>
        <h3>Personagens</h3>
        <p>Guarde perfis, motivações e relações de cada personagem.</p>
    </article>
    <article class="feature">
        <span class="feature-icon">⌖</span>
        <h3>Mundos</h3>
        <p>Mapeie lugares, linhas do tempo e as regras do seu universo.</p>
    </article>
</section>
