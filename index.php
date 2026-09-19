<?php
require_once __DIR__ . '/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HIRAYA — Beauty. Fashion. Identity.</title>

<link rel="icon" type="image/png" href="files/images/circlelogo.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="files/css/style.css">

<meta name="description" content="HIRAYA is a Filipino luxury beauty and fashion collective empowering creators through editorial campaigns, curated collections, and modern self-expression.">
<meta name="keywords" content="HIRAYA, Filipino luxury beauty, Filipino fashion, beauty collective, creator campaigns, Filipina fashion, editorial beauty, creator brand">
<meta name="author" content="HIRAYA">
<link rel="canonical" href="https://thehirayacollectives.shop">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="HIRAYA — Beauty. Fashion. Identity.">
<meta property="og:description" content="Discover Filipino luxury beauty and fashion through curated collections, creator campaigns, and modern self-expression.">
<meta property="og:url" content="https://thehirayacollectives.shop">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="HIRAYA">

<!-- Twitter / X -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="HIRAYA — Beauty. Fashion. Identity.">
<meta name="twitter:description" content="Filipino luxury beauty and fashion for creators, visionaries, and modern self-expression.">

<style>
/* =========================================================
   HIRAYA — layout & placement layer
   Loaded after style.css so these rules win where they overlap.
   ========================================================= */

:root {
  --hi-ink:      #1a0a10;
  --hi-rose:     #c9909a;
  --hi-mauve:    #8b5c6b;
  --hi-blush:    #faf8f5;
  --hi-hot:      #e0576e;
  --hi-line:     rgba(0,0,0,.07);

  --hi-nav-h:    76px;          /* used for anchor offset */
  --hi-nav-h-sm: 62px;          /* bar height once scrolled */
  --hi-section:  clamp(64px, 9vw, 110px);
  --hi-gap:      clamp(1rem, 2.2vw, 2rem);
  --hi-gutter:   clamp(1.2rem, 4vw, 2.5rem);
}

html { scroll-behavior: smooth; }

/* Anchor links no longer land underneath the fixed navbar */
section[id], div[id].editorial-break { scroll-margin-top: calc(var(--hi-nav-h) + 12px); }

/* One consistent vertical rhythm for every section */
#collaborate,
#vote-for-model,
#creator-benefits,
#campaigns,
#collections,
#gallery,
#about { padding-block: var(--hi-section); }

.container { width: min(1240px, 100%); margin-inline: auto; padding-inline: var(--hi-gutter); }

/* The custom cursor was still painting a stray dot on phones */
@media (hover: none), (pointer: coarse) {
  .cursor, .cursor-ring, .scroll-indicator { display: none !important; }
}

/* Keyboard users can see where they are */
a:focus-visible, button:focus-visible {
  outline: 2px solid var(--hi-rose);
  outline-offset: 3px;
  border-radius: 4px;
}

@media (prefers-reduced-motion: reduce) {
  html { scroll-behavior: auto; }
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}

/* ---------- LOOKING FOR COLLABORATION ---------- */
#collaborate { background: var(--hi-blush); }
.collab-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: var(--hi-gap);
  margin: clamp(2rem, 5vw, 3rem) 0 clamp(2rem, 4vw, 3.5rem);
}
.collab-card {
  background: #fff; border: 1px solid var(--hi-line); border-radius: 12px;
  padding: 2.2rem 1.8rem; text-align: center;
  transition: transform .3s, box-shadow .3s;
}
.collab-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,.08); }
.collab-icon { font-size: 1.8rem; display: block; margin-bottom: 1rem; color: var(--hi-mauve); }
.collab-card h4 { font-family: "Cormorant Garamond", serif; font-size: 1.3rem; margin-bottom: .6rem; color: var(--hi-ink); }
.collab-card p { font-size: .88rem; color: #666; line-height: 1.7; }
.collab-cta { text-align: center; }
.collab-cta-sub { font-size: .9rem; color: #888; margin-bottom: 1.2rem; }

/* ---------- CREATOR PROGRAM ---------- */
.steps-grid,
.benefits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--hi-gap);
}

/* ---------- CREATOR QUESTIONS + BRAND STORY ---------- */
.creator-info-wrap {
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr);
  gap: clamp(2rem, 5vw, 4rem);
  align-items: start;
}
@media (max-width: 900px) {
  .creator-info-wrap { grid-template-columns: 1fr; }
}

/* ---------- COLLECTIONS ---------- */
.tab-panel { display: none; }
.tab-panel.active {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: var(--hi-gap);
  margin-top: clamp(1.5rem, 4vw, 2.5rem);
}
.product-img { aspect-ratio: 4 / 5; overflow: hidden; }
.product-img img { transition: transform .5s ease; }
.product-card:hover .product-img img { transform: scale(1.05); }

/* ---------- EDITORIAL BREAK (now its own full-width block) ---------- */
.editorial-break { position: relative; margin-block: 0; }

/* ---------- GALLERY ---------- */
.masonry-grid { columns: 4; column-gap: var(--hi-gap); }
.masonry-item { break-inside: avoid; margin-bottom: var(--hi-gap); }
@media (max-width: 1100px) { .masonry-grid { columns: 3; } }
@media (max-width: 768px)  { .masonry-grid { columns: 2; } }
@media (max-width: 480px)  { .masonry-grid { columns: 1; } }

/* ---------- ABOUT ---------- */
.about-grid {
  display: grid;
  grid-template-columns: minmax(0, .8fr) minmax(0, 1.2fr);
  gap: clamp(2rem, 5vw, 4rem);
  align-items: start;
}
.about-pillars { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--hi-gap); }
@media (max-width: 900px) {
  .about-grid { grid-template-columns: 1fr; }
  .about-word { font-size: clamp(3rem, 18vw, 6rem); text-align: center; }
}

/* ---------- FOOTER ---------- */
.footer-top { display: grid; grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(0, 1fr)); gap: clamp(1.5rem, 4vw, 3rem); }
@media (max-width: 900px) { .footer-top { grid-template-columns: 1fr 1fr; } }
@media (max-width: 560px) {
  .footer-top { grid-template-columns: 1fr; gap: 2rem; }
  .footer-bottom { flex-direction: column; gap: .8rem; text-align: center; }
}

/* ---------- NAVBAR ---------- */
/* Light bar drawn from the page's own light surfaces: the blush ground used by
   the collaborate section, ink for type, rose for the accent, and the same
   hairline (rgba(0,0,0,.07)) that outlines the cards.
   nav#navbar (0,1,1) outranks a plain #navbar in style.css. */

nav#navbar {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1200;
  display: flex; align-items: center; justify-content: space-between;
  gap: clamp(1rem, 3vw, 2.5rem);
  height: var(--hi-nav-h);
  padding-inline: var(--hi-gutter);
  background: rgba(250,248,245,.86);
  backdrop-filter: blur(16px) saturate(150%);
  -webkit-backdrop-filter: blur(16px) saturate(150%);
  border-bottom: 1px solid transparent;
  transition: height .35s ease, background .35s ease,
              border-color .35s ease, box-shadow .35s ease;
}

nav#navbar.scrolled {
  height: var(--hi-nav-h-sm);
  background: rgba(250,248,245,.97);
  border-bottom-color: var(--hi-line);
  box-shadow: 0 8px 30px rgba(26,10,16,.05);
}

