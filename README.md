# アプリケーション名
Laravel フリマアプリケーション

## 環境構築

### 1. リポジトリをクローン

```bash
git clone https://github.com/MizukiShinya/flea_market.git
cd flea_market
```

### 2. Dockerコンテナを起動して動作確認

```bash
docker-compose up -d --build
```

**重要**: 
この時点で4つのコンテナ（nginx、php、mysql、phpmyadmin）がすべて「Up」状態になっていることを確認してください。

### 3. 依存関係のインストール

```bash
docker compose exec php composer install
```

### 4. 環境変数ファイルの作成

```bash
docker compose exec php cp .env.example .env
```

### 5. データベース設定

.env ファイルの以下の項目を設定します：

**データベース**
- DB_CONNECTION=mysql
- DB_HOST=mysql
- DB_PORT=3306
- DB_DATABASE=laravel_db
- DB_USERNAME=laravel_user
- DB_PASSWORD=laravel_pass

**Stripe**
- STRIPE_KEY=pk_test_ここに公開可能キー
- STRIPE_SECRET=sk_test_ここにシークレットキー

### 5. アプリケーションキーの生成

```bash
docker compose exec php php artisan key:generate
```

### 6. マイグレーションとシーディングを実行

```bash
docker compose exec php php artisan migrate
docker compose exec php php artisan db:seed
```

## 開発用初期アカウント（Seederで作成）
Email: test@example.com  
Password: password

## 使用技術(実行環境)
PHP：8.1
Laravel：8.83.8
Laravel Fortify：1.19.1
MySQL：11.8.3
Stripe：19.0

## ER図
![ER図](./docs/.png)

## URL
開発環境：http://localhost/
phpMyAdmin：http://localhost:8080/

