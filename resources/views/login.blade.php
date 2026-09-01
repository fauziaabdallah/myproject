<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fuel Vouchar Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            background-image: url("{{ asset('images/pic1.jpg') }}");
            
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }

        .login-card {
            width: 380px;
            background: #fff;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .login-title {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: #2c5364;
        }

        .input-group-text {
            background: #fff;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .toggle-password {
            cursor: pointer;
        }

        .btn-login {
            background: #2c5364;
            color: #fff;
            border-radius: 10px;
            padding: 10px;
        }

        .btn-login:hover {
            background: #203a43;
            color: white;
            font-weight: bolder;
        }

        /* RESPONSIVE */
        @media(max-width: 500px) {
            .login-card {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-card">
   
    
    <h4 class="login-title text-center" style="font-size: 19px">
        Fuel Vouchar Management System
    </h4>

    <p class="text-center text-muted mb-3" style="font-size: 14px;">
        Enter your credentials to login
    </p>

    <form action="https://fvmsproject.up.railway.app/login" method="post">
       @csrf
        <!-- Email -->
        <div class="mb-3 input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
        </div>

        <!-- Password -->
        <div class="mb-2 input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            <span class="input-group-text toggle-password" onclick="togglePassword()">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </span>
        </div>

        <!-- Forgot password -->
        <div class="text-end mb-3">
            <a href="{{ route('forgot') }}" style="font-size: 13px; text-decoration: none;">
                Forgot password?
            </a>
        </div>

        <!-- Button -->
        <button type="submit" class="btn btn-login w-100">
            Login
        </button>
        @if(session("error"))
            <span style="color: red">{{ session("error") }}</span>
        @endif

    </form>

</div>

<script>
function togglePassword() {
    let password = document.getElementById("password");
    let icon = document.getElementById("eyeIcon");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        password.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>

</body>
</html>