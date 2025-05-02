package com.recyclage.jfxrecyclage.models;

public class RecyclingPoint {
    private final String id;
    private final String name;
    private final String address;
    private final double latitude;
    private final double longitude;
    private final String[] materials;

    public RecyclingPoint(String id, String name, String address, 
                         double latitude, double longitude, String[] materials) {
        this.id = id;
        this.name = name;
        this.address = address;
        this.latitude = latitude;
        this.longitude = longitude;
        this.materials = materials;
    }

    // Getters
    public String getId() { return id; }
    public String getName() { return name; }
    public String getAddress() { return address; }
    public double getLatitude() { return latitude; }
    public double getLongitude() { return longitude; }
    public String[] getMaterials() { return materials; }
    public String getMaterialsString() {
        return materials != null ? String.join(", ", materials) : "";
    }
}

