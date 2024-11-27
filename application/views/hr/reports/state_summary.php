<?php error_reporting(1);  ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.base64.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/drag_drop_index_table.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/html2canvas.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/jspdf.plugin.autotable"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/jspdf.umd.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>my-assets/js/invoice_tableManager.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script type="text/javascript" src="<?php echo base_url()?>my-assets/js/tableManager.js"></script>
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script type="text/javascript" src="http://mrrio.github.io/jsPDF/dist/jspdf.debug.js"></script>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
<script src="<?php echo base_url() ?>assets/js/dashboard.js" ></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">
<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel="stylesheet" href="<?php echo base_url() ?>my-assets/css/style.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="http://www.bacubacu.com/colresizable/js/colResizable-1.5.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>my-assets/css/css.css" />
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>my-assets/css/css.css" />
<script type="text/javascript" src="http://www.bacubacu.com/colresizable/js/colResizable-1.5.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>my-assets/css/css.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>my-assets/css/css.css" />
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
 <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" integrity="sha512-CryKbMe7sjSCDPl18jtJI5DR5jtkUWxPXWaLCst6QjH8wxDexfRJic2WRmRXmstr2Y8SxDDWuBO6CQC6IE4KTA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="<?php echo base_url() ?>assets/css/daterangepicker.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">

