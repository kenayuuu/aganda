<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login AGANDA</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
        }

        h1 {
            margin-top: 0;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #c60c00;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #c60c00;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background: #a90900;
        }

        .error {
            color: #c60c00;
            background: #ffe9e7;
            border: 1px solid #f5b5b0;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .remember {
            margin-bottom: 18px;
        }

        .remember label {
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember input {
            width: auto;
        }

        .forgot-password {
            display: block;
            margin-top: 10px;
            margin-bottom: 18px;
            text-align: right;
            color: #c60c00;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .hint {
            margin-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <h1>AGANDA</h1>

        <p class="subtitle">
            Silakan masuk ke akun Anda
        </p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">

            @csrf

            <div class="form-group">

                <label for="login">
                    Email / Member ID
                </label>

                <input type="text" name="login" id="login" value="{{ old('login') }}"
                    placeholder="Masukkan email atau Member ID" autocomplete="username" required autofocus>

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input type="password" name="password" id="password" placeholder="Masukkan password"
                    autocomplete="current-password" required>

            </div>

            <div class="remember">

                <label>

                    <input type="checkbox" name="remember" value="1">

                    <span>
                        Ingat saya
                    </span>

                </label>

            </div>

            <a href="{{ route('password.request') }}" class="forgot-password">
                Lupa Password?
            </a>

            <button type="submit">
                Login
            </button>

        </form>

        <div class="hint">
            Gunakan email atau Member ID untuk masuk.
        </div>

    </div>

</body>

</html>
