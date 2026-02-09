<thead>
    <tr>
        <th>Title</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td><?= esc($item['title']) ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>