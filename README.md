# Gallery 2

An overkill implementation of an automated image gallery. Based on my single-file version from many years ago, but far more complicated for no reason.

## Setup

1. Clone the repository
2. `composer install`
3. `cp .env.example .env`
4. `php artisan key:generate`
5. Set the path to your images in the filesystem as `GALLERY_PATH` in `.env`
6. `npm ci && npm run prod`
7. Point your web server to the `public/` directory

### Authentication

If you want to require a login to access the app, there are a few extra steps:

1. Configure a database connection in `.env`
2. `php artisan migrate`
3. Set `GALLERY_AUTH=true` in `.env`
4. `php artisan user:add`

If you want users to be able to create their own accounts, set `GALLERY_REGISTRATION=true` in `.env`. Otherwise, you'll need to manually create new users from the command line with `php artisan user:add`.

### Video thumbnails

To generate thumbnail images for videos, `ffmpeg` and `ffmprobe` are required to be in your `PATH`.
