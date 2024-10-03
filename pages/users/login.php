<?php

declare(strict_types=1);

use Anemic\{Page, Validate, Users, View};

Page::CheckValidMethod(["POST", "GET"]);

if (! Page::IsPOST()) {
    View::Render("users.login", []);
    return;
}

// Validate POST fields
$errors = Validate::Check($_POST, [
    "email" => 'required | email | encode',
    "password" => "required | alpha | range: 8,255"
]);

Validate::RedirectOnError($errors);

$email = $_POST["email"];
$password = $_POST["password"];

if (! Users::Login($email, $password)) {
    Page::ErrorUnauthorized();
}

View::FlashMsgGood("Login correct!");
Page::Redirect(Anemic\Config::Get("initial_path", "/"));
