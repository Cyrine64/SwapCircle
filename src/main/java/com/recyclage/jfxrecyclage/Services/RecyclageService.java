package com.recyclage.jfxrecyclage.services;

import com.recyclage.jfxrecyclage.models.Recyclage;
import com.recyclage.jfxrecyclage.models.TypeRecyclage;
import com.recyclage.jfxrecyclage.models.User;

import java.sql.*;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;

public class RecyclageService {
    public void addRecyclage(Recyclage recyclage) throws SQLException {
        String sql = "INSERT INTO Recyclage (type, commentaire, date_creation, id_user) VALUES (?, ?, ?, ?)";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            
            stmt.setString(1, recyclage.getType().name()); // Stocke le nom de l'enum
            stmt.setString(2, recyclage.getCommentaire());
            stmt.setTimestamp(3, Timestamp.valueOf(recyclage.getDateCreation()));
            stmt.setInt(4, recyclage.getIdUser());
            
            stmt.executeUpdate();
            
            try (ResultSet generatedKeys = stmt.getGeneratedKeys()) {
                if (generatedKeys.next()) {
                    recyclage.setId(generatedKeys.getInt(1));
                }
            }
        }
    }

    public List<Recyclage> getAllRecyclages() throws SQLException {
        List<Recyclage> recyclages = new ArrayList<>();
        String sql = "SELECT r.*, u.nom, u.prenom FROM Recyclage r JOIN User u ON r.id_user = u.id";
        
        try (Connection conn = DatabaseService.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {
            
            while (rs.next()) {
                Recyclage recyclage = mapResultSetToRecyclage(rs);
                recyclages.add(recyclage);
            }
        }
        return recyclages;
    }

    public List<Recyclage> getRecyclagesByUser(int userId) throws SQLException {
        List<Recyclage> recyclages = new ArrayList<>();
        String sql = "SELECT r.*, u.nom, u.prenom FROM Recyclage r JOIN User u ON r.id_user = u.id WHERE r.id_user = ?";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            
            stmt.setInt(1, userId);
            
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    Recyclage recyclage = mapResultSetToRecyclage(rs);
                    recyclages.add(recyclage);
                }
            }
        }
        return recyclages;
    }

    public Recyclage getRecyclageById(int id) throws SQLException {
        String sql = "SELECT r.*, u.nom, u.prenom FROM Recyclage r JOIN User u ON r.id_user = u.id WHERE r.id = ?";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            
            stmt.setInt(1, id);
            
            try (ResultSet rs = stmt.executeQuery()) {
                if (rs.next()) {
                    return mapResultSetToRecyclage(rs);
                }
            }
        }
        return null;
    }
/*
    public void updateRecyclage(Recyclage recyclage) throws SQLException {
        String sql = "UPDATE Recyclage SET type = ?, commentaire = ? WHERE id = ?";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            
            stmt.setString(1, recyclage.getType().name());
            stmt.setString(2, recyclage.getCommentaire());
            stmt.setInt(3, recyclage.getId());
            
            stmt.executeUpdate();
        }
    }*/
    
    public void updateRecyclage(Recyclage recyclage) throws SQLException {
    String sql = "UPDATE Recyclage SET type = ?, commentaire = ?, date_creation = ?, id_user = ? WHERE id = ?";
    
    try (Connection conn = DatabaseService.getConnection();
         PreparedStatement stmt = conn.prepareStatement(sql)) {
        
        stmt.setString(1, recyclage.getType().name());
        stmt.setString(2, recyclage.getCommentaire());
        stmt.setTimestamp(3, Timestamp.valueOf(recyclage.getDateCreation()));
        stmt.setInt(4, recyclage.getUser().getId()); // Mise à jour de l'utilisateur
        stmt.setInt(5, recyclage.getId());
        
        stmt.executeUpdate();
    }
}

    public void deleteRecyclage(int id) throws SQLException {
        String sql = "DELETE FROM Recyclage WHERE id = ?";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            
            stmt.setInt(1, id);
            stmt.executeUpdate();
        }
    }

    private Recyclage mapResultSetToRecyclage(ResultSet rs) throws SQLException {
        Recyclage recyclage = new Recyclage();
        recyclage.setId(rs.getInt("id"));
        
        // Conversion du String vers l'enum TypeRecyclage
        TypeRecyclage type = TypeRecyclage.valueOf(rs.getString("type"));
        recyclage.setType(type);
        
        recyclage.setCommentaire(rs.getString("commentaire"));
        recyclage.setDateCreation(rs.getTimestamp("date_creation").toLocalDateTime());
        recyclage.setIdUser(rs.getInt("id_user"));
        
        // Création d'un objet User simplifié
        User user = new User();
        user.setId(rs.getInt("id_user"));
        user.setNom(rs.getString("nom"));
        user.setPrenom(rs.getString("prenom"));
        recyclage.setUser(user);
        
        return recyclage;
    }

    public List<Recyclage> getRecyclagesByType(TypeRecyclage type) throws SQLException {
        List<Recyclage> recyclages = new ArrayList<>();
        String sql = "SELECT r.*, u.nom, u.prenom FROM Recyclage r JOIN User u ON r.id_user = u.id WHERE r.type = ?";
        
        try (Connection conn = DatabaseService.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            
            stmt.setString(1, type.name());
            
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    Recyclage recyclage = mapResultSetToRecyclage(rs);
                    recyclages.add(recyclage);
                }
            }
        }
        return recyclages;
    }
}