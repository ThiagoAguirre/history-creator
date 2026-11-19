<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var array<string, string> $socialProviders
 */
$this->assign('title', 'Criar conta');
?>
<section class="auth-card auth-card-register" aria-labelledby="register-title">
    <div class="auth-card-mark" aria-hidden="true">
        <span></span>
        <svg width="54" height="54" viewBox="0 0 64 64"><path d="M32 2l4.6 25.4L62 32l-25.4 4.6L32 62l-4.6-25.4L2 32l25.4-4.6z" fill="#fbf7f6" stroke="currentColor" stroke-width="2"></path><path d="M32 14l2.6 15.4L50 32l-15.4 2.6L32 50l-2.6-15.4L14 32l15.4-2.6z" fill="currentColor"></path></svg>
        <span></span>
    </div>
    <div class="auth-heading">
        <h1 id="register-title">Crie sua <em>conta</em></h1>
        <p>Junte-se a nós e comece sua jornada</p>
    </div>
    <?= $this->Form->create($user, ['class' => 'auth-form']) ?>
        <?= $this->Form->control('name', ['label' => 'Nome completo', 'required' => true, 'autocomplete' => 'name', 'placeholder' => 'Seu nome', 'class' => 'field']) ?>
        <?= $this->Form->control('email', ['label' => 'E-mail', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'placeholder' => 'voce@exemplo.com', 'class' => 'field']) ?>
        <?= $this->Form->control('password', ['label' => 'Senha', 'type' => 'password', 'required' => true, 'value' => '', 'autocomplete' => 'new-password', 'minlength' => 8, 'placeholder' => 'Pelo menos 8 caracteres', 'class' => 'field']) ?>
        <?= $this->Form->control('password_confirm', ['label' => 'Confirmar senha', 'type' => 'password', 'required' => true, 'value' => '', 'autocomplete' => 'new-password', 'placeholder' => 'Digite a senha novamente', 'class' => 'field']) ?>
        <button class="auth-submit" type="submit"><span aria-hidden="true">✦</span> Criar conta</button>
    <?= $this->Form->end() ?>
    <?= $this->element('social_login') ?>
    <p class="auth-switch">Já tem uma conta? <?= $this->Html->link('Entrar ›', ['action' => 'login']) ?></p>
</section>
