<?php $this->layout('layout') ?>


<section class="hero is-primary">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                Моя галерея
            </h1>
            <h2 class="subtitle">
                Загруженные вами картинки
            </h2>
        </div>
    </div>
</section>
<section class="section main-content">
    <div class="container">

        <div class="columns  is-multiline">
            <?php foreach($photos as $photo):?>
                <div class="column is-one-quarter">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <a href="/photos/<?= $photo['id'];?>">
                                    <img src="<?= getImage($photo['image']);?>">
                                </a>
                            </figure>
                        </div>
                        <div class="card-content">
                            <div class="media my-photo">
                                <div class="media-left">
                                    <p class="title is-5">
                                        <a href="/photos/<?= $photo['id'];?>/edit" class="button is-warning">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="/photos/<?= $photo['id'];?>/delete" class="button is-danger" onclick="confirm('Вы уверены?');">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>


    </div>
</section>