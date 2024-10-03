<?php

use Anemic\View; ?>
<?php View::Extends("base") ?>

<?php View::BeginBlock("content") ?>
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Login to your account</h2>
                <form action="/users/login" method="POST" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" placeholder="Email address"
                            autocomplete="off">
                    </div>
                    <div class="mb-2">
                        <label class="form-label"> Password </label>
                        <input type="password" class="form-control" name="password" placeholder="Your password"
                            autocomplete="off">
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php View::EndBlock() ?>