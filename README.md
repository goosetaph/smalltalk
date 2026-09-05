# SmallTalk

SmallTalk is a minimal Twitter/X-style microblog built with Laravel 11 and Laravel Breeze. Registered users can post short messages ("tweets"), comment on each other's posts, and edit or delete their own content. It is a small learning/portfolio project: the goal is a clean, idiomatic Laravel CRUD app with proper ownership-based authorization and feature tests, rather than a feature-complete social network.

## Tech stack

- **PHP 8.2+** / **Laravel 11** (slim skeleton — no `app/Http/Kernel.php`, middleware configured in `bootstrap/app.php`)
- **Laravel Breeze** (Blade + Alpine.js starter kit) for authentication scaffolding
- **Blade** templates
- **Tailwind CSS** + **daisyUI** for styling
- **Alpine.js** for small bits of interactivity
- **Vite** for asset bundling
- **MySQL** (default) or **SQLite**
- **PHPUnit** for feature tests

## Features

**Authentication (via Breeze)**
- Registration and login
- Email verification
- Password reset / forgot password
- Password confirmation
- Profile management (update name & email, change password, delete account)

**Tweets**
- Post a tweet from the dashboard (max 255 characters)
- Dashboard feed showing all tweets, newest first
- Edit and delete your own tweets

**Comments**
- Comment on any tweet (max 2000 characters)
- Edit and delete your own comments

**Authorization**
- `TweetPolicy` and `CommentPolicy` enforce that only the author of a tweet or comment can edit or delete it
- Controllers call `authorize()`, so a non-owner gets a **403** even if they craft the request by hand
- Blade views additionally hide Edit/Delete buttons for content you don't own (`@can` directives)

## Getting started

### Requirements

- PHP 8.2 or newer
- Composer
- Node.js 18+ and npm
- MySQL 8 (or SQLite, see below)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/goosetaph/smalltalk.git
cd smalltalk

# 2. Install PHP and JS dependencies
composer install
npm install

# 3. Create your environment file
cp .env.example .env        # on Windows: copy .env.example .env
php artisan key:generate
```

### Configure the database

**Option A — MySQL** (the default in `.env.example`). Create an empty database, then set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smalltalk
DB_USERNAME=root
DB_PASSWORD=
```

**Option B — SQLite** (zero setup). Create the database file and point `.env` at it:

```bash
touch database/database.sqlite   # on Windows: type nul > database\database.sqlite
```

```env
DB_CONNECTION=sqlite
# and remove or comment out the DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD lines
```

### Migrate and run

```bash
# Create the tables (and a Test User: test@example.com / password)
php artisan migrate --seed

# Build the frontend assets...
npm run build
# ...or run the Vite dev server in a separate terminal while developing
npm run dev

# Serve the app at http://localhost:8000
php artisan serve
```

Mail is configured to the `log` driver by default, so verification and password-reset emails are written to `storage/logs/laravel.log` instead of being sent.

### Running the tests

```bash
php artisan test
```

The suite covers Breeze's auth flows plus tweet/comment creation, editing, deletion, validation, and the ownership rules (including that a non-owner receives a 403).

## Known limitations / possible next steps

This is a learning project, and there is plenty it deliberately does not do:

- **No rate limiting** on posting tweets or comments — nothing stops a user from spamming the feed.
- **Tweet content is capped at 255 characters** because the column is a `varchar(255)`; a `text` column would be the more natural choice.
- **No pagination** — the dashboard loads every tweet in the database in one query.
- **No image or media uploads**, no link previews, no emoji picker.
- **No likes, follows, retweets, hashtags, mentions, or notifications** — the feed is a flat, chronological list of everyone's posts.
- **No search** and no user profile pages showing a single user's tweets.
- **No soft deletes** — deleting a tweet permanently removes it, and its comments are left orphaned rather than cascade-deleted.
- **Tests cover the happy paths and the authorization rules**, not exhaustive edge cases; there is no browser/end-to-end testing.
- **No CI pipeline** and no deployment configuration.

## License

Released under the [MIT License](https://opensource.org/licenses/MIT), the same license as the Laravel framework it is built on.
