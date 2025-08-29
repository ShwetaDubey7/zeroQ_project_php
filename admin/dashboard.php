<?php
session_start();

if (!isset($_SESSION['business_id'])) {
    header('Location: login.php');
    exit;
}

// Ensure you have a db.php file that provides a $conn variable for MySQLi
require_once '../includes/db.php';

$businessId = $_SESSION['business_id'];
$businessName = $_SESSION['business_name'];

// Fetch business details using MySQLi
$stmt = mysqli_prepare($conn, "SELECT queue_code, queue_status FROM businesses WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $businessId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$business = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Fetch customer list using MySQLi
$stmt = mysqli_prepare($conn, "SELECT id, customer_name, token_number, status, join_time FROM queues WHERE business_id = ? AND status IN ('waiting', 'serving') ORDER BY join_time ASC");
mysqli_stmt_bind_param($stmt, "i", $businessId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customerList = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

mysqli_close($conn);

ob_start();
?>

<div class="relative min-h-screen flex flex-col">
    <!-- Accent Circles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Header -->
    <header class="relative backdrop-blur-xl bg-dark/50 border-b border-light/10 sticky top-0 z-10">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                 <span class="text-accent animate-gradient bg-gradient-to-r from-accent via-purple-500 to-accent bg-clip-text text-transparent">
                    ZeroQ Dashboard
                </span>
            </h1>
            <div>
                <span class="text-light/70 mr-4 hidden sm:inline">Welcome, <?= htmlspecialchars($businessName); ?>!</span>
                <a href="logout.php" class="px-4 py-2 bg-red-600/50 text-red-200 border border-red-500 rounded-lg hover:bg-red-600/80 transition-colors">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative container mx-auto px-6 py-8 flex-1">
        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="backdrop-blur-xl bg-light/5 p-6 rounded-2xl border border-light/10 glow">
                <h2 class="text-lg font-semibold text-light/50 mb-2">Your Queue Code</h2>
                <p class="text-4xl font-bold text-accent tracking-widest"><?= htmlspecialchars($business['queue_code']); ?></p>
            </div>
            <div class="backdrop-blur-xl bg-light/5 p-6 rounded-2xl border border-light/10 glow">
                <h2 class="text-lg font-semibold text-light/50 mb-2">Queue Status</h2>
                <p class="text-4xl font-bold <?= $business['queue_status'] === 'open' ? 'text-green-400' : 'text-red-400'; ?>"><?= ucfirst($business['queue_status']); ?></p>
            </div>
            <div class="backdrop-blur-xl bg-light/5 p-6 rounded-2xl border border-light/10 glow">
                <h2 class="text-lg font-semibold text-light/50 mb-2">Customers Waiting</h2>
                <p class="text-4xl font-bold text-light"><?= count($customerList); ?></p>
            </div>
        </div>

        <!-- NEW: Invite User Section -->
        <div class="backdrop-blur-xl bg-light/5 p-8 rounded-2xl border border-light/10 shadow-lg mb-8">
            <h2 class="text-2xl font-bold mb-4">Invite User</h2>
            <?php if (isset($_SESSION['invite_success'])): ?>
                <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-xl mb-4 text-center" role="alert">
                    <?= htmlspecialchars($_SESSION['invite_success']) ?>
                </div>
                <?php unset($_SESSION['invite_success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['invite_error'])): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-xl mb-4 text-center" role="alert">
                    <?= htmlspecialchars($_SESSION['invite_error']) ?>
                </div>
                <?php unset($_SESSION['invite_error']); ?>
            <?php endif; ?>
            <form action="../actions/handle_invitation.php" method="POST" class="flex items-center space-x-4">
                <input type="email" name="user_email" placeholder="Enter User email" required class="flex-grow bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all">
                <button type="submit" class="px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all">
                    Invite
                </button>
            </form>
        </div>


        <!-- Management Section -->
        <div class="backdrop-blur-xl bg-light/5 p-8 rounded-2xl border border-light/10 glow">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold">Manage Queue</h2>
                <div class="flex items-center gap-4">
                    <form action="manage_queue.php" method="POST">
                        <button type="submit" name="action" value="<?= $business['queue_status'] === 'open' ? 'close_queue' : 'open_queue'; ?>" class="px-6 py-3 rounded-xl text-light transition-all <?= $business['queue_status'] === 'open' ? 'bg-red-600/80 hover:bg-red-600' : 'bg-green-600/80 hover:bg-green-600'; ?> "><?= $business['queue_status'] === 'open' ? 'Close Queue' : 'Open Queue'; ?></button>
                    </form>
                    <form action="manage_queue.php" method="POST">
                        <button type="submit" name="action" value="call_next" class="px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all disabled:bg-gray-500/50 disabled:cursor-not-allowed" <?= empty($customerList) ? 'disabled' : ''; ?>>Call Next</button>
                    </form>
                </div>
            </div>

            <!-- Customer List -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-light/10">
                            <th class="p-4 font-semibold text-light/60">Token</th>
                            <th class="p-4 font-semibold text-light/60">Name</th>
                            <th class="p-4 font-semibold text-light/60">Status</th>
                            <th class="p-4 font-semibold text-light/60">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customerList)): ?>
                            <tr>
                                <td colspan="4" class="p-6 text-center text-light/50">The queue is empty.</td>
                            </tr>
                            <?php else: foreach ($customerList as $customer): ?>
                                <tr class="border-b border-light/5 hover:bg-light/5">
                                    <td class="p-4 font-mono text-lg text-accent"><?= htmlspecialchars($customer['token_number']); ?></td>
                                    <td class="p-4"><?= htmlspecialchars($customer['customer_name']); ?></td>
                                    <td class="p-4"><span class="px-3 py-1 text-sm font-semibold rounded-full <?= $customer['status'] === 'serving' ? 'bg-green-500/20 text-green-300' : 'bg-yellow-500/20 text-yellow-300'; ?>"><?= ucfirst($customer['status']); ?></span></td>
                                    <td class="p-4 text-light/60"><?= date('g:i A', strtotime($customer['join_time'])); ?></td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Dashboard';
require_once '../includes/template.php';
?>