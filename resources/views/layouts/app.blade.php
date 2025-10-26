<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Cyberpunk Inventory'))</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --neon-cyan: #00ffff;
            --neon-pink: #ff0080;
            --neon-purple: #8a2be2;
            --dark-bg: #0a0a0a;
            --dark-surface: #1a1a1a;
            --dark-card: #2a2a2a;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-muted: #888888;
            --border-glow: #333333;
            --success: #00ff88;
            --warning: #ffaa00;
            --danger: #ff4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Orbitron', monospace;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 0, 128, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(138, 43, 226, 0.1) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite alternate;
            z-index: -1;
        }

        @keyframes pulse {
            0% { opacity: 0.3; }
            100% { opacity: 0.7; }
        }

        .navbar {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--neon-cyan);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--neon-cyan);
            text-shadow: 0 0 10px var(--neon-cyan);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            border-radius: 4px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--neon-cyan);
            border-color: var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .user-role {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .role-admin {
            background: var(--neon-purple);
            color: var(--text-primary);
        }

        .role-manager {
            background: var(--neon-cyan);
            color: var(--dark-bg);
        }

        .role-staff {
            background: var(--text-muted);
            color: var(--text-primary);
        }

        .logout-btn {
            background: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: inherit;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: var(--text-primary);
            box-shadow: 0 0 15px rgba(255, 68, 68, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--neon-cyan), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            animation: titleGlow 2s ease-in-out infinite alternate;
        }

        @keyframes titleGlow {
            0% { filter: brightness(1); }
            100% { filter: brightness(1.2) drop-shadow(0 0 20px rgba(0, 255, 255, 0.5)); }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--dark-card);
            border: 1px solid var(--border-glow);
            border-radius: 8px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.2);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--neon-cyan);
            text-shadow: 0 0 10px var(--neon-cyan);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-container {
            background: var(--dark-surface);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid var(--border-glow);
        }

        .table-header {
            background: var(--dark-card);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-glow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--neon-cyan);
        }

        .cyber-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cyber-table th,
        .cyber-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-glow);
        }

        .cyber-table th {
            background: var(--dark-card);
            color: var(--neon-cyan);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }

        .cyber-table tbody tr {
            transition: all 0.3s ease;
        }

        .cyber-table tbody tr:hover {
            background: rgba(0, 255, 255, 0.1);
            transform: scale(1.01);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-family: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--neon-cyan), var(--neon-pink));
            color: var(--dark-bg);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--text-secondary);
        }

        .btn-success {
            background: var(--success);
            color: var(--dark-bg);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--text-primary);
        }

        .btn-warning {
            background: var(--warning);
            color: var(--dark-bg);
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 255, 0.4);
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-active {
            background: var(--success);
            color: var(--dark-bg);
        }

        .badge-inactive {
            background: var(--text-muted);
            color: var(--text-primary);
        }

        .badge-inbound {
            background: var(--neon-cyan);
            color: var(--dark-bg);
        }

        .badge-outbound {
            background: var(--neon-pink);
            color: var(--dark-bg);
        }

        .badge-low-stock {
            background: var(--warning);
            color: var(--dark-bg);
            animation: warning-pulse 1s ease-in-out infinite alternate;
        }

        .badge-admin {
            background: var(--neon-purple);
            color: var(--text-primary);
        }

        .badge-manager {
            background: var(--neon-cyan);
            color: var(--dark-bg);
        }

        .badge-staff {
            background: var(--text-muted);
            color: var(--text-primary);
        }

        @keyframes warning-pulse {
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--neon-cyan);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: var(--dark-surface);
            border: 1px solid var(--border-glow);
            border-radius: 4px;
            color: var(--text-primary);
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--neon-cyan);
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
        }

        .card {
            background: var(--dark-card);
            border: 1px solid var(--border-glow);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            background: var(--dark-surface);
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-glow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card-title {
            color: var(--neon-cyan);
            font-weight: 700;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Custom Checkbox Styling */
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 0.9rem;
            user-select: none;
            margin: 0.5rem 0;
        }

        .checkbox-container input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 22px;
            height: 22px;
            border: 2px solid var(--border-glow);
            border-radius: 4px;
            background: var(--dark-surface);
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .checkbox-container:hover .checkmark {
            border-color: var(--neon-cyan);
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
        }

        .checkbox-container input[type="checkbox"]:checked + .checkmark {
            background: var(--neon-cyan);
            border-color: var(--neon-cyan);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
        }

        .checkbox-container input[type="checkbox"]:checked + .checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--dark-bg);
            font-size: 14px;
            font-weight: bold;
            text-shadow: none;
        }

        .checkbox-container input[type="checkbox"]:checked ~ .checkbox-label {
            color: var(--neon-cyan);
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .user-info {
                order: -1;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 2rem;
            }

            .container {
                padding: 1rem;
            }

            .cyber-table {
                font-size: 0.8rem;
            }

            .cyber-table th,
            .cyber-table td {
                padding: 0.5rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('dashboard') }}" class="logo">CYventory</a>
            <ul class="nav-links">
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">Inventory</a></li>
                <li><a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">Transactions</a></li>
                @if(auth()->user()->canManageUsers())
                    <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a></li>
                @endif
            </ul>
            <div class="user-info">
                <div>
                    <span>{{ auth()->user()->name }}</span>
                    <span class="user-role role-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;" id="logout-form">
                    @csrf
                    <button type="button" class="logout-btn" onclick="confirmLogout()">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <div class="container">
        @yield('content')
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Custom SweetAlert2 theme for cyberpunk style
        const cyberpunkTheme = {
            background: '#1a1a1a',
            color: '#ffffff',
            confirmButtonColor: '#00ffff',
            cancelButtonColor: '#ff4444',
            customClass: {
                popup: 'cyber-swal-popup',
                title: 'cyber-swal-title',
                htmlContainer: 'cyber-swal-html',
                confirmButton: 'cyber-swal-confirm',
                cancelButton: 'cyber-swal-cancel'
            }
        };

        // Success notification
        function showSuccess(title, message) {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                ...cyberpunkTheme,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }

        // Error notification
        function showError(title, message) {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                ...cyberpunkTheme,
                confirmButtonText: 'Understood'
            });
        }

        // Warning with confirmation
        function showWarning(title, message, confirmCallback) {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: message,
                ...cyberpunkTheme,
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                }
            });
        }

        // Delete confirmation
        function confirmDelete(title, message, confirmCallback) {
            Swal.fire({
                icon: 'warning',
                title: title,
                html: message,
                ...cyberpunkTheme,
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                }
            });
        }

        // Update confirmation
        function confirmUpdate(title, message, confirmCallback) {
            Swal.fire({
                icon: 'question',
                title: title,
                html: message,
                ...cyberpunkTheme,
                showCancelButton: true,
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                }
            });
        }

        // Logout confirmation
        function confirmLogout() {
            Swal.fire({
                icon: 'question',
                title: 'Logout Confirmation',
                text: 'Are you sure you want to logout from CYventory?',
                ...cyberpunkTheme,
                showCancelButton: true,
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Stay logged in'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // Transaction confirmation with details
        function confirmTransaction(data, confirmCallback) {
            const itemName = data.itemName || 'Unknown Item';
            const transactionType = data.transactionType || 'Unknown';
            const quantity = data.quantity || '0';
            const unitPrice = data.unitPrice || '0.00';
            const totalAmount = data.totalAmount || '0.00';
            const currentStock = data.currentStock || '0';
            const newStock = data.newStock || '0';
            
            const html = `
                <div class="transaction-summary">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <strong>Item:</strong> <span>${itemName}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Type:</strong> <span class="type-${transactionType.toLowerCase()}">${transactionType}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Quantity:</strong> <span>${quantity}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Unit Price:</strong> <span>₱${unitPrice}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Total Amount:</strong> <span class="highlight">₱${totalAmount}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Stock Change:</strong> <span>${currentStock} → ${newStock}</span>
                        </div>
                    </div>
                </div>
            `;
            
            Swal.fire({
                icon: 'question',
                title: 'Confirm Transaction',
                html: html,
                ...cyberpunkTheme,
                showCancelButton: true,
                confirmButtonText: 'Process Transaction',
                cancelButtonText: 'Cancel',
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                }
            });
        }

        // Show login success message
        @if(session('success') && request()->routeIs('dashboard'))
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('Welcome to CYventory!', '{{ session('success') }}');
        });
        @endif

        // Show general success messages
        @if(session('success') && !request()->routeIs('dashboard'))
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('Success!', '{{ session('success') }}');
        });
        @endif

        // Show error messages
        @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            showError('Error!', '{{ session('error') }}');
        });
        @endif

        // Show warning messages
        @if(session('warning'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: '{{ session('warning') }}',
                ...cyberpunkTheme,
                confirmButtonText: 'OK'
            });
        });
        @endif

        // Auto-hide alerts after 5 seconds - disable since we're using SweetAlert2
        // const alerts = document.querySelectorAll('.alert');
        // alerts.forEach(alert => {
        //     setTimeout(() => {
        //         alert.style.opacity = '0';
        //         alert.style.transform = 'translateY(-20px)';
        //         setTimeout(() => alert.remove(), 300);
        //     }, 5000);
        // });

        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('.cyber-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 0 20px rgba(0, 255, 255, 0.2)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });
    </script>
    
    <style>
    /* Custom SweetAlert2 Cyberpunk Styling */
    .cyber-swal-popup {
        background: var(--dark-card) !important;
        border: 1px solid var(--neon-cyan) !important;
        border-radius: 8px !important;
        box-shadow: 0 0 30px rgba(0, 255, 255, 0.3) !important;
    }

    /* Ensure all SweetAlert2 elements use Orbitron */
    .swal2-container * {
        font-family: 'Orbitron', monospace !important;
    }

    .cyber-swal-title {
        color: var(--neon-cyan) !important;
        font-family: 'Orbitron', monospace !important;
        font-weight: 700 !important;
        text-shadow: 0 0 10px var(--neon-cyan) !important;
    }

    .cyber-swal-html {
        color: var(--text-primary) !important;
        font-family: 'Orbitron', monospace !important;
    }

    .cyber-swal-confirm {
        background: linear-gradient(45deg, var(--neon-cyan), var(--neon-pink)) !important;
        color: var(--dark-bg) !important;
        border: none !important;
        border-radius: 4px !important;
        font-family: 'Orbitron', monospace !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.3) !important;
    }

    .cyber-swal-cancel {
        background: transparent !important;
        color: var(--text-secondary) !important;
        border: 1px solid var(--text-secondary) !important;
        border-radius: 4px !important;
        font-family: 'Orbitron', monospace !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
    }

    .cyber-swal-cancel:hover {
        background: var(--text-secondary) !important;
        color: var(--dark-bg) !important;
    }

    /* SweetAlert2 close button and inputs in Orbitron style */
    .swal2-close {
        font-family: 'Orbitron', monospace !important;
    }

    .swal2-input,
    .swal2-textarea,
    .swal2-select,
    .swal2-radio,
    .swal2-checkbox,
    .swal2-file {
        font-family: 'Orbitron', monospace !important;
    }

    /* Transaction Summary Styling */
    .transaction-summary {
        text-align: left;
        margin: 1rem 0;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.5rem;
        background: rgba(0, 255, 255, 0.05);
        padding: 1rem;
        border-radius: 4px;
        border: 1px solid rgba(0, 255, 255, 0.2);
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.25rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .type-inbound {
        color: var(--neon-cyan);
        font-weight: bold;
    }

    .type-outbound {
        color: var(--neon-pink);
        font-weight: bold;
    }

    .highlight {
        color: var(--neon-cyan);
        font-weight: bold;
        text-shadow: 0 0 5px var(--neon-cyan);
    }

    /* Override SweetAlert2 icons */
    .swal2-icon.swal2-success {
        color: var(--success) !important;
        border-color: var(--success) !important;
    }

    .swal2-icon.swal2-error {
        color: var(--danger) !important;
        border-color: var(--danger) !important;
    }

    .swal2-icon.swal2-warning {
        color: var(--warning) !important;
        border-color: var(--warning) !important;
    }

    .swal2-icon.swal2-question {
        color: var(--neon-cyan) !important;
        border-color: var(--neon-cyan) !important;
    }

    /* Progress bar for timed alerts */
    .swal2-timer-progress-bar {
        background: var(--neon-cyan) !important;
    }
    </style>
    
    @stack('scripts')
</body>
</html>