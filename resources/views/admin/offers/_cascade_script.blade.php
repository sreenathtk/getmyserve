<script>
(function () {
    const allCategories = @json($categories);
    const allSubCats    = @json($subCategories);
    const allServices   = @json($services);
    const container     = document.getElementById('service-rows-container');
    const addBtn        = document.getElementById('add-service-row');

    function esc(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function catOptions(selectedId) {
        let html = '<option value="">— Category —</option>';
        allCategories.forEach(c => {
            html += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${esc(c.name)}</option>`;
        });
        return html;
    }

    function subCatOptions(catId, selectedId) {
        let html = '<option value="">— Sub-Category —</option>';
        if (catId) {
            allSubCats.filter(s => s.category_id == catId).forEach(s => {
                html += `<option value="${s.id}" ${s.id == selectedId ? 'selected' : ''}>${esc(s.name)}</option>`;
            });
        }
        return html;
    }

    function serviceOptions(subCatId, selectedId) {
        let html = '<option value="">— Service —</option>';
        if (subCatId) {
            allServices.filter(s => s.sub_category_id == subCatId).forEach(s => {
                html += `<option value="${s.id}" ${s.id == selectedId ? 'selected' : ''}>${esc(s.name)}</option>`;
            });
        }
        return html;
    }

    function packageOptions(selectedPkg) {
        return `<option value="">— Package —</option>` +
               `<option value="basic" ${selectedPkg === 'basic' ? 'selected' : ''}>Basic</option>` +
               `<option value="premium" ${selectedPkg === 'premium' ? 'selected' : ''}>Premium</option>`;
    }

    function createRow(catId, subCatId, svcId, pkg) {
        const div = document.createElement('div');
        div.className = 'service-row border rounded p-2 mb-2 bg-light';

        const selectedSvc = allServices.find(s => s.id == svcId);
        const hasPremium  = !!(selectedSvc && selectedSvc.has_premium_package);
        const pkgDisplay  = hasPremium ? '' : 'none';

        div.innerHTML = `
            <div class="d-flex gap-2 align-items-end">
                <div class="flex-fill">
                    <div class="row g-2">
                        <div class="col-sm-3">
                            <label class="form-label small text-muted mb-1">Category</label>
                            <select class="form-select form-select-sm service-row-category">
                                ${catOptions(catId)}
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label small text-muted mb-1">Sub-Category</label>
                            <select class="form-select form-select-sm service-row-subcategory" ${catId ? '' : 'disabled'}>
                                ${subCatOptions(catId, subCatId)}
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label small text-muted mb-1">Service <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm service-row-service" name="service_ids[]" required ${subCatId ? '' : 'disabled'}>
                                ${serviceOptions(subCatId, svcId)}
                            </select>
                        </div>
                        <div class="col-sm-3 service-row-package-wrap" style="display:${pkgDisplay}">
                            <label class="form-label small text-muted mb-1">Package</label>
                            <select class="form-select form-select-sm service-row-package" name="service_packages[]">
                                ${packageOptions(hasPremium ? (pkg || '') : '')}
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm service-row-remove flex-shrink-0" title="Remove service">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;

        const catSel    = div.querySelector('.service-row-category');
        const subCatSel = div.querySelector('.service-row-subcategory');
        const svcSel    = div.querySelector('.service-row-service');
        const pkgWrap   = div.querySelector('.service-row-package-wrap');
        const pkgSel    = div.querySelector('.service-row-package');
        const removeBtn = div.querySelector('.service-row-remove');

        catSel.addEventListener('change', function () {
            subCatSel.innerHTML = subCatOptions(this.value, null);
            subCatSel.disabled  = !this.value;
            svcSel.innerHTML    = serviceOptions(null, null);
            svcSel.disabled     = true;
            pkgWrap.style.display = 'none';
            pkgSel.value = '';
        });

        subCatSel.addEventListener('change', function () {
            svcSel.innerHTML = serviceOptions(this.value, null);
            svcSel.disabled  = !this.value;
            pkgWrap.style.display = 'none';
            pkgSel.value = '';
        });

        svcSel.addEventListener('change', function () {
            const svc = allServices.find(s => s.id == this.value);
            if (svc && svc.has_premium_package) {
                pkgWrap.style.display = '';
                pkgSel.innerHTML = packageOptions('basic');
                pkgSel.value = 'basic';
            } else {
                pkgWrap.style.display = 'none';
                pkgSel.value = '';
            }
        });

        removeBtn.addEventListener('click', function () {
            if (container.querySelectorAll('.service-row').length > 1) {
                div.remove();
            }
        });

        return div;
    }

    // Render initial rows (from $selectedServices passed by the controller)
    const initData = @json($selectedServices);
    initData.forEach(function (item) {
        container.appendChild(createRow(item.category_id, item.sub_category_id, item.service_id, item.package || ''));
    });

    addBtn.addEventListener('click', function () {
        container.appendChild(createRow('', '', '', ''));
    });
})();
</script>
