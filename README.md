# the-shift

シフト管理

## 開発環境前提

* docker
* docker-compose

## 開発環境構築

* `git clone XXXX`
* `cd finest`
* `cp .env.example .env`
* .env を適宜修正
* `./dev.sh build`
* `./dev.sh run composer i`
* `cd laravel`
* `cp .env.example .env`
* `cd ..`
* 必要に応じてdocker-compose-dev.yml でMysqlのポート修正
* `./dev.sh run artisan key:generate`
* `./dev.sh run npm i`
* `./dev.sh run npm run dev`
* `find laravel/storage/ | sudo xargs -i chmod 777 {}`
* `./dev.sh up -d`

ここまででトップページへ接続できることを確認する

* laravel/.envを編集 
   ```
   DB_HOST=dev_mysql
   .
   DB_DATABASE=dev
   ...
   DB_PASSWORD=zaq123
  ```
* `./dev.sh run laravel ./artisan migrate:install`
* `./dev.sh run laravel ./artisan migrate`
* `./dev.sh run laravel ./artisan db:seed`

ここで、
- admin@example.com
- password

でログインできることを確認する

# MEMO
## 権限修正
find laravel/storage/ | sudo xargs -i chmod 777 {}

## 認証機能開通まで
```
dc run composer require laravel/ui --dev
dc run artisan ui --auth vue
dc run npm i
dc run npm run dev
```
