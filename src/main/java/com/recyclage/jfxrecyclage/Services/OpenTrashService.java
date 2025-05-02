package com.recyclage.jfxrecyclage.services;

import com.google.gson.*;
import com.recyclage.jfxrecyclage.models.RecyclingPoint;
import java.io.IOException;
import okhttp3.*;
import javax.net.ssl.*;
import java.security.cert.X509Certificate;
import java.util.*;

public class OpenTrashService {
    private static final String API_URL = "https://openrecycling.org/api/v1/locations";
    private final OkHttpClient client;
    private final Gson gson = new Gson();

    public OpenTrashService() {
        this.client = createUnsafeOkHttpClient();
    }

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

    private OkHttpClient createUnsafeOkHttpClient() {
        try {
            final TrustManager[] trustAllCerts = new TrustManager[] {
                new X509TrustManager() {
                    @Override public void checkClientTrusted(X509Certificate[] chain, String authType) {}
                    @Override public void checkServerTrusted(X509Certificate[] chain, String authType) {}
                    @Override public X509Certificate[] getAcceptedIssuers() { return new X509Certificate[]{}; }
                }
            };

            final SSLContext sslContext = SSLContext.getInstance("SSL");
            sslContext.init(null, trustAllCerts, new java.security.SecureRandom());
            final SSLSocketFactory sslSocketFactory = sslContext.getSocketFactory();

            return new OkHttpClient.Builder()
                .sslSocketFactory(sslSocketFactory, (X509TrustManager)trustAllCerts[0])
                .hostnameVerifier((hostname, session) -> true)
                .build();
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }
}