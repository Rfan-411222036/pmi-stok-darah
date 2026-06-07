<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloodjek - Masuk</title>
    <link rel="icon" type="image/png" href="<?= base_url('/icon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #fdf2f2 0%, #f0f2f5 50%, #fce8e8 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .login-card {
            width: min(100%, 1200px);
            border-radius: 36px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(220, 53, 69, 0.08);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            background: #ffffff;
        }

        /* ── Left panel ── */
        .login-panel-left {
            position: relative;
            padding: 72px 56px;
            background: linear-gradient(160deg, #fff4f4 0%, #ffffff 45%, #fde8e8 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 24px;
            text-align: center;
            overflow: hidden;
        }

        .login-panel-left::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(220, 53, 69, 0.07) 0%, transparent 70%);
            top: -120px;
            left: -80px;
            pointer-events: none;
        }

        .login-panel-left::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(220, 53, 69, 0.05) 0%, transparent 70%);
            bottom: -60px;
            right: -60px;
            pointer-events: none;
        }

        .login-panel-left > * {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: min(320px, 80%);
            height: auto;
            filter: drop-shadow(0 8px 24px rgba(180, 0, 0, 0.12));
        }

        .brand-divider {
            width: 48px;
            height: 3px;
            background: linear-gradient(90deg, #dc3545, #ff6b7a);
            border-radius: 99px;
            margin: 0 auto;
        }

        .tagline {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #444444;
            font-size: 1.05rem;
            line-height: 1.8;
            max-width: 380px;
            font-weight: 500;
        }

        .info-list {
            display: grid;
            gap: 14px;
            max-width: 420px;
            text-align: left;
            margin-top: 4px;
        }

        .info-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(220, 53, 69, 0.1);
            border-radius: 14px;
            padding: 12px 16px;
            backdrop-filter: blur(4px);
        }

        .info-icon {
            color: #dc3545;
            font-size: 1rem;
            margin-top: 2px;
            flex-shrink: 0;
            width: 18px;
        }

        .info-text {
            color: #5a5a5a;
            font-size: 0.92rem;
            line-height: 1.6;
            font-family: 'Inter', sans-serif;
        }

        /* ── Right panel ── */
        .login-panel-right {
            padding: 56px 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-card-inner {
            border: 1.5px solid rgba(220, 53, 69, 0.12);
            border-radius: 28px;
            padding: 40px 36px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.07);
            background: #ffffff;
        }

        .login-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: #111111;
            margin-bottom: 6px;
            letter-spacing: -0.03em;
        }

        .login-subtitle {
            color: #888888;
            margin-bottom: 28px;
            line-height: 1.65;
            font-size: 0.93rem;
        }

        .input-label {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: #444444;
            margin-bottom: 6px;
            display: block;
            letter-spacing: 0.01em;
        }

        .form-control {
            border-radius: 14px;
            border: 1.5px solid #e8ecf0;
            background: #f9fafb;
            height: 52px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: #111111;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .input-group-text {
            border-radius: 0 14px 14px 0;
            border: 1.5px solid #e8ecf0;
            border-left: none;
            background: #f9fafb;
            color: #dc3545;
            min-width: 50px;
            justify-content: center;
            transition: border-color 0.2s;
        }

        .input-group .form-control {
            border-right: none;
            border-radius: 14px 0 0 14px;
        }

        .input-group .form-control:focus {
            box-shadow: none;
            border-color: #dc3545;
        }

        .input-group .form-control:focus + .input-group-append .input-group-text {
            border-color: #dc3545;
            background: #ffffff;
        }

        .form-check-label {
            color: #666666;
            font-size: 0.92rem;
            font-family: 'Inter', sans-serif;
        }

        .btn-login {
            border-radius: 14px;
            padding: 15px 0;
            background: linear-gradient(135deg, #dc3545 0%, #b02535 100%);
            border: none;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            transition: all 0.25s ease;
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #c9293a 0%, #991f2d 100%);
            box-shadow: 0 8px 28px rgba(220, 53, 69, 0.4);
            transform: translateY(-1px);
            color: #ffffff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer-note {
            color: #b0b8c1;
            margin-top: 22px;
            font-size: 0.83rem;
            line-height: 1.65;
            text-align: center;
            font-family: 'Inter', sans-serif;
        }

        .footer-note a {
            color: #dc3545;
            text-decoration: none;
        }

        .alert {
            border-radius: 14px;
            font-family: 'Inter', sans-serif;
            font-size: 0.92rem;
        }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .login-card {
                grid-template-columns: 1fr;
                border-radius: 28px;
            }

            .login-panel-left {
                padding: 48px 32px 36px;
            }

            .login-panel-right {
                padding: 32px 28px 48px;
            }

            .brand-logo {
                width: min(280px, 75%);
            }
        }

        @media (max-width: 575px) {
            .login-wrapper {
                padding: 16px;
            }

            .login-card {
                border-radius: 24px;
            }

            .login-panel-left {
                padding: 40px 20px 28px;
            }

            .login-panel-right {
                padding: 20px 20px 40px;
            }

            .login-card-inner {
                padding: 28px 20px;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body class="hold-transition">
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Left: Branding -->
            <div class="login-panel-left">
                <img src="<?= base_url('/logo.png') ?>" alt="BLOODJek Logo" class="brand-logo">

                <div class="brand-divider"></div>

                <!-- <p class="tagline">
                    Platform manajemen produk darah PMI — dari stok masuk, distribusi ke rumah sakit, hingga pemusnahan darah kedaluwarsa.
                </p> -->

                <!-- <div class="info-list">
                    <div class="info-item">
                        <i class="fas fa-tint info-icon"></i>
                        <span class="info-text">Pantau stok darah secara real-time berdasarkan golongan, jenis, dan tanggal kedaluwarsa.</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-truck info-icon"></i>
                        <span class="info-text">Catat distribusi ke rumah sakit mitra dan lacak riwayat lengkap setiap kantong darah.</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-file-alt info-icon"></i>
                        <span class="info-text">Laporan pemusnahan otomatis untuk darah kedaluwarsa atau rusak, lengkap dengan audit trail.</span>
                    </div>
                </div> -->
            </div>

            <!-- Right: Login form -->
            <div class="login-panel-right">
                <div class="login-box">
                    <div class="login-card-inner">

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <h2 class="login-title">Selamat Datang</h2>
                        <p class="login-subtitle">Silakan masuk menggunakan email dan kata sandi yang terdaftar.</p>

                        <form action="<?= base_url('/login/process') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="form-group mb-3">
                                <label class="input-label">Alamat Email</label>
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control"
                                        placeholder="contoh@pmi.or.id" required
                                        value="<?= old('email') ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="input-label">Kata Sandi</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Masukkan kata sandi" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">Tetap masuk selama 30 hari</label>
                            </div>

                            <button type="submit" class="btn btn-login btn-block">MASUK</button>
                        </form>

                        <p class="footer-note">
                            Lupa akses? Hubungi administrator sistem PMI di unit Anda.
                        </p>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
</body>

</html>
