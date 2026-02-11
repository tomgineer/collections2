<thead>
    <tr>
        <th>Title</th>
        <th>Collection</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td class="font-semibold"><?= esc($item['title']) ?></td>
            <td class="text-base-content/80"><?= esc($item['collection']) ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>