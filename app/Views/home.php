<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<section>
    <h1>Hello World!</h1>

    <?php if ($status):?>
        <h1>Nai malaka</h1>
    <?php else:?>
        <h1>Oxi malaka</h1>
    <?php endif;?>
</section>

<?= $this->endSection() ?>
