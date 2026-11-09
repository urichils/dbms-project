<?php
include 'db.php';

$query = "select f.name as faculty_name, f.department, l.lab_name, b.booking_date, b.time_slot 
          from lab_booking b
          join faculty f on b.faculty_id = f.faculty_id
          join lab l on b.lab_id = l.lab_id";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lab Booking Report</title>
</head>
<body>
    <h2>Computer Lab Booking Report</h2>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Faculty Name</th>
                <th>Department</th>
                <th>Lab Name</th>
                <th>Booking Date</th>
                <th>Time Slot</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['faculty_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No lab bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="labbook.php">Back to Booking Form (Page 1)</a>
</body>
</html>