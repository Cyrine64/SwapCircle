package com.recyclage.jfxrecyclage.controllers;

import com.recyclage.jfxrecyclage.models.Recyclage;
import com.recyclage.jfxrecyclage.models.TypeRecyclage;
import com.recyclage.jfxrecyclage.models.User;
import com.recyclage.jfxrecyclage.services.RecyclageService;
import com.recyclage.jfxrecyclage.services.UserService;
import java.io.IOException;
import java.time.LocalDate;
import java.time.LocalDateTime;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import java.net.URL;
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;
import javafx.beans.property.SimpleStringProperty;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class RecyclageController implements Initializable {
    @FXML private TableView<Recyclage> recyclageTable;
    @FXML private TableColumn<Recyclage, Integer> idColumn;
    @FXML private TableColumn<Recyclage, String> typeColumn;
    @FXML private TableColumn<Recyclage, String> commentaireColumn;
    @FXML private TableColumn<Recyclage, String> dateColumn;
    @FXML private TableColumn<Recyclage, String> userColumn;
    
    @FXML private ComboBox<TypeRecyclage> typeCombo;
    @FXML private ComboBox<User> userCombo;
    @FXML private TextArea commentaireField;
    @FXML private DatePicker datePicker;

    private final RecyclageService recyclageService = new RecyclageService();
    private final UserService userService = new UserService();
    private Recyclage selectedRecyclage;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        configureTable();
        configureForm();
        loadRecyclages();
        loadUsers();
        
        // Initialiser la date avec la date du jour
        datePicker.setValue(LocalDate.now());
    }
    
    private void configureTable() {
        idColumn.setCellValueFactory(new PropertyValueFactory<>("id"));
        typeColumn.setCellValueFactory(cellData -> 
            new SimpleStringProperty(cellData.getValue().getType().getLibelle()));
        commentaireColumn.setCellValueFactory(new PropertyValueFactory<>("commentaire"));
        dateColumn.setCellValueFactory(new PropertyValueFactory<>("formattedDate"));
        
        userColumn.setCellValueFactory(cellData -> {
            User user = cellData.getValue().getUser();
            return new SimpleStringProperty(user != null ? user.getNom() + " " + user.getPrenom() : "");
        });
        
        recyclageTable.getSelectionModel().selectedItemProperty().addListener(
            (obs, oldSelection, newSelection) -> selectRecyclage(newSelection));
    }
    
    private void configureForm() {
        typeCombo.setItems(FXCollections.observableArrayList(TypeRecyclage.values()));
    }
    
    private void loadUsers() {
        try {
            List<User> users = userService.getAllUsers();
            userCombo.setItems(FXCollections.observableArrayList(users));
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de charger les utilisateurs: " + e.getMessage());
        }
    }
    
    private void loadRecyclages() {
        try {
            recyclageTable.setItems(FXCollections.observableArrayList(
                recyclageService.getAllRecyclages()));
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de charger les recyclages: " + e.getMessage());
        }
    }
    
private void selectRecyclage(Recyclage recyclage) {
    selectedRecyclage = recyclage;
    if (recyclage != null) {
        typeCombo.setValue(recyclage.getType());
        commentaireField.setText(recyclage.getCommentaire());
        datePicker.setValue(recyclage.getDateCreation().toLocalDate());
        userCombo.setValue(recyclage.getUser()); // Cette ligne est cruciale
    }
}
    
    @FXML
    private void handleAddRecyclage() {
        if (userCombo.getValue() == null) {
            showAlert("Erreur", "Veuillez sélectionner un utilisateur");
            return;
        }
        
        try {
            Recyclage recyclage = new Recyclage();
            recyclage.setType(typeCombo.getValue());
            recyclage.setCommentaire(commentaireField.getText());
            
            // Gestion de la date
            LocalDate localDate = datePicker.getValue();
            LocalDateTime dateTime = localDate.atStartOfDay();
            recyclage.setDateCreation(dateTime);
            
            // Utilisateur sélectionné
            recyclage.setUser(userCombo.getValue());
            
            recyclageService.addRecyclage(recyclage);
            clearForm();
            loadRecyclages();
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible d'ajouter le recyclage: " + e.getMessage());
        }
    }
    /*
    @FXML
    private void handleUpdateRecyclage() {
        if (selectedRecyclage == null) {
            showAlert("Aucune sélection", "Veuillez sélectionner un recyclage à modifier");
            return;
        }
        
        try {
            selectedRecyclage.setType(typeCombo.getValue());
            selectedRecyclage.setCommentaire(commentaireField.getText());
            
            // Mise à jour de la date
            LocalDate localDate = datePicker.getValue();
            LocalDateTime dateTime = localDate.atStartOfDay();
            selectedRecyclage.setDateCreation(dateTime);
            
            // Mise à jour de l'utilisateur
            selectedRecyclage.setUser(userCombo.getValue());
            
            recyclageService.updateRecyclage(selectedRecyclage);
            loadRecyclages();
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de mettre à jour le recyclage: " + e.getMessage());
        }
    }
    */
    
    @FXML
private void handleUpdateRecyclage() {
    if (selectedRecyclage == null) {
        showAlert("Aucune sélection", "Veuillez sélectionner un recyclage à modifier");
        return;
    }
    
    try {
        selectedRecyclage.setType(typeCombo.getValue());
        selectedRecyclage.setCommentaire(commentaireField.getText());
        
        // Mise à jour de la date
        LocalDate localDate = datePicker.getValue();
        LocalDateTime dateTime = localDate.atStartOfDay();
        selectedRecyclage.setDateCreation(dateTime);
        
        // Mise à jour de l'utilisateur (ajout de cette ligne)
        selectedRecyclage.setUser(userCombo.getValue());
        
        recyclageService.updateRecyclage(selectedRecyclage);
        loadRecyclages();
    } catch (SQLException e) {
        showAlert("Erreur", "Impossible de mettre à jour le recyclage: " + e.getMessage());
    }
}
    @FXML
    private void handleDeleteRecyclage() {
        if (selectedRecyclage == null) {
            showAlert("Aucune sélection", "Veuillez sélectionner un recyclage à supprimer");
            return;
        }
        
        try {
            recyclageService.deleteRecyclage(selectedRecyclage.getId());
            clearForm();
            loadRecyclages();
        } catch (SQLException e) {
            showAlert("Erreur", "Impossible de supprimer le recyclage: " + e.getMessage());
        }
    }
    
    private void clearForm() {
        typeCombo.setValue(null);
        userCombo.setValue(null);
        commentaireField.clear();
        datePicker.setValue(LocalDate.now());
        selectedRecyclage = null;
    }
    
    private void showAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
    
    @FXML
    private void handleBackToDashboard() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/recyclage/jfxrecyclage/main.fxml"));
            Parent dashboardView = loader.load();

            Scene currentScene = typeCombo.getScene();
            Stage stage = (Stage) currentScene.getWindow();
            stage.setScene(new Scene(dashboardView));
        } catch (IOException e) {
            showAlert("Erreur", "Impossible de charger le dashboard : " + e.getMessage());
        }
    }
}

