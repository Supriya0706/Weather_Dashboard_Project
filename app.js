// PHP Endpoints (Proxy for OpenWeatherMap and Python Analytics)
const PROXY_URL = 'api/proxy.php';
const ANALYZE_URL = 'api/analyze.php';
const HISTORY_URL = 'api/history.php';

// DOM Elements
const cityInput = document.getElementById('city-input');
const searchBtn = document.getElementById('search-btn');
const geoBtn = document.getElementById('geo-btn');
const themeToggle = document.getElementById('theme-toggle');
const searchHistory = document.getElementById('search-history');
const weatherIconContainer = document.getElementById('weather-icon-container');
const forecastList = document.getElementById('forecast-list');

let tempChart = null;

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    loadSearchHistory();
    
    // Default search or Geolocation
    if (navigator.geolocation) {
        geoBtn.click();
    } else {
        handleSearch('London');
    }
});

// --- Event Listeners ---

searchBtn.addEventListener('click', () => {
    const city = cityInput.value.trim();
    if (city) handleSearch(city);
});

cityInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') searchBtn.click();
});

geoBtn.addEventListener('click', () => {
    if (navigator.geolocation) {
        geoBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Locating...';
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                fetchWeatherByCoords(pos.coords.latitude, pos.coords.longitude);
                geoBtn.innerHTML = '<i class="fas fa-location-arrow me-2"></i> Use My Location';
            },
            (err) => {
                console.error(err);
                handleSearch('London');
                geoBtn.innerHTML = '<i class="fas fa-location-arrow me-2"></i> Location Blocked';
            }
        );
    }
});

themeToggle.addEventListener('click', toggleTheme);

// --- Core Functions ---

async function handleSearch(city) {
    try {
        // Fetch current to calculate score first (standard SPA behavior)
        const dummyCurrent = await fetchAPI('/weather', { q: city });
        const score = calculateWeatherScore(dummyCurrent);
        
        // Fetch with score to log to MySQL
        const currentData = await fetchAPI('/weather', { q: city, score: score });
        const forecastData = await fetchAPI('/forecast', { q: city });
        
        updateUI(currentData, forecastData);
        loadSearchHistory(); 
    } catch (error) {
        console.error(error);
        alert(`Search Error: ${error.message || 'Check your database connection and API key.'}`);
    }
}

async function fetchWeatherByCoords(lat, lon) {
    try {
        const currentData = await fetchAPI('/weather', { lat, lon });
        const forecastData = await fetchAPI('/forecast', { lat, lon });
        
        updateUI(currentData, forecastData);
    } catch (error) {
        console.error(error);
    }
}

async function fetchAPI(endpoint, params) {
    const query = new URLSearchParams({
        ...params,
        endpoint: endpoint
    });
    const response = await fetch(`${PROXY_URL}?${query}`);
    if (!response.ok) throw new Error('API request failed');
    return await response.json();
}

async function fetchPythonInsights(data) {
    try {
        const query = new URLSearchParams({
            temp: data.main.temp,
            humidity: data.main.humidity,
            weather: data.weather[0].main
        });
        const response = await fetch(`${ANALYZE_URL}?${query}`);
        const result = await response.json();
        updatePythonUI(result);
    } catch (error) {
        console.error('Python Analytics failed', error);
    }
}

// --- UI Updates ---

function updateUI(current, forecast) {
    // Basic Details
    document.getElementById('location-name').textContent = `${current.name}, ${current.sys.country}`;
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    document.getElementById('current-temp').textContent = `${Math.round(current.main.temp)}°`;
    document.getElementById('weather-desc').textContent = current.weather[0].description;
    document.getElementById('feels-like').textContent = `${Math.round(current.main.feels_like)}°`;
    document.getElementById('humidity').textContent = `${current.main.humidity}%`;
    document.getElementById('wind-speed').textContent = `${Math.round(current.wind.speed * 3.6)} km/h`;
    
    // Icon
    const iconCode = current.weather[0].icon;
    weatherIconContainer.innerHTML = `<img src="https://openweathermap.org/img/wn/${iconCode}@4x.png" alt="weather" style="width:120px;">`;

    // Weather Score & Recommendations
    const score = calculateWeatherScore(current);
    updateScoreUI(score, current);
    updateRecommendations(current, score);

    // Fetch Python Analytics
    fetchPythonInsights(current);

    // Charts & Forecast
    renderForecast(forecast);
    renderChart(forecast);
}

function updatePythonUI(result) {
    const activityRec = document.getElementById('activity-rec');
    if (result.insights && result.insights.length > 0) {
        // Append a python insight to the activity recommendation or create a new section
        const insight = result.insights[0];
        const pythonBadge = `<br><span class="badge bg-dark mt-2"><i class="fab fa-python me-1"></i> Python Insight:</span><br><small class="text-info">${insight}</small>`;
        activityRec.innerHTML += pythonBadge;
    }
}

function calculateWeatherScore(data) {
    let score = 100;
    const temp = data.main.temp;
    const humidity = data.main.humidity;
    const wind = data.wind.speed;
    const weather = data.weather[0].main.toLowerCase();

    // Deductions based on business rules
    if (temp > 30) score -= (temp - 30) * 2;
    if (temp < 15) score -= (15 - temp) * 2;
    if (humidity > 70) score -= (humidity - 70) * 0.5;
    if (wind > 10) score -= wind * 1.5;
    if (weather.includes('rain')) score -= 30;
    if (weather.includes('thunderstorm')) score -= 50;
    if (weather.includes('snow')) score -= 20;

    return Math.max(0, Math.min(100, Math.round(score)));
}

