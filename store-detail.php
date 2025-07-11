<?php
session_start();

$food = null;
$error = '';
$reviews = [];
$success = '';

// レビュー投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $error = 'レビューを投稿するにはログインが必要です。';
    } else {
        $content = trim($_POST['content'] ?? '');
        $rating = (int)($_POST['rating'] ?? 0);
        $food_id = (int)($_POST['food_id'] ?? 0);
        
        if (empty($content)) {
            $error = 'レビュー内容を入力してください。';
        } elseif ($rating < 1 || $rating > 5) {
            $error = '評価は1〜5の範囲で選択してください。';
        } else {
            try {
                $db = new PDO('sqlite:/var/www/html/secure/store-info.db');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $db->prepare("INSERT INTO review (food_id, user_name, content, rating) VALUES (:food_id, :user_name, :content, :rating)");
                $stmt->bindParam(':food_id', $food_id);
                $stmt->bindParam(':user_name', $_SESSION['user_name']);
                $stmt->bindParam(':content', $content);
                $stmt->bindParam(':rating', $rating);
                
                if ($stmt->execute()) {
                    $success = 'レビューが投稿されました！';
                } else {
                    $error = 'レビューの投稿に失敗しました。';
                }
            } catch (PDOException $e) {
                $error = 'データベースエラー: ' . $e->getMessage();
            }
        }
    }
}

