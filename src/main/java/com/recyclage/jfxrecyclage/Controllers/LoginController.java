package com.recyclage.jfxrecyclage.controllers;

import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;

public class LoginController {

    @FXML private TextField usernameField;
    @FXML private PasswordField passwordField;
    @FXML private Label errorLabel;

    // Identifiants "admin" statiques
    private final String ADMIN_USERNAME = "hamza";
    private final String ADMIN_PASSWORD = "1234";

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
}
