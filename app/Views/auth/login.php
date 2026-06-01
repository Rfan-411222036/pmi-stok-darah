<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIMONDAR PMI</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <style>
        body {
            background: radial-gradient(circle at top, rgba(220, 53, 69, 0.08), transparent 30%),
                linear-gradient(180deg, #f6f7f9 0%, #e6e8ec 100%);
            min-height: 100vh;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        .login-card {
            width: min(100%, 1180px);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 30px 90px rgba(15, 23, 42, 0.12);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            background: #ffffff;
        }

        .login-panel-left {
            position: relative;
            padding: 64px 60px;
            background: linear-gradient(145deg, #fff7f7 0%, #fff 40%, #fde5e8 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 26px;
        }

        .login-panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at top left, rgba(220, 53, 69, 0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(220, 53, 69, 0.08), transparent 20%);
            pointer-events: none;
        }

        .login-panel-left > * {
            position: relative;
            z-index: 1;
        }

        .brand-box {
            width: 96px;
            height: 96px;
            border-radius: 28px;
            border: 2px solid #dc3545;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 45px rgba(220, 53, 69, 0.05);
        }

        .brand-box img {
            width: 48px;
            height: auto;
        }

        .login-panel-left h1 {
            font-size: clamp(2.4rem, 2.7vw, 3rem);
            line-height: 1.02;
            margin: 0;
            color: #111111;
            letter-spacing: -0.04em;
        }

        .login-panel-left p {
            color: #5b5b5b;
            font-size: 1rem;
            line-height: 1.85;
            max-width: 440px;
        }

        .info-list {
            display: grid;
            gap: 16px;
            margin-top: 12px;
            max-width: 520px;
        }

        .info-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .info-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dc3545;
            margin-top: 8px;
            flex-shrink: 0;
        }

        .info-text {
            color: #5f5f5f;
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .login-panel-right {
            padding: 48px 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }

        .login-box {
            width: 100%;
            max-width: 440px;
        }

        .login-card-inner {
            border: 1px solid rgba(220, 53, 69, 0.16);
            border-radius: 28px;
            padding: 36px 34px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
            background: #ffffff;
        }

        .login-title {
            margin-bottom: 6px;
            font-weight: 700;
            font-size: 2rem;
            color: #111111;
        }

        .login-subtitle {
            color: #6c757d;
            margin-bottom: 26px;
            line-height: 1.7;
        }

        .form-control {
            border-radius: 16px;
            border: 1px solid #dae2ea;
            background: #fbfbfb;
            height: 52px;
        }

        .input-group-text {
            border-radius: 0 16px 16px 0;
            border-left: none;
            background: #ffffff;
            color: #dc3545;
            min-width: 54px;
            justify-content: center;
        }

        .input-group .form-control {
            border-right: none;
        }

        .input-group .form-control:focus {
            box-shadow: none;
            border-color: #dc3545;
        }

        .form-group {
            position: relative;
        }

        .form-check-label {
            color: #495057;
        }

        .btn-login {
            border-radius: 16px;
            padding: 14px 0;
            background: #111111;
            border: none;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .btn-login:hover {
            background: #000000;
        }

        .help-text {
            color: #6c757d;
            margin-top: 24px;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .alert {
            border-radius: 18px;
        }

        @media (max-width: 991px) {
            .login-card {
                grid-template-columns: 1fr;
            }

            .login-panel-left,
            .login-panel-right {
                padding: 28px 24px;
            }

            .login-panel-left h1 {
                font-size: 2.2rem;
            }

            .login-panel-right {
                padding-top: 0;
            }
        }

        @media (max-width: 575px) {
            .login-card {
                border-radius: 24px;
            }

            .login-panel-left {
                padding: 32px 24px;
            }

            .login-panel-right {
                padding: 32px 24px;
            }

            .login-card-inner {
                padding: 28px 22px;
            }
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-panel-left">
                <div class="brand-box">
                    <img src="<?= base_url('/logo.png') ?>" alt="PMI Logo">
                </div>
                <h1>SIMONDAR by PMI</h1>
                <p>Mendukung pelayanan kemanusiaan yang profesional dan terpercaya untuk seluruh jaringan PMI di Indonesia.</p>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-dot"></span>
                        <span class="info-text">Antarmuka bersih, fokus pada akses cepat dan kemudahan laporan stok darah.</span>
                    </div>
                    <div class="info-item">
                        <span class="info-dot"></span>
                        <span class="info-text">Desain responsif dan ringan sehingga dapat digunakan pada tablet maupun desktop.</span>
                    </div>
                </div>
            </div>
            <div class="login-panel-right">
                <div class="login-box">
                    <div class="login-card-inner">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        <div class="login-header">
                            <h2 class="login-title">Masuk ke Sistem</h2>
                            <p class="login-subtitle">Akses sistem ini diperuntukkan bagi petugas dan mitra resmi Palang Merah Indonesia.</p>
                        </div>
                        <form action="<?= base_url('/login/process') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="input-group mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Email" required value="<?= old('email') ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-envelope"></span></span>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-lock"></span></span>
                                </div>
                            </div>
                            <div class="form-group form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">Ingat saya 30 hari</label>
                            </div>
                            <button type="submit" class="btn btn-login btn-block">Masuk ke Sistem</button>
                        </form>
                        <p class="help-text">Akses sistem ini diperuntukkan bagi petugas dan mitra resmi Palang Merah Indonesia.</p>
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
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
</body>

</html>
