<?php

declare(strict_types=1);

use Anemic\FlashType;

Anemic\Page::CheckValidMethod(["POST", "GET"]);

if (! Anemic\Page::IsPOST()) {
    Anemic\View::Render("users.login", []);
    return;
}

$email = $_POST["email"];
$password = $_POST["password"];

// TODO: Validations

if (! Anemic\Users::Login($email, $password)) {
    Anemic\Page::ErrorUnauthorized();
}

Anemic\View::FlashMsg("Login correct!", true);
Anemic\Page::Redirect(Anemic\Config::Get("initial_path", "/"));