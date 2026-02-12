<thead>
    <tr>
        <th class="font-heading text-lg lg:text-xl text-base-content">Title</th>
        <th>&nbsp;</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td class="font-semibold"><?= esc($item['title']) ?></td>
            <td class="text-right">
                <a
                    class="btn btn-sm btn-circle btn-soft btn-info"
                    href="<?= esc($item['search_query']) ?>"
                    target="_blank"
                    rel="noopener noreferrer">

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-8">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9h.01" />
                        <path d="M11 12h1v4h1" />
                    </svg>
                </a>
            </td>

        </tr>
    <?php endforeach; ?>
</tbody>