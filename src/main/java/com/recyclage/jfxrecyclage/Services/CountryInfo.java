package com.recyclage.jfxrecyclage.services;

import java.net.HttpURLConnection;
import java.net.URL;
import java.io.BufferedReader;
import java.io.InputStreamReader;

public class CountryInfo {
    public static void main(String[] args) {
        try {
            // URL pour récupérer les informations sur la Tunisie
            String urlString = "https://restcountries.com/v3.1/name/tunisia";
            URL url = new URL(urlString);

            // Ouvrir la connexion HTTP
            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setRequestMethod("GET");  // Méthode GET pour récupérer les données
            connection.setRequestProperty("Accept", "application/json");

            // Lire la réponse de l'API
            BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
            String inputLine;
            StringBuilder response = new StringBuilder();

            while ((inputLine = in.readLine()) != null) {
                response.append(inputLine);
            }
            in.close();

            // Afficher la réponse JSON
            System.out.println("Réponse JSON : ");
            System.out.println(response.toString());

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
