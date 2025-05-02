package com.recyclage.jfxrecyclage.controllers;

import com.recyclage.jfxrecyclage.services.RecyclageService;
import java.io.IOException;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.chart.PieChart;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.scene.control.Alert;

import java.net.URL;
import java.sql.SQLException;
import java.util.Map;
import java.util.ResourceBundle;
import javafx.event.ActionEvent;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class DashboardController implements Initializable {

    @FXML
    private PieChart recyclagePieChart;

    private final RecyclageService recyclageService = new RecyclageService();

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        try {
            // Charger les données dès l'initialisation
            loadRecyclagePieChart();
        } catch (Exception e) {
            showAlert("Erreur", "Erreur lors du chargement du graphique: " + e.getMessage());
        }
    }

    private void loadRecyclagePieChart() throws SQLException {
        Map<String, Integer> typeCounts = recyclageService.getRecyclageCountsByType();
        ObservableList<PieChart.Data> pieChartData = FXCollections.observableArrayList();

        typeCounts.forEach((type, count) -> {
            pieChartData.add(new PieChart.Data(type, count));
        });

        // Configuration du PieChart
        recyclagePieChart.setData(pieChartData);
        recyclagePieChart.setTitle("Répartition des recyclages par type");
        recyclagePieChart.setLegendVisible(true);
        recyclagePieChart.setLabelsVisible(true);
        
        // Style optionnel pour mieux voir les segments
        recyclagePieChart.getData().forEach(data -> {
            data.nameProperty().set(data.getName() + " (" + (int)data.getPieValue() + ")");
        });
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
