#pragma once
#include <Arduino.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

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
        display.setTextColor(SSD1306_WHITE);
        return true;
    }

    void clear() {
        display.clearDisplay();
    }

    void update() {
        display.display();
    }

    Adafruit_SSD1306& getDriver() {
        return display;
    }
};
