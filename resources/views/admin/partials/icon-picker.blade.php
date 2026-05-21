{{--
    Icon Picker Partial
    Variables:
      $inputName    - form field name  (default: 'icon')
      $currentIcon  - current value    (default: '')
      $pickerId     - unique id suffix (default: 'default') — set when using multiple pickers on one page
--}}
@php
    $inputName   = $inputName   ?? 'icon';
    $currentIcon = $currentIcon ?? '';
    $pickerId    = $pickerId    ?? 'default';
    $modalId     = 'iconPickerModal_' . $pickerId;
    $inputId     = 'iconInput_' . $pickerId;
    $previewId   = 'iconPreview_' . $pickerId;
    $searchId    = 'iconSearch_' . $pickerId;
    $gridId      = 'iconGrid_' . $pickerId;
@endphp

<div class="d-flex align-items-center gap-2 flex-wrap">
    {{-- Live preview --}}
    <div id="{{ $previewId }}" class="icon-picker-preview d-flex align-items-center justify-content-center"
         style="width:44px;height:44px;border:1px solid #dee2e6;border-radius:.375rem;background:#f8f9fa;font-size:1.4rem;color:#5664d2;flex-shrink:0;">
        @if($currentIcon)
            <i class="{{ $currentIcon }}"></i>
        @else
            <i class="ri-image-line text-muted" style="font-size:1.1rem;"></i>
        @endif
    </div>

    {{-- Text input --}}
    <input type="text"
           id="{{ $inputId }}"
           name="{{ $inputName }}"
           class="form-control @error($inputName) is-invalid @enderror"
           style="max-width:240px;"
           placeholder="e.g. fas fa-donate"
           value="{{ old($inputName, $currentIcon) }}"
           oninput="iconPickerPreviewUpdate('{{ $previewId }}', this.value)">
    @error($inputName)<div class="invalid-feedback">{{ $message }}</div>@enderror

    {{-- Browse button --}}
    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
        <i class="ri-search-line me-1"></i> Browse Icons
    </button>

    {{-- Clear button --}}
    @if($currentIcon)
    <button type="button" class="btn btn-outline-secondary btn-sm"
            onclick="iconPickerClear('{{ $inputId }}', '{{ $previewId }}')">
        <i class="ri-close-line"></i>
    </button>
    @endif

    <small class="text-muted w-100" style="font-size:11px;">Type a Font Awesome v5 class or click Browse to search.</small>
</div>

