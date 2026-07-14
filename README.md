# Eugen Portfolio

PHP/MySQL software engineering portfolio for job applications.

## Stack

- HTML for the portfolio structure
- CSS for the responsive design
- JavaScript for contact form submission
- PHP for server-side rendering and form handling
- MySQL for skills, projects, and contact messages

## Setup

1. Create the database by importing `database/schema.sql` into MySQL.
2. Update database credentials in `config.php`, or set these environment variables:
   `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. Update your real name and email in `config.php`.
4. Replace the sample projects and skills in MySQL with your real work.
5. Run locally from this folder:

```bash
php -S 127.0.0.1:8000
```

Then open `http://127.0.0.1:8000/index.php`.

## Vercel Static Build

Vercel does not run the PHP/MySQL contact form directly. Generate a static
deployment page before deploying:

```bash
php scripts/build-static.php
```

This writes `index.html` for Vercel and keeps the PHP version available at
`index.php`.

## Files

- `index.php` renders the portfolio.
- `index.html` is the generated static version for Vercel.
- `submit_contact.php` validates and saves contact messages.
- `scripts/build-static.php` generates the Vercel static page.
- `includes/bootstrap.php` handles config, MySQL connection, and fallback content.
- `database/schema.sql` creates and seeds the MySQL tables.
- `styles.css` contains the layout and visual design.
- `assets/app.js` handles the contact form.
- `assets/hero-workspace.png` is the generated hero image.

## Contact Email

The contact form sends notifications to the email in `config.php` using PHP's
`mail()` function when your PHP host has mail/SMTP configured. On local XAMPP or
PHP's built-in server, email delivery may need SMTP setup before messages arrive
in Gmail.
