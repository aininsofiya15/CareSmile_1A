<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Appointments</title>
    <style>
        body {
            font-family: Arial;
            background: #eef2f7;
        }

        .container {
            width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        table {
            width: 100%;
        }

        th {
            background: #333;
            color: white;
        }

        th, td {
            padding: 10px;
        }

        .approve { background: green; color: white; }
        .reject { background: red; color: white; }
    </style>
</head>

<body>

<div class="container">
    <h2>Admin - Appointment Management</h2>

    <table>
        <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Time</th>
            <th>Service</th>
            <th>Reschedule</th>
            <th>Action</th>
        </tr>

        <tr>
            <td>John</td>
            <td>2026-04-01</td>
            <td>10:00</td>
            <td>Scaling</td>
            <td>Pending</td>
            <td>
                <button class="approve">Approve</button>
                <button class="reject">Reject</button>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
