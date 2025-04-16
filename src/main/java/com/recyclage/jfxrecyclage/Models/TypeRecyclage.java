package com.recyclage.jfxrecyclage.models;

public enum TypeRecyclage {
    PLASTIQUE("Plastique"),
    VERRE("Verre"),
    PAPIER("Papier"),
    METAL("Métal"),
    ELECTRONIQUE("Déchets électroniques"),
    ORGANIQUE("Déchets organiques"),
    AUTRE("Autre");

    private final String libelle;

    TypeRecyclage(String libelle) {
        this.libelle = libelle;
    }

    public String getLibelle() {
        return libelle;
    }

    @Override
    public String toString() {
        return libelle;
    }

 

}
