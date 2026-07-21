<?php
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../" . strtolower($_SESSION['role']) . "/dashboard.php");
    exit();
}

$errors = [];
$old = [
    'name' => '', 'email' => '', 'gender' => '', 'dob' => '', 'mobile' => '',
    'address' => '', 'emergency_contact' => '', 'blood_group' => '',
    'medical_history' => '', 'allergies' => ''
];

if (isset($_SESSION['register_errors'])) {
    $errors = $_SESSION['register_errors'];
    $old = $_SESSION['register_old'];
    unset($_SESSION['register_errors']);
    unset($_SESSION['register_old']);
}

$input_class = "w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 text-white placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow";
$label_class = "block text-sm font-medium text-slate-300 mb-1";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | City Care Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/hospital-appointment-system/assets/css/custom.css">
</head>
<body class="bg-[#0a0e1a] min-h-screen relative overflow-x-hidden">

<div class="blob w-72 h-72 bg-blue-600 top-0 right-0"></div>
<div class="blob w-64 h-64 bg-purple-600 bottom-0 left-0" style="animation-delay: 3s;"></div>

<div class="relative z-10 min-h-screen flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-2xl dark-glass rounded-3xl shadow-xl p-8 fade-in-up">
        <div class="text-center mb-8">
            <div class="text-4xl mb-2">🏥</div>
            <h1 class="text-3xl font-display font-bold text-white">Create Patient Account</h1>
            <p class="text-slate-400 mt-1">Register to book appointments with our doctors</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl p-4 mb-6">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register_action.php" method="POST" class="space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="<?php echo $label_class; ?>">Full Name *</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($old['name']); ?>" class="<?php echo $input_class; ?>">
                </div>
                <div>
                    <label class="<?php echo $label_class; ?>">Email Address *</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($old['email']); ?>" class="<?php echo $input_class; ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="<?php echo $label_class; ?>">Password *</label>
                    <input type="password" name="password" required minlength="6" class="<?php echo $input_class; ?>">
                </div>
                <div>
                    <label class="<?php echo $label_class; ?>">Confirm Password *</label>
                    <input type="password" name="confirm_password" required minlength="6" class="<?php echo $input_class; ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="<?php echo $label_class; ?>">Gender *</label>
                    <select name="gender" required class="<?php echo $input_class; ?>">
                        <option value="" class="bg-slate-900">Select</option>
                        <option value="MALE" class="bg-slate-900" <?php echo $old['gender'] === 'MALE' ? 'selected' : ''; ?>>Male</option>
                        <option value="FEMALE" class="bg-slate-900" <?php echo $old['gender'] === 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                        <option value="OTHER" class="bg-slate-900" <?php echo $old['gender'] === 'OTHER' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div>
                    <label class="<?php echo $label_class; ?>">Date of Birth *</label>
                    <input type="date" name="dob" required value="<?php echo htmlspecialchars($old['dob']); ?>" max="<?php echo date('Y-m-d'); ?>" class="<?php echo $input_class; ?>">
                </div>
                <div>
                    <label class="<?php echo $label_class; ?>">Blood Group</label>
                    <select name="blood_group" class="<?php echo $input_class; ?>">
                        <option value="" class="bg-slate-900">Select</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                            <option value="<?php echo $bg; ?>" class="bg-slate-900" <?php echo $old['blood_group'] === $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="<?php echo $label_class; ?>">Mobile Number *</label>
                    <input type="tel" name="mobile" required pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($old['mobile']); ?>" placeholder="10-digit number" class="<?php echo $input_class; ?>">
                </div>
                <div>
                    <label class="<?php echo $label_class; ?>">Emergency Contact</label>
                    <input type="tel" name="emergency_contact" pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($old['emergency_contact']); ?>" placeholder="10-digit number" class="<?php echo $input_class; ?>">
                </div>
            </div>

            <div>
                <label class="<?php echo $label_class; ?>">Address</label>
                <textarea name="address" rows="2" class="<?php echo $input_class; ?>"><?php echo htmlspecialchars($old['address']); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="<?php echo $label_class; ?>">Medical History</label>
                    <textarea name="medical_history" rows="2" placeholder="Any past conditions/surgeries" class="<?php echo $input_class; ?>"><?php echo htmlspecialchars($old['medical_history']); ?></textarea>
                </div>
                <div>
                    <label class="<?php echo $label_class; ?>">Allergies</label>
                    <textarea name="allergies" rows="2" placeholder="Any known allergies" class="<?php echo $input_class; ?>"><?php echo htmlspecialchars($old['allergies']); ?></textarea>
                </div>
            </div>

            <button type="submit"
                class="btn-shine w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:shadow-lg hover:shadow-blue-900/50 text-white font-semibold py-3.5 rounded-xl transition-all">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-slate-400 mt-6">
            Already have an account?
            <a href="login.php" class="text-blue-400 font-medium hover:underline">Log in</a>
        </p>
    </div>

</div>

</body>
</html>