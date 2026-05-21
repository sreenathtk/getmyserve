/**
 * Alpine.js softphone component.
 * Wraps the ZIWO JS SDK (ziwo-sdk.min.js must be loaded before this file).
 * Hold/Mute/Transfer are handled entirely by the SDK over WebRTC.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('softphone', () => ({
        status:       'offline', // offline|connecting|online|ringing|on_call
        ziwoClient:   null,
        activeCallId: null,
        incomingCall: null,      // { callId, from, fromName }
        dialpadNumber:'',
        isMuted:      false,
        isOnHold:     false,
        callDuration: 0,
        callTimer:    null,
        error:        null,
        panelOpen:    false,

        init() {
            // Listen for "dial this number" events from call buttons on entity pages
            window.addEventListener('ziwo:dial', ({ detail }) => {
                this.dialpadNumber = detail.phone;
                this.panelOpen     = true;
                if (this.status === 'online') this.dial();
            });

            // Incoming call popup from WebSocket (server-side missed call)
            window.addEventListener('ziwo:call-missed', () => {
                // Already handled by SDK; server broadcast is for non-active-agent admins
            });
        },

        async goOnline() {
            this.status = 'connecting';
            this.error  = null;
            try {
                const res   = await fetch('/admin/calls/agent-token', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data  = await res.json();

                // Initialise the ZIWO SDK — adapt method names to actual ZIWO SDK docs
                this.ziwoClient = new window.ZiwoClient({
                    token:   data.token,
                    account: window.ziwoConfig.accountName,
                });

                this.ziwoClient.on('ready',        () => { this.status = 'online'; });
                this.ziwoClient.on('incoming_call', (call) => this.onIncomingCall(call));
                this.ziwoClient.on('call_answered', (call) => this.onCallAnswered(call));
                this.ziwoClient.on('call_ended',    ()     => this.onCallEnded());
                this.ziwoClient.on('call_held',     ()     => { this.isOnHold = true; });
                this.ziwoClient.on('call_unheld',   ()     => { this.isOnHold = false; });
                this.ziwoClient.on('muted',         ()     => { this.isMuted = true; });
                this.ziwoClient.on('unmuted',       ()     => { this.isMuted = false; });
                this.ziwoClient.on('error',         (err)  => { this.error = err.message || 'SDK error'; });

                await this.ziwoClient.connect();
            } catch (e) {
                this.status = 'offline';
                this.error  = 'Failed to connect: ' + e.message;
            }
        },

        goOffline() {
            this.ziwoClient?.disconnect();
            this.status = 'offline';
            this.stopTimer();
        },

        async dial() {
            if (!this.dialpadNumber || this.status !== 'online') return;
            try {
                await fetch('/admin/calls/dial', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ phone: this.dialpadNumber }),
                });
                this.status = 'on_call';
                this.startTimer();
            } catch (e) {
                this.error = 'Dial failed: ' + e.message;
            }
        },

        answerCall() {
            this.ziwoClient?.answer(this.activeCallId);
            this.incomingCall = null;
            this.status       = 'on_call';
            document.getElementById('ziwo-ringtone')?.pause();
            this.startTimer();
        },

        rejectCall() {
            this.ziwoClient?.reject(this.activeCallId);
            this.incomingCall = null;
            this.activeCallId = null;
            this.status       = 'online';
            document.getElementById('ziwo-ringtone')?.pause();
        },

        hangup() {
            this.ziwoClient?.hangup(this.activeCallId);
            this.onCallEnded();
        },

        toggleHold() {
            this.isOnHold
                ? this.ziwoClient?.unhold(this.activeCallId)
                : this.ziwoClient?.hold(this.activeCallId);
        },

        toggleMute() {
            this.isMuted
                ? this.ziwoClient?.unmute(this.activeCallId)
                : this.ziwoClient?.mute(this.activeCallId);
        },

        transfer(toNumber) {
            if (!toNumber) return;
            this.ziwoClient?.blindTransfer(this.activeCallId, toNumber);
        },

        pressKey(key) {
            this.dialpadNumber += key;
            if (this.status === 'on_call') {
                this.ziwoClient?.sendDTMF(key);
            }
        },

        clearNumber() {
            this.dialpadNumber = this.dialpadNumber.slice(0, -1);
        },

        // SDK event handlers
        onIncomingCall(call) {
            this.incomingCall  = call;
            this.activeCallId  = call.callId;
            this.status        = 'ringing';
            document.getElementById('ziwo-ringtone')?.play().catch(() => {});
        },

        onCallAnswered(call) {
            this.activeCallId = call.callId;
            this.status       = 'on_call';
            document.getElementById('ziwo-ringtone')?.pause();
            this.startTimer();
        },

        onCallEnded() {
            this.activeCallId = null;
            this.incomingCall = null;
            this.isOnHold     = false;
            this.isMuted      = false;
            this.status       = 'online';
            this.stopTimer();
        },

        startTimer() {
            this.callDuration = 0;
            this.callTimer    = setInterval(() => { this.callDuration++; }, 1000);
        },

        stopTimer() {
            clearInterval(this.callTimer);
            this.callDuration = 0;
        },

        get formattedDuration() {
            const h = Math.floor(this.callDuration / 3600);
            const m = Math.floor((this.callDuration % 3600) / 60);
            const s = this.callDuration % 60;
            return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        },

        get statusLabel() {
            const map = {
                offline:    'Offline',
                connecting: 'Connecting…',
                online:     'Online',
                ringing:    'Incoming Call',
                on_call:    'On Call',
            };
            return map[this.status] ?? this.status;
        },

        get statusColor() {
            const map = {
                offline:    'bg-gray-400',
                connecting: 'bg-yellow-400',
                online:     'bg-green-400',
                ringing:    'bg-red-400 animate-pulse',
                on_call:    'bg-blue-400',
            };
            return map[this.status] ?? 'bg-gray-400';
        },
    }));
});
