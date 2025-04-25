<?php

// Connessione al database usando PDO (PHP Data Objects)
$config = require 'config.php';

$pdo = new PDO(
    "mysql:host={$config['host']};dbname={$config['dbname']}",
    $config['user'],
    $config['password']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Gestione CRUD attraverso le richieste HTTP.
// La visualizzazione della pagina è in GET.
// Le richieste di azioni sugli elementi sono in POST, con action (endpoint) "add", "edit", "delete", che compiono le azioni con le query preparate.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO tasks (title, status) VALUES (?, ?)");
            $stmt->execute([$_POST['title'], $_POST['status']]);
        } elseif ($_POST['action'] === 'edit') {
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, status = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['status'], $_POST['id']]);
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$_POST['id']]);
        }
    }
    header("Location: index.php"); // Reindirizza alla pagina chiamando in GET.
    exit(); // Esce dall'esecuzione del codice in caso di POST (caricherà GET a breve a causa di header)
}

// Recupera lista attività in caso la pagina sia stata richiesta in GET
$filterClosed = isset($_GET['showClosed']) && $_GET['showClosed'] == '1' ? '' : "WHERE status != 'closed'";
$tasks = $pdo->query("SELECT * FROM tasks $filterClosed ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="container py-5">
<h1>Task Manager</h1>
<button class="btn btn-success my-3" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="openAddModal()">Aggiungi Attività</button>

<?php
$showClosed = isset($_GET['showClosed']) && $_GET['showClosed'] == '1';
$newValue = $showClosed ? 0 : 1;
?>
<a href="?showClosed=<?= $newValue ?>" class="btn btn-secondary">
    Mostra/Nascondi chiuse
</a>

<!-- Tabella principale con i dati -->
<table class="table table-bordered" id="taskTable">
    <thead>
    <tr>
        <th onclick="sortTable(0)">Titolo</th>
        <th onclick="sortTable(1)">Stato</th>
        <th>Azioni</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tasks as $task): ?>
        <tr>
            <td><?= htmlspecialchars($task['title']) ?></td>
            <td><?= htmlspecialchars($task['status']) ?></td>
            <td>
                <!-- Il button apre il div col form per la modifica. -->
                <button class="btn btn-warning btn-sm" onclick='openEditModal(<?= json_encode($task) ?>)'>Modifica</button>
                <!-- Il form invece invia direttamente la richiesta di cancellazione (dopo il return confirm). -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Sei sicuro?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                    <button class="btn btn-danger btn-sm">Elimina</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal con form per inserimento e modifica -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Gestione Attività</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="taskId">
                    <div class="mb-3">
                        <label class="form-label">Titolo</label>
                        <input type="text" name="title" class="form-control" id="taskTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stato</label>
                        <select name="status" class="form-control" id="taskStatus">
                            <option value="open">open</option>
                            <option value="closed">closed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('formAction').value = 'add';
        document.getElementById('taskId').value = '';
        document.getElementById('taskTitle').value = '';
        document.getElementById('taskStatus').value = 'open';
        //Non ha bisogno di aprire nuovo bootstrap modal perché l'elemento button ha già  -> data-bs-target="#taskModal"
    }
    function openEditModal(task) {
        document.getElementById('formAction').value = 'edit';
        document.getElementById('taskId').value = task.id;
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskStatus').value = task.status;
        new bootstrap.Modal(document.getElementById('taskModal')).show();
    }
    function sortTable(col) {
        const table = document.getElementById("taskTable");
        const rows = Array.from(table.rows).slice(1);
        rows.sort((a, b) => a.cells[col].innerText.localeCompare(b.cells[col].innerText));
        rows.forEach(row => table.appendChild(row));
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
