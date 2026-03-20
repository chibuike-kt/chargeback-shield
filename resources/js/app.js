import Alpine from "alpinejs";
import Chart from "chart.js/auto";

window.Chart = Chart;

// Register Alpine components before Alpine.start()
window.heroFeed = function () {
    return {
        events: [],
        maxEvents: 6,
        timer: null,

        init() {
            var self = this;

            var mockEvents = [
                {
                    last4: "4242",
                    bin: "459234",
                    amount: "NGN 5,000.00",
                    route: "NG → NG",
                    decision: "allow",
                    score: 0.124,
                    label: "Approved",
                },
                {
                    last4: "1111",
                    bin: "520000",
                    amount: "NGN 35,000.00",
                    route: "GH → NG",
                    decision: "step_up",
                    score: 0.512,
                    label: "Step-Up",
                },
                {
                    last4: "9999",
                    bin: "670123",
                    amount: "NGN 150,000.00",
                    route: "RU → NG",
                    decision: "decline",
                    score: 0.891,
                    label: "Declined",
                },
                {
                    last4: "5678",
                    bin: "440647",
                    amount: "NGN 12,500.00",
                    route: "NG → NG",
                    decision: "allow",
                    score: 0.087,
                    label: "Approved",
                },
                {
                    last4: "0001",
                    bin: "490123",
                    amount: "NGN 100.00",
                    route: "NG → NG",
                    decision: "decline",
                    score: 0.923,
                    label: "Declined",
                },
                {
                    last4: "3456",
                    bin: "539983",
                    amount: "NGN 8,750.00",
                    route: "KE → KE",
                    decision: "allow",
                    score: 0.156,
                    label: "Approved",
                },
            ];

            var index = 0;

            function addEvent() {
                var event = Object.assign(
                    {},
                    mockEvents[index % mockEvents.length],
                    {
                        id: Date.now() + Math.random(),
                    },
                );
                self.events.unshift(event);
                if (self.events.length > self.maxEvents) {
                    self.events = self.events.slice(0, self.maxEvents);
                }
                index++;
            }

            setTimeout(function () {
                addEvent();
            }, 300);
            setTimeout(function () {
                addEvent();
            }, 800);
            setTimeout(function () {
                addEvent();
            }, 1400);

            self.timer = setInterval(function () {
                addEvent();
            }, 2500);
        },

        decisionColor: function (d) {
            return (
                { allow: "#059669", step_up: "#d97706", decline: "#dc2626" }[
                    d
                ] || "#64748b"
            );
        },
        decisionBg: function (d) {
            return (
                { allow: "#ecfdf5", step_up: "#fffbeb", decline: "#fef2f2" }[
                    d
                ] || "#f1f5f9"
            );
        },
        scoreColor: function (s) {
            return s < 0.4 ? "#059669" : s < 0.7 ? "#d97706" : "#dc2626";
        },
    };
};

// Register simulationPanel and liveFeed the same way
window.simulationPanel = function () {
    return {
        running: null,
        result: null,
        runScenario(scenarioId) {
            if (this.running) return;
            this.running = scenarioId;
            this.result = null;
            fetch("/simulate/run", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                },
                body: JSON.stringify({ scenario: scenarioId }),
            })
                .then((r) => r.json())
                .then((data) => {
                    this.result = data;
                    this.running = null;
                })
                .catch(() => {
                    this.running = null;
                });
        },
        stepStyle(status) {
            return (
                {
                    success: "background:#059669;color:white;",
                    danger: "background:#dc2626;color:white;",
                    warning: "background:#d97706;color:white;",
                    pending: "background:#94a3b8;color:white;",
                }[status] || "background:#94a3b8;color:white;"
            );
        },
        scoreColor(score) {
            if (!score) return "color:#64748b;";
            if (score < 0.4) return "color:#059669;";
            if (score < 0.7) return "color:#d97706;";
            return "color:#dc2626;";
        },
    };
};

window.liveFeed = function () {
    return {
        connected: false,
        paused: false,
        events: [],
        ws: null,
        reconnectTimer: null,

        init() {
            this.connect();
        },

        connect() {
            var self = this;
            try {
                self.ws = new WebSocket("ws://localhost:3001/ws");
                self.ws.onopen = function () {
                    self.connected = true;
                    self.ws.send(
                        JSON.stringify({
                            type: "identify",
                            merchant_id:
                                document.querySelector("[data-merchant-id]")
                                    ?.dataset.merchantId || "",
                        }),
                    );
                    if (self.reconnectTimer) {
                        clearTimeout(self.reconnectTimer);
                        self.reconnectTimer = null;
                    }
                };
                self.ws.onmessage = function (e) {
                    try {
                        var data = JSON.parse(e.data);
                        if (data.type === "transaction") {
                            self.events.unshift(
                                Object.assign({}, data, {
                                    id: Date.now() + Math.random(),
                                }),
                            );
                            if (self.events.length > 50)
                                self.events = self.events.slice(0, 50);
                            if (!self.paused && self.$refs.feedContainer) {
                                self.$nextTick(function () {
                                    self.$refs.feedContainer.scrollTop = 0;
                                });
                            }
                        }
                    } catch (err) {}
                };
                self.ws.onclose = function () {
                    self.connected = false;
                    self.reconnectTimer = setTimeout(function () {
                        self.reconnectTimer = null;
                        self.connect();
                    }, 3000);
                };
                self.ws.onerror = function () {
                    self.connected = false;
                };
            } catch (e) {
                self.connected = false;
                self.reconnectTimer = setTimeout(function () {
                    self.reconnectTimer = null;
                    self.connect();
                }, 3000);
            }
        },

        decisionColor: function (d) {
            return (
                { allow: "#059669", step_up: "#d97706", decline: "#dc2626" }[
                    d
                ] || "#64748b"
            );
        },
        decisionBg: function (d) {
            return (
                { allow: "#ecfdf5", step_up: "#fffbeb", decline: "#fef2f2" }[
                    d
                ] || "#f1f5f9"
            );
        },
        riskColor: function (s) {
            return s < 0.4 ? "#059669" : s < 0.7 ? "#d97706" : "#dc2626";
        },
        formatTime: function (iso) {
            if (!iso) return "";
            return new Date(iso).toLocaleTimeString("en-NG", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            });
        },
    };
};

window.Alpine = Alpine;
Alpine.start();
