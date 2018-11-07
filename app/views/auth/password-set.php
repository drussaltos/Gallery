<?php $this->layout('layout') ?>

<section class="hero is-dark">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                Восстановление пароля.
            </h1>
            <h2 class="subtitle">
                Введите новый пароль.
            </h2>
        </div>
    </div>
</section>
<div class="container main-content">
    <div class="columns">
        <div class="column"></div>
        <div class="column is-quarter auth-form">
            <?= flash(); ?>
            <form action="/password-recovery/change" method="post">
                <input type="hidden" name="selector" value="<?= $data['selector'];?>">
                <input type="hidden" name="token" value="<?= $data['token'];?>">
                <div class="field">
                    <label class="label">Новый пароль</label>
                    <div class="control has-icons-left has-icons-right">
                        <input class="input" type="password" name="password">  <!-- is-danger -->
                        <span class="icon is-small is-left">
                      <i class="fas fa-lock"></i>
                    </span>
                        <!-- <span class="icon is-small is-right">
                          <i class="fas fa-exclamation-triangle"></i>
                        </span> -->
                    </div>
                    <!-- <p class="help is-danger">This email is invalid</p> -->
                </div>

                <div class="field">
                    <label class="label">Подтвердите пароль</label>
                    <div class="control has-icons-left has-icons-right">
                        <input class="input" type="password" name="password_confirmation">
                        <span class="icon is-small is-left">
                      <i class="fas fa-lock"></i>
                    </span>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit">Отправить</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="column"></div>
    </div>
</div>
