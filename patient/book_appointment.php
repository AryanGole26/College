<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';
require_once '../includes/dept_theme.php';

$department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 0;
$doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;

$departments = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");

$doctors = null;
if ($department_id > 0) {
    $stmt = $conn->prepare("SELECT user_id, name, qualification, experience, fee FROM doctors WHERE department_id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $doctors = $stmt->get_result();
}

$selected_doctor = null;
if ($doctor_id > 0) {
    $stmt = $conn->prepare("
        SELECT d.user_id, d.name, d.qualification, d.experience, d.fee, d.available_days, d.available_time_slots, dep.name AS dept_name
        FROM doctors d JOIN departments dep ON d.department_id = dep.id
        WHERE d.user_id = ?
    ");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $selected_doctor = $stmt->get_result()->fetch_assoc();
}

$current_dept_name = '';
if ($department_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $stmt->bind_result($current_dept_name);
    $stmt->fetch();
    $stmt->close();
}
$theme = dept_theme($current_dept_name ?: 'general');

$error = $_SESSION['booking_error'] ?? '';
unset($_SESSION['booking_error']);

require_once '../includes/header.php';
?>

<div class="mb-8" data-aos="fade-up">
    <h1 class="text-3xl font-display font-bold text-slate-800">Book an Appointment</h1>
    <p class="text-slate-500 mt-1">Search by department, choose a doctor, then pick a slot</p>
</div>

<?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6 text-sm" data-aos="fade-up"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Step 1: Department selection -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6" data-aos="fade-up">
    <h2 class="font-display font-semibold text-slate-800 mb-4">1. Select Department</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <?php while ($dep = $departments->fetch_assoc()): $dt = dept_theme($dep['name']); ?>
            <a href="?department_id=<?php echo $dep['id']; ?>"
               class="hover-lift rounded-2xl p-4 text-center border transition-all
                      <?php echo $department_id == $dep['id'] ? 'bg-gradient-to-br ' . $dt['gradient'] . ' text-white border-transparent shadow-lg' : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300'; ?>">
                <div class="text-2xl mb-1"><?php echo $dt['icon']; ?></div>
                <p class="text-xs font-semibold"><?php echo htmlspecialchars($dep['name']); ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<!-- Step 2: Doctor selection -->
<?php if ($doctors): ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6" data-aos="fade-up">
    <h2 class="font-display font-semibold text-slate-800 mb-4">2. Select Doctor</h2>
    <?php if ($doctors->num_rows === 0): ?>
        <p class="text-sm text-slate-500">No doctors currently available in this department.</p>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php while ($doc = $doctors->fetch_assoc()): ?>
            <a href="?department_id=<?php echo $department_id; ?>&doctor_id=<?php echo $doc['user_id']; ?>"
               class="hover-lift block p-5 rounded-2xl border transition-all relative overflow-hidden
                      <?php echo $doctor_id == $doc['user_id'] ? 'ring-2 ' . $theme['ring'] . ' ' . $theme['bg'] . ' border-transparent' : 'border-slate-200 bg-white'; ?>">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br <?php echo $theme['gradient']; ?> flex items-center justify-center text-lg text-white shadow-md shrink-0">
                        <?php echo $theme['icon']; ?>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Dr. <?php echo htmlspecialchars($doc['name']); ?></p>
                        <p class="text-sm text-slate-500"><?php echo htmlspecialchars($doc['qualification']); ?> &middot; <?php echo (int)$doc['experience']; ?> yrs exp</p>
                        <p class="text-sm <?php echo $theme['text']; ?> font-semibold mt-1">₹<?php echo number_format((float)$doc['fee'], 2); ?> consultation</p>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>
<?php endif; ?>

<!-- Step 3: Booking form -->
<?php if ($selected_doctor): ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6" data-aos="fade-up">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br <?php echo $theme['gradient']; ?> flex items-center justify-center text-white shadow-md">
            <?php echo $theme['icon']; ?>
        </div>
        <h2 class="font-display font-semibold text-slate-800">3. Confirm Appointment Details</h2>
    </div>
    <p class="text-sm text-slate-500 mb-4 ml-13">
        Dr. <?php echo htmlspecialchars($selected_doctor['name']); ?> — <?php echo htmlspecialchars($selected_doctor['dept_name']); ?><br>
        Available days: <span class="font-medium"><?php echo htmlspecialchars($selected_doctor['available_days']); ?></span>
    </p>

    <form action="book_appointment_action.php" method="POST" enctype="multipart/form-data" class="space-y-5">
        <input type="hidden" name="doctor_id" value="<?php echo (int)$selected_doctor['user_id']; ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Appointment Date *</label>
                <input type="date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Time Slot *</label>
                <select name="time_slot" required
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Select a slot</option>
                    <?php foreach (explode(',', $selected_doctor['available_time_slots']) as $slot): ?>
                        <option value="<?php echo htmlspecialchars(trim($slot)); ?>"><?php echo htmlspecialchars(trim($slot)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Reason for Visit *</label>
            <textarea name="reason" rows="3" required placeholder="Briefly describe your symptoms or reason for visit"
                class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Upload Report (optional)</label>
            <input type="file" name="report" accept=".pdf,.jpg,.jpeg,.png"
                class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
            <p class="text-xs text-slate-400 mt-1">PDF or image, max 10MB</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Payment Method *</label>
            <div class="grid grid-cols-3 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="CASH" required class="hidden peer">
                    <div class="border-2 border-slate-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl p-4 text-center transition-colors">
                        <div class="text-2xl mb-1">💵</div>
                        <p class="text-sm font-medium text-slate-700">Cash</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="CARD" required class="hidden peer">
                    <div class="border-2 border-slate-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl p-4 text-center transition-colors">
                        <div class="text-2xl mb-1">💳</div>
                        <p class="text-sm font-medium text-slate-700">Card</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="UPI" required class="hidden peer">
                    <div class="border-2 border-slate-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl p-4 text-center transition-colors">
                        <div class="text-2xl mb-1">📱</div>
                        <p class="text-sm font-medium text-slate-700">UPI / QR</p>
                    </div>
                </label>
            </div>
            <p class="text-xs text-slate-400 mt-2">Cash and Card are paid at the hospital counter. UPI can be paid now via QR code.</p>
        </div>

        <button type="submit" class="btn-shine w-full bg-gradient-to-r <?php echo $theme['gradient']; ?> hover:shadow-lg text-white font-semibold py-3.5 rounded-xl transition-all">
            Confirm Booking
        </button>
    </form>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>