<?php
// Mulai session di paling atas
session_start();

// 1. PENGECEKAN SESSION LOGIN
// Jika user sudah login, langsung arahkan ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: views/admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] == 'kepala') {
        header("Location: views/kepala/dashboard.php");
        exit();
    }
}

// 2. KONEKSI & PENGATURAN
require 'config/koneksi.php';

// 3. TAMPILAN HALAMAN LOGIN
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Masuk - <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></title>
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#af101a",
                        "primary-variant": "#930010",
                        "surface": "#fcfcfc",
                        "surface-container": "#f3f4f5",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#5b403d",
                        "outline": "#8f6f6c",
                        "outline-variant": "#e4beba",
                    },
                    fontFamily: {
                        "headline": ["Work Sans", "sans-serif"],
                        "body": ["Work Sans", "sans-serif"],
                    },
                },
            },
        }
    </script>
    
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
        }
        .pattern-bg {
            background-color: #ffffff;
            background-image: radial-gradient(#af101a 0.5px, transparent 0.5px), radial-gradient(#af101a 0.5px, #ffffff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.03;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }
    </style>
</head>

<body class="bg-surface text-on-surface min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
    
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute inset-0 pattern-bg"></div>
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-primary/5 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-primary/5 blur-[100px]"></div>
    </div>
    
    <main class="relative z-10 w-full max-w-[1000px] flex flex-col md:flex-row bg-white rounded-3xl overflow-hidden shadow-[0_32px_64px_-16px_rgba(0,0,0,0.08)] ring-1 ring-black/[0.03]">
        
        <div class="hidden md:flex flex-1 bg-primary items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg height="100%" width="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern height="40" id="grid" patternunits="userSpaceOnUse" width="40">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"></path>
                        </pattern>
                    </defs>
                    <rect fill="url(#grid)" height="100%" width="100%"></rect>
                </svg>
            </div>
            
            <div class="relative z-10 text-center flex flex-col items-center">
                <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-2xl overflow-hidden p-2">
                    <?php if(!empty($_SETTINGS['logo'])): ?>
                        <img alt="<?= htmlspecialchars($_SETTINGS['nama_sistem']) ?> Logo" class="w-full h-full object-contain" src="assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['logo']) ?>"/>
                    <?php else: ?>
                        <span class="material-symbols-outlined text-primary text-5xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
                    <?php endif; ?>
                </div>
                <h2 class="text-white text-3xl font-bold tracking-tight mb-4"><?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></h2>
                <p class="text-white/80 text-lg font-light max-w-[280px] leading-relaxed">
                    Sistem Manajemen Stok dan Keluar Masuk Barang.
                </p>
            </div>
        </div>
        
        <div class="flex-1 p-8 sm:p-12 lg:p-16 flex flex-col">
            
            <div class="md:hidden mb-8 flex items-center gap-3">
                <?php if(!empty($_SETTINGS['logo'])): ?>
                    <img alt="Logo" class="w-10 h-10 object-contain" src="assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['logo']) ?>"/>
                <?php else: ?>
                    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
                    </div>
                <?php endif; ?>
                <span class="font-bold text-primary tracking-tight"><?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></span>
            </div>
            
            <header class="mb-10">
                <h1 class="text-3xl font-bold text-on-surface mb-2 tracking-tight">Selamat Datang Kembali</h1>
                <p class="text-on-surface-variant font-medium">Silakan masuk ke akun Anda</p>
            </header>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm font-medium flex items-center gap-2" role="alert">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span><?= $_SESSION['error']; ?></span>
                </div>
                <?php unset($_SESSION['error']); // Hapus error setelah ditampilkan ?>
            <?php endif; ?>

            <form action="proses/auth_login.php" method="POST" class="flex flex-col gap-6">
                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-on-surface-variant flex items-center gap-2" for="username">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                            Nama Pengguna
                        </label>
                        <input class="w-full h-12 px-4 bg-surface-container border border-outline-variant/30 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all duration-200 outline-none placeholder:text-outline/50" id="username" name="username" placeholder="Username" type="text" required autocomplete="off"/>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-on-surface-variant flex items-center gap-2" for="password">
                            <span class="material-symbols-outlined text-[18px]">lock</span>
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input class="w-full h-12 px-4 pr-12 bg-surface-container border border-outline-variant/30 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all duration-200 outline-none placeholder:text-outline/50" id="password" name="password" placeholder="••••••••" type="password" required/>
                            <button id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center text-outline hover:text-primary transition-colors p-1" type="button">
                                <span class="material-symbols-outlined" id="eyeIcon">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary transition-all cursor-pointer" type="checkbox"/>
                        <span class="text-sm font-medium text-on-surface-variant group-hover:text-on-surface transition-colors">Ingat saya</span>
                    </label>
                    <a class="text-sm font-semibold text-primary hover:text-primary-variant transition-colors" href="#">Lupa Password?</a>
                </div>
                
                <button class="w-full h-12 mt-4 bg-primary hover:bg-primary-variant text-white font-bold rounded-xl shadow-lg shadow-primary/20 transition-all duration-300 active:scale-[0.98]" type="submit">
                    Masuk Ke Sistem
                </button>
            </form>
            
            <footer class="mt-auto pt-10">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-[2px] bg-outline-variant/40 mb-4"></div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-outline opacity-60 text-center leading-relaxed">
                        © <?php echo date("Y"); ?> <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?><br/>
                    </p>
                </div>
            </footer>
            
        </div>
    </main>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Cek tipe saat ini, lalu ubah ke teks atau password
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Ubah ikon antara mata terbuka dan tertutup
            eyeIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    </script>
</body>
</html>