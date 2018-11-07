<?php $this->layout('layout') ?>

<section class="hero is-info is-dark">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                Поделитесь красочными снимками!
            </h1>
            <h2 class="subtitle">
                Вход в систему.
            </h2>
        </div>
    </div>
</section>

<section class="section main-content">
<div class="container main-content">
    <div class="columns">
        <div class="column"></div>
        <div class="column is-quarter auth-form">
            <?php echo flash(); ?>
            <form action="/login" method="POST">
            <div class="field">
                <label class="label">Email</label>
                <div class="control has-icons-left has-icons-right">
                    <input class="input" type="email" name="email">  <!-- is-danger -->
                    <span class="icon is-small is-left">
                  <i class="fas fa-envelope"></i>
                </span>
                    <!-- <span class="icon is-small is-right">
                      <i class="fas fa-exclamation-triangle"></i>
                    </span> -->
                </div>
                <!-- <p class="help is-danger">This email is invalid</p> -->
            </div>

            <div class="field">
                <label class="label">Пароль</label>
                <div class="control has-icons-left has-icons-right">
                    <input class="input" type="password" name="password">
                    <span class="icon is-small is-left">
                  <i class="fas fa-lock"></i>
                </span>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox"  name="remember">
                        Запомнить меня
                    </label>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button class="button is-link">Войти</button>
                </div>
                <div class="control">
                    <a class="button is-text" href="/">Отмена</a>
                </div>
            </div>
            <div class="field">
                <p>Забыли пароль? <b><a href="/password-recovery">Восстановление пароля</a></b></p>
                <p>Не пришло письмо подтверждения? <b><a href="/email-verification">Переотправить</a></b></p>
            </div>
            </form>
        </div>
        <div class="column"></div>
    </div>
</div>
</section>