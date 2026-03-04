# M-Invoice
[![wakatime](https://wakatime.com/badge/user/563ecbb7-89c4-4563-82c1-258e14191d74/project/b0182d87-f326-41a7-8003-877498f468d0.svg)](https://wakatime.com/badge/user/563ecbb7-89c4-4563-82c1-258e14191d74/project/b0182d87-f326-41a7-8003-877498f468d0)
A comprehensive invoice management system built with Laravel 12, featuring invoicing, quotations, expense tracking, and M-Pesa payment integration.

## Features

- **Invoice Management** - Create, send, and track invoices with automatic reminders
- **Quotations** - Generate and convert quotations to invoices
- **Client Management** - Organize and manage client information
- **Expense Tracking** - Monitor business expenses and profitability
- **Catalog Items** - Maintain a product/service catalog with pricing
- **Recurring Invoices** - Automate recurring billing cycles
- **M-Pesa Integration** - Accept payments via M-Pesa
- **PDF Generation** - Generate professional PDF invoices and quotations
- **Multi-user Support** - Role-based access with staff invitations
- **Subscription Management** - Built-in subscription and billing system
- **Email Notifications** - Automated invoice and reminder emails
- **Google OAuth** - Sign in with Google

## Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd m-invoice
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.sample .env
php artisan key:generate
```

4. Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=m-invoice
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations:
```bash
php artisan migrate
```

6. Build assets:
```bash
npm run build
```

7. Start the development server:
```bash
php artisan serve
```

## Configuration

### Mail Setup
Configure your mail settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=hello@yourdomain.com
```

### M-Pesa Integration
Add your M-Pesa credentials:
```env
MPESA_CONSUMER_KEY=your-consumer-key
MPESA_CONSUMER_SECRET=your-consumer-secret
MPESA_SHORTCODE=your-shortcode
MPESA_PASSKEY=your-passkey
MPESA_CALLBACK_URL=https://yourdomain.com/api/mpesa/callback
MPESA_SANDBOX=true
```

### Google OAuth
Configure Google Sign-In:
```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

## Development

Run all development services concurrently:
```bash
composer dev
```

This starts:
- Laravel development server
- Queue worker
- Log viewer (Pail)
- Vite dev server

## Queue Workers

The application uses queues for:
- Sending invoice emails
- Processing invoice reminders
- Marking overdue invoices

Start the queue worker:
```bash
php artisan queue:work
```

## Testing

Run the test suite:
```bash
composer test
```

## License

MIT License
