<?php
require_once __DIR__."/config.php";

// Fetch dynamic content
$hero = $mysqli->query("SELECT title, subtitle, hero_image FROM settings LIMIT 1")->fetch_assoc();
$services = $mysqli->query("SELECT id, name, price, duration_min, description, image_path FROM services WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
$about = $mysqli->query("SELECT about_html FROM settings LIMIT 1")->fetch_assoc();

// Reservation handler (store + redirect to WA)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date  = trim($_POST['date'] ?? '');
    $time  = trim($_POST['time'] ?? '');
    $service_id = intval($_POST['service_id'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    // Lookup service
    $svc = null;
    if ($service_id) {
        $stmt = $mysqli->prepare("SELECT name, price, duration_min FROM services WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $svc = $stmt->get_result()->fetch_assoc();
    }
    // Save
    $stmt = $mysqli->prepare("INSERT INTO reservations(name, phone, service_id, date_str, time_str, notes, created_at, status) VALUES(?,?,?,?,?,?,NOW(),'NEW')");
    $stmt->bind_param("ssisss", $name, $phone, $service_id, $date, $time, $notes);
    $stmt->execute();

    // Build WhatsApp text
    $svcText = $svc ? $svc['name']." (".$svc['duration_min']." min, Rp".number_format($svc['price']).")" : "Custom/Not listed";
    $text = "Hello Kupu Kupu Mas Spa,%0A%0A".
            "Reservation Request:%0A".
            "--------------------%0A".
            "Name: ".rawurlencode($name)."%0A".
            "Phone: ".rawurlencode($phone)."%0A".
            "Service: ".rawurlencode($svcText)."%0A".
            "Date: ".rawurlencode($date)." ".rawurlencode($time)."%0A".
            "Notes: ".rawurlencode($notes)."%0A".
            "--------------------%0A".
            "Sent from website.";
    $wa = "https://wa.me/".$WA_NUMBER."?text=".$text;
    header("Location: ".$wa);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($hero['title'] ?? 'Luxury Spa') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/styles.css">
<script defer src="assets/script.js"></script>
</head>
<body>
<header class="site-header glass">
  <div class="container nav">
    <div class="brand">
      <span class="logo">🌿</span>
      <span class="brand-name">Kupu Kupu Mas Spa</span>
    </div>
    <nav>
      <a href="#home">Home</a>
      <a href="#services">Services</a>
      <a href="#about">About</a>
      <a href="#reserve" class="btn btn-primary">Reserve</a>
    </nav>
  </div>
</header>

<section id="home" class="hero" style="--hero:url('<?= htmlspecialchars($hero['hero_image'] ?? 'assets/hero.jpg') ?>')">
  <div class="overlay"></div>
  <div class="container hero-inner">
     <h1><?= htmlspecialchars($hero['title'] ?? 'Recharge in Style') ?></h1>
     <p class="subtitle"><?= htmlspecialchars($hero['subtitle'] ?? 'Modern Bali-inspired treatments for deep relaxation') ?></p>
     <a href="#services" class="btn btn-outline">Explore Services</a>
     <a href="#reserve" class="btn btn-primary">Book Now</a>
  </div>
</section>

<section id="services" class="section">
  <div class="container">
    <h2 class="section-title">Our Signature Treatments</h2>
    <div class="cards">
      <?php while($row = $services->fetch_assoc()): ?>
        <div class="card">
          <div class="card-media" style="background-image:url('<?= htmlspecialchars($row['image_path'] ?: 'assets/placeholder.jpg') ?>')"></div>
          <div class="card-body">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p class="muted"><?= htmlspecialchars($row['duration_min']) ?> min &middot; Rp<?= number_format($row['price']) ?></p>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <a href="#reserve" class="btn btn-soft" data-service-id="<?= $row['id'] ?>" data-service-name="<?= htmlspecialchars($row['name']) ?>">Reserve</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<section id="about" class="section alt">
  <div class="container two-col">
    <div>
      <h2 class="section-title">About Us</h2>
      <div class="content">
        <?= $about ? $about['about_html'] : "<p>We are a boutique spa offering curated wellness experiences with premium products and expert therapists.</p>" ?>
      </div>
    </div>
    <div class="about-media">
      <img src="assets/about.jpg" alt="Spa ambience">
    </div>
  </div>
</section>

<section id="reserve" class="section">
  <div class="container">
    <h2 class="section-title">Reserve Your Moment</h2>
    <form method="post" class="form" id="reserve-form">
      <input type="hidden" name="reserve" value="1">
      <div class="grid">
        <div class="form-field">
          <label>Your Name</label>
          <input type="text" name="name" placeholder="e.g. Agus Dipta" required>
        </div>
        <div class="form-field">
          <label>WhatsApp Number</label>
          <input type="tel" name="phone" placeholder="e.g. 628123456789" required>
        </div>
        <div class="form-field">
          <label>Date</label>
          <input type="date" name="date" required>
        </div>
        <div class="form-field">
          <label>Time</label>
          <input type="time" name="time" required>
        </div>
        <div class="form-field">
          <label>Service</label>
          <select name="service_id" id="service-select">
            <option value="">-- Select a service --</option>
            <?php
              $res = $mysqli->query("SELECT id, name FROM services WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
              while($r = $res->fetch_assoc()): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-field full">
          <label>Notes (optional)</label>
          <textarea name="notes" rows="4" placeholder="Preferences, allergies, or special requests"></textarea>
        </div>
      </div>
      <button class="btn btn-primary" type="submit">Send to WhatsApp</button>
      <p class="muted small">We’ll save your request in our system and continue on WhatsApp to confirm.</p>
    </form>
  </div>
</section>

<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <div class="brand"><span class="logo">🌿</span><span class="brand-name">Kupu Kupu Mas Spa</span></div>
      <p class="muted">Ungasan, Bali · Open 09:00–21:00</p>
    </div>
    <div>
      <h4>Contact</h4>
      <p class="muted">WhatsApp: <a href="https://wa.me/<?= $WA_NUMBER ?>" target="_blank"><?= $WA_NUMBER ?></a></p>
      <p class="muted">Email: hello@example.com</p>
    </div>
    <div>
      <h4>Admin</h4>
      <p><a href="admin/login.php" class="btn btn-soft small">Admin Login</a></p>
    </div>
  </div>
  <div class="container tiny muted">© <?= date('Y') ?> Kupu Kupu Mas Spa. All rights reserved.</div>
</footer>
</body>
</html>
