<?php

declare(strict_types=1);

use Anemic\{Page, Validate, Users, View};

Page::CheckValidMethod(["POST", "GET"]);
Page::PopState();

if (Page::IsPOST()) {
    $errors = Validate::Check($_POST, [
        "firstname" => 'required | alpha | range:3,255',
        "lastname" => "required | alpha | range:3,255",
        "password" => "required | range:8,255",
        "email" => "required | email | range:6,255"
    ]);

    Validate::RedirectOnError($errors);

    if (! Users::Add($_POST["email"], $_POST["password"], $_POST["firstname"], $_POST["lastname"])) {
        View::FlashMsgError("Error adding user");
    } else {
        View::FlashMsgGood("User added!");
    }

    Page::Redirect("/users/admin");
}

View::Render("users.add", [
    "title" => "Add User"
]);

