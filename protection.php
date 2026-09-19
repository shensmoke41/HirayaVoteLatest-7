<?php

$allowedDomains = [
    "hirayaph.onrender.com",
];

$currentHost = strtolower($_SERVER['HTTP_HOST'] ?? '');

if (!in_array($currentHost, $allowedDomains, true)) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>HIRAYA Protection</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:#f8f5f2;
font-family:Georgia,serif;
}

.card{
width:min(700px,90%);
background:#fff;
padding:70px 55px;
border-radius:18px;
box-shadow:0 30px 70px rgba(0,0,0,.08);
text-align:center;
}

.logo{
font-size:48px;
letter-spacing:12px;
margin-bottom:20px;
}

.line{
width:90px;
height:2px;
background:#c9909a;
margin:25px auto;
}

h1{
font-weight:400;
margin-bottom:18px;
font-size:34px;
}

p{
color:#666;
line-height:1.9;
font-size:16px;
}

.warning{
margin-top:35px;
padding:18px;
background:#faf5f6;
border:1px solid #edd6dc;
color:#b54c63;
letter-spacing:2px;
text-transform:uppercase;
font-size:13px;
}

.footer{
margin-top:35px;
font-size:13px;
color:#999;
}

</style>

</head>

<body>

<div class="card">

<div class="logo">HIRAYA</div>

<div class="line"></div>

<h1>Unauthorized Copy Detected</h1>

<p>
This website is licensed exclusively for authorized domains.
</p>

<p style="margin-top:18px">
This deployment failed domain verification.
Unauthorized copying, redistribution, modification,
or commercial use is prohibited.
</p>

<div class="warning">
DO NOT ATTEMPT TO STEAL THIS SCHEME
</div>

<div class="footer">
© 2026 HIRAYA. All Rights Reserved.
</div>

</div>

</body>
</html>

<?php
exit;
}
