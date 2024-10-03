<?php

declare(strict_types=1);

use Anemic\{Db, Users, Roles, Config};

$ok = Db::RunInTx(function ($db) {
    // Create USERS and ROLES tables in Auth DB
    $db->exec(<<<SQL
        create table if not exists users (
            id integer primary key autoincrement, 
            email,
            firstname,
            lastname,
            password_hash,
            unique(email)
        );
        create unique index users_email on users(email);
        create table if not exists roles (
            id integer primary key autoincrement,
            id_user integer,
            name,
            unique(id_user, name),
            foreign key(id_user) REFERENCES users(id)
        );
        create unique index roles_users on roles(id_user, name);
    SQL);

    Users::Add("admin@local.host", "admin1234", "Local", "Administrator");
    Users::Add("user@local.host", "user1234", "Local", "User");
    Users::GrantRole(Users::GetId("admin@local.host"), "admin");
}, Config::Get("auth_database"));

if (! $ok) {
    $error = Db::GetLastError();
    echo "Error: {$error->getMessage()} ({$error->getFile()} - {$error->getLine()}";
} else {
    echo "- Auth DB created!";
}
