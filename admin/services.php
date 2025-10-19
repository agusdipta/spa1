<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/Auth.php";
Auth::requireLogin();

// Create / Update
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['create']) || isset($_POST['update'])) {
        $id = intval($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $duration_min = intval($_POST['duration_min'] ?? 60);
        $desc = $_POST['description'] ?? '';
        $image_path = $_POST['current_image'] ?? '';

        if (!empty($_FILES['image']['name'])) {
            $f = $_FILES['image'];
            if ($f['error']===0) {
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                $new = "../uploads/svc_".time()."_".rand(100,999).".".$ext;
                if (move_uploaded_file($f['tmp_name'], $new)) {
                    $image_path = substr($new, 3); // remove ../
                }
            }
        }

        if ($id>0) {
            $stmt = $mysqli->prepare("UPDATE services SET name=?, price=?, duration_min=?, description=?, image_path=? WHERE id=?");
            $stmt->bind_param("sdissi", $name, $price, $duration_min, $desc, $image_path, $id);
            $stmt->execute();
        } else {
            $stmt = $mysqli->prepare("INSERT INTO services(name, price, duration_min, description, image_path, is_active, sort_order) VALUES(?,?,?,?,?,1,100)");
            $stmt->bind_param("sdisi", $name, $price, $duration_min, $desc, $image_path);
            $stmt->execute();
        }
    }
}

// Actions: delete, toggle, reorder
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $mysqli->query("DELETE FROM services WHERE id=".$id);
}
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $mysqli->query("UPDATE services SET is_active=1-is_active WHERE id=".$id);
}
if (isset($_GET['up']) || isset($_GET['down'])) {
    $id = intval($_GET['id'] ?? 0);
    $delta = isset($_GET['up']) ? -1 : 1;
    $mysqli->query("UPDATE services SET sort_order=sort_order+($delta) WHERE id=".$id);
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit = $mysqli->query("SELECT * FROM services WHERE id=".$id)->fetch_assoc();
}

$list = $mysqli->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC");
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Services</title><link href="../assets/styles.css" rel="stylesheet">
<style>.wrap{max-width:1100px;margin:20px auto;padding:0 16px}.row{display:grid;grid-template-columns:1fr 1fr;gap:16px}.tbl{width:100%;border-collapse:collapse}.tbl th,.tbl td{border-bottom:1px solid #1f2937;padding:10px}.preview{height:90px;width:140px;background:#0f172a;border:1px solid #1e293b;background-size:cover;background-position:center;border-radius:8px}</style>
</head><body>
<div class="wrap">
  <h2 class="section-title">Manage Services</h2>
  <div class="row">
    <div class="form card-body" style="border-radius:16px">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($edit['id'] ?? 0) ?>">
        <div class="form-field"><label>Name</label><input name="name" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>"></div>
        <div class="form-field"><label>Price (IDR)</label><input type="number" step="1000" name="price" required value="<?= htmlspecialchars($edit['price'] ?? '') ?>"></div>
        <div class="form-field"><label>Duration (min)</label><input type="number" name="duration_min" required value="<?= htmlspecialchars($edit['duration_min'] ?? 60) ?>"></div>
        <div class="form-field"><label>Description</label><textarea name="description" rows="5"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea></div>
        <div class="form-field">
          <label>Image</label>
          <div class="preview" style="background-image:url('../<?= htmlspecialchars($edit['image_path'] ?? 'assets/placeholder.jpg') ?>')"></div>
          <input type="hidden" name="current_image" value="<?= htmlspecialchars($edit['image_path'] ?? '') ?>">
          <input type="file" name="image" accept="image/*">
        </div>
        <button class="btn btn-primary" name="<?= $edit ? 'update' : 'create' ?>" type="submit"><?= $edit ? 'Update' : 'Create' ?></button>
        <?php if($edit): ?><a class="btn btn-soft" href="services.php">Cancel</a><?php endif; ?>
      </form>
    </div>
    <div>
      <table class="tbl">
        <tr><th>Order</th><th>Image</th><th>Name</th><th>Price</th><th>Dur</th><th>Status</th><th>Actions</th></tr>
        <?php while($r=$list->fetch_assoc()): ?>
        <tr>
          <td>
            <a class="btn btn-soft small" href="services.php?up=1&id=<?= $r['id'] ?>">↑</a>
            <a class="btn btn-soft small" href="services.php?down=1&id=<?= $r['id'] ?>">↓</a>
          </td>
          <td><div class="preview" style="background-image:url('../<?= htmlspecialchars($r['image_path'] ?: 'assets/placeholder.jpg') ?>')"></div></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td>Rp<?= number_format($r['price']) ?></td>
          <td><?= intval($r['duration_min']) ?>m</td>
          <td><?= $r['is_active'] ? 'Active' : 'Hidden' ?></td>
          <td>
            <a class="btn btn-soft small" href="services.php?edit=<?= $r['id'] ?>">Edit</a>
            <a class="btn btn-soft small" href="services.php?toggle=<?= $r['id'] ?>"><?= $r['is_active']?'Hide':'Show' ?></a>
            <a class="btn btn-outline small" onclick="return confirm('Delete?')" href="services.php?del=<?= $r['id'] ?>">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
  <p><a class="btn btn-outline" href="dashboard.php">Back</a></p>
</div>
</body></html>
