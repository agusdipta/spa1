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
  <div class="container nav" data-animate>
    <div class="brand">
      <span class="logo">🌿</span>
      <span class="brand-name">Kupu Kupu Mas Spa</span>
    </div>
    <button class="nav-toggle" type="button" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>
    <nav class="nav-links">
      <a href="#home">Home</a>
      <a href="#services">Services</a>
      <a href="#experience">Experience</a>
      <a href="#about">About</a>
      <a href="#reserve" class="btn btn-primary">Reserve</a>
    </nav>
  </div>
</header>

<section id="home" class="hero" style="--hero:url('<?= htmlspecialchars($hero['hero_image'] ?? 'assets/hero.jpg') ?>')">
  <div class="overlay"></div>
  <div class="container hero-inner" data-animate>
     <div class="hero-badge">Award-winning Balinese Wellness</div>
     <h1><?= htmlspecialchars($hero['title'] ?? 'Recharge in Style') ?></h1>
     <p class="subtitle"><?= htmlspecialchars($hero['subtitle'] ?? 'Modern Bali-inspired treatments for deep relaxation') ?></p>
     <div class="hero-actions">
       <a href="#services" class="btn btn-outline">Explore Services</a>
       <a href="#reserve" class="btn btn-primary">Book Now</a>
     </div>
     <div class="hero-meta">
       <span>✨ Complimentary welcome ritual</span>
       <span>🧖‍♀️ Private suites available</span>
       <span>🌺 Natural aromatherapy oils</span>
     </div>
  </div>
  <div class="hero-orb"></div>
</section>

<section id="experience" class="section section-features">
  <div class="container">
    <div class="section-header" data-animate>
      <h2 class="section-title">Experience Elevated Wellness</h2>
      <p class="muted">Curated journeys that harmonise body, mind, and spirit in the heart of Ungasan.</p>
    </div>
    <div class="features-grid">
      <article class="feature-card" data-animate>
        <div class="feature-icon">🕯️</div>
        <h3>Immersive Atmosphere</h3>
        <p>Soothing soundscapes, gentle lighting, and bespoke scents crafted to help you unwind instantly.</p>
      </article>
      <article class="feature-card" data-animate>
        <div class="feature-icon">🌊</div>
        <h3>Hydro Experience Lounge</h3>
        <p>Rejuvenate in our vitality pool and infrared sauna before your treatment for complete restoration.</p>
      </article>
      <article class="feature-card" data-animate>
        <div class="feature-icon">💆‍♀️</div>
        <h3>Master Therapists</h3>
        <p>Our certified therapists personalise every session using centuries-old Balinese healing techniques.</p>
      </article>
      <article class="feature-card" data-animate>
        <div class="feature-icon">🥂</div>
        <h3>Signature Rituals</h3>
        <p>Indulge in welcome refreshments, curated playlists, and post-treatment elixirs tailored to you.</p>
      </article>
    </div>
  </div>
</section>

<section id="services" class="section">
  <div class="container">
    <div class="section-header" data-animate>
      <h2 class="section-title">Our Signature Treatments</h2>
      <div class="section-description">
        <p class="muted">Discover personalised rituals designed to restore, revitalise, and realign your energy.</p>
        <div class="legend">
          <span><span class="dot premium"></span> Premium Oils</span>
          <span><span class="dot suite"></span> Private Suite</span>
        </div>
      </div>
    </div>
    <div class="cards">
      <?php while($row = $services->fetch_assoc()): ?>
        <div class="card" data-animate>
          <div class="card-media" style="background-image:url('<?= htmlspecialchars($row['image_path'] ?: 'assets/placeholder.jpg') ?>')"></div>
          <div class="card-body">
            <div class="card-header">
              <h3><?= htmlspecialchars($row['name']) ?></h3>
              <p class="muted tag"><?= htmlspecialchars($row['duration_min']) ?> min</p>
            </div>
            <p class="price">Rp<?= number_format($row['price']) ?></p>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <div class="card-footer">
              <div class="card-flags">
                <span class="chip premium">Premium oils</span>
                <span class="chip suite">Private suite</span>
              </div>
              <a href="#reserve" class="btn btn-soft" data-service-id="<?= $row['id'] ?>" data-service-name="<?= htmlspecialchars($row['name']) ?>">Reserve</a>
            </div>
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

