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
        <h1 class="text-5xl font-bold">Simple <span class="text-accent">Pricing</span></h1>
        <p class="text-xl text-light/70 mt-4">
            Our pricing is straightforward and affordable for businesses of all sizes. Contact us for a custom quote based on your needs.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Pricing';
require_once 'includes/template.php';
?>
