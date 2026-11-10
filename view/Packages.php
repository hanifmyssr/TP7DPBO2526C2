<h3>Package List</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Package Name</th>
        <th>Price</th>
        <th>Duration (days)</th>
        <th>Action</th>
    </tr>
    <?php foreach ($package->getAllPackages() as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['package_name']) ?></td>
        <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
        <td><?= $p['duration_days'] ?></td>
        <td>
            <a href="?page=packages&edit=<?= $p['id'] ?>">Edit</a>
            <form method="POST" action="?page=packages" style="display:inline"
                  onsubmit="return confirm('Yakin hapus paket ini?')">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" name="delete_package">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3><?= $editPackage ? 'Edit Package' : 'Add Package' ?></h3>
<form method="POST" action="?page=packages">
    <?php if ($editPackage): ?>
        <input type="hidden" name="id" value="<?= $editPackage['id'] ?>">
    <?php endif; ?>

    <label>Package Name:</label>
    <input type="text" name="package_name" value="<?= htmlspecialchars($editPackage['package_name'] ?? '') ?>">

    <label>Price:</label>
    <input type="number" name="price" min="1" step="any" value="<?= htmlspecialchars($editPackage['price'] ?? '') ?>">

    <label>Duration (days):</label>
    <input type="number" name="duration_days" min="1" step="1" value="<?= htmlspecialchars($editPackage['duration_days'] ?? '') ?>">

    <button type="submit" name="<?= $editPackage ? 'update_package' : 'add_package' ?>">
        <?= $editPackage ? 'Update' : 'Add' ?>
    </button>
    <?php if ($editPackage): ?>
        <a href="?page=packages">Cancel</a>
    <?php endif; ?>
</form>