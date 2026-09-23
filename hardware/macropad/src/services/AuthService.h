#pragma once
#include <Arduino.h>
#include <Preferences.h>

class AuthService {
private:
    Preferences preferences;
    String apiToken;

public:
    void begin() {
        preferences.begin("macropad", false);
        apiToken = preferences.getString("code", "");
        
        if (apiToken == "") {
            const char charset[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            for(int i = 0; i < 6; i++) {
                apiToken += charset[random(0, 36)];
            }
            preferences.putString("code", apiToken);
        }
    }

    String getToken() const {
        return apiToken;
    }
};
