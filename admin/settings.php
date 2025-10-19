<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/Auth.php";
Auth::requireLogin();

$msg = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $about_html = $_POST['about_html'] ?? '';
    $hero_image = $_POST['current_hero'] ?? 'assets/hero.jpg';

    // handle hero upload
    if (!empty($_FILES['hero']['name'])) {
        $f = $_FILES['hero'];
        if ($f['error']===0) {
            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $new = "../uploads/hero_".time().".".$ext;
            if (move_uploaded_file($f['tmp_name'], $new)) {
                $hero_image = substr($new, 3); // remove ../
            }
        }
    }

    // upsert
    $exists = $mysqli->query("SELECT id FROM settings LIMIT 1")->fetch_assoc();
    if ($exists) {
        $stmt = $mysqli->prepare("UPDATE settings SET title=?, subtitle=?, about_html=?, hero_image=? WHERE id=?");
        $id = $exists['id'];
        $stmt->bind_param("ssssi", $title, $subtitle, $about_html, $hero_image, $id);
        $stmt->execute();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO settings(title, subtitle, about_html, hero_image) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss", $title, $subtitle, $about_html, $hero_image);
        $stmt->execute();
    }
    $msg = "Saved.";
}

$row = $mysqli->query("SELECT * FROM settings LIMIT 1")->fetch_assoc();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Settings</title><link href="../assets/styles.css" rel="stylesheet">
<style>.wrap{max-width:900px;margin:20px auto;padding:0 16px}.preview{height:160px;border-radius:12px;background:#0f172a;border:1px solid #1e293b;background-size:cover;background-position:center}</style>
</head><body>
<div class="wrap">
  <h2 class="section-title">Site Settings</h2>
  <?php if($msg): ?><p class="muted" style="color:#86efac"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="form">
    <div class="form-field"><label>Title</label><input name="title" value="<?= htmlspecialchars($row['title'] ?? 'Luxury Spa') ?>"></div>
    <div class="form-field"><label>Subtitle</label><input name="subtitle" value="<?= htmlspecialchars($row['subtitle'] ?? 'Modern Bali-inspired treatments') ?>"></div>
    <div class="form-field"><label>About HTML</label><textarea name="about_html" rows="8"><?= htmlspecialchars($row['about_html'] ?? '<p>We are a boutique spa offering curated wellness experiences with premium products and expert therapists.</p>') ?></textarea></div>
    <div class="form-field">
      <label>Hero Image</label>
      <div class="preview" style="background-image:url('../<?= htmlspecialchars($row['hero_image'] ?? 'assets/hero.jpg') ?>')"></div>
      <input type="hidden" name="current_hero" value="<?= htmlspecialchars($row['hero_image'] ?? 'assets/hero.jpg') ?>">
      <input type="file" name="hero" accept="image/*">
    </div>
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-soft" href="dashboard.php">Back</a>
  </form>
</div>
</body></html>
