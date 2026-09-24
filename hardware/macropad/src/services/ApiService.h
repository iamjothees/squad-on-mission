#pragma once
#include <Arduino.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "../core/TimerState.h"

class ApiService {
private:
    String apiUrl;
    String apiToken;

public:
    void begin(const char* url, const String& token) {
        apiUrl = url;
        apiToken = token;
    }

    void syncStatus(TimerState& state) {
        HTTPClient http;
        String url = apiUrl + "/status";
        http.begin(url);
        http.addHeader("Authorization", "Bearer " + apiToken);
        
        int httpCode = http.GET();
        if (httpCode == 200) {
            state.isPaired = true;
            String payload = http.getString();
            JsonDocument doc;
            if (!deserializeJson(doc, payload)) {
                state.isActive = doc["active"];
                state.currentStatus = doc["status"].as<String>();
                state.taskName = doc["task_name"].as<String>();
                state.elapsedSeconds = doc["elapsed"];
                state.hasMultiple = doc["has_multiple"] | false;
                
                if (!doc["last_duration"].isNull()) {
                    state.lastDuration = doc["last_duration"];
                } else if (state.isActive) {
                    state.lastDuration = 0;
                }
                
                if (!doc["last_entity_name"].isNull()) {
                    state.lastEntityName = doc["last_entity_name"].as<String>();
                } else {
                    state.lastEntityName = "";
                }
                
                if (!doc["work_hours_per_day"].isNull()) {
                    state.workHoursPerDay = doc["work_hours_per_day"];
                }

                if (!doc["user_id"].isNull() && state.userId == -1) {
                    state.userId = doc["user_id"];
                }
            } else {
                state.currentStatus = "JSON ERR";
            }
        } else if (httpCode > 0) {
            if (httpCode == 401) {
                state.isPaired = false;
                state.pairingStatus = "Not Found (401)";
            } else {
                state.currentStatus = "HTTP " + String(httpCode);
                state.pairingStatus = "HTTP ERR: " + String(httpCode);
            }
        } else {
            state.currentStatus = "NET ERR";
            state.pairingStatus = "Unreachable (Check IP)";
        }
        http.end();
    }

    void sendAction(const String& action, TimerState& state) {
        HTTPClient http;
        String url = apiUrl + action;
        http.begin(url);
        http.addHeader("Authorization", "Bearer " + apiToken);
        http.addHeader("Content-Length", "0");
        
        int httpCode = http.POST("");
        if (httpCode == 200) {
            String payload = http.getString();
            JsonDocument doc;
            if (!deserializeJson(doc, payload) && !doc["elapsed"].isNull()) {
                state.elapsedSeconds = doc["elapsed"];
                state.currentStatus = doc["status"].as<String>();
                state.hasMultiple = doc["has_multiple"] | false;
                if (!doc["work_hours_per_day"].isNull()) {
                    state.workHoursPerDay = doc["work_hours_per_day"];
                }
                if (state.currentStatus == "IDLE") {
                    state.isActive = false;
                    state.taskName = "Ready";
                } else {
                    state.isActive = true;
                }
            }
        } else if (httpCode > 0) {
            if (httpCode == 401) {
                state.isPaired = false;
                state.pairingStatus = "Not Found (401)";
            } else {
                state.currentStatus = "HTTP " + String(httpCode);
                state.pairingStatus = "HTTP ERR: " + String(httpCode);
            }
        } else {
            state.currentStatus = "NET ERR";
            state.pairingStatus = "Unreachable (Check IP)";
        }
        http.end();
    }
};
