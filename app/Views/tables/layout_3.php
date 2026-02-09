<thead>
    <tr>
        <th>Title</th>
        <th>Collection</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td><?= esc($item['title']) ?></td>
            <td><?= esc($item['collection']) ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>