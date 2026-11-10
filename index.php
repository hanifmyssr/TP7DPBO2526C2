<?php
session_start();
require_once 'class/Member.php';
require_once 'class/Package.php';
require_once 'class/Membership.php';

$member = new Member();
$package = new Package();
$membership = new Membership();

function findById($rows, $id) {
    foreach ($rows as $row) {
        if ((int) $row['id'] === $id) {
            return $row;
        }
    }
    return null;
}

$page = $_GET['page'] ?? 'members';
$error = '';
$success = '';
$editMember = null;
$editPackage = null;
$editMembership = null;

// ===== MEMBER =====
if (isset($_POST['add_member']) || isset($_POST['update_member'])) {
    $id    = (int) ($_POST['id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $phone === '' || $email === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        if (isset($_POST['add_member'])) {
            $result = $member->createMember($name, $phone, $email);
            $okMessage = 'Member berhasil ditambahkan.';
        } else {
            $result = $member->updateMember($id, $name, $phone, $email);
            $okMessage = 'Member berhasil diperbarui.';
        }

        if ($result === true) {
            $success = $okMessage;
        } elseif ($result === 'duplicate') {
            $error = 'Email sudah terdaftar.';
        } else {
            $error = 'Gagal menyimpan data.';
        }
    }
}

if (isset($_POST['delete_member'])) {
    $result = $member->deleteMember((int) ($_POST['id'] ?? 0));

    if ($result === true) {
        $success = 'Member berhasil dihapus.';
    } elseif ($result === 'in use') {
        $error = 'Member tidak bisa dihapus karena masih memiliki data keanggotaan.';
    } else {
        $error = 'Gagal menghapus data.';
    }
}

// ===== PACKAGE =====
if (isset($_POST['add_package']) || isset($_POST['update_package'])) {
    $pkgId    = (int) ($_POST['id'] ?? 0);
    $pkgName  = trim($_POST['package_name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $duration = trim($_POST['duration_days'] ?? '');

    if ($pkgName === '' || $price === '' || $duration === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Harga harus berupa angka lebih dari 0.';
    } elseif (!ctype_digit($duration) || (int) $duration <= 0) {
        $error = 'Durasi harus berupa bilangan bulat lebih dari 0.';
    } else {
        if (isset($_POST['add_package'])) {
            $result = $package->createPackage($pkgName, $price, (int) $duration);
            $okMessage = 'Paket berhasil ditambahkan.';
        } else {
            $result = $package->updatePackage($pkgId, $pkgName, $price, (int) $duration);
            $okMessage = 'Paket berhasil diperbarui.';
        }

        if ($result === true) {
            $success = $okMessage;
        } else {
            $error = 'Gagal menyimpan data.';
        }
    }
}

if (isset($_POST['delete_package'])) {
    $result = $package->deletePackage((int) ($_POST['id'] ?? 0));

    if ($result === true) {
        $success = 'Paket berhasil dihapus.';
    } elseif ($result === 'in use') {
        $error = 'Paket tidak bisa dihapus karena masih dipakai data keanggotaan.';
    } else {
        $error = 'Gagal menghapus data.';
    }
}

// ===== MEMBERSHIP =====
if (isset($_POST['add_membership'])) {
    $memberId  = (int) ($_POST['member_id'] ?? 0);
    $packageId = (int) ($_POST['package_id'] ?? 0);
    $startDate = trim($_POST['start_date'] ?? '');

    if ($memberId === 0 || $packageId === 0 || $startDate === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!strtotime($startDate)) {
        $error = 'Tanggal mulai tidak valid.';
    } elseif ($membership->hasActiveMembership($memberId)) {
        $error = 'Member masih memiliki keanggotaan aktif.';
    } else {
        $pkg = findById($package->getAllPackages(), $packageId);

        if (!$pkg) {
            $error = 'Paket tidak ditemukan.';
        } else {
            $result = $membership->createMembership(
                $memberId, $packageId, $startDate, $pkg['price'], $pkg['duration_days']
            );

            if ($result === true) {
                $success = 'Keanggotaan berhasil dibuat.';
            } elseif ($result === 'invalid_reference') {
                $error = 'Member atau paket tidak valid.';
            } else {
                $error = 'Gagal menyimpan data.';
            }
        }
    }
}

if (isset($_POST['update_membership'])) {
    $msId   = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($membership->updateMembership($msId, $status) === true) {
        $success = 'Status keanggotaan berhasil diperbarui.';
    } else {
        $error = 'Gagal memperbarui status.';
    }
}

if (isset($_POST['delete_membership'])) {
    if ($membership->deleteMembership((int) ($_POST['id'] ?? 0)) === true) {
        $success = 'Keanggotaan berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus data.';
    }
}

// ===== REDIRECT SETELAH AKSI (POST) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targets = [
        'add_member' => 'members', 'update_member' => 'members', 'delete_member' => 'members',
        'add_package' => 'packages', 'update_package' => 'packages', 'delete_package' => 'packages',
        'add_membership' => 'memberships', 'update_membership' => 'memberships', 'delete_membership' => 'memberships',
    ];

    $redirect = $page;
    foreach ($targets as $button => $target) {
        if (isset($_POST[$button])) {
            $redirect = $target;
        }
    }

    $_SESSION['flash'] = [
        'type'    => $error !== '' ? 'error' : 'success',
        'message' => $error !== '' ? $error : $success,
    ];

    header('Location: index.php?page=' . $redirect);
    exit;
}

// ===== AMBIL PESAN SETELAH REDIRECT =====
if (isset($_SESSION['flash'])) {
    if ($_SESSION['flash']['type'] === 'error') {
        $error = $_SESSION['flash']['message'];
    } else {
        $success = $_SESSION['flash']['message'];
    }
    unset($_SESSION['flash']);
}

// ===== MODE EDIT =====
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];

    if ($page === 'members') {
        $editMember = findById($member->getAllMembers(), $editId);
    } elseif ($page === 'packages') {
        $editPackage = findById($package->getAllPackages(), $editId);
    } elseif ($page === 'memberships') {
        $editMembership = findById($membership->getAllMemberships(), $editId);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GymHub - Gym Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'view/header.php'; ?>

    <main>
        <nav>
            <a href="?page=members" class="<?= $page === 'members' ? 'active' : '' ?>">Members</a>
            <a href="?page=packages" class="<?= $page === 'packages' ? 'active' : '' ?>">Packages</a>
            <a href="?page=memberships" class="<?= $page === 'memberships' ? 'active' : '' ?>">Memberships</a>
        </nav>

        <?php if ($error !== ''): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php
        $pages = [
            'members'     => 'view/members.php',
            'packages'    => 'view/packages.php',
            'memberships' => 'view/memberships.php',
        ];
        if (isset($pages[$page])) {
            include $pages[$page];
        }
        ?>
    </main>

    <?php include 'view/footer.php'; ?>
</body>
</html>