<?php

use Anemic\{View, Page} ?>
<?php View::Extends("base") ?>

<?php View::BeginBlock("content") ?>
<div class="col-12">
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4"><?= View::GetStr("message") ?></h2>
            <form action="<?= Page::Self() ?>" method="POST" autocomplete="off" novalidate>
                <input type="hidden" name="id" value="<?= View::GetStr("id") ?>" />
                <div class="form-footer">
                    <button type="submit" name="submit" value="1" class="btn btn-primary w-100">Yes</button>
                    <button type="submit" name="submit" value="0" class="btn btn-secundary w-100">No</button>
                </div>
            </form>
        </div>
    </div>

    <a href="/users/admin">Go Back</a>
</div>
<?php View::EndBlock() ?>