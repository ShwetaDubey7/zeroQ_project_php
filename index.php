<?php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
ob_start();
?>

<div class="relative min-h-screen flex flex-col">
    <!-- Hero Section -->
    <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 pt-32 pb-16">
        <!-- Accent Circles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative max-w-2xl mx-auto text-center space-y-6 mb-12">
            <h1 class="text-5xl sm:text-6xl font-bold tracking-tight -mt-12">
                Skip the Line with
                <span class="text-accent animate-gradient bg-gradient-to-r from-accent via-purple-500 to-accent bg-clip-text text-transparent">
                    ZeroQ
                </span>
            </h1>
            <p class="text-xl text-light/70">
                Join a queue from your phone and get notified when it's your turn.
                Experience seamless queue management like never before.
            </p>
        </div>

        <!-- Queue Join Card -->
        <div class="relative -mt-6 w-full max-w-md mx-auto">
            <div class="backdrop-blur-xl bg-light/5 rounded-2xl border border-light/10 shadow-2xl glow">
                <div class="p-8">
                    <form action="join.php" method="POST" class="space-y-6">
                        <div>
                            <input type="text"
                                name="queue_code"
                                required
                                placeholder="Enter 6-digit code"
                                class="w-full bg-dark/50 border-2 border-accent/20 rounded-xl px-4 py-3 text-center text-2xl tracking-widest placeholder-light/30 focus:border-accent focus:ring-1 focus:ring-accent transition-all"
                                maxlength="6"
                                style="text-transform:uppercase">
                        </div>
                        <button type="submit"
                            class="w-full px-6 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all flex items-center justify-center space-x-2">
                            <span>Join Queue</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </form>
                </div>
                <div class="border-t border-light/10 p-4 text-center">
                    <a href="admin/login.php" class="text-accent hover:text-accent/80 text-sm">
                        Are you a business? Login →
                    </a>
                </div>
            </div>

            <!-- Login & Register Buttons (Updated) -->
            <div class="flex justify-center items-center space-x-8 pt-12 -mt-6">
                <!-- Simple Login Button -->
                <a href="user/login_user.php"
                    class="px-8 py-3 border-2 border-accent/50 rounded-xl text-accent hover:bg-accent hover:text-light transition-all duration-300 backdrop-blur-xl bg-dark/50">
                    Login
                </a>
                
                <!-- Register Dropdown Button -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="px-8 py-3 border-2 border-accent/50 rounded-xl text-accent hover:bg-accent hover:text-light transition-all duration-300 backdrop-blur-xl bg-dark/50 flex items-center">
                        Register
                        <svg class="ml-2 w-5 h-5 transition-transform duration-300"
                            :class="{ 'rotate-180': open }"
                            fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-dark/95 backdrop-blur-xl border border-accent/20"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div class="py-1">
                            <a href="user/register_user.php"
                                class="block px-4 py-2 text-sm text-light hover:text-accent transition-colors">
                                Register as User
                            </a>
                        </div>
                        <div class="border-t border-accent/10"></div>
                        <div class="py-1">
                            <a href="admin/register.php"
                                class="block px-4 py-2 text-sm text-light hover:text-accent transition-colors">
                                Register as Business
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'ZeroQ - Skip the Line';
require_once 'includes/template.php';
?>