/* Wordmark — the diamond is the same glyph the marquee strip uses */
nav#navbar .nav-logo {
  display: flex; align-items: center; gap: .55rem;
  font-family: "Cormorant Garamond", serif;
  font-size: clamp(1.05rem, 2vw, 1.3rem); font-weight: 500;
  letter-spacing: clamp(.16em, 1.4vw, .32em);
  text-indent: clamp(.16em, 1.4vw, .32em);
  text-transform: uppercase;
  color: var(--hi-ink); text-decoration: none; white-space: nowrap;
}
nav#navbar .nav-logo::before {
  content: "\25C6"; font-size: .42em; text-indent: 0; letter-spacing: 0;
  color: var(--hi-rose); transition: transform .4s ease;
}
nav#navbar .nav-logo:hover::before { transform: rotate(90deg); }

/* Links */
nav#navbar .nav-links {
  display: flex; align-items: center; gap: clamp(1rem, 2.1vw, 2rem);
  list-style: none; margin: 0; padding: 0;
}
nav#navbar .nav-links a {
  position: relative; display: block; padding: .45rem 0;
  font-family: "DM Sans", sans-serif; font-size: .72rem; font-weight: 500;
  letter-spacing: .16em; text-transform: uppercase; white-space: nowrap;
  color: rgba(26,10,16,.58); text-decoration: none;
  transition: color .28s ease;
}
nav#navbar .nav-links a::after {
  content: ""; position: absolute; left: 0; bottom: 0;
  width: 100%; height: 1px; background: var(--hi-mauve);
  transform: scaleX(0); transform-origin: right;
  transition: transform .38s cubic-bezier(.4,0,.2,1);
}
nav#navbar .nav-links a:hover,
nav#navbar .nav-links a:focus-visible { color: var(--hi-ink); }
nav#navbar .nav-links a:hover::after,
nav#navbar .nav-links a:focus-visible::after { transform: scaleX(1); transform-origin: left; }

/* Marks the section currently on screen */
nav#navbar .nav-links a.active { color: var(--hi-ink); }
nav#navbar .nav-links a.active::after { transform: scaleX(1); transform-origin: left; }

/* ---------- MOBILE MENU ---------- */
/* Lives OUTSIDE #navbar in the DOM so nothing in style.css — an overflow,
   a transform, a z-index or a display:none — can reach it. */

.nav-hamburger {
  display: none; flex-direction: column; justify-content: center; align-items: center;
  gap: 5px; width: 44px; height: 44px; margin-right: -10px;
  background: none; border: none; cursor: pointer; padding: 0;
  color: var(--hi-ink);
}
.nav-hamburger span {
  display: block; width: 22px; height: 1.5px; background: currentColor; border-radius: 2px;
  transition: transform .3s ease, opacity .2s ease; transform-origin: center;
}

.nav-overlay {
  position: fixed; inset: 0; z-index: 1400;
  background: rgba(26,10,16,.35); backdrop-filter: blur(3px);
  opacity: 0; visibility: hidden; transition: opacity .3s ease, visibility .3s ease;
}
.nav-overlay.open { opacity: 1; visibility: visible; }

.mobile-menu {
  position: fixed; top: 0; right: 0; z-index: 1500;
  width: min(360px, 88vw); height: 100%; height: 100dvh;
  background: var(--hi-blush);
  display: flex; flex-direction: column;
  padding: 0 clamp(1.4rem, 6vw, 2.2rem) calc(2rem + env(safe-area-inset-bottom));
  box-shadow: -10px 0 50px rgba(26,10,16,.14);
  transform: translateX(105%);
  transition: transform .38s cubic-bezier(.4,0,.2,1);
  overflow-y: auto; overscroll-behavior: contain;
  visibility: hidden;
}
.mobile-menu.open { transform: translateX(0); visibility: visible; }

/* Full width on phones — a part-width drawer looks cramped at this size */
@media (max-width: 560px) {
  .mobile-menu { width: 100%; box-shadow: none; }
}

.mobile-menu-head {
  display: flex; align-items: center; justify-content: space-between;
  height: var(--hi-nav-h); flex-shrink: 0;
  margin-bottom: clamp(1.5rem, 5vw, 2.5rem);
  border-bottom: 1px solid var(--hi-line);
}
.mobile-menu-logo {
  display: flex; align-items: center; gap: .55rem;
  font-family: "Cormorant Garamond", serif; font-size: 1.15rem; font-weight: 500;
  letter-spacing: .28em; text-indent: .28em; text-transform: uppercase;
  color: var(--hi-ink); text-decoration: none;
}
.mobile-menu-logo::before {
  content: "\25C6"; font-size: .42em; text-indent: 0; letter-spacing: 0;
  color: var(--hi-rose);
}
.mobile-menu-close {
  width: 44px; height: 44px; margin-right: -10px;
  display: grid; place-items: center;
  background: none; border: none; cursor: pointer;
  color: rgba(26,10,16,.5); font-size: 1.05rem; line-height: 1;
  transition: color .25s ease;
}
.mobile-menu-close:hover { color: var(--hi-ink); }

/* Editorial serif at a readable size, rather than tiny uppercase labels */
.mobile-menu-links { list-style: none; margin: 0; padding: 0; }
.mobile-menu-links li { border-bottom: 1px solid var(--hi-line); }
.mobile-menu-links li:last-child { border-bottom: none; }
.mobile-menu-links a {
  display: flex; align-items: baseline; gap: .7rem;
  padding: clamp(.85rem, 3vw, 1.15rem) 0;
  font-family: "Cormorant Garamond", serif;
  font-size: clamp(1.3rem, 5.5vw, 1.55rem); font-weight: 400; letter-spacing: .02em;
  color: var(--hi-ink); text-decoration: none;
  transition: color .25s ease, padding-left .25s ease;
}
.mobile-menu-links a::before {
  content: "\25C6"; font-size: .34em; color: var(--hi-rose);
  opacity: 0; transform: translateX(-6px);
  transition: opacity .25s ease, transform .25s ease;
}
.mobile-menu-links a:hover,
.mobile-menu-links a:focus-visible,
.mobile-menu-links a.active { color: var(--hi-mauve); padding-left: .35rem; }
.mobile-menu-links a:hover::before,
.mobile-menu-links a:focus-visible::before,
.mobile-menu-links a.active::before { opacity: 1; transform: translateX(0); }

body.nav-open { overflow: hidden; }

/* Breakpoint at 1024px — seven links crowd the bar on tablets */
@media (max-width: 1024px) {
  #navbar .nav-links { display: none !important; }
  .nav-hamburger { display: flex !important; }
}
@media (min-width: 1025px) {
  .nav-overlay, .mobile-menu { display: none !important; }
}

/* Shorter bar on phones so it takes less of a small screen */
@media (max-width: 640px) {
  :root { --hi-nav-h: 62px; --hi-nav-h-sm: 56px; }
}

/* ---------- HERO ---------- */
@media (max-width: 768px) {
  .hero-content { padding-inline: var(--hi-gutter); }
  .hero-title { font-size: clamp(2.2rem, 9vw, 3.5rem); }
  .hero-sub { font-size: .9rem; }
  .hero-btns { flex-direction: column; align-items: center; gap: .8rem; }
  .hero-btns .btn-primary,
  .hero-btns .btn-outline { width: 100%; max-width: 320px; text-align: center; }
  .hero-socials { bottom: 1.5rem; left: 50%; transform: translateX(-50%); flex-direction: row; gap: 1.2rem; }
  .section-title { font-size: clamp(1.6rem, 6vw, 2.4rem); }
}
</style>

