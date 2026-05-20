<?php
$json      = json_decode(file_get_contents(__DIR__ . '/report.json'), true);
$flat_data = $json['data']['flat_data'];

$matrix   = [];
$regions  = [];
$dates    = [];
$workloads = ['(not set)', 'established', 'new'];

foreach ($flat_data as $row) {
    $matrix[$row['date']][$row['workload_type']][$row['region']] = $row;
    if (!in_array($row['region'], $regions)) $regions[] = $row['region'];
    if (!in_array($row['date'], $dates)) $dates[] = $row['date'];
}

sort($dates);
sort($regions);
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
        /* Styling Header Regional (Baris Pertama) */
        thead tr:first-child th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: #334155;
            font-size: 14px;
            text-align: left;
        }
        /* Styling Sub-Header Kolom (Baris Kedua) */
        thead tr:nth-child(2) th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #475569;
            font-size: 12px;
        }
        /* Kolom Kiri Utama */
        .col-sticky-date {
            background-color: #ffffff;
            font-weight: 500;
            vertical-align: top;
            color: #0f172a;
        }
        .col-workload {
            font-weight: 600;
            color: #0f172a;
        }
        /* Aligment Kolom Sesuai Jenis Data */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Efek Hover untuk Baris */
        tbody tr:hover {
            background-color: #f1f5f9;
        }
        /* Warna Teks Status Active */
        .status-active {
            color: #0f172a;
        }
    </style>
</head>
<body>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 100px;">Date</th>
                <th rowspan="2" style="width: 150px;">Workload Type</th>
                <?php foreach ($regions as $region): ?>
                    <th colspan="4">Active | <?php echo htmlspecialchars($region); ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($regions as $region): ?>
                    <th class="text-right">Memory (GB) (SUM)</th>
                    <th class="text-right">Monthly Cost (SUM)</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Instance ID</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dates as $date): ?>
                <?php foreach ($workloads as $w_index => $workload): ?>
                    <tr>
                        <?php if ($w_index === 0): ?>
                            <td class="col-sticky-date" rowspan="<?php echo count($workloads); ?>">
                                <?php echo htmlspecialchars($date); ?>
                            </td>
                        <?php endif; ?>
                        
                        <td class="col-workload"><?php echo htmlspecialchars($workload); ?></td>
                        
                        <?php foreach ($regions as $region): ?>
                            <?php 
                            // Mengambil data spesifik koordinat [Date][Workload][Region]
                            $cell = $matrix[$date][$workload][$region] ?? null; 
                            ?>
                            
                            <?php if ($cell): ?>
                                <td class="text-right"><?php echo number_format($cell['memory_gb_sum']); ?></td>
                                <td class="text-right"><?php echo number_format($cell['monthly_cost_sum']); ?></td>
                                <td class="text-center status-active"><?php echo htmlspecialchars($cell['status']); ?></td>
                                <td class="text-center" style="color: #64748b;"><?php echo htmlspecialchars($cell['instance_id']); ?></td>
                            <?php else: ?>
                                <td class="text-right">-</td>
                                <td class="text-right">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
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