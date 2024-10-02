<?php

declare(strict_types=1);

Anemic\View::Render("home.index", [
    "title" => "Home",
    "user_menu" => [
        [
            "url" => "/users/admin",
            "item" => "Users Administration",
            "when" => function ($id, $email) {
                return $email === "admin@local.host";
            }
        ]
    ]
]);
