<thead>
    <tr>
        <th><?=($alias==='cds'?'Artist':'Author')?></th>
        <th>Title</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td><?= esc($item['creator']) ?></td>
            <td><?= esc($item['title']) ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>