#include <Arduino.h>
#include <Wire.h>
#include "secrets.h"
#include "core/TimerState.h"
#include "services/AuthService.h"
#include "services/WifiService.h"
#include "services/ApiService.h"
#include "services/WebSocketService.h"
#include <Adafruit_SSD1306.h>
#include "ui/AppRenderer.h"
#include <OneButton.h>

// --- Pins ---
#define BUTTON_1_PIN 4
#define BUTTON_2_PIN 5

// --- Global Managers ---
TimerState state;
AuthService auth;
WifiService wifi;
ApiService api;
WebSocketService ws;
Adafruit_SSD1306 display(128, 64, &Wire, -1);
AppRenderer renderer(display);

OneButton btn1(BUTTON_1_PIN, true);
OneButton btn2(BUTTON_2_PIN, true);

bool needsSync = false;
bool wsInitialized = false;
unsigned long lastSyncTime = 0;
unsigned long lastTickTime = 0;
const unsigned long SYNC_INTERVAL = 3600000; // 1 hour

void requestSync() {
    needsSync = true;
}

void globalWsEvent(WStype_t type, uint8_t * payload, size_t length) {
    ws.handleEvent(type, payload, length);
}

void onWebSocketMessage(String payload) {
    if (payload.indexOf("TimerUpdated") > 0 || payload.indexOf("TimerSwitched") > 0) {
        requestSync();
    }
}

void performSync() {
    if (!wifi.isConnected()) return;
    
    api.syncStatus(state);
    
    // Initialize WebSockets once we have a valid User ID from the API
    if (state.userId > 0 && !wsInitialized) {
        ws.begin(API_URL, state.userId, onWebSocketMessage, globalWsEvent);
        wsInitialized = true;
    }

    lastSyncTime = millis();
    lastTickTime = millis();
    renderer.renderState(state, true);
}

void onBtn1Click() {
    if (!state.isPaired) return;
    renderer.showConnecting();
    api.sendAction("/timer/toggle", state);
    lastSyncTime = millis();
    lastTickTime = millis();
    renderer.renderState(state, true);
}

void onBtn1LongPress() {
    if (!state.isPaired) return;
    renderer.showConnecting();
    api.sendAction("/timer/stop", state);
    lastSyncTime = millis();
    lastTickTime = millis();
    renderer.renderState(state, true);
}

void onBtn2Click() {
    // Cycle logic
}

void setup() {
    Serial.begin(115200);

    if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
        Serial.println("SSD1306 allocation failed");
        for(;;);
    }
    
    auth.begin();

    wifi.begin(WIFI_SSID, WIFI_PASSWORD, []() {
        renderer.showConnecting();
    });

    if (wifi.isConnected()) {
        renderer.showPairingScreen(auth.getToken(), "Polling API...");
    } else {
        renderer.showPairingScreen(auth.getToken(), "WiFi Failed");
    }

    api.begin(API_URL, auth.getToken());
    
    btn1.attachClick(onBtn1Click);
    btn1.attachLongPressStart(onBtn1LongPress);
    btn2.attachClick(onBtn2Click);

    performSync();
}

void loop() {
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
