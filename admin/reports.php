<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

$by_status = $conn->query("SELECT status, COUNT(*) c FROM appointments GROUP BY status");
$by_department = $conn->query("
    SELECT dep.name, COUNT(a.id) total, SUM(CASE WHEN a.status='COMPLETED' THEN d.fee ELSE 0 END) revenue
    FROM departments dep
    LEFT JOIN doctors d ON d.department_id = dep.id
    LEFT JOIN appointments a ON a.doctor_id = d.user_id
    GROUP BY dep.id, dep.name
    ORDER BY total DESC
");
$total_revenue = $conn->query("
    SELECT SUM(d.fee) total FROM appointments a JOIN doctors d ON a.doctor_id = d.user_id WHERE a.status = 'COMPLETED'
")->fetch_assoc()['total'] ?? 0;

require_once '../includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Reports</h1>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
    <p class="text-sm text-slate-500 font-medium">Total Revenue (Completed Appointments)</p>
    <p class="text-3xl font-bold text-green-600 mt-2">₹<?php echo number_format((float)$total_revenue, 2); ?></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Appointments by Status</h2>
        <div class="space-y-3">
            <?php while ($r = $by_status->fetch_assoc()): ?>
                <div class="flex justify-between items-center bg-slate-50 rounded-lg p-3">
                    <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($r['status']); ?></span>
                    <span class="text-sm font-bold text-slate-800"><?php echo (int)$r['c']; ?></span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">By Department</h2>
        <div class="space-y-3">
            <?php while ($r = $by_department->fetch_assoc()): ?>
                <div class="flex justify-between items-center bg-slate-50 rounded-lg p-3">
                    <div>
                        <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($r['name']); ?></span>
                        <span class="text-xs text-slate-400 block"><?php echo (int)$r['total']; ?> appointment(s)</span>
                    </div>
                    <span class="text-sm font-bold text-green-600">₹<?php echo number_format((float)($r['revenue'] ?? 0), 2); ?></span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>