{{-- Modal --}}
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="ri-search-line me-2"></i>Pick a Font Awesome v5 Icon
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="{{ $searchId }}" class="form-control"
                           placeholder="Search icons… (e.g. plane, money, building)"
                           oninput="iconPickerFilter('{{ $gridId }}', '{{ $searchId }}')">
                </div>
                <div id="{{ $gridId }}" class="icon-picker-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:6px;max-height:480px;overflow-y:auto;">
                    {{-- Populated by JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    /* ── Icon list ── */
    const ICONS = [
        // Finance / Tax
        ['fas fa-donate','donate'],['fas fa-money-bill','money-bill'],['fas fa-money-bill-wave','money-bill-wave'],
        ['fas fa-dollar-sign','dollar-sign'],['fas fa-coins','coins'],['fas fa-piggy-bank','piggy-bank'],
        ['fas fa-wallet','wallet'],['fas fa-credit-card','credit-card'],['fas fa-receipt','receipt'],
        ['fas fa-hand-holding-usd','hand-holding-usd'],['fas fa-percentage','percentage'],
        ['fas fa-calculator','calculator'],['fas fa-cash-register','cash-register'],
        ['fas fa-file-invoice','file-invoice'],['fas fa-file-invoice-dollar','file-invoice-dollar'],
        // Charts / Analytics
        ['fas fa-chart-bar','chart-bar'],['fas fa-chart-line','chart-line'],['fas fa-chart-pie','chart-pie'],
        ['fas fa-chart-area','chart-area'],['fas fa-poll','poll'],['fas fa-tachometer-alt','tachometer-alt'],
        // Legal / Compliance
        ['fas fa-balance-scale','balance-scale'],['fas fa-gavel','gavel'],['fas fa-landmark','landmark'],
        ['fas fa-university','university'],['fas fa-scroll','scroll'],['fas fa-stamp','stamp'],
        ['fas fa-file-contract','file-contract'],['fas fa-file-signature','file-signature'],
        ['fas fa-handshake','handshake'],['fas fa-signature','signature'],
        // Business / Office
        ['fas fa-briefcase','briefcase'],['fas fa-building','building'],['fas fa-city','city'],
        ['fas fa-industry','industry'],['fas fa-store','store'],['fas fa-store-alt','store-alt'],
        ['fas fa-print','print'],['fas fa-fax','fax'],['fas fa-phone','phone'],['fas fa-phone-alt','phone-alt'],
        ['fas fa-headset','headset'],['fas fa-inbox','inbox'],['fas fa-mail-bulk','mail-bulk'],
        ['fas fa-envelope','envelope'],['fas fa-envelope-open-text','envelope-open-text'],
        // Documents / Files
        ['fas fa-file','file'],['fas fa-file-alt','file-alt'],['fas fa-file-pdf','file-pdf'],
        ['fas fa-file-word','file-word'],['fas fa-file-excel','file-excel'],['fas fa-file-powerpoint','file-powerpoint'],
        ['fas fa-file-archive','file-archive'],['fas fa-folder','folder'],['fas fa-folder-open','folder-open'],
        ['fas fa-clipboard','clipboard'],['fas fa-clipboard-list','clipboard-list'],['fas fa-clipboard-check','clipboard-check'],
        ['fas fa-tasks','tasks'],['fas fa-list-alt','list-alt'],['fas fa-book','book'],['fas fa-book-open','book-open'],
        // Visa / Travel
        ['fas fa-passport','passport'],['fas fa-plane','plane'],['fas fa-plane-departure','plane-departure'],
        ['fas fa-plane-arrival','plane-arrival'],['fas fa-suitcase','suitcase'],['fas fa-suitcase-rolling','suitcase-rolling'],
        ['fas fa-globe','globe'],['fas fa-globe-americas','globe-americas'],['fas fa-globe-asia','globe-asia'],
        ['fas fa-map','map'],['fas fa-map-marker-alt','map-marker-alt'],['fas fa-map-signs','map-signs'],
        ['fas fa-hotel','hotel'],['fas fa-car','car'],['fas fa-taxi','taxi'],['fas fa-bus','bus'],
        ['fas fa-ship','ship'],['fas fa-subway','subway'],['fas fa-train','train'],
        ['fab fa-cc-visa','cc-visa'],['fab fa-cc-mastercard','cc-mastercard'],
        // People / HR
        ['fas fa-user','user'],['fas fa-users','users'],['fas fa-user-tie','user-tie'],
        ['fas fa-user-check','user-check'],['fas fa-user-plus','user-plus'],['fas fa-user-friends','user-friends'],
        ['fas fa-id-card','id-card'],['fas fa-id-badge','id-badge'],['fas fa-address-card','address-card'],
        ['fas fa-address-book','address-book'],['fas fa-hard-hat','hard-hat'],['fas fa-people-carry','people-carry'],
        // Company Setup
        ['fas fa-registered','registered'],['fas fa-trademark','trademark'],['fas fa-copyright','copyright'],
        ['fas fa-certificate','certificate'],['fas fa-award','award'],['fas fa-medal','medal'],
        ['fas fa-trophy','trophy'],['fas fa-star','star'],['fas fa-shield-alt','shield-alt'],
        // IT / Tech
        ['fas fa-laptop','laptop'],['fas fa-desktop','desktop'],['fas fa-mobile-alt','mobile-alt'],
        ['fas fa-server','server'],['fas fa-wifi','wifi'],['fas fa-network-wired','network-wired'],
        ['fas fa-code','code'],['fas fa-database','database'],['fas fa-cog','cog'],['fas fa-cogs','cogs'],
        ['fas fa-cloud','cloud'],['fas fa-cloud-upload-alt','cloud-upload-alt'],['fas fa-satellite-dish','satellite-dish'],
        // Home / Property
        ['fas fa-home','home'],['fas fa-house-user','house-user'],['fas fa-key','key'],
        ['fas fa-door-open','door-open'],['fas fa-couch','couch'],['fas fa-tools','tools'],
        ['fas fa-wrench','wrench'],['fas fa-hammer','hammer'],['fas fa-paint-roller','paint-roller'],
        // Health / Medical
        ['fas fa-heartbeat','heartbeat'],['fas fa-medkit','medkit'],['fas fa-hospital','hospital'],
        ['fas fa-stethoscope','stethoscope'],['fas fa-pills','pills'],['fas fa-ambulance','ambulance'],
        ['fas fa-plus-circle','plus-circle'],
        // Education
        ['fas fa-graduation-cap','graduation-cap'],['fas fa-school','school'],['fas fa-chalkboard','chalkboard'],
        ['fas fa-pen','pen'],['fas fa-pencil-alt','pencil-alt'],['fas fa-book-reader','book-reader'],
        // Shopping / Logistics
        ['fas fa-shopping-cart','shopping-cart'],['fas fa-shopping-bag','shopping-bag'],['fas fa-gift','gift'],
        ['fas fa-truck','truck'],['fas fa-shipping-fast','shipping-fast'],['fas fa-box','box'],
        ['fas fa-boxes','boxes'],['fas fa-barcode','barcode'],['fas fa-qrcode','qrcode'],
        // Energy / Environment
        ['fas fa-recycle','recycle'],['fas fa-leaf','leaf'],['fas fa-sun','sun'],
        ['fas fa-water','water'],['fas fa-fire','fire'],['fas fa-bolt','bolt'],['fas fa-wind','wind'],
        // Communication / Media
        ['fas fa-comments','comments'],['fas fa-comment-alt','comment-alt'],['fas fa-microphone','microphone'],
        ['fas fa-camera','camera'],['fas fa-video','video'],['fas fa-photo-video','photo-video'],
        ['fas fa-bullhorn','bullhorn'],['fas fa-broadcast-tower','broadcast-tower'],
        // Tags / Misc
        ['fas fa-tags','tags'],['fas fa-tag','tag'],['fas fa-search','search'],
        ['fas fa-info-circle','info-circle'],['fas fa-question-circle','question-circle'],
        ['fas fa-lightbulb','lightbulb'],['fas fa-th-large','th-large'],['fas fa-th','th'],
        ['fas fa-check-circle','check-circle'],['fas fa-times-circle','times-circle'],
        ['fas fa-exclamation-circle','exclamation-circle'],['fas fa-bell','bell'],['fas fa-clock','clock'],
        ['fas fa-calendar-alt','calendar-alt'],['fas fa-paper-plane','paper-plane'],['fas fa-link','link'],
        // Brands
        ['fab fa-whatsapp','whatsapp'],['fab fa-telegram','telegram'],['fab fa-linkedin','linkedin'],
        ['fab fa-twitter','twitter'],['fab fa-facebook','facebook'],['fab fa-instagram','instagram'],
        ['fab fa-youtube','youtube'],['fab fa-google','google'],['fab fa-apple','apple'],
        ['fab fa-android','android'],['fab fa-windows','windows'],
        // Regular variants
        ['far fa-envelope','envelope (regular)'],['far fa-calendar-alt','calendar (regular)'],
        ['far fa-clock','clock (regular)'],['far fa-star','star (regular)'],
        ['far fa-heart','heart'],['far fa-user','user (regular)'],['far fa-file-alt','file-alt (regular)'],
    ];

    const MODAL_ID   = '{{ $modalId }}';
    const INPUT_ID   = '{{ $inputId }}';
    const PREVIEW_ID = '{{ $previewId }}';
    const GRID_ID    = '{{ $gridId }}';

    function buildGrid(filter) {
        const grid  = document.getElementById(GRID_ID);
        const lower = (filter || '').toLowerCase().trim();
        const items = lower ? ICONS.filter(i => i[0].includes(lower) || i[1].includes(lower)) : ICONS;
        grid.innerHTML = '';
        items.forEach(([cls, name]) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'icon-picker-item btn btn-light p-2 d-flex flex-column align-items-center gap-1';
            btn.title = cls;
            btn.style.cssText = 'font-size:12px;min-height:72px;border:1px solid #dee2e6;';
            btn.innerHTML = `<i class="${cls}" style="font-size:1.4rem;color:#5664d2;"></i><span style="word-break:break-all;line-height:1.1;">${name}</span>`;
            btn.addEventListener('click', () => {
                document.getElementById(INPUT_ID).value = cls;
                iconPickerPreviewUpdate(PREVIEW_ID, cls);
                bootstrap.Modal.getInstance(document.getElementById(MODAL_ID)).hide();
            });
            grid.appendChild(btn);
        });
        if (!items.length) {
            grid.innerHTML = '<p class="text-muted p-3">No icons match your search.</p>';
        }
    }

    document.getElementById(MODAL_ID).addEventListener('show.bs.modal', function () {
        const search = document.getElementById('{{ $searchId }}');
        search.value = '';
        buildGrid('');
        setTimeout(() => search.focus(), 300);
    });

    window.iconPickerFilter = function(gridId, searchId) {
        const term = document.getElementById(searchId).value;
        buildGrid(term);
    };
})();

window.iconPickerPreviewUpdate = window.iconPickerPreviewUpdate || function(previewId, cls) {
    const box = document.getElementById(previewId);
    if (!box) return;
    box.innerHTML = cls.trim()
        ? `<i class="${cls.trim()}" style="font-size:1.4rem;color:#5664d2;"></i>`
        : `<i class="ri-image-line text-muted" style="font-size:1.1rem;"></i>`;
};

window.iconPickerClear = window.iconPickerClear || function(inputId, previewId) {
    document.getElementById(inputId).value = '';
    iconPickerPreviewUpdate(previewId, '');
};
</script>
