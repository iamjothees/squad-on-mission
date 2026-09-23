#pragma once
#include <Arduino.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include "../core/TimerState.h"

#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64

class DisplayManager {
private:
    Adafruit_SSD1306 display;

public:
    DisplayManager() : display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1) {}

    bool begin() {
        if(!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
            return false;
        }
        display.clearDisplay();
        return true;
    }

    void showConnecting() {
        display.clearDisplay();
        display.setTextSize(1);
        display.setTextColor(SSD1306_WHITE);
        display.setCursor(10, 30);
        display.print("Connecting WiFi...");
        display.display();
    }

    void showPairingScreen(const String& token, const String& status) {
        display.clearDisplay();
        display.setTextSize(1);
        display.setTextColor(SSD1306_WHITE);
        display.setCursor(0, 0);
        display.println("WIFI CONNECTED");
        display.setCursor(0, 15);
        display.println(status);
        display.setCursor(0, 35);
        display.println("Enter code in Profile:");
        display.setTextSize(2);
        display.setCursor(20, 50);
        display.println(token);
        display.display();
    }

    void render(const TimerState& state, bool wifiConnected) {
        if (!state.isPaired) {
            // Usually we'd need the token here, but state doesn't have it.
            // We can pass token via a setter, or just let main handle pairing screen explicitly.
            return; 
        }

        display.clearDisplay();
        display.setTextSize(1);
        display.setTextColor(SSD1306_WHITE);
        
        // Status Bar
        display.setCursor(0, 0);
        display.print(wifiConnected ? "WIFI" : "ERR");
        display.setCursor(90, 0);
        display.print(state.currentStatus);
        display.drawLine(0, 10, 128, 10, SSD1306_WHITE);

        // Task Name
        display.setCursor(0, 15);
        display.print(state.taskName);
        if (state.hasMultiple) {
            display.print(" (+)");
        }

        // Timer
        if (state.isActive) {
            long secondsPerDay = (long)(state.workHoursPerDay * 3600);
            int d = state.elapsedSeconds / secondsPerDay;
            long remainingSeconds = state.elapsedSeconds % secondsPerDay;
            
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
            if (state.lastDuration > 0) {
                long secondsPerDay = (long)(state.workHoursPerDay * 3600);
                int d = state.lastDuration / secondsPerDay;
                long rem = state.lastDuration % secondsPerDay;
                int h = rem / 3600;
                int m = (rem % 3600) / 60;
                int s = rem % 60;
                
                char timeStr[9];
                sprintf(timeStr, "%02d:%02d:%02d", h, m, s);
                
                display.setTextSize(1);
                display.setCursor(25, 34);
                display.print("Last Timer:");
                
                display.setTextSize(1);
                display.setCursor(40, 48);
                if (d > 0) {
                    display.setCursor(20, 48);
                    display.print(String(d) + "d " + String(timeStr));
                } else {
                    display.print(timeStr);
                }
            } else {
                display.setTextSize(1);
                display.setCursor(20, 45);
                display.print("Press to Start");
            }
        }

        display.display();
    }
};