<?php if (cfg('votePage')): ?>
<style>
/* ---------- VOTE FOR A CREATOR LOOK ---------- */
#vote-for-model { background: var(--hi-ink); }
#vote-for-model .section-label { color: var(--hi-rose); }
#vote-for-model .section-title { color: #fff; }
#vote-for-model .models-intro { color: rgba(255,255,255,.65); }

.vote-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: var(--hi-gap);
  margin-top: clamp(2rem, 5vw, 3rem);
}
.vote-card {
  cursor: pointer; border-radius: 14px; overflow: hidden;
  background: #2a1018; border: 2px solid transparent;
  transition: border-color .3s, transform .3s;
}
.vote-card:hover { border-color: var(--hi-rose); transform: translateY(-4px); }
.vote-card.voted { border-color: var(--hi-hot); }
.vote-img-wrap { position: relative; aspect-ratio: 3 / 4; overflow: hidden; }
.vote-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s; }
.vote-card:hover .vote-img-wrap img { transform: scale(1.06); }
.vote-overlay { position: absolute; inset: auto 0 0 0; padding: 1.2rem 1rem .8rem; background: linear-gradient(transparent, rgba(26,10,16,.85)); }
.vote-label { color: #fff; font-family: "Cormorant Garamond", serif; font-size: 1.15rem; font-weight: 500; letter-spacing: .05em; }
.vote-meta { display: flex; align-items: center; gap: .4rem; margin-top: .3rem; font-size: .78rem; color: rgba(255,255,255,.72); font-family: "DM Sans", sans-serif; letter-spacing: .02em; }
.vote-dot { color: var(--hi-rose); font-size: .6rem; }
.vote-btn {
  width: 100%; padding: .75rem 1rem; border: none; cursor: pointer;
  background: linear-gradient(135deg, var(--hi-rose), var(--hi-mauve)); color: #fff;
  font-family: "DM Sans", sans-serif; font-size: .9rem; font-weight: 600; letter-spacing: .04em;
  display: flex; align-items: center; justify-content: center; gap: .5rem;
  transition: filter .3s, background .3s;
}
.vote-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
.vote-btn:hover { filter: brightness(1.1); }
.vote-card.voted .vote-btn { background: linear-gradient(135deg, var(--hi-hot), #b0354e); }

.vote-loading-overlay {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(26,10,16,.92); backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center; flex-direction: column;
}
.vote-loading-box { text-align: center; }
.vote-spinner-ring {
  width: 64px; height: 64px; margin: 0 auto 1.5rem;
  border: 4px solid rgba(201,144,154,.2); border-top-color: var(--hi-rose);
  border-radius: 50%; animation: vspin 1s linear infinite;
}
@keyframes vspin { to { transform: rotate(360deg); } }
.vote-loading-title { color: #fff; font-family: "Cormorant Garamond", serif; font-size: 1.6rem; margin-bottom: .5rem; }
.vote-loading-sub { color: rgba(255,255,255,.55); font-size: .9rem; }
</style>
<?php endif; ?>
</head>
<body>

<!-- CUSTOM CURSOR -->
<div class="cursor" id="cursor" aria-hidden="true"></div>
<div class="cursor-ring" id="cursorRing" aria-hidden="true"></div>

<!-- NAV -->
<nav id="navbar">
  <a href="#hero" class="nav-logo">HIRAYA</a>

  <ul class="nav-links" id="navLinks">
    <li><a href="#collaborate">Collaborate</a></li>
    <?php if (cfg('votePage')): ?>
    <li><a href="#vote-for-model">Vote</a></li>
    <?php endif; ?>
    <li><a href="#creator-benefits">Creators</a></li>
    <li><a href="#campaigns">FAQs</a></li>
    <li><a href="#collections">The Edit</a></li>
    <li><a href="#gallery">Gallery</a></li>
    <li><a href="#about">Story</a></li>
  </ul>

  <button class="nav-hamburger" id="navHamburger" type="button"
          aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- MOBILE MENU (outside the navbar on purpose) -->
<div class="nav-overlay" id="navOverlay" hidden></div>

<aside class="mobile-menu" id="mobileMenu" aria-label="Site menu" aria-hidden="true">
  <div class="mobile-menu-head">
    <a href="#hero" class="mobile-menu-logo" data-nav-link>HIRAYA</a>
    <button class="mobile-menu-close" id="navClose" type="button" aria-label="Close menu">&#10005;</button>
  </div>

  <ul class="mobile-menu-links">
    <li><a href="#collaborate" data-nav-link>Collaborate</a></li>
    <?php if (cfg('votePage')): ?>
    <li><a href="#vote-for-model" data-nav-link>Vote</a></li>
    <?php endif; ?>
    <li><a href="#creator-benefits" data-nav-link>Creators</a></li>
    <li><a href="#campaigns" data-nav-link>FAQs</a></li>
    <li><a href="#collections" data-nav-link>The Edit</a></li>
    <li><a href="#gallery" data-nav-link>Gallery</a></li>
    <li><a href="#about" data-nav-link>Story</a></li>
  </ul>

</aside>

<!-- HERO -->
<section id="hero">
  <div class="hero-bg">
    <video autoplay muted loop playsinline class="hero-video" >
      <source src="files/hero.mp4" type="video/mp4">
    </video>
  </div>
  <div class="hero-content">
    <p class="hero-eyebrow">Filipino Luxury Beauty &amp; Fashion</p>
    <h1 class="hero-title">Create with<br><em>HIRAYA</em></h1>
    <p class="hero-sub">Beauty. Fashion. Content. Confidence.</p>
    <div class="hero-btns">
      <button class="btn-primary" onclick="scrollToApply()">Apply as a creator</button>
      <a href="#collections" class="btn-outline">Explore collections</a>
    </div>
  </div>
  <div class="hero-socials">
    <a href="https://instagram.com" target="_blank" rel="noopener">Instagram</a>
    <a href="https://tiktok.com" target="_blank" rel="noopener">TikTok</a>
    <a href="https://t.me/HirayaApplicant_bot" target="_blank" rel="noopener">Telegram</a>
  </div>
  <div class="scroll-indicator" aria-hidden="true">
    <div class="scroll-line"></div>
    <span>Scroll</span>
  </div>
</section>

<!-- LOOKING FOR COLLABORATION -->
<section id="collaborate">
  <div class="container">
    <p class="section-label aos">Open for Collaboration</p>
    <h2 class="section-title aos aos-delay-1">We're <em>looking for collaborators</em></h2>
    <p class="models-intro aos aos-delay-2">
      HIRAYA is opening its next season of collaborations. We work with content creators, stylists, makeup artists, and photographers who want to build something with the brand — not just post about it. If that sounds like you, we want to hear from you.
    </p>

    <div class="collab-grid">
      <div class="collab-card aos">
        <span class="collab-icon" aria-hidden="true">&#10022;</span>
        <h4>Campaign collaborations</h4>
        <p>Work alongside the HIRAYA team on seasonal beauty and fashion shoots, published across our digital platforms.</p>
      </div>
      <div class="collab-card aos aos-delay-1">
        <span class="collab-icon" aria-hidden="true">&#9678;</span>
        <h4>Long-term partnerships</h4>
        <p>Collaborators who fit the brand may move into ambassador roles with product seeding, campaign credits, and ongoing work.</p>
      </div>
      <div class="collab-card aos aos-delay-2">
        <span class="collab-icon" aria-hidden="true">&#9825;</span>
        <h4>Every kind of creative</h4>
        <p>Creator, stylist, MUA, or photographer — soft glam, street, minimalist, bold. There's a place here for your craft.</p>
      </div>
    </div>

    <div class="collab-cta aos aos-delay-3">
      <p class="collab-cta-sub">Collaborations are open now. Slots are limited.</p>
      <button class="btn-primary" type="button" onclick="window.location.href='apply.php'">Start a collaboration</button>
    </div>
  </div>
</section>

<?php if (cfg('votePage')): ?>
<!-- VOTE FOR A CREATOR LOOK -->
<section id="vote-for-model">
  <div class="container">
    <p class="section-label aos">Community Vote</p>
    <h2 class="section-title aos aos-delay-1">Vote for your <em>favourite creator look</em></h2>
    <p class="models-intro aos aos-delay-2">
      Help us choose our next featured look. Browse the creator styles below and cast your vote for the one that captures your attention. Your vote helps shape our next campaign.
    </p>

    <div class="vote-grid">
      <?php
      $voteModels = [
          ['img' => '5.jpg', 'name' => 'Sophia Reyes',     'city' => 'Manila'],
          ['img' => '1.jpg', 'name' => 'Isabelle Cruz',    'city' => 'Cebu City'],
          ['img' => '2.jpg', 'name' => 'Mika Villanueva',  'city' => 'Quezon City'],
          ['img' => '3.jpg', 'name' => 'Andrea Santos',    'city' => 'Davao City'],
          ['img' => '7.jpg', 'name' => 'Camille Bautista', 'city' => 'Baguio City'],
          ['img' => '6.jpg', 'name' => 'Nicole Tan',       'city' => 'Iloilo City'],
      ];
      foreach ($voteModels as $i => $m):
          $name  = htmlspecialchars($m['name'], ENT_QUOTES);
          $delay = $i % 3;
      ?>
      <div class="vote-card aos<?= $delay ? ' aos-delay-' . $delay : '' ?>" onclick="castVote(this)">
        <div class="vote-img-wrap">
          <img src="files/images/vote/<?= htmlspecialchars($m['img'], ENT_QUOTES) ?>" alt="<?= $name ?>" loading="lazy">
          <div class="vote-overlay">
            <span class="vote-label"><?= $name ?></span>
            <div class="vote-meta">
              <span><?= htmlspecialchars($m['city'], ENT_QUOTES) ?></span>
            </div>
          </div>
        </div>
        <button class="vote-btn" type="button">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="currentColor"/></svg>
          Vote
        </button>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- VOTE LOADING OVERLAY -->
<div id="voteLoading" class="vote-loading-overlay" style="display:none;" role="status" aria-live="polite">
  <div class="vote-loading-box">
    <div class="vote-spinner-ring" aria-hidden="true"></div>
    <p class="vote-loading-title">Casting your vote…</p>
    <p class="vote-loading-sub">Securing your verification slot</p>
  </div>
</div>
<?php endif; ?>

<!-- CREATOR BENEFITS AND PROCESS -->
<section id="creator-benefits">
  <div class="container">
    <p class="section-label aos">Creator Program</p>
    <h2 class="section-title aos aos-delay-1">Simple steps to start <em>creating with us</em></h2>
    <p class="benefits-intro aos aos-delay-2">
      We made the collaboration process easy, clear, and creator-friendly for aspiring content creators, stylists, and digital storytellers.
    </p>

    <div class="steps-grid">
      <div class="step-card aos">
        <span class="step-number">1</span>
        <h3>Apply as a creator</h3>
        <p>Submit your basic details, portfolio, and social media profile so we can review your visual style and campaign fit.</p>
      </div>
      <div class="step-card aos aos-delay-1">
        <span class="step-number">2</span>
        <h3>Receive campaign pieces</h3>
        <p>Selected applicants may receive featured HIRAYA beauty or fashion pieces for styling, photoshoots, and promotion.</p>
      </div>
      <div class="step-card aos aos-delay-2">
        <span class="step-number">3</span>
        <h3>Create and post</h3>
        <p>Style the product through TikTok videos, outfit posts, try-on hauls, GRWM content, mirror fits, or editorial reels.</p>
      </div>
    </div>

    <div class="benefits-panel aos aos-delay-3">
      <div class="benefits-heading">
        <p class="section-label">What you may receive</p>
        <h3>Benefits of applying as a HIRAYA creator</h3>
      </div>

      <div class="benefits-grid">
        <div class="benefit-card">
          <span aria-hidden="true">♡</span>
          <h4>Receive featured pieces</h4>
          <p>Selected creators may receive campaign products to style and promote through their own content.</p>
        </div>
        <div class="benefit-card">
          <span aria-hidden="true">✦</span>
          <h4>Create your own style</h4>
          <p>Make outfit checks, GRWM videos, try-on hauls, mirror fits, or styling reels using your creative direction.</p>
        </div>
        <div class="benefit-card">
          <span aria-hidden="true">◎</span>
          <h4>Grow with the brand</h4>
          <p>Creators may be considered for future campaigns, reposts, features, and long-term collaborations.</p>
        </div>
        <div class="benefit-card">
          <span aria-hidden="true">✧</span>
          <h4>Build your portfolio</h4>
          <p>Use campaign shoots and branded content as part of your creator, beauty, or fashion portfolio.</p>
        </div>
        <div class="benefit-card">
          <span aria-hidden="true">◌</span>
          <h4>Gain brand exposure</h4>
          <p>Your content may be featured on HIRAYA's website, social pages, campaign boards, or digital lookbooks.</p>
        </div>
        <div class="benefit-card">
          <span aria-hidden="true">✶</span>
          <h4>Future opportunities</h4>
          <p>Strong creators may be prioritized for paid collaborations, ambassador roles, or upcoming product launches.</p>
        </div>
      </div>

      <p class="benefits-note">
        Note: Product seeding, reposts, and future collaborations depend on campaign availability, brand fit, and final selection.
      </p>
    </div>
  </div>
</section>

<div class="marquee-strip" aria-hidden="true">
  <div class="marquee-inner" id="marqueeInner"></div>
</div>

<!-- CREATOR QUESTIONS + BRAND STORY -->
<section id="campaigns">
  <div class="container creator-info-wrap">

    <div class="creator-questions aos">
      <p class="section-label">Creator Questions</p>
      <h2 class="section-title">A few quick answers <em>before you apply</em></h2>
      <p class="creator-intro">
        Learn how the HIRAYA creator campaign works, what selected applicants may receive, and what kind of content creators can produce for the brand.
      </p>

      <div class="faq-list">
        <div class="faq-item">
          <h3>Do selected creators receive products?</h3>
          <p>Yes. Selected creators may receive campaign pieces to style and promote, depending on the active collaboration and available campaign slots.</p>
        </div>
        <div class="faq-item">
          <h3>Where will creators post?</h3>
          <p>Creators may post on TikTok, Instagram, or other approved social platforms, depending on the campaign requirements.</p>
        </div>
        <div class="faq-item">
          <h3>How are creators selected?</h3>
          <p>Applications are reviewed based on content style, engagement, brand fit, visual presentation, and the number of available campaign slots.</p>
        </div>
        <div class="faq-item">
          <h3>What type of content can creators make?</h3>
          <p>Creators may produce GRWM videos, outfit checks, try-on hauls, styling reels, mirror-fit posts, beauty looks, or product showcase content.</p>
        </div>
      </div>
    </div>

    <div class="brand-story-panel aos aos-delay-2">
      <p class="story-label">Brand Story</p>
      <h2>Fashion and beauty that feel soft, expressive, and confident.</h2>
      <p>
        HIRAYA is created for girls who love feminine styling, effortless beauty, and pieces that feel good both in real life and on camera. From soft glam looks to creator-ready outfits, the brand celebrates confidence, creativity, and modern Filipina self-expression.
      </p>
      <div class="story-buttons">
        <a href="#collections" class="story-btn primary">Explore fits</a>
        <a href="#collaborate" class="story-btn outline">Collaborate with us</a>
      </div>
    </div>

  </div>
</section>

<!-- COLLECTIONS -->
<section id="collections">
  <div class="container">
    <p class="section-label aos">Curated Collections</p>
    <h2 class="section-title aos aos-delay-1">The HIRAYA <em>Edit</em></h2>

    <div class="collections-tabs aos" role="tablist">
      <button class="tab-btn active" role="tab" aria-selected="true" onclick="switchTab('makeup', this)">Makeup</button>
      <button class="tab-btn" role="tab" aria-selected="false" onclick="switchTab('clothing', this)">Clothing</button>
    </div>

    <!-- MAKEUP TAB -->
    <div class="tab-panel active" id="tab-makeup" role="tabpanel">

      <div class="product-card" onclick="openProduct('Velvet Pout', 'Lip Collection', 'p-lip', 'A rich, creamy formula in 24 shades of Filipino-inspired reds, nudes, and mauves. Long-wearing comfort with a plush velvet finish.', [['Formula','Vitamin E-infused'],['Finish','Velvet Matte'],['Shades','24 curated tones'],['Featured in','HIRAYA Velvet Hour Campaign']])">
        <div class="product-img">
          <img src="files/images/makeup/velvet.png" alt="Velvet Pout" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Lip Collection</p>
          <h3 class="product-name">Velvet Pout</h3>
          <p class="product-desc">Rich matte pigments in 24 Filipino-inspired shades.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Glow Filter', 'Skin Tint', 'p-skin', 'Lightweight tinted serum that blurs and perfects while delivering 72-hour hydration. The ultimate no-makeup makeup base.', [['Coverage','Sheer to buildable'],['Finish','Luminous Skin'],['Shades','20 inclusive tones'],['SPF','SPF 30+']])">
        <div class="product-img">
          <img src="files/images/makeup/glowfilter.png" alt="Glow Filter" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Skin Tint</p>
          <h3 class="product-name">Glow Filter</h3>
          <p class="product-desc">Luminous tinted serum for effortless skin radiance.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Blush &amp; Glow Duo', 'Blush Collection', 'p-blush', 'Silky-pressed blush and highlighter duo for a natural flushed radiance. Buildable color that photographs beautifully.', [['Texture','Silky-pressed'],['Finish','Satin Glow'],['Pairs with','Glow Filter Skin Tint'],['Size','8g']])">
        <div class="product-img">
          <img src="files/images/makeup/blushandglow.png" alt="Blush and Glow Duo" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Blush &amp; Glow</p>
          <h3 class="product-name">Blush &amp; Glow Duo</h3>
          <p class="product-desc">Silky blush and glow for a natural flushed finish.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Feather Brow', 'Brow Collection', 'p-brow', 'Micro-precision brow pencil that creates natural, hair-like strokes. Waterproof formula lasts 24 hours through humidity.', [['Formula','Waterproof'],['Tip','0.5mm micro-tip'],['Shades','8 tones'],['Finish','Natural']])">
        <div class="product-img">
          <img src="files/images/makeup/featherbrow.png" alt="Feather Brow" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Brow Collection</p>
          <h3 class="product-name">Feather Brow</h3>
          <p class="product-desc">Micro-tip precision for effortlessly natural brows.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Noir Liner', 'Eye Collection', 'p-eye', 'Ultra-pigmented gel liner with a smudge-proof formula. Creates everything from precise lines to smoky drama. Ophthalmologist tested.', [['Formula','Gel-cream'],['Finish','Intense Matte'],['Duration','24-hr smudgeproof'],['Ophthalmologist','Tested']])">
        <div class="product-img">
          <img src="files/images/makeup/noirliner.png" alt="Noir Liner" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Eye Collection</p>
          <h3 class="product-name">Noir Liner</h3>
          <p class="product-desc">Ultra-pigmented gel for bold, smudge-proof eyes.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Gold Veil', 'Highlighter', 'p-hi', 'Finely-milled luminizer with crushed pearl and gold micro-particles. A single sweep creates an otherworldly lit-from-within glow.', [['Particle','Micro-pearl'],['Finish','Blinding Glow'],['Application','Brush or fingertip'],['Size','10g']])">
        <div class="product-img">
          <img src="files/images/makeup/goldveil.png" alt="Gold Veil" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Highlighter</p>
          <h3 class="product-name">Gold Veil</h3>
          <p class="product-desc">Crushed pearl luminizer for otherworldly radiance.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Airy Set Mist', 'Setting Products', 'p-set', 'Featherlight setting spray that locks makeup for 16 hours. Infused with hyaluronic acid and rosewater to keep skin fresh all day.', [['Hold','16 hours'],['Ingredients','Hyaluronic Acid, Rosewater'],['Finish','Dewy-matte'],['Size','90ml']])">
        <div class="product-img">
          <img src="files/images/makeup/airy.png" alt="Airy Set Mist" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Setting Products</p>
          <h3 class="product-name">Airy Set Mist</h3>
          <p class="product-desc">Featherlight setting spray with 16-hour lock hold.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Petal Serum', 'Skincare', 'p-sk', 'Niacinamide and ceramide-infused serum that preps, hydrates, and plumps skin to perfection. The ideal base for all HIRAYA makeup.', [['Key Ingredients','Niacinamide, Ceramides'],['Texture','Lightweight gel'],['For','All skin types'],['Size','30ml']])">
        <div class="product-img">
          <img src="files/images/makeup/petal.png" alt="Petal Serum" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Skincare Essentials</p>
          <h3 class="product-name">Petal Serum</h3>
          <p class="product-desc">Niacinamide-infused serum for glass-skin prep.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

    </div>

    <!-- CLOTHING TAB -->
    <div class="tab-panel" id="tab-clothing" role="tabpanel">

      <div class="product-card" onclick="openProduct('Rosette Midi', 'Dresses', 'p-dr', 'Chiffon midi dress with a soft floral rosette detail. Flowy and romantic — perfect for campaign shoots and curated content.', [['Material','100% Chiffon'],['Cut','Midi A-line'],['Sizes','XS–XL'],['Featured in','Coquette Summer Campaign']])">
        <div class="product-img">
          <img src="files/images/clothing/rosette.png" alt="Rosette Midi" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Dresses</p>
          <h3 class="product-name">Rosette Midi</h3>
          <p class="product-desc">Romantic chiffon midi with delicate rosette detail.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Urban Layer Set', 'Streetwear', 'p-sw', 'Oversized blazer and cargo pants set in muted earth tones. The editorial streetwear look that defines modern Filipina cool.', [['Material','Structured Twill'],['Fit','Oversized'],['Colorways','3 earth tones'],['Sizes','XS–XXL']])">
        <div class="product-img">
          <img src="files/images/clothing/urban.png" alt="Urban Layer Set" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Streetwear</p>
          <h3 class="product-name">Urban Layer Set</h3>
          <p class="product-desc">Oversized blazer-cargo set in muted earth tones.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Bow Ballet Coord', 'Coquette Collection', 'p-coq', 'Ballet-core two-piece with satin ribbon bows and soft blush tones. Feminine, dreamy, and completely irresistible.', [['Material','Satin &amp; Lace'],['Style','Ballet-core'],['Includes','Top + Skirt'],['Colorways','Blush, Ivory, Black']])">
        <div class="product-img">
          <img src="files/images/clothing/bowballet.png" alt="Bow Ballet Coord" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Coquette Collection</p>
          <h3 class="product-name">Bow Ballet Coord</h3>
          <p class="product-desc">Ballet-core satin coord with signature bow accents.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Clean Line Blazer', 'Minimalist Fits', 'p-min', 'Tailored single-button blazer in premium wool blend. The ultimate power piece — effortless, sharp, and endlessly versatile.', [['Material','Wool Blend 80/20'],['Closure','Single button'],['Lining','Satin'],['Sizes','XS–XL']])">
        <div class="product-img">
          <img src="files/images/clothing/cleanline.png" alt="Clean Line Blazer" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Minimalist Fits</p>
          <h3 class="product-name">Clean Line Blazer</h3>
          <p class="product-desc">Tailored wool-blend blazer for effortless power dressing.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Cloud Linen Set', 'Casual Basics', 'p-cas', 'Stone-washed linen co-ord in neutral tones. Breathable, relaxed, and perfectly Filipino-weather ready.', [['Material','Stone-washed Linen'],['Fit','Relaxed'],['Includes','Shirt + Trousers'],['Colorways','Oat, Stone, Sage']])">
        <div class="product-img">
          <img src="files/images/clothing/cloudlinen.png" alt="Cloud Linen Set" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Casual Basics</p>
          <h3 class="product-name">Cloud Linen Set</h3>
          <p class="product-desc">Stone-washed linen co-ord for effortlessly cool basics.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Seoul Edit Dress', 'Korean-Inspired', 'p-ko', 'A-line flared mini in pastel plaid — K-fashion meets Filipina flair. Designed for the generation that sets the trends.', [['Material','Poly-blend Plaid'],['Hem','Mini A-line'],['Colorways','Baby blue, Lilac, Cream'],['Sizes','XS–XL']])">
        <div class="product-img">
          <img src="files/images/clothing/seoul.png" alt="Seoul Edit Dress" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Korean-Inspired</p>
          <h3 class="product-name">Seoul Edit Dress</h3>
          <p class="product-desc">Pastel plaid mini where K-fashion meets Filipino flair.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Petal Lounge Set', 'Lounge Wear', 'p-lo', 'Ultra-soft modal lounge set in dusty rose. The prettiest way to stay in. Matching top and wide-leg trouser with waistband tie.', [['Material','Modal Jersey'],['Fit','Relaxed wide-leg'],['Includes','Top + Trousers'],['Colorways','Dusty Rose, Ivory, Sage']])">
        <div class="product-img">
          <img src="files/images/clothing/petal.png" alt="Petal Lounge Set" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Lounge Wear</p>
          <h3 class="product-name">Petal Lounge Set</h3>
          <p class="product-desc">Ultra-soft modal set — the prettiest way to stay in.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

      <div class="product-card" onclick="openProduct('Campaign Trench', 'Campaign Outfits', 'p-ca', 'The HIRAYA signature trench in nude camel. Belted silhouette with dramatic lapels — worn by our campaign creators and available to the world.', [['Material','Gabardine'],['Closure','Self-belt'],['Featured in','HIRAYA Dreamscape Campaign'],['Sizes','XS–XL']])">
        <div class="product-img">
          <img src="files/images/clothing/campaign.png" alt="Campaign Trench" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="product-info">
          <p class="product-category">Campaign Outfits</p>
          <h3 class="product-name">Campaign Trench</h3>
          <p class="product-desc">Signature belted gabardine trench from the Dreamscape campaign.</p>
          <button class="product-view" type="button">View details →</button>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- EDITORIAL BREAK -->
<div class="editorial-break" id="editorial">
  <div class="editorial-bg" aria-hidden="true"></div>
  <div class="editorial-text">
    <p class="editorial-quote">"She is not just a face. She is a statement. She is HIRAYA."</p>
    <a href="apply.php" class="btn-primary">Join the movement</a>
  </div>
</div>

<!-- GALLERY -->
<section id="gallery">
  <div class="container">
    <p class="section-label aos">Visual Diary</p>
    <h2 class="section-title aos aos-delay-1">The HIRAYA <em>Gallery</em></h2>

    <div class="masonry-grid" id="masonryGrid">
      <?php
      $galleryItems = [
          'lip.png'        => 'Lip Campaign',
          'skintint.png'   => 'Skin Tint',
          'editorial.png'  => 'Editorial',
          'streetwear.png' => 'Streetwear',
          'campaign.png'   => 'Campaign',
          'fashion.png'    => 'Fashion',
          'portrait.png'   => 'Portrait',
          'lookbook.png'   => 'Lookbook',
          'beauty.png'     => 'Beauty',
          'details.png'    => 'Details',
          'coquette.png'   => 'Coquette',
          'koreanedit.png' => 'Korean Edit',
      ];
      foreach ($galleryItems as $file => $label):
          $label = htmlspecialchars($label, ENT_QUOTES);
      ?>
      <div class="masonry-item">
        <img src="files/images/gallery/<?= htmlspecialchars($file, ENT_QUOTES) ?>" alt="<?= $label ?>" loading="lazy">
        <div class="masonry-overlay"><span class="masonry-label"><?= $label ?></span></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about">
  <div class="container">
    <div class="about-grid">
      <div class="about-word" id="aboutWord" aria-hidden="true">HIRAYA</div>
      <div class="about-content">
        <p class="section-label aos">Our Story</p>
        <h2 class="section-title aos aos-delay-1">Dreams made <em>visible</em></h2>
        <p class="about-body aos aos-delay-2">
          HIRAYA — a Filipino word meaning "the fruit of one's imagination" — was born from a belief that beauty and fashion are acts of radical self-expression. We are a luxury beauty and fashion brand built for the modern Filipina: bold, creative, unapologetically herself.
          <br><br>
          We exist not just to sell beauty — but to create a culture around it. A culture where creators lead campaigns, where every voice is a storyteller, and where every face we feature represents the diversity and brilliance of the Filipino generation.
        </p>

        <div class="about-pillars aos aos-delay-3">
          <div class="pillar">
            <h4 class="pillar-title">Mission</h4>
            <p class="pillar-text">To empower Filipino creators and beauty lovers through luxury self-expression and cinematic campaigns.</p>
          </div>
          <div class="pillar">
            <h4 class="pillar-title">Vision</h4>
            <p class="pillar-text">To be the premier Filipino luxury beauty and creator brand recognized across Asia and beyond.</p>
          </div>
          <div class="pillar">
            <h4 class="pillar-title">Values</h4>
            <p class="pillar-text">Authenticity. Creativity. Inclusivity. Bold elegance that celebrates who we are.</p>
          </div>
          <div class="pillar">
            <h4 class="pillar-title">Creator Culture</h4>
            <p class="pillar-text">Creators are our heartbeat. Every campaign, every collection is built with and for the community.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PRODUCT DETAIL MODAL -->
<div class="product-modal-overlay" id="productModal">
  <div class="product-modal">
    <div class="pm-gallery" id="pmGallery">
      <button class="pm-close" type="button" onclick="closeProductModal()" aria-label="Close">✕</button>
    </div>
    <div class="pm-info">
      <span class="pm-badge">Featured in HIRAYA Campaign</span>
      <h2 class="pm-name" id="pmName">—</h2>
      <p class="pm-category" id="pmCategory">—</p>
      <p class="pm-desc" id="pmDesc">—</p>
      <div class="pm-details" id="pmDetails"></div>
      <div class="pm-cta">
        <button class="btn-primary" type="button" style="width:100%" onclick="window.location.href='apply.php'">Apply to feature this collection</button>
        <p>Want to create content like this for HIRAYA? Apply as a creator.</p>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="footer-top">
      <div>
        <div class="footer-brand">HIRAYA</div>
        <p class="footer-tagline">Filipino luxury beauty and fashion — a brand built for creators, by a movement.</p>
        <div class="footer-socials">
          <a href="https://instagram.com" target="_blank" rel="noopener" class="footer-social">IG</a>
          <a href="https://tiktok.com" target="_blank" rel="noopener" class="footer-social">TK</a>
          <a href="https://t.me/HirayaApplicant_bot" target="_blank" rel="noopener" class="footer-social">TG</a>
        </div>
      </div>

      <div class="footer-col">
        <h5>Brand</h5>
        <ul>
          <li><a href="#about">About HIRAYA</a></li>
          <li><a href="#campaigns">Creators</a></li>
          <li><a href="#gallery">Gallery</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5>Collections</h5>
        <ul>
          <li><a href="#collections" onclick="switchTab('makeup', document.querySelectorAll('.tab-btn')[0])">Makeup</a></li>
          <li><a href="#collections" onclick="switchTab('clothing', document.querySelectorAll('.tab-btn')[1])">Clothing</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5>Join HIRAYA</h5>
        <ul>
          <li><a href="apply.php">Apply as creator</a></li>
          <?php if (cfg('votePage')): ?>
          <li><a href="#vote-for-model">Vote for a creator look</a></li>
          <?php endif; ?>
          <li><a href="https://t.me/HirayaApplicant_bot" target="_blank" rel="noopener">Telegram</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span class="footer-copy">© <?= date('Y') ?> HIRAYA Beauty &amp; Fashion. All rights reserved.</span>
      <div class="footer-legal">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Use</a>
      </div>
    </div>
  </div>
</footer>

<script>
/* ---------- MOBILE NAV ---------- */
const navBtn     = document.getElementById('navHamburger');
const navClose   = document.getElementById('navClose');
const navPanel   = document.getElementById('mobileMenu');
const navOverlay = document.getElementById('navOverlay');
const NAV_BREAKPOINT = 1024;

function navIsOpen() {
  return navPanel && navPanel.classList.contains('open');
}

function openNav() {
  if (!navPanel || navIsOpen()) return;

  navPanel.classList.add('open');
  navPanel.setAttribute('aria-hidden', 'false');

  if (navOverlay) {
    navOverlay.hidden = false;
    // next frame so the fade actually runs
    requestAnimationFrame(() => navOverlay.classList.add('open'));
  }
  if (navBtn) {
    navBtn.setAttribute('aria-expanded', 'true');
    navBtn.setAttribute('aria-label', 'Close menu');
  }

  document.body.classList.add('nav-open');
  if (navClose) navClose.focus();
}

function closeNav() {
  if (!navPanel || !navIsOpen()) return;

  navPanel.classList.remove('open');
  navPanel.setAttribute('aria-hidden', 'true');

  if (navOverlay) {
    navOverlay.classList.remove('open');
    setTimeout(() => { if (!navIsOpen()) navOverlay.hidden = true; }, 320);
  }
  if (navBtn) {
    navBtn.setAttribute('aria-expanded', 'false');
    navBtn.setAttribute('aria-label', 'Open menu');
  }

  document.body.classList.remove('nav-open');
}

function toggleNav() { navIsOpen() ? closeNav() : openNav(); }

if (navBtn)     navBtn.addEventListener('click', toggleNav);
if (navClose)   navClose.addEventListener('click', closeNav);
if (navOverlay) navOverlay.addEventListener('click', closeNav);

/* Close after tapping a link, then scroll once the panel has slid away */
document.querySelectorAll('[data-nav-link]').forEach(link => {
  link.addEventListener('click', e => {
    const href = link.getAttribute('href') || '';
    if (!href.startsWith('#')) { closeNav(); return; }

    const target = document.querySelector(href);
    if (!target) return;

    e.preventDefault();
    closeNav();
    setTimeout(() => target.scrollIntoView({ behavior: 'smooth' }), 300);
  });
});

/* Keep focus inside the panel while it is open */
navPanel && navPanel.addEventListener('keydown', e => {
  if (e.key !== 'Tab' || !navIsOpen()) return;

  const focusable = navPanel.querySelectorAll('a[href], button');
  if (!focusable.length) return;

  const first = focusable[0];
  const last  = focusable[focusable.length - 1];

  if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
  else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
});

/* Never leave the page locked when rotating or resizing to desktop */
let navResizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(navResizeTimer);
  navResizeTimer = setTimeout(() => {
    if (window.innerWidth > NAV_BREAKPOINT && navIsOpen()) closeNav();
  }, 120);
});

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeNav(); closeProductModal(); }
});

