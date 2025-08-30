<?php
// template.php - Base template for ZeroQ project
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'ZeroQ - Smart Queue Management' ?></title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts: Space Grotesk -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind Configuration -->
     <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        dark: '#0F172A',
                        light: '#F8FAFC',
                        accent: '#6366F1',
                    }
                }
            }
        }
    </script>

    <style>
        .glow {
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.2);
        }
        
        .animate-gradient {
            background-size: 200% 200%;
            animation: moveGradient 5s ease infinite;
        }

        @keyframes moveGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .blur-backdrop {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Hamburger Menu Animation */
        .hamburger-line {
            transition: transform 0.3s ease-in-out;
        }
        
        .hamburger-active .line-1 {
            transform: translateY(11px) rotate(60deg);
        }
        
        .hamburger-active .line-2 {
            transform: translateY(-11px) scale(0);
        }
        
        .hamburger-active .line-3 {
            transform: translateY(-11px) rotate(-60deg);
        }
    </style>
</head>
<body x-data="navigation" class="bg-dark text-light min-h-screen">
    <!-- Updated Header/Navigation -->
    <header class="fixed w-full z-50 blur-backdrop bg-dark/50 border-b border-accent/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="index.php" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold">
                        <span class="text-accent animate-gradient bg-gradient-to-r from-accent via-purple-500 to-accent bg-clip-text text-transparent">
                            ZeroQ
                        </span>
                    </span>
                </a>

                <!-- Desktop Navigation (THEEK KIYE GAYE PATHS) -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="about.php" class="text-light/70 hover:text-accent transition-colors">About</a>
                    <a href="features.php" class="text-light/70 hover:text-accent transition-colors">Features</a>
                    <a href="pricing.php" class="text-light/70 hover:text-accent transition-colors">Pricing</a>
                    <a href="contact.php" class="text-light/70 hover:text-accent transition-colors">Contact</a>
                </nav>

                <!-- Hamburger Menu Button -->
                <button 
                    class="md:hidden p-2 rounded-lg hover:bg-accent/10 transition-colors"
                    @click="mobileMenu = !mobileMenu"
                    :class="{ 'hamburger-active': mobileMenu }"
                    aria-label="Menu">
                    <div class="w-6 h-6 flex flex-col justify-between">
                        <span class="hamburger-line w-full h-0.5 bg-light/70 line-1"></span>
                        <span class="hamburger-line w-full h-0.5 bg-light/70 line-2"></span>
                        <span class="hamburger-line w-full h-0.5 bg-light/70 line-3"></span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Mobile Menu (THEEK KIYE GAYE PATHS) -->
        <div 
            x-show="mobileMenu" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden absolute inset-x-0 top-20 blur-backdrop bg-dark/95 border-b border-accent/10"
            @click.away="mobileMenu = false">
            <nav class="px-4 py-6 space-y-4">
                <a href="about.php" class="block py-2 text-light/70 hover:text-accent transition-colors">About</a>
                <a href="features.php" class="block py-2 text-light/70 hover:text-accent transition-colors">Features</a>
                <a href="pricing.php" class="block py-2 text-light/70 hover:text-accent transition-colors">Pricing</a>
                <a href="contact.php" class="block py-2 text-light/70 hover:text-accent transition-colors">Contact</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20">
        <?= $content ?? '' ?>
    </main>

    <!-- Updated Footer (THEEK KIYE GAYE PATHS) -->
    <footer class="bg-dark/50 blur-backdrop border-t border-accent/10 mt-0">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="space-y-4">
                    <span class="text-2xl font-bold">
                        <span class="text-accent animate-gradient bg-gradient-to-r from-accent via-purple-500 to-accent bg-clip-text text-transparent">ZeroQ</span>
                    </span>
                    <p class="text-light/50 text-sm">
                        Smart queue management system for modern businesses.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-light/70 uppercase tracking-wider mb-4">Company</h3>
                    <div class="space-y-3">
                        <a href="about.php" class="block text-light/50 hover:text-accent transition-colors">About</a>
                        <a href="features.php" class="block text-light/50 hover:text-accent transition-colors">Features</a>
                        <a href="pricing.php" class="block text-light/50 hover:text-accent transition-colors">Pricing</a>
                    </div>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-sm font-semibold text-light/70 uppercase tracking-wider mb-4">Support</h3>
                    <div class="space-y-3">
                        <a href="contact.php" class="block text-light/50 hover:text-accent transition-colors">Contact</a>
                        <a href="faq.php" class="block text-light/50 hover:text-accent transition-colors">FAQ</a>
                        <a href="help.php" class="block text-light/50 hover:text-accent transition-colors">Help Center</a>
                    </div>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-sm font-semibold text-light/70 uppercase tracking-wider mb-4">Legal</h3>
                    <div class="space-y-3">
                        <a href="privacy.php" class="block text-light/50 hover:text-accent transition-colors">Privacy</a>
                        <a href="terms.php" class="block text-light/50 hover:text-accent transition-colors">Terms</a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-16 pt-16 border-t border-accent/10 justify-center">
                <p class="text-center text-light/50 text-sm">
                    &copy; <?= date('Y') ?> ZeroQ. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Add this script before closing body tag -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('navigation', () => ({
                mobileMenu: false
            }))
        })
    </script>
</body>
</html>
