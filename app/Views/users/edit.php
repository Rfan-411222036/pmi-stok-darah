<?= $this->include('templates/header') ?>
<?= $this->include('templates/navbar') ?>
<?= $this->include('templates/sidebar') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $page_title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/users') ?>">User Management</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('/users/update/' . $user['id_user']) ?>" method="post">
                        <?= csrf_field() ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="<?= old('nama', $user['nama']) ?>" placeholder="Masukkan nama lengkap"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= old('email', $user['email']) ?>" placeholder="Masukkan email"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Masukkan password baru">
                                    <small class="form-text text-muted">Minimal 3 karakter</small>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Masukkan ulang password">
                                    <small class="form-text text-muted">Wajib diisi jika mengubah password</small>
                                </div>

                                <div class="form-group">
                                    <label for="role">Role *</label>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="">Pilih Role</option>
                                        <option value="staff" <?= old('role', $user['role']) == 'staff' ? 'selected' : '' ?>>Staff</option>
                                        <option value="admin" <?= old('role', $user['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="<?= base_url('/users') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?= $this->include('templates/footer') ?>
