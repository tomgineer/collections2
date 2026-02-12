<thead>
    <tr>
        <th class="font-heading text-lg lg:text-xl text-base-content">Title</th>
        <?php if ($alias === 'blu-rays'): ?>
            <th>&nbsp;</th>
        <?php endif; ?>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td class="font-semibold"><?= esc($item['title']) ?></td>
            <?php if ($alias === 'blu-rays'): ?>
                <?php
                    $searchQuery = trim($item['title'] . ' movie');
                    $googleUrl = 'https://www.google.com/search?' . http_build_query(['q' => $searchQuery]);
                ?>
                <td class="text-right">
                    <a
                        class="btn btn-sm btn-circle btn-soft btn-info"
                        href="<?= esc($googleUrl, 'attr') ?>"
                        target="_blank"
                        rel="noopener noreferrer">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-8">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 9h.01" />
                            <path d="M11 12h1v4h1" />
                        </svg>
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</tbody>
