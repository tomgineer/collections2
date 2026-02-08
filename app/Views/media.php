<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<section class="max-w-4xl mx-auto p-4 my-12">
    <h1 class="text-4xl text-secondary"><?= esc($label) ?></h1>

    <?php if (! empty($media)): ?>
        <div class="overflow-x-auto mt-6">
            <table class="table table-zebra w-full text-base">
                <thead>
                    <tr>
                        <?php if ($layout === 'one'): ?>
                            <th>Creator</th>
                            <th>Title</th>
                        <?php elseif ($layout === 'two'): ?>
                            <th>Title</th>
                        <?php elseif ($layout === 'three'): ?>
                            <th>Title</th>
                            <th>Collection</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($media as $item): ?>
                        <tr>
                            <?php if ($layout === 'one'): ?>
                                <td><?= esc($item['creator']) ?></td>
                                <td><?= esc($item['title']) ?></td>
                            <?php elseif ($layout === 'two'): ?>
                                <td><?= esc($item['title']) ?></td>
                            <?php elseif ($layout === 'three'): ?>
                                <td><?= esc($item['title']) ?></td>
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

<?= $this->include('partials/pagination') ?>
<?= $this->endSection() ?>