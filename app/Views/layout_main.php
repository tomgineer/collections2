<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections :: Own Everything and be Much Happier</title>
    <link rel="stylesheet" href="<?= base_url('css/tailwind.css?v='.SYS_VERSION) ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fonts/arimo/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fonts/firasans/style.css') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('gfx/favicon.svg') ?>">
</head>

<body class="flex flex-col min-h-screen">
    <header>
        <?=$this->include('partials/nav.php')?>
    </header>

    <main class="flex-1">
        <?= $this->renderSection('main') ?>
    </main>

    <footer class="mt-24">
        <?=$this->include('partials/footer.php')?>
    </footer>
</body>

</html>
