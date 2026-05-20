<?php
$json = json_decode(file_get_contents(__DIR__ . '/report.json'), true);

$meta        = $json['meta'];
$column_keys = $json['data']['pivoted_data']['column_keys'];
$row_groups  = $json['data']['pivoted_data']['row_groups'];

$workload_order = ['(not set)', 'established', 'new'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cloud Infrastructure Reporting Table</title>
    <style>
        body {
            font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 20px;
            color: #1e293b;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            border-radius: 8px;
            background: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            white-space: nowrap;
        }
        thead tr:first-child th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: #334155;
            font-size: 14px;
        }
        thead tr:nth-child(2) th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #475569;
            font-size: 12px;
        }
        .col-sticky-date {
            background-color: #ffffff;
            font-weight: 500;
            vertical-align: top;
            color: #0f172a;
        }
        .col-workload  { font-weight: 600; color: #0f172a; }
        .text-right    { text-align: right; }
        .text-center   { text-align: center; }
        tbody tr:hover { background-color: #f1f5f9; }
        .status-active { color: #0f172a; }
    </style>
</head>
<body>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <?php foreach ($meta['group_rows'] as $gr): ?>
                    <th rowspan="2"><?php echo htmlspecialchars($gr['label']); ?></th>
                <?php endforeach; ?>
                <?php foreach ($column_keys as $ck): ?>
                    <th colspan="<?php echo count($meta['columns']); ?>">
                        <?php echo htmlspecialchars($ck['status'] . ' | ' . $ck['region']); ?>
                    </th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($column_keys as $ck): ?>
                    <?php foreach ($meta['columns'] as $col): ?>
                        <th class="<?php echo in_array($col['key'], ['memory_gb_sum', 'monthly_cost_sum']) ? 'text-right' : 'text-center'; ?>">
                            <?php echo htmlspecialchars($col['label']); ?>
                        </th>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($row_groups as $group): ?>
                <?php
                // Sort rows by $workload_order
                usort($group['rows'], function($a, $b) use ($workload_order) {
                    return array_search($a['workload_type'], $workload_order) <=> array_search($b['workload_type'], $workload_order);
                });
                $row_count = count($group['rows']);
                ?>
                <?php foreach ($group['rows'] as $r_index => $row): ?>
                    <tr>
                        <?php if ($r_index === 0): ?>
                            <td class="col-sticky-date" rowspan="<?php echo $row_count; ?>">
                                <?php echo htmlspecialchars($group['date']); ?>
                            </td>
                        <?php endif; ?>

                        <td class="col-workload"><?php echo htmlspecialchars($row['workload_type']); ?></td>

                        <?php foreach ($row['cells'] as $cell): ?>
                            <?php if ($cell !== null): ?>
                                <?php foreach ($meta['columns'] as $col): ?>
                                    <?php $val = $cell[$col['key']] ?? null; ?>
                                    <?php if (in_array($col['key'], ['memory_gb_sum', 'monthly_cost_sum'])): ?>
                                        <td class="text-right"><?php echo $val !== null ? number_format($val) : '-'; ?></td>
                                    <?php elseif ($col['key'] === 'status'): ?>
                                        <td class="text-center status-active"><?php echo htmlspecialchars($val ?? '-'); ?></td>
                                    <?php else: ?>
                                        <td class="text-center" style="color: #64748b;"><?php echo htmlspecialchars($val ?? '-'); ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($meta['columns'] as $col): ?>
                                    <td class="text-center">-</td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
