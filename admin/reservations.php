<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/Auth.php";
Auth::requireLogin();

$res = $mysqli->query("SELECT r.*, s.name AS service_name FROM reservations r LEFT JOIN services s ON s.id=r.service_id ORDER BY r.id DESC LIMIT 200");
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reservations</title><link href="../assets/styles.css" rel="stylesheet">
<style>.wrap{max-width:1100px;margin:20px auto;padding:0 16px}.tbl{width:100%;border-collapse:collapse}.tbl th,.tbl td{border-bottom:1px solid #1f2937;padding:10px}</style>
</head><body>
<div class="wrap">
  <h2 class="section-title">Reservations</h2>
  <table class="tbl">
    <tr><th>#</th><th>Name</th><th>Phone</th><th>Service</th><th>Date</th><th>Time</th><th>Notes</th><th>Status</th><th>Created</th></tr>
    <?php while($r=$res->fetch_assoc()): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><a href="https://wa.me/<?= htmlspecialchars($r['phone']) ?>" target="_blank"><?= htmlspecialchars($r['phone']) ?></a></td>
        <td><?= htmlspecialchars($r['service_name'] ?? 'N/A') ?></td>
        <td><?= htmlspecialchars($r['date_str']) ?></td>
        <td><?= htmlspecialchars($r['time_str']) ?></td>
        <td><?= nl2br(htmlspecialchars($r['notes'])) ?></td>
        <td><?= htmlspecialchars($r['status']) ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <p><a class="btn btn-outline" href="dashboard.php">Back</a></p>
</div>
</body></html>
