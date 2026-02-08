<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<section class="max-w-4xl mx-auto p-4 mt-12">
    <h1 class="text-4xl text-secondary"><?= esc($label) ?></h1>

    <?php if (! empty($media)): ?>
        <div class="overflow-x-auto mt-6">
            <table class="table table-zebra w-full text-base">
                <thead>
                    <tr>
                        <?php if (in_array($alias, ['cds', 'books'], true)): ?>
                            <th>Creator</th>
                        <?php endif; ?>
                        <th>Title</th>
                        <?php if (in_array($alias, ['arkas'], true)): ?>
                            <th>Collection</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($media as $item): ?>
                        <tr>
                            <?php if (in_array($alias, ['cds', 'books'], true)): ?>
                                <td><?= esc($item['creator']) ?></td>
                            <?php endif; ?>
                            <td><?= esc($item['title']) ?></td>
                            <?php if (in_array($alias, ['arkas'], true)): ?>
                                <td><?= esc($item['collection']) ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="mt-6">No media found.</p>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
