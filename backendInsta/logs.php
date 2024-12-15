<?php
include '../config.php';

// Consultar os logs
$logs_query = "SELECT * FROM message_logs ORDER BY created_at DESC";
$logs_result = $conn->query($logs_query);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }
        .container {
            width: 80%;
            max-width: 800px;
        }
        .log-item {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .log-item p {
            margin: 0.5rem 0;
        }
        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .no-logs {
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Histórico de Logs</h1>

    <?php if ($logs_result->num_rows > 0): ?>
        <?php while ($log = $logs_result->fetch_assoc()): ?>
            <div class="log-item">
                <p><strong>Status:</strong> <?php echo htmlspecialchars($log['status']); ?></p>
                <p><strong>Mensagem:</strong> <?php echo htmlspecialchars($log['message']); ?></p>
                <p><strong>Data:</strong> <?php echo $log['created_at']; ?></p>
                <?php if (isset($log['error'])): ?>
                    <p><strong>Erro:</strong> <?php echo htmlspecialchars($log['error']); ?></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-logs">Nenhum log encontrado.</p>
    <?php endif; ?>
</div>

</body>
</html>
