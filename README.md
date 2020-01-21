# finest

# MEMO
## 権限修正
find laravel/storage/ | sudo xargs -i chmod 777 {}

## 認証機能開通まで
dc run composer require laravel/ui --dev
dc run artisan ui --auth vue
dc run npm i
dc run npm run dev
