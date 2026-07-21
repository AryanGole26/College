<?php
require_once 'config/db.php';
require_once 'includes/dept_theme.php';
$departments = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");
$doctor_count = $conn->query("SELECT COUNT(*) c FROM doctors")->fetch_assoc()['c'];
$dept_count = $conn->query("SELECT COUNT(*) c FROM departments")->fetch_assoc()['c'];
$patient_count = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
require_once 'includes/header.php';
?>

<!-- HERO with video background -->
<div class="relative overflow-hidden rounded-3xl mb-20 -mt-2 min-h-[560px] flex items-center">
    <div id="particle-canvas" class="absolute inset-0"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-[#0a0e1a]/70 via-blue-950/60 to-purple-950/70 pointer-events-none"></div>
    <div class="noise-overlay pointer-events-none"></div>

    <div class="blob w-72 h-72 bg-purple-500 top-0 left-0"></div>
    <div class="blob w-96 h-96 bg-blue-500 bottom-0 right-0" style="animation-delay: 2s;"></div>

    <span class="floating-icon absolute top-24 left-[15%] text-4xl opacity-40" style="animation-delay:0s;">❤️</span>
    <span class="floating-icon absolute top-40 right-[20%] text-4xl opacity-40" style="animation-delay:1.5s;">🩺</span>
    <span class="floating-icon absolute bottom-32 left-[25%] text-4xl opacity-40" style="animation-delay:3s;">💊</span>
    <span class="floating-icon absolute bottom-20 right-[15%] text-4xl opacity-40" style="animation-delay:2s;">🧬</span>

    <div class="relative z-10 w-full text-center px-8 py-24" data-aos="fade-up">
        <span class="inline-block dark-glass px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide mb-6 text-blue-300 ring-1 ring-blue-500/30">
            🩺 Trusted by <?php echo $patient_count; ?>+ patients
        </span>
        <h1 class="text-4xl md:text-6xl font-display font-extrabold mb-5 leading-tight text-white">
            Your Health,<br class="hidden md:block"> <span class="gradient-text">Our Priority</span>
        </h1>
        <p class="text-slate-300 text-lg max-w-2xl mx-auto mb-10">
            Book appointments with trusted doctors across specializations — fast, simple, and secure.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="auth/register.php" class="btn-shine bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold px-8 py-3.5 rounded-full hover:scale-105 transition-transform shadow-xl shadow-blue-900/50">
                    Get Started Free
                </a>
                <a href="auth/login.php" class="dark-glass border border-slate-600 text-white font-semibold px-8 py-3.5 rounded-full hover:bg-slate-800/50 transition-colors">
                    Login
                </a>
            <?php else: ?>
                <a href="<?php echo strtolower($_SESSION['role']); ?>/dashboard.php" class="btn-shine bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold px-8 py-3.5 rounded-full hover:scale-105 transition-transform shadow-xl">
                    Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="grid grid-cols-3 gap-4 md:gap-6 mb-20" data-aos="fade-up">
    <div class="dark-card rounded-2xl p-6 text-center">
        <p class="text-3xl md:text-4xl font-display font-bold gradient-text"><?php echo $doctor_count; ?>+</p>
        <p class="text-xs md:text-sm text-slate-400 mt-1 font-medium">Expert Doctors</p>
    </div>
    <div class="dark-card rounded-2xl p-6 text-center">
        <p class="text-3xl md:text-4xl font-display font-bold gradient-text"><?php echo $dept_count; ?>+</p>
        <p class="text-xs md:text-sm text-slate-400 mt-1 font-medium">Departments</p>
    </div>
    <div class="dark-card rounded-2xl p-6 text-center">
        <p class="text-3xl md:text-4xl font-display font-bold gradient-text"><?php echo $patient_count; ?>+</p>
        <p class="text-xs md:text-sm text-slate-400 mt-1 font-medium">Happy Patients</p>
    </div>
</div>

<!-- DEPARTMENTS - clickable photo cards -->
<div class="mb-20">
    <div class="text-center mb-10" data-aos="fade-up">
        <h2 class="text-3xl font-display font-bold text-white mb-2">Our Departments</h2>
        <p class="text-slate-400">Specialized care across every major field of medicine — click to explore doctors</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $dept_photos = [
            'cardio'    => 'https://images.unsplash.com/photo-1628595351029-c2bf17511435?w=600&q=80',
            'neuro'     => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?w=600&q=80',
            'ortho'     => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=600&q=80',
            'pediatric' => 'https://images.unsplash.com/photo-1587351021355-a479a299d2f9?w=600&q=80',
            'dermat'    => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=600&q=80',
            'general'   => 'https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=600&q=80',
        ];
        $i = 0;
        while ($dep = $departments->fetch_assoc()):
            $dt = dept_theme($dep['name']);
            $photo = $dept_photos['general'];
            foreach ($dept_photos as $key => $url) {
                if (str_contains(strtolower($dep['name']), $key)) { $photo = $url; break; }
            }
            // Where clicking should go: patients go to booking pre-filtered; guests/others go to login
            $click_url = (isset($_SESSION['role']) && $_SESSION['role'] === 'PATIENT')
                ? "patient/book_appointment.php?department_id={$dep['id']}"
                : "auth/login.php";
        ?>
            <a href="<?php echo $click_url; ?>" class="hover-lift group relative rounded-3xl overflow-hidden shadow-md h-64 cursor-pointer block glow-border"
               data-aos="zoom-in" data-aos-delay="<?php echo ($i % 6) * 80; ?>">
                <img src="<?php echo $photo; ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="<?php echo htmlspecialchars($dep['name']); ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=600&q=80';">
                <div class="absolute inset-0 bg-gradient-to-t <?php echo $dt['gradient']; ?> opacity-70 mix-blend-multiply"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                    <div class="text-3xl mb-1"><?php echo $dt['icon']; ?></div>
                    <p class="font-display font-bold text-lg"><?php echo htmlspecialchars($dep['name']); ?></p>
                    <p class="text-xs text-slate-300 opacity-0 group-hover:opacity-100 transition-opacity mt-1">View doctors &rarr;</p>
                </div>
            </a>
            <?php $i++; ?>
        <?php endwhile; ?>
    </div>
</div>

<!-- FEATURES with photos -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
    <div class="hover-lift dark-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="0">
        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=500&q=80" class="w-full h-40 object-cover" alt="Booking">
        <div class="p-6 text-center">
            <h3 class="font-display font-semibold text-white mb-2">Easy Booking</h3>
            <p class="text-sm text-slate-400">Find and book appointments with the right doctor in minutes.</p>
        </div>
    </div>
    <div class="hover-lift dark-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="150">
        <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=500&q=80" class="w-full h-40 object-cover" alt="Doctors">
        <div class="p-6 text-center">
            <h3 class="font-display font-semibold text-white mb-2">Trusted Doctors</h3>
            <p class="text-sm text-slate-400">Experienced specialists across every major department.</p>
        </div>
    </div>
    <div class="hover-lift dark-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="300">
        <img src="https://images.unsplash.com/photo-1550831107-1553da8c8464?w=500&q=80" class="w-full h-40 object-cover" alt="Security">
        <div class="p-6 text-center">
            <h3 class="font-display font-semibold text-white mb-2">Secure & Private</h3>
            <p class="text-sm text-slate-400">Your medical records and history stay protected.</p>
        </div>
    </div>
</div>

<!-- CTA -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-purple-900 to-blue-900 px-8 py-16 text-center text-white mb-10" data-aos="fade-up">
    <div class="blob w-64 h-64 bg-blue-400 top-0 right-1/4"></div>
    <div class="relative z-10">
        <h2 class="text-2xl md:text-3xl font-display font-bold mb-3">Ready to take control of your health?</h2>
        <p class="text-slate-300 mb-8">Create your free account and book your first appointment today.</p>
        <a href="auth/register.php" class="btn-shine inline-block bg-white text-blue-900 font-semibold px-8 py-3.5 rounded-full hover:scale-105 transition-transform">
            Create Free Account
        </a>
    </div>
</div>
<?php endif; ?>
<script src="assets/js/particle-hero.js"></script>
<?php require_once 'includes/footer.php'; ?>