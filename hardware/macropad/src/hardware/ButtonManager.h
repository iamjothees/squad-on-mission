#pragma once
#include <Arduino.h>
#include <OneButton.h>
#include <functional>

class ButtonManager {
private:
    OneButton btn1;
    OneButton btn2;
    std::function<void()> onBtn1Click;
    std::function<void()> onBtn1LongPress;
    std::function<void()> onBtn2Click;

public:
    ButtonManager(int pin1, int pin2) : btn1(pin1, true), btn2(pin2, true) {}

    void begin(std::function<void()> btn1Click, std::function<void()> btn1LongPress, std::function<void()> btn2Click) {
        onBtn1Click = btn1Click;
        onBtn1LongPress = btn1LongPress;
        onBtn2Click = btn2Click;

        btn1.attachClick([](void *ptr) {
            ButtonManager *mgr = (ButtonManager *)ptr;
            if (mgr->onBtn1Click) mgr->onBtn1Click();
        }, this);
        
        btn1.attachLongPressStart([](void *ptr) {
            ButtonManager *mgr = (ButtonManager *)ptr;
            if (mgr->onBtn1LongPress) mgr->onBtn1LongPress();
        }, this);
        
        btn2.attachClick([](void *ptr) {
            ButtonManager *mgr = (ButtonManager *)ptr;
            if (mgr->onBtn2Click) mgr->onBtn2Click();
        }, this);
    }

    void tick() {
        btn1.tick();
        btn2.tick();
    }
};
