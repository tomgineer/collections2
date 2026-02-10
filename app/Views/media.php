<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<section class="max-w-4xl mx-auto p-4 my-12">
    <h1 class="text-5xl lg:text-6xl font-extrabold text-secondary bg-gradient-to-r from-primary to-secondary
                bg-clip-text text-transparent inline-block">
        <?= esc($label) ?>
    </h1>

    <?php if (! empty($media)): ?>
        <div class="overflow-x-auto mt-6">
            <table class="table table-zebra w-full lg:text-base">
                <?php if (in_array($alias, ['cds', 'books'], true)): ?>
                    <?= $this->include('tables/layout_1') ?>
                <?php elseif (in_array($alias, ['blu-rays'], true)): ?>
                    <?= $this->include('tables/layout_2') ?>
                <?php else: ?>
                    <?= $this->include('tables/layout_3') ?>
                <?php endif; ?>
            </table>
        </div>
    <?php else: ?>
        <p class="mt-6">No media found.</p>
    <?php endif; ?>
</section>

<?= $this->include('partials/pagination') ?>
<?= $this->endSection() ?>