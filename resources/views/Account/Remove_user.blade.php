<!-- resources/views/delete-account.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .delete-form {
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 150px;
        }

        .btn-danger {
            width: 100%;
        }

        .footer-text {
            font-size: 0.9rem;
            color: #777;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="delete-form">
        <img src="{{ url('/images/logoo.png') }}" alt="Logo" class="logo">

        <h4 class="text-center mb-4">Delete Your Account</h4>

        <form method="POST" action="{{ route('account.delete') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="email" required>
                @error('email')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Confirm Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
                @error('password')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger">Delete My Account</button>
        </form>

        <div class="footer-text mt-3">
            <p>This action is permanent and cannot be undone.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
