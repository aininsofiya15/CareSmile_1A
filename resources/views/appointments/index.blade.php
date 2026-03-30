<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f7fb;
        }

        .container {
            width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }

        .reschedule { background: orange; color: white; }
        .cancel { background: red; color: white; }
    </style>
</head>

<body>

<div class="container">
    <h2>My Appointments</h2>

    <table>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Service</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <tr>
            <td>2026-02-19</td>
            <td>10:00 AM</td>
            <td>Scaling</td>
            <td>Completed</td>
            <td>
                <button class="btn reschedule">Reschedule</button>
                <button class="btn cancel">Cancel</button>
            </td>
        </tr>

        <tr>
            <td>2026-04-03</td>
            <td>11:00 AM</td>
            <td>Whitening</td>
            <td>Confirmed</td>
            <td>
                <button class="btn reschedule">Reschedule</button>
                <button class="btn cancel">Cancel</button>
            </td>
        </tr>

        <tr>
            <td>2026-04-05</td>
            <td>2:00 PM</td>
            <td>Extraction</td>
            <td>Confirmed</td>
            <td>
                <button class="btn reschedule">Reschedule</button>
                <button class="btn cancel">Cancel</button>
            </td>
        </tr>

    </table>
</div>

</body>
</html>
