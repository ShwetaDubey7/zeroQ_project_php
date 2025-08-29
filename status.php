<?php
session_start();

if (!isset($_SESSION['queue_id'])) {
    header('Location: index.php?error=You have not joined a queue.');
    exit;
}

require_once 'includes/db.php';

$queueId = $_SESSION['queue_id'];
$customerData = null;
$businessData = null;
$peopleAhead = 0;

try {
    // Fetch Customer's Own Data
    $stmt = mysqli_prepare($conn, "SELECT * FROM queues WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $queueId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $customerData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$customerData) {
        unset($_SESSION['queue_id']);
        header('Location: index.php?error=Your queue ticket was not found.');
        exit;
    }

    // Fetch Business Data
    $businessId = $customerData['business_id'];
    $stmt = mysqli_prepare($conn, "SELECT business_name FROM businesses WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $businessId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $businessData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Count People Ahead in the Queue
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS ahead FROM queues WHERE business_id = ? AND status = 'waiting' AND join_time < ?");
    mysqli_stmt_bind_param($stmt, "is", $businessId, $customerData['join_time']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $aheadData = mysqli_fetch_assoc($result);
    $peopleAhead = $aheadData['ahead'];
    mysqli_stmt_close($stmt);

} catch (mysqli_sql_exception $e) {
    error_log("Database error in status.php: " . $e->getMessage());
    header('Location: index.php?error=A database error occurred.');
    exit;
} finally {
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}

ob_start();
?>

<div class="relative min-h-screen flex items-center justify-center p-4">
    <!-- Accent Circles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md mx-auto text-center">
        <div class="backdrop-blur-xl bg-light/5 rounded-2xl border border-light/10 shadow-2xl glow p-8 space-y-6">
            <div>
                <p class="text-light/70">Your Queue Status at</p>
                <h2 class="text-3xl font-bold mt-1 text-light"><?= htmlspecialchars($businessData['business_name']); ?></h2>
            </div>
            
            <div class="border-t border-b border-light/10 py-6">
                <p class="text-light/70">Your Token Number</p>
                <p class="text-8xl font-extrabold text-accent my-2"><?= htmlspecialchars($customerData['token_number']); ?></p>
            </div>

            <div class="space-y-2">
                <p class="text-light/70">Current Status</p>
                <?php if ($customerData['status'] === 'serving'): ?>
                    <p class="text-3xl font-bold text-green-400 animate-pulse">It's Your Turn!</p>
                <?php else: ?>
                    <p class="text-3xl font-bold text-yellow-400">You are in the queue.</p>
                    <p class="text-xl text-light/80 mt-2">
                        <span class="font-bold text-2xl"><?= $peopleAhead; ?></span> 
                        <?= ($peopleAhead === 1) ? 'person' : 'people'; ?> ahead of you.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <p class="text-xs text-light/40 mt-6">This page will automatically refresh every 15 seconds.</p>
    </div>
</div>

<?php
// Add auto-refresh header before any other output
header("Refresh: 15");
$content = ob_get_clean();
$pageTitle = 'Your Queue Status';
require_once 'includes/template.php';
?>