<?php

declare(strict_types=1);

use Anemic\{Page, Validate, Users, View};

Page::CheckValidMethod(["POST", "GET"]);

if (Page::IsPOST()) {
    $errors = Validate::Check($_POST, [
        "id" => 'required',
        "firstname" => 'required | alpha | range:3,255',
        "lastname" => "required | alpha | range:3,255"
    ]);

    Validate::RedirectOnError($errors, Page::QS("id", $_POST['id']));

    $id = (int) $_POST["id"];
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];

    Page::TraceVar($id);

    if (! Users::Update($id, $firstname, $lastname)) {
        View::FlashMsgError("Error updating user");
    } else {
        View::FlashMsgGood("User updated!");
    }

    Page::Redirect("/users/admin");
}

$id = (int) ($_GET["id"] ?? -1);

$user = Users::Get($id);
if (empty($user)) {
    View::FlashMsgError("User not found!");
    Page::Redirect("/users/admin");
}

View::Render("users.edit", ["id" => $id, "user" => $user]);