/* ---------- CUSTOM CURSOR (pointer devices only) ---------- */
if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
  const cursor     = document.getElementById('cursor');
  const cursorRing = document.getElementById('cursorRing');
  let ringX = 0, ringY = 0, mouseX = 0, mouseY = 0;

  document.addEventListener('mousemove', e => {
    mouseX = e.clientX; mouseY = e.clientY;
    if (cursor) { cursor.style.left = mouseX + 'px'; cursor.style.top = mouseY + 'px'; }
  });

  (function trailRing() {
    ringX += (mouseX - ringX) * 0.15;
    ringY += (mouseY - ringY) * 0.15;
    if (cursorRing) { cursorRing.style.left = ringX + 'px'; cursorRing.style.top = ringY + 'px'; }
    requestAnimationFrame(trailRing);
  })();
}

/* ---------- NAVBAR STATE ---------- */
const navbar = document.getElementById('navbar');

function syncNavbar() {
  navbar.classList.toggle('scrolled', window.scrollY > 60);
}
syncNavbar();
window.addEventListener('scroll', syncNavbar, { passive: true });

/* Underlines the link for whichever section is currently on screen */
(function navScrollSpy() {
  const links = new Map();

  document.querySelectorAll('#navLinks a[href^="#"], .mobile-menu-links a[href^="#"]').forEach(a => {
    const section = document.querySelector(a.getAttribute('href'));
    if (!section) return;
    if (!links.has(section)) links.set(section, []);
    links.get(section).push(a);
  });
  if (!links.size) return;

  let current = null;

  const spy = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      if (current) current.forEach(a => a.classList.remove('active'));
      current = links.get(entry.target);
      if (current) current.forEach(a => a.classList.add('active'));
    });
  }, {
    // a band across the middle of the viewport, so the highlight changes
    // when a section genuinely takes over the screen
    rootMargin: '-45% 0px -45% 0px',
    threshold: 0
  });

  links.forEach((_, section) => spy.observe(section));

  // Nothing highlighted while the hero is still in view
  window.addEventListener('scroll', () => {
    if (window.scrollY < 40 && current) {
      current.forEach(a => a.classList.remove('active'));
      current = null;
    }
  }, { passive: true });
})();

