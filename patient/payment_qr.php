<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

// ⚠️ Replace this with your own UPI ID (or a dummy one for demo safety)
$UPI_ID = "yourname@upi";
$PAYEE_NAME = "City Care Hospital";

$patient_id = $_SESSION['user_id'];
$appointment_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT a.*, d.name AS doctor_name FROM appointments a
    JOIN doctors d ON a.doctor_id = d.user_id
    WHERE a.id = ? AND a.patient_id = ? AND a.payment_method = 'UPI'
");
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();
$appt = $stmt->get_result()->fetch_assoc();

if (!$appt) {
    header("Location: my_appointments.php");
    exit();
}

$upi_uri = "upi://pay?pa=" . urlencode($UPI_ID) . "&pn=" . urlencode($PAYEE_NAME) . "&am=" . urlencode($appt['amount']) . "&cu=INR&tn=" . urlencode("Appointment #" . $appt['id']);
$qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=" . urlencode($upi_uri);

require_once '../includes/header.php';
?>

<div class="mb-6">
    <a href="my_appointments.php" class="text-sm text-blue-400 hover:underline">&larr; Back to Appointments</a>
</div>

<div class="dark-card rounded-2xl p-8 max-w-md mx-auto text-center">
    <h1 class="text-xl font-display font-bold text-white mb-1">Scan to Pay</h1>
    <p class="text-sm text-slate-400 mb-6">Dr. <?php echo htmlspecialchars($appt['doctor_name']); ?> — Appointment #<?php echo $appt['id']; ?></p>

    <div class="bg-white rounded-2xl p-4 inline-block mb-4">
        <img src="<?php echo $qr_image_url; ?>" alt="UPI QR Code" class="w-56 h-56">
    </div>

    <p class="text-3xl font-display font-bold text-white mb-1">₹<?php echo number_format((float)$appt['amount'], 2); ?></p>
    <p class="text-xs text-slate-500 mb-6">Pay to: <?php echo htmlspecialchars($UPI_ID); ?></p>

    <form action="payment_confirm.php" method="POST">
        <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
        <button type="submit" class="btn-shine w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold py-3 rounded-xl transition-all">
            ✓ I've Paid
        </button>
    </form>
    <p class="text-xs text-slate-500 mt-4">Click above only after completing the payment in your UPI app.</p>
</div>

<?php require_once '../includes/footer.php'; ?>