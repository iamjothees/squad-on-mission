#pragma once
#include <Arduino.h>
#include <WebSocketsClient.h>
#include <functional>

class WebSocketService {
private:
    WebSocketsClient webSocket;
    bool wsConnected = false;
    unsigned long lastPingTime = 0;
    int currentUserId = -1;
    std::function<void(String)> onMessageCallback;

public:
    void begin(const String& apiUrl, int userId, std::function<void(String)> onMessage, std::function<void(WStype_t, uint8_t*, size_t)> rawEventProxy) {
        currentUserId = userId;
        onMessageCallback = onMessage;
        
        int hostStart = apiUrl.indexOf("://") + 3;
        int hostEnd = apiUrl.indexOf(":", hostStart);
        if (hostEnd == -1) hostEnd = apiUrl.indexOf("/", hostStart);
        String wsHost = apiUrl.substring(hostStart, hostEnd);
        
        Serial.println("WebSocket Host: " + wsHost);
        webSocket.begin(wsHost, 8031, "/app/local?protocol=7&client=js&version=8.3.0&flash=false");
        
        webSocket.onEvent(rawEventProxy);
        webSocket.setReconnectInterval(5000);
    }
    
    void handleEvent(WStype_t type, uint8_t * payload, size_t length) {
        switch(type) {
            case WStype_DISCONNECTED:
                wsConnected = false;
                break;
            case WStype_CONNECTED:
                wsConnected = true;
                if (currentUserId > 0) {
                    String subMsg = "{\"event\":\"pusher:subscribe\",\"data\":{\"auth\":\"\",\"channel\":\"users." + String(currentUserId) + "\"}}";
                    webSocket.sendTXT(subMsg);
                }
                break;
            case WStype_TEXT:
                if (onMessageCallback) {
                    onMessageCallback(String((char*)payload));
                }
                break;
            default:
                break;
        }
    }
    
    void tick() {
        webSocket.loop();
        
        if (wsConnected && millis() - lastPingTime > 25000) {
            webSocket.sendTXT("{\"event\":\"pusher:ping\",\"data\":{}}");
            lastPingTime = millis();
        }
    }
};
