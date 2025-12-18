define([
    'core/ajax',
    'core/templates',
    'core/toast',
    'local_curriculum/vendor/sortable'
], function(Ajax, Templates, Toast, Sortable) {

    let state = null;

    /* ============================================================
     * Public API
     * ============================================================ */

    const init = function(initialState) {
        state = initialState;

        if (!state || state.readonly) {
            return;
        }

        initAreaSorting();
        initSubjectSorting();
        initSaveButton();
        initIhsInputs();
    };

    /* ============================================================
     * Drag & Drop: Areas
     * ============================================================ */

    function initAreaSorting() {
        const container = document.querySelector('.plan-areas');

        if (!container) {
            return;
        }

        new Sortable(container, {
            handle: '.area-drag-handle',
            animation: 150,
            filter: '.area-readonly',
            onEnd: function(evt) {
                moveArea(evt.oldIndex, evt.newIndex);
            }
        });
    }

    function moveArea(oldIndex, newIndex) {
        const area = state.areas.splice(oldIndex, 1)[0];
        state.areas.splice(newIndex, 0, area);
        state.dirty = true;
    }

    /* ============================================================
     * Drag & Drop: Subjects
     * ============================================================ */

    function initSubjectSorting() {
        document.querySelectorAll('.subjects-container').forEach(container => {
            new Sortable(container, {
                group: 'subjects',
                handle: '.subject-drag-handle',
                animation: 150,
                onEnd: function(evt) {
                    const fromAreaIndex = parseInt(evt.from.dataset.areaIndex, 10);
                    const toAreaIndex   = parseInt(evt.to.dataset.areaIndex, 10);

                    if (fromAreaIndex === toAreaIndex) {
                        moveSubjectWithinArea(
                            fromAreaIndex,
                            evt.oldIndex,
                            evt.newIndex
                        );
                    } else {
                        moveSubjectBetweenAreas(
                            fromAreaIndex,
                            toAreaIndex,
                            evt.oldIndex,
                            evt.newIndex
                        );
                    }
                }
            });
        });
    }

    function moveSubjectWithinArea(areaIndex, oldIndex, newIndex) {
        const subjects = state.areas[areaIndex].subjects;
        const subject = subjects.splice(oldIndex, 1)[0];
        subjects.splice(newIndex, 0, subject);
        state.dirty = true;
    }

    function moveSubjectBetweenAreas(fromAreaIndex, toAreaIndex, oldIndex, newIndex) {
        const fromSubjects = state.areas[fromAreaIndex].subjects;
        const toSubjects   = state.areas[toAreaIndex].subjects;

        const subject = fromSubjects.splice(oldIndex, 1)[0];
        subject.areaid = state.areas[toAreaIndex].id;

        toSubjects.splice(newIndex, 0, subject);
        state.dirty = true;

        renderAreas();
    }

    /* ============================================================
     * Render parcial
     * ============================================================ */

    function renderAreas() {
        const container = document.querySelector('.plan-areas');

        if (!container) {
            return;
        }

        Templates.render('local_curriculum/plan_areas', {
            areas: state.areas,
            readonly: state.readonly
        }).then(function(html) {
            container.innerHTML = html;

            initAreaSorting();
            initSubjectSorting();
            initIhsInputs();
        });
    }

    /* ============================================================
     * IHS
     * ============================================================ */

    function initIhsInputs() {
        document.querySelectorAll('.ihs-input').forEach(input => {
            input.addEventListener('change', function(e) {
                const subjectId = parseInt(e.target.dataset.subjectId, 10);
                const value = parseInt(e.target.value, 10);

                updateSubjectIhs(subjectId, value);
            });
        });
    }

    function updateSubjectIhs(subjectId, value) {
        state.areas.forEach(area => {
            area.subjects.forEach(subject => {
                if (subject.id === subjectId) {
                    subject.ihs = value;
                    state.dirty = true;
                }
            });
        });
    }

    /* ============================================================
     * Save
     * ============================================================ */

    function initSaveButton() {
        const button = document.querySelector('[data-action="save-plan"]');

        if (!button) {
            return;
        }

        button.addEventListener('click', function() {
            if (!state.dirty) {
                return;
            }

            button.disabled = true;

            saveStructure()
                .then(function() {
                    state.dirty = false;
                    showToast('Cambios guardados');
                })
                .catch(showError)
                .finally(function() {
                    button.disabled = false;
                });
        });
    }

    function saveStructure() {
        return Ajax.call([{
            methodname: 'local_curriculum_save_plan_structure',
            args: buildSavePayload()
        }])[0];
    }

    /* ============================================================
     * Payload
     * ============================================================ */

    function buildSavePayload() {
        normalizeSortOrders();

        return {
            planid: state.plan.id,
            areas: state.areas
                .filter(area => !area.is_virtual)
                .map(area => ({
                    id: area.id,
                    sortorder: area.sortorder,
                    subjects: area.subjects.map(subject => ({
                        id: subject.id,
                        sortorder: subject.sortorder,
                        ihs: subject.ihs
                    }))
                }))
        };
    }

    function normalizeSortOrders() {
        let areaOrder = 1;

        state.areas.forEach(area => {
            if (area.is_virtual) {
                return;
            }

            area.sortorder = areaOrder++;

            let subjectOrder = 1;
            area.subjects.forEach(subject => {
                subject.sortorder = subjectOrder++;
                subject.areaid = area.id;
            });
        });
    }

    /* ============================================================
     * Toasts
     * ============================================================ */

    function showToast(message) {
        Toast.add(message, { type: Toast.TYPE.SUCCESS });
    }

    function showError(error) {
        console.error(error);
        Toast.add(
            'Ocurrió un error al guardar los cambios',
            { type: Toast.TYPE.ERROR }
        );
    }

    return {
        init: init
    };
});