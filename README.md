# Career Skills Tracker

A comprehensive career development platform built with Laravel, React, and Machine Learning capabilities.

## Project Overview

### Technology Stack
- **Backend**: Laravel 10 (PHP)
- **Frontend**: React + TypeScript + Vite
- **Database**: PostgreSQL (Docker) / SQLite (Local)
- **ML Service**: Python Flask with scikit-learn
- **Containerization**: Docker & Docker Compose
- **Authentication**: Laravel Sanctum

### Key Features
1. **Authentication System**
   - Login/Register with dark theme UI
   - Token-based authentication
   - Remember me functionality
   - Password reset capability

2. **Career Management**
   - Skills tracking and assessment
   - Career path recommendations
   - Goal setting and tracking
   - Course recommendations

3. **Machine Learning Integration**
   - Course recommendations based on user skills
   - Career path prediction
   - Skill gap analysis
   - TF-IDF vectorization for skill matching

4. **Dashboard Features**
   - Skills dashboard
   - Course progress tracking
   - Goals management
   - Profile management

## Setup Instructions

### Prerequisites
- Docker and Docker Compose
- Node.js (v16+)
- PHP 8.1+
- Composer
- Python 3.8+ (for ML service)

### Step 1: Environment Setup
```bash
# Clone the repository
git clone https://github.com/walidEssaied/career-skills.git
cd career-skills

# Copy environment files
cp .env.example .env

# Install PHP dependencies
composer install

# Install frontend dependencies
cd frontend
npm install
```

### Step 2: Database Setup
```bash
# Using Docker (recommended)
docker-compose up -d db

# Or for SQLite (local development)
touch database/database.sqlite
```

### Step 3: Application Setup
```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Build frontend assets
cd frontend
npm run build
```

### Step 4: ML Service Setup
```bash
# Setup Python virtual environment
python -m venv ml_env
source ml_env/bin/activate  # On Windows: ml_env\Scripts\activate

# Install ML dependencies
cd ml
pip install -r requirements.txt
```

### Step 5: Start the Services
```bash
# Using Docker (all services)
docker-compose up -d

# Or start services individually:
# Laravel
php artisan serve

# Frontend (development)
cd frontend
npm run dev

# ML Service
cd ml
python recommender.py
```

## Project Structure

```
career-skills/
├── app/                    # Laravel application code
│   ├── Http/              # Controllers, Middleware
│   ├── Models/            # Eloquent models
│   └── Services/          # Business logic services
├── database/              # Migrations and seeders
├── frontend/              # React frontend application
│   ├── src/
│   │   ├── components/    # React components
│   │   ├── services/      # API services
│   │   └── store/         # Redux store
├── ml/                    # Machine Learning service
│   ├── recommender.py     # ML recommendation system
│   └── requirements.txt   # Python dependencies
└── docker/               # Docker configuration files
```

## API Endpoints

### Authentication
- POST `/api/register` - User registration
- POST `/api/login` - User login
- POST `/api/logout` - User logout

### Career Management
- GET `/api/goals` - List user goals
- POST `/api/goals` - Create new goal
- GET `/api/skills` - List user skills
- GET `/api/courses` - List available courses

### ML Endpoints
- GET `/api/ml/recommendations` - Get course recommendations
- GET `/api/ml/career-prediction` - Get career path predictions
- POST `/api/ml/skill-gaps` - Analyze skill gaps

## Development Notes

1. **Environment Configuration**
   - Configure database settings in `.env`
   - Set up ML service URL in frontend environment

2. **Docker Usage**
   - Use `docker-compose up -d` for full stack deployment
   - Individual services can be started with `docker-compose up [service]`

3. **ML Model Training**
   - Initial training data is provided in seeders
   - Models are automatically trained on first run
   - Retrain models using admin panel

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
