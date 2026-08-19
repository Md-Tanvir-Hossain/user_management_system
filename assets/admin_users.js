/*
 * Admin User Management JavaScript
 *
 * This file is loaded globally through assets/app.js.
 * Therefore, the initialization function checks whether
 * the admin page actually exists before doing anything.
 */

function initializeAdminUsers() {

    /*
     * ============================================================
     * CHECK THAT WE ARE ON THE ADMIN USERS PAGE
     * ============================================================
     */

    const table = document.getElementById('users-table');

    if (!table) {
        return;
    }

    /*
     * Prevent initializing the same page twice.
     */

    if (table.dataset.jsInitialized === 'true') {
        return;
    }

    table.dataset.jsInitialized = 'true';


    /*
     * ============================================================
     * ELEMENTS
     * ============================================================
     */

    const selectAll =
        document.getElementById('select-all');

    const userCheckboxes =
        document.querySelectorAll('.user-checkbox');

    const selectionCount =
        document.getElementById('selection-count');

    const blockButton =
        document.getElementById('block-btn');

    const unblockButton =
        document.getElementById('unblock-btn');

    const deleteButton =
        document.getElementById('delete-btn');

    const deleteUnverifiedButton =
        document.getElementById('delete-unverified-btn');

    const selectedUserInputs =
        document.getElementById('selected-user-inputs');

    const unblockSelectedUserInputs =
        document.getElementById(
            'unblock-selected-user-inputs'
        );

    const deleteSelectedUserInputs =
        document.getElementById(
            'delete-selected-user-inputs'
        );

    const deleteUnverifiedSelectedUserInputs =
        document.getElementById(
            'delete-unverified-selected-user-inputs'
        );


    /*
     * ============================================================
     * UPDATE SELECTION
     * ============================================================
     */

    function updateSelection() {

        const selected =
            document.querySelectorAll(
                '.user-checkbox:checked'
            );

        const count = selected.length;


        /*
         * Selection counter
         */

        if (selectionCount) {

            selectionCount.textContent =
                count === 1
                    ? '1 user selected'
                    : `${count} users selected`;

        }


        /*
         * Select-all checkbox
         */

        if (selectAll) {

            selectAll.checked =
                count > 0 &&
                count === userCheckboxes.length;

            selectAll.indeterminate =
                count > 0 &&
                count < userCheckboxes.length;

        }


        /*
         * Toolbar buttons
         */

        const hasSelection = count > 0;


        if (blockButton) {
            blockButton.disabled = !hasSelection;
        }

        if (unblockButton) {
            unblockButton.disabled = !hasSelection;
        }

        if (deleteButton) {
            deleteButton.disabled = !hasSelection;
        }

        if (deleteUnverifiedButton) {
            deleteUnverifiedButton.disabled = !hasSelection;
        }


        /*
         * Clear hidden inputs
         */

        if (selectedUserInputs) {
            selectedUserInputs.innerHTML = '';
        }

        if (unblockSelectedUserInputs) {
            unblockSelectedUserInputs.innerHTML = '';
        }

        if (deleteSelectedUserInputs) {
            deleteSelectedUserInputs.innerHTML = '';
        }

        if (deleteUnverifiedSelectedUserInputs) {
            deleteUnverifiedSelectedUserInputs.innerHTML = '';
        }


        /*
         * Add selected IDs to every form
         */

        selected.forEach(function (checkbox) {

            const value = checkbox.value;


            if (selectedUserInputs) {

                const input =
                    document.createElement('input');

                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = value;

                selectedUserInputs.appendChild(input);

            }


            if (unblockSelectedUserInputs) {

                const input =
                    document.createElement('input');

                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = value;

                unblockSelectedUserInputs.appendChild(input);

            }


            if (deleteSelectedUserInputs) {

                const input =
                    document.createElement('input');

                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = value;

                deleteSelectedUserInputs.appendChild(input);

            }


            if (deleteUnverifiedSelectedUserInputs) {

                const input =
                    document.createElement('input');

                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = value;

                deleteUnverifiedSelectedUserInputs.appendChild(input);

            }

        });

    }


    /*
     * ============================================================
     * SELECT ALL
     * ============================================================
     */

    if (selectAll) {

        selectAll.addEventListener(
            'change',
            function () {

                userCheckboxes.forEach(
                    function (checkbox) {

                        checkbox.checked =
                            selectAll.checked;

                    }
                );

                updateSelection();

            }
        );

    }


    /*
     * ============================================================
     * INDIVIDUAL CHECKBOXES
     * ============================================================
     */

    userCheckboxes.forEach(
        function (checkbox) {

            checkbox.addEventListener(
                'change',
                function () {

                    updateSelection();

                }
            );

        }
    );


    /*
     * ============================================================
     * SEARCH
     * ============================================================
     */

    const searchInput =
        document.getElementById('user-search');

    const statusFilter =
        document.getElementById('status-filter');


    function filterUsers() {

        const searchText =
            searchInput
                ? searchInput.value
                    .toLowerCase()
                    .trim()
                : '';

        const selectedStatus =
            statusFilter
                ? statusFilter.value.toLowerCase()
                : 'all';


        const rows =
            table.querySelectorAll('.user-row');


        rows.forEach(function (row) {

            const name =
                (row.dataset.name || '')
                    .toLowerCase();

            const email =
                (row.dataset.email || '')
                    .toLowerCase();

            const status =
                (row.dataset.status || '')
                    .toLowerCase();


            const matchesSearch =
                name.includes(searchText) ||
                email.includes(searchText);


            const matchesStatus =
                selectedStatus === 'all' ||
                status === selectedStatus;


            row.style.display =
                matchesSearch && matchesStatus
                    ? ''
                    : 'none';

        });

    }


    if (searchInput) {

        searchInput.addEventListener(
            'input',
            filterUsers
        );

    }


    if (statusFilter) {

        statusFilter.addEventListener(
            'change',
            filterUsers
        );

    }


    /*
     * ============================================================
     * COLUMN SORTING
     * ============================================================
     */

    const sortableHeaders =
        document.querySelectorAll('.sortable');


    sortableHeaders.forEach(
        function (header) {

            header.addEventListener(
                'click',
                function () {

                    const columnIndex =
                        Number(
                            header.dataset.column
                        );


                    const currentDirection =
                        header.dataset.sortDirection ||
                        'none';


                    const newDirection =
                        currentDirection === 'asc'
                            ? 'desc'
                            : 'asc';


                    /*
                     * Reset all headers
                     */

                    sortableHeaders.forEach(
                        function (otherHeader) {

                            otherHeader.dataset.sortDirection =
                                'none';

                            const indicator =
                                otherHeader.querySelector(
                                    '.sort-indicator'
                                );

                            if (indicator) {
                                indicator.textContent = '';
                            }

                        }
                    );


                    /*
                     * Set current header
                     */

                    header.dataset.sortDirection =
                        newDirection;


                    const indicator =
                        header.querySelector(
                            '.sort-indicator'
                        );


                    if (indicator) {

                        indicator.textContent =
                            newDirection === 'asc'
                                ? ' ↑'
                                : ' ↓';

                    }


                    sortTable(
                        columnIndex,
                        newDirection
                    );

                }
            );

        }
    );


    /*
     * ============================================================
     * SORT TABLE
     * ============================================================
     */

    function sortTable(
        columnIndex,
        direction
    ) {

        const tbody =
            table.querySelector('tbody');

        if (!tbody) {
            return;
        }


        const rows =
            Array.from(
                tbody.querySelectorAll('.user-row')
            );


        rows.sort(function (rowA, rowB) {

            const cellA =
                rowA.children[columnIndex];

            const cellB =
                rowB.children[columnIndex];


            if (!cellA || !cellB) {
                return 0;
            }


            /*
             * Use data-sort-value if available.
             */

            let valueA =
                cellA.dataset.sortValue ??
                cellA.textContent.trim();

            let valueB =
                cellB.dataset.sortValue ??
                cellB.textContent.trim();


            valueA =
                valueA.toLowerCase();

            valueB =
                valueB.toLowerCase();


            /*
             * ID
             *
             * Column 0 = checkbox
             * Column 1 = ID
             */

            if (columnIndex === 1) {

                const numberA =
                    Number(valueA);

                const numberB =
                    Number(valueB);


                return direction === 'asc'
                    ? numberA - numberB
                    : numberB - numberA;

            }


            /*
             * Empty values go last.
             */

            if (
                valueA === '' &&
                valueB !== ''
            ) {
                return 1;
            }


            if (
                valueB === '' &&
                valueA !== ''
            ) {
                return -1;
            }


            /*
             * Last Login
             *
             * "Never" goes last.
             */

            if (columnIndex === 5) {

                if (
                    valueA === 'never' &&
                    valueB !== 'never'
                ) {
                    return 1;
                }

                if (
                    valueB === 'never' &&
                    valueA !== 'never'
                ) {
                    return -1;
                }

            }


            /*
             * Registered / Last Login
             */

            if (
                columnIndex === 5 ||
                columnIndex === 6
            ) {

                const dateA =
                    new Date(valueA).getTime();

                const dateB =
                    new Date(valueB).getTime();


                if (
                    !Number.isNaN(dateA) &&
                    !Number.isNaN(dateB)
                ) {

                    return direction === 'asc'
                        ? dateA - dateB
                        : dateB - dateA;

                }

            }


            /*
             * Normal text sorting.
             */

            if (valueA < valueB) {

                return direction === 'asc'
                    ? -1
                    : 1;

            }


            if (valueA > valueB) {

                return direction === 'asc'
                    ? 1
                    : -1;

            }


            return 0;

        });


        rows.forEach(function (row) {

            tbody.appendChild(row);

        });

    }


    /*
     * ============================================================
     * TOOLTIP INITIALIZATION
     * ============================================================
     */

    if (
        typeof bootstrap !== 'undefined' &&
        bootstrap.Tooltip
    ) {

        document
            .querySelectorAll(
                '[data-bs-toggle="tooltip"]'
            )
            .forEach(
                function (element) {

                    new bootstrap.Tooltip(element);

                }
            );

    }


    /*
     * ============================================================
     * DELETE CONFIRMATIONS
     * ============================================================
     */

    const deleteForm =
        document.getElementById('delete-form');

    const deleteUnverifiedForm =
        document.getElementById(
            'delete-unverified-form'
        );


    if (deleteForm) {

        deleteForm.addEventListener(
            'submit',
            function (event) {

                if (
                    !window.confirm(
                        'Are you sure you want to permanently delete the selected users?'
                    )
                ) {

                    event.preventDefault();

                }

            }
        );

    }


    if (deleteUnverifiedForm) {

        deleteUnverifiedForm.addEventListener(
            'submit',
            function (event) {

                if (
                    !window.confirm(
                        'Delete the selected unverified users permanently?'
                    )
                ) {

                    event.preventDefault();

                }

            }
        );

    }


    /*
     * ============================================================
     * ACTIVITY SPARKLINES
     * ============================================================
     */

    document
        .querySelectorAll('.activity-sparkline')
        .forEach(
            function (element) {

                const activity =
                    element.dataset.activity || '';

                /*
                 * Don't destroy existing content if
                 * there is no activity data.
                 */

                if (!activity) {
                    return;
                }


                element.innerHTML = '';


                activity
                    .split(',')
                    .forEach(
                        function (value) {

                            const bar =
                                document.createElement('span');

                            bar.classList.add(
                                'activity-bar'
                            );


                            if (
                                Number(value) > 0
                            ) {

                                bar.classList.add(
                                    'active'
                                );

                            }


                            element.appendChild(bar);

                        }
                    );

            }
        );


    /*
     * ============================================================
     * DEBUG MESSAGE
     * ============================================================
     */

    console.log(
        'Admin users JavaScript initialized.'
    );
}


/*
 * ================================================================
 * INITIAL PAGE LOAD
 * ================================================================
 */

if (
    document.readyState === 'loading'
) {

    document.addEventListener(
        'DOMContentLoaded',
        initializeAdminUsers,
        { once: true }
    );

} else {

    initializeAdminUsers();

}


/*
 * ================================================================
 * TURBO / CLIENT-SIDE NAVIGATION
 * ================================================================
 *
 * If Symfony/Turbo performs a client-side navigation,
 * DOMContentLoaded may not fire again.
 *
 * turbo:load handles that situation.
 */

document.addEventListener(
    'turbo:load',
    initializeAdminUsers
);


/*
 * Also support pages/navigation that use
 * Symfony's page lifecycle events.
 */

document.addEventListener(
    'turbo:render',
    initializeAdminUsers
);
