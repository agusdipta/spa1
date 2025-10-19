<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/Auth.php";
Auth::requireLogin();
?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard</title>
<link href="../assets/styles.css" rel="stylesheet">
<style>.wrap{max-width:1100px;margin:20px auto;padding:0 16px}.row{display:flex;gap:14px;flex-wrap:wrap}.card a.btn{margin-top:8px;display:inline-block}</style>
</head><body>
<div class="wrap">
  <h2 class="section-title">Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></h2>
  <div class="row">
    <div class="card" style="flex:1;min-width:260px">
      <div class="card-body">
        <h3>Site Settings</h3>
        <p class="muted">Hero, subtitle, About content</p>
        <a class="btn btn-soft" href="settings.php">Edit</a>
      </div>
    </div>
    <div class="card" style="flex:1;min-width:260px">
      <div class="card-body">
        <h3>Services</h3>
        <p class="muted">Create, edit, reorder, activate/deactivate</p>
        <a class="btn btn-soft" href="services.php">Manage</a>
      </div>
    </div>
    <div class="card" style="flex:1;min-width:260px">
      <div class="card-body">
        <h3>Reservations</h3>
        <p class="muted">View reservation logs</p>
        <a class="btn btn-soft" href="reservations.php">Open</a>
      </div>
    </div>
  </div>
  <p><a class="btn btn-outline" href="logout.php">Logout</a></p>
</div>
</body></html>
