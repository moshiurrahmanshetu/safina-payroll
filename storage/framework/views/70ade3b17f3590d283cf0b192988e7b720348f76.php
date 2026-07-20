<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Attendance Report'); ?></title>
    <style>
        @media  print {
            @page  {
                size: A4 portrait;
                margin: 10mm;
            }
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-4 { margin-bottom: 15px; }
        .mt-4 { margin-top: 15px; }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -5px;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 5px;
        }
        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 5px;
        }
        h2 {
            font-size: 18px;
            margin: 10px 0 5px 0;
            text-align: center;
        }
        h3 {
            font-size: 14px;
            margin: 5px 0 10px 0;
            text-align: center;
        }
        h4 {
            font-size: 12px;
            margin: 10px 0 5px 0;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $__env->yieldContent('header'); ?>
        
        <?php echo $__env->yieldContent('content'); ?>
        
        <?php echo $__env->yieldContent('footer'); ?>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/attendance_reports/print/layout.blade.php ENDPATH**/ ?>