<?php

declare(strict_types=1);

use Anemic\{View, Users, Roles};

Roles::RequireRoleLogged("admin");

View::Render("users.admin", [
    "title" => "User Administration",
    "users" => Users::List()
]);
