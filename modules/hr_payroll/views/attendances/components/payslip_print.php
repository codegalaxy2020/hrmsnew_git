<?php

// Employee ID | Version | Date Range    |  CR ID         | Propose
// 100008      | initial | 22.01.24      |  08-220124     | OD26 

?>


<!DOCTYPE html>
<html lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            margin: 80px 0px;
            font-family: 'Poppins', sans-serif;
        }

        #header {
            position: fixed;
            left: 0px;
            top: -80px;
            right: 0px;
            height: 50px;
            background-color: #a5cd3a;
            text-align: left;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: -80px;
            right: 0px;
            height: 50px;

        }

        #footer .page:after {
            content: counter(page, decimal);


        }

        .b-font {
            /*           font-family: 'Kalpurush', sans-serif; !important;*/
            font-family: 'Poppins', sans-serif;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;500;600;800&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        #footer .page {
            text-align: right;
            padding: 10px;
            font-size: 10px;
        }

        #content {
            padding: 10px 20px;
            font-family: 'Poppins', sans-serif;
        }

        .bigfont {
            font-size: 24px;
            font-weight: bold;
        }

        .bigfont2 {
            font-size: 18px;
            font-weight: bold;
        }

        .smallFont {
            font-size: 12px;
            font-style: italic;
        }

        p {
            margin: 0 0 2px 0;
        }

        .sub-logo {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px;
        }

        .mt-5 {
            margin-top: 8px;
        }

        .bt-1 {
            border-top: 1px solid #ccc;
        }

        .bb-1 {
            border-bottom: 1px solid #ccc;
        }

        .tble-font {
            font-size: 12px;
            font-weight: normal;
        }

        .namepanel {
            color: #006266;
        }

        .text-center {
            text-align: center;
        }

        .pb-4 {
            padding-bottom: 4px;
        }

        .text-small {
            font-size: 10px !important;
        }
    </style>
    <title>Deep Basak November, 2024 Payslip</title>
</head>

<body>
    <!----------------------------------- Page Header Start Here  --------------------------------------->
    <div id="header">
        <img src="" width="180">
    </div>
    <!----------------------------------- Page Header End Here  --------------------------------------->
    <!----------------------------------- Page Footer Start Here  --------------------------------------->
    <div id="footer">
        <p class="page">Page </p>
    </div>
    <!----------------------------------- Page Footer End Here  --------------------------------------->

    <!----------------------------------- Dynamic Content Section Start Here  --------------------------------------->
    <div id="content">

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr align="center">
                    <td>
                        <img src="https://www.codegalaxy.co.in/uploads/2437f-logosmall.png" width="100">
                    </td>
                </tr>
                <tr align="center">
                    <td>
                        <span class="smallFont">10/11 Bangal Ambuja, City Center - Durgapur - 713216</span>
                    </td>
                </tr>
                <tr align="center">
                    <td>
                        <strong>Payslip for the period of <?= $payslip_details->month_text ?> - <?= $payslip_details->year ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>
        <!----------------------------------------------------------------------------------------->
        <table class="mt-5 bt-1 tble-font" border="0">
            <thead>
                <tr class="bordertopbottom text-left">
                    <th class="text-left">
                        <span class="namepanel">
                            Employee. :
                        </span>
                        <?= $payslip_details->firstname . ' ' . $payslip_details->lastname ?> (<?= $payslip_details->staff_identifi ?>)
                    </th>
                    <th class="text-right">
                        <span class="namepanel">
                            Payment Date : <?= date('F d, Y', strtotime($payslip_details->created_at)) ?>
                        </span>
                    </th>
                </tr>
                <tr class="bordertopbottom text-left">
                    <th class="text-left"><span class="namepanel">Department. :</span>
                        HR
                    </th>
                    <th class="text-right"><span class="namepanel">Designation. :</span>
                        Seniour Software Engineer
                    </th>
                </tr>

                <tr class="bordertopbottom text-right">
                    <th class="text-left"><span class="namepanel">Days of Worked:</span> <?= $payslip_details->days_working ?> Days</th>
                    <th class="text-right1">
                        <span class="namepanel">Mode of Payment:</span>
                        Bank Transfer
                    </th>
                </tr>
            </thead>
        </table>

        <table class="mt-5 bt-1 bb-1 tble-font table table-bordered table-sm" border="0">
            <tbody>
                <tr>
                    <th width="25%">Earnings</th>
                    <th width="25%">Amount (Rs.)</th>
                    <th width="25%">Deduction</th>
                    <th width="25%">Amount (Rs.)</th>
                </tr>
                <tr>
                    <td>Basic Salary</td>
                    <td><?= $payslip_details->basic_salary ?></td>
                    <td>Professional Tax</td>
                    <td><?= $payslip_details->p_tax ?></td>
                </tr>
                <tr>
                    <td>Monthly Alowance</td>
                    <td><?= $payslip_details->allowance ?></td>
                    <td>Provedent Fund</td>
                    <td><?= $payslip_details->pf ?></td>
                </tr>
                <tr>
                    <td>Dearness Alowance</td>
                    <td><?= $payslip_details->da ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>House Rent Alowance</td>
                    <td><?= $payslip_details->hra ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php if($payslip_details->employee_exp != 0): ?>
                <tr>
                    <td>Employee Expence</td>
                    <td><?= $payslip_details->employee_exp ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>Gross Salary</th>
                    <td><?= $payslip_details->gross_salary ?></td>
                    <th>Total Deduction</th>
                    <td><?= $payslip_details->pf + $payslip_details->p_tax ?></td>
                </tr>
                <tr>
                    <th>Net Salary</th>
                    <td><?= $payslip_details->net_salary + $payslip_details->employee_exp ?></td>
                </tr>
            </tbody>
        </table>


        <table class="">
            <tbody>
                <tr align="center">
                    <td width="50%"><img src="<?= base_url('assets/images/company_stamp.png') ?>"></img></td>
                    <td width="50%"><img src=""></img></td>
                </tr>
                <tr align="center">
                    <td>
                        _______________________<br>
                        Employer's Signature
                    </td>
                    <td>
                        _______________________<br>
                        Employee's Signature
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="text-muted"><strong>Note:</strong> This is System generated document, don't need any signature</p>

    </div>
    <!----------------------------------- Dynamic Content Section End Here  --------------------------------------->
</body>

</html>