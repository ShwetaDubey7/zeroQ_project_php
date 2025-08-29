<?php
ob_start();
?>

<div class="relative min-h-screen flex flex-col items-center justify-center p-4 text-center">
    <!-- Accent Circles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-accent/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-3xl mx-auto">
        <h1 class="text-5xl font-bold">Contact <span class="text-accent">Us</span></h1>
        <p class="text-xl text-light/70 mt-4">
            Have questions or need support? We're here to help. Reach out to us via email for any inquiries regarding our services.
        </p>
        <div class="mt-8">
            <a href="mailto:support@zeroq.com" class="inline-block px-8 py-3 bg-accent hover:bg-accent/90 text-light rounded-xl transition-all text-lg font-semibold">
                support@zeroq.com
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Contact Us';
require_once 'includes/template.php';
?>
