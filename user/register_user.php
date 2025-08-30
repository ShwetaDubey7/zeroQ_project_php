<?php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
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
            <p class="text-xl text-light/70 mt-2">Create Your Account</p>
        </div>

        <div class="backdrop-blur-xl bg-light/5 rounded-2xl border border-light/10 shadow-2xl glow p-8">
            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-xl mb-6 text-center" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form action="../actions/handle_register_user.php" method="POST" class="space-y-6">
                 <div>
                    <label for="name" class="block text-sm font-medium text-light/60 mb-1">Full Name</label>
                    <input type="text" name="name" id="name" required class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-light/60 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-light/60 mb-1">Password</label>
                    <input type="password" name="password" id="password" required class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all">
                    Create Account
                </button>
            </form>
        </div>
         <p class="mt-8 text-center text-sm text-light/50">
            Already have an account? <a href="login_user.php" class="font-semibold text-accent hover:underline">Login here</a>.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'User Registration';
require_once '../includes/template.php'; 
?>
