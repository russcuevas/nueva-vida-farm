$(document).ready(function () {
    var dataTable = $('#example').DataTable({
        "paging": true,
        "pagingType": "full_numbers",
        "searching": true,
        "pageLength": 10,
        "language": {
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "columnDefs": [
            { "targets": [4], "orderable": false }
        ]
    });

    $('#filterMonth').val('');
    $('#filterYear').val('');

    $('#filterMonth, #filterYear').on('change', function () {
        applyFilters();
    });


    function applyFilters() {
        var filterMonth = $('#filterMonth').val();
        var filterYear = $('#filterYear').val();

        var dateFilter = "";

        if (filterMonth !== "" && filterYear !== "") {
            dateFilter = filterMonth + "/01/" + filterYear;
        } else if (filterMonth !== "" && filterYear === "") {
            dateFilter = filterMonth + "/01/2000";
        } else if (filterYear !== "" && filterMonth === "") {
            dateFilter = "01/01/" + filterYear;
        }

        var selectedDate = dateFilter ? new Date(dateFilter) : null;
        var selectedMonth = selectedDate ? selectedDate.toLocaleString('default', { month: 'long' }) : '';
        var selectedYear = selectedDate ? selectedDate.getFullYear() : '';

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            var orderDate = data[4];

            if ((filterMonth === "" || orderDate.includes(selectedMonth)) &&
                (filterYear === "" || orderDate.includes(selectedYear))) {
                return true;
            }
            return false;
        });

        dataTable.draw();

        $.fn.dataTable.ext.search.pop();
    }


    $('#export-pdf').on('click', function () {
        var selectedMonth = $('#filterMonth').val();
        var selectedYear = $('#filterYear').val();
        downloadPDF(selectedMonth, selectedYear);
    });

    $('#export-excel').on('click', function () {
        var selectedMonth = $('#filterMonth').val();
        var selectedYear = $('#filterYear').val();
        exportExcel(selectedMonth, selectedYear);
    });

    function downloadPDF(selectedMonth, selectedYear) {
        var table = $('#example').DataTable();
        var allData = table.rows().data().toArray();

        var filteredData = allData.filter(function (rowData) {
            var orderDate = rowData[4];
            var dateParts = orderDate.split('/');
            var dataYear = parseInt(dateParts[2], 10);
            var dataMonth = dateParts[0];

            if (selectedMonth === "" && selectedYear !== "") {
                return dataYear === parseInt(selectedYear, 10);
            } else if (selectedMonth !== "" && selectedYear === "") {
                return dataMonth === selectedMonth;
            } else if (selectedMonth !== "" && selectedYear !== "") {
                return dataYear === parseInt(selectedYear, 10) && dataMonth === selectedMonth;
            } else {
                return true;
            }
        });

        if (filteredData.length === 0) {
            Swal.fire({
                icon: "error",
                title: "No data found",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }

        var totalSales = calculateTotalSales(filteredData);

        var pdf = new window.jsPDF('p', 'pt', 'letter');

        var startX = 50;
        var startY = 50;

        var tableData = [];

        filteredData.forEach(function (rowData) {
            var formattedRow = [];
            rowData.forEach(function (columnData) {
                var cellData = columnData.replace(/<br\s*\/?>/gi, '\n').trim();
                var lines = cellData.split('\n');
                var concatenatedData = lines.map(function (line) {
                    return line.trim();
                }).join(' ');
                formattedRow.push(concatenatedData);
            });
            tableData.push(formattedRow);
        });

        pdf.addImage('../assets/images/dashboard/logo.png', 'PNG', 250, 20, 100, 70);
        pdf.setFontSize(14);
        pdf.setFont(undefined, 'bold');
        pdf.text('Nueva Vida Farms', startX, startY + 50);
        pdf.setFontSize(12);
        pdf.text('Contact Number: 123-456-7890', startX, startY + 70);
        pdf.text('Email: nuevavidafarmsinc@gmail.com', startX, startY + 90);
        pdf.text('Address: Galamay Amo, San Jose, Batangas', startX, startY + 110);

        var pageWidth = pdf.internal.pageSize.width;
        var totalPriceX = pageWidth - startX;
        pdf.text('Total Sales : ' + totalSales.toFixed(2), totalPriceX, startY + 50, { align: 'right' });

        function drawTable() {
            pdf.setFontSize(12);
            pdf.setFont(undefined, 'normal');
            pdf.setTextColor(0, 0, 0);

            var headerRow = [
                'Reference No.',
                'Payment Method',
                'Customer Name',
                'Total Product',
                'Date Completed',
                'Total Amount',
                'Status'
            ];
            tableData.unshift(headerRow);

            pdf.autoTable({
                startY: startY + 150,
                body: tableData,
            });

            var pageCount = pdf.internal.getNumberOfPages();
            for (var i = 1; i <= pageCount; i++) {
                pdf.setPage(i);
                pdf.text('Page ' + i + ' of ' + pageCount, startX, pdf.internal.pageSize.height - 10);
            }
        }

        drawTable();

        Swal.fire({
            icon: "success",
            title: "Export pdf successfully",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
        }).then(function () {
            pdf.save('sales.pdf');
        });
    }

    function calculateTotalSales(data) {
        var total = 0;
        var totalAmountIndex = 5;

        for (var i = 0; i < data.length; i++) {
            var rowData = data[i];
            var totalAmount = parseFloat(rowData[totalAmountIndex]);
            if (!isNaN(totalAmount)) {
                total += totalAmount;
            }
        }

        return total;
    }

    function exportExcel(selectedMonth, selectedYear) {
        var table = $('#example').DataTable();
        var allData = table.rows().data().toArray();

        var filteredData = allData.filter(function (rowData) {
            var orderDate = rowData[4];
            var dateParts = orderDate.split('/');
            var dataYear = parseInt(dateParts[2], 10);
            var dataMonth = dateParts[0];

            if (
                (selectedMonth === "" && selectedYear !== "" && dataYear === parseInt(selectedYear, 10)) ||
                (selectedMonth !== "" && selectedYear === "" && dataMonth === selectedMonth) ||
                (selectedMonth !== "" && selectedYear !== "" && dataYear === parseInt(selectedYear, 10) && dataMonth === selectedMonth) ||
                (selectedMonth === "" && selectedYear === "")
            ) {
                return true;
            } else {
                return false;
            }
        });

        if (filteredData.length === 0) {
            Swal.fire({
                icon: "error",
                title: "No data found",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }

        var monthsWithData = allData.reduce((acc, rowData) => {
            var orderDate = rowData[4];
            var dateParts = orderDate.split('/');
            var dataYear = parseInt(dateParts[2], 10);
            var dataMonth = dateParts[0];

            if (!acc[dataMonth]) {
                acc[dataMonth] = new Set();
            }
            acc[dataMonth].add(dataYear);

            return acc;
        }, {});

        if (selectedMonth !== "" && selectedYear === "") {
            var selectedMonthHasDataForAllYears = [...monthsWithData[selectedMonth]].length === new Set(allData.map(row => row[4].split('/')[2])).size;
            if (!selectedMonthHasDataForAllYears) {
                Swal.fire({
                    icon: "error",
                    title: "No data found",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                });
                return;
            }
        }

        var totalSales = calculateTotalSales(filteredData);
        var monthlyTotals = calculateMonthlyTotals(filteredData);

        var excelData = [];

        filteredData.forEach(function (rowData) {
            var excelRow = [];
            rowData.forEach(function (columnData) {
                var cellData = columnData.replace(/<br\s*\/?>/gi, ' ').trim();
                excelRow.push(cellData);
            });
            excelData.push(excelRow);
        });

        if (selectedMonth !== "" || selectedYear !== "") {
            excelData.push(['', '', '', '', 'Total Sales for ' + selectedMonth + '/' + selectedYear, totalSales.toFixed(2), '']);
        }

        if (selectedMonth === "" && selectedYear === "") {
            monthlyTotals.forEach(function (monthlyTotal) {
                excelData.push(['', '', '', '', monthlyTotal.month, monthlyTotal.total.toFixed(2), '']);
            });
        }

        if (excelData.length === 0) {
            Swal.fire({
                icon: "error",
                title: "No data found",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }

        var workbook = XLSX.utils.book_new();
        var worksheet = XLSX.utils.aoa_to_sheet(excelData);

        XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
        Swal.fire({
            icon: "success",
            title: "Export excel successfully",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
        }).then(function () {
            XLSX.writeFile(workbook, 'sales.xlsx');
        });
    }




    function calculateMonthlyTotals(data) {
        var monthlyTotals = {};
        var totalAmountIndex = 5;

        data.forEach(function (rowData) {
            var orderDate = rowData[4];
            var dateParts = orderDate.split('/');
            var dataYear = parseInt(dateParts[2], 10);
            var dataMonth = dateParts[0];

            var key = dataYear + '-' + dataMonth;
            var totalAmount = parseFloat(rowData[totalAmountIndex]);

            if (!isNaN(totalAmount)) {
                if (!monthlyTotals[key]) {
                    monthlyTotals[key] = { month: dataMonth + '/' + dataYear, total: 0 };
                }

                monthlyTotals[key].total += totalAmount;
            }
        });

        return Object.values(monthlyTotals);
    }

});
