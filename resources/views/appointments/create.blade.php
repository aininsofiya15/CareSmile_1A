<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f6f9;
        }

        .container {
            width: 400px;
            margin: 60px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Book Appointment</h2>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="/appointments/store">
        @csrf

        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Time:</label>
        <input type="time" name="time" required>

        <label>Service:</label>
        <select name="service">
            <option>Scaling</option>
            <option>Extraction</option>
            <option>Whitening</option>
        </select>

        <button type="submit">Book Appointment</button>
    </form>
</div>

</body>
</html>
