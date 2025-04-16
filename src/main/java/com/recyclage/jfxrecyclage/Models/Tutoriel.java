package com.recyclage.jfxrecyclage.models;

public class Tutoriel {
    private int id;
    private String name;
    private String description;
    private String urlVideo;
    private int idUser;
    private int idRecyclage;
    private User user; // Référence à l'objet User
    private Recyclage recyclage; // Référence à l'objet Recyclage

    // Constructeurs
    public Tutoriel() {
    }

    public Tutoriel(String name, String description, String urlVideo, int idUser, int idRecyclage) {
        this.name = name;
        this.description = description;
        this.urlVideo = urlVideo;
        this.idUser = idUser;
        this.idRecyclage = idRecyclage;
    }

    // Getters et Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getUrlVideo() {
        return urlVideo;
    }

    public void setUrlVideo(String urlVideo) {
        this.urlVideo = urlVideo;
    }

    public int getIdUser() {
        return idUser;
    }

    public void setIdUser(int idUser) {
        this.idUser = idUser;
    }

    public int getIdRecyclage() {
        return idRecyclage;
    }

    public void setIdRecyclage(int idRecyclage) {
        this.idRecyclage = idRecyclage;
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

    public Recyclage getRecyclage() {
        return recyclage;
    }

    public void setRecyclage(Recyclage recyclage) {
        this.recyclage = recyclage;
        if (recyclage != null) {
            this.idRecyclage = recyclage.getId();
        }
    }

    @Override
    public String toString() {
        return name + " (" + (recyclage != null ? recyclage.getType() : "N/A") + ")";
    }
}