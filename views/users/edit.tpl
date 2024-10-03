<?php

use Anemic\{View, Users} ?>
<?php View::Extends("base") ?>

<?php View::BeginBlock("content") ?>
<div class="col-12">
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">Edit User</h2>
            <form action="/users/edit" method="POST" autocomplete="off" novalidate>
                <input type="hidden" name="id" value="<?= View::GetStr("id") ?>" />
                <?php $user = View::GetVar("user") ?>
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="firstname" placeholder="First name"
                        autocomplete="off" value="<?= View::AsHTML($user["firstname"]) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lastname" placeholder="Last name"
                        autocomplete="off" value="<?= View::AsHTML($user["lastname"]) ?>">
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::EndBlock() ?>