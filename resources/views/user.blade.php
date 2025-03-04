<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data User</title>
</head>
<body>
    <h1>Data User</h1>
    <table border="1" cellpadding="2" cellspacing="0">
        <tr>
            <td>ID</td>
            <td>Username</td>
            <td>Nama</td>
            <td>Level</td>
            <td>AKsi</td>
        </tr>
        <?php foreach ($data as $d) { ?>
        <tr>
            <td><?php echo $d->user_id; ?></td>
            <td><?php echo $d->username; ?></td>
            <td><?php echo $d->nama; ?></td>
            <td><?php echo $d->level; ?></td>
            <td>
                <a href="/user/ubah/<?php echo $d->user_id; ?>">Ubah</a> |
                <a href="/user/hapus/<?php echo $d->user_id; ?>">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
