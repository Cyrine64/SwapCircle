module com.recyclage.jfxrecyclage {
    requires javafx.controls;
    requires javafx.fxml;
    requires java.sql;
   // requires mysql.connector.java;
    
    opens com.recyclage.jfxrecyclage to javafx.fxml;
    opens com.recyclage.jfxrecyclage.controllers to javafx.fxml;
    opens com.recyclage.jfxrecyclage.models to javafx.base;
    
    exports com.recyclage.jfxrecyclage;
}