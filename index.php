<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyCast | Smart Weather Decision Assistant</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg glass-nav sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-cloud-sun-rain me-2 text-primary fs-3"></i>
                <span class="fw-bold tracking-tight">SkyCast</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <button id="theme-toggle" class="btn btn-outline-secondary rounded-circle theme-btn">
                    <i class="fas fa-moon"></i>
                </button>
                <button id="geo-btn" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-location-arrow me-2"></i> Use My Location
                </button>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <!-- Search Section -->
        <section class="row mb-5 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="search-container glass-card p-2 d-flex align-items-center">
                    <i class="fas fa-search ms-3 text-muted"></i>
                    <input type="text" id="city-input" class="form-control border-0 bg-transparent py-3 px-3" placeholder="Search for a city...">
                    <button id="search-btn" class="btn btn-primary rounded-pill px-4 ms-2">Search</button>
                </div>
                <div id="search-history" class="mt-3 d-flex flex-wrap gap-2 overflow-auto">
                    <!-- History items will be injected here -->
                </div>
            </div>
        </section>

        <!-- Current Weather & Score -->
        <section class="row g-4 mb-5">
            <div class="col-lg-7">
                <div id="current-weather-card" class="glass-card main-weather-card h-100 p-4 position-relative overflow-hidden">
                    <div class="weather-overlay"></div>
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <h2 id="location-name" class="display-5 fw-bold mb-0">---</h2>
                            <p id="current-date" class="text-muted mb-4 opacity-75">---</p>
                            <div class="d-flex align-items-center">
                                <h1 id="current-temp" class="display-1 fw-bold mb-0 me-3">--°</h1>
                                <div id="weather-icon-container">
                                    <i class="fas fa-cloud mx-auto fa-5x"></i>
                                </div>
                            </div>
                            <h3 id="weather-desc" class="h4 text-capitalize mt-2">---</h3>
                        </div>
                        <div class="text-end">
                            <div class="weather-detail mb-3">
                                <span class="text-muted d-block opacity-75">RealFeel</span>
                                <span id="feels-like" class="fw-bold fs-4">--°</span>
                            </div>
                            <div class="weather-detail">
                                <span class="text-muted d-block opacity-75">Humidity</span>
                                <span id="humidity" class="fw-bold fs-4">--%</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5 position-relative z-1 text-center g-3">
                        <div class="col-4">
                            <i class="fas fa-wind text-primary mb-2"></i>
                            <span class="d-block text-muted small opacity-75">Wind</span>
                            <span id="wind-speed" class="fw-bold">-- km/h</span>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-droplet text-primary mb-2"></i>
                            <span class="d-block text-muted small opacity-75">Rain Prob</span>
                            <span id="rain-prob" class="fw-bold">--%</span>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-sun text-primary mb-2"></i>
                            <span class="d-block text-muted small opacity-75">UV Index</span>
                            <span id="uv-index" class="fw-bold">--</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="glass-card h-100 p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center">
                        <i class="fas fa-brain me-2 text-primary"></i> Weather Score
                    </h5>
                    <div class="score-container text-center py-3">
                        <div class="circular-progress-wrapper position-relative mx-auto">
                            <svg class="circular-progress" viewBox="0 0 100 100">
                                <circle class="bg" cx="50" cy="50" r="45"></circle>
                                <circle class="fg" cx="50" cy="50" r="45" style="stroke-dasharray: 0, 283;"></circle>
                            </svg>
                            <div class="score-value position-absolute top-50 start-50 translate-middle">
                                <span id="weather-score-num" class="display-3 fw-bold">--</span>
                                <span class="text-muted fs-5">/100</span>
                            </div>
                        </div>
                        <h4 id="score-label" class="mt-4 fw-bold">---</h4>
                        <p id="score-summary" class="text-muted px-3">---</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recommendations Grid -->
        <section class="mb-5">
            <h4 class="fw-bold mb-4">Smart Suggestions</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card recommendation-card h-100 p-4 border-start border-4 border-primary">
                        <div class="icon-circle mb-3 bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-running fa-lg"></i>
                        </div>
                        <h5>Activities</h5>
                        <p id="activity-rec" class="text-muted small mb-0">Enter a city to get personalized activity ideas.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card recommendation-card h-100 p-4 border-start border-4 border-info">
                        <div class="icon-circle mb-3 bg-info bg-opacity-10 text-info">
                            <i class="fas fa-plane fa-lg"></i>
                        </div>
                        <h5>Travel</h5>
                        <p id="travel-advisory" class="text-muted small mb-0">Travel advisories based on current conditions.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card recommendation-card h-100 p-4 border-start border-4 border-success">
                        <div class="icon-circle mb-3 bg-success bg-opacity-10 text-success">
                            <i class="fas fa-heartbeat fa-lg"></i>
                        </div>
                        <h5>Health</h5>
                        <p id="health-suggestion" class="text-muted small mb-0">Health tips considering humidity and temp.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts & Forecast -->
        <section class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-4">5-Day Temperature Trend</h5>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="tempChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-4">5-Day Forecast</h5>
                    <div id="forecast-list" class="forecast-container">
                        <!-- Forecast items will be injected here -->
                        <div class="text-center py-5 opacity-50">
                            <i class="fas fa-calendar-day fa-3x mb-3"></i>
                            <p>No forecast data</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-5 text-center mt-5">
        <p class="text-muted small">© 2026 SkyCast Decision Assistant. Power by OpenWeatherMap.</p>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="app.js"></script>
</body>
</html>