<div class="content-wrapper">
   <section class="content-header">
      <div class="header-icon">
         <figure class="one">
         <img src="<?php echo base_url()  ?>asset/images/salesreport.png"  class="headshotphoto" style="height:50px;" />
      </div>
      <div class="header-title">
         <div class="logo-holder logo-9">
            <h1><?php echo 'State Overall Summary' ?></h1>
         </div>
         <small><?php //echo display('user_wise_sale_report') ?></small>
         <ol class="breadcrumb"   style=" border: 3px solid #d7d4d6;"   >
            <li><a href="<?php echo base_url()?>"><i class="pe-7s-home"></i> <?php echo display('home') ?></a></li>
            <li><a href="#"><?php echo display('report') ?></a></li>
            <li class="active" style="color:orange"><?php echo 'Federal Overall Summary';?></li>
            <div class="load-wrapp">
               <div class="load-10">
                  <div class="bar"></div>
               </div>
            </div>
         </ol>
      </div>
   </section>
   <script></script>
   <section class="content">
      <div class="row">
         <div class="col-sm-12">
         </div>
      </div>
      <!-- Sales report -->
      <?php  
         $commercial_invoice_number  = array();
         foreach ($sale_datas as $invoice) {
         $commercial_invoice_number [] = $invoice['customer_name'];
         }
         $unique_commercial_invoice_number = array_unique($commercial_invoice_number);
         
         
         $container_no = array();
         foreach ($sale_datas as $invoice) {
         $container_no[] = $invoice['product_name'];
         }
         $unique_container_no = array_unique($container_no);
         
         
         $customer_name = array();
         foreach ($sale_datas as $invoice) {
         $customer_name[] = $invoice['payment_due_date'];
         }
         $unique_customer_name = array_unique($customer_name);
         
         
         $payment_terms = array();
         foreach ($sale_datas as $invoice) {
         $payment_terms[] = $invoice['details'];
         }
         $unique_payment_terms = array_unique($payment_terms);
         ?>
      <?php //echo form_open('Admin_dashboard/user_sales_report', array('class' => 'form-inline', 'method' => 'get')) ?>
      <div class="row">
         <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd lobidrag" style='height:80px; border: 3px solid #d7d4d6;'>
               <div class='col-sm-12'>
                  <form id="fetch_tax">
                     <table class="table" align="center" style="overflow-x: unset;position: relative;">
                        <tr style='text-align:center;font-weight:bold;' class="filters">
                           <!-- <td style='width:200px;'></td> -->
                           <td class="search_dropdown" style="color: black;width:200px;">
                              <input type="hidden" class="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                              <span><?php echo 'Employee'; ?></span>
                              <select id="customer-name-filter" name="employee_name" class="form-control">
                                 <option value="All">All</option>
                                 <?php
                                
                                    foreach ($emp_name as $emp) {
                                      $emp['first_name']=trim($emp['first_name']);
                                      $emp['last_name']=trim($emp['last_name']);
                                    ?>
                                 <option value="<?php echo $emp['first_name']." ".$emp['middle_name']." ".$emp['last_name']; ?>"><?php echo $emp['first_name']." ".$emp['middle_name']." ".$emp['last_name']; ?></option>
                                 <?php } ?>
                              </select>
                           </td>
                         
                            <td class="search_dropdown" style="color: black;width:150px;">
                              <span>Tax Choice </span>
                              <select id="tax_Choice" name='tax_choice' class="tax_choice form-control" >
                               <option value="All">All</option>
                                 <option value="living_state_tax">Living</option>
                                 <option value="state_tax">Working</option>
                              </select>
                            </td>

                           <td class="search_dropdown" style="color: black;width:200px;">
                              <span>State <span class="text-danger">*</span></span>
                              <select id="tax_Choice" name='selectState' class="selectState form-control" >
                                 <option value="">Select Your State</option>
                                 <?php 
                                    foreach ($state_list as $value) {
                                 ?>
                                 <option value="<?php echo $value['state_code']; ?>"><?php echo $value['state']; ?></option>
                                 <?php } ?>
                              </select>
                           </td>

                            <td class="search_dropdown" style="color: black;">
                              <span>Tax Type </span>
                              <select id="tax_Choice" name='taxType' class="taxType form-control" >
                                 <option value="">Select Your Tax Type</option>
                                 <?php foreach ($state_tax_list as $value) { ?>
                                 <option value="<?php echo $value['tax']; ?>"><?php echo $value['tax']; ?></option>
                                 <?php } ?>
                              </select>
                            </td>

                           <td class="search_dropdown" style="color: black; position: relative; top: 4px;">
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
             
             
      <?php //echo form_close() ?>
      <!-- Manage Invoice report -->
      </form>
        <div class="dropdown bootcol" id="drop">
               <button class="btnclr btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="position: relative; left: 185px;">
               <span class="fa fa-download"></span> <?php echo display('download') ?>
               </button>
               <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
               <li><a href="#" onclick="generate()"> <img src="<?php echo base_url() ?>assets/images/pdf.png" width="24px"> <?php echo display('PDF') ?></a></li>
               <li class="divider"></li>
               <li><a href="#" id="download"> <img src="<?php echo base_url() ?>assets/images/xls.png" width="24px"> <?php echo display('XLS') ?></a></li>
               </ul>
               &nbsp;  &nbsp;
               &nbsp;
               <button type="button" class="btnclr btn btn-default dropdown-toggle"  onclick="printDiv('printableArea')" style="margin-top: -54px; margin-left: 304px;float:left; position: relative; top: 54px;"><b class="ti-printer"></b>&nbsp;<?php echo display('print') ?></button>
               </div>
            </div> 
     
         </div>
      </div>
      <div class="row">
         <div class="col-sm-16 col-md-14">
            <div class="panel panel-bd lobidrag" id="printableArea" style="border: 3px solid #d7d4d6;">
               <div class="panel-body">
                  <div class="sortableTable__container">
                     <div class="sortableTable__discard">
                     </div>
                     <div id='printableArea'>
                         <div id='search_details'></div>
                           <div id='period'></div>
                        <!-- <table class="table table-bordered" cellspacing="0" width="100%" id="ProfarmaInvList">
                           <thead class="sortableTable" style="width: 100% !important;" id="ProfarmaInvList_head">
                              
                              
                           </thead>
                           <tbody id="ProfarmaInvList">
                             
                           </tbody>
                           <tfoot  class="btnclr">
                               
                           </tfoot>
                        </table> -->


                <div class="row">
         <div class="col-sm-16 col-md-14">
            <div class="panel panel-bd lobidrag" id="printableArea"   style="border: 3px solid #d7d4d6;" >
               <div class="panel-body">
                   <div class="sortableTable__container">
                    <div class="sortableTable__discard">
                    </div>





                <div id='printableArea'>
<table class="table table-bordered" cellspacing="0" width="100%"   id="ProfarmaInvList">


  <thead></thead>
  <tbody></tbody>
  <tfoot></tfoot>
</table>




