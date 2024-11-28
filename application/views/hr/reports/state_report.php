
<style>
th,td{
    text-align:center;
}
   .select2{display:none;}
   #pagesControllers{
   padding:20px;
   }
   .dropdown-menu{
    left: 229px !important;
   }
   .dropdown{
    position: relative;
    left: 1193px !important;
    bottom: 68px !important;
   }
   .table{
   display: block;
   overflow-x: auto;
}
.select2{display:none;}
   #pagesControllers{
    padding:20px;
}
</style>


<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <figure class="one">
            <img src="<?php echo base_url() ?>assets/images/salesreport.png"  class="headshotphoto" style="height:50px;" />
        </div>
            <div class="header-title">
                <div class="logo-holder logo-9">
                    <h1><?php echo $tax_n; ?></h1>
                </div>
                <small></small>
                <ol class="breadcrumb"   style=" border: 3px solid #d7d4d6;"   >
                    <li><a href="<?php echo base_url() ?>"><i class="pe-7s-home"></i> <?php echo display('home') ?></a></li>
                    <li><a href="#"><?php echo display('report') ?></a></li>
                    <li class="active" style="color:orange"><?php echo 'State Tax'; ?></li>
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
         <div class="col-sm-12">

         </div>
      </div>
      <!-- Sales report -->
     
      <div class="row">
      <div class="col-sm-16 col-md-14">
            <div class="panel panel-bd lobidrag" style='height:80px; border: 3px solid #d7d4d6;'>
                <div class='col-sm-8'>
            <form id="fetch_tax" action="<?php echo base_url(); ?>Chrm/report_state_search/" method="POST">
               <table class="table" align="center" style="overflow-x: unset;position: relative; left: 150px;">
                  <tr style='text-align:center;font-weight:bold;' class="filters">
                     <!-- <td style='width:200px;'></td> -->
                     <input type='hidden' id="url_tax" name='url' value='<?php echo $tax_n; ?>'/>
                     <td class="search_dropdown" style="width: 15%; color: black;">
                      <input type="hidden" class="txt_csrfname" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                        <span><?php echo 'Employee'; ?></span>
                        <select id="customer-name-filter" name="employee_name" class="form-control">
                           <option value="All">All</option>
                           <?php
                                foreach ($employee_data as $emp) {
                                    $emp['first_name'] = trim($emp['first_name']);
                                    $emp['last_name']  = trim($emp['last_name']);
                                    ?>
                                    <option value="<?php echo $emp['first_name'] . " " . $emp['last_name']; ?>"><?php echo $emp['first_name'] . " " . $emp['middle_name'] . " " . $emp['last_name']; ?></option>
                            <?php }?>
                        </select>
                     </td>

                     <td class="search_dropdown" style="width: 15%; color: black; position: relative; top: 4px;">
                        <div id="datepicker-container">
                           <input type="text" class="form-control daterangepicker-field getdate_reults" id="daterangepicker-field" name="daterangepicker-field" style="margin-top: 15px;padding: 5px; width: 200px; border-radius: 8px; height: 35px;" />
                        </div>
                     </td>
                     <input type="hidden" class="getcurrency" value="<?php echo $currency; ?>">
                     <td style='float: left;width:30px; position: relative; top: 4px;'>
                        <input type="submit"  name="btnSave" class="btn btnclr" style='margin-top: 15px;' value='Search'/>
                     </td>
                  </tr>
               </table>

            </div>
                <div class='col-sm-2'>
                  <div class="dropdown bootcol" id="drop" style="width: 300px;">
                     <button class="btnclr btn btn-default dropdown-toggle" type="button"  style="margin-top: 20px;margin-left: 120px;float:left;" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                     <span class="fa fa-download"></span> <?php echo display('download') ?>
                     </button>
                     <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#" onclick="generate()"> <img src="<?php echo base_url() ?>assets/images/pdf.png" width="24px"> <?php echo display('PDF') ?></a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="$('#ProfarmaInvList').tableExport({type:'excel',escape:'false'});"> <img src="<?php echo base_url() ?>assets/images/xls.png" width="24px"> <?php echo display('XLS') ?></a></li>
                     </ul>
                     &nbsp;                     &nbsp;
                     &nbsp;

                     <button type="button"   class="btnclr btn btn-default dropdown-toggle"  onclick="printDiv('printableArea')"><b class="ti-printer"></b>&nbsp;<?php echo display('print') ?></button>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php //echo form_close() ?>
      <!-- Manage Invoice report -->
      </form>



                <div class="row">
         <div class="col-sm-16 col-md-14">
            <div class="panel panel-bd lobidrag" style="border: 3px solid #d7d4d6;" >
               <div class="panel-body">
                   <div class="sortableTable__container">
                    <div class="sortableTable__discard">
                    </div>

                <table class="table table-bordered" cellspacing="0" width="100%" id="statereport_list">
                    <thead class="sortableTable">
                        <tr class="sortableTable__header btnclr">
                            <th rowspan="2" class="1 value" data-col="1" style="height: 45.0114px; text-align:center; ">S.NO</th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;">Employee Name</th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;">Employee Tax</th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;">Working State Tax</th>
                            <th rowspan="2" class="2 value" data-col="2" style="text-align:center; width: 300px;">Living State Tax</th>
                            <th rowspan="2" class="3 value" data-col="3" style="text-align:center;width: 150px; ">TimeSheet ID</th>
                            <th rowspan="2" class="3 value" data-col="3" style="text-align:center;width: 300px; ">Month</th>
                            <th colspan="2" class="3 value" data-col="3" style="text-align:center; width: 300px;">Employee Contribution</th>
                            <th colspan="2" class="3 value" data-col="3" style="text-align:center; width: 300px;">Employer Contribution</th>
                        </tr>
                        <tr class="btnclr" >
                            <th class="3 value" data-col="3" style="text-align:center; width: 300px;"><?php echo 'Working State Tax' ?></th>
                            <th class="3 value" data-col="3" style="text-align:center; width: 300px;"><?php echo 'Living State Tax' ?></th>
                            <th class="3 value" data-col="3" style="text-align:center; width: 300px;"><?php echo 'Working State Tax' ?></th>
                            <th class="3 value" data-col="3" style="text-align:center; width: 300px;"><?php echo 'Living State Tax' ?></th>
                        </tr>
                    </thead>
                <tbody>

                <?php $c = 1;if (!empty($merged_report)) {foreach ($merged_report as $time_sheet_id => $data): ?>
                    <?php foreach ($data['state_tax'] as $state_tax): ?>
                        <tr>
                            <td><?= $c; ?></td>
                            <td><?= $state_tax['first_name'] . ' ' . $state_tax['middle_name'] . " " . $state_tax['last_name']; ?></td>
                            <td><?= $state_tax['employee_tax']; ?></td>
                            <td><?= $state_tax['state_tx']; ?></td>
                            <td><?= $state_tax['living_state_tax']; ?></td>
                            <td><?= $state_tax['time_sheet_id']; ?></td>
                            <td><?= $state_tax['month']; ?></td>
                       <td>
                        <?php
                            $final_amount = '';
                            if ($state_tax['weekly'] > 0) {
                                $final_amount = $state_tax['weekly'];
                            } elseif ($state_tax['biweekly'] > 0) {
                                $final_amount = $state_tax['biweekly'];
                            } elseif ($state_tax['monthly'] > 0) {
                                $final_amount = $state_tax['monthly'];
                            } else {
                                $final_amount = $state_tax['amount'];
                            }

                            echo isset($final_amount) ? number_format($final_amount, 3) : '0.00';?>
                        </td>
                        <td>
                        <?php
                            $living_state_tax_found = false;
                            foreach ($data['living_state_tax'] as $living_state_tax) {
                                if ($living_state_tax['time_sheet_id'] == $time_sheet_id) {
                                    echo isset($living_state_tax['amount']) ? number_format($living_state_tax['amount'], 3) : '0';
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
                            $found_employer_state_tax = $merged_reports_employer[$time_sheet_id]['state_tax'];
                                $found_employer_living_state_tax = $merged_reports_employer[$time_sheet_id]['living_state_tax'];

                                foreach ($found_employer_state_tax as $employer_state_tax): ?>
                                            <td><?php echo isset($employer_state_tax['amount']) ? $employer_state_tax['amount'] : '0'; ?></td>
                                        <?php endforeach;?>

                                        <?php if (empty($found_employer_living_state_tax)): ?>
                                            <td>0</td>
                                        <?php else: ?>
                                            <?php foreach ($found_employer_living_state_tax as $employer_living_state_tax): ?>
                                                <td><?php echo isset($employer_living_state_tax['amount']) ? $employer_living_state_tax['amount'] : '0'; ?></td>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                    </tr>
                            <?php $c++; endforeach;?>
                        <?php endforeach;?>
                        <?php } ?>

                <?php $d = 1;if (!empty($merged_reports_employer) && empty($merged_report)) {foreach ($merged_reports_employer as $time_sheet_id => $data): ?>
                    <?php foreach ($data['state_tax'] as $state_tax): ?>
                        <tr>
                            <td><?php echo $d; ?></td>
                            <td><?php echo $state_tax['first_name'] . ' ' . $state_tax['middle_name'] . ' ' . $state_tax['last_name']; ?></td>
                            <td><?php echo $state_tax['employee_tax']; ?></td>
                            <td><?php echo $state_tax['state_tx']; ?></td>
                            <td><?php echo $state_tax['living_state_tax']; ?></td>
                            <td><?php echo $state_tax['time_sheet_id']; ?></td>
                            <td><?php echo $state_tax['month']; ?></td>
                            <td>
                                <?php
                                    $final_amount = '';

                                    if ($state_tax['weekly'] > 0) {
                                        $final_amount = $state_tax['weekly'];
                                    } elseif ($state_tax['biweekly'] > 0) {
                                        $final_amount = $state_tax['biweekly'];
                                    } elseif ($state_tax['monthly'] > 0) {
                                        $final_amount = $state_tax['monthly'];
                                    } else {
                                        $final_amount = $state_tax['amount'];
                                    }
                                    echo '0';
                                ?>
                            </td>

                            <td>
                            <?php
                                $living_state_tax_found = false;
                                    foreach ($data['living_state_tax'] as $living_state_tax) {
                                        if ($living_state_tax['time_sheet_id'] == $time_sheet_id) {
                                            $living_state_tax_found = true;
                                            break;
                                        }
                                    }
                                    if (!$living_state_tax_found) {
                                        echo '0';
                                    }
                                ?>
                            </td>
                            <?php
                                $found_employer_state_tax = isset($merged_reports_employer[$time_sheet_id]['state_tax']) ? $merged_reports_employer[$time_sheet_id]['state_tax'] : [];
                                $found_employer_living_state_tax = isset($merged_reports_employer[$time_sheet_id]['living_state_tax']) ? $merged_reports_employer[$time_sheet_id]['living_state_tax'] : [];

                    // Display employer state tax data
                    foreach ($found_employer_state_tax as $employer_state_tax): ?>
                                <td><?php echo isset($employer_state_tax['amount']) ? number_format($employer_state_tax['amount'], 3) : '0'; ?></td>
                            <?php endforeach;?>

                            <?php if (empty($found_employer_living_state_tax)): ?>
                                <td>0</td>
                            <?php else: ?>
                                <?php foreach ($found_employer_living_state_tax as $employer_living_state_tax): ?>
                                    <td><?php echo isset($employer_living_state_tax['amount']) ? number_format($employer_living_state_tax['amount'], 3) : '0'; ?></td>
                                <?php endforeach;?>
                            <?php endif;?>
                        </tr>
                    <?php $d++;endforeach;?>
                <?php endforeach;}
                ?>
            </tbody>
            <tfoot>
                <tr class="btnclr">
                    <td colspan="7" style="text-align: end;">Total :</td>
                    <?php
            // Calculate totals for each column
            $totalEmployeeContributionWorking = $totalEmployeeContributionLiving = $totalEmployerContributionWorking = $totalEmployerContributionLiving = 0;
            foreach ($merged_reports as $time_sheet_id => $data) {
                foreach ($data['state_tax'] as $state_tax) {
                    $totalEmployeeContributionWorking += isset($state_tax['amount']) ? $state_tax['amount'] : 0;
                }
                foreach ($data['living_state_tax'] as $living_state_tax) {
                    $totalEmployeeContributionLiving += isset($living_state_tax['amount']) ? $living_state_tax['amount'] : 0;
                }
                $found_employer_state_tax        = $merged_reports_employer[$time_sheet_id]['state_tax'];
                $found_employer_living_state_tax = $merged_reports_employer[$time_sheet_id]['living_state_tax'];
                foreach ($found_employer_state_tax as $employer_state_tax) {
                    $totalEmployerContributionWorking += isset($employer_state_tax['amount']) ? $employer_state_tax['amount'] : 0;
                }
                foreach ($found_employer_living_state_tax as $employer_living_state_tax) {
                    $totalEmployerContributionLiving += isset($employer_living_state_tax['amount']) ? $employer_living_state_tax['amount'] : 0;
                }
            }
            ?>
                    <td><?php echo number_format($totalEmployeeContributionWorking, 3); ?></td>
                    <td><?php echo number_format($totalEmployeeContributionLiving, 3); ?></td>
                    <td><?php echo number_format($totalEmployerContributionWorking, 3); ?></td>
                    <td><?php echo number_format($totalEmployerContributionLiving, 3); ?></td>
                </tr>
            </tfoot>
    </table>

</div>
   </section>
   </div>
   
   </div>
   </div>
</div>
</div>
<input type="hidden" id="total_invoice" value="<?php echo $total_invoice; ?>" name="">
<input type="hidden" id="currency" value="{currency}" name="">
</div>
</div>
</section>
<input type ="hidden" name="csrf_test_name" id="csrf_test_name" value="<?php echo $this->security->get_csrf_hash(); ?>">
</div>


<script>
    // Event listener for form submission
    $(document).on('submit', '#fetch_tax', function(event) {
        event.preventDefault();

        var dataString = $("#fetch_tax").serialize();
        dataString[csrfName] = csrfHash;

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "<?php echo base_url(); ?>Chrm/state_tax_search",
            data: dataString,
            success: function(data) {
            console.log(data);
                processAjaxResponse(data);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function() {

            }
        });
    });
   
$( function() {
      $( ".daterangepicker-field" ).daterangepicker({
        dateFormat: 'mm/dd/yy' // Setting the desired date format
      });
    });
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(function() {
    var start = moment().startOf('isoWeek'); // Start of the current week
    var end = moment().endOf('isoWeek'); // End of the current week
    var startOfLastWeek = moment().subtract(1, 'week').startOf('week');
    var endOfLastWeek = moment().subtract(1, 'week').endOf('week').add(1, 'day');
    // Add one extra day
    function cb(start, end) {
    $('#daterangepicker-field').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }
    $('#daterangepicker-field').daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
       'Last Week Before': [moment().subtract(2,  'week').startOf('week') , moment().subtract(2, 'week').endOf('week')],
       'Last Week': [startOfLastWeek, endOfLastWeek],
       'This Week': [moment().startOf('week'), moment().endOf('week')],
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
    }, cb);
});

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




