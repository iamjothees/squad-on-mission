#pragma once
#include <Arduino.h>
#include <OneButton.h>
#include "TimerState.h"
#include "../services/AuthService.h"
#include "../services/WifiService.h"
#include "../services/ApiService.h"
#include "../services/WebSocketService.h"
#include "../ui/AppRenderer.h"

class MacropadApp {
private:
    TimerState state;
    AuthService auth;
    WifiService wifi;
    ApiService api;
    WebSocketService ws;
    Adafruit_SSD1306 display;
    AppRenderer renderer;

    OneButton btn1;
    OneButton btn2;

    bool needsSync = false;
    bool wsInitialized = false;
    unsigned long lastSyncTime = 0;
    unsigned long lastTickTime = 0;
    const unsigned long SYNC_INTERVAL = 3600000; // 1 hour

    // --- Singleton Access for Callbacks ---
    static MacropadApp* instance;

public:
    MacropadApp(int btn1Pin, int btn2Pin) 
        : display(128, 64, &Wire, -1),
          renderer(display),
          btn1(btn1Pin, true),
          btn2(btn2Pin, true) 
    {
        instance = this;
    }

    void begin(const char* ssid, const char* pass, const char* apiUrl) {
        if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
            Serial.println("SSD1306 allocation failed");
            for(;;);
        }
        renderer.showConnecting();
        
        auth.begin();

        wifi.begin(ssid, pass, [this]() {
            
        });

        if (wifi.isConnected()) {
            renderer.showPairingScreen(auth.getToken(), "Polling API...");
        } else {
            renderer.showPairingScreen(auth.getToken(), "WiFi Failed");
        }

        api.begin(apiUrl, auth.getToken());
        
        btn1.attachClick([]() { if(instance) instance->onBtn1Click(); });
        btn1.attachLongPressStart([]() { if(instance) instance->onBtn1LongPress(); });
        btn2.attachClick([]() { if(instance) instance->onBtn2Click(); });

        performSync();
    }

    void tick() {
        btn1.tick();
        btn2.tick();

        if (wsInitialized) {
            ws.tick();
        }

        if (needsSync) {
            needsSync = false;
            performSync();
        }

        if (state.isActive && state.currentStatus == "RUNNING") {
            if (millis() - lastTickTime >= 1000) {
                state.elapsedSeconds++;
                lastTickTime = millis();
                renderer.renderState(state, wifi.isConnected());
            }
        }

        unsigned long currentInterval = state.isPaired ? SYNC_INTERVAL : 5000;
        if (millis() - lastSyncTime >= currentInterval) {
            performSync();
        }
    }

private:
    static void staticWsEvent(WStype_t type, uint8_t * payload, size_t length) {
        if (instance) instance->ws.handleEvent(type, payload, length);
    }

    static void staticWsMessage(String payload) {
        if (instance) {
            if (payload.indexOf("TimerUpdated") > 0 || payload.indexOf("TimerSwitched") > 0) {
                instance->needsSync = true;
            }
        }
    }

    void performSync() {
        if (!wifi.isConnected()) return;
        
        api.syncStatus(state);
        
        if (state.userId > 0 && !wsInitialized) {
            // Need the API_URL here, we can get it from ApiService or pass it in begin.
            // For now, let's assume we can get it from the environment or pass it.
            // Since API_URL is a macro in secrets.h, we can just use it.
            // Wait, including secrets.h here is fine, but better to store it.
            ws.begin(API_URL, state.userId, staticWsMessage, staticWsEvent);
            wsInitialized = true;
        }

        lastSyncTime = millis();
        lastTickTime = millis();
        renderer.renderState(state, true);
    }

    void onBtn1Click() {
        if (!state.isPaired) return;
        
        api.sendAction("/timer/toggle", state);
        lastSyncTime = millis();
        lastTickTime = millis();
        renderer.renderState(state, true);
    }

    void onBtn1LongPress() {
        if (!state.isPaired) return;
        
        api.sendAction("/timer/stop", state);
        lastSyncTime = millis();
        lastTickTime = millis();
        renderer.renderState(state, true);
    }

    void onBtn2Click() {
        if (!state.isPaired) return;
        
        performSync();
    }
};

MacropadApp* MacropadApp::instance = nullptr;
