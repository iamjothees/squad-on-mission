#pragma once
#include <Arduino.h>
#include <OneButton.h>
#include <functional>

class HardwareButton {
private:
    OneButton button;
    std::function<void()> onClickCb;
    std::function<void()> onLongPressCb;
    std::function<void()> onDoubleClickCb;

public:
    HardwareButton(int pin) : button(pin, true) {}

    void onSingleClick(std::function<void()> cb) {
        onClickCb = cb;
        button.attachClick([](void *ptr) {
            HardwareButton *btn = (HardwareButton *)ptr;
            if (btn->onClickCb) btn->onClickCb();
        }, this);
    }

    void onLongPress(std::function<void()> cb) {
        onLongPressCb = cb;
        button.attachLongPressStart([](void *ptr) {
            HardwareButton *btn = (HardwareButton *)ptr;
            if (btn->onLongPressCb) btn->onLongPressCb();
        }, this);
    }

    void onDoubleClick(std::function<void()> cb) {
        onDoubleClickCb = cb;
        button.attachDoubleClick([](void *ptr) {
            HardwareButton *btn = (HardwareButton *)ptr;
            if (btn->onDoubleClickCb) btn->onDoubleClickCb();
        }, this);
    }

    void tick() {
        button.tick();
    }
};
