<?php use Anemic\{Auth, View} ?>
</body>
</html>

<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta20
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= View::GetStr("title") ?></title>

    <!-- CSS files -->
    <link href="/dist/css/tabler.min.css?1692870487" rel="stylesheet"/>
    <link href="/dist/css/tabler-flags.min.css?1692870487" rel="stylesheet"/>
    <link href="/dist/css/tabler-payments.min.css?1692870487" rel="stylesheet"/>
    <link href="/dist/css/tabler-vendors.min.css?1692870487" rel="stylesheet"/>
    <link href="/dist/css/demo.min.css?1692870487" rel="stylesheet"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>

    <?= View::GetBlock("header") ?>
  </head>

  <body  class=" d-flex flex-column">
    <script src="/dist/js/demo-theme.min.js?1692870487"></script>
 
 <div class="page">
    <header class="navbar navbar-expand-md d-print-none">
        <div class="container-xl">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <?= View::GetStr("title") ?>
          </div>
          <div class="navbar-nav flex-row order-md-last">
            <div class="nav-item d-none d-md-flex me-3">
              <div class="btn-list">
              </div>
            </div>
            <div class="d-none d-md-flex">
            </div>
            <?php 
              $user = Auth::GetUser();
              $user_id = $user["id"] ?? -1;
              $current_user = $user["email"] ?? 'Anonymous'; 
            ?>
            <div class="nav-item dropdown">
              <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                <div class="d-none d-xl-block ps-2">
                <div><?= View::AsHTML($current_user) ?></div>
                  <div class="mt-1 small text-secondary"></div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <?php foreach (View::GetVar("user_menu") as $item): ?>
              <?php
                $url = $item["url"];
                $name = $item["item"];
                $when = $item["when"] ?: '';

                if (is_callable($when) and ! $when($user_id, $current_user)) {
                  continue;
                }
                ?>
                <a href="<?= $url ?>" class="dropdown-item"><?= View::AsHTML($name) ?></a>
              <?php endforeach; ?>
              
              <?php if ($current_user === "Anonymous"): ?>
                <a href="/users/login" class="dropdown-item">Log In</a>
              <?php else: ?>
                <a href="/users/logout" class="dropdown-item">Logout</a>
              <?php endif; ?>
              </div>


            </div>
          </div>
        </div>
      </header>
     </header>
     

   <div class="page-wrapper">
       <div class="page-body">
          <div class="container-xl my-auto">
   
    <?php $error = View::GetFlasgMsg() ?>
    <?php if (!empty($error)): ?>
      <div class="alert alert-<?= $error['type'] ?>" role="alert">
        <?= $error["msg"] ?>
      </div>
    <?php endif; ?>
    
    <?= View::GetBlock("content") ?>

</div>
</div>
</div>
</div>

    <script src="/dist/js/tabler.min.js?1692870487" defer></script>
    <script src="/dist/js/demo.min.js?1692870487" defer></script>
  </body>
</html>
