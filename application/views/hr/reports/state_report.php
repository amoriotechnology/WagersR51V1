<?php error_reporting(1);?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/toastr.min.css')?>" />
<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/calanderstyle.css">

<script src="<?= base_url('assets/js/toastr.min.js')?>" ></script>
<script src='<?php echo base_url();?>assets/js/moment.min.js'></script>
<script src='<?php echo base_url();?>assets/js/knockout-debug.js'></script>
<script  src="<?php echo base_url() ?>assets/js/scripts.js"></script>

<style>
.table td{
   text-align:center;
}

.table{
   display: block;
   overflow-x: auto;

}
</style>


<div class="content-wrapper">

   <section class="content-header">
      <div class="header-icon">
        <figure class="one">
            <img src="<?= base_url() ?>assets/images/salesreport.png"  class="headshotphoto" style="height:50px;" />
        </figure>
      </div>

        <div class="header-title">
        <div class="logo-holder logo-9">
            <h1><?= $tax_n; ?></h1>
        </div>

        <ol class="breadcrumb" style="height: 60px;border: 3px solid #d7d4d6;" >
            <li><a href="<?= base_url() ?>"><i class="pe-7s-home"></i> <?= display('home') ?></a></li>
            <li><a href="#"><?= display('report') ?></a></li>
            <li class="active" style="color:orange"><?= 'State Tax'; ?></li>

            <div class="load-wrapp">
                <div class="load-10">
                    <div class="bar"></div>
                </div>
            </div>
         </ol>
      </div>
   </section>

    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidrag" style='height:80px; border: 3px solid #d7d4d6;'>
                    <div class='col-sm-8'>
                    
                        <form id="fetch_tax" method="POST">
                        <table class="table" align="center" style="overflow-x: unset;position: relative; left: 150px;">
                        
                            <tr style='text-align:center;font-weight:bold;' class="filters">
                                <input type='hidden' id="url_tax" name='url' value='<?= $tax_n; ?>'/>
                                <input type="hidden" class="txt_csrfname" name="<?= $this->security->get_csrf_token_name();?>" value="<?= $this->security->get_csrf_hash();?>">

                                <td class="search_dropdown" style="width:30%; color: black;">
                                    <span><?= 'Employee'; ?></span>
                                    <select id="customer-name-filter" name="employee_name" class="form-control">
                                        <option value="All">All</option>
                                        <?php
                                            foreach ($employee_data as $emp) {
                                                $emp['first_name'] = trim($emp['first_name']);
                                                $emp['last_name']  = trim($emp['last_name']);
                                        ?>
                                            <option value="<?= $emp['first_name'] . " " . $emp['last_name']; ?>"><?= $emp['first_name'] . " " . $emp['middle_name'] . " " . $emp['last_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>

                                <td class="search_dropdown" style="width: 15%; color: black; position: relative; top: 4px;">
                                    <div class="search">
                                        <span class="fa fa-search"></span>
                                        <input class="daterangepicker_field dateSearch" name="daterangepicker-field" autocomplete="off" id="daterangepicker-field" placeholder="Search...">
                                    </div>
                                    <input type="hidden" value="<?= $_GET['id'] ?>" name="id"/>
                                </td>

                                <input type="hidden" class="getcurrency" value="<?= $currency; ?>">
                                <td style='float: left;width:30px; position: relative; top: 4px;'>
                                    <input type="submit"  name="btnSave" class="btn btnclr" style='margin-top: 15px;' value='Search'/>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    


    <div class="row">
        <div class="col-sm-16 col-md-14">
            <div class="panel panel-bd lobidrag" id="printableArea"   style="border: 3px solid #d7d4d6;" >
               <div class="panel-body">
                   <div class="sortableTable__container">
                 <div id='printableArea'>

                <table class="table table-bordered" cellspacing="0" width="100%" id="StateList">
                    <thead class="sortableTable">
                        <tr class="sortableTable__header btnclr">
                            <th rowspan="2" class="1 value" data-col="1" style="height: 45.0114px; text-align:center; "><?= 'S.NO' ?></th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;"><?= 'Employee Name' ?></th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;"><?= 'Employee Tax' ?></th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;"><?= 'Working State Tax' ?></th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;"><?= 'Living State Tax' ?></th>
                            <th rowspan="2" class="3 value" data-col="3" style="text-align:center;width: 150px; "><?= 'TimeSheet ID' ?></th>
                            <th rowspan="2" class="3 value" data-col="3" style="text-align:center;width: 300px; "><?= 'Month' ?></th>
                            <th colspan="2" class="3 value" data-col="3" style="text-align:center; width: 300px;"><?= 'Employee Contribution' ?></th>
                            <th colspan="2" class="3 value" data-col="3" style="text-align:center; width: 300px;"><?= 'Employer Contribution' ?></th>
                        </tr>
                        <tr class="btnclr" >
                            <th  class="3 value" data-col="3" style="text-align:center; width: 300px;"><?= 'Working State Tax' ?></th>
                            <th  class="3 value" data-col="3" style="text-align:center; width: 300px;"><?= 'Living State Tax' ?></th>
                            <th  class="3 value" data-col="3" style="text-align:center; width: 300px;"><?= 'Working State Tax' ?></th>
                            <th  class="3 value" data-col="3" style="text-align:center; width: 300px;"><?= 'Living State Tax' ?></th>
                        </tr>
                    </thead>
                    
                    <tbody id="tableBody">

                    <?php 
                        $total_employee_working_state = 0;
                        $total_employee_living_state = 0;
                        $total_employer_working_state = 0;
                        $total_employer_living_state = 0; 
                        $c = 1;
                        
                        if (!empty($merged_report)) {
   
                        foreach ($merged_report as $time_sheet_id => $data):
                            foreach ($data['state_tax'] as $state_tax) : ?>
                    <tr>
                        <td><?= $c; ?></td>
                        <td><?= $state_tax['first_name'] . ' ' . $state_tax['middle_name'] . " " . $state_tax['last_name']; ?></td>
                        <td><?= $state_tax['employee_tax']; ?></td>
                        <td><?= $state_tax['working_state_tax']; ?></td>
                        <td><?= $state_tax['living_state_tax']; ?></td>
                        <td><?= $state_tax['time_sheet_id']; ?></td>
                        <td><?= $state_tax['month']; ?></td>
                        
                        <td>
                            <?php
                            $final_amount = $state_tax['amount'];
                            $total_employee_working_state += isset($state_tax['amount']) ? $state_tax['amount'] : 0;
                            echo isset($final_amount) ? number_format($final_amount, 3) : '0.00';?>
                        </td>

                        <td>
                            <?php
                                foreach ($data['living_state_tax'] as $living_state_tax) {
                                    if ($living_state_tax['time_sheet_id'] == $time_sheet_id) {
                                        $total_employee_living_state += isset($living_state_tax['amount']) ? $living_state_tax['amount'] : 0;
                                        echo isset($living_state_tax['amount']) ? number_format($living_state_tax['amount'], 2) : '0';
                                        break;
                                    }
                                }
                                if (empty($living_state_tax_found)) { echo '0';}
                            ?>
                        </td>
                        
                        <td>
                        <?php
                            $found_employer_state_tax        = $merged_reports_employer[$time_sheet_id]['state_tax'];
                            $found_employer_living_state_tax = $merged_reports_employer[$time_sheet_id]['living_state_tax'];

                            if(!empty($found_employer_state_tax)) {
                                foreach ($found_employer_state_tax as $employer_state_tax): 
                                    $total_employer_working_state += isset($employer_state_tax['amount']) ? $employer_state_tax['amount'] : 0; 
                                    echo isset($employer_state_tax['amount']) ? $employer_state_tax['amount'] : '0'; 
                                endforeach;
                            } else {
                                echo 0;
                            }
                            ?>
                        </td>
                            
                        <td> <?php if (empty($found_employer_living_state_tax)): echo 0; else: ?> </td>
                 
                        <?php 
                            foreach ($found_employer_living_state_tax as $employer_living_state_tax): 
                            $total_employer_living_state += isset($employer_living_state_tax['amount']) ? $employer_living_state_tax['amount'] : 0;
                        ?>
                        <td>
                            <?= isset($employer_living_state_tax['amount']) ? $employer_living_state_tax['amount'] : '0';?>
                        </td>
                    <?php endforeach;?>
                <?php endif;?>
            </tr>
        <?php $c++;endforeach;?>
    <?php endforeach;?>
    <?php }?>

    <?php $d = 1;
        if (!empty($merged_reports_employer) && empty($merged_report)) {
            foreach ($merged_reports_employer as $time_sheet_id => $data): ?>
    <?php foreach ($data['state_tax'] as $state_tax): 
          $total_employee_working_state += isset($state_tax['amount']) ? $state_tax['amount'] : 0;
        ?>
        <tr>
            <td><?= $d; ?></td>
            <td><?= $state_tax['first_name'] . ' ' . $state_tax['middle_name'] . ' ' . $state_tax['last_name']; ?></td>
            <td><?= $state_tax['employee_tax']; ?></td>
            <td><?= $state_tax['working_state_tax']; ?></td>
            <td><?= $state_tax['living_state_tax']; ?></td>
            <td><?= $state_tax['time_sheet_id']; ?></td>
            <td><?= $state_tax['month']; ?></td>
            <td> <?= '0'; ?> </td>

            <td>
                <?php
                    $living_state_tax_found = false;
                    foreach ($data['living_state_tax'] as $living_state_tax) {
                        if ($living_state_tax['time_sheet_id'] == $time_sheet_id) {
                            $total_employee_living_state += isset($living_state_tax['amount']) ? $living_state_tax['amount'] : 0;
                            $living_state_tax_found = true;
                            break;
                        }
                    }
                    if (empty($data['living_state_tax'])) {
                        $total_employee_living_state += 0;
                        echo '0';
                    }
                ?>
            </td>

            <!-- Display employer contribution data -->
            <?php

$found_employer_state_tax        = $merged_reports_employer[$time_sheet_id]['state_tax'];
    $found_employer_living_state_tax = $merged_reports_employer[$time_sheet_id]['living_state_tax'];

    foreach ($found_employer_state_tax as $employer_state_tax): ?>
                <td><?php echo isset($employer_state_tax['amount']) ? $employer_state_tax['amount'] : '0'; break; ?></td>

            <?php endforeach;?>

            <?php if (empty($found_employer_living_state_tax)): ?>
                <td>0</td>
            <?php else: ?>

                <?php foreach ($found_employer_living_state_tax as $employer_living_state_tax): ?>
                    <td><?php echo isset($employer_living_state_tax['amount']) ? $employer_living_state_tax['amount'] : '0'; break;?></td>
                <?php endforeach;?>
            <?php endif;?>
        </tr>
    <?php $c++;endforeach;?>
<?php endforeach;?>
<?php }?>
<?php $d = 1;if (!empty($merged_reports_employer) && empty($merged_report)) {foreach ($merged_reports_employer as $time_sheet_id => $data): ?>
    <?php foreach ($data['state_tax'] as $state_tax): ?>
        <tr>
            <td><?php echo $d; ?></td>
            <td><?php echo $state_tax['first_name'] . ' ' . $state_tax['middle_name'] . ' ' . $state_tax['last_name']; ?></td>
            <td><?php echo $state_tax['employee_tax']; ?></td>
            <td><?php echo $state_tax['working_state_tax']; ?></td>
            <td><?php echo $state_tax['living_state_tax']; ?></td>
            <td><?php echo $state_tax['time_sheet_id']; ?></td>
            <td><?php echo $state_tax['month']; ?></td>
            <td>
                <?php
 echo '0';
    ?>
            </td>

            <td>
                <?php
$living_state_tax_found = false;
    foreach ($data['living_state_tax'] as $living_state_tax) {
        if ($living_state_tax['time_sheet_id'] == $time_sheet_id) {
            //echo isset($living_state_tax['amount']) ? number_format($living_state_tax['amount'], 3) : '0';
            $living_state_tax_found = true;
            break;
        }
    }
    if (!$living_state_tax_found) {
        echo '0';
    }
    ?>
            </td>

            <!-- Display employer contribution data -->
            <?php
// Fetch employer state tax and living state tax for the current time_sheet_id
    $found_employer_state_tax        = isset($merged_reports_employer[$time_sheet_id]['state_tax']) ? $merged_reports_employer[$time_sheet_id]['state_tax'] : [];
    $found_employer_living_state_tax = isset($merged_reports_employer[$time_sheet_id]['living_state_tax']) ? $merged_reports_employer[$time_sheet_id]['living_state_tax'] : [];

    // Display employer state tax data
    foreach ($found_employer_state_tax as $employer_state_tax): ?>
                <td><?php echo isset($employer_state_tax['amount']) ? number_format($employer_state_tax['amount'], 3) : '0';         break;?></td>
           
                <?php endforeach;?>

            <?php if (empty($found_employer_living_state_tax)): ?>
                <td>0</td>
            <?php else: ?>
                <?php foreach ($found_employer_living_state_tax as $employer_living_state_tax): ?>
                    <td><?php echo isset($employer_living_state_tax['amount']) ? number_format($employer_living_state_tax['amount'], 3) : '0'; 
                    break;
                    ?></td>
                <?php endforeach;?>
            <?php endif;?>
        </tr>
    <?php $d++;endforeach;?>

<?php endforeach;}
?>
 </tbody>

    <tfoot class="btnclr">
        <tr>
            <td colspan="7" style="text-align:right; font-weight:bold;">Total</td>
            <td style="text-align:center; font-weight:bold;">
                <?= $total_employee_working_state > 0 ? number_format($total_employee_working_state, 3) : '0.00'; ?>
            </td>
            <td style="text-align:center; font-weight:bold;">
                <?= $total_employee_living_state > 0 ? number_format($total_employee_living_state, 3) : '0.00'; ?>
            </td>
            <td style="text-align:center; font-weight:bold;">
                <?= $total_employer_working_state > 0 ? number_format($total_employer_working_state, 3) : '0.00'; ?>
            </td>
        <td style="text-align:center; font-weight:bold;">
                <?= $total_employer_living_state > 0 ? number_format($total_employer_living_state, 3) : '0.00'; ?>
            </td>
        </tr>
    </tfoot>

</table>
</div>
</div>
</section>


<script src='<?= base_url();?>assets/js/moment.min.js'></script>
<script  src="<?= base_url() ?>assets/js/scripts.js"></script> 

<script>
    
var csrfName = '<?= $this->security->get_csrf_token_name();?>';
var csrfHash = '<?= $this->security->get_csrf_hash();?>';
$(document).on('submit', '#fetch_tax', function(event) {
    event.preventDefault();
    var dataString = $("#fetch_tax").serialize();
    dataString[csrfName] = csrfHash;

    $.ajax({
        type: "POST",
        url: "<?= base_url(); ?>Chrm/state_tax_search",
        data: dataString,
        dataType: "json",
        success: function(data) {
            processAjaxResponse(data);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        },

    });
});


$(document).ready(function() {
    
    if ($.fn.DataTable.isDataTable('#StateList')) {
        $('#StateList').DataTable().clear().destroy();
    }

    $('#StateList').DataTable({
        "dom": "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
            "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-6'i><'col-sm-6'p>>",
        "buttons": [{
                "extend": "copy",
                "className": "btn-sm",
                "exportOptions": {
                    "columns": ':not(:eq(8))'
                }
            },
            {
                "extend": "csv",
                "title": "Generated Pay Slips List Report",
                "text": "Excel",
                "className": "btn-sm",
                "exportOptions": {
                    "columns": ':not(:eq(8))'
                }
            },
            {
                "extend": "print",
                "className": "btn-sm",
                "title": '',  
                "exportOptions": {
                    "columns": ':not(:eq(8))'  
                },
                
                "customize": function(win) {
                    $(win.document.body).find('div.dt-buttons').remove();
                    $(win.document.body).css('font-size', '10pt').prepend('<div style="text-align:center;"><h3>State Tax</h3></div>');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    var rows = $(win.document.body).find('table tbody tr');
                    rows.each(function() {
                        if ($(this).find('td').length === 0) {
                            $(this).remove();
                        }
                    });
                    var totalAmount = 0;
                    var totalOvertime = 0;
                    var totalSalesCommission = 0;
                    $(win.document.body).find('table tbody tr').each(function() {
                        var amount = parseFloat($(this).find('td:eq(5)').text()) || 0; 
                        var overtime = parseFloat($(this).find('td:eq(6)').text()) || 0; 
                        var salesCommission = parseFloat($(this).find('td:eq(7)').text()) || 0;
                        totalAmount += amount;
                        totalOvertime += overtime;
                        totalSalesCommission += salesCommission;
                    });
                    $(win.document.body).find('table tbody').append(
                        '<tr>' +
                            '<th colspan="5" style="text-align:right; font-weight: bold;">Total</th>' +
                            '<th style="text-align:center; font-weight: bold;">' + totalAmount.toFixed(2) + '</th>' +
                            '<th style="text-align:center; font-weight: bold;">' + totalOvertime.toFixed(2) + '</th>' +
                            '<th style="text-align:center; font-weight: bold;">' + totalSalesCommission.toFixed(2) + '</th>' +
                        '</tr>'
                    );
                    $(win.document.body).find('div:last-child').css('page-break-after', 'auto');
                }
            },
            {
               "extend": "colvis",
               "className": "btn-sm"
            }
        ],
    });


});

   
function processAjaxResponse(data) {

    var rows = []; 
    var totals = { 
        employeeTax: 0,
        stateTax: 0,
        livingStateTax: 0,
        timeSheetId: 0,
        month: 0,
        employeeAmount: 0,
        employerAmount: 0,
        employerLivingStateTax: 0
    };
    var c=1;
    Object.keys(data).forEach(function(timesheetId) {
        var employeeData = data[timesheetId]?.employee?.state_tax;
        var employerData = data[timesheetId]?.employer?.state_tax;
       
        if (employeeData && employeeData.length > 0) {
           
            if (!employerData || employerData.length === 0) {
                employerData = [{}]; 
            }
            employeeData.forEach(function(employee, index) {
            var employer = employerData[index] || {}; 
            var containsAlphabets = /[a-zA-Z]/.test(employee.living_state_tax);

            totals.employeeTax += parseFloat(employee.employee_tax) || 0;
            totals.stateTax += parseFloat(employee.state_tx) || 0;
            totals.livingStateTax += parseFloat(employee.living_state_tax) || 0;
            totals.timeSheetId += parseFloat(employee.time_sheet_id) || 0;
            totals.month += parseFloat(employee.month) || 0;
            totals.employeeAmount += parseFloat(employee.amount) || 0;
            totals.employerAmount += parseFloat(employer.amount) || 0;
            totals.employerLivingStateTax += parseFloat(employer.living_state_tax) || 0;
            totals.employeeLivingStateTax += parseFloat(employee.living_state_tax) || 0;

            var row = "<tr>";
            row += "<td>" + c + "</td>";
            row += "<td>" + (employee.first_name ? employee.first_name + ' ' + employee.last_name : '') + "</td>";
            row += "<td>" + (employee.employee_tax ? employee.employee_tax : '') + "</td>";
            row += "<td>" + (employee.state_tx ? employee.state_tx : '') + "</td>";
            row += "<td>" + (employee.living_state_tax ? employee.living_state_tax : '0') + "</td>";
            row += "<td>" + (employee.time_sheet_id ? employee.time_sheet_id : '') + "</td>";
            row += "<td>" + (employee.month ? employee.month : '') + "</td>";
            row += "<td>" + (employee.amount ? employee.amount : '0') + "</td>";
            row += "<td>" + (containsAlphabets ? '0' : employee.living_state_tax) + "</td>";
            row += "<td>" + (employer.amount ? employer.amount : '0') + "</td>";
            row += "<td>" + (containsAlphabets ? '0' : employer.living_state_tax) + "</td>";
            row += "</tr>";
            rows.push(row);

        });
    }
    c++;
});

    var rowsHtml = rows.join('');
    $("#tableBody").empty();
    $("#tableBody").append(rowsHtml);
    removeDuplicateRows();
    var footerRow = "<tr class='btnclr'>";
    footerRow += "<td style='text-align:end;' colspan='7'><strong>Total</strong></td>";

    footerRow += "<td>" + totals.employeeAmount.toFixed(2) + "</td>";
    footerRow += "<td>" + (isNaN(totals.employeeLivingStateTax) ? '0.00' : totals.employeeLivingStateTax.toFixed(2)) + "</td>";

    footerRow += "<td>" + totals.employerAmount.toFixed(2) + "</td>";
    footerRow += "<td>" + (isNaN(totals.employerLivingStateTax) ? '0.00' : totals.employerLivingStateTax.toFixed(2)) + "</td>";

    footerRow += "</tr>";
        $("#StateList tfoot").empty();
        $("#StateList tfoot").append(footerRow);
    }
   
    function calculateColumnSum(colIndex) {
        var sum = 0;
        $('#StateList tbody tr').each(function() {
            var cellText = $(this).find('td').eq(colIndex).text();
            var cellValue = parseFloat(cellText);
            if (!isNaN(cellValue)) {
                sum += cellValue;
            }
        });
        return sum;
    }

    function removeDuplicateRows() {
        var seen = {};
        $('#StateList tbody tr').each(function() {
            var txt = $(this).text();
            if (seen[txt])
                $(this).remove();
            else
                seen[txt] = true;
        });
    }

    function removeDuplicateFooter() {
        var footerText = ''; 
        var footerRows = $('#StateList tfoot tr'); 
        footerRows.each(function() {
            var rowText = $(this).text(); 
          
            if (rowText === footerText) {
                $(this).remove();
            } else {
                footerText = rowText; 
            }
        });
    }


// Datatable
$(document).ready(function () {
    $('#statereport_list').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'print', 'colvis'
        ],
        responsive: true,
        paging: true,
        searching: true,
        ordering: true
    });
});

$(document).ready(function () {
    $('#statereport_list').DataTable({
        dom: 'Bfrtip',
        buttons: [ 'copy', 'excel', 'print', 'colvis'],
        responsive: true,
        paging: true,
        searching: true,
        ordering: true
    });
});


</script>




