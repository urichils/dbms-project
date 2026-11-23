<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use the manually typed Faculty ID if entered; otherwise use the dropdown selection
    $raw_faculty_id = !empty(trim($_POST['faculty_id_manual'])) ? trim($_POST['faculty_id_manual']) : $_POST['faculty_id'];

    $faculty_id = mysqli_real_escape_string($conn, $raw_faculty_id);
    $lab_id = mysqli_real_escape_string($conn, $_POST['lab_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);

    if (empty($faculty_id)) {
        $message = "<p style='color: red;'>Error: Please select a faculty member or enter a Faculty ID!</p>";
    } else {
        // Verify that the Faculty ID exists in the faculty table
        $fac_check = mysqli_query($conn, "select faculty_id from faculty where faculty_id = '$faculty_id'");

        if (mysqli_num_rows($fac_check) == 0) {
            $message = "<p style='color: red;'>Error: Faculty ID '$faculty_id' does not exist!</p>";
        } else {
            $check_sql = "select booking_id from lab_booking 
                          where lab_id = '$lab_id' 
                          and booking_date = '$booking_date' 
                          and time_slot = '$time_slot'";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                $message = "<p style='color: red;'>Error: This lab is already booked for that date and time slot!</p>";
            } else {
                $sql = "insert into lab_booking (faculty_id, lab_id, booking_date, time_slot) 
                        values ('$faculty_id', '$lab_id', '$booking_date', '$time_slot')";

                if (mysqli_query($conn, $sql)) {
                    $message = "<p style='color: green;'>Booking saved successfully!</p>";
                } else {
                    $message = "<p style='color: red;'>Error saving record: " . mysqli_error($conn) . "</p>";
                }
            }
        }
    }
}

$faculties = mysqli_query($conn, "select * from faculty");
$faculty_list = [];
while ($row = mysqli_fetch_assoc($faculties)) {
    $faculty_list[] = $row;
}

$labs = mysqli_query($conn, "select * from lab");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Computer Lab</title>
</head>
<body>
    <h2>Computer Lab Booking Form</h2>
    
    <?php echo $message; ?>

    <form action="labbook.php" method="POST">
        <p>
            <label>Faculty Member:</label><br>
            <select name="faculty_id" id="faculty_select">
                <option value="">-- Select Faculty --</option>
                <?php foreach ($faculty_list as $f): ?>
                    <option value="<?php echo $f['faculty_id']; ?>">
                        <?php echo htmlspecialchars($f['name'] . " (" . $f['department'] . ")"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span> OR </span>
            <input type="number" name="faculty_id_manual" id="faculty_manual" placeholder="Enter Faculty ID">
        </p>

        <div id="faculty-info" style="display: none; margin-top: -8px; margin-bottom: 14px; padding: 8px 12px; background: #f4f6f8; border-left: 4px solid #2b6cb0; max-width: 420px; font-size: 14px;"></div>

        <p>
            <label>Lab:</label><br>
            <select name="lab_id" required>
                <option value="">-- Select Lab --</option>
                <?php while ($l = mysqli_fetch_assoc($labs)): ?>
                    <option value="<?php echo $l['lab_id']; ?>">
                        <?php echo htmlspecialchars($l['lab_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </p>

        <p>
            <label>Booking Date:</label><br>
            <input type="date" name="booking_date" required>
        </p>

        <p>
            <label>Time Slot:</label><br>
            <select name="time_slot" required>
                <option value="">-- Select Time Slot --</option>
                <option value="09:00 AM - 11:00 AM">09:00 AM - 11:00 AM</option>
                <option value="11:00 AM - 01:00 PM">11:00 AM - 01:00 PM</option>
                <option value="02:00 PM - 04:00 PM">02:00 PM - 04:00 PM</option>
                <option value="04:00 PM - 05:00 PM">04:00 PM - 05:00 PM</option>
            </select>
        </p>

        <button type="submit">Submit Booking</button>
    </form>

    <br>
    <a href="report.php">View Booking Report (Page 2)</a>

    <script>
        const facultyRecords = <?php echo json_encode($faculty_list); ?>;
        const facultySelect = document.getElementById('faculty_select');
        const facultyManual = document.getElementById('faculty_manual');
        const facultyInfo = document.getElementById('faculty-info');

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function showFacultyDetails(id) {
            const cleanId = String(id).trim();
            if (!cleanId) {
                facultyInfo.style.display = 'none';
                facultyInfo.innerHTML = '';
                return;
            }

            const match = facultyRecords.find(f => String(f.faculty_id) === cleanId);

            if (match) {
                facultyInfo.style.display = 'block';
                facultyInfo.style.borderLeftColor = '#2b6cb0';
                facultyInfo.style.background = '#f4f6f8';
                facultyInfo.style.color = '#1a202c';
                facultyInfo.innerHTML = `
                    <strong>Faculty Record (ID: ${escapeHtml(match.faculty_id)})</strong><br>
                    Name: ${escapeHtml(match.name)}<br>
                    Department: ${escapeHtml(match.department)}<br>
                    Email: ${escapeHtml(match.email)}
                `;
            } else {
                facultyInfo.style.display = 'block';
                facultyInfo.style.borderLeftColor = '#c53030';
                facultyInfo.style.background = '#fff5f5';
                facultyInfo.style.color = '#c53030';
                facultyInfo.innerHTML = `No faculty record found for ID <strong>${escapeHtml(cleanId)}</strong>.`;
            }
        }

        facultySelect.addEventListener('change', function () {
            if (this.value !== '') {
                facultyManual.value = '';
            }
            showFacultyDetails(this.value);
        });

        facultyManual.addEventListener('input', function () {
            if (this.value.trim() !== '') {
                facultySelect.value = '';
            }
            showFacultyDetails(this.value);
        });
    </script>
</body>
</html>