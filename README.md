# LeetCodeTrainer

A personalized algorithmic learning platform that models programmer skills, predicts problem-solving success, and recommends targeted practice.

## Project Structure

```
leetcode_trainer/
├── frontend/          # React + TypeScript + Vite
├── backend/           # Laravel (PHP)
├── docs/              # Documentation
│   ├── DESIGN_GUIDELINES.md
│   └── design-references/
├── CLAUDE.md          # Development agent instructions
└── README.md          # This file
```

## Prerequisites

- **Node.js**: v25.8.1 (detected)
- **npm**: 11.11.0 (detected)
- **PHP**: 8.5.4 (detected)
- **Composer**: 2.9.5 (detected)
- **MySQL**: 9.6.0 (detected)

## Setup Instructions

### 1. Clone the Repository

```bash
git clone <repository-url>
cd leetcode_trainer
```

### 2. Frontend Setup

```bash
cd frontend
npm install
cp .env.example .env
```

Edit `frontend/.env` if needed (default points to `http://localhost:8000/api`).

### 3. Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration

Edit `backend/.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leetcode_trainer
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database:

```bash
mysql -u your_username -p
CREATE DATABASE leetcode_trainer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Run migrations:

```bash
php artisan migrate
```

**Note**: The backend is currently using SQLite (database/database.sqlite) as created during installation. Switch to MySQL when ready by updating `.env` and running migrations.

### 5. Run the Applications

**Terminal 1 - Backend:**
```bash
cd backend
php artisan serve
# Runs on http://localhost:8000
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run dev
# Runs on http://localhost:5173
```

### 6. Verify Setup

Open http://localhost:5173 in your browser. You should see:
- ✅ Backend health check passing
- LeetCodeTrainer landing page
- Next steps for development

## Development Commands

### Frontend
```bash
cd frontend
npm run dev          # Start dev server
npm run build        # Build for production
npm run preview      # Preview production build
npm run lint         # Run ESLint
```

### Backend
```bash
cd backend
php artisan serve    # Start development server
php artisan test     # Run tests
php artisan migrate  # Run database migrations
php artisan tinker   # Interactive REPL
```

## Environment Variables

### Frontend (`frontend/.env`)
```env
VITE_API_BASE_URL=http://localhost:8000/api
```

### Backend (`backend/.env`)
Key variables (see `.env.example` for full list):
- `APP_URL`: Application URL
- `DB_CONNECTION`: Database driver (mysql, pgsql, sqlite)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Database credentials

## Project Documentation

- **CLAUDE.md**: Complete project specification and development guidelines
- **docs/DESIGN_GUIDELINES.md**: UI/UX design principles from "The Design of Everyday Things" and "Refactoring UI"
- **docs/design-references/**: Design reference PDFs

## Technology Stack

**Frontend:**
- React 18
- TypeScript
- Vite (build tool)
- CSS (styled according to design guidelines)

**Backend:**
- Laravel 13.38.0
- PHP 8.5.4
- MySQL 9.6 (or SQLite for development)

## Development Workflow

Before starting substantial UI work, review `docs/DESIGN_GUIDELINES.md` for:
- Human-centered design principles
- Visual hierarchy and spacing
- Typography and color systems
- Component patterns
- Accessibility requirements

## Current Status

✅ **Completed:**
- Project structure initialized
- Frontend (React + TypeScript + Vite) set up
- Backend (Laravel) set up
- Health check endpoint implemented
- Design guidelines documented

🚧 **Next Steps (Phase 1 - Data Foundation):**
- Implement problem metadata models
- Create user attempt tracking
- Build data import/validation system
- Set up database schema for skill modeling

See CLAUDE.md for complete roadmap and development phases.

## Testing

### Frontend
```bash
cd frontend
npm run test  # (Tests not yet configured)
```

### Backend
```bash
cd backend
php artisan test
```

## Troubleshooting

### Backend connection fails
- Ensure backend is running: `cd backend && php artisan serve`
- Check port 8000 is not in use
- Verify `frontend/.env` has correct `VITE_API_BASE_URL`

### Database errors
- Verify MySQL is running: `mysql -u root -p`
- Check database exists: `SHOW DATABASES;`
- Ensure `backend/.env` has correct credentials
- Run migrations: `php artisan migrate`

### Frontend build errors
- Clear cache: `rm -rf node_modules package-lock.json && npm install`
- Check Node version: `node --version` (should be v18+)

## Contributing

1. Review `CLAUDE.md` for project requirements and conventions
2. Consult `docs/DESIGN_GUIDELINES.md` before implementing UI features
3. Follow Laravel and React best practices
4. Write tests for new features
5. Ensure accessibility compliance (WCAG AA)

## License

[License information to be added]
