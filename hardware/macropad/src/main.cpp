#include <Arduino.h>
#include "secrets.h"
#include "core/MacropadApp.h"

// --- Pins ---
#define BUTTON_1_PIN 4
#define BUTTON_2_PIN 5

MacropadApp app(BUTTON_1_PIN, BUTTON_2_PIN);

void setup() {
    Serial.begin(115200);
    app.begin(WIFI_SSID, WIFI_PASSWORD, API_URL);
}

void loop() {
    app.tick();
}
