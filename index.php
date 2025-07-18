<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodDelivery - 美味しい料理をお届け</title>
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

        .auth-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .user-greeting {
            color: white;
            font-weight: 500;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background-color: white;
            color: #28a745;
        }

        .btn-primary:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background-color: white;
            color: #28a745;
            transform: translateY(-2px);
        }

        .hero {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .search-section {
            background: white;
            padding: 40px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .search-form {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1.1rem;
            transition: border-color 0.3s ease;
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
            transition: transform 0.3s ease;
        }

        .btn-search:hover {
            transform: translateY(-2px);
        }

        .features {
            padding: 80px 0;
            background-color: #f8f9fa;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 50px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            color: #28a745;
            margin-bottom: 15px;
        }

        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 0;
        }

        .category-card:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 20px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .search-form {
                flex-direction: column;
            }

            .features-grid {
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
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="user-greeting">こんにちは、<?= ($_SESSION['user_name']) ?>さん</span>
                        <a href="profile.php" class="btn btn-secondary">会員情報</a>
                        <a href="logout.php" class="btn btn-primary">ログアウト</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary">ログイン</a>
                        <a href="user-register.php" class="btn btn-primary">新規会員登録</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>美味しい料理を、あなたの元へ 🚚</h1>
            <p>地元の人気レストランから、お気に入りの料理を最短30分でお届けします</p>
            <div style="margin-top: 30px;">
                <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; margin: 0 10px;">🍕 ピザ</span>
                <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; margin: 0 10px;">🍣 寿司</span>
                <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; margin: 0 10px;">🍜 ラーメン</span>
                <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; margin: 0 10px;">🍛 カレー</span>
            </div>
        </div>
    </section>

    <section class="search-section">
        <div class="container">
            <div class="search-container">
                <h2 style="text-align: center; margin-bottom: 30px; color: #28a745;">🔍 料理を検索</h2>
                <form class="search-form" action="search.php" method="GET">
                    <input 
                        type="text" 
                        name="query" 
                        class="search-input" 
                        placeholder="料理名を入力してください（例: ピザ、寿司、カレー）"
                        required
                    >
                    <button type="submit" class="btn-search">🔍 検索</button>
                </form>
                <p style="text-align: center; color: #6c757d; font-size: 0.9rem;">
                    お好みの料理を検索して、近くのレストランを見つけましょう
                </p>
            </div>
        </div>
    </section>

    <section class="categories-section" style="padding: 60px 0; background: white;">
        <div class="container">
            <h2 style="text-align: center; color: #28a745; margin-bottom: 40px;">🍽️ 人気カテゴリ</h2>
            <div class="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
                <div class="category-card" onclick="searchCategory('ピザ')" style="background: linear-gradient(135deg, #ff6b6b, #ff8e8e); color: white; padding: 30px 20px; border-radius: 15px; text-align: center; cursor: pointer; transition: transform 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🍕</div>
                    <h3>ピザ</h3>
                </div>
                <div class="category-card" onclick="searchCategory('寿司')" style="background: linear-gradient(135deg, #4ecdc4, #6de6dc); color: white; padding: 30px 20px; border-radius: 15px; text-align: center; cursor: pointer; transition: transform 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🍣</div>
                    <h3>寿司</h3>
                </div>
                <div class="category-card" onclick="searchCategory('ラーメン')" style="background: linear-gradient(135deg, #f7b731, #f39c12); color: white; padding: 30px 20px; border-radius: 15px; text-align: center; cursor: pointer; transition: transform 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🍜</div>
                    <h3>ラーメン</h3>
                </div>
                <div class="category-card" onclick="searchCategory('カレー')" style="background: linear-gradient(135deg, #e55039, #e74c3c); color: white; padding: 30px 20px; border-radius: 15px; text-align: center; cursor: pointer; transition: transform 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🍛</div>
                    <h3>カレー</h3>
                </div>
                <div class="category-card" onclick="searchCategory('ハンバーガー')" style="background: linear-gradient(135deg, #a55eea, #8e44ad); color: white; padding: 30px 20px; border-radius: 15px; text-align: center; cursor: pointer; transition: transform 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🍔</div>
                    <h3>ハンバーガー</h3>
                </div>
                <div class="category-card" onclick="searchCategory('中華')" style="background: linear-gradient(135deg, #fd79a8, #e84393); color: white; padding: 30px 20px; border-radius: 15px; text-align: center; cursor: pointer; transition: transform 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🥟</div>
                    <h3>中華</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 style="text-align: center; color: #28a745; margin-bottom: 20px;">なぜFoodDeliveryを選ぶのか？</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>高速配達</h3>
                    <p>注文から30分以内にお届け。熱々の料理をお楽しみください。</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🍴</div>
                    <h3>豊富な選択肢</h3>
                    <p>和食から洋食まで、多様なレストランから選べます。</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>お得な価格</h3>
                    <p>定期的なキャンペーンと割引で、お財布にも優しい。</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 FoodDelivery. すべての権利を保有します。</p>
        </div>
    </footer>

    <script>
        function searchCategory(category) {
            window.location.href = 'search.php?query=' + encodeURIComponent(category);
        }

        // ページ読み込み時のアニメーション
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.category-card, .feature-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
