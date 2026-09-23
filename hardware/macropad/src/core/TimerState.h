#pragma once
#include <Arduino.h>

struct TimerState {
    bool isActive = false;
    String currentStatus = "IDLE";
    String taskName = "Ready";
    long elapsedSeconds = 0;
    float workHoursPerDay = 24.0;
    bool hasMultiple = false;
    long lastDuration = 0;
    
    bool isPaired = false;
    String pairingStatus = "Polling API...";
    int userId = -1;
};
