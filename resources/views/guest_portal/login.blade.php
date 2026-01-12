<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { max-width: 400px; width: 100%; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="card login-card p-4">
    <div class="text-center mb-4">
        <h4 class="fw-bold">Client Portal</h4>
        <p class="text-muted small">Please enter your registered Mobile Number to access your details.</p>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('guest.verify') }}" method="POST">
        @csrf
        <input type="hidden" name="lead_id" value="{{ $lead->id }}">

        <div class="mb-3">
            <label class="form-label fw-bold">Mobile Number / Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your mobile number" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Access My Details</button>
    </form>
</div>

</body>
</html>