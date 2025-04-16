package com.recyclage.jfxrecyclage.controllers;

import com.recyclage.jfxrecyclage.models.Recyclage;
import com.recyclage.jfxrecyclage.models.Tutoriel;
import com.recyclage.jfxrecyclage.models.User;
import com.recyclage.jfxrecyclage.services.RecyclageService;
import com.recyclage.jfxrecyclage.services.TutorielService;
import com.recyclage.jfxrecyclage.services.UserService;
import java.io.IOException;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import java.net.URL;
import java.sql.SQLException;
import java.util.ResourceBundle;
import javafx.beans.property.SimpleStringProperty;
import javafx.event.ActionEvent;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class TutorielController implements Initializable {
    @FXML private TableView<Tutoriel> tutorielTable;
    @FXML private TableColumn<Tutoriel, String> nameColumn;
    @FXML private TableColumn<Tutoriel, String> descriptionColumn;
    @FXML private TableColumn<Tutoriel, String> videoColumn;
    @FXML private TableColumn<Tutoriel, String> recyclageColumn;
    @FXML private TableColumn<Tutoriel, String> userColumn;
    
    @FXML private TextField nameField;
    @FXML private TextArea descriptionField;
    @FXML private TextField videoUrlField;
    @FXML private ComboBox<Recyclage> recyclageCombo;
    @FXML private ComboBox<User> userCombo;

    private final TutorielService tutorielService = new TutorielService();
    private final RecyclageService recyclageService = new RecyclageService();
    private final UserService userService = new UserService();
    private Tutoriel selectedTutoriel;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        configureTable();
        loadRecyclagesForCombo();
        loadUsers();
        loadTutoriels();
    }
    
    private void configureTable() {
        nameColumn.setCellValueFactory(new PropertyValueFactory<>("name"));
        descriptionColumn.setCellValueFactory(new PropertyValueFactory<>("description"));
        videoColumn.setCellValueFactory(new PropertyValueFactory<>("urlVideo"));
        
        // Colonne Recyclage
        recyclageColumn.setCellValueFactory(cellData -> {
            Recyclage recyclage = cellData.getValue().getRecyclage();
            return new SimpleStringProperty(recyclage != null ? recyclage.getType().getLibelle() : "");
        });
        
        // Colonne Utilisateur
        userColumn.setCellValueFactory(cellData -> {
            User user = cellData.getValue().getUser();
            return new SimpleStringProperty(user != null ? user.getNom() + " " + user.getPrenom() : "");
        });
        
        tutorielTable.getSelectionModel().selectedItemProperty().addListener(
            (obs, oldSelection, newSelection) -> selectTutoriel(newSelection));
    }
    
    private void loadRecyclagesForCombo() {
        try {
            recyclageCombo.setItems(FXCollections.observableArrayList(
                recyclageService.getAllRecyclages()));
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de charger les recyclages: " + e.getMessage());
        }
    }
    
    private void loadUsers() {
        try {
            userCombo.setItems(FXCollections.observableArrayList(
                userService.getAllUsers()));
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de charger les utilisateurs: " + e.getMessage());
        }
    }
    
    private void loadTutoriels() {
        try {
            tutorielTable.setItems(FXCollections.observableArrayList(
                tutorielService.getAllTutoriels()));
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de charger les tutoriels: " + e.getMessage());
        }
    }
    
    private void selectTutoriel(Tutoriel tutoriel) {
        selectedTutoriel = tutoriel;
        if (tutoriel != null) {
            nameField.setText(tutoriel.getName());
            descriptionField.setText(tutoriel.getDescription());
            videoUrlField.setText(tutoriel.getUrlVideo());
            recyclageCombo.setValue(tutoriel.getRecyclage());
            userCombo.setValue(tutoriel.getUser());
            
        }
    }
    
    @FXML
    private void handleAddTutoriel() {
        if (recyclageCombo.getValue() == null || userCombo.getValue() == null) {
            showAlert("Erreur", "Veuillez sélectionner un recyclage et un utilisateur");
            return;
        }
        
        try {
            Tutoriel tutoriel = new Tutoriel();
            tutoriel.setName(nameField.getText());
            tutoriel.setDescription(descriptionField.getText());
            tutoriel.setUrlVideo(videoUrlField.getText());
            tutoriel.setRecyclage(recyclageCombo.getValue());
            tutoriel.setUser(userCombo.getValue());
            
            tutorielService.addTutoriel(tutoriel);
            clearForm();
            loadTutoriels();
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible d'ajouter le tutoriel: " + e.getMessage());
        }
    }
    
   /* @FXML
    private void handleUpdateTutoriel() {
        if (selectedTutoriel == null) {
            showAlert("Aucune sélection", "Veuillez sélectionner un tutoriel à modifier");
            return;
        }
        
        try {
            selectedTutoriel.setName(nameField.getText());
            selectedTutoriel.setDescription(descriptionField.getText());
            selectedTutoriel.setUrlVideo(videoUrlField.getText());
            selectedTutoriel.setRecyclage(recyclageCombo.getValue());
            selectedTutoriel.setUser(userCombo.getValue());

            tutorielService.updateTutoriel(selectedTutoriel);
            loadTutoriels();
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de mettre à jour le tutoriel: " + e.getMessage());
        }
    }*/
    
    @FXML
private void handleUpdateTutoriel() {
    if (selectedTutoriel == null) {
        showAlert("Aucune sélection", "Veuillez sélectionner un tutoriel à modifier");
        return;
    }
    
    // Debug
    System.out.println("Avant modification - User: " + selectedTutoriel.getUser());
    System.out.println("Nouvel user sélectionné: " + userCombo.getValue());
    
    try {
        selectedTutoriel.setName(nameField.getText());
        selectedTutoriel.setDescription(descriptionField.getText());
        selectedTutoriel.setUrlVideo(videoUrlField.getText());
        selectedTutoriel.setRecyclage(recyclageCombo.getValue());
        selectedTutoriel.setUser(userCombo.getValue()); // Cette ligne est cruciale

        // Debug
        System.out.println("Après modification - User: " + selectedTutoriel.getUser());
        
        tutorielService.updateTutoriel(selectedTutoriel);
        loadTutoriels();
    } catch (SQLException e) {
        showAlert("Erreur", "Impossible de mettre à jour le tutoriel: " + e.getMessage());
        e.printStackTrace();
    }
}
    
    @FXML
    private void handleDeleteTutoriel() {
        if (selectedTutoriel == null) {
            showAlert("Aucune sélection", "Veuillez sélectionner un tutoriel à supprimer");
            return;
        }
        
        try {
            tutorielService.deleteTutoriel(selectedTutoriel.getId());
            clearForm();
            loadTutoriels();
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de supprimer le tutoriel: " + e.getMessage());
        }
    }
    
    private void clearForm() {
        nameField.clear();
        descriptionField.clear();
        videoUrlField.clear();
        recyclageCombo.setValue(null);
        userCombo.setValue(null);
        selectedTutoriel = null;
    }
    
    private void showAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
    
    @FXML
    private void handleBackToDashboard(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/recyclage/jfxrecyclage/main.fxml"));
            Parent dashboardView = loader.load();

            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            Scene scene = new Scene(dashboardView);
            stage.setScene(scene);
            stage.show();
        } catch (IOException e) {
            showAlert("Erreur", "Impossible de charger le dashboard: " + e.getMessage());
        }
    }
}