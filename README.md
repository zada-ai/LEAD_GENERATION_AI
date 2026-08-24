# Lead Automation CRM - Part 1

A professional, production-ready **Lead Automation CRM** built with Laravel, PHP 8+, MySQL, and Tailwind CSS.

This is Part 1 of the Lead Automation CRM project, focusing on the foundation and lead management system.

## Features

### Completed Features (Part 1)

- ✅ User Authentication (Register, Login, Forgot Password, Reset Password, Email Verification)
- ✅ Professional Dashboard with Statistics
- ✅ Complete Lead Management (CRUD Operations)
- ✅ Lead Search across multiple fields
- ✅ Lead Filtering (Status, Priority, Source, Category)
- ✅ Lead Pagination
- ✅ Lead Status Management (new, contacted, interested, follow-up, converted, not-interested, do-not-contact)
- ✅ Lead Priority Levels (low, medium, high)
- ✅ Lead Sources (Website, Facebook, Instagram, LinkedIn, Google, Manual, Import, Other)
- ✅ Service Interests (AI Automation, AI Chatbot, Website Development, CRM Development, App Development, SEO, Other)
- ✅ Responsive Design (Mobile, Tablet, Desktop)
- ✅ Reusable Blade Components
- ✅ Form Validation with Form Request Classes
- ✅ Authorization Policies
- ✅ Database Migrations with Proper Indexing
- ✅ Demo Data Seeder
- ✅ Professional UI with Tailwind CSS

### Future Features (Part 2, 3, etc.)

- 🔄 Lead Discovery & Enrichment
- 🔄 Email Campaigns
- 🔄 Gmail Integration & OAuth
- 🔄 AI Qualification
- 🔄 Inbox Management
- 🔄 AI Reply Suggestions
- 🔄 Follow-up Automation
- 🔄 Analytics & Reporting

## Tech Stack

- **Backend**: Laravel 11+ with PHP 8.2+
- **Database**: MySQL 8.0+
- **Frontend**: Blade Templates, Tailwind CSS 4.0+
- **Build Tool**: Vite
- **Authentication**: Laravel Built-in Authentication
- **ORM**: Eloquent

## Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- Node.js 18+ and npm

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd lead-automation
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Create Environment File

```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Configure Database

Edit `.env` file and update database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lead_automation
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Seed Demo Data

```bash
php artisan db:seed
```

This will create:
- 1 demo user account
- 20 demo leads with realistic data

### 9. Build Frontend Assets

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

### 10. Start the Development Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

## Demo Account Credentials

After seeding, use these credentials to login:

- **Email**: `demo@example.com`
- **Password**: `password123`

## Directory Structure

### Key Application Files

```
app/
  ├── Http/
  │   ├── Controllers/
  │   │   ├── Auth/                 # Authentication controllers
  │   │   ├── DashboardController.php
  │   │   ├── LeadController.php
  │   │   └── ProfileController.php
  │   ├── Requests/
  │   │   ├── StoreLeadRequest.php  # Lead creation validation
  │   │   └── UpdateLeadRequest.php # Lead update validation
  │   └── Policies/
  │       └── LeadPolicy.php        # Authorization policies
  ├── Models/
  │   ├── User.php
  │   └── Lead.php
  └── Providers/
      └── AppServiceProvider.php

database/
  ├── migrations/
  │   └── *_create_leads_table.php  # Leads table structure
  ├── factories/
  │   └── LeadFactory.php           # Demo data generation
  └── seeders/
      └── DatabaseSeeder.php        # Seeds demo data

resources/
  ├── views/
  │   ├── layouts/
  │   │   ├── app.blade.php         # Main authenticated layout
  │   │   └── guest.blade.php       # Login/register layout
  │   ├── components/
  │   │   ├── sidebar.blade.php
  │   │   ├── navbar.blade.php
  │   │   ├── button.blade.php
  │   │   ├── card.blade.php
  │   │   ├── badge.blade.php
  │   │   ├── alert.blade.php
  │   │   ├── form-field.blade.php
  │   │   └── ...
  │   ├── auth/
  │   │   ├── login.blade.php
  │   │   ├── register.blade.php
  │   │   └── ...
  │   ├── leads/
  │   │   ├── index.blade.php       # Leads list
  │   │   ├── create.blade.php      # Create lead form
  │   │   ├── edit.blade.php        # Edit lead form
  │   │   └── show.blade.php        # Lead details
  │   ├── profile/
  │   │   └── edit.blade.php        # User profile
  │   └── dashboard.blade.php
  ├── css/
  │   └── app.css                   # Tailwind CSS
  └── js/
      └── app.js

