<?php
/**
 * @var \App\View\AppView $this
 * @var array<string, string> $socialProviders
 */
$this->assign('title', 'Entrar');
?>
<section class="auth-card" aria-labelledby="login-title">
    <div class="auth-card-mark" aria-hidden="true">
        <span></span>
        <svg width="54" height="54" viewBox="0 0 64 64"><path d="M32 2l4.6 25.4L62 32l-25.4 4.6L32 62l-4.6-25.4L2 32l25.4-4.6z" fill="#fbf7f6" stroke="currentColor" stroke-width="2"></path><path d="M32 14l2.6 15.4L50 32l-15.4 2.6L32 50l-2.6-15.4L14 32l15.4-2.6z" fill="currentColor"></path></svg>
        <span></span>
    </div>
    <div class="auth-heading">
        <h1 id="login-title">Bem-vindo <em>de volta!</em></h1>
        <p>Acesse sua conta e continue sua jornada</p>
    </div>
    <?= $this->Form->create(null, ['class' => 'auth-form']) ?>
        <?= $this->Form->control('email', ['label' => 'E-mail', 'type' => 'email', 'required' => true, 'autocomplete' => 'username', 'placeholder' => 'Seu e-mail', 'class' => 'field']) ?>
        <?= $this->Form->control('password', ['label' => 'Senha', 'type' => 'password', 'required' => true, 'autocomplete' => 'current-password', 'placeholder' => 'Sua senha', 'class' => 'field']) ?>
        <div class="auth-options">
            <label><?= $this->Form->checkbox('remember', ['hiddenField' => false]) ?> <span>Lembrar de mim</span></label>
            <?= $this->Html->link('Esqueceu sua senha?', ['action' => 'forgotPassword']) ?>
        </div>
        <button class="auth-submit" type="submit"><span aria-hidden="true">↪</span> Entrar</button>
    <?= $this->Form->end() ?>
    <?= $this->element('social_login') ?>
    <p class="auth-switch">Ainda não tem uma conta? <?= $this->Html->link('Criar conta ›', ['action' => 'register']) ?></p>
</section>
