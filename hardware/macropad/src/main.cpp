#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <OneButton.h>
#include <Preferences.h>
#include <WebSocketsClient.h>


#include "secrets.h"

// --- Configuration ---
const char* ssid = WIFI_SSID;
const char* password = WIFI_PASSWORD;
const char* apiUrl = API_URL;
// apiToken is now dynamically generated and read from Preferences
String apiToken = "";
Preferences preferences;


// --- WebSockets ---
WebSocketsClient webSocket;
int userId = -1;
bool needsSync = false;
unsigned long lastPingTime = 0;
bool wsConnected = false;

void webSocketEvent(WStype_t type, uint8_t * payload, size_t length) {
    switch(type) {
        case WStype_DISCONNECTED:
            Serial.printf("[WSc] Disconnected!\n");
            wsConnected = false;
            break;
        case WStype_CONNECTED:
            Serial.printf("[WSc] Connected to url: %s\n", payload);
            wsConnected = true;
            // Subscribe to channel
            if (userId > 0) {
                String subMsg = "{\"event\":\"pusher:subscribe\",\"data\":{\"auth\":\"\",\"channel\":\"users." + String(userId) + "\"}}";
                webSocket.sendTXT(subMsg);
                Serial.println("[WSc] Subscribing: " + subMsg);
            }
            break;
        case WStype_TEXT:
            Serial.printf("[WSc] get text: %s\n", payload);
            // If it is a TimerUpdated or TimerSwitched event, trigger a sync
            if (strstr((char*)payload, "TimerUpdated") != NULL || strstr((char*)payload, "TimerSwitched") != NULL) {
                needsSync = true;
            }
            break;
        case WStype_BIN:
        case WStype_PING:
        case WStype_PONG:
        case WStype_ERROR:
        case WStype_FRAGMENT_TEXT_START:
        case WStype_FRAGMENT_BIN_START:
        case WStype_FRAGMENT:
        case WStype_FRAGMENT_FIN:
            break;
    }
}

void setupWebSocket() {
    String url = String(apiUrl);
    int hostStart = url.indexOf("://") + 3;
    int hostEnd = url.indexOf(":", hostStart);
    if (hostEnd == -1) hostEnd = url.indexOf("/", hostStart);
    String wsHost = url.substring(hostStart, hostEnd);
    
    Serial.println("WebSocket Host: " + wsHost);
    webSocket.begin(wsHost, 8031, "/app/local?protocol=7&client=js&version=8.3.0&flash=false");
    webSocket.onEvent(webSocketEvent);
    webSocket.setReconnectInterval(5000);
}

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
float workHoursPerDay = 24.0;
bool hasMultiple = false;
unsigned long lastSyncTime = 0;
unsigned long lastTickTime = 0;
bool isPaired = false;
String pairingStatus = "Polling API...";
const unsigned long SYNC_INTERVAL = 3600000; // 1 hour (relying on WebSockets now)

void showPairingScreen() {
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 0);
  display.println("WIFI CONNECTED");
  display.setCursor(0, 15);
  display.println(pairingStatus);
  display.setCursor(0, 35);
  display.println("Enter code in Profile:");
  display.setTextSize(2);
  display.setCursor(20, 50);
  display.println(apiToken);
  display.display();
}

