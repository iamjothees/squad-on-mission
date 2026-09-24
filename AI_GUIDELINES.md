# Squad on Mission - AI Agent Guidelines

Welcome to the **Squad on Mission** repository. This document serves as a comprehensive guide for all AI agents, LLMs, and coding assistants working on this project. Please read and adhere to these principles, architectural rules, and design philosophies before making any modifications.

## 1. Project Overview & Scope
**Squad on Mission** is a hybrid platform serving as a:
- Freelance career progress tracker
- Productivity and Time Tracker
- Digital Book / Knowledge Base

The platform consists of a **Web Application** (Laravel) and a physical **Mission Control Macropad** (ESP32).

## 2. Design Philosophy & UI Rules
- **Data Dense UI:** The interface must maximize the display of information. Avoid wasting empty space with excessive padding or margins.
- **Minimalism:** Use minimal colors. Stick to a strict Dark Mode aesthetic. "No distractions."
- **Typography:** Use small fonts. Base font scale should be **14px**. 
- **Icons:** Use **Lucide icons** globally across the web application.

## 3. Technology Stack
- **Web App:** TALL Stack (Tailwind CSS, Alpine.js, Laravel, Livewire).
- **SPA Navigation:** Use `wire:navigate` for seamless Single Page Application transitions.
- **WebSockets:** Laravel Reverb handles real-time state synchronization via Pusher protocol.
- **Hardware:** ESP32 DevKit, 0.96" I2C OLED Display, Tactile Switches, programmed in C++ using the Arduino framework.

## 4. Architectural Rules & Anti-Patterns
### General Code Rules
- **No Poltergeists or Useless Wrappers:** Do not create intermediate classes or wrapper classes that serve no distinct business logic or architectural purpose. Keep the class hierarchy flat and purposeful.
- **Single Source of Truth:** The Laravel database and backend is the absolute source of truth. All clients (Web UI, Macropad) must sync to it.

### Web Architecture (Laravel / Livewire)
- **Timer Syncing:** Live timers use WebSockets (`TimerUpdated`, `TimerSwitched` events) to notify clients of state changes. 
- **Time Calculations:** Backend logic must rely on robust timestamps (`last_started_at`) rather than trusting client-side ticks. JavaScript and ESP32 clients calculate elapsed time dynamically against the backend's synchronized timestamp.

### Hardware Architecture (ESP32 C++)
- **Pristine `main.cpp`:** The `main.cpp` file must remain completely minimal. It should only serve as an entry point (`setup` and `loop`).
- **Composition Root Pattern:** Use a central orchestrator class (e.g., `MacropadApp.h`) to initialize and wire together services, UI renderers, and hardware managers.
- **Event-Driven Polling:** The ESP32 listens to WebSockets for "ping" events (e.g., `needsSync = true`). It does not process complex JSON payloads directly from WebSockets. Instead, the websocket event triggers an HTTP GET request to fetch the definitive state from the API (`/api/device/status`), guaranteeing state consistency without race conditions.
- **Zero-Config Auth:** The device pairs securely via a profile screen on the web UI, exchanging a token.

## 5. Macropad Hardware Mapping
- **Button 1 (Toggle):** 
  - *Click:* Toggle the active timer (Play/Pause).
  - *Long Press:* Stop the active timer completely.
- **Button 2 (Cycle):**
  - *Click:* Manual Refresh / Sync (triggers a fetch to ensure the OLED display is perfectly in sync with the backend).

## 6. Implementation Workflow for Agents
1. **Understand Context First:** Always trace the event flow from Backend -> Websocket -> Web UI -> ESP32 before modifying timer logic.
2. **Respect the UX:** When modifying the hardware code, ensure no blocking HTTP calls halt the display refresh (`renderState`), and avoid redundant screen clearing/flashing (e.g., no "Connecting WiFi..." flashes on simple button clicks).
3. **Commit Incrementally:** Separate web UI fixes from C++ hardware fixes.

---
*End of Guidelines. Act strictly within these constraints.*
