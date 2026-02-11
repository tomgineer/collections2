<thead>
    <tr>
        <th>Title</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td class="font-semibold"><?= esc($item['title']) ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>