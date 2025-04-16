package com.recyclage.jfxrecyclage.controllers;

import com.recyclage.jfxrecyclage.models.User;
import com.recyclage.jfxrecyclage.services.UserService;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

import java.io.IOException;
import java.sql.SQLException;

public class AuthController {
    @FXML private TextField emailField;
    @FXML private PasswordField passwordField;
    
    private final UserService userService = new UserService();
    private static User currentUser;

    @FXML
    private void handleLogin() throws IOException {
        String email = emailField.getText();
        String password = passwordField.getText();
        
        try {
            User user = userService.authenticate(email, password);
            if (user != null) {
                currentUser = user;
                loadMainView();
            } else {
                showAlert("Erreur d'authentification", "Email ou mot de passe incorrect");
            }
        } catch (SQLException e) {
            showAlert("Erreur de base de données", e.getMessage());
        }
    }

    @FXML
    private void handleRegisterNavigation() throws IOException {
        Parent root = FXMLLoader.load(getClass().getResource("/com/recyclage/jfxrecyclage/views/auth/register.fxml"));
        Stage stage = (Stage) emailField.getScene().getWindow();
        stage.setScene(new Scene(root));
    }

    private void loadMainView() throws IOException {
        Parent root = FXMLLoader.load(getClass().getResource("/com/recyclage/jfxrecyclage/main.fxml"));
        Stage stage = (Stage) emailField.getScene().getWindow();
        stage.setScene(new Scene(root));
        stage.setTitle("EcoRecyclage - Tableau de bord");
    }

    public static User getCurrentUser() {
        return currentUser;
    }

    private void showAlert(String title, String message) {
        javafx.scene.control.Alert alert = new javafx.scene.control.Alert(javafx.scene.control.Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}