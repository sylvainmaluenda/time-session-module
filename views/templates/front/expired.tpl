<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Session expirée</title>

  <!-- Tailwind -->
  <!-- npx @tailwindcss/cli -i views/css/input.css -o views/css/output.css --watch -->
  <!-- <link rel="stylesheet" href="{$module_path|escape:'htmlall':'UTF-8'}views/css/output.css"> -->

  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      align-items: center;

      justify-content: center;
      font-family: Arial;
      background: #f4f6f9;
    }

    .box {
      text-align: center;
    }

    a {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 25px;
      background: #2e86de;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <!--
  <iframe
      class="fixed top-0 left-0 w-full h-full"
      src="https://psconfigurator.com">
  </iframe>
  -->
  <div class="box">
    <h1>Time's up!</h1>
    <p>Thanks for checking our demo</p>
    <a href="{$urls.shop_domain_url|escape:'htmlall':'UTF-8'}/{$admin_path|escape:'htmlall':'UTF-8'}">Connexion</a>
  </div>
</body>

</html>