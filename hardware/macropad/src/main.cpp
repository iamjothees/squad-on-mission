#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <OneButton.h>
#include <Preferences.h>

#include "secrets.h"

// --- Configuration ---
const char* ssid = WIFI_SSID;
const char* password = WIFI_PASSWORD;
const char* apiUrl = API_URL;
// apiToken is now dynamically generated and read from Preferences
String apiToken = "";
Preferences preferences;

// --- Pins ---
#define BUTTON_1_PIN 4 // Start/Pause/Stop
#define BUTTON_2_PIN 5 // Cycle
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64

Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);
OneButton btn1(BUTTON_1_PIN, true);
OneButton btn2(BUTTON_2_PIN, true);

// --- State ---
bool isActive = false;
String currentStatus = "IDLE";
String taskName = "Ready";
long elapsedSeconds = 0;
unsigned long lastSyncTime = 0;
unsigned long lastTickTime = 0;
const unsigned long SYNC_INTERVAL = 10000; // 10 seconds

void updateDisplay() {
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  
  // Status Bar
  display.setCursor(0, 0);
  display.print(WiFi.status() == WL_CONNECTED ? "WIFI" : "ERR");
  display.setCursor(90, 0);
  display.print(currentStatus);
  display.drawLine(0, 10, 128, 10, SSD1306_WHITE);

  // Task Name
  display.setCursor(0, 15);
  display.println(taskName);

  // Timer
  if (isActive) {
    int h = elapsedSeconds / 3600;
    int m = (elapsedSeconds % 3600) / 60;
    int s = elapsedSeconds % 60;
    
    char timeStr[9];
    sprintf(timeStr, "%02d:%02d:%02d", h, m, s);
    
    display.setTextSize(2);
    display.setCursor(16, 40);
    display.print(timeStr);
  } else {
    display.setTextSize(1);
    display.setCursor(20, 45);
    display.print("Press to Start");
  }

  display.display();
}

void syncWithServer() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = String(apiUrl) + "/status";
  http.begin(url);
  http.addHeader("Authorization", String("Bearer ") + apiToken);
  
  int httpCode = http.GET();
  if (httpCode == 200) {
    String payload = http.getString();
    JsonDocument doc;
    DeserializationError error = deserializeJson(doc, payload);
    
    if (!error) {
      isActive = doc["active"];
      currentStatus = doc["status"].as<String>();
      taskName = doc["task_name"].as<String>();
      elapsedSeconds = doc["elapsed"];
    } else {
      currentStatus = "JSON ERR";
      taskName = "Parse Failed";
    }
  } else if (httpCode > 0) {
    currentStatus = "HTTP " + String(httpCode);
    taskName = "Server Error";
  } else {
    currentStatus = "NET ERR";
    taskName = "Unreachable";
  }
  http.end();
  
  lastSyncTime = millis();
  lastTickTime = millis();
  updateDisplay();
}

void sendPostAction(String action) {
  if (WiFi.status() != WL_CONNECTED) return;
  
  display.clearDisplay();
  display.setCursor(40, 25);
  display.print("Syncing...");
  display.display();

  HTTPClient http;
  String url = String(apiUrl) + action;
  http.begin(url);
  http.addHeader("Authorization", String("Bearer ") + apiToken);
  http.addHeader("Content-Length", "0");
  
  int httpCode = http.POST("");
  if (httpCode == 200) {
    String payload = http.getString();
    JsonDocument doc;
    DeserializationError error = deserializeJson(doc, payload);
    
    if (!error && !doc["elapsed"].isNull()) {
      elapsedSeconds = doc["elapsed"];
      currentStatus = doc["status"].as<String>();
      if (currentStatus == "IDLE") {
          isActive = false;
          taskName = "Ready";
      } else {
          isActive = true;
      }
    }
  } else if (httpCode > 0) {
    currentStatus = "HTTP " + String(httpCode);
    taskName = "Server Error";
  } else {
    currentStatus = "NET ERR";
    taskName = "Unreachable";
  }
  http.end();
  lastSyncTime = millis();
  lastTickTime = millis();
  updateDisplay();
}

void onBtn1Click() {
  sendPostAction("/timer/toggle");
}

void onBtn1LongPress() {
  sendPostAction("/timer/stop");
}

void onBtn2Click() {
  // Cycle logic here
}

void setup() {
  Serial.begin(115200);
  
  if(!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println(F("SSD1306 allocation failed"));
    for(;;);
  }
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(10, 30);
  display.print("Connecting WiFi...");
  display.display();

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
  }

  // Generate pairing code if not exists
  preferences.begin("macropad", false);
  apiToken = preferences.getString("code", "");
  if (apiToken == "") {
    const char charset[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for(int i=0; i<6; i++) {
       apiToken += charset[random(0, 36)];
    }
    preferences.putString("code", apiToken);
  }
  
  display.clearDisplay();
  display.setCursor(0, 10);
  display.println("WIFI CONNECTED");
  display.setCursor(0, 30);
  display.println("Pairing Code:");
  display.setTextSize(2);
  display.setCursor(0, 45);
  display.println(apiToken);
  display.display();
  delay(5000); // Show code for 5 seconds on boot
  display.setTextSize(1);

  btn1.attachClick(onBtn1Click);
  btn1.attachLongPressStart(onBtn1LongPress);
  btn2.attachClick(onBtn2Click);

  syncWithServer();
}

void loop() {
  btn1.tick();
  btn2.tick();

  if (isActive && currentStatus == "RUNNING") {
    if (millis() - lastTickTime >= 1000) {
      elapsedSeconds++;
      lastTickTime = millis();
      updateDisplay();
    }
  }

  if (millis() - lastSyncTime >= SYNC_INTERVAL) {
    syncWithServer();
  }
}
