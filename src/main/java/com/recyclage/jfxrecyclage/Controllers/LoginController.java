package com.recyclage.jfxrecyclage.controllers;

import com.google.zxing.BarcodeFormat;
import com.google.zxing.qrcode.QRCodeWriter;
import javafx.fxml.FXML;
import javafx.scene.control.TextField;
import javafx.scene.control.PasswordField;
import javafx.scene.control.Label;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.image.WritableImage;
import javafx.embed.swing.SwingFXUtils;
import javafx.fxml.Initializable;

import java.awt.*;
import java.awt.image.BufferedImage;
import java.io.IOException;
import java.net.URL;
import java.util.ResourceBundle;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import org.json.JSONArray;
import org.json.JSONObject;

public class LoginController implements Initializable {

    @FXML private TextField usernameField;
    @FXML private PasswordField passwordField;
    @FXML private Label errorLabel;
    @FXML private ImageView qrCodeImageView;

    private final String ADMIN_USERNAME = "hamza";
    private final String ADMIN_PASSWORD = "1234";
    
    @FXML private Label countryInfoLabel;
@FXML private Label countryNameLabel;
@FXML private Label capitalLabel;
@FXML private ImageView flagImageView;


@Override
public void initialize(URL location, ResourceBundle resources) {
    System.out.println("Initialisation du contrôleur..."); // Debug
    if (qrCodeImageView != null) {
        System.out.println("qrCodeImageView est initialisé"); // Debug
        generateQRCode("Welcome to my app");
    } else {
        System.out.println("qrCodeImageView est NULL !"); // Debug
    }
     loadCountryInfo();
}

    private void generateQRCode(String text) {
        try {
            QRCodeWriter qrCodeWriter = new QRCodeWriter();
            com.google.zxing.common.BitMatrix bitMatrix = qrCodeWriter.encode(text, BarcodeFormat.QR_CODE, 150, 150);

            BufferedImage bufferedImage = new BufferedImage(150, 150, BufferedImage.TYPE_INT_RGB);
            for (int x = 0; x < 150; x++) {
                for (int y = 0; y < 150; y++) {
                    bufferedImage.setRGB(x, y, bitMatrix.get(x, y) ? Color.BLACK.getRGB() : Color.WHITE.getRGB());
                }
            }

            WritableImage image = SwingFXUtils.toFXImage(bufferedImage, null);
            qrCodeImageView.setImage(image);

        } catch (Exception e) {
            e.printStackTrace();
            errorLabel.setText("Erreur lors de la génération du QR code");
        }
    }

    @FXML
    private void handleLogin() {
        String username = usernameField.getText();
        String password = passwordField.getText();

        if (username.equals(ADMIN_USERNAME) && password.equals(ADMIN_PASSWORD)) {
            try {
                FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/recyclage/jfxrecyclage/main.fxml"));
                Parent root = loader.load();
                Stage stage = (Stage) usernameField.getScene().getWindow();
                stage.setScene(new Scene(root));
                stage.setTitle("Dashboard Admin");
            } catch (IOException e) {
                errorLabel.setText("Erreur de chargement du tableau de bord.");
                e.printStackTrace();
            }
        } else {
            errorLabel.setText("Nom d'utilisateur ou mot de passe incorrect.");
        }
    }
    
    


    // Récupère les informations de la Tunisie depuis l'API et les affiche dans le Label
    private void loadCountryInfo() {
    try {
        String urlString = "https://restcountries.com/v3.1/name/tunisia";
        URL url = new URL(urlString);
        HttpURLConnection connection = (HttpURLConnection) url.openConnection();
        connection.setRequestMethod("GET");
        connection.setRequestProperty("Accept", "application/json");

        BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
        StringBuilder response = new StringBuilder();
        String inputLine;
        while ((inputLine = in.readLine()) != null) {
            response.append(inputLine);
        }
        in.close();

        JSONArray countries = new JSONArray(response.toString());
        JSONObject country = countries.getJSONObject(0);

        String name = country.getJSONObject("name").getString("common");
        String capital = country.getJSONArray("capital").getString(0);
        String region = country.getString("region");
        String population = String.valueOf(country.getInt("population"));
        String flagUrl = country.getJSONObject("flags").getString("png");

        // Afficher dans les labels
        if (countryInfoLabel != null) {
            String countryInfo = String.format("Nom: %s\nCapitale: %s\nRégion: %s\nPopulation: %s", 
                                               name, capital, region, population);
            countryInfoLabel.setText(countryInfo);
        }

        if (countryNameLabel != null) countryNameLabel.setText("Pays : " + name);
        if (capitalLabel != null) capitalLabel.setText("Capitale : " + capital);

        if (flagImageView != null) {
            Image flagImage = new Image(flagUrl, true);
            flagImageView.setImage(flagImage);
        }

    } catch (Exception e) {
        e.printStackTrace();
        if (countryInfoLabel != null) {
            countryInfoLabel.setText("Erreur lors de la récupération des informations.");
        }
    }
}

    
    
    
    
    
}