routes/
  ├── web.php                       # Web routes
  └── auth.php                      # Authentication routes

config/
  ├── database.php
  ├── auth.php
  └── ...
```

## Lead Database Schema

### Leads Table Fields

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| created_by | bigint | FK to users table |
| business_name | string | Business name |
| contact_name | string | Contact person name |
| email | string | Contact email (indexed) |
| phone | string | Contact phone |
| website | string | Business website URL |
| facebook_url | string | Facebook page URL |
| instagram_url | string | Instagram profile URL |
| linkedin_url | string | LinkedIn profile URL |
| country | string | Country |
| state | string | State/Province |
| city | string | City (indexed) |
| area | string | Area/Address |
| category | string | Business category (indexed) |
| service_interest | string | Service of interest |
| source | string | How lead was acquired (indexed) |
| status | string | Lead status (indexed) |
| priority | string | Priority level |
| notes | longtext | Additional notes |
| last_contacted_at | timestamp | Last contact datetime |
| next_follow_up_at | timestamp | Next follow-up datetime |
| created_at | timestamp | Record created time |
| updated_at | timestamp | Record last updated |

### Indexes

- created_by
- email
- status
- source
- category
- city
- created_at

## Lead Statuses

| Status | Description |
|--------|-------------|
| new | Newly added lead |
| contacted | Lead has been contacted |
| interested | Lead showed interest |
| follow_up | Awaiting follow-up |
| converted | Lead converted to customer |
| not_interested | Lead not interested |
| do_not_contact | Lead requested not to be contacted |

## Lead Priorities

- **Low**: Non-urgent leads
- **Medium**: Standard priority leads
- **High**: Urgent, high-value leads

## API Routes

### Leads Resource Routes

```
GET    /leads                  - List all leads
GET    /leads/create           - Create lead form
POST   /leads                  - Store new lead
GET    /leads/{lead}           - View lead details
GET    /leads/{lead}/edit      - Edit lead form
PATCH  /leads/{lead}           - Update lead
DELETE /leads/{lead}           - Delete lead

POST   /leads/{lead}/mark-contacted      - Mark as contacted
POST   /leads/{lead}/schedule-follow-up  - Schedule follow-up
```

### Authentication Routes

```
GET  /register              - Registration form
POST /register              - Register new user
GET  /login                 - Login form
POST /login                 - Authenticate user
POST /logout                - Logout user
GET  /forgot-password       - Forgot password form
POST /forgot-password       - Send reset link
GET  /reset-password/{token} - Reset password form
POST /reset-password        - Store new password
```

### Profile Routes

```
GET   /profile              - Profile form
PATCH /profile              - Update profile
DELETE /profile             - Delete account
```

## Development Commands

### Run Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback all migrations
php artisan migrate:reset

# Refresh database (reset + migrate)
php artisan migrate:refresh

# Refresh and seed
php artisan migrate:refresh --seed
```

### Run Seeders

```bash
# Run the database seeder
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=DatabaseSeeder
```

### Build Frontend

```bash
# Development with hot reload
npm run dev

# Production build
npm run build
```

### Cache

```bash
# Clear all caches
php artisan cache:clear

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache
```

## Code Quality

### Project Structure

- Follows Laravel best practices and conventions
- Uses Form Request classes for validation
- Uses Policy classes for authorization
- Uses Eloquent relationships
- Uses reusable Blade components
- Database queries at model/controller level, not in views

### Validation

All user inputs are validated using Form Request classes:

- **StoreLeadRequest**: Validates new lead creation
- **UpdateLeadRequest**: Validates lead updates
- Business logic validation in Model scopes where needed

### Authorization

Lead access is controlled via LeadPolicy:

