package com.recyclage.jfxrecyclage.controllers;

import com.recyclage.jfxrecyclage.models.User;
import com.recyclage.jfxrecyclage.services.WeatherService;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Label;

import java.net.URL;
import java.util.ResourceBundle;
import javafx.application.HostServices;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class MainController implements Initializable {
    @FXML private Label welcomeLabel;
    @FXML private Label weatherLabel;  
    
       private HostServices hostServices;

    public void setHostServices(HostServices hostServices) {
        this.hostServices = hostServices;
    }
    
    @Override
    public void initialize(URL location, ResourceBundle resources) {
        // Récupération de l'utilisateur actuel
        User currentUser = AuthController.getCurrentUser();
        if (currentUser != null) {
            welcomeLabel.setText("Bienvenue, " + currentUser.getPrenom() + " " + currentUser.getNom());
        } else {
            welcomeLabel.setText("Administrateur, ");
        }

        // Affichage de la météo
        try {
            String weather = WeatherService.getWeather("Paris,FR");  // Météo pour Ariana
            weatherLabel.setText(weather);  // Mise à jour du Label météo
        } catch (Exception e) {
            weatherLabel.setText("Météo indisponible");
            e.printStackTrace();
        }
    }
    
    
    
    @FXML
    private void handleRecyclageManagement() throws Exception {
        loadView("/com/recyclage/jfxrecyclage/recyclage-list.fxml");
    }
    
    @FXML
    private void handleTutorielManagement() throws Exception {
        loadView("/com/recyclage/jfxrecyclage/tutoriel-list.fxml");
    }
    
    @FXML
    private void handleLogout() throws Exception {
      //  AuthController.setCurrentUser(null);
        loadView("/com/recyclage/jfxrecyclage/login2.fxml");
    }
    
    @FXML
private void handleDashboard() throws Exception {
    loadView("/com/recyclage/jfxrecyclage/dashboard.fxml");
}

    /*private void loadView(String fxmlPath) throws Exception {
        javafx.scene.Parent root = javafx.fxml.FXMLLoader.load(getClass().getResource(fxmlPath));
        javafx.stage.Stage stage = (javafx.stage.Stage) welcomeLabel.getScene().getWindow();
        stage.setScene(new javafx.scene.Scene(root));
        stage.show();
    }*/
    
    private void loadView(String fxmlPath) throws Exception {
    FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlPath));
    Parent root = loader.load();

    Object controller = loader.getController();

    // Injection de HostServices si le contrôleur le supporte
    if (controller instanceof RecyclingController) {
        ((RecyclingController) controller).setHostServices(hostServices);
    }

    Stage stage = (Stage) welcomeLabel.getScene().getWindow();
    stage.setScene(new Scene(root));
    stage.show();
}

    
}
