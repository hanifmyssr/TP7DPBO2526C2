<!-- Tabel Riwayat & Daftar Keanggotaan -->
<h3>Membership List</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Member</th>
        <th>Package</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Total Paid</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($membership->getAllMemberships() as $ms): ?>
    <tr>
        <td><?= $ms['id'] ?></td>
        <td><?= htmlspecialchars($ms['member_name']) ?></td>
        <td><?= htmlspecialchars($ms['package_name']) ?></td>
        <td><?= $ms['start_date'] ?></td>
        <td><?= $ms['end_date'] ?></td>
        <td>Rp <?= number_format($ms['total_paid'], 0, ',', '.') ?></td>
        <td><?= htmlspecialchars($ms['status']) ?></td>
        <td>
            <a href="?page=memberships&edit=<?= $ms['id'] ?>">Edit</a>
            <form method="POST" action="?page=memberships" style="display:inline"
                  onsubmit="return confirm('Yakin hapus keanggotaan ini?')">
                <input type="hidden" name="id" value="<?= $ms['id'] ?>">
                <button type="submit" name="delete_membership">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if ($editMembership): ?>

    <!-- Form Edit Status Keanggotaan -->
    <h3>Edit Membership Status</h3>
    <form method="POST" action="?page=memberships">
        <input type="hidden" name="id" value="<?= $editMembership['id'] ?>">

        <p>Member: <?= htmlspecialchars($editMembership['member_name']) ?></p>
        <p>Package: <?= htmlspecialchars($editMembership['package_name']) ?></p>
        <p>Period: <?= $editMembership['start_date'] ?> to <?= $editMembership['end_date'] ?></p>

        <label>Status:</label>
        <select name="status">
            <?php foreach (['active', 'expired', 'cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $editMembership['status'] === $s ? 'selected' : '' ?>>
                    <?= $s ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="update_membership">Update</button>
        <a href="?page=memberships">Cancel</a>
    </form>

<?php else: ?>

    <!-- Form Tambah Transaksi Keanggotaan Baru -->
    <h3>Add Membership</h3>
    <form method="POST" action="?page=memberships">
        <label>Member:</label>
        <select name="member_id">
            <?php foreach ($member->getAllMembers() as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Package:</label>
        <select name="package_id">
            <?php foreach ($package->getAllPackages() as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['package_name']) ?> - Rp <?= number_format($p['price'], 0, ',', '.') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Start Date:</label>
        <input type="date" name="start_date" value="<?= date('Y-m-d') ?>">

        <button type="submit" name="add_membership">Add</button>
    </form>

<?php endif; ?>