function updateScoreUI(score, data) {
    const scoreNum = document.getElementById('weather-score-num');
    const scoreLabel = document.getElementById('score-label');
    const scoreSummary = document.getElementById('score-summary');
    const fgCircle = document.querySelector('.circular-progress .fg');

    scoreNum.textContent = score;
    
    // Circular animation
    const offset = 283 - (score / 100) * 283;
    fgCircle.style.strokeDasharray = `${283 - offset}, 283`;

    if (score >= 80) {
        scoreLabel.textContent = 'Perfect Day';
        scoreSummary.textContent = "Conditions are ideal for any outdoor activity. Enjoy the fresh air!";
        fgCircle.style.stroke = '#198754'; // Success green
    } else if (score >= 60) {
        scoreLabel.textContent = 'Good Conditions';
        scoreSummary.textContent = "Mostly pleasant weather. Great for a walk or light travel.";
        fgCircle.style.stroke = '#0dcaf0'; // Info blue
    } else if (score >= 40) {
        scoreLabel.textContent = 'Moderate';
        scoreSummary.textContent = "Some minor discomforts. Dress accordingly and keep an eye on sky.";
        fgCircle.style.stroke = '#ffc107'; // Warning yellow
    } else {
        scoreLabel.textContent = 'Challenging';
        scoreSummary.textContent = "Not ideal for outdoor plans. Stay safe and comfortable indoors.";
        fgCircle.style.stroke = '#dc3545'; // Danger red
    }
}

function updateRecommendations(data, score) {
    const temp = data.main.temp;
    const weather = data.weather[0].main.toLowerCase();
    
    // Activity Recs
    let activityText = "Ideal for outdoor sports and city exploration.";
    if (score < 50) activityText = "Consider indoor yoga, gym, or visiting a museum.";
    else if (temp > 28) activityText = "Swimming or water sports are highly recommended.";
    document.getElementById('activity-rec').textContent = activityText;

    // Travel Advisory
    let travelText = "Smooth travel conditions today. No delays expected.";
    if (weather.includes('rain')) travelText = "Expect minor delays due to rain. Drive safely.";
    else if (weather.includes('storm')) travelText = "Severe weather warning. Avoid non-essential travel.";
    document.getElementById('travel-advisory').textContent = travelText;

    // Health Suggestions
    let healthText = "Conditions are great for physical activity.";
    if (data.main.humidity > 80) healthText = "High humidity might cause fatigue. Hydrate well.";
    else if (temp < 10) healthText = "Cold weather; wear layers to prevent hypothermia.";
    document.getElementById('health-suggestion').textContent = healthText;
}

function renderForecast(data) {
    forecastList.innerHTML = '';
    // Filter to get one item per day (approx noon)
    const dailyData = data.list.filter(item => item.dt_txt.includes('12:00:00')).slice(0, 5);

    dailyData.forEach(item => {
        const date = new Date(item.dt * 1000);
        const day = date.toLocaleDateString('en-US', { weekday: 'short' });
        const temp = Math.round(item.main.temp);
        const icon = item.weather[0].icon;

        const div = document.createElement('div');
        div.className = 'forecast-item';
        div.innerHTML = `
            <span class="fw-bold" style="width: 50px;">${day}</span>
            <img src="https://openweathermap.org/img/wn/${icon}.png" alt="w" style="width: 40px;" class="mx-auto">
            <span class="fw-bold">${temp}°</span>
            <span class="text-muted small ms-2">${item.weather[0].main}</span>
        `;
        forecastList.appendChild(div);
    });
}

function renderChart(data) {
    const ctx = document.getElementById('tempChart').getContext('2d');
    const labels = data.list.slice(0, 8).map(item => {
        return new Date(item.dt * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    });
    const temps = data.list.slice(0, 8).map(item => item.main.temp);

    if (tempChart) tempChart.destroy();

    tempChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temperature (°C)',
                data: temps,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: false, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// --- History & Theme ---

function saveToHistory(city) {
    let history = JSON.parse(localStorage.getItem('weatherHistory') || '[]');
    if (!history.includes(city)) {
        history.unshift(city);
        if (history.length > 5) history.pop();
        localStorage.setItem('weatherHistory', JSON.stringify(history));
        loadSearchHistory();
    }
}

async function loadSearchHistory() {
    try {
        const response = await fetch(HISTORY_URL);
        const history = await response.json();
        
        searchHistory.innerHTML = '';
        history.forEach(city => {
            const badge = document.createElement('span');
            badge.className = 'history-badge';
            badge.textContent = city;
            badge.onclick = () => handleSearch(city);
            searchHistory.appendChild(badge);
        });
    } catch (error) {
        console.error('Failed to load history', error);
    }
}

function initTheme() {
    const theme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', theme);
    updateThemeIcon(theme);
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-bs-theme');
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
}

function updateThemeIcon(theme) {
    const icon = themeToggle.querySelector('i');
    icon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
}
