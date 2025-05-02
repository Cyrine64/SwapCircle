module com.recyclage.jfxrecyclage {
    requires javafx.controls;
    requires javafx.fxml;
    requires java.sql;
   // requires mysql.connector.java;
        requires com.google.gson; // 👈 Ajoute ceci
    requires java.net.http;  // Si tu utilises HttpURLConnection
        requires com.google.zxing; 
        requires com.google.zxing.javase; // 👈 Ajoute ceci
// 👈 Ajoute ceci
requires lombok;
requires java.desktop;
requires javafx.swing;
requires okhttp3;
requires org.json;



    opens com.recyclage.jfxrecyclage to javafx.fxml;
    opens com.recyclage.jfxrecyclage.controllers to javafx.fxml;
    opens com.recyclage.jfxrecyclage.models to javafx.base;
    opens com.recyclage.jfxrecyclage.services to javafx.fxml, javafx.base;  // Ajouté pour WeatherService

    exports com.recyclage.jfxrecyclage;
}
    