<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $token
 */
$this->assign('title', 'Nova senha');
?>
<div class="auth-card">
    <h1>Criar nova senha</h1>
    <p class="muted">Escolha uma nova senha para <strong><?= h($user->email) ?></strong>.</p>

    <?= $this->Form->create($user, ['class' => 'form']) ?>
        <?= $this->Form->control('password', ['label' => 'Nova senha', 'type' => 'password', 'required' => true, 'value' => '', 'autocomplete' => 'new-password', 'minlength' => 8, 'autofocus' => true]) ?>
        <?= $this->Form->control('password_confirm', ['label' => 'Confirmar nova senha', 'type' => 'password', 'required' => true, 'value' => '', 'autocomplete' => 'new-password']) ?>
        <?= $this->Form->button('Salvar nova senha', ['class' => 'btn btn-primary btn-block']) ?>
    <?= $this->Form->end() ?>
</div>
