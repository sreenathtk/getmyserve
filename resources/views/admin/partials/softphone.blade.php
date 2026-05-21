{{-- Floating softphone widget — Bootstrap 5 + Alpine.js --}}
<audio id="ziwo-ringtone" src="/sounds/ringtone.mp3" loop preload="none"></audio>

<style>
    .softphone-fab {
        position: fixed;
        bottom: 70px;
        right: 24px;
        z-index: 1050;
    }
    .softphone-btn {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #5664d2;
        border: none;
        color: #fff;
        font-size: 1.2rem;
        box-shadow: 0 4px 16px rgba(86,100,210,.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .2s;
        position: relative;
    }
    .softphone-btn:hover { background: #4a57c0; }
    .softphone-status-dot {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    .softphone-panel {
        position: fixed;
        bottom: 130px;
        right: 24px;
        width: 280px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,.15);
        z-index: 1049;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    .softphone-header {
        background: #2a3042;
        color: #fff;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 600;
    }
    .softphone-body { padding: 16px; }
    .dialpad-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .dialpad-key {
        padding: 10px 0;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        transition: background .15s;
    }
    .dialpad-key:hover { background: #e9ecef; }
    .call-timer {
        font-size: 2rem;
        font-weight: 700;
        font-family: monospace;
        color: #5664d2;
        text-align: center;
        letter-spacing: 2px;
    }
    .pulse-ring {
        animation: pulse-ring 1.2s ease-out infinite;
    }
    @keyframes pulse-ring {
        0%   { box-shadow: 0 0 0 0 rgba(244,106,106,.5); }
        70%  { box-shadow: 0 0 0 12px rgba(244,106,106,0); }
        100% { box-shadow: 0 0 0 0 rgba(244,106,106,0); }
    }
    .status-dot-offline  { background: #adb5bd; }
    .status-dot-online   { background: #34c38f; }
    .status-dot-ringing  { background: #f46a6a; animation: pulse-ring 1s infinite; }
    .status-dot-on_call  { background: #5664d2; }
    .status-dot-connecting { background: #f1b44c; }
</style>

<div x-data="softphone" class="softphone-fab">

    {{-- FAB Toggle Button --}}
    <button @click="panelOpen = !panelOpen" class="softphone-btn" title="Softphone">
        <i class="ri-phone-line"></i>
        <span class="softphone-status-dot" :class="'status-dot-' + status"></span>
    </button>

    {{-- Softphone Panel --}}
    <div class="softphone-panel" x-show="panelOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translateY(10px)"
         x-transition:enter-end="opacity-100 translateY(0)"
         style="display:none;">

        {{-- Header --}}
        <div class="softphone-header">
            <span><i class="ri-phone-fill me-1"></i> Softphone</span>
            <div class="d-flex align-items-center gap-2">
                <span class="softphone-status-dot d-inline-block" :class="'status-dot-' + status"
                      style="position:static;border-color:#2a3042;"></span>
                <small x-text="statusLabel" style="text-transform:capitalize;"></small>
                <button @click="panelOpen = false"
                        style="background:none;border:none;color:#adb5bd;font-size:16px;line-height:1;padding:0 0 0 8px;">
                    &times;
                </button>
            </div>
        </div>

        {{-- Error --}}
        <div x-show="error" class="alert alert-danger alert-dismissible m-2 mb-0 py-2 px-3" style="font-size:12px;">
            <span x-text="error"></span>
            <button @click="error=null" type="button" class="btn-close" style="font-size:10px;"></button>
        </div>

        {{-- ── OFFLINE ── --}}
        <div x-show="status === 'offline'" class="softphone-body text-center">
            <p class="text-muted mb-3" style="font-size:13px;">You are offline. Go online to make and receive calls.</p>
            <button @click="goOnline()" class="btn btn-success btn-sm w-100">
                <i class="ri-phone-fill me-1"></i> Go Online
            </button>
        </div>

        {{-- ── CONNECTING ── --}}
        <div x-show="status === 'connecting'" class="softphone-body text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
            <p class="text-muted mb-0" style="font-size:13px;">Connecting to ZIWO…</p>
        </div>

        {{-- ── RINGING (incoming) ── --}}
        <div x-show="status === 'ringing'" class="softphone-body text-center">
            <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3 pulse-ring"
                 style="width:60px;height:60px;">
                <i class="ri-phone-incoming-line text-danger" style="font-size:1.6rem;"></i>
            </div>
            <p class="fw-semibold mb-1" x-text="incomingCall?.fromName ?? 'Unknown Caller'"></p>
            <p class="text-muted mb-1" style="font-size:12px;font-family:monospace;" x-text="incomingCall?.from"></p>
            <span class="badge bg-danger mb-3">Incoming Call</span>
            <div class="d-flex gap-2">
                <button @click="answerCall()" class="btn btn-success btn-sm flex-fill">
                    <i class="ri-phone-fill"></i> Answer
                </button>
                <button @click="rejectCall()" class="btn btn-danger btn-sm flex-fill">
                    <i class="ri-phone-close-fill"></i> Decline
                </button>
            </div>
        </div>

        {{-- ── ONLINE (dialpad) ── --}}
        <div x-show="status === 'online'" class="softphone-body">
            <div class="input-group mb-3">
                <input x-model="dialpadNumber" type="tel"
                       class="form-control text-center fw-bold"
                       style="font-size:16px;font-family:monospace;letter-spacing:2px;"
                       placeholder="+971…" />
                <button @click="clearNumber()" class="btn btn-outline-secondary" type="button">⌫</button>
            </div>
            <div class="dialpad-grid">
                @foreach (['1','2','3','4','5','6','7','8','9','*','0','#'] as $key)
                    <div @click="pressKey('{{ $key }}')" class="dialpad-key">{{ $key }}</div>
                @endforeach
            </div>
            <div class="d-flex gap-2">
                <button @click="dial()" :disabled="!dialpadNumber" class="btn btn-success btn-sm flex-fill">
                    <i class="ri-phone-fill"></i> Call
                </button>
                <button @click="goOffline()" class="btn btn-outline-secondary btn-sm">
                    Offline
                </button>
            </div>
        </div>

        {{-- ── ON CALL ── --}}
        <div x-show="status === 'on_call'" class="softphone-body">
            <div class="text-center mb-3">
                <p class="mb-1 fw-semibold" style="font-family:monospace;" x-text="dialpadNumber || 'Active Call'"></p>
                <div class="call-timer" x-text="formattedDuration"></div>
                <span x-show="isOnHold" class="badge bg-warning text-dark mt-1">ON HOLD</span>
            </div>

            <div class="row g-2 mb-3">
                {{-- Mute --}}
                <div class="col-4">
                    <button @click="toggleMute()"
                            :class="isMuted ? 'btn-danger' : 'btn-outline-secondary'"
                            class="btn btn-sm w-100 d-flex flex-column align-items-center py-2 gap-1"
                            style="font-size:11px;">
                        <i :class="isMuted ? 'ri-mic-off-line' : 'ri-mic-line'" style="font-size:1.1rem;"></i>
                        <span x-text="isMuted ? 'Unmute' : 'Mute'"></span>
                    </button>
                </div>
                {{-- Hold --}}
                <div class="col-4">
                    <button @click="toggleHold()"
                            :class="isOnHold ? 'btn-warning' : 'btn-outline-secondary'"
                            class="btn btn-sm w-100 d-flex flex-column align-items-center py-2 gap-1"
                            style="font-size:11px;">
                        <i class="ri-pause-circle-line" style="font-size:1.1rem;"></i>
                        <span x-text="isOnHold ? 'Unhold' : 'Hold'"></span>
                    </button>
                </div>
                {{-- Transfer --}}
                <div class="col-4" x-data="{ transferOpen: false, transferNumber: '' }">
                    <button @click="transferOpen = !transferOpen"
                            class="btn btn-outline-secondary btn-sm w-100 d-flex flex-column align-items-center py-2 gap-1"
                            style="font-size:11px;">
                        <i class="ri-phone-forward-line" style="font-size:1.1rem;"></i>
                        <span>Transfer</span>
                    </button>
                    <div x-show="transferOpen" @click.away="transferOpen = false"
                         class="position-absolute bg-white border rounded shadow p-2"
                         style="bottom:100%;right:0;width:180px;z-index:10;">
                        <input x-model="transferNumber" type="tel" placeholder="Transfer to…"
                               class="form-control form-control-sm mb-1" />
                        <button @click="transfer(transferNumber); transferOpen = false"
                                class="btn btn-primary btn-sm w-100" style="font-size:11px;">
                            Transfer
                        </button>
                    </div>
                </div>
            </div>

            <button @click="hangup()" class="btn btn-danger w-100">
                <i class="ri-phone-close-fill me-1"></i> End Call
            </button>
        </div>

    </div>{{-- /panel --}}
</div>{{-- /softphone-fab --}}

@php $ziwoAccountName = config('ziwo.account_name', ''); @endphp
<script>
    window.ziwoConfig = { accountName: '{{ $ziwoAccountName }}' };
    window.csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
</script>

{{-- Load softphone Alpine component after Alpine is ready --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('softphone', () => ({
        status:        'offline',
        ziwoClient:    null,
        activeCallId:  null,
        incomingCall:  null,
        dialpadNumber: '',
        dialContext:   null,
        isMuted:       false,
        isOnHold:      false,
        callDuration:  0,
        callTimer:     null,
        error:         null,
        panelOpen:     false,

        init() {
            // Listen for dial-from-entity-page events (call buttons on Order/Customer pages)
            window.addEventListener('ziwo:dial', ({ detail }) => {
                this.dialpadNumber = detail.phone ?? '';
                this.dialContext   = (detail.entity_type && detail.entity_id)
                                     ? { entity_type: detail.entity_type, entity_id: detail.entity_id }
                                     : null;
                this.panelOpen     = true;
                if (this.status === 'online') this.dial();
            });
        },

        async goOnline() {
            this.status = 'connecting';
            this.error  = null;
            try {
                const res  = await fetch('/admin/calls/agent-token', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Could not get agent token (HTTP ' + res.status + ')');
                const data = await res.json();

                // Initialise ZIWO SDK — adapt event names to actual SDK docs
                this.ziwoClient = new window.ZiwoClient({
                    token:   data.token,
                    account: window.ziwoConfig.accountName,
                });

                this.ziwoClient.on('ready',        ()     => { this.status = 'online'; });
                this.ziwoClient.on('incoming_call', (call) => this.onIncomingCall(call));
                this.ziwoClient.on('call_answered', (call) => this.onCallAnswered(call));
                this.ziwoClient.on('call_ended',    ()     => this.onCallEnded());
                this.ziwoClient.on('call_held',     ()     => { this.isOnHold = true; });
                this.ziwoClient.on('call_unheld',   ()     => { this.isOnHold = false; });
                this.ziwoClient.on('muted',         ()     => { this.isMuted = true; });
                this.ziwoClient.on('unmuted',        ()    => { this.isMuted = false; });
                this.ziwoClient.on('error',         (err)  => { this.error = err.message ?? 'SDK error'; });

                await this.ziwoClient.connect();
            } catch (e) {
                this.status = 'offline';
                this.error  = e.message;
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
                const res = await fetch('/admin/calls/dial', {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'X-CSRF-TOKEN':     window.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ phone: this.dialpadNumber, ...(this.dialContext ?? {}) }),
                });
                if (!res.ok) throw new Error('Dial request failed');
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
            if (this.status === 'on_call') this.ziwoClient?.sendDTMF(key);
        },

        clearNumber() {
            this.dialpadNumber = this.dialpadNumber.slice(0, -1);
        },

        onIncomingCall(call) {
            this.incomingCall = call;
            this.activeCallId = call.callId;
            this.status       = 'ringing';
            this.panelOpen    = true;
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
            this.dialContext   = null;
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
            return { offline:'Offline', connecting:'Connecting…', online:'Online',
                     ringing:'Incoming Call', on_call:'On Call' }[this.status] ?? this.status;
        },
    }));
});
</script>
