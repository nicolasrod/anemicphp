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

