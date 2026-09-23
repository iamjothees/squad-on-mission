#include <Arduino.h>
#include <Wire.h>
#include <OneButton.h>

#include "secrets.h"
#include "core/TimerState.h"
#include "services/AuthService.h"
#include "services/WifiService.h"
#include "services/ApiService.h"
#include "ui/DisplayManager.h"

// --- Pins ---
#define BUTTON_1_PIN 4
#define BUTTON_2_PIN 5

// --- Global Managers ---
TimerState state;
AuthService auth;
WifiService wifi;
ApiService api;
DisplayManager display;

OneButton btn1(BUTTON_1_PIN, true);
OneButton btn2(BUTTON_2_PIN, true);

bool needsSync = false;
unsigned long lastSyncTime = 0;
unsigned long lastTickTime = 0;
const unsigned long SYNC_INTERVAL = 3600000; // 1 hour

void requestSync() {
    needsSync = true;
}

void performSync() {
    if (!wifi.isConnected()) return;
    api.syncStatus(state);
    lastSyncTime = millis();
    lastTickTime = millis();
    display.render(state, true);
}

void onBtn1Click() {
    if (!state.isPaired) return;
    display.showConnecting(); // Use as loading screen
    api.sendAction("/timer/toggle", state);
    lastSyncTime = millis();
    lastTickTime = millis();
    display.render(state, true);
}

void onBtn1LongPress() {
    if (!state.isPaired) return;
    display.showConnecting(); // Use as loading screen
    api.sendAction("/timer/stop", state);
    lastSyncTime = millis();
    lastTickTime = millis();
    display.render(state, true);
}

void onBtn2Click() {
    // Cycle logic
}

void setup() {
    Serial.begin(115200);

    if (!display.begin()) {
        Serial.println("SSD1306 allocation failed");
        for(;;);
    }
    
    auth.begin();

    wifi.begin(WIFI_SSID, WIFI_PASSWORD, []() {
        display.showConnecting();
    });

    if (wifi.isConnected()) {
        display.showPairingScreen(auth.getToken(), "Polling API...");
    } else {
        display.showPairingScreen(auth.getToken(), "WiFi Failed");
    }

    api.begin(API_URL, auth.getToken(), requestSync);

    btn1.attachClick(onBtn1Click);
    btn1.attachLongPressStart(onBtn1LongPress);
    btn2.attachClick(onBtn2Click);

    performSync();
}

void loop() {
    btn1.tick();
    btn2.tick();

    if (state.userId > 0) {
        api.tick();
    }

    if (needsSync) {
        needsSync = false;
        performSync();
    }

    if (state.isActive && state.currentStatus == "RUNNING") {
        if (millis() - lastTickTime >= 1000) {
            state.elapsedSeconds++;
            lastTickTime = millis();
            display.render(state, wifi.isConnected());
        }
    }

    unsigned long currentInterval = state.isPaired ? SYNC_INTERVAL : 5000;
    if (millis() - lastSyncTime >= currentInterval) {
        performSync();
    }
}