</div>
               </div>
   </section>
   </div>
 <input type="hidden" value="Sale/New Sale" id="url"/>
 
 <input type="hidden" value="Sale/New Sale" id="url"/>
   <script src="<?php echo base_url()?>assets/js/jquery.bootgrid.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.0-alpha.1/jspdf.plugin.autotable.js"></script>
   <!--<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>-->
   <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js'></script>
   <script src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.0/knockout-debug.js'></script>
   <!--<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
   <!-- <script  src="<?php //echo base_url() ?>my-assets/js/script.js"></script>  -->
   <!--<script type="text/javascript" src="<?php echo base_url()?>my-assets/js/profarma.js"></script>-->
   <!-- <script  src="<?php //echo base_url() ?>my-assets/js/script.js"></script> -->
   <script src="<?php echo base_url()?>assets/js/jquery.bootgrid.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.0-alpha.1/jspdf.plugin.autotable.js"></script>
   <!--<script type="text/javascript" src="<?php echo base_url()?>my-assets/js/profarma.js"></script>-->
   
    <script src="https://cdn.jsdelivr.net/npm/table2excel@1.0.4/dist/table2excel.min.js"></script>
  
   <!--<script src="<?php echo base_url()?>assets/js/jquery.bootgrid.min.js"></script>-->
  
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.0-alpha.1/jspdf.plugin.autotable.js"></script>-->
   <!--<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>-->
   <!--<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js'></script>-->
   <!--<script src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.0/knockout-debug.js'></script>-->
   <!--<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>-->
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>-->
   <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>-->
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>-->
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>-->
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>-->
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>-->
    <!--<script type="text/javascript" src="<?php echo base_url()?>my-assets/js/profarma.js"></script>-->
   <!-- <script src="<?php echo base_url()?>assets/js/jquery.bootgrid.min.js"></script>-->
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>-->
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.0-alpha.1/jspdf.plugin.autotable.js"></script>-->
   
   <!--<script type="text/javascript" src="<?php echo base_url()?>my-assets/js/profarma.js"></script>-->
   <!-- The Modal Column Switch -->
   <div id="myModal_colSwitch" class="modal_colSwitch" >
   <div class="modal-content_colSwitch" style="width:10%;height:25%;">
   <span class="close_colSwitch">&times;</span>
   <input type="checkbox"  data-control-column="1" checked = "checked" class="1"  value="1"/>Sl.No<br>
   <input type="checkbox"  data-control-column="2" checked = "checked" class="2"  value="2"/>User Name<br>
   <input type="checkbox"  data-control-column="3" checked = "checked" class="3"   value="3"/> Total Invoice<br>
   <input type="checkbox"  data-control-column="4" checked = "checked" class="4"   value="4"/>Total Amount<br>
   </div>
   </div>
   </div>
   </div>
   </div>
</div>
<input type="hidden" id="total_invoice" value="<?php echo $total_invoice;?>" name="">
<input type="hidden" id="currency" value="{currency}" name="">
</div>
</div>
</section>
<input type ="hidden" name="csrf_test_name" id="csrf_test_name" value="<?php echo $this->security->get_csrf_hash();?>">
</div>
<!-- Manage Invoice End -->
<script type="text/javascript" src="<?php echo base_url()?>my-assets/js/profarma.js"></script>
<script>
$(function() {
  // Initialize the daterangepicker with desired date format
  $(".daterangepicker-field").daterangepicker({
    dateFormat: 'mm/dd/yy' // Setting the desired date format
  });
});

var csrfName = '<?php echo $this->security->get_csrf_token_name();?>';
var csrfHash = '<?php echo $this->security->get_csrf_hash();?>';

$(function() {
  var start = moment().startOf('isoWeek'); // Start of the current week
  var end = moment().endOf('isoWeek'); // End of the current week
  var startOfLastWeek = moment().subtract(1, 'week').startOf('week'); // Start of last week
  var endOfLastWeek = moment().subtract(1, 'week').endOf('week').add(1, 'day'); // End of last week (with one extra day)

  // Function to update the date field
  function cb(start, end) {
    $('#daterangepicker-field').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
  }

  // Initialize daterangepicker
  $('#daterangepicker-field').daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'Last 90 Days': [moment().subtract(89, 'days'), moment()],
      'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
      'All Time': [moment().subtract(50, 'years'), moment()], // Example for 'All Time', adjust as needed
      'Custom Range': [moment().subtract(1, 'month'), moment()], // Default custom range (can be updated by user)
      'Last Week Before': [moment().subtract(2, 'week').startOf('week'), moment().subtract(2, 'week').endOf('week')],
      'Last Week': [startOfLastWeek, endOfLastWeek],
      'This Week': [moment().startOf('week'), moment().endOf('week')],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
  }, cb);
});


