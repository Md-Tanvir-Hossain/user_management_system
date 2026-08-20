document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.user-checkbox');

    const selectionCount = document.getElementById('selection-count');

    const blockButton = document.getElementById('block-btn');
    const unblockButton = document.getElementById('unblock-btn');
    const deleteButton = document.getElementById('delete-btn');
    const deleteUnverifiedButton =
        document.getElementById('delete-unverified-btn');

    function updateSelection() {

        const selected = document.querySelectorAll(
            '.user-checkbox:checked'
        );

        const count = selected.length;

        selectionCount.textContent =
            count === 1
                ? '1 user selected'
                : `${count} users selected`;

        const hasSelection = count > 0;

        blockButton.disabled = !hasSelection;
        unblockButton.disabled = !hasSelection;
        deleteButton.disabled = !hasSelection;
        deleteUnverifiedButton.disabled = !hasSelection;

        if (selectAll) {
            selectAll.checked =
                count === checkboxes.length && count > 0;

            selectAll.indeterminate =
                count > 0 && count < checkboxes.length;
        }

        // Put selected IDs into all forms
        const containers = [
            'selected-user-inputs',
            'unblock-selected-user-inputs',
            'delete-selected-user-inputs',
            'delete-unverified-selected-user-inputs'
        ];

        containers.forEach(function (id) {
            const container = document.getElementById(id);

            if (!container) {
                return;
            }

            container.innerHTML = '';

            selected.forEach(function (checkbox) {
                const input = document.createElement('input');

                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = checkbox.value;

                container.appendChild(input);
            });
        });
    }

    // Select all
    selectAll.addEventListener('change', function () {

        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });

        updateSelection();
    });

    // Individual checkboxes
    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {
            updateSelection();
        });

    });

    // Initial state
    updateSelection();

    console.log('Admin users JavaScript WORKING');
});