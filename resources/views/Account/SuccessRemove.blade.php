<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deleted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .success-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
        }

        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .btn-home {
            margin-top: 25px;
        }

        .logo {
            max-width: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="success-box">
        <!-- Optional Logo -->
        <img src="{{ url('/images/logoo.png') }}" alt="Logo" class="logo">

        <div class="success-icon">
            ✅
        </div>
        <h2>Account Deleted</h2>
        <p class="text-muted">Your account has been successfully deleted. We're sorry to see you go.</p>

        {{-- <a href="{{ url('/') }}" class="btn btn-outline-primary btn-home">Go to Homepage</a> --}}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
