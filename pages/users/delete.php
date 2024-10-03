<?php

use Anemic\{Page, Validate, Users, View};

Page::CheckValidMethod(["POST", "GET"]);

if (Page::IsPOST()) {
    if ($_POST["submit"] === "0") {
        Page::Redirect("/users/admin");        
    }

    $errors = Validate::Check($_POST, [
        "id" => 'required',
    ]);

    Validate::RedirectOnError($errors, Page::QS("id", $_POST['id']));

    $id = (int) $_POST["id"];

    if (! Users::Delete($id)) {
        View::FlashMsgError("Error deleting user");
    } else {
        View::FlashMsgGood("User deleted!");
    }

    Page::Redirect("/users/admin");
}

$id = (int) ($_GET["id"] ?? -1);

$user = Users::Get($id);
if (empty($user)) {
    View::FlashMsgError("User not found!");
    Page::Redirect("/users/admin");
}

View::Render("confirm", [
    "id" => $id, 
    "title" => "Delete User?", 
    "message" => "Do you really want to delete the user {$user['email']}?"
]);

