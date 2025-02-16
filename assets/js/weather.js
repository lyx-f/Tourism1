// weather codes to descriptions
function getWeatherDescription(code) {
    const descriptions = {
        0: "Clear sky",
        1: "Mainly clear",
        2: "Partly cloudy",
        3: "Overcast",
        45: "Foggy",
        48: "Depositing rime fog",
        51: "Light drizzle",
        53: "Moderate drizzle",
        55: "Dense drizzle",
        56: "Light freezing drizzle",
        57: "Dense freezing drizzle",
        61: "Slight rain",
        63: "Moderate rain",
        65: "Heavy rain",
        66: "Light freezing rain",
        67: "Heavy freezing rain",
        80: "Slight rain showers",
        81: "Moderate rain showers",
        82: "Violent rain showers",
        95: "Thunderstorm",
        96: "Thunderstorm with slight hail",
        99: "Thunderstorm with heavy hail"
    };
    return descriptions[code] || "Unknown conditions";
}

// Function to fetch weather data from Open-Meteo
async function fetchWeather() {
    const latitude = 6.957455318696107; 
    const longitude = -126.22036233055836;  
    const apiUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current_weather=true`;

    try {
        const response = await fetch(apiUrl);
        const data = await response.json();
        
        // Extract temperature and weather code
        const temperature = data.current_weather.temperature;
        const weatherCode = data.current_weather.weathercode;
        const weatherDescription = getWeatherDescription(weatherCode);

        // Update HTML to show temperature and description
        const weatherElement = document.getElementById("weather");
        weatherElement.textContent = `${temperature}°C, ${weatherDescription}`;
    } catch (error) {
        console.error("Failed to fetch weather data", error);
        document.getElementById("weather").textContent = "Weather unavailable";
    }
}

// Fetch weather when the page loads
window.onload = fetchWeather;