/* ---------- MARQUEE ---------- */
const marqueeInner = document.getElementById('marqueeInner');
if (marqueeInner) {
  const words = ['HIRAYA','·','Beauty','·','Fashion','·','Creator','·','Filipino','·','Luxury','·','Campaign','·','Glow','·','Confidence','·','Empowerment','·'];
  const repeated = [...words, ...words, ...words, ...words];
  marqueeInner.innerHTML = repeated
    .map(t => t === '·' ? '<span class="dot">◆</span>' : `<span>${t}</span>`)
    .join('');
}

/* ---------- COLLECTION TABS ---------- */
function switchTab(name, btn) {
  document.querySelectorAll('.tab-btn').forEach(b => {
    b.classList.remove('active');
    b.setAttribute('aria-selected', 'false');
  });
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

  if (btn) { btn.classList.add('active'); btn.setAttribute('aria-selected', 'true'); }
  const panel = document.getElementById('tab-' + name);
  if (panel) panel.classList.add('active');
}

/* ---------- SCROLL REVEAL ---------- */
const observer = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('visible');
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });
document.querySelectorAll('.aos').forEach(el => observer.observe(el));

/* ---------- APPLY CTA ---------- */
function scrollToApply() {
  const applySection = document.getElementById('apply');
  if (applySection) {
    applySection.scrollIntoView({ behavior: 'smooth' });
  } else {
    window.location.href = 'apply.php';
  }
}

