package com.recyclage.jfxrecyclage.services;

import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import com.google.gson.JsonElement;

import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

public class WeatherService {

    private static final String API_KEY = "1742576e4429a6d6f871623a07a557b2";  // Remplace par ta clé API
    private static final String BASE_URL = "http://api.openweathermap.org/data/2.5/weather";

    public static String getWeather(String city) throws Exception {
        String urlString = BASE_URL + "?q=" + city + "&appid=" + API_KEY + "&units=metric&lang=fr";  // Unités en Celsius et langue en français
        URL url = new URL(urlString);

        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        conn.setRequestMethod("GET");

        try (InputStreamReader reader = new InputStreamReader(conn.getInputStream())) {
JsonElement element = JsonParser.parseReader(reader);
JsonObject jsonResponse = element.getAsJsonObject();

            // Extraire les informations spécifiques de la réponse JSON
            JsonObject main = jsonResponse.getAsJsonObject("main");
            double temp = main.get("temp").getAsDouble();
            String description = jsonResponse.getAsJsonArray("weather").get(0).getAsJsonObject().get("description").getAsString();

            return String.format("Température: %.2f°C\nDescription: %s", temp, description);
        }
    }
}
