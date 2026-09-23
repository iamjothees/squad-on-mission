#pragma once
#include <Arduino.h>
#include <OneButton.h>
#include <functional>

class ButtonManager {
private:
    OneButton btn1;
    OneButton btn2;
    std::function<void()> onActionClick;
    std::function<void()> onActionLongPress;
    std::function<void()> onCycleClick;

public:
    ButtonManager(int pin1, int pin2) : btn1(pin1, true), btn2(pin2, true) {}

    void begin(std::function<void()> actionClick, std::function<void()> actionLongPress, std::function<void()> cycleClick) {
        onActionClick = actionClick;
        onActionLongPress = actionLongPress;
        onCycleClick = cycleClick;

        btn1.attachClick([](void *ptr) {
            ButtonManager *mgr = (ButtonManager *)ptr;
            if (mgr->onActionClick) mgr->onActionClick();
        }, this);
        
        btn1.attachLongPressStart([](void *ptr) {
            ButtonManager *mgr = (ButtonManager *)ptr;
            if (mgr->onActionLongPress) mgr->onActionLongPress();
        }, this);
        
        btn2.attachClick([](void *ptr) {
            ButtonManager *mgr = (ButtonManager *)ptr;
            if (mgr->onCycleClick) mgr->onCycleClick();
        }, this);
    }

    void tick() {
        btn1.tick();
        btn2.tick();
    }
};
