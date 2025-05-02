package com.recyclage.jfxrecyclage.controllers;

import com.google.gson.*;
import com.recyclage.jfxrecyclage.models.RecyclingPoint;
import javafx.application.HostServices;
import javafx.application.Platform;
import javafx.concurrent.Task;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.stage.Stage;
import okhttp3.*;

import java.io.IOException;
import java.util.*;
import java.util.concurrent.*;
import com.recyclage.jfxrecyclage.services.OpenTrashService;

public class RecyclingController {

    @FXML private TextField latField;
    @FXML private TextField lonField;
    @FXML private TextField radiusField;
    @FXML private TableView<RecyclingPoint> pointsTable;
    @FXML private Label statusLabel;

    private final OpenTrashService service = new OpenTrashService();
    
    private HostServices hostServices;
    private Stage stage;

    public void setHostServices(HostServices hostServices) {
        this.hostServices = hostServices;
    }

    public void setStage(Stage stage) {
        this.stage = stage;
    }
    
    

    @FXML
    public void initialize() {
        // Configuration des colonnes
        pointsTable.getColumns().get(0).setCellValueFactory(new PropertyValueFactory<>("name"));
        pointsTable.getColumns().get(1).setCellValueFactory(new PropertyValueFactory<>("address"));

        // Style alterné des lignes
        pointsTable.setRowFactory(tv -> new TableRow<>() {
            @Override
            protected void updateItem(RecyclingPoint item, boolean empty) {
                super.updateItem(item, empty);
                if (item != null && !empty) {
                    if (getIndex() % 2 == 0) {
                        setStyle("-fx-background-color: #f9f9f9;");
                    } else {
                        setStyle("-fx-background-color: white;");
                    }
                } else {
                    setStyle(""); // reset
                }
            }
        });
    }

 @FXML
private void handleSearch() {
    try {
        double lat = Double.parseDouble(latField.getText());
        double lon = Double.parseDouble(lonField.getText());
        int radius = Integer.parseInt(radiusField.getText());

        updateStatus("Recherche en cours...");
        pointsTable.getItems().clear(); // Clear previous results

        Task<List<RecyclingPoint>> task = new Task<>() {
            @Override
            protected List<RecyclingPoint> call() throws Exception {
                try {
                    return service.getRecyclingPoints(lat, lon, radius);
                } catch (Exception e) {
                    Platform.runLater(() -> {
                        updateStatus("Erreur réseau");
                        showAlert("Erreur", "Échec de la connexion au serveur: " + e.getMessage());
                    });
                    throw e;
                }
            }
        };

        task.setOnSucceeded(e -> {
            List<RecyclingPoint> points = task.getValue();
            if (points.isEmpty()) {
                updateStatus("Aucun résultat trouvé");
                showAlert("Information", "Aucun point de recyclage trouvé dans ce rayon");
            } else {
                pointsTable.getItems().setAll(points);
                updateStatus(String.format("%d points trouvés", points.size()));
            }
        });

        new Thread(task).start();

    } catch (NumberFormatException e) {
        updateStatus("Coordonnées invalides");
        showAlert("Erreur", "Veuillez entrer des valeurs numériques valides");
    }
}

    @FXML
    private void handleViewOnMap() {
        RecyclingPoint selected = pointsTable.getSelectionModel().getSelectedItem();
        if (selected != null) {
            try {
                String url = String.format("https://www.openstreetmap.org/?mlat=%f&mlon=%f#map=17/%f/%f",
                        selected.getLatitude(),
                        selected.getLongitude(),
                        selected.getLatitude(),
                        selected.getLongitude());

                if (hostServices != null) {
                    hostServices.showDocument(url);
                } else {
                    showAlert("Erreur", "HostServices non défini.");
                }

            } catch (Exception e) {
                showAlert("Erreur", "Impossible d'ouvrir la carte");
            }
        } else {
            showAlert("Aucune sélection", "Veuillez sélectionner un point");
        }
    }

    @FXML
    private void handleClearResults() {
        pointsTable.getItems().clear();
        updateStatus("Prêt");
    }

    private void updateStatus(String message) {
        Platform.runLater(() -> statusLabel.setText(message));
    }

    private void showAlert(String title, String message) {
        Platform.runLater(() -> {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle(title);
            alert.setHeaderText(null);
            alert.setContentText(message);
            alert.showAndWait();
        });
    }

    // Classe de service interne
    private static class OpenTrashService {
        private static final String API_URL = "https://openrecycling.org/api/v1/locations";
        private final OkHttpClient client = new OkHttpClient();
        private final Gson gson = new Gson();

        public List<RecyclingPoint> getRecyclingPoints(double lat, double lon, int radius) throws IOException {
            String url = String.format("%s?lat=%f&lon=%f&radius=%d", API_URL, lat, lon, radius);

            Request request = new Request.Builder()
                    .url(url)
                    .addHeader("Accept", "application/json")
                    .build();

            try (Response response = client.newCall(request).execute()) {
                if (!response.isSuccessful()) {
                    throw new IOException("Erreur API: " + response.code());
                }

                String jsonData = response.body().string();
                return parseResponse(jsonData);
            }
        }

        private List<RecyclingPoint> parseResponse(String json) {
            List<RecyclingPoint> points = new ArrayList<>();
            JsonArray array = JsonParser.parseString(json).getAsJsonArray();

            for (JsonElement element : array) {
                JsonObject obj = element.getAsJsonObject();
                RecyclingPoint point = new RecyclingPoint(
                        obj.get("id").getAsString(),
                        obj.get("name").getAsString(),
                        obj.get("address").getAsString(),
                        obj.get("lat").getAsDouble(),
                        obj.get("lon").getAsDouble(),
                        obj.get("materials").getAsString().split(",")
                );
                points.add(point);
            }

            return points;
        }
    }
}
