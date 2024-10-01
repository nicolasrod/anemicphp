<?php

declare(strict_types=1);

use Anemic\FlashType;

Anemic\Page::CheckValidMethod(["POST", "GET"]);

if (! Anemic\Page::IsPOST()) {
    Anemic\View::Render("users.login", []);
    return;
}

$errors = Anemic\Validate::Check($_POST, [
    "email" => 'required | email | encode',
    "password" => "required | alphanumeric | range: 12,255"
]);

if (! empty($errors)) {
    Anemic\View::FlashMsg(join("<br/>", array_values($errors)), false);
    Anemic\Page::RedirectToSelf();
}

$email = $_POST["email"];
$password = $_POST["password"];

if (! Anemic\Users::Login($email, $password)) {
    Anemic\Page::ErrorUnauthorized();
}

Anemic\View::FlashMsg("Login correct!", true);
Anemic\Page::Redirect(Anemic\Config::Get("initial_path", "/"));