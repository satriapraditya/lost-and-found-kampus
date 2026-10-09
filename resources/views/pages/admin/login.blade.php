<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin - Lost & Found Kampus</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo h1 {
            color: #2563eb;
            margin-bottom: 8px;
        }

        .logo p {
            color: #6b7280;
            font-size: 14px;
        }

        .admin-label {
            text-align: center;
            margin-bottom: 25px;
        }

        .admin-label span {
            background: #eff6ff;
            color: #2563eb;
            padding: 7px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="logo">
            <h1>Lost & Found</h1>
            <p>Portal Kehilangan & Penemuan Barang Kampus</p>
        </div>

        <div class="admin-label">
            <span>LOGIN ADMIN</span>
        </div>

        <form>

            <div class="form-group">
                <label for="email">Email Admin</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email admin"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                >
            </div>

            <button type="submit">
                Masuk sebagai Admin
            </button>

        </form>

        <div class="back-link">
            <a href="/login">← Kembali ke Login User</a>
        </div>

    </div>

</body>
</html>