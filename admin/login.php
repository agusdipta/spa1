<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/Auth.php";
if (Auth::check()) { header("Location: dashboard.php"); exit; }
$error = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if (Auth::login($u, $p, $mysqli)) {
        header("Location: dashboard.php"); exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<link href="../assets/styles.css" rel="stylesheet">
<style>body{display:grid;place-items:center;min-height:100vh;background:#0b1120}.box{width:min(420px,92vw)}.box .form{padding:24px}</style>
</head><body>
<div class="box">
  <h2 class="section-title">Admin Login</h2>
  <form method="post" class="form">
    <?php if($error): ?><p class="muted" style="color:#fca5a5"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <div class="form-field"><label>Username</label><input type="text" name="username" required></div>
    <div class="form-field"><label>Password</label><input type="password" name="password" required></div>
    <button class="btn btn-primary" type="submit">Login</button>
  </form>
</div>
</body></html>
