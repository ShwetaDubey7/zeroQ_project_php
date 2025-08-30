<?php
session_start();
// Error aur success, dono messages ko session se lein
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
// Dono messages ko session se hata dein
unset($_SESSION['error'], $_SESSION['success']);
ob_start();
?>

<div class="relative min-h-screen flex items-center justify-center p-4">
    <!-- Accent Circles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold tracking-tight">
                <span class="text-accent animate-gradient bg-gradient-to-r from-accent via-purple-500 to-accent bg-clip-text text-transparent">
                    ZeroQ
                </span>
            </h1>
            <p class="text-xl text-light/70 mt-2">Business Portal Login</p>
        </div>

        <div class="backdrop-blur-xl bg-light/5 rounded-2xl border border-light/10 shadow-2xl glow p-8">
            <!-- Success Message Section -->
            <?php if ($success): ?>
                <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-xl mb-6 text-center" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Error Message Section -->
            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-xl mb-6 text-center" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- THEEK KIYA GAYA PATH: '../actions/handle_admin_login.php' se '../actions/handle_login.php' -->
            <form action="../actions/handle_login.php" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-light/60 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-light/60 mb-1">Password</label>
                    <input type="password" name="password" id="password" required class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all flex items-center justify-center space-x-2">
                    <span>Sign In</span>
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </form>
        </div>
         <p class="mt-8 text-center text-sm text-light/50">
            Don't have an account? <a href="register.php" class="font-semibold text-accent hover:underline">Register here</a>.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Business Login';
require_once '../includes/template.php'; 
?>

