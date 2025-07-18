<?php
session_start();

$error = '';
$success = '';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($password)) {
        // ここでエラーメッセージがエスケープされない
        $error = 'ユーザー名とパスワードを入力してください。<script>alert(\'XSS\');</script>'; 
    } else {
        try {
            $db = new PDO('sqlite:/var/www/html/secure/user.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $db->prepare("SELECT * FROM user WHERE name = :name");
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header('Location: index.php');
                exit;
            } else {
                // ここでエラーメッセージがエスケープされない
                $error = 'ユーザー名またはパスワードが正しくありません。<script>alert(\'XSS\');</script>';
            }
        } catch (PDOException $e) {
            // ここでエラーメッセージがエスケープされない
            $error = 'データベースエラー: ' . $e->getMessage() . '<script>alert(\'XSS\');</script>';
        }
    }
}

// 既にログインしている場合はリダイレクト
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - FoodDelivery</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #28a745, #20c997);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #28a745;
        }

        .login-title {
            color: #333;
            margin-bottom: 30px;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #28a745;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 2px solid #e9ecef;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            font-size: 0.9rem;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            font-size: 0.9rem;
        }

        .form-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .form-footer p {
            color: #6c757d;
            margin-bottom: 15px;
        }

        .form-footer a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .logo {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🍕</div>
        <h1 class="login-title">FoodDeliveryにログイン</h1>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= $error // XSS脆弱性: htmlspecialcharsを削除 ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?= $success // XSS脆弱性: htmlspecialcharsを削除 ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name" class="form-label">ユーザー名</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input" 
                    value="<?= $_POST['name'] ?? '' // XSS脆弱性: htmlspecialcharsを削除 ?>"
                    placeholder="ユーザー名を入力"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="パスワードを入力"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">ログイン</button>
        </form>

        <a href="index.php" class="btn btn-secondary">ホームに戻る</a>

        <div class="form-footer">
            <p>アカウントをお持ちでない方</p>
            <a href="user-register.php">新規会員登録はこちら</a>
        </div>
    </div>
</body>
</html>