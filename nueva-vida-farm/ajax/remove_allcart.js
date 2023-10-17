$(document).ready(function () {
    const dataTable = $('#example').DataTable({
        "dom": '<lf<t>ip<l>',
        "ordering": true,
        "info": false,
        "paging": false,
        "bLengthChange": false,
        "pageLength": -1,
        "searching": true,
    });

    dataTable.on('draw', function () {
        $('input:checkbox.product-checkbox').change(function () {
            const itemId = $(this).data('item-id');
            if (this.checked) {
                // 
            } else {
                // 
            }
        });
    });

    $('#select-all').change(function () {
        dataTable.rows().nodes().to$().find('input:checkbox.product-checkbox').prop('checked', this.checked);
    });

    // dataTable.on('page.dt', function () {
    //     $('#select-all').prop('checked', false);
    // });
});

document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("select-all");
    const productCheckboxes = document.querySelectorAll(".product-checkbox");
    const deleteAllButton = document.getElementById("delete-all-button");

    function areAllCheckboxesChecked() {
        return [...productCheckboxes].every(checkbox => checkbox.checked);
    }

    function uncheckSelectAll() {
        selectAllCheckbox.checked = false;
    }

    function removeAllSelectedItems() {
        const selectedProductIds = [];

        productCheckboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedProductIds.push(checkbox.value);
            }
        });

        if (selectedProductIds.length > 0) {
            HoldOn.open({
                theme: "sk-dot",
                message: "Please wait...",
            });

            const formData = new FormData();
            formData.append("selected_products", JSON.stringify(selectedProductIds));

            fetch("functions/remove_allcart.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                })
                .catch(error => {
                    console.error(error);
                })
                .finally(() => {
                    setTimeout(() => {
                        HoldOn.close();
                        location.reload();
                    }, 3000);
                });
        }
    }

    selectAllCheckbox.addEventListener("change", function () {
        const isChecked = this.checked;

        productCheckboxes.forEach(function (checkbox) {
            checkbox.checked = isChecked;
        });

        if (areAllCheckboxesChecked()) {
            deleteAllButton.style.display = "block";
        } else {
            deleteAllButton.style.display = "none";
        }
    });

    productCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            if (areAllCheckboxesChecked()) {
                deleteAllButton.style.display = "block";
            } else {
                deleteAllButton.style.display = "none";
            }
        });
    });

    deleteAllButton.addEventListener("click", function () {
        removeAllSelectedItems();
    });
});
