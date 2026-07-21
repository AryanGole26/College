<?php
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../" . strtolower($_SESSION['role']) . "/dashboard.php");
    exit();
}

$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | City Care Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/hospital-appointment-system/assets/css/custom.css">
</head>
<body class="min-h-screen flex bg-[#0a0e1a]">

    <div class="hidden lg:flex w-1/2 relative overflow-hidden items-center justify-center p-12">
        <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=1200&q=80" class="absolute inset-0 w-full h-full object-cover" alt="Hospital">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0a0e1a]/95 via-blue-950/90 to-purple-950/90"></div>
        <div class="blob w-72 h-72 bg-purple-500 top-10 left-10"></div>
        <div class="blob w-96 h-96 bg-blue-500 bottom-0 right-0" style="animation-delay: 2s;"></div>
        <div class="relative z-10 text-white text-center fade-in-up">
            <div class="text-6xl mb-6">🏥</div>
            <h1 class="text-3xl font-display font-bold mb-3">City Care Hospital</h1>
            <p class="text-slate-300 max-w-sm mx-auto">Your trusted healthcare partner. Book appointments, manage your health, all in one place.</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center px-4 py-10 relative overflow-hidden">
        <div class="blob w-64 h-64 bg-blue-600 top-0 right-0 opacity-20"></div>
        <div class="w-full max-w-md fade-in-up relative z-10">
            <div class="mb-8">
                <h1 class="text-3xl font-display font-bold text-white">Welcome Back</h1>
                <p class="text-slate-400 mt-1">Log in to your account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl p-3 mb-6 text-sm">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="login_action.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 bg-slate-900/60 border border-slate-700 text-white placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 bg-slate-900/60 border border-slate-700 text-white placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow">
                </div>
                <button type="submit"
                    class="btn-shine w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:shadow-lg hover:shadow-blue-900/50 text-white font-semibold py-3.5 rounded-xl transition-all">
                    Log In
                </button>
            </form>

            <p class="text-center text-sm text-slate-400 mt-6">
                Don't have an account?
                <a href="register.php" class="text-blue-400 font-medium hover:underline">Register as Patient</a>
            </p>
            <p class="text-center text-sm text-slate-500 mt-3">
                <a href="../index.php" class="hover:underline">&larr; Back to home</a>
            </p>
        </div>
    </div>

</body>
</html>