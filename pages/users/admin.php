<?php

declare(strict_types=1);

use Anemic\{View, Users, Auth};

Auth::RequireRole("admin");

View::Render("users.admin", [
    "title" => "User Administration",
    "users" => Users::List()
]);
