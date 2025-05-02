/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.recyclage.jfxrecyclage.Controllers;

import com.recyclage.jfxrecyclage.services.WeatherService;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;

/**
 *
 * @author ASUS
 */
public class WeatherApp {
    
    
    /*@FXML
    private TextField cityInput;

    @FXML
    private Button fetchButton;

    @FXML
    private Label weatherOutput;

    @FXML
    public void initialize() {
        fetchButton.setOnAction(e -> {
            String city = cityInput.getText();
            try {
                String weather = WeatherService.getWeather(city);
                weatherOutput.setText(weather);
            } catch (Exception ex) {
                weatherOutput.setText("Erreur : " + ex.getMessage());
            }
        });
    }*/
    
    
  @FXML
    private Label weatherLabel;

    @FXML
    public void initialize() {
        try {
            // Appel direct avec la ville "Tunis"
String weatherInfo = WeatherService.getWeather("Ariana,TN");
            weatherLabel.setText(weatherInfo);
        } catch (Exception e) {
            weatherLabel.setText("Erreur de chargement météo : " + e.getMessage());
            e.printStackTrace();
        }
    }
}