void updateDisplay() {
  if (!isPaired) {
    showPairingScreen();
    return;
  }

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
  display.print(taskName);
  if (hasMultiple) {
      display.print(" (+)");
  }

  // Timer
  if (isActive) {
    long secondsPerDay = (long)(workHoursPerDay * 3600);
    int d = elapsedSeconds / secondsPerDay;
    long remainingSeconds = elapsedSeconds % secondsPerDay;
    
    int h = remainingSeconds / 3600;
    int m = (remainingSeconds % 3600) / 60;
    int s = remainingSeconds % 60;
    
    char timeStr[9];
    sprintf(timeStr, "%02d:%02d:%02d", h, m, s);
    
    if (d > 0) {
        char dayStr[10];
        sprintf(dayStr, "+ %d days", d);
        display.setTextSize(1);
        display.setCursor(16, 32);
        display.print(dayStr);
        
        display.setTextSize(2);
        display.setCursor(16, 46);
        display.print(timeStr);
    } else {
        display.setTextSize(2);
        display.setCursor(16, 40);
        display.print(timeStr);
    }
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
  
  Serial.println("Syncing with API: " + url);
  Serial.println("Token: Bearer " + apiToken);
  
  int httpCode = http.GET();
  Serial.println("HTTP Response Code: " + String(httpCode));
  
  if (httpCode == 200) {
    isPaired = true; // Successfully authenticated!
    String payload = http.getString();
    JsonDocument doc;
    DeserializationError error = deserializeJson(doc, payload);
    
    if (!error) {
      isActive = doc["active"];
      currentStatus = doc["status"].as<String>();
      taskName = doc["task_name"].as<String>();
      elapsedSeconds = doc["elapsed"];
      hasMultiple = doc["has_multiple"] | false;
      if (!doc["work_hours_per_day"].isNull()) {
        workHoursPerDay = doc["work_hours_per_day"];
      }
      if (!doc["user_id"].isNull() && userId == -1) {
          userId = doc["user_id"];
          setupWebSocket();
      }
    } else {
      currentStatus = "JSON ERR";
      taskName = "Parse Failed";
    }
  } else if (httpCode > 0) {
    if (httpCode == 401) {
      isPaired = false;
      pairingStatus = "Not Found (401)";
    } else {
      currentStatus = "HTTP " + String(httpCode);
      taskName = "Server Error";
      pairingStatus = "HTTP ERR: " + String(httpCode);
    }
  } else {
    currentStatus = "NET ERR";
    taskName = "Unreachable";
    pairingStatus = "Unreachable (Check IP)";
    Serial.println("HTTP GET failed, error: " + http.errorToString(httpCode));
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
      hasMultiple = doc["has_multiple"] | false;
      if (!doc["work_hours_per_day"].isNull()) {
        workHoursPerDay = doc["work_hours_per_day"];
      }
      if (!doc["user_id"].isNull() && userId == -1) {
          userId = doc["user_id"];
          setupWebSocket();
      }
      if (currentStatus == "IDLE") {
          isActive = false;
          taskName = "Ready";
      } else {
          isActive = true;
      }
    }
  } else if (httpCode > 0) {
    if (httpCode == 401) {
      isPaired = false;
      pairingStatus = "Not Found (401)";
    } else {
      currentStatus = "HTTP " + String(httpCode);
      taskName = "Server Error";
      pairingStatus = "HTTP ERR: " + String(httpCode);
    }
  } else {
    currentStatus = "NET ERR";
    taskName = "Unreachable";
    pairingStatus = "Unreachable (Check IP)";
    Serial.println("HTTP GET failed, error: " + http.errorToString(httpCode));
  }
  http.end();
  lastSyncTime = millis();
  lastTickTime = millis();
  updateDisplay();
}

void onBtn1Click() {
  if (!isPaired) return;
  sendPostAction("/timer/toggle");
}

void onBtn1LongPress() {
  if (!isPaired) return;
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

  randomSeed(analogRead(0));
  
  // Explicitly set to Station mode and clear old states for faster connection
  WiFi.mode(WIFI_STA);
  WiFi.disconnect();
  delay(100);

  WiFi.begin(ssid, password);
  
  // Add a timeout just in case it hangs forever
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    attempts++;
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
  
  showPairingScreen();

  btn1.attachClick(onBtn1Click);
  btn1.attachLongPressStart(onBtn1LongPress);
  btn2.attachClick(onBtn2Click);

  syncWithServer();
}

void loop() {
  btn1.tick();
  btn2.tick();
  
  if (userId > 0) {
      webSocket.loop();
  }
  
  if (needsSync) {
      needsSync = false;
      syncWithServer();
  }
  
  // Reverb requires ping every 30s
  if (wsConnected && millis() - lastPingTime > 25000) {
      webSocket.sendTXT("{\"event\":\"pusher:ping\",\"data\":{}}");
      lastPingTime = millis();
  }

  if (isActive && currentStatus == "RUNNING") {
    if (millis() - lastTickTime >= 1000) {
      elapsedSeconds++;
      lastTickTime = millis();
      updateDisplay();
    }
  }

  unsigned long currentInterval = isPaired ? SYNC_INTERVAL : 5000; // Poll every 5s while pairing
  if (millis() - lastSyncTime >= currentInterval) {
    syncWithServer();
  }
}