<section class="section timeline">
  <div class="container">
    <div class="section-header" data-animate>
      <h2 class="section-title">Wellness Journey</h2>
      <p class="muted">Your visit unfolds through calming moments, each designed to rejuvenate you from within.</p>
    </div>
    <div class="timeline-grid">
      <article class="timeline-card" data-animate>
        <span class="timeline-step">01</span>
        <h3>Arrival Ritual</h3>
        <p>Begin with a grounding tea ceremony and aromatherapy breathing ritual curated by our wellness concierge.</p>
      </article>
      <article class="timeline-card" data-animate>
        <span class="timeline-step">02</span>
        <h3>Bespoke Consultation</h3>
        <p>Your therapist tailors the treatment, selecting oils, pressure, and focus areas to match your goals.</p>
      </article>
      <article class="timeline-card" data-animate>
        <span class="timeline-step">03</span>
        <h3>Signature Treatment</h3>
        <p>Relax in a private suite with immersive lighting, curated music, and temperature-controlled beds.</p>
      </article>
      <article class="timeline-card" data-animate>
        <span class="timeline-step">04</span>
        <h3>Post-therapy Glow</h3>
        <p>Unwind in our serenity lounge with botanical refreshments and mindful stretching guidance.</p>
      </article>
    </div>
  </div>
</section>

<section class="section testimonials">
  <div class="container">
    <div class="section-header" data-animate>
      <h2 class="section-title">Loved by Our Guests</h2>
      <p class="muted">Real stories from travellers and locals who made Kupu Kupu Mas Spa their haven.</p>
    </div>
    <div class="testimonials-grid">
      <article class="testimonial" data-animate>
        <p class="quote">“The ambience transports you instantly. My therapist intuitively knew every pressure point.”</p>
        <div class="meta">
          <span class="name">Nadia S.</span>
          <span class="detail">Wellness Retreat Guest</span>
        </div>
      </article>
      <article class="testimonial" data-animate>
        <p class="quote">“Luxurious yet warm. The welcome ritual and hydro lounge made my Bali trip unforgettable.”</p>
        <div class="meta">
          <span class="name">Jackson T.</span>
          <span class="detail">Digital Nomad</span>
        </div>
      </article>
      <article class="testimonial" data-animate>
        <p class="quote">“Loved the private suite! The post-treatment elixir was such a thoughtful finishing touch.”</p>
        <div class="meta">
          <span class="name">Putri A.</span>
          <span class="detail">Local Member</span>
        </div>
      </article>
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

<section class="section faq alt">
  <div class="container">
    <div class="section-header" data-animate>
      <h2 class="section-title">Frequently Asked</h2>
      <p class="muted">Have a question? Explore quick answers before reaching out to our concierge.</p>
    </div>
    <div class="faq-list">
      <article class="faq-item" data-animate>
        <button class="faq-question" type="button">How early should I arrive?<span class="icon">+</span></button>
        <div class="faq-answer">
          <p>Arrive at least 20 minutes before your booking to enjoy our welcome lounge and pre-treatment rituals.</p>
        </div>
      </article>
      <article class="faq-item" data-animate>
        <button class="faq-question" type="button">Do you offer couple treatments?<span class="icon">+</span></button>
        <div class="faq-answer">
          <p>Yes, our couple suites are equipped with dual treatment beds, rainfall showers, and mood lighting controls.</p>
        </div>
      </article>
      <article class="faq-item" data-animate>
        <button class="faq-question" type="button">Can I customise my ritual?<span class="icon">+</span></button>
        <div class="faq-answer">
          <p>Absolutely. Choose from aroma blends, pressure levels, and targeted enhancements guided by your therapist.</p>
        </div>
      </article>
    </div>
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

<a class="floating-cta" href="https://wa.me/<?= $WA_NUMBER ?>" target="_blank" rel="noopener">
  <span class="pulse"></span>
  <span class="icon">💬</span>
  <span class="text">Chat with us</span>
</a>
</body>
</html>
