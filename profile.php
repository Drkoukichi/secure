<?php
session_start();

// ログインチェック
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user = null;
$error = "";

try {
    $db = new PDO("sqlite:/var/www/html/secure/user.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
    $stmt->bindParam(":id", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "データベースエラー: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員情報 - FoodDelivery</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .main-content {
            padding: 40px 0;
        }

        .profile-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-avatar {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #28a745;
        }

        .profile-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .profile-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .profile-info {
            display: grid;
            gap: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 4px solid #28a745;
        }

        .info-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            color: #28a745;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 1.1rem;
        }

        .empty-value {
            color: #999;
            font-style: italic;
        }

        .action-buttons {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-block;
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
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 15px;
            }

            .profile-container {
                margin: 20px;
                padding: 30px 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">🍕 FoodDelivery</a>
                <div class="nav-links">
                    <a href="search.php" class="nav-link">料理検索</a>
                    <a href="index.php" class="nav-link">ホーム</a>
                    <a href="logout.php" class="nav-link">ログアウト</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="profile-container">
                <?php if ($error): ?>
                    <div class="error-message">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($user): ?>
                    <div class="profile-header">
                        <div class="profile-avatar">👤</div>
                        <h1 class="profile-title">会員情報</h1>
                        <p class="profile-subtitle">ようこそ、<?= htmlspecialchars($user["name"]) ?>さん</p>
                    </div>

                    <div class="profile-info">
                        <div class="info-item">
                            <div class="info-icon">👤</div>
                            <div class="info-content">
                                <div class="info-label">ユーザー名</div>
                                <div class="info-value"><?= htmlspecialchars($user["name"]) ?></div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">🏠</div>
                            <div class="info-content">
                                <div class="info-label">住所</div>
                                <div class="info-value <?= empty($user["address"]) ? "empty-value" : "" ?>">
                                    <?= !empty($user["address"]) ? htmlspecialchars($user["address"]) : "未設定" ?>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">📞</div>
                            <div class="info-content">
                                <div class="info-label">電話番号</div>
                                <div class="info-value <?= empty($user["phone"]) ? "empty-value" : "" ?>">
                                    <?= !empty($user["phone"]) ? htmlspecialchars($user["phone"]) : "未設定" ?>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">⭐</div>
                            <div class="info-content">
                                <div class="info-label">会員ステータス</div>
                                <div class="info-value">一般会員</div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="index.php" class="btn btn-primary">ホームに戻る</a>
                        <a href="search.php" class="btn btn-secondary">料理を検索</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
