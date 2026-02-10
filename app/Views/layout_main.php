<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?=base_url();?>">
    <title>Collections :: Own Everything and be Much Happier</title>
    <link rel="stylesheet" href="<?= base_url('css/tailwind.css?v='.SYS_VERSION) ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fonts/arimo/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fonts/firasans/style.css') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('gfx/favicon.svg') ?>">
    <script src="<?=base_url('js/app-dist.js?v='.SYS_VERSION)?>" defer></script>
</head>

<body>
    <div class="drawer">
        <input id="site-nav-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex min-h-screen flex-col">
            <header>
                <?=$this->include('partials/nav.php')?>
            </header>

            <main class="flex-1">
                <?= $this->renderSection('main') ?>
            </main>

            <footer>
                <?=$this->include('partials/footer.php')?>
            </footer>
        </div>

        <?=$this->include('partials/nav_drawer.php')?>
    </div>
</body>

</html>
