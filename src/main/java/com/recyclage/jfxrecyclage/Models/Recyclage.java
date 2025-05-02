package com.recyclage.jfxrecyclage.models;

import java.text.SimpleDateFormat;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

public class Recyclage {
    private int id;
    private TypeRecyclage type;
    private String commentaire;
    private LocalDateTime dateCreation;
    private int idUser;
    private User user; // Référence à l'objet User complet

    // Constructeurs
    public Recyclage() {
        this.dateCreation = LocalDateTime.now();
    }

    public Recyclage(TypeRecyclage type, String commentaire, int idUser) {
        this();
        this.type = type;
        this.commentaire = commentaire;
        this.idUser = idUser;
    }

    public Recyclage(TypeRecyclage type, String commentaire, User user) {
        this();
        this.type = type;
        this.commentaire = commentaire;
        setUser(user); // cela met aussi à jour idUser
    }

    // Getters et Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public TypeRecyclage getType() {
        return type;
    }

    public void setType(TypeRecyclage type) {
        this.type = type;
    }

    public String getCommentaire() {
        return commentaire;
    }

    public void setCommentaire(String commentaire) {
        this.commentaire = commentaire;
    }

    public LocalDateTime getDateCreation() {
        return dateCreation;
    }

    public void setDateCreation(LocalDateTime dateCreation) {
        this.dateCreation = dateCreation;
    }

    public int getIdUser() {
        return idUser;
    }

    public void setIdUser(int idUser) {
        this.idUser = idUser;
    }

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
        if (user != null) {
            this.idUser = user.getId();
        }
    }
/*public String getFormattedDate() {
        SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy");
        return sdf.format(dateCreation);
    }
*/
public String getFormattedDate() {
    DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd/MM/yyyy");
    return dateCreation.format(formatter);
}

    @Override
    public String toString() {
        return type.getLibelle() + " - " + getFormattedDate();
    }
}
