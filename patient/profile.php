<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

$patient_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT p.*, u.email FROM patients p JOIN users u ON p.user_id = u.id WHERE p.user_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

require_once '../includes/header.php';
?>

<div class="mb-6" data-aos="fade-up">
    <h1 class="text-3xl font-display font-bold text-slate-800">My Profile</h1>
</div>

<?php if ($flash_success): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash_success); ?></div>
<?php endif; ?>
<?php if ($flash_error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash_error); ?></div>
<?php endif; ?>

<div class="hover-lift bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-3xl" data-aos="fade-up">
    <div class="flex items-center gap-4 mb-6">
        <img src="<?php echo $profile['profile_photo'] ? '../uploads/profile_photos/' . htmlspecialchars($profile['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($profile['name']) . '&background=2563eb&color=fff'; ?>"
             class="w-16 h-16 rounded-full object-cover border border-slate-200">
        <div>
            <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($profile['name']); ?></p>
            <p class="text-sm text-slate-500"><?php echo htmlspecialchars($profile['email']); ?></p>
        </div>
    </div>

    <form action="profile_update.php" method="POST" enctype="multipart/form-data" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Profile Photo</label>
            <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png"
                class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($profile['name']); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mobile Number *</label>
                <input type="tel" name="mobile" required pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($profile['mobile']); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Emergency Contact</label>
                <input type="tel" name="emergency_contact" pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($profile['emergency_contact'] ?? ''); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Blood Group</label>
                <select name="blood_group" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Select</option>
                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                        <option value="<?php echo $bg; ?>" <?php echo $profile['blood_group'] === $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
            <textarea name="address" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Medical History</label>
                <textarea name="medical_history" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"><?php echo htmlspecialchars($profile['medical_history'] ?? ''); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Allergies</label>
                <textarea name="allergies" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"><?php echo htmlspecialchars($profile['allergies'] ?? ''); ?></textarea>
            </div>
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
            Save Changes
        </button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>