# アプリケーション名
Laravel フリマアプリケーション

## 環境構築

### 1. リポジトリをクローン

```bash
git clone [リポジトリURL]
cd test
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

- DB_CONNECTION=mysql
- DB_HOST=mysql
- DB_PORT=3306
- DB_DATABASE=laravel_db
- DB_USERNAME=laravel_user
- DB_PASSWORD=laravel_pass

### 5. アプリケーションキーの生成

```bash
docker compose exec php php artisan key:generate
```

### 6. マイグレーションとシーディングのを実行

```bash
docker compose exec php php artisan migrate
docker compose exec php php artisan db:seed
```

これにより、カテゴリやサンプル問い合わせデータが自動で登録されます。

## アクセス方法

セットアップが完了したら、以下のURLでアクセスできます：

- TODOアプリケーション: http://localhost/
- phpMyAdmin: http://localhost:8080/


## 使用技術(実行環境)
- フレームワーク：Laravel 8.83
- 言語：PHP 8.4
- 開発環境：Docker
- MySQL：8.0

## ER図
![ER図](./docs/.png)

## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/

