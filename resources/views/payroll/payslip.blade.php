<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>{{ $payroll->payroll_number }} Payslip</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f8fafc;
            padding: 30px;
        }

        .sheet {
            max-width: 900px;
            margin: auto;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .sheet {
                max-width: none !important;
            }

            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 0 !important;
            }
        }
    </style>
</head>

<body>

<div class="sheet">

    <div class="text-end mb-3 no-print">
        <button
            type="button"
            onclick="window.print()"
            class="btn btn-primary"
        >
            Print / Save as PDF
        </button>
    </div>

    @include('payroll._payslip-card', [
        'payroll' => $payroll
    ])

</div>

</body>
</html>