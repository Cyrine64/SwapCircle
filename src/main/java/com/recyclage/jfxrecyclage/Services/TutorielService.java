package com.recyclage.jfxrecyclage.services;

import com.recyclage.jfxrecyclage.models.Recyclage;
import com.recyclage.jfxrecyclage.models.Tutoriel;
import com.recyclage.jfxrecyclage.models.TypeRecyclage;
import com.recyclage.jfxrecyclage.models.User;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class TutorielService {
    public void addTutoriel(Tutoriel tutoriel) throws SQLException {
        String sql = "INSERT INTO Tutoriel (name, description, url_video, id_user, id_recyclage) VALUES (?, ?, ?, ?, ?)";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            
            stmt.setString(1, tutoriel.getName());
            stmt.setString(2, tutoriel.getDescription());
            stmt.setString(3, tutoriel.getUrlVideo());
            stmt.setInt(4, tutoriel.getIdUser());
            stmt.setInt(5, tutoriel.getIdRecyclage());
            
            stmt.executeUpdate();
            
            try (ResultSet generatedKeys = stmt.getGeneratedKeys()) {
                if (generatedKeys.next()) {
                    tutoriel.setId(generatedKeys.getInt(1));
                }
            }
        }
    }

public List<Tutoriel> getAllTutoriels() throws SQLException {
    List<Tutoriel> tutoriels = new ArrayList<>();
    String sql = "SELECT t.*, u.nom, u.prenom, r.id as recyclage_id, r.type, r.commentaire, r.date_creation, r.id_user as recyclage_user_id " +
                 "FROM Tutoriel t " +
                 "JOIN User u ON t.id_user = u.id " +
                 "JOIN Recyclage r ON t.id_recyclage = r.id";
    
    try (Connection conn = DatabaseService.getConnection();
         Statement stmt = conn.createStatement();
         ResultSet rs = stmt.executeQuery(sql)) {
        
        while (rs.next()) {
            Tutoriel tutoriel = mapResultSetToTutoriel(rs);
            tutoriels.add(tutoriel);
        }
    }
    return tutoriels;
}

private Tutoriel mapResultSetToTutoriel(ResultSet rs) throws SQLException {
    Tutoriel tutoriel = new Tutoriel();
    tutoriel.setId(rs.getInt("id"));
    tutoriel.setName(rs.getString("name"));
    tutoriel.setDescription(rs.getString("description"));
    tutoriel.setUrlVideo(rs.getString("url_video"));
    tutoriel.setIdUser(rs.getInt("id_user"));
    tutoriel.setIdRecyclage(rs.getInt("id_recyclage"));
    
    // Création de l'objet User associé
    User user = new User();
    user.setId(rs.getInt("id_user"));
    user.setNom(rs.getString("nom"));
    user.setPrenom(rs.getString("prenom"));
    tutoriel.setUser(user);
    
    // Création de l'objet Recyclage complet avec l'enum TypeRecyclage
    Recyclage recyclage = new Recyclage();
    recyclage.setId(rs.getInt("recyclage_id"));
recyclage.setType(TypeRecyclage.valueOf(rs.getString("type").toUpperCase())); // Conversion String -> enum (en majuscules)
    recyclage.setCommentaire(rs.getString("commentaire"));
    recyclage.setDateCreation(rs.getTimestamp("date_creation").toLocalDateTime());
    recyclage.setIdUser(rs.getInt("recyclage_user_id"));
    
    // Si vous avez besoin des infos user dans le recyclage
    if (tutoriel.getUser() != null) {
        recyclage.setUser(tutoriel.getUser());
    }
    
    tutoriel.setRecyclage(recyclage);
    
    return tutoriel;
}

    public List<Tutoriel> getTutorielsByRecyclage(int recyclageId) throws SQLException {
        List<Tutoriel> tutoriels = new ArrayList<>();
        String sql = "SELECT * FROM Tutoriel WHERE id_recyclage = ?";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            
            stmt.setInt(1, recyclageId);
            
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    Tutoriel tutoriel = new Tutoriel();
                    tutoriel.setId(rs.getInt("id"));
                    tutoriel.setName(rs.getString("name"));
                    tutoriel.setDescription(rs.getString("description"));
                    tutoriel.setUrlVideo(rs.getString("url_video"));
                    tutoriel.setIdUser(rs.getInt("id_user"));
                    tutoriel.setIdRecyclage(rs.getInt("id_recyclage"));
                    tutoriels.add(tutoriel);
                }
            }
        }
        return tutoriels;
    }

public void updateTutoriel(Tutoriel tutoriel) throws SQLException {
    String sql = "UPDATE Tutoriel SET name = ?, description = ?, url_video = ?, id_recyclage = ?, id_user = ? WHERE id = ?";
    
    try (Connection conn = DatabaseService.getConnection();
         PreparedStatement stmt = conn.prepareStatement(sql)) {
        
        stmt.setString(1, tutoriel.getName());
        stmt.setString(2, tutoriel.getDescription());
        stmt.setString(3, tutoriel.getUrlVideo());
        stmt.setInt(4, tutoriel.getRecyclage().getId());
        stmt.setInt(5, tutoriel.getUser().getId()); // Ligne cruciale pour l'utilisateur
        stmt.setInt(6, tutoriel.getId());
        
        stmt.executeUpdate();
    }
}

    public void deleteTutoriel(int id) throws SQLException {
        String sql = "DELETE FROM Tutoriel WHERE id = ?";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            
            stmt.setInt(1, id);
            stmt.executeUpdate();
        }
    }
    
    public List<Tutoriel> getTutorielsByUser(int userId) throws SQLException {
    List<Tutoriel> tutoriels = new ArrayList<>();
    String sql = "SELECT t.*, u.nom, u.prenom, r.id as recyclage_id, r.type, r.commentaire, r.date_creation, r.id_user as recyclage_user_id " +
                 "FROM Tutoriel t " +
                 "JOIN User u ON t.id_user = u.id " +
                 "JOIN Recyclage r ON t.id_recyclage = r.id " +
                 "WHERE t.id_user = ?";
    
    try (Connection conn = DatabaseService.getConnection();
         PreparedStatement stmt = conn.prepareStatement(sql)) {
        
        stmt.setInt(1, userId);
        
        try (ResultSet rs = stmt.executeQuery()) {
            while (rs.next()) {
                Tutoriel tutoriel = mapResultSetToTutoriel(rs);
                tutoriels.add(tutoriel);
            }
        }
    }
    return tutoriels;
}

}