$(document).ready(function(){
   // $('#printableArea').hide();
   function removeDuplicates() {
    var selectElement = document.getElementById("customer-name-filter");
    var options = selectElement.options;
    var uniqueValues = [];

    // Iterate over each option
    for (var i = 0; i < options.length; i++) {
        var optionValue = options[i].value;
        
        // Check if the value is not already in the unique array
        if (uniqueValues.indexOf(optionValue) === -1) {
            uniqueValues.push(optionValue);
        }
    }

    // Clear the select element
    selectElement.innerHTML = '';

    // Append unique options back to the select element
    uniqueValues.forEach(function(value) {
        var option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        selectElement.appendChild(option);
    });
}

// Call the function to remove duplicates
removeDuplicates(); 
});


$(document).ready(function () {
    $('#fetch_tax').submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "<?php echo base_url('Chrm/state_tax_search_summary'); ?>",
            data: formData,
            success: function (response) {
                console.log(response);
                populateTable(response);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});
function populateTable(response) {
    var ProfarmaInvList = $("#ProfarmaInvList");
    // Clear existing table data
    ProfarmaInvList.find("thead").empty();
    ProfarmaInvList.find("tbody").empty();
    ProfarmaInvList.find("tfoot").empty();
    debugger;
    // Create a map to store all tax types and their corresponding tax types
    var allTaxTypes = {};
    var taxTypeCounts = {};
    var selectedOption = $('.tax_choice').val();

    // Function to iterate over contributions and populate tax types
    function populateTaxTypes(contributions, type) {
        Object.keys(contributions).forEach(function (taxType) {
            contributions[taxType].forEach(function (item) {
                if (type === 'All' || type === taxType) {
                    var key = taxType + '-' + item.tax;
                    allTaxTypes[key] = { tax: item.tax, taxType: taxType, code: item.code || '' };
                    taxTypeCounts[key] = (taxTypeCounts[key] || 0) + 1;
                }
            });
        });
    }

    // Iterate over employer and employee contributions based on selected option
    populateTaxTypes(response.employer_contribution, selectedOption);
    populateTaxTypes(response.employee_contribution, selectedOption);

    // Create tax type map
    var taxTypeMap = {};
    Object.keys(allTaxTypes).forEach(function (key) {
        var taxType = allTaxTypes[key].taxType;
        if (!taxTypeMap[taxType]) {
            taxTypeMap[taxType] = [];
        }
        taxTypeMap[taxType].push(allTaxTypes[key]);
    });

    // Populate headers dynamically
    var taxHeaders = "<tr class='btnclr'><th rowspan='2' style='border-bottom:none;text-align:center'>Employee Name</th>";

    // Iterate over each tax type and its corresponding taxes
    Object.keys(taxTypeMap).forEach(function (taxType) {
        var taxes = taxTypeMap[taxType];
        switch (taxType) {
            case "living_state_tax":
                taxType = "LIVING STATE TAX";
                break;
            case "state_tax":
                taxType = "WORKING STATE TAX";
                break;
        }
        taxHeaders += "<th colspan='" + (2 * taxes.length) + "' style='text-align:center'>" + taxType + "</th>";
    });

    taxHeaders += "</tr><tr class='btnclr'>";

    // Iterate over tax types and their corresponding taxes to create the table headers
    Object.keys(taxTypeMap).forEach(function (taxType) {
        var taxes = taxTypeMap[taxType];
        taxes.forEach(function (item) {
            taxHeaders += "<th colspan='2' style='text-align:center'>" + item.tax + "-" + item.code + "</th>";
        });
    });

    taxHeaders += "</tr><tr class='btnclr'><th></th>";

    // Add "Employee Contribution" and "Employer Contribution" headers for each tax
    Object.keys(taxTypeMap).forEach(function (taxType) {
        var taxes = taxTypeMap[taxType];
        taxes.forEach(function () {
            taxHeaders += "<th style='text-align:center'>Employee Contribution</th><th style='text-align:center'>Employer Contribution</th>";
        });
    });

    taxHeaders += "</tr>";
    ProfarmaInvList.find("thead").append(taxHeaders);

    // Create a map to consolidate contributions for each employee
    var consolidatedContributions = {};
    Object.keys(response.employer_contribution).forEach(function (taxType) {
        response.employer_contribution[taxType].forEach(function (item) {
            var employeeName = item.employee_name;
            var key = taxType + '-' + item.tax;
            if (!consolidatedContributions[employeeName]) {
                consolidatedContributions[employeeName] = {};
            }
            if (!consolidatedContributions[employeeName][key]) {
                consolidatedContributions[employeeName][key] = {
                    employee: "0.000",
                    employer: "0.000"
                };
            }
            consolidatedContributions[employeeName][key].employer = parseFloat(item.total_amount).toFixed(3) || "0.000";
        });
    });

    Object.keys(response.employee_contribution).forEach(function (taxType) {
        response.employee_contribution[taxType].forEach(function (item) {
            var employeeName = item.employee_name;
            var key = taxType + '-' + item.tax;
            if (!consolidatedContributions[employeeName]) {
                consolidatedContributions[employeeName] = {};
            }
            if (!consolidatedContributions[employeeName][key]) {
                consolidatedContributions[employeeName][key] = {
                    employee: "0.000",
                    employer: "0.000"
                };
            }
            consolidatedContributions[employeeName][key].employee = parseFloat(item.total_amount).toFixed(3) || "0.000";
        });
    });

    // Populate data rows
    var tbody = ProfarmaInvList.find("tbody");
    Object.keys(consolidatedContributions).forEach(function (employeeName) {
        var contributions = consolidatedContributions[employeeName];
        var row = $("<tr>");
        row.append("<td>" + employeeName + "</td>");

        // Iterate over tax types and their corresponding taxes to populate the row
        Object.keys(taxTypeMap).forEach(function (taxType) {
            var taxes = taxTypeMap[taxType];
            taxes.forEach(function (item) {
                var key = item.taxType + '-' + item.tax;
                var taxData = contributions[key] || { employee: "0.000", employer: "0.000" };
                row.append("<td>$" + taxData.employee + "</td>");
                row.append("<td>$" + taxData.employer + "</td>");
            });
        });

        tbody.append(row);
    });

    var tfoot = ProfarmaInvList.find("tfoot");
    var footerRow = $("<tr class='btnclr'>");
    footerRow.append("<td>Total</td>");

    // Iterate over tax types
    Object.keys(taxTypeMap).forEach(function (taxType) {
        var taxes = taxTypeMap[taxType];
        taxes.forEach(function (item) {
            var key = item.taxType + '-' + item.tax;
            var totalEmployeeContribution = 0;
            var totalEmployerContribution = 0;
            // Iterate over contributions for this tax
            Object.keys(consolidatedContributions).forEach(function (employeeName) {
                var contribution = consolidatedContributions[employeeName][key];
                if (contribution) {
                    totalEmployeeContribution += parseFloat(contribution.employee);
                    totalEmployerContribution += parseFloat(contribution.employer);
                }
            });
            footerRow.append("<td>$" + totalEmployeeContribution.toFixed(3) + "</td>");
            footerRow.append("<td>$" + totalEmployerContribution.toFixed(3) + "</td>");
        });
    });

    tfoot.append(footerRow);
}




// document.getElementById("btn").addEventListener("click", () => {
//   let table2excel = new Table2Excel();
//   table2excel.export(document.querySelector("#ProfarmaInvList"));
// });


function generateExcel(el) {
    var clon = el.clone();
    var html = clon.wrap('<div>').parent().html();

    //add more symbols if needed...
    while (html.indexOf('á') != -1) html = html.replace(/á/g, '&aacute;');
    while (html.indexOf('é') != -1) html = html.replace(/é/g, '&eacute;');
    while (html.indexOf('í') != -1) html = html.replace(/í/g, '&iacute;');
    while (html.indexOf('ó') != -1) html = html.replace(/ó/g, '&oacute;');
    while (html.indexOf('ú') != -1) html = html.replace(/ú/g, '&uacute;');
    while (html.indexOf('º') != -1) html = html.replace(/º/g, '&ordm;');
    html = html.replace(/<td>/g, "<td>&nbsp;");

    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
}
$("#download").click(function (event) {
 	generateExcel($("#ProfarmaInvList"));
});

    
      </script>

   
</script>
<style>

   #drop{
   float:right
   }
  
</style>