#pragma once
#include <Arduino.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <WebSocketsClient.h>
#include "../core/TimerState.h"

class ApiService {
private:
    String apiUrl;
    String apiToken;
    WebSocketsClient webSocket;
    bool wsConnected = false;
    unsigned long lastPingTime = 0;
    std::function<void()> onSyncRequested;

public:
    std::function<void(WStype_t, uint8_t *, size_t)> wsCb;
    
    void begin(const char* url, const String& token, std::function<void()> syncCallback, std::function<void(WStype_t, uint8_t *, size_t)> wsCallback) {
        apiUrl = url;
        apiToken = token;
        onSyncRequested = syncCallback;
        wsCb = wsCallback;
    }

    void setupWebSocket(int userId, std::function<void(WStype_t, uint8_t *, size_t)> cbEvent) {
        String url = apiUrl;
        int hostStart = url.indexOf("://") + 3;
        int hostEnd = url.indexOf(":", hostStart);
        if (hostEnd == -1) hostEnd = url.indexOf("/", hostStart);
        String wsHost = url.substring(hostStart, hostEnd);
        
        Serial.println("WebSocket Host: " + wsHost);
        webSocket.begin(wsHost, 8031, "/app/local?protocol=7&client=js&version=8.3.0&flash=false");
        
        webSocket.onEvent(cbEvent);
        webSocket.setReconnectInterval(5000);
    }
    
    WebSocketsClient& getWebSocket() {
        return webSocket;
    }

    void handleWebSocketEvent(WStype_t type, uint8_t * payload, size_t length, int userId) {
        switch(type) {
            case WStype_DISCONNECTED:
                wsConnected = false;
                break;
            case WStype_CONNECTED:
                wsConnected = true;
                if (userId > 0) {
                    String subMsg = "{\"event\":\"pusher:subscribe\",\"data\":{\"auth\":\"\",\"channel\":\"users." + String(userId) + "\"}}";
                    webSocket.sendTXT(subMsg);
                }
                break;
            case WStype_TEXT:
                if (strstr((char*)payload, "TimerUpdated") != NULL || strstr((char*)payload, "TimerSwitched") != NULL) {
                    if (onSyncRequested) onSyncRequested();
                }
                break;
            default:
                break;
        }
    }

    void tick() {
        webSocket.loop();
        
        // Reverb requires ping every 30s
        if (wsConnected && millis() - lastPingTime > 25000) {
            webSocket.sendTXT("{\"event\":\"pusher:ping\",\"data\":{}}");
            lastPingTime = millis();
        }
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
                
                if (!doc["work_hours_per_day"].isNull()) {
                    state.workHoursPerDay = doc["work_hours_per_day"];
                }

                if (!doc["user_id"].isNull() && state.userId == -1) {
                    state.userId = doc["user_id"];
                    setupWebSocket(state.userId, wsCb);
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
