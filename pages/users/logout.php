<?php

declare(strict_types=1);

use Anemic\{Page, Auth, Config};

// Make this page ONLY respond to HTTP GET requests
Page::CheckValidMethod(["GET"]);

Auth::Logout();
Page::Redirect(Config::Get("initial_path", "/"));

