<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }
        h1 {
            margin-bottom: 2rem;
            color: #333;
        }
        .button-container {
            display: flex;
            gap: 1rem;
        }
        a.btn {
            padding: 0.75rem 1.5rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        a.btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Report Dashboard</h1>
    <div class="button-container">
        <a class="btn" href="report_flat.php">Report Flat</a>
        <a class="btn" href="report_pivoted.php">Report Pivoted</a>
    </div>
</body>
</html>
