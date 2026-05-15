# Publiska izvietošana hostingā

Projekts ir sagatavots publicēšanai tā, lai frontend kodā nav cieti ierakstīta lokālā API adrese.

## Frontend

Frontend API adrese tiek ņemta no:

```env
VITE_API_URL=https://tavs-backend-domens.lv/api
```

Ja frontend un backend atrodas vienā domēnā, var izmantot:

```env
VITE_API_URL=/api
```

Pirms publicēšanas:

```sh
npm install
npm run build
```

Hostinga publiskajā mapē jāieliek `dist` mapes saturs.

## Backend

Backend production vidē jāizmanto `backend/.env.production.example` kā paraugs.

Obligāti jānomaina:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tavs-backend-domens.lv
DB_DATABASE=tava_datubaze
DB_USERNAME=tavs_lietotajs
DB_PASSWORD=tava_parole
```

Uz hostinga jāpalaiž:

```sh
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate
php artisan config:cache
php artisan route:cache
```

Publiskajai web saknes mapei jābūt `backend/public`.

## Pārbaude

Pēc publicēšanas API jābūt pieejamam publiski, piemēram:

```text
https://tavs-backend-domens.lv/api/hobbies
```

Frontend jālieto šī publiskā API adrese caur `VITE_API_URL`.
