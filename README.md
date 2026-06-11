# SkyCast | Smart Weather Decision Assistant

SkyCast is a modern, premium full-stack weather application that provides actionable recommendations and decision support based on weather conditions. It features a hybrid architecture using PHP for the backend, Python for advanced analytics, and MySQL for persistent search history.

## 🌟 Features

- **Real-Time Weather**: Global weather conditions powered by OpenWeatherMap API.
- **Smart Decision Assistant**: Calculates a "Weather Score" (0-100) based on environmental data.
- **Advanced Recommendations**: Personalized suggestions for activities, travel, and health.
- **Python Analytics Engine**: Integrated Python script for advanced climatic insight generation.
- **5-Day Forecast & Trends**: Interactive charts using **Chart.js**.
- **Full-Stack Persistence**: Search history and scores stored in **MySQL**.
- **Premium UI**: Glassmorphic design, smooth animations, and responsive Bootstrap 5 layout.
- **Deployment Ready**: Fully containerized with **Docker** for easy cloud hosting.

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3 (Glassmorphism), JavaScript (ES6), Bootstrap 5, Chart.js.
- **Backend**: **PHP** (Proxy API & Database Bridge).
- **Analytics**: **Python** (Advanced Recommendation Engine).
- **Database**: **MySQL** (History Tracking).
- **DevOps**: **Docker**, Docker Compose.
- **APIs**: OpenWeatherMap API, Browser Geolocation API.

## 🚀 Installation & Local Setup

### Using XAMPP / Manual PHP
1. Clone the repository to your local web server directory.
2. Ensure **PHP**, **Python**, and **MySQL** are installed.
3. Configure your database credentials in `api/db.php`.
4. Run `php -S localhost:8000` and open it in your browser.

### Using Docker (Recommended)
1. Run `docker-compose up -d`.
2. Access the app at `http://localhost:8000`.

## ☁️ Deployment

The project is optimized for deployment on platforms like **Render** or **Railway** using the included `Dockerfile` and support for environment variables (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_PORT`).

---
Built with ❤️ for the Modern Web