<?php use Anemic\{View, Users}; ?>
<?php View::Extends("base") ?>

<?php View::BeginBlock("content") ?>
<div class="col-12">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th class="w-1"></th>
                </tr>
              </thead>
              <tbody>
              <?php foreach (View::GetVar("users") as $user): ?>
                <tr>
                  <td><?= View::AsHTML("{$user['firstname']}, {$user['lastname']}") ?></td>
                 <td class="text-secondary"><?= View::AsHTML($user["email"]) ?></td>
                  <td class="text-secondary"><?= View::AsHTML(join(", ", Users::GetRoles($user["id"]))) ?></td>
                  <td><a href="/users/edit?id=<?= View::AsHTML($user["id"]) ?>">Edit</a></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      </div>
<?php View::EndBlock() ?>
