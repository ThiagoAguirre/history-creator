<?php
/**
 * Social login buttons.
 *
 * @var \App\View\AppView $this
 * @var array<string, string> $socialProviders provider key => label
 */
if (empty($socialProviders)) {
    return;
}
?>
<div class="divider"><span>ou</span></div>
<div class="social-buttons">
    <?php foreach ($socialProviders as $key => $label) : ?>
        <a class="btn btn-social btn-<?= h($key) ?>" href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'socialLogin', $key]) ?>">
            <span class="google-mark" aria-hidden="true">G</span><?= h($label) ?>
        </a>
    <?php endforeach; ?>
</div>
