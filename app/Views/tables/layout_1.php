<thead>
    <tr>
        <th class="font-heading text-lg lg:text-xl text-base-content"><?= ($alias === 'music' ? 'Artist' : 'Author') ?></th>
        <th class="font-heading text-lg lg:text-xl text-base-content">Title</th>
        <th class="font-heading text-lg lg:text-xl text-base-content text-center">Format</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td class="font-semibold">
                <?= esc($item['creator']) ?>
            </td>
            <td class="text-base-content/80"><?= esc($item['title']) ?></td>
            <td class="text-center">
                <span class="badge badge-xs badge-secondary font-bold"><?= esc($item['format'] !== '' ? $item['format'] : '-') ?></span>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
