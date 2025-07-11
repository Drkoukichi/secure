<?php
session_start();

// 検索クエリを取得
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// データベースから料理を検索
$results = [];
if (!empty($query)) {
    try {
        $db = new PDO('sqlite:/var/www/html/secure/store-info.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // LIKE演算子を使用して部分一致検索（平均評価とレビュー数も取得）
        $stmt = $db->prepare("
            SELECT f.*, 
                   COALESCE(AVG(r.rating), 0) as avg_rating,
                   COUNT(r.id) as review_count
            FROM food f 
            LEFT JOIN review r ON f.ID = r.food_id 
            WHERE f.name LIKE :query 
            GROUP BY f.ID 
            ORDER BY f.name
        ");
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $error = "データベースエラー: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索結果 - FoodDelivery</title>
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

        .back-btn {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: rgba(255,255,255,0.3);
        }

        .search-header {
            background: white;
            padding: 30px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-form {
            display: flex;
            gap: 15px;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1.1rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #28a745;
        }

        .btn-search {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .results-section {
            padding: 40px 0;
        }

        .results-header {
            margin-bottom: 30px;
        }

        .results-header h2 {
            color: #28a745;
            margin-bottom: 10px;
        }

        .results-count {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .food-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .food-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #999;
        }

        .food-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .food-info {
            padding: 20px;
        }

        .food-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .food-description {
            color: #6c757d;
            margin-bottom: 15px;
        }

        .food-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .rating-stars {
            color: #ffc107;
        }

        .rating-text {
            color: #6c757d;
        }

        .food-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-order {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: transform 0.3s ease;
        }

        .btn-order:hover {
            transform: translateY(-2px);
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            margin-top: 30px;
        }

        .no-results-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-results h3 {
            color: #6c757d;
            margin-bottom: 15px;
        }

        .no-results p {
            color: #999;
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

            .nav-links {
                flex-direction: column !important;
                gap: 10px !important;
            }

            .search-form {
                flex-direction: column;
            }

            .food-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">🍕 FoodDelivery</a>
                <div class="nav-links" style="display: flex; gap: 15px; align-items: center;">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span style="color: white;">こんにちは、<?= htmlspecialchars($_SESSION['user_name']) ?>さん</span>
                        <a href="profile.php" class="back-btn">会員情報</a>
                        <a href="logout.php" class="back-btn">ログアウト</a>
                    <?php else: ?>
                        <a href="login.php" class="back-btn">ログイン</a>
                        <a href="user-register.php" class="back-btn">新規会員登録</a>
                    <?php endif; ?>
                    <a href="index.php" class="back-btn">← ホームに戻る</a>
                </div>
            </nav>
        </div>
    </header>

    <section class="search-header">
        <div class="container">
            <form class="search-form" action="search.php" method="GET">
                <input 
                    type="text" 
                    name="query" 
                    class="search-input" 
                    placeholder="料理名を入力してください"
                    value="<?= htmlspecialchars($query) ?>"
                    required
                >
                <button type="submit" class="btn-search">🔍 検索</button>
            </form>
        </div>
    </section>

    <section class="results-section">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($query)): ?>
                <div class="results-header">
                    <h2>「<?= htmlspecialchars($query) ?>」の検索結果</h2>
                    <p class="results-count"><?= count($results) ?>件の料理が見つかりました</p>
                </div>

                <?php if (!empty($results)): ?>
                    <div class="food-grid">
                        <?php foreach ($results as $food): ?>
                            <div class="food-card" onclick="viewDetails(<?= $food['ID'] ?>)">
                                <div class="food-image">
                                    <?php if (!empty($food['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($food['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($food['name']) ?>"
                                             onerror="this.style.display='none'; this.parentNode.innerHTML='🍽️';">
                                    <?php else: ?>
                                        🍽️
                                    <?php endif; ?>
                                </div>
                                <div class="food-info">
                                    <div class="food-name"><?= htmlspecialchars($food['name']) ?></div>
                                    <div class="food-description">
                                        美味しい<?= htmlspecialchars($food['name']) ?>をお楽しみください
                                    </div>
                                    <?php if ($food['review_count'] > 0): ?>
                                        <div class="food-rating">
                                            <span class="rating-stars">
                                                <?php 
                                                $avgRating = round($food['avg_rating'], 1);
                                                for ($i = 1; $i <= 5; $i++): 
                                                    echo $i <= $avgRating ? '★' : '☆';
                                                endfor; 
                                                ?>
                                            </span>
                                            <span class="rating-text"><?= $avgRating ?> (<?= $food['review_count'] ?>件のレビュー)</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="food-rating">
                                            <span class="rating-text">まだレビューがありません</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="food-actions">
                                        <span style="font-weight: bold; color: #28a745;">詳細を見る</span>
                                        <a href="store-detail.php?id=<?= $food['ID'] ?>" class="btn-order" onclick="event.stopPropagation();">
                                            注文する
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <div class="no-results-icon">🔍</div>
                        <h3>検索結果が見つかりませんでした</h3>
                        <p>「<?= htmlspecialchars($query) ?>」に一致する料理が見つかりませんでした。</p>
                        <p>別のキーワードで検索してみてください。</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">🍽️</div>
                    <h3>料理を検索してください</h3>
                    <p>上の検索窓に料理名を入力して、お気に入りの料理を見つけましょう。</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        function viewDetails(foodId) {
            window.location.href = 'store-detail.php?id=' + foodId;
        }
    </script>
</body>
</html>
