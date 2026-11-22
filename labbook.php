<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = mysqli_real_escape_string($conn, $_POST['faculty_id']);
    $lab_id = mysqli_real_escape_string($conn, $_POST['lab_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);

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

$faculties = mysqli_query($conn, "select * from faculty");
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
            <select name="faculty_id" required>
                <option value="">-- Select Faculty --</option>
                <?php while ($f = mysqli_fetch_assoc($faculties)): ?>
                    <option value="<?php echo $f['faculty_id']; ?>">
                        <?php echo htmlspecialchars($f['name'] . " (" . $f['department'] . ")"); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </p>

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
</body>
</html>