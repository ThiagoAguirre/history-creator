<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Esqueci minha senha');
?>
<div class="auth-card">
    <h1>Esqueci minha senha</h1>
    <p class="muted">Informe seu e-mail e enviaremos um link para você criar uma nova senha.</p>

    <?= $this->Form->create(null, ['class' => 'form']) ?>
        <?= $this->Form->control('email', ['label' => 'E-mail', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'autofocus' => true]) ?>
        <?= $this->Form->button('Enviar link', ['class' => 'btn btn-primary btn-block']) ?>
    <?= $this->Form->end() ?>

    <p class="auth-footer"><?= $this->Html->link('← Voltar para o login', ['action' => 'login']) ?></p>
</div>
