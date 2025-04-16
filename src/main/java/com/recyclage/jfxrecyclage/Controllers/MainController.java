package com.recyclage.jfxrecyclage.controllers;

import com.recyclage.jfxrecyclage.models.User;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Label;

import java.net.URL;
import java.util.ResourceBundle;

public class MainController implements Initializable {
    @FXML private Label welcomeLabel;
    
    
    
    @Override
    public void initialize(URL location, ResourceBundle resources) {
        User currentUser = AuthController.getCurrentUser();
        if( currentUser != null)
        {
        welcomeLabel.setText("Bienvenue, " + currentUser.getPrenom() + " " + currentUser.getNom());
        }
        else
        {
                    welcomeLabel.setText("Administrateur, ");

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
    
    private void loadView(String fxmlPath) throws Exception {
        javafx.scene.Parent root = javafx.fxml.FXMLLoader.load(getClass().getResource(fxmlPath));
        javafx.stage.Stage stage = (javafx.stage.Stage) welcomeLabel.getScene().getWindow();
        stage.setScene(new javafx.scene.Scene(root));
        stage.show();
    }
}
