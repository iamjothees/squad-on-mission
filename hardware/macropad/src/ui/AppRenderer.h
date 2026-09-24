#pragma once
#include <Arduino.h>
#include <Adafruit_SSD1306.h>
#include "../core/TimerState.h"

class AppRenderer {
private:
    Adafruit_SSD1306& display;

public:
    AppRenderer(Adafruit_SSD1306& disp) : display(disp) {}

    void showConnecting() {
        display.clearDisplay();
        display.setTextColor(SSD1306_WHITE);
        display.setTextSize(1);
        display.setCursor(10, 30);
        display.print("Connecting WiFi...");
        display.display();
    }

    void showPairingScreen(const String& token, const String& status) {
        display.clearDisplay();
        display.setTextColor(SSD1306_WHITE);
        display.setTextSize(1);
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

    void renderState(const TimerState& state, bool wifiConnected) {
        if (!state.isPaired) {
            return; 
        }

        display.clearDisplay();
        display.setTextColor(SSD1306_WHITE);
        display.setTextSize(1);
        
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
            drawTimeLarge(state.elapsedSeconds, state.workHoursPerDay);
        } else {
            if (state.lastDuration > 0) {
                display.setTextSize(1);
                display.setCursor(0, 20);
                display.print("Last: ");
                
                String label = state.lastEntityName;
                if (label.length() > 14) {
                    label = label.substring(0, 12) + "..";
                }
                display.print(label);
                
                drawTimeLarge(state.lastDuration, state.workHoursPerDay, 40);
            } else {
                display.setTextSize(1);
                display.setCursor(20, 45);
                display.print("Press to Start");
            }
        }

        display.display();
    }

private:
    void drawTimeLarge(long totalSeconds, float workHoursPerDay, int yOffset = 34) {
        long secondsPerDay = (long)(workHoursPerDay * 3600);
        int d = totalSeconds / secondsPerDay;
        long remainingSeconds = totalSeconds % secondsPerDay;
        
        int h = remainingSeconds / 3600;
        int m = (remainingSeconds % 3600) / 60;
        int s = remainingSeconds % 60;
        
        char timeStr[9];
        sprintf(timeStr, "%02d:%02d:%02d", h, m, s);
        
        if (d > 0) {
            char dayStr[10];
            sprintf(dayStr, "+ %d days", d);
            display.setTextSize(1);
            display.setCursor(16, yOffset - 5);
            display.print(dayStr);
            
            display.setTextSize(2);
            display.setCursor(16, yOffset + 9);
            display.print(timeStr);
        } else {
            display.setTextSize(2);
            display.setCursor(16, yOffset + 6);
            display.print(timeStr);
        }
    }
};
