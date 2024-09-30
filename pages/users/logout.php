<?php

declare(strict_types=1);

// Make this page ONLY respond to HTTP GET requests
Anemic\Page::CheckValidMethod(["GET"]);

Anemic\Auth::Logout();
Anemic\Page::Redirect(Anemic\Config::Get("initial_path", "/"));