- Users can only view/edit/delete their own leads
- All destructive actions require authorization check
- CSRF protection on all forms

## Features Breakdown

### Dashboard

The dashboard provides at-a-glance insights:

- **Statistics Cards**: Total leads, new, contacted, interested, follow-up, converted
- **Recent Leads Table**: Last 10 leads with quick actions
- **Status Distribution**: Visual breakdown of leads by status
- **Quick Actions**: Common tasks (Add Lead, View Leads, Settings)

### Lead Management

**List View** (`/leads`)
- Search across business name, contact name, email, phone, city, area, category
- Filter by: Status, Priority, Source, Category
- Pagination (15 leads per page)
- Responsive table with mobile-friendly layout
- Quick view/edit actions

**Create Lead** (`/leads/create`)
- Comprehensive form with sections:
  - Business Information
  - Contact Information
  - Location
  - Sales Information
  - Notes
- Validation with error messages
- Form values preserved on validation failure

**Edit Lead** (`/leads/{lead}/edit`)
- Pre-filled form with existing data
- Same sections as create
- All validations apply

**Lead Details** (`/leads/{lead}`)
- Complete lead profile
- Business, contact, and location details
- Links to websites and social profiles
- Status and priority badges
- Timeline (created, last contacted, next follow-up)
- Quick actions:
  - Mark Contacted
  - Schedule Follow-up
  - Delete with confirmation

### Authentication

Complete authentication system:

- **Register**: New user registration with validation
- **Login**: Email and password authentication with "Remember Me"
- **Forgot Password**: Password reset via email
- **Reset Password**: Secure password reset with token
- **Email Verification**: Optional email verification
- **Logout**: Secure session logout

All passwords are hashed with bcrypt.

### Responsive Design

The application is fully responsive:

**Desktop (1440px+)**
- Full sidebar navigation
- Multi-column layouts
- Full data tables

**Tablet (768px)**
- Responsive grids
- Compact navigation
- Adjusted spacing

**Mobile (375px)**
- Hamburger navigation menu
- Single-column layouts
- Card-based design
- Touch-friendly buttons
- Horizontally scrollable tables (if needed)

All breakpoints tested and optimized for usability.

## Troubleshooting

### Database Connection Error

```bash
# Check .env file has correct database credentials
# Make sure MySQL is running
# Create database if it doesn't exist:
mysql -u root -p
mysql> CREATE DATABASE lead_automation;
```

### Migrations Won't Run

```bash
# Rollback and migrate fresh
php artisan migrate:reset
php artisan migrate

# Or refresh with seed
php artisan migrate:refresh --seed
```

### Authentication Not Working

- Make sure .env is configured correctly
- Check that sessions table exists: `php artisan migrate`
- Clear cache: `php artisan cache:clear`

### Frontend Not Styling Properly

```bash
# Rebuild frontend assets
npm install
npm run build

# Or run dev server
npm run dev
```

## Testing

### Manual Testing Checklist

- [ ] User can register new account
- [ ] User can login with credentials
- [ ] User can logout
- [ ] User can reset password
- [ ] Dashboard loads with statistics
- [ ] User can create a new lead
- [ ] User can view lead list
- [ ] User can search leads
- [ ] User can filter leads
- [ ] User can view lead details
- [ ] User can edit lead
- [ ] User can delete lead
- [ ] User can mark lead as contacted
- [ ] User can schedule follow-up
- [ ] User can update profile
- [ ] Application is responsive on mobile/tablet/desktop
- [ ] All forms have proper validation
- [ ] Flash messages appear correctly
- [ ] Pagination works properly

## Known Limitations (Part 1)

- Email sending is not configured (register confirmation emails won't actually send)
- No file uploads for attachments
- No bulk operations
- No lead assignment to multiple users
- No activity history/audit log
- No integrations with external services
- No API endpoints (REST API)
- No real-time updates

These will be addressed in future parts.

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open source and available under the MIT License.

## Support

For questions, issues, or suggestions, please open an issue in the repository.

---

**Version**: 1.0.0 (Part 1)  
**Last Updated**: 2024  
**Status**: Production Ready for Part 1


## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
"# LEAD_GENERATION_AI" 
