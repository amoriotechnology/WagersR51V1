<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon"> <i class="pe-7s-note2"></i> </div>
        <div class="header-title">
            <h1>Timesheet</h1>
            <small><?php echo $title ?></small>
            <ol class="breadcrumb">
                <li><a href="#"><i class="pe-7s-home"></i> <?php echo display('home') ?></a></li>
                <li><a href="#">HRM</a></li>
                <li class="active" style="color:orange">Timesheet</li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-sm-12">                
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading" style="height: 50px;">
                        <div class="panel-title">
                            <a style="float:right;color:white;" href="<?php echo base_url('Chrm/manage_timesheet?id=' . $_GET['id'] . '&admin_id=' . $_GET['admin_id']); ?>" class="btnclr btn m-b-5 m-r-2"><i class="ti-align-justify"> </i> <?php echo "Manage TimeSheet" ?> </a>
                        </div>
                    </div>
                <?php echo form_open_multipart('Chrm/pay_slip?id=' . $_GET['id'], 'id="validate"'); ?>
                  <?php  $id=random_int(100000, 999999); ?>
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="panel-body">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="customer" class="col-sm-4 col-form-label">Employee Name<i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input type="hidden" id="tsheet_id" value="<?php echo $id ; ?>" name="tsheet_id" />
                                    <input  type="hidden" readonly id="unique_id" value="<?php echo $this->session->userdata('unique_id') ?>" name="unique_id" />

                                    <input type ="hidden"  id="admin_company_id" value="<?php echo $_GET['id'];  ?>" name="admin_company_id" />
                                    <input type ="hidden" id="adminId" value="<?php echo $_GET['admin_id'];  ?>" name="adminId" />
                                    <select name="templ_name" id="templ_name" class="form-control"  required  tabindex="3" style="width:100;">
                                        <option value=""> <?php echo ('Select Employee Name') ?></option>
                                    <?php foreach($employee_name as $emp_name) { ?>
                                        <option value="<?php  echo $emp_name['id'] ;?>"> <?php echo isset($emp_name['first_name']) ? $emp_name['first_name'] . " " : ""; echo isset($emp_name['middle_name']) ? $emp_name['middle_name'] . " " : ""; echo isset($emp_name['last_name']) ? $emp_name['last_name'] : ""; ?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <label for="qdate" class="col-sm-4 col-form-label">Job title</label>
                                <div class="col-sm-6">
                                    <input type="text" readonly name="job_title" id="job_title" placeholder="Job title" value="" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="dailybreak" class="col-sm-4 col-form-label">Date Range<i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input id="reportrange" name="date_range" type="text" class="form-control"/>
                                    <div id='check_date' style='font-weight:bold;color:red;'></div>
                                </div>
                            </div>
                            
                             <div class="col-sm-6">
                                <label for="dailybreak" class="col-sm-4 col-form-label">Payroll Frequency <i class="text-danger"></i></label>
                                <div class="col-sm-6">
                                    <input type="text" readonly name="payroll_type" value="" class="form-control" id="payroll_type"  placeholder="Payroll Type">
                                </div>
                             </div>
                        </div>

                        <div class="table-responsive work_table col-md-12">
                            <table class=" table table-striped table-bordered" cellspacing="0" width="100%" id="PurList"> 
                                <thead class="btnclr" id='tHead'>
                                </thead>
                                <tbody id="tBody">
                                </tbody>
                                <tfoot id="tFoot">
                                </tfoot>
                            </table>
                        </div>

                        <input type="submit" value="Submit" class="sub_btn btnclr btn text-center"/> 
                        <input type="hidden" id="csrf" data-name="<?= $this->security->get_csrf_token_name();?>" value="<?= $this->security->get_csrf_hash();?>">
                        <input type="hidden" id="week_setting" data-start="<?= (!empty($setting_detail[0]['start_week'])) ? $setting_detail[0]['start_week'] : 'Monday'; ?>" data-end="<?= (!empty($setting_detail[0]['end_week']) ? $setting_detail[0]['end_week'] : 'Friday'); ?>" >
                    </div>               
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>
    </section>

</div>
<br><br>

<script src="<?= base_url('assets/js/timesheet/add_sheet.js'); ?>"></script>