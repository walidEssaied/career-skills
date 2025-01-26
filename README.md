# Career Skills Tracker

A modern web application built with Laravel and React for tracking professional skills, career paths, and learning progress.

## Features

### Skills Management
- Track and update your professional skills
- Set proficiency levels and target goals for each skill
- Visual progress tracking and skill gap analysis
- Skill recommendations based on your career goals

### Career Paths
- Browse and join various career paths
- Detailed skill requirements for each career path
- Real-time skill gap analysis when joining a path
- Progress tracking towards career goals
- Visual indicators for skill achievements and gaps

### Course Management
- Access a curated list of professional development courses
- Track course progress and completion status
- Course recommendations based on skill gaps
- Integration with various course providers
- Detailed course information including duration, provider, and difficulty level

### AI-Powered Insights
- Personalized course recommendations based on your skill profile
- Career path predictions using skill matching algorithms
- Detailed skill gap analysis with importance levels
- Progress visualization and tracking
- Smart learning path suggestions

### User Dashboard
- Comprehensive overview of your learning journey
- Visual progress tracking
- Skill gap analysis
- Course completion statistics
- Career path progress

## Technology Stack

### Backend
- Laravel 10.x
- PHP 8.x
- MySQL/SQLite
- RESTful API architecture
- JWT Authentication

### Frontend
- React 18
- TypeScript
- Redux Toolkit for state management
- Tailwind CSS for styling
- Heroicons for UI elements

## Getting Started

### Prerequisites
- PHP >= 8.0
- Composer
- Node.js >= 14
- npm or yarn

### Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/career-skills-tracker.git
cd career-skills-tracker
```

2. Install PHP dependencies:
```bash
composer install
```

3. Set up environment variables:
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up the database:
```bash
php artisan migrate
php artisan db:seed
```

5. Install frontend dependencies:
```bash
cd frontend
npm install
```

### Running the Application

1. Start the Laravel backend:
```bash
php artisan serve
```

2. Start the React frontend:
```bash
cd frontend
npm start
```

The application will be available at `http://localhost:3000`

## Features in Detail

### AI-Powered Career Insights
The application uses advanced algorithms to provide:
- Course recommendations based on your current skill levels and career goals
- Career path predictions with confidence scores
- Detailed skill gap analysis with importance levels
- Visual progress tracking and recommendations

### Skill Management
- Set and track proficiency levels (1-5 scale)
- Visual progress indicators
- Skill gap analysis with recommended levels
- Importance-based prioritization

### Career Paths
- Multiple career paths with detailed skill requirements
- Real-time skill gap analysis
- Progress tracking
- Course recommendations for skill improvement

### Course Integration
- Course catalog with detailed information
- Progress tracking
- Completion certificates
- Integration with external course providers

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
