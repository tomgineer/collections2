<thead>
    <tr>
        <?php if ($alias === 'arkas'): ?>
            <th class="font-heading text-lg lg:text-xl text-base-content">Series</th>
        <?php endif; ?>
        <th class="font-heading text-lg lg:text-xl text-base-content">Title</th>
        <?php if ($alias !== 'arkas'): ?>
            <th class="font-heading text-lg lg:text-xl text-base-content text-center">Format</th>
        <?php endif; ?>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <?php if ($alias === 'arkas'): ?>
                <td class="font-semibold"><?= esc($item['creator']) ?></td>
            <?php endif; ?>
            <td><?= esc($item['title']) ?></td>
            <?php if ($alias !== 'arkas'): ?>
                <td class="text-center">
                    <span class="badge badge-xs badge-secondary font-bold whitespace-nowrap">
                        <?= esc($item['format'] !== '' ? $item['format'] : '-') ?>
                    </span>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</tbody>
