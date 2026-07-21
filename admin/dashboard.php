<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

$stats = [];
$stats['doctors'] = $conn->query("SELECT COUNT(*) c FROM doctors")->fetch_assoc()['c'];
$stats['patients'] = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$stats['departments'] = $conn->query("SELECT COUNT(*) c FROM departments")->fetch_assoc()['c'];
$stats['today_appts'] = $conn->query("SELECT COUNT(*) c FROM appointments WHERE appointment_date = CURDATE()")->fetch_assoc()['c'];
$stats['pending'] = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status = 'PENDING'")->fetch_assoc()['c'];
$stats['completed'] = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status = 'COMPLETED'")->fetch_assoc()['c'];

$recent = $conn->query("
    SELECT a.appointment_date, a.time_slot, a.status, p.name AS patient_name, d.name AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.user_id
    JOIN doctors d ON a.doctor_id = d.user_id
    ORDER BY a.created_at DESC LIMIT 8
");

require_once '../includes/header.php';
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Admin Dashboard</h1>
    <p class="text-slate-500 mt-1">Hospital-wide overview</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <p class="text-xs text-slate-500 font-medium">Doctors</p>
        <p class="text-2xl font-bold text-slate-800 mt-1"><?php echo $stats['doctors']; ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <p class="text-xs text-slate-500 font-medium">Patients</p>
        <p class="text-2xl font-bold text-slate-800 mt-1"><?php echo $stats['patients']; ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <p class="text-xs text-slate-500 font-medium">Departments</p>
        <p class="text-2xl font-bold text-slate-800 mt-1"><?php echo $stats['departments']; ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <p class="text-xs text-slate-500 font-medium">Today's Appts</p>
        <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo $stats['today_appts']; ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <p class="text-xs text-slate-500 font-medium">Pending</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1"><?php echo $stats['pending']; ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <p class="text-xs text-slate-500 font-medium">Completed</p>
        <p class="text-2xl font-bold text-green-600 mt-1"><?php echo $stats['completed']; ?></p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="manage_doctors.php" class="bg-blue-600 hover:bg-blue-700 text-white rounded-2xl p-6 font-semibold transition-colors">Manage Doctors &rarr;</a>
    <a href="manage_patients.php" class="bg-slate-800 hover:bg-slate-900 text-white rounded-2xl p-6 font-semibold transition-colors">Manage Patients &rarr;</a>
    <a href="manage_departments.php" class="bg-slate-600 hover:bg-slate-700 text-white rounded-2xl p-6 font-semibold transition-colors">Manage Departments &rarr;</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-4">Recent Appointments</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 border-b border-slate-200">
                    <th class="pb-2 font-medium">Patient</th>
                    <th class="pb-2 font-medium">Doctor</th>
                    <th class="pb-2 font-medium">Date</th>
                    <th class="pb-2 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $recent->fetch_assoc()): ?>
                <tr class="border-b border-slate-100">
                    <td class="py-2.5"><?php echo htmlspecialchars($r['patient_name']); ?></td>
                    <td class="py-2.5">Dr. <?php echo htmlspecialchars($r['doctor_name']); ?></td>
                    <td class="py-2.5"><?php echo date('d M Y', strtotime($r['appointment_date'])); ?></td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            <?php
                            echo match($r['status']) {
                                'CONFIRMED' => 'bg-green-100 text-green-700',
                                'PENDING' => 'bg-yellow-100 text-yellow-700',
                                'CANCELLED' => 'bg-red-100 text-red-700',
                                'COMPLETED' => 'bg-blue-100 text-blue-700',
                                default => 'bg-slate-100 text-slate-700'
                            };
                            ?>"><?php echo htmlspecialchars($r['status']); ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>