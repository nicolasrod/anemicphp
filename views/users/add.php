<?php

use Anemic\{View, Page}; ?>

<?php View::Extends("base") ?>

<?php View::BeginBlock("content") ?>
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Add User</h2>
        <form action="<?= Page::Self() ?>" method="POST" autocomplete="off" novalidate>
            <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" name="firstname" placeholder="First name"
                    autocomplete="off"  value="<?= View::AsHTML($_POST["firstname"] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" name="lastname" placeholder="Last name"
                    autocomplete="off"  value="<?= View::AsHTML($_POST["lastname"] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" placeholder="Email address"
                    autocomplete="off"  value="<?= View::AsHTML($_POST["email"] ?? '') ?>">
            </div>
            <div class="mb-2">
                <label class="form-label"> Password </label>
                <input type="password" class="form-control" name="password" placeholder="Your password"
                    autocomplete="off">
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
        </form>
    </div>
</div>

<a href="/users/admin">Go Back</a>
<?php View::EndBlock() ?>