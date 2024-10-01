# AnemicPHP framework

```
    ___                         _      ____  __  ______ 
   /   |  ____  ___  ____ ___  (_)____/ __ \/ / / / __ \
  / /| | / __ \/ _ \/ __ `__ \/ / ___/ /_/ / /_/ / /_/ /
 / ___ |/ / / /  __/ / / / / / / /__/ ____/ __  / ____/ 
/_/  |_/_/ /_/\___/_/ /_/ /_/_/\___/_/   /_/ /_/_/      
```

Minimalistic and PHP oriented framework that doesn't try to change the nature of PHP.

A few things it tries to achieve:

- [x] PHPStan Level 8 compliant

```
phpstan analyse -l 8 libs pages views
```
- [x] Views are PHP scripts with some logic to have output encoding and master layout pages
- [x] Minimal glue to make PHP bearable, without redefining things already OK in PHP
- [x] Poor's man MVC: maintain old-school folder as router paradigm
- [x] Framework you can understand in a few minutes of looking at its source code
- [x] Maintaining magic to a minimum
- [x] Tabler.IO included
- [x] Database layer included: SQLite3 only (tuned for concurrent access) with separate Auth database and Application database
- [x] Ability to install and run composer packages. Wanna change the Database layer? The Auth layer? No problem!
- [x] Quickstart
- [x] Form Validations
- [ ] Security Features
- [ ] Deployment guides
- [ ] FAQs
- [ ] CRUD UI for Users and Roles
- [ ] User Self-signing?
- [ ] CRUD generator
- [ ] Documentation
