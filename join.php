<?php
require_once 'includes/db.php'; 

$errorMessage = '';
$business = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['queue_code'])) {
    $queueCode = trim($_POST['queue_code']);

    if (empty($queueCode)) {
        $errorMessage = 'Queue Code cannot be empty.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, business_name FROM businesses WHERE queue_code = ? AND queue_status = 'open'");
        mysqli_stmt_bind_param($stmt, "s", $queueCode);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $business = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$business) {
            $errorMessage = 'Invalid Queue Code or the queue is currently closed. Please try again.';
        }
    }
} else {
    header('Location: index.php');
    exit;
}

mysqli_close($conn);
ob_start();
?>

<div class="relative min-h-screen flex items-center justify-center p-4">
    <!-- Accent Circles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md mx-auto">
        <div class="backdrop-blur-xl bg-light/5 rounded-2xl border border-light/10 shadow-2xl glow p-8">
            <?php if ($errorMessage): ?>
                <div class="text-center space-y-4">
                    <h2 class="text-2xl font-bold text-red-400">Error</h2>
                    <p class="text-light/70"><?= htmlspecialchars($errorMessage); ?></p>
                    <a href="index.php" class="inline-block px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all">
                        Go Back
                    </a>
                </div>
            <?php elseif ($business): ?>
                <div class="text-center mb-6">
                    <p class="text-light/70">You are joining the queue for:</p>
                    <h2 class="text-3xl font-bold mt-2 text-light"><?= htmlspecialchars($business['business_name']); ?></h2>
                </div>
                
                <form action="handle_join.php" method="POST" class="space-y-6">
                    <input type="hidden" name="business_id" value="<?= $business['id']; ?>">
                    
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-light/60 mb-1">Your Name</label>
                        <input type="text" name="customer_name" id="customer_name" required placeholder="Enter your name to join" class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all flex items-center justify-center space-x-2">
                        <span>Confirm & Join Queue</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Join Queue';
require_once 'includes/template.php';
?>

