<?php use Anemic\{Auth, View}; ?>
<?php View::Extends("base") ?>

<?php View::BeginBlock("content") ?>
<div class="page page-center">
    <div class="container  py-4">
        <div class="col-12">
            <pre>
        ___                         _      ____  __  ______ 
       /   |  ____  ___  ____ ___  (_)____/ __ \/ / / / __ \
      / /| | / __ \/ _ \/ __ `__ \/ / ___/ /_/ / /_/ / /_/ /
     / ___ |/ / / /  __/ / / / / / / /__/ ____/ __  / ____/ 
    /_/  |_/_/ /_/\___/_/ /_/ /_/_/\___/_/   /_/ /_/_/      
            </pre>
        </div>
        <div>
            <?php if (! Auth::IsUserLogged()): ?>
            <a href="/users/login">Login</a> 
            <?php else: ?>
            <?= Auth::GetUser()["email"] ?>
            <a href="/users/logout">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php View::EndBlock() ?>
