<?php
// includes/page_banner.php
// Usage: set $banner_title, $banner_subtitle, $banner_photo, $banner_icon before including this file.
// Falls back to sensible defaults if not set.

$banner_title = $banner_title ?? 'City Care Hospital';
$banner_subtitle = $banner_subtitle ?? '';
$banner_photo = $banner_photo ?? 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=1600&q=80';
$banner_icon = $banner_icon ?? '🏥';
$banner_gradient = $banner_gradient ?? 'from-blue-900/85 via-blue-800/80 to-indigo-900/85';
?>
<div class="relative overflow-hidden rounded-3xl mb-8 h-52 flex items-center" data-aos="fade-up">
    <img src="<?php echo htmlspecialchars($banner_photo); ?>" class="absolute inset-0 w-full h-full object-cover" alt="<?php echo htmlspecialchars($banner_title); ?>"
         onerror="this.src='https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=1600&q=80';">
    <div class="absolute inset-0 bg-gradient-to-br <?php echo $banner_gradient; ?>"></div>
    <div class="blob w-56 h-56 bg-white top-0 right-0 opacity-10"></div>
    <div class="relative z-10 px-8 text-white flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center text-3xl shrink-0">
            <?php echo $banner_icon; ?>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-display font-bold"><?php echo htmlspecialchars($banner_title); ?></h1>
            <?php if ($banner_subtitle): ?>
                <p class="text-blue-100 text-sm mt-1"><?php echo htmlspecialchars($banner_subtitle); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>