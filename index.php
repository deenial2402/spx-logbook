<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPXID-LOGBOOK PDA-PALANGKA RAYA DC</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        :root {
            --spx-orange: #EE4D2D;
            --spx-orange-dark: #d63b1d;
            --spx-light-bg: #F5F5F5;
            --spx-dark: #333333;
            --spx-gray: #888888;
            --spx-border: #e0e0e0;
            --spx-danger: #dc3545;
            --spx-warning: #ffc107;
            --spx-success: #28a745;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Roboto', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background-color: var(--spx-light-bg); color: var(--spx-dark); display: flex; justify-content: center; }
        .app-container { width: 100%; max-width: 480px; min-height: 100vh; background-color: #ffffff; display: flex; flex-direction: column; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); position: relative; }
        .header { background-color: var(--spx-orange); color: #ffffff; padding: 16px; text-align: center; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header h1 { font-size: 1.1rem; font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .header p { font-size: 0.75rem; opacity: 0.9; margin-top: 2px; }
        .bottom-nav { display: flex; position: sticky; bottom: 0; background-color: #ffffff; border-top: 1px solid var(--spx-border); z-index: 100; }
        .nav-item { flex: 1; padding: 10px 0; text-align: center; color: var(--spx-gray); font-size: 0.7rem; cursor: pointer; text-decoration: none; transition: 0.2s; }
        .nav-item i { font-size: 1.1rem; display: block; margin-bottom: 3px; }
        .nav-item.active { color: var(--spx-orange); font-weight: 700; border-top: 3px solid var(--spx-orange); }
        .app-footer { text-align: center; padding: 10px; background-color: #fafafa; border-top: 1px solid var(--spx-border); font-size: 0.75rem; color: var(--spx-gray); }
        .content { flex: 1; padding: 16px; overflow-y: auto; }
        .page { display: none; }
        .page.active { display: block; }
        .card { background: #fff; border-radius: 8px; padding: 16px; border: 1px solid var(--spx-border); margin-bottom: 16px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: var(--spx-dark); }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--spx-border); border-radius: 6px; font-size: 0.9rem; outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: var(--spx-orange); }
        .password-wrapper { position: relative; display: flex; align-items: center; }
        .password-wrapper input { padding-right: 40px; }
        .password-toggle-icon { position: absolute; right: 12px; color: var(--spx-gray); cursor: pointer; font-size: 1rem; user-select: none; }
        .camera-box { width: 100%; border: 2px dashed var(--spx-border); border-radius: 8px; padding: 12px; text-align: center; background-color: #fafafa; position: relative; }
        .camera-box video, .camera-box canvas { width: 100%; border-radius: 6px; display: none; }
        .preview-img { width: 100%; border-radius: 6px; display: none; margin-top: 8px; }
        .btn { width: 100%; background-color: var(--spx-orange); color: #ffffff; border: none; padding: 12px; border-radius: 6px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn:hover { background-color: var(--spx-orange-dark); }
        .btn-secondary { background-color: #6c757d; margin-top: 8px; }
        .btn-outline { background: transparent; color: var(--spx-orange); border: 1px solid var(--spx-orange); margin-top: 8px; }
        .btn-danger { background-color: var(--spx-danger); color: white; }
        .btn-export { background-color: #198754; color: white; font-size: 0.8rem; padding: 8px 12px; margin-top: 8px; }
        .btn-export-pdf { background-color: #dc3545; color: white; font-size: 0.8rem; padding: 8px 12px; margin-top: 8px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-borrow { background-color: #fff3cd; color: #856404; }
        .badge-return { background-color: #d4edda; color: #155724; }
        .badge-overdue { background-color: #f8d7da; color: #721c24; }
        .summary-cards { display: flex; gap: 10px; margin-bottom: 16px; }
        .summary-card { flex: 1; padding: 12px; border-radius: 8px; background: #fff; border: 1px solid var(--spx-border); text-align: center; }
        .summary-card.borrowed { border-left: 4px solid var(--spx-orange); }
        .summary-card.unreturned { border-left: 4px solid var(--spx-danger); }
        .summary-card h4 { font-size: 0.75rem; color: var(--spx-gray); margin-bottom: 4px; }
        .summary-card .number { font-size: 1.4rem; font-weight: bold; }
        .filter-box { background: #fff; border: 1px solid var(--spx-border); border-radius: 8px; padding: 12px; margin-bottom: 16px; }
        .filter-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px; }
        .monitoring-item { border-left: 4px solid var(--spx-orange); padding: 12px; margin-bottom: 12px; background: #fff; border-radius: 0 8px 8px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.08); cursor: pointer; }
        .user-bar { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background-color: #fff0ed; border-radius: 6px; margin-bottom: 16px; border: 1px solid #ffd3cc; }
        .user-bar span { font-size: 0.85rem; font-weight: bold; color: var(--spx-orange-dark); }
        .logout-btn { background: none; border: none; color: var(--spx-orange); font-weight: bold; font-size: 0.8rem; cursor: pointer; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center; padding: 16px; }
        .modal-content { background: #fff; width: 100%; max-width: 400px; border-radius: 8px; padding: 16px; max-height: 90vh; overflow-y: auto; position: relative; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--spx-border); padding-bottom: 8px; margin-bottom: 12px; }
        .close-modal { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--spx-gray); }
        .modal-body img { width: 100%; max-height: 200px; object-fit: cover; border-radius: 6px; margin-top: 6px; border: 1px solid var(--spx-border); }
        .modal-detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #eee; font-size: 0.85rem; }
        .asset-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; margin-top: 8px; }
        .asset-table th, .asset-table td { border: 1px solid var(--spx-border); padding: 8px; text-align: left; }
        .asset-table th { background-color: #f8f9fa; }
        .action-btns { display: flex; gap: 4px; }
        .btn-sm { padding: 4px 8px; font-size: 0.75rem; border-radius: 4px; border: none; cursor: pointer; color: white; }
        .btn-edit { background-color: var(--spx-warning); color: #000; }
        .btn-delete { background-color: var(--spx-danger); }
        .tutorial-step { display: flex; gap: 12px; margin-bottom: 14px; }
        .step-number { background: var(--spx-orange); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem; flex-shrink: 0; }
        .step-content h4 { font-size: 0.85rem; margin-bottom: 2px; }
        .step-content p { font-size: 0.78rem; color: var(--spx-gray); }
    </style>
</head>
<body>

    <div class="app-container">
        <header class="header">
            <h1>SHOPEE EXPRESS</h1>
            <p>LOGBOOK ASSET PDA PALANGKA RAYA DC</p>
        </header>

        <main class="content">

            <!-- PAGE 1: FORM -->
            <section id="page-form" class="page active">
                <div class="card">
                    <h2 style="font-size: 1rem; margin-bottom: 14px; color: var(--spx-orange);">
                        <i class="fa-solid fa-file-pen"></i> Form Peminjaman/Pengembalian PDA
                    </h2>
                    <form id="borrowForm">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" class="form-control" placeholder="Masukkan nama lengkap Anda" required>
                        </div>
                        <div class="form-group">
                            <label for="opsId">Ops ID</label>
                            <input type="text" id="opsId" class="form-control" placeholder="Contoh: Ops99999" required>
                        </div>
                        <div class="form-group">
                            <label for="snAsset">Serial Number Asset - 5 Digit Terakhir</label>
                            <input type="text" id="snAsset" class="form-control" placeholder="Contoh: 99999" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        <div class="form-group">
                            <label for="statusPeminjaman">Status Transaksi</label>
                            <select id="statusPeminjaman" class="form-control" required>
                                <option value="Pinjam">Pinjam Asset</option>
                                <option value="Kembali">Kembalikan Asset</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Foto Selfie dengan Asset (+Timestamp)</label>
                            <div class="camera-box">
                                <video id="webcam" autoplay playsinline></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <img id="preview" class="preview-img" alt="Preview Selfie">
                                <button type="button" id="btnStartCamera" class="btn btn-outline" onclick="requestCameraPermission()">
                                    <i class="fa-solid fa-camera"></i> Buka Kamera
                                </button>
                                <button type="button" id="btnCapture" class="btn btn-secondary" onclick="takeSnapshot()" style="display:none;">
                                    <i class="fa-solid fa-circle-dot"></i> Ambil Foto
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn" style="margin-top: 16px;">
                            <i class="fa-solid fa-paper-plane"></i> Submit
                        </button>
                    </form>
                </div>
            </section>

            <!-- PAGE TUTORIAL -->
            <section id="page-tutorial" class="page">
                <div class="card">
                    <h2 style="font-size: 1rem; margin-bottom: 12px; color: var(--spx-orange);">
                        <i class="fa-solid fa-circle-info"></i> Persyaratan Peminjaman
                    </h2>
                    <ul style="font-size: 0.8rem; line-height: 1.5; margin-left: 18px; color: var(--spx-dark);">
                        <li>Peminjam wajib merupakan Daily Worker/Dedicated terdaftar di <b>Palangka Raya DC</b>.</li>
                        <li>Asset PDA hanya boleh digunakan untuk keperluan operasional kerja.</li>
                        <li>Peminjam bertanggung jawab penuh atas keutuhan dan fisik PDA selama masa peminjaman.</li>
                        <li>Wajib menyertakan foto selfie memegang PDA yang dipinjam saat submit form.</li>
                        <li>Asset <b>WAJIB dikembalikan</b> di akhir shift operasional.</li>
                        <li><b>Peminjam bersedia bertanggung jawab terhadap Asset tersebut jika ada kerugian yang ditimbulkan saat masa peminjaman.</b></li>
                    </ul>
                </div>
            </section>

            <!-- PAGE LOGIN -->
            <section id="page-login" class="page">
                <div class="card" style="text-align: center; margin-top: 20px;">
                    <i class="fa-solid fa-lock" style="font-size: 2.5rem; color: var(--spx-orange); margin-bottom: 10px;"></i>
                    <h2 style="font-size: 1.1rem; margin-bottom: 6px;">Login Administrator</h2>
                    <form id="loginForm">
                        <div class="form-group" style="text-align: left;">
                            <label for="username">Username</label>
                            <input type="text" id="username" class="form-control" required>
                        </div>
                        <div class="form-group" style="text-align: left;">
                            <label for="password">Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" class="form-control" required>
                                <i class="fa-solid fa-eye password-toggle-icon" id="togglePasswordIcon" onclick="togglePasswordVisibility()"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn">Masuk Sistem</button>
                    </form>
                </div>
            </section>

            <!-- PAGE MONITORING -->
            <section id="page-monitoring" class="page">
                <div class="user-bar">
                    <span><i class="fa-solid fa-user-shield"></i> <span id="userBadgeRole">Admin</span> Online</span>
                    <button class="logout-btn" onclick="logout()"><i class="fa-solid fa-power-off"></i> Logout</button>
                </div>
                <div class="summary-cards">
                    <div class="summary-card borrowed">
                        <h4>Total Dipinjam</h4>
                        <div class="number" id="summaryBorrowed" style="color: var(--spx-orange);">0</div>
                    </div>
                    <div class="summary-card unreturned">
                        <h4>Belum Kembali</h4>
                        <div class="number" id="summaryUnreturned" style="color: var(--spx-danger);">0</div>
                    </div>
                </div>
                <div class="filter-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari Nama, Ops ID, atau SN..." oninput="renderMonitoringList()">
                    <div class="filter-grid" style="margin-top:8px;">
                        <div>
                            <input type="date" id="startDate" class="form-control" onchange="renderMonitoringList()">
                        </div>
                        <div>
                            <input type="date" id="endDate" class="form-control" onchange="renderMonitoringList()">
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 8px;">
                        <button type="button" class="btn btn-export" onclick="exportToExcel()">Export Excel</button>
                        <button type="button" class="btn btn-export-pdf" onclick="exportToPDF()">Export PDF</button>
                    </div>
                </div>
                <div id="monitoringList"></div>
            </section>

            <!-- PAGE DATABASE -->
            <section id="page-database" class="page">
                <div class="user-bar">
                    <span><i class="fa-solid fa-database"></i> Database Asset</span>
                    <button class="logout-btn" onclick="logout()"><i class="fa-solid fa-power-off"></i> Logout</button>
                </div>
                <div class="card" id="superAdminAssetFormCard" style="display:none;">
                    <h3>Tambah Master Asset</h3>
                    <form id="assetMasterForm">
                        <div class="form-group"><label>ID Asset</label><input type="text" id="masterAssetId" class="form-control" required></div>
                        <div class="form-group"><label>Serial Number (SN)</label><input type="text" id="masterSn" class="form-control" required></div>
                        <div class="form-group"><label>Nama Asset</label><input type="text" id="masterName" class="form-control" required></div>
                        <button type="submit" class="btn">Simpan Asset</button>
                    </form>
                </div>
                <div class="card">
                    <table class="asset-table">
                        <thead>
                            <tr>
                                <th>ID Asset</th>
                                <th>SN Asset</th>
                                <th>Nama Asset</th>
                                <th>Status</th>
                                <th id="thAksi" style="display:none;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="assetMasterTableBody"></tbody>
                    </table>
                </div>
            </section>

        </main>

        <nav class="bottom-nav">
            <div class="nav-item active" onclick="switchTab('form')"><i class="fa-solid fa-pen-to-square"></i> Form</div>
            <div class="nav-item" onclick="switchTab('tutorial')"><i class="fa-solid fa-book-open"></i> Panduan</div>
            <div class="nav-item" id="nav-login" onclick="switchTab('login')"><i class="fa-solid fa-right-to-bracket"></i> Login</div>
            <div class="nav-item" id="nav-monitoring" style="display:none;" onclick="switchTab('monitoring')"><i class="fa-solid fa-list-check"></i> Monitoring</div>
            <div class="nav-item" id="nav-database" style="display:none;" onclick="switchTab('database')"><i class="fa-solid fa-database"></i> Database</div>
        </nav>

        <footer class="app-footer">
            Developed by <strong>SPXID23710 - Palangka Raya DC</strong> &copy; 2026
        </footer>
    </div>

    <!-- MODAL CAMERA PERMISSION -->
    <div id="cameraPermissionModal" class="modal-overlay">
        <div class="modal-content" style="text-align: center;">
            <i class="fa-solid fa-camera-retro" style="font-size: 3rem; color: var(--spx-orange); margin-bottom: 12px;"></i>
            <h3>Akses Kamera Diperlukan</h3>
            <p style="margin-bottom:20px;">Izinkan akses kamera untuk verifikasi foto selfie transaksi.</p>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-secondary" onclick="closeCameraModal()">Batal</button>
                <button class="btn" onclick="confirmStartCamera()">Izinkan</button>
            </div>
        </div>
    </div>

    <!-- MODAL NOTIFIKASI UNIVERSAL -->
    <div id="notificationModal" class="modal-overlay">
        <div class="modal-content" style="text-align: center;">
            <div id="notifIcon"></div>
            <h3 id="notifTitle"></h3>
            <p id="notifMessage" style="margin-bottom:16px;"></p>
            <button class="btn btn-secondary" onclick="closeNotifModal()">Tutup</button>
        </div>
    </div>

    <!-- MODAL DETAIL TRANSACTION -->
    <div id="detailModal" class="modal-overlay" onclick="closeModalOutside(event)">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detail Transaksi Asset</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalDetailContent"></div>
        </div>
    </div>

    <script>
        let currentUserRole = localStorage.getItem('spx_user_role') || null; 
        let capturedImageData = "";
        let stream = null;
        let assetMaster = [];
        let assetLogs = [];

        document.addEventListener('DOMContentLoaded', () => {
            fetchAssets();
            fetchLogs();
            updateUIBasedOnAuth();
        });

        function fetchAssets() {
            fetch('api.php?action=get_assets')
                .then(res => res.json())
                .then(res => {
                    if (res.status) {
                        assetMaster = res.data;
                        renderDatabaseAsset();
                    }
                });
        }

        function fetchLogs() {
            fetch('api.php?action=get_logs')
                .then(res => res.json())
                .then(res => {
                    if (res.status) {
                        assetLogs = res.data;
                        renderMonitoringList();
                        renderDatabaseAsset();
                    }
                });
        }

        function updateUIBasedOnAuth() {
            const navLogin = document.getElementById('nav-login');
            const navMonitoring = document.getElementById('nav-monitoring');
            const navDatabase = document.getElementById('nav-database');
            const superAdminForm = document.getElementById('superAdminAssetFormCard');
            const thAksi = document.getElementById('thAksi');

            if (currentUserRole) {
                navLogin.style.display = 'none';
                navMonitoring.style.display = 'block';
                navDatabase.style.display = 'block';
                document.getElementById('userBadgeRole').innerText = currentUserRole;

                if (currentUserRole === 'Super Admin') {
                    superAdminForm.style.display = 'block';
                    thAksi.style.display = 'table-cell';
                } else {
                    superAdminForm.style.display = 'none';
                    thAksi.style.display = 'none';
                }
            } else {
                navLogin.style.display = 'block';
                navMonitoring.style.display = 'none';
                navDatabase.style.display = 'none';
            }
        }

        function switchTab(tab) {
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));

            if (tab === 'form') {
                document.querySelectorAll('.nav-item')[0].classList.add('active');
                document.getElementById('page-form').classList.add('active');
            } else if (tab === 'tutorial') {
                document.querySelectorAll('.nav-item')[1].classList.add('active');
                document.getElementById('page-tutorial').classList.add('active');
            } else if (tab === 'login') {
                document.getElementById('nav-login').classList.add('active');
                document.getElementById('page-login').classList.add('active');
            } else if (tab === 'monitoring') {
                if (!currentUserRole) return switchTab('login');
                document.getElementById('nav-monitoring').classList.add('active');
                document.getElementById('page-monitoring').classList.add('active');
            } else if (tab === 'database') {
                if (!currentUserRole) return switchTab('login');
                document.getElementById('nav-database').classList.add('active');
                document.getElementById('page-database').classList.add('active');
            }
            stopCamera();
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = "fa-solid fa-eye-slash password-toggle-icon";
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = "fa-solid fa-eye password-toggle-icon";
            }
        }

        function requestCameraPermission() {
            document.getElementById('cameraPermissionModal').style.display = 'flex';
        }

        function closeCameraModal() {
            document.getElementById('cameraPermissionModal').style.display = 'none';
        }

        async function confirmStartCamera() {
            closeCameraModal();
            const video = document.getElementById('webcam');
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = stream;
                video.style.display = 'block';
                document.getElementById('btnStartCamera').style.display = 'none';
                document.getElementById('btnCapture').style.display = 'block';
            } catch (err) {
                showNotifModal(false, 'Gagal Buka Kamera', 'Akses kamera ditolak atau tidak tersedia!');
            }
        }

        function takeSnapshot() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('preview');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 240;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const timeString = `${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID')} WIB`;
            context.fillStyle = "rgba(0, 0, 0, 0.6)";
            context.fillRect(0, canvas.height - 30, canvas.width, 30);
            context.fillStyle = "#FFD700";
            context.font = "bold 12px Roboto, sans-serif";
            context.fillText(`SPX-PALANGKARAYA DC: ${timeString}`, 10, canvas.height - 10);

            capturedImageData = canvas.toDataURL('image/jpeg');
            preview.src = capturedImageData;
            preview.style.display = 'block';
            stopCamera();
            document.getElementById('btnStartCamera').innerText = "Ambil Ulang Foto";
            document.getElementById('btnStartCamera').style.display = 'block';
            document.getElementById('btnCapture').style.display = 'none';
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                document.getElementById('webcam').style.display = 'none';
            }
        }

        function showNotifModal(isSuccess, title, message) {
            document.getElementById('notifIcon').innerHTML = isSuccess 
                ? `<i class="fa-solid fa-circle-check" style="font-size: 3rem; color: var(--spx-success); margin-bottom: 12px;"></i>` 
                : `<i class="fa-solid fa-circle-xmark" style="font-size: 3rem; color: var(--spx-danger); margin-bottom: 12px;"></i>`;
            document.getElementById('notifTitle').innerText = title;
            document.getElementById('notifMessage').innerHTML = message;
            document.getElementById('notificationModal').style.display = 'flex';
        }

        function closeNotifModal() {
            document.getElementById('notificationModal').style.display = 'none';
        }

        // Submit Form Peminjaman & Pengembalian
        document.getElementById('borrowForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const payload = {
                nama: document.getElementById('nama').value.trim(),
                opsId: document.getElementById('opsId').value.trim().toUpperCase(),
                snAsset: document.getElementById('snAsset').value.trim(),
                status: document.getElementById('statusPeminjaman').value,
                photo: capturedImageData
            };

            if(!capturedImageData) {
                showNotifModal(false, 'Gagal Transaksi', 'Ambil foto selfie terlebih dahulu!');
                return;
            }

            fetch('api.php?action=submit_transaction', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    showNotifModal(true, 'Sukses Transaksi', res.message);
                    this.reset();
                    capturedImageData = "";
                    document.getElementById('preview').style.display = 'none';
                    fetchLogs();
                } else {
                    showNotifModal(false, 'Gagal Transaksi', res.message);
                }
            });
        });

        // Submit Login Handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const user = document.getElementById('username').value.trim();
            const pass = document.getElementById('password').value.trim();

            fetch('api.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: user, password: pass })
            })
            .then(res => res.json())
            .then(res => {
                if(res.status) {
                    currentUserRole = res.role;
                    localStorage.setItem('spx_user_role', currentUserRole);
                    updateUIBasedOnAuth();
                    switchTab('monitoring');
                } else {
                    showNotifModal(false, 'Login Gagal', res.message);
                }
            });
        });

        function logout() {
            fetch('api.php?action=logout').then(() => {
                currentUserRole = null;
                localStorage.removeItem('spx_user_role');
                updateUIBasedOnAuth();
                switchTab('form');
            });
        }

        function renderMonitoringList() {
            const container = document.getElementById('monitoringList');
            container.innerHTML = "";

            document.getElementById('summaryBorrowed').innerText = assetLogs.length;
            document.getElementById('summaryUnreturned').innerText = assetLogs.filter(l => l.status === 'Pinjam').length;

            assetLogs.forEach(log => {
                const badgeHtml = log.status === 'Pinjam' 
                    ? `<span class="badge badge-borrow">DIPINJAM</span>` 
                    : `<span class="badge badge-return">DIKEMBALIKAN</span>`;

                let deleteBtnHtml = currentUserRole === 'Super Admin' ? `
                    <button class="btn-sm btn-delete" onclick="deleteLog(event, ${log.id})"><i class="fa-solid fa-trash"></i></button>
                ` : '';

                container.innerHTML += `
                    <div class="monitoring-item" onclick="openModal(${log.id})">
                        <div style="display:flex; justify-content:space-between;">
                            <strong>${log.nama} (${log.opsId})</strong>
                            <div>${badgeHtml} ${deleteBtnHtml}</div>
                        </div>
                        <div style="font-size:0.8rem; color:var(--spx-gray); margin-top:4px;">
                            SN Asset: <b>${log.snAsset}</b> | Waktu: ${log.isoDate}
                        </div>
                    </div>
                `;
            });
        }

        function deleteLog(e, id) {
            e.stopPropagation();
            if (currentUserRole !== 'Super Admin') return;
            if(confirm("Apakah Anda yakin ingin menghapus catatan transaksi ini?")) {
                fetch(`api.php?action=delete_log&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    fetchLogs();
                });
            }
        }

        function renderDatabaseAsset() {
            const tbody = document.getElementById('assetMasterTableBody');
            tbody.innerHTML = "";

            assetMaster.forEach(asset => {
                const activeBorrower = assetLogs.find(log => log.snAsset.toUpperCase() === asset.sn.toUpperCase() && log.status === 'Pinjam');

                const statusBadge = activeBorrower 
                    ? `<span class="badge badge-overdue">Dipinjam (${activeBorrower.opsId})</span>` 
                    : `<span class="badge badge-return">Tersedia</span>`;

                let actionTd = currentUserRole === 'Super Admin' ? `
                    <td>
                        <button class="btn-sm btn-delete" onclick="deleteAsset('${asset.assetId}')"><i class="fa-solid fa-trash"></i> Hapus</button>
                    </td>
                ` : '';

                tbody.innerHTML += `
                    <tr>
                        <td><b>${asset.assetId}</b></td>
                        <td><b>${asset.sn}</b></td>
                        <td>${asset.name}</td>
                        <td>${statusBadge}</td>
                        ${actionTd}
                    </tr>
                `;
            });
        }

        document.getElementById('assetMasterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const payload = {
                assetId: document.getElementById('masterAssetId').value,
                sn: document.getElementById('masterSn').value,
                name: document.getElementById('masterName').value
            };

            fetch('api.php?action=add_asset', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    showNotifModal(true, 'Berhasil', res.message);
                    this.reset();
                    fetchAssets();
                } else {
                    showNotifModal(false, 'Gagal', res.message);
                }
            });
        });

        function deleteAsset(id) {
            if (confirm("Hapus asset ini?")) {
                fetch(`api.php?action=delete_asset&id=${id}`)
                .then(res => res.json())
                .then(() => fetchAssets());
            }
        }

        function openModal(id) {
            const log = assetLogs.find(item => item.id === id);
            if (!log) return;

            let modalContent = `
                <div class="modal-detail-row"><span>Nama Staff</span><strong>${log.nama}</strong></div>
                <div class="modal-detail-row"><span>Ops ID</span><strong>${log.opsId}</strong></div>
                <div class="modal-detail-row"><span>SN Asset</span><strong>${log.snAsset}</strong></div>
                <div class="modal-detail-row"><span>Status</span><strong>${log.status}</strong></div>
                <div class="modal-detail-row"><span>Waktu</span><strong>${log.isoDate}</strong></div>
                <div style="margin-top: 12px;">
                    <label style="font-size:0.8rem; font-weight:bold;">Foto Bukti Peminjaman:</label>
                    <img src="${log.photo ? log.photo : ''}">
                </div>
            `;
            document.getElementById('modalDetailContent').innerHTML = modalContent;
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeModal() { document.getElementById('detailModal').style.display = 'none'; }
        function closeModalOutside(e) { if (e.target.id === 'detailModal') closeModal(); }
    </script>
</body>
</html>