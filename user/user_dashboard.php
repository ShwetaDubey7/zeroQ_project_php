<?php
session_start();
require_once '../includes/db.php';
// Protect this page - only logged-in users can see it
if (!isset($_SESSION['user_id'])) {
    // THEEK KIYA GAYA REDIRECT PATH: 'login.php' se 'login_user.php'
    header('Location: ./login_user.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

$invitations = [];
try {
    $sql = "SELECT i.id, b.business_name, b.queue_code 
            FROM invitations i
            JOIN businesses b ON i.business_id = b.id
            WHERE i.user_id = ? AND i.status = 'pending'";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invitations = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

} catch (mysqli_sql_exception $e) {
    error_log("Database error on user dashboard: " . $e->getMessage());
    // Handle error appropriately
}

$pageTitle = 'Dashboard';
ob_start();
?>

<div class="relative min-h-screen bg-dark text-light p-4 sm:p-6 lg:p-8">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="absolute top-6 right-6">
        <a href="logout_user.php" class="px-4 py-2 bg-red-600/50 text-red-200 border border-red-500 rounded-lg hover:bg-red-600/80 transition-colors">Logout</a>
    </div>

    <div class="relative max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-light mb-2">Welcome, <span class="text-accent"><?= htmlspecialchars($userName) ?>!</span></h1>
            <p class="text-light/60">Manage your queues and invitations here.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Side: Main Actions -->
            <div class="space-y-6">
                <a href="/index.php" class="block w-full text-center px-6 py-4 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all text-lg font-semibold">
                    Join a New Queue
                </a>
                <a href="/status.php" class="block w-full text-center px-6 py-4 backdrop-blur-xl bg-light/5 border border-light/10 hover:bg-light/10 text-light rounded-xl transition-all text-lg font-semibold">
                    Check My Queue Status
                </a>
            </div>

            <!-- Right Side: Invitations -->
            <div class="backdrop-blur-xl bg-light/5 rounded-2xl border border-light/10 shadow-2xl glow p-8">
                <h2 class="text-2xl font-bold mb-6 text-center">Pending Invitations</h2>
                
                <?php if (empty($invitations)): ?>
                    <p class="text-light/50 text-center">You don't have any pending invitations.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($invitations as $invite): ?>
                            <div class="flex justify-between items-center bg-dark/50 p-4 rounded-xl border border-light/10">
                                <div>
                                    <p class="font-bold text-lg"><?= htmlspecialchars($invite['business_name']) ?></p>
                                    <p class="text-sm text-light/50">You have been invited to this queue.</p>
                                </div>
                                <form action="/join.php" method="POST">
                                    <input type="hidden" name="queue_code" value="<?= htmlspecialchars($invite['queue_code']) ?>">
                                    <button type="submit" class="px-5 py-2 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all">
                                        Join
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Dashboard';
require_once '../includes/template.php';
?>