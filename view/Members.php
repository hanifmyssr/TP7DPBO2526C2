<h3>Member list</h3>
<table border = "1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Join Date</th>
        <th>Action</th>
    </tr>
    <?php foreach ($member->getAllMembers() as $m): ?>
    <tr>
        <td><?= $m["id"]?></td>
        <td><?= htmlspecialchars($m["name"])?></td>
        <td><?= htmlspecialchars($m["phone"])?></td>
        <td><?= htmlspecialchars($m["email"])?></td>
        <td><?= $m["join_date"]?></td>
        <td>
            <a href="?page=members&edit=<?= $m['id'] ?>">Edit</a>
            <form method="POST" action="?page=members" style="display:inline"
                onsubmit="return confirm('Yakin hapus member ini?')">
                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                <button type="submit" name="delete_member">DELETE</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3><?= $editMember ? 'Edit Member' : 'Add Member'?></h3>
<form method="POST" action="?page=me,bers?">
    <?php if ($editMember): ?>
        <input type="hidden" name="id" value="<?= $editMember['id'] ?>">
    <?php endif; ?>

    <label>Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($editMember['name'] ?? '') ?>">

    <label>Phone:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($editMember['phone'] ?? '') ?>">

    <label>Email:</label>
    <input type="text" name="email" value="<?= htmlspecialchars($editMember['email'] ?? '') ?>">

    <button type="submit" name="<?= $editMember ? 'update_member' : 'add_member' ?>">
        <?= $editMember ? 'Update' : 'Add' ?>
    </button>
    <?php if ($editMember): ?>
        <a href="?page=members">Cancel</a>
    <?php endif; ?>
</form>