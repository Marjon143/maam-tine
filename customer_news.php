<?php
// customer_news.php

// === REPLACE these with your actual API keys ===
$weatherApiKey = 'your_actual_openweathermap_api_key_here';
$newsApiKey = 'your_actual_newsapi_key_here';

// Location for weather - you can change this or make dynamic later
$city = 'New York';

// Fetch Weather data from OpenWeatherMap API
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&units=metric&appid=" . $weatherApiKey;
$weatherJson = @file_get_contents($weatherUrl);
$weatherData = $weatherJson ? json_decode($weatherJson, true) : null;

// Fetch News data from NewsAPI
$newsUrl = "https://newsapi.org/v2/top-headlines?country=us&apiKey=" . $newsApiKey;
$newsJson = @file_get_contents($newsUrl);
$newsData = $newsJson ? json_decode($newsJson, true) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer News & Weather</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f7f9; margin: 0; padding: 0; }
    header { background: #007BFF; color: white; padding: 1rem; text-align: center; }
    .container { max-width: 900px; margin: 2rem auto; padding: 1rem; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 8px; }
    .weather, .news { margin-bottom: 2rem; }
    .weather h2, .news h2 { border-bottom: 2px solid #007BFF; padding-bottom: 0.5rem; color: #333; }
    .weather-details { display: flex; align-items: center; gap: 1rem; }
    .weather-icon img { width: 60px; }
    .weather-info { font-size: 1.2rem; }
    .news-list { list-style: none; padding: 0; }
    .news-list li { margin-bottom: 1rem; }
    .news-list a { text-decoration: none; color: #007BFF; font-weight: bold; }
    .news-list a:hover { text-decoration: underline; }
    footer { text-align: center; padding: 1rem; font-size: 0.8rem; color: #777; }
  </style>
</head>
<body>
  <header>
    <h1>Customer News & Weather</h1>
  </header>
  <div class="container">

    <section class="weather">
      <h2>Current Weather in <?php echo htmlspecialchars($city); ?></h2>
      <?php if ($weatherData && isset($weatherData['cod']) && $weatherData['cod'] == 200): ?>
        <div class="weather-details">
          <div class="weather-icon">
            <img src="https://openweathermap.org/img/wn/<?php echo $weatherData['weather'][0]['icon']; ?>@2x.png" alt="Weather Icon" />
          </div>
          <div class="weather-info">
            <p><strong><?php echo ucfirst($weatherData['weather'][0]['description']); ?></strong></p>
            <p>Temperature: <?php echo $weatherData['main']['temp']; ?>°C</p>
            <p>Humidity: <?php echo $weatherData['main']['humidity']; ?>%</p>
            <p>Wind Speed: <?php echo $weatherData['wind']['speed']; ?> m/s</p>
          </div>
        </div>
      <?php else: ?>
        <p>Weather data not available.</p>
      <?php endif; ?>
    </section>

    <section class="news">
      <h2>Latest News</h2>
      <?php if ($newsData && isset($newsData['status']) && $newsData['status'] === 'ok'): ?>
        <ul class="news-list">
          <?php foreach (array_slice($newsData['articles'], 0, 5) as $article): ?>
            <li>
              <a href="<?php echo htmlspecialchars($article['url']); ?>" target="_blank" rel="noopener noreferrer">
                <?php echo htmlspecialchars($article['title']); ?>
              </a>
              <p><small><?php echo htmlspecialchars($article['source']['name']); ?></small></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>News data not available.</p>
      <?php endif; ?>
    </section>

  </div>

  <footer>
    <p>Powered by OpenWeatherMap and NewsAPI</p>
  </footer>
</body>
</html>