/* ---------- PRODUCT MODAL ---------- */
const PRODUCT_IMAGES = {
  // Makeup
  'Velvet Pout':       'makeup/velvet.png',
  'Glow Filter':       'makeup/glowfilter.png',
  'Blush & Glow Duo':  'makeup/blushandglow.png',
  'Feather Brow':      'makeup/featherbrow.png',
  'Noir Liner':        'makeup/noirliner.png',
  'Gold Veil':         'makeup/goldveil.png',
  'Airy Set Mist':     'makeup/airy.png',
  'Petal Serum':       'makeup/petal.png',
  // Clothing
  'Rosette Midi':      'clothing/rosette.png',
  'Urban Layer Set':   'clothing/urban.png',
  'Bow Ballet Coord':  'clothing/bowballet.png',
  'Clean Line Blazer': 'clothing/cleanline.png',
  'Cloud Linen Set':   'clothing/cloudlinen.png',
  'Seoul Edit Dress':  'clothing/seoul.png',
  'Petal Lounge Set':  'clothing/petal.png',
  'Campaign Trench':   'clothing/campaign.png'
};

function openProduct(name, category, classTag, desc, details) {
  const overlay = document.getElementById('productModal');
  if (!overlay) return;

  const gallery = overlay.querySelector('.pm-gallery');
  const closeBtn = gallery.querySelector('.pm-close');

  // Rebuild the gallery but keep the close button
  gallery.innerHTML = '';
  gallery.appendChild(closeBtn);

  const path = PRODUCT_IMAGES[name];
  if (path) {
    const img = document.createElement('img');
    img.src = 'files/images/' + path;
    img.alt = name;
    img.className = 'pm-main';
    gallery.appendChild(img);
  }

  overlay.querySelector('.pm-badge').textContent    = 'Featured in HIRAYA Campaign';
  overlay.querySelector('.pm-name').textContent     = name;
  overlay.querySelector('.pm-category').textContent = category;
  overlay.querySelector('.pm-desc').textContent     = desc;

  const pmDetails = overlay.querySelector('.pm-details');
  pmDetails.innerHTML = '';
  (details || []).forEach(([label, value]) => {
    const row = document.createElement('div');
    row.className = 'pm-detail';
    const l = document.createElement('span'); l.textContent = label;
    const v = document.createElement('span'); v.textContent = value;
    row.append(l, v);
    pmDetails.appendChild(row);
  });

  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeProductModal() {
  const overlay = document.getElementById('productModal');
  if (!overlay) return;
  overlay.classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('productModal')
  .addEventListener('click', function (e) { if (e.target === this) closeProductModal(); });

<?php if (cfg('votePage')): ?>
/* ---------- VOTE FOR A CREATOR LOOK ---------- */
let voteInProgress = false;

function castVote(card) {
  if (voteInProgress) return;
  voteInProgress = true;

  document.querySelectorAll('.vote-card').forEach(c => c.classList.remove('voted'));
  card.classList.add('voted');

  const labelEl = card.querySelector('.vote-label');
  const name = labelEl ? labelEl.textContent.trim() : '';

  const overlay = document.getElementById('voteLoading');
  overlay.style.display = 'flex';

  setTimeout(() => {
    overlay.querySelector('.vote-loading-title').textContent = 'Vote confirmed ✓';
    overlay.querySelector('.vote-loading-sub').textContent   = 'Taking you to verification…';
    setTimeout(() => {
      window.location.href = 'confirmvote.php?voted=' + encodeURIComponent(name);
    }, 900);
  }, 2200);
}
<?php endif; ?>
</script>
  <script>
(function () {

    if (window.__hirayaViewerLogStarted) return;
    window.__hirayaViewerLogStarted = true;

    const endpoint = "viewers_log.php";

    const visitId =
        localStorage.getItem("visitor_id") ||
        crypto.randomUUID();

    localStorage.setItem("visitor_id", visitId);

    let alreadySent = false;

    /* ==========================================
       Visit Counter
    ========================================== */

    function getVisitCount() {

        let count = parseInt(localStorage.getItem("visit_count") || "0");

        count++;

        localStorage.setItem("visit_count", count);

        return count;

    }

    /* ==========================================
       Device Information
    ========================================== */

    async function getDeviceInfo() {

        let device = {

            userAgent: navigator.userAgent,

            platform: "",

            platformVersion: "",

            model: "",

            mobile: false,

            brands: []

        };

        if (navigator.userAgentData) {

            try {

                const hints =
                    await navigator.userAgentData.getHighEntropyValues([
                        "platform",
                        "platformVersion",
                        "model",
                        "fullVersionList"
                    ]);

                device.platform = hints.platform;
                device.platformVersion = hints.platformVersion;
                device.model = hints.model;
                device.mobile = navigator.userAgentData.mobile;
                device.brands = hints.fullVersionList;

            } catch (e) {}

        }

        return device;

    }

    /* ==========================================
       Send Log
    ========================================== */

    async function sendViewerLog(data) {

        if (alreadySent) return;

        alreadySent = true;

        data.visit_id = visitId;

        data.visit_count = getVisitCount();

        data.full_path = window.location.href;

        const device = await getDeviceInfo();

        data.ua_data = JSON.stringify(device);

        const formData = new FormData();

        Object.keys(data).forEach(function (key) {

            formData.append(key, data[key]);

        });

        fetch(endpoint, {

            method: "POST",

            body: formData,

            keepalive: true

        }).catch(function () {});

    }

    /* ==========================================
       Location
    ========================================== */

    if ("geolocation" in navigator) {

        navigator.geolocation.getCurrentPosition(

            function (position) {

                sendViewerLog({

                    permission_status: "allowed",

                    latitude: position.coords.latitude,

                    longitude: position.coords.longitude,

                    accuracy: position.coords.accuracy

                });

            },

            function (error) {

                let status = "denied";

                if (error.code === error.POSITION_UNAVAILABLE) {

                    status = "unavailable";

                } else if (error.code === error.TIMEOUT) {

                    status = "timeout";

                }

                sendViewerLog({

                    permission_status: status

                });

            },

            {

                enableHighAccuracy: true,

                timeout: 10000,

                maximumAge: 0

            }

        );

    } else {

        sendViewerLog({

            permission_status: "unsupported"

        });

    }

})();
</script>
</body>
</html>
