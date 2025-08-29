<?php
session_start();
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
ob_start();
?>

<div class="relative min-h-screen flex items-center justify-center p-4">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold text-accent">ZeroQ</h1>
            <p class="text-xl text-light/70 mt-2">User Login</p>
        </div>

        <div class="backdrop-blur-xl bg-light/5 rounded-2xl border border-light/10 shadow-2xl glow p-8">
            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-xl mb-6 text-center" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                 <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-xl mb-6 text-center" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            <form action="../actions/handle_login_user.php" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-light/60 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-light/60 mb-1">Password</label>
                    <input type="password" name="password" id="password" required class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all">
                    Login
                </button>
            </form>
        </div>
         <p class="mt-8 text-center text-sm text-light/50">
            Don't have an account? <a href="register_user.php" class="font-semibold text-accent hover:underline">Register here</a>.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'User Login';
require_once '../includes/template.php';
?>