// 料理IDを取得
$food_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($food_id > 0) {
    try {
        $db = new PDO('sqlite:/var/www/html/secure/store-info.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("SELECT * FROM food WHERE ID = :id");
        $stmt->bindParam(':id', $food_id, PDO::PARAM_INT);
        $stmt->execute();
        $food = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$food) {
            $error = '指定された料理が見つかりません。';
        } else {
            // レビューを取得
            $reviewStmt = $db->prepare("SELECT * FROM review WHERE food_id = :food_id ORDER BY created_at DESC");
            $reviewStmt->bindParam(':food_id', $food_id);
            $reviewStmt->execute();
            $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error = 'データベースエラー: ' . $e->getMessage();
    }
} else {
    $error = '無効な料理IDです。';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $food ? htmlspecialchars($food['name']) . ' - ' : '' ?>FoodDelivery</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .food-detail {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .food-image-container {
            position: relative;
            height: 400px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .food-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .food-image-placeholder {
            font-size: 6rem;
            color: #999;
        }

        .food-info {
            padding: 40px;
        }

        .food-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .food-description {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .food-actions {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .restaurant-info {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .restaurant-info h3 {
            color: #28a745;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-icon {
            font-size: 1.2rem;
            color: #28a745;
        }

        .error-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .error-message {
            color: #721c24;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .reviews-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .reviews-header {
            color: #28a745;
            margin-bottom: 25px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .review-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 2px solid #e9ecef;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        .form-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
            transition: border-color 0.3s ease;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #28a745;
        }

        .rating-group {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .rating-star {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .rating-star:hover,
        .rating-star.active {
            color: #ffc107;
        }

        .review-item {
            border-bottom: 1px solid #e9ecef;
            padding: 20px 0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .reviewer-name {
            font-weight: 600;
            color: #333;
        }

        .review-date {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .review-rating {
            display: flex;
            gap: 2px;
            margin-bottom: 10px;
        }

        .review-content {
            color: #555;
            line-height: 1.6;
        }

        .no-reviews {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 15px;
            }

            .food-title {
                font-size: 2rem;
            }

            .food-info {
                padding: 30px 20px;
            }

            .food-actions {
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="nav-link">こんにちは、<?= htmlspecialchars($_SESSION['user_name']) ?>さん</span>
                        <a href="profile.php" class="nav-link">会員情報</a>
                        <a href="logout.php" class="nav-link">ログアウト</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">ログイン</a>
                        <a href="user-register.php" class="nav-link">新規会員登録</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-container">
                    <div class="error-icon">⚠️</div>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                    <a href="index.php" class="btn btn-primary">ホームに戻る</a>
                </div>
            <?php elseif ($food): ?>
                <div class="food-detail">
                    <div class="food-image-container">
                        <?php if (!empty($food['image_url'])): ?>
                            <img src="<?= htmlspecialchars($food['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($food['name']) ?>"
                                 class="food-image"
                                 onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\'food-image-placeholder\'>🍽️</div>';">
                        <?php else: ?>
                            <div class="food-image-placeholder">🍽️</div>
                        <?php endif; ?>
                    </div>
                    <div class="food-info">
                        <h1 class="food-title"><?= htmlspecialchars($food['name']) ?></h1>
                        <p class="food-description">
                            美味しい<?= htmlspecialchars($food['name']) ?>をお楽しみください。
                            厳選された食材を使用し、熟練のシェフが心を込めて調理いたします。
                            ご注文いただいてから新鮮な状態でお届けします。
                        </p>
                        <div class="food-actions">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-primary" onclick="orderFood(<?= $food['ID'] ?>)">
                                    🛒 注文する
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary">
                                    ログインして注文
                                </a>
                            <?php endif; ?>
                            <a href="search.php" class="btn btn-secondary">
                                他の料理を見る
                            </a>
                        </div>
                    </div>
                </div>

                <div class="restaurant-info">
                    <h3>🏪 レストラン情報</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-icon">★</span>
                            <span>評価: 4.8/5.0</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">🚚</span>
                            <span>配達時間: 25-35分</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">💰</span>
                            <span>配送料: 無料</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">📞</span>
                            <span>電話: 03-1234-5678</span>
                        </div>
                    </div>
                </div>

                <!-- レビューセクション -->
                <div class="reviews-section">
                    <h3 class="reviews-header">
                        <span>💬</span>
                        <span>ユーザーレビュー (<?= count($reviews) ?>件)</span>
                    </h3>

                    <!-- レビュー投稿フォーム -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="review-form">
                            <h4 style="margin-bottom: 15px; color: #333;">レビューを投稿する</h4>
                            <?php if (!empty($error) && strpos($error, 'レビュー') !== false): ?>
                                <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="">
                                <input type="hidden" name="food_id" value="<?= $food['ID'] ?>">
                                
                                <div class="form-group">
                                    <label class="form-label">評価 *</label>
                                    <div class="rating-group">
                                        <input type="hidden" name="rating" id="rating-input" value="1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="rating-star <?= $i <= 1 ? 'active' : '' ?>" data-rating="<?= $i ?>"><?= $i <= 1 ? '★' : '☆' ?></span>
                                        <?php endfor; ?>
                                        <span style="margin-left: 10px; color: #6c757d;" id="rating-text">不満</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="content" class="form-label">レビュー内容 *</label>
                                    <textarea 
                                        name="content" 
                                        id="content" 
                                        class="form-textarea" 
                                        placeholder="料理の感想をお聞かせください..."
                                        required
                                    ><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" name="submit_review" class="btn btn-primary">
                                    📝 レビューを投稿
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="review-form" style="text-align: center; background: #f8f9fa;">
                            <p style="margin-bottom: 15px; color: #6c757d;">レビューを投稿するにはログインが必要です</p>
                            <a href="login.php" class="btn btn-primary">ログインしてレビューを投稿</a>
                        </div>
                    <?php endif; ?>

                    <!-- レビュー一覧 -->
                    <div class="reviews-list">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <span class="reviewer-name">👤 <?= htmlspecialchars($review['user_name']) ?></span>
                                        <span class="review-date"><?= date('Y年m月d日', strtotime($review['created_at'])) ?></span>
                                    </div>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span style="color: #ffc107;"><?= $i <= $review['rating'] ? '★' : '☆' ?></span>
                                        <?php endfor; ?>
                                        <span style="margin-left: 8px; color: #6c757d; font-size: 0.9rem;">(<?= $review['rating'] ?>/5)</span>
                                    </div>
                                    <div class="review-content"><?= nl2br(htmlspecialchars($review['content'])) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-reviews">
                                <div style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;">💬</div>
                                <p>まだレビューが投稿されていません。</p>
                                <p>最初のレビューを投稿してみませんか？</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function orderFood(foodId) {
            if (confirm('この料理を注文しますか？')) {
                alert('ご注文ありがとうございます！\n注文ID: ' + Math.floor(Math.random() * 10000) + '\n\n配達予定時間: 30分後\nお楽しみにお待ちください！');
            }
        }

        // 星評価の処理
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('rating-input');
            const ratingText = document.getElementById('rating-text');
            
            const ratingTexts = {
                1: '不満',
                2: '普通',
                3: '良い',
                4: 'とても良い',
                5: '最高'
            };

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    ratingInput.value = rating;
                    ratingText.textContent = ratingTexts[rating];
                    
                    // 星の表示を更新
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('active');
                            s.innerHTML = '★';
                        } else {
                            s.classList.remove('active');
                            s.innerHTML = '☆';
                        }
                    });
                });

                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.dataset.rating);
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.innerHTML = '★';
                            s.style.color = '#ffc107';
                        } else {
                            s.innerHTML = '☆';
                            s.style.color = '#ddd';
                        }
                    });
                });
            });

            // マウスアウト時に選択された評価に戻す
            document.querySelector('.rating-group').addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value);
                stars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.innerHTML = '★';
                        s.style.color = '#ffc107';
                    } else {
                        s.innerHTML = '☆';
                        s.style.color = '#ddd';
                    }
                });
            });
        });
    </script>
</body>
</html>