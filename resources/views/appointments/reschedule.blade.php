<!DOCTYPE html>
<html>
<head>
    <title>Reschedule Appointment</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f7fb;
        }

        .container {
            width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: orange;
            color: white;
            border: none;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Reschedule Appointment</h2>

    <form>
        <label>New Date</label>
        <input type="date">

        <label>New Time</label>
        <input type="time">

        <button>Submit Request</button>
    </form>
</div>

</body>
</html>
