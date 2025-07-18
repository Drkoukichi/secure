<?php
session_start();

$error = '';
$success = '';

// 新規登録処理（演習用なのでエスケープなし・簡易チェック）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (trim($name) === '' || trim($password) === '') {
        $error = 'ユーザー名とパスワードは必須です。';
    } elseif (mb_strlen($password) < 6) {
        $error = 'パスワードは6文字以上で入力してください。';
    } elseif ($password !== $password_confirm) {
        $error = 'パスワードが一致しません。';
    } else {
        try {
            $db = new PDO('sqlite:/var/www/html/secure/user.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQLite用簡易エスケープ（演習用）
            $name_esc = str_replace("'", "''", $name);
            $address_esc = str_replace("'", "''", $address);
            $phone_esc = str_replace("'", "''", $phone);
            $password_esc = str_replace("'", "''", $password);

            // ユーザー名重複チェック
            $sql = "SELECT COUNT(*) FROM user WHERE name = '$name_esc'";
            $count = $db->query($sql)->fetchColumn();

            if ($count > 0) {
                $error = 'このユーザー名は既に使用されています。';
            } else {
                $sql = "INSERT INTO user (name, address, phone, password) VALUES ('$name_esc', '$address_esc', '$phone_esc', '$password_esc')";
                $db->exec($sql);
                $success = 'アカウントが正常に作成されました。ログインページに移動します。';
                header('refresh:2;url=login.php');
            }
        } catch (PDOException $e) {
            $error = 'データベースエラー: ' . $e->getMessage();
        }
    }
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>新規会員登録 - FoodDelivery</title>
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

.register-container {
  background: white;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 450px;
  text-align: center;
}

.logo {
  font-size: 2.5rem;
  margin-bottom: 10px;
  color: #28a745;
}

.register-title {
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

.form-label .required {
  color: #dc3545;
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
  text-decoration: none;
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

.password-requirements {
  font-size: 0.85rem;
  color: #6c757d;
  margin-top: 5px;
}

.input-display {
  margin-top: 20px;
  padding: 15px;
  background-color: #fff3f3;
  border: 1px solid #f5c6cb;
  border-radius: 8px;
  color: #721c24;
  font-size: 1rem;
  word-break: break-all;
}

@media (max-width: 480px) {
  .register-container {
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
<div class="register-container">
  <div class="logo">🍕</div>
  <h1 class="register-title">FoodDeliveryに新規登録</h1>

  <?php if (!empty($error)): ?>
    <div class="error-message">
      <?= $error // 演習用にhtmlspecialcharsなし ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="success-message">
      <?= $success // 演習用にhtmlspecialcharsなし ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="form-group">
      <label for="name" class="form-label">ユーザー名 <span class="required">*</span></label>
      <input
        type="text"
        id="name"
        name="name"
        class="form-input"
        placeholder="ユーザー名を入力"
        required
        value="<?= $_POST['name'] ?? '' ?>"
      >
    </div>

    <div class="form-group">
      <label for="address" class="form-label">住所</label>
      <input
        type="text"
        id="address"
        name="address"
        class="form-input"
        value="<?= $_POST['address'] ?? '' ?>"
        placeholder="配達先住所を入力（任意）"
      >
    </div>

    <div class="form-group">
      <label for="phone" class="form-label">電話番号</label>
      <input
        type="tel"
        id="phone"
        name="phone"
        class="form-input"
        value="<?= $_POST['phone'] ?? '' ?>"
        placeholder="電話番号を入力（任意）"
      >
    </div>

    <div class="form-group">
      <label for="password" class="form-label">パスワード <span class="required">*</span></label>
      <input
        type="password"
        id="password"
        name="password"
        class="form-input"
        placeholder="パスワードを入力"
        required
      >
      <div class="password-requirements">
        ※ 6文字以上で入力してください
      </div>
    </div>

    <div class="form-group">
      <label for="password_confirm" class="form-label">パスワード確認 <span class="required">*</span></label>
      <input
        type="password"
        id="password_confirm"
        name="password_confirm"
        class="form-input"
        placeholder="パスワードを再入力"
        required
      >
    </div>

    <button type="submit" class="btn btn-primary">アカウントを作成</button>
  </form>

  <a href="index.php" class="btn btn-secondary">ホームに戻る</a>

  <div class="form-footer">
    <p>既にアカウントをお持ちの方</p>
    <a href="login.php">ログインはこちら</a>
  </div>

  <!-- 入力値をそのまま表示してXSS演習用 -->
  <div style="display:none">
  <?= $_POST['name'] ?? '' ?>
  <?= $_POST['address'] ?? '' ?>
  <?= $_POST['phone'] ?? '' ?>
  </div>
</div>
</body>
</html>