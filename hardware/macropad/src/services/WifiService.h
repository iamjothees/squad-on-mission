#pragma once
#include <Arduino.h>
#include <WiFi.h>

class WifiService {
public:
    bool begin(const char* ssid, const char* password, void (*onConnecting)()) {
        WiFi.mode(WIFI_STA);
        WiFi.disconnect();
        delay(100);

        if (onConnecting) onConnecting();

        WiFi.begin(ssid, password);
        
        int attempts = 0;
        while (WiFi.status() != WL_CONNECTED && attempts < 20) {
            delay(500);
            attempts++;
        }
        
        return isConnected();
    }

    bool isConnected() const {
        return WiFi.status() == WL_CONNECTED;
    }
};
