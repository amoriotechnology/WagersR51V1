<?php
error_reporting(0);
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/dompdf/vendor/autoload.php';
use Dompdf\Dompdf;

class Chrm extends CI_Controller {
    public $menu, $CI;
    function __construct() {
        parent::__construct();
        $this->db->query('SET SESSION sql_mode = ""');
        $this->load->library('auth');
        $this->load->library('session');
        $this->load->model('Web_settings');
        $this->load->model('Hrm_model');
        $this->load->model('invoice_content');
        $this->auth->check_admin_auth();
        $this->CI = &get_instance();
    }
    public function UC_2a_form() {
        $CI = &get_instance();
        $this->load->model("Hrm_model");
        $data = array(
            'title' => 'uc_2a',
        );
        $content = $CI->parser->parse("hr/uc_2aform.php", $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function wr30_form() {
        $CI = &get_instance();
        $this->load->model("Hrm_model");
        $data['get_cominfo']    = $this->Hrm_model->get_company_info();
        $data['info_for_wr']    = $this->Hrm_model->info_for_wrf();
        $data['overall_amount'] = $this->Hrm_model->total_amt_wr30();
        $content                = $CI->parser->parse("hr/wr30_form.php", $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function new_employee() {
        $this->auth->check_admin_auth();
        $this->CI->load->model("Web_settings");
        $this->CI->load->model('invoice_content');
        $company_info = $this->CI->Web_settings->retrieve_companysetting_editdata();
        $setting      = $this->CI->Web_settings->retrieve_setting_editdata();
        $data         = array(
            "company_content" => $this->CI->invoice_content->retrieve_info_data(),
            "logo"            => !empty($setting[0]["invoice_logo"]) ? $setting[0]["invoice_logo"] : $company_info[0]["logo"],
            "id"              => $_GET['id'],
            "admin_id"        => $_GET['admin_id'],
        );
        $content = $this->parser->parse('hr/new_employee_form', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function formnj927($quarter = null) {
        $CI = &get_instance();
        $this->load->model("Hrm_model");
        $data = array(
            'title' => 'NJ927',
        );
        $data['info_for_nj']                       = $this->Hrm_model->info_for_nj($quarter);
        $data['info_info_for_salescommssion_data'] = $this->Hrm_model->info_info_for_salescommssion_data($quarter);
        $data['month']                             = $this->Hrm_model->fetchQuarterlyData($quarter);
        $data['get_cominfo']                       = $this->Hrm_model->get_company_info();
        $data['income_tax']                        = $this->Hrm_model->Quarterone($quarter);
        $data['quarterData']                       = $this->Hrm_model->getQuarterlyMonthData($quarter);
        $content                                   = $CI->parser->parse("hr/formnj927", $data, true);
        $this->template->full_admin_html_view($content);
    }
    // Delete Employee
    public function employee_delete() {
        $this->load->model('Hrm_model');
        $id     = $_GET['id'];
        $emp_id = $_GET['employee'];
        $result = $this->Hrm_model->delete_employee($emp_id);
        if ($result) {
            logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), $id, '', $this->session->userdata('userName'), 'Delete Employee', 'Human Resource', 'Employee has been deleted successfully', 'Delete', date('m-d-Y'));
            $this->session->set_flashdata('message', display('delete_successfully'));
        }
        redirect(base_url("Chrm/manage_employee?id=" . $id . "&admin_id=" . $_GET['admin_id']));
    }
    public function state_summary() {
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $this->load->model('Hrm_model');
        $setting_detail                 = $CI->Web_settings->retrieve_setting_editdata();
        $data['setting_detail']         = $setting_detail;
        $tax_name                       = urldecode($this->input->post('url'));
        $emp_name                       = $this->input->post('employee_name');
        $taxType                        = $this->input->post('taxType');
        $date                           = $this->input->post('daterangepicker-field');
        $data['state_tax_list']         = $CI->Hrm_model->stateTaxlist();
        $data['state_summary_employee'] = $this->Hrm_model->state_summary_employee();
        $data['state_list']             = $this->db->select('*')->from('state_and_tax')->order_by('state', 'ASC')->where('created_by', $this->session->userdata('user_id'))->where('Status', 2)->group_by('id')->get()->result_array();
        $data['state_summary_employer'] = $this->Hrm_model->state_summary_employer();
        $data['emp_name']               = $this->db->select('*')->from('employee_history')->where('create_by', $this->session->userdata('user_id'))->get()->result_array();
        $employee_tax_data              = [];
        foreach ($state_summary_employee as $employee_tax) {
            $employee_tax_data[$employee_tax['time_sheet_id']][$employee_tax['tax_type'] . '_employee'] = $employee_tax['amount'];
        }
        foreach ($state_summary_employer as $employer_tax) {
            $employee_tax_data[$employer_tax['time_sheet_id']][$employer_tax['tax_type'] . '_employer'] = $employer_tax['amount'];
        }
        $data['employee_tax_data'] = $employee_tax_data;
        $content                   = $this->parser->parse('hr/reports/state_summary', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function state_tax_search_summary() {
        $CI = get_instance();
        $CI->load->model('Web_settings');
        $this->load->model('Hrm_model');
        $emp_name               = $this->input->post('employee_name');
        $tax_choice             = $this->input->post('tax_choice');
        $taxType                = $this->input->post('taxType');
        $selectState            = $this->input->post('selectState');
        $date                   = $this->input->post('daterangepicker-field');
        $state_summary_employer = $this->Hrm_model->state_summary_employer($emp_name, $tax_choice, $selectState, $date, $taxType);
        $state_summary_employee = $this->Hrm_model->state_summary_employee($emp_name, $tax_choice, $selectState, $date, $taxType);
        $employer_contributions = [
            'state_tax'        => [],
            'living_state_tax' => [],
        ];
        $employee_contributions = [
            'state_tax'        => [],
            'living_state_tax' => [],
        ];
        foreach ($state_summary_employer as $row) {
            $employee_name = $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'];
            $tax_type      = $row['tax_type'];
            $tax           = $row['tax'];
            $timesheet_id  = $row['timesheet_id'];
            $net_amount    = $row['net'];
            $gross_amount  = $row['gross'];
            $total_amount  = $row['total_amount'];
            if (!empty($gross_amount) && $gross_amount != 0 && !empty($net_amount) && $net_amount != 0) {
                $employer_contributions[$tax_type][] = [
                    'employee_name' => $employee_name,
                    'tax'           => $tax,
                    'net'           => $net_amount,
                    'gross'         => $gross_amount,
                    'taxType'       => $tax_type,
                    'code'          => $row['code'],
                    'total_amount'  => $total_amount,
                    'timesheet_id'  => $timesheet_id,
                ];
            }
        }
        foreach ($state_summary_employee as $row) {
            $employee_name = $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'];
            $tax_type      = $row['tax_type'];
            $tax           = $row['tax'];
            $total_amount  = $row['total_amount'];
            if (!empty($total_amount) && $total_amount != 0) {
                $employee_contributions[$tax_type][] = [
                    'employee_name' => $employee_name,
                    'tax'           => $tax,
                    'code'          => $row['code'],
                    'net'           => 0,
                    'gross'         => 0,
                    'taxType'       => $tax_type,
                    'total_amount'  => $total_amount,
                ];
            }
        }
        foreach ($employer_contributions as $tax_type => &$contributions) {
            foreach ($contributions as &$contribution) {
                $employee_name        = $contribution['employee_name'];
                $tax                  = $contribution['tax'];
                $sum                  = 0;
                $gross_sum            = 0;
                $net_sum              = 0;
                $processed_timesheets = [];
                foreach ($state_summary_employer as $row) {
                    if ($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'] === $employee_name
                        && $row['tax_type'] === $tax_type
                        && $row['tax'] === $tax) {
                        if (!in_array($row['timesheet_id'], $processed_timesheets)) {
                            $final_amount = $row['total_amount'];
                            $gross        = $row['gross'];
                            $net          = $row['net'];
                            if (!empty($final_amount) && $final_amount != 0 &&
                                !empty($gross) && $gross != 0 &&
                                !empty($net) && $net != 0) {
                                $sum += $final_amount;
                                $gross_sum += $gross;
                                $net_sum += $net;
                                $processed_timesheets[] = $row['timesheet_id'];
                            }
                        }
                    }
                }
                $contribution['total_amount'] = $sum;
                $contribution['gross']        = $gross_sum;
                $contribution['net']          = $net_sum;
            }
        }
        foreach ($employee_contributions as $tax_type => &$contributions) {
            foreach ($contributions as &$contribution) {
                $employee_name = $contribution['employee_name'];
                $tax           = $contribution['tax'];
                $sum           = 0;
                foreach ($state_summary_employee as $row) {
                    if ($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'] === $employee_name
                        && $row['tax_type'] === $tax_type
                        && $row['tax'] === $tax) {
                        $final_amount = $row['total_amount'];
                        if (!empty($final_amount) && $final_amount != 0) {
                            $sum += $final_amount;
                        }
                    }
                }
                $contribution['total_amount'] = $sum;
            }
        }
        $responseData = [
            'employer_contribution' => $employee_contributions,
            'employee_contribution' => $employer_contributions,
        ];
        $jsonData = json_encode($responseData, JSON_PRETTY_PRINT);
        echo $jsonData;
    }
    public function social_taxsearch() {
        $emp_name               = trim($this->input->post('employee_name'));
        $date                   = $this->input->post('daterangepicker-field');
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata();
        $data['setting_detail'] = $setting_detail;
        $data['employe']        = $this->Hrm_model->so_tax_report_employee($emp_name, $date, $status);
        $data['employer']       = $this->Hrm_model->so_tax_report_employer($emp_name, $date, $status);
        if ($data['employe']) {
            $aggregated         = [];
            $aggregated_employe = [];
            foreach ($data['employe'] as $row) {
                $key = $row['id'];
                if (!isset($aggregated_employe[$key])) {
                    $aggregated_employe[$key] = [
                        'id'           => $row['id'],
                        'first_name'   => $row['first_name'],
                        'middle_name'  => $row['middle_name'],
                        'last_name'    => $row['last_name'],
                        'employee_tax' => $row['employee_tax'],
                        'gross'        => $row['gross'],
                        'net'          => $row['net'],
                        'fftax'        => 0,
                        'mmtax'        => 0,
                        'sstax'        => 0,
                        'uutax'        => 0,
                    ];
                }
                $aggregated_employe[$key]['fftax'] += $row['fftax'];
                $aggregated_employe[$key]['mmtax'] += $row['mmtax'];
                $aggregated_employe[$key]['sstax'] += $row['sstax'];
                $aggregated_employe[$key]['uutax'] += $row['uutax'];
            }
            $data['aggregated_employe'] = array_values($aggregated_employe);
        } else {
            $data['aggregated_employe'] = [];
        }
        if ($data['employer']) {
            $aggregated = [];
            foreach ($data['employer'] as $row) {
                $key = $row['id'];
                if (!isset($aggregated[$key])) {
                    $aggregated[$key] = [
                        'id'           => $row['id'],
                        'first_name'   => $row['first_name'],
                        'middle_name'  => $row['middle_name'],
                        'last_name'    => $row['last_name'],
                        'employee_tax' => $row['employee_tax'],
                        'fftax'        => 0,
                        'mmtax'        => 0,
                        'sstax'        => 0,
                        'uutax'        => 0,
                    ];
                }
                $aggregated[$key]['fftax'] += $row['fftax'];
                $aggregated[$key]['mmtax'] += $row['mmtax'];
                $aggregated[$key]['sstax'] += $row['sstax'];
                $aggregated[$key]['uutax'] += $row['uutax'];
            }
            $data['aggregated_employer'] = array_values($aggregated);
        } else {
            $data['aggregated_employer'] = [];
        }
        $data['employee_data'] = $this->Hrm_model->employee_data_get();
        echo json_encode($data);
    }
    public function OverallSummary() {
        $data['setting_detail'] = $this->Web_settings->retrieve_setting_editdata();
        $data['emp_name']       = $this->db->select('*')->from('employee_history')->where('create_by', $this->session->userdata('user_id'))->get()->result_array();
        $content                = $this->parser->parse('hr/reports/overall_state_summary', $data, true);
        $this->template->full_admin_html_view($content);
    }
// Old State Income Tax - Madhu
    public function report($tax_name = '') {
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $this->load->model('Hrm_model');
        $tax_name               = urldecode($tax_name);
        $data['employee_data']  = $this->Hrm_model->employee_data_get();
        $setting_detail         = $CI->Web_settings->retrieve_setting_editdata();
        $data['setting_detail'] = $setting_detail;
        $date                   = $this->input->post('daterangepicker-field');
        $employee_name          = $this->input->post('employee_name');
        $data['tax_n']          = $tax_name;
        if (!empty($tax_name)) {
            $data['state_tax_report']        = $this->Hrm_model->statetaxreport($employee_name, $tax_name, $date);
            $data['living_state_tax_report'] = $this->Hrm_model->living_state_tax_report($employee_name, $tax_name, $date);
            $merged_array                    = [];
            foreach ($data['state_tax_report'] as $state_tax) {
                $time_sheet_id                               = $state_tax['time_sheet_id'];
                $merged_array[$time_sheet_id]['state_tax'][] = $state_tax;
            }
            foreach ($data['living_state_tax_report'] as $living_state_tax) {
                $time_sheet_id                                      = $living_state_tax['time_sheet_id'];
                $merged_array[$time_sheet_id]['living_state_tax'][] = $living_state_tax;
            }
            $data['merged_reports']                   = $merged_array;
            $data['employer_state_tax_report']        = $this->Hrm_model->employer_state_tax_report($employee_name, $tax_name, $date);
            $data['employer_living_state_tax_report'] = $this->Hrm_model->employer_living_state_tax_report($employee_name, $tax_name, $date);
            if (empty($data['employer_state_tax_report'])) {
                $data['employer_state_tax_report'] = $data['employer_living_state_tax_report'];
            }
            if (empty($data['employer_living_state_tax_report'])) {
                $data['employer_living_state_tax_report'] = $data['employer_state_tax_report'];
            }
            $merged_array_employer = [];
            foreach ($data['employer_state_tax_report'] as $state_tax) {
                $time_sheet_id                                        = $state_tax['time_sheet_id'];
                $merged_array_employer[$time_sheet_id]['state_tax'][] = $state_tax;
            }
            foreach ($data['employer_living_state_tax_report'] as $living_state_tax) {
                $time_sheet_id                                               = $living_state_tax['time_sheet_id'];
                $merged_array_employer[$time_sheet_id]['living_state_tax'][] = $living_state_tax;
            }
            $data['merged_reports_employer'] = $merged_array_employer;
            $content                         = $this->parser->parse('hr/reports/state_report', $data, true);
            $this->template->full_admin_html_view($content);
        }
    }
// Fetch data in State Income Tax Index - Madhu
    public function stateIncomeReportData() {
        $encodedId                    = isset($_GET["id"]) ? $_GET["id"] : null;
        $decodedId                    = decodeBase64UrlParameter($encodedId);
        $limit                        = $this->input->post("length");
        $start                        = $this->input->post("start");
        $search                       = $this->input->post("search")["value"];
        $orderField                   = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection               = "desc";
        $date                         = $this->input->post("federal_date_search");
        $employee_name                = $this->input->post('employee_name');
        $taxname                      = $this->input->post('taxname');
        $url                          = 'Income tax';
        $stateTaxReport               = $this->Hrm_model->state_tax_report($limit, $start, $orderField, $orderDirection, $search, $taxname, $date, $employee_name, $decodedId);
        $totalItems                   = $this->Hrm_model->getTotalIncomeTax($search, $date, $emp_name, $decodedId, $taxname);
        $livingStateTaxReport         = $this->Hrm_model->living_state_tax_report($employee_name, $taxname, $date);
        $employerStateTaxReport       = $this->Hrm_model->employer_state_tax_report($employee_name, $taxname, $date);
        $employerLivingStateTaxReport = $this->Hrm_model->employer_living_state_tax_report($employee_name, $taxname, $date);
        $mergedArray                  = [];
        foreach ($stateTaxReport as $stateTax) {
            $timeSheetId = $stateTax['time_sheet_id'];
            if (!isset($mergedArray[$timeSheetId])) {
                $mergedArray[$timeSheetId] = [];
            }
            $mergedArray[$timeSheetId]['state_tax'][] = $stateTax;
        }
        foreach ($livingStateTaxReport as $livingStateTax) {
            $timeSheetId = $livingStateTax['time_sheet_id'];
            if (!isset($mergedArray[$timeSheetId])) {
                $mergedArray[$timeSheetId] = [];
            }
            $mergedArray[$timeSheetId]['living_state_tax'][] = $livingStateTax;
        }
        foreach ($employerStateTaxReport as $stateTax) {
            $timeSheetId = $stateTax['time_sheet_id'];
            if (!isset($mergedArray[$timeSheetId])) {
                $mergedArray[$timeSheetId] = [];
            }
            $mergedArray[$timeSheetId]['employer_state_tax'][] = $stateTax;
        }
        foreach ($employerLivingStateTaxReport as $livingStateTax) {
            $timeSheetId = $livingStateTax['time_sheet_id'];
            if (!isset($mergedArray[$timeSheetId])) {
                $mergedArray[$timeSheetId] = [];
            }
            $mergedArray[$timeSheetId]['employer_living_state_tax'][] = $livingStateTax;
        }
        $data         = [];
        $i            = $start + 1;
        $final_amount = '';
        foreach ($mergedArray as $timeSheetId => $report) {
            $stateTax       = $report['state_tax'][0] ?? [];
            $livingStateTax = $report['living_state_tax'][0] ?? [];
            if ($report['weekly'] > 0) {
                $final_amount = $report['weekly'];
            } elseif ($report['biweekly'] > 0) {
                $final_amount = $report['biweekly'];
            } elseif ($report['monthly'] > 0) {
                $final_amount = $report['monthly'];
            } else {
                $final_amount = $report['amount'];
            }
            $found_employer_state_tax         = $report['employer_state_tax'] ?? [];
            $found_employer_living_state_tax  = $report['living_state_tax'] ?? [];
            $employer_state_tax_amount        = 0;
            $employer_living_state_tax_amount = 0;
            foreach ($found_employer_state_tax as $employer_state_tax) {
                $employer_state_tax_amount += isset($employer_state_tax['amount']) ? $employer_state_tax['amount'] : 0;
            }
            foreach ($found_employer_living_state_tax as $employer_living_state_tax) {
                $employer_living_state_tax_amount += isset($employer_living_state_tax['amount']) ? $employer_living_state_tax['amount'] : 0;
            }
            $row = [
                'table_id'         => $i,
                "first_name"       => ($stateTax['first_name'] ?? '') . ' ' . ($stateTax['middle_name'] ?? '') . ' ' . ($stateTax['last_name'] ?? ''),
                "employee_tax"     => $stateTax['employee_tax'] ?? '',
                'state_tx'         => $stateTax['state_tx'] ?? '',
                'living_state_tax' => $stateTax['living_state_tax'] ?? '',
                'time_sheet_id'    => $timeSheetId,
                "month"            => $stateTax['month'] ?? '',
                "cheque_date"      => $stateTax['cheque_date'] ?? '',
                "amount"           => $stateTax['amount'] ?? 0,
                "weekly"           => $livingStateTax['amount'] ?? 0,
                "employer_tax"     => number_format($employer_state_tax_amount ?? 0, 3),
                "employer_weekly"  => ($url === 'Income tax') ? "0.000" : number_format($employer_living_state_tax_amount ?? 0, 3),
            ];
            if (trim($row['first_name']) !== '' && trim($row['employee_tax']) !== '') {
                $data[] = $row;
                $i++;
            }
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
    public function other_tax() {
        $data['employee_data']  = $this->Hrm_model->employee_data_get();
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata();
        $data['setting_detail'] = $setting_detail;
        $employee_other_tax     = $this->Hrm_model->other_tax_report();
        $employer_other_tax     = $this->Hrm_model->other_tax_employer_report();
        $merged_array           = [];
        foreach ($employee_other_tax as $employee_tax) {
            $time_sheet_id                                        = $employee_tax['time_sheet_id'];
            $merged_array[$time_sheet_id]['employee_other_tax'][] = $employee_tax;
        }
        foreach ($employer_other_tax as $employer_tax) {
            $time_sheet_id                                        = $employer_tax['time_sheet_id'];
            $merged_array[$time_sheet_id]['employer_other_tax'][] = $employer_tax;
        }
        $data['merged_reports'] = $merged_array;
        $content                = $this->parser->parse('hr/reports/other_tax', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function other_tax_search() {
        $emp_name               = $this->input->post('employee_name');
        $date                   = $this->input->post('daterangepicker-field');
        $data['employee_data']  = $this->Hrm_model->employee_data_get();
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata();
        $data['setting_detail'] = $setting_detail;
        $employee_other_tax     = $this->Hrm_model->other_tax_report_search($emp_name, $date);
        $employer_other_tax     = $this->Hrm_model->other_tax_employer_report_search($emp_name, $date);
        $merged_array           = [];
        foreach ($employee_other_tax as $employee_tax) {
            $time_sheet_id                                        = $employee_tax['time_sheet_id'];
            $merged_array[$time_sheet_id]['employee_other_tax'][] = $employee_tax;
        }
        foreach ($employer_other_tax as $employer_tax) {
            $time_sheet_id                                        = $employer_tax['time_sheet_id'];
            $merged_array[$time_sheet_id]['employer_other_tax'][] = $employer_tax;
        }
        $data['merged_reports'] = $merged_array;
        echo json_encode($data['merged_reports']);
    }
// old Federal Income tax Index - Madhu
    public function federal_tax_report() {
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata();
        $emp_name               = $this->input->post('employee_name');
        $data['setting_detail'] = $setting_detail;
        $date                   = $this->input->post('daterangepicker-field');
        $split                  = explode(" - ", $date);
        $data['start']          = isset($split[0]) ? $split[0] : null;
        $data['end']            = isset($split[1]) ? $split[1] : null;
        $data['fed_tax']        = $this->Hrm_model->employe($emp_name, $date);
        $timesheetId            = $data['fed_tax'][0]['timesheet_id'];
        $data['fed_tax_emplr']  = $this->Hrm_model->employr($emp_name, $date);
        $data['employee_data']  = $this->Hrm_model->employee_data_get($timesheetId);
        $content                = $this->load->view('hr/reports/fed_income_tax_report', $data, true);
        $this->template->full_admin_html_view($content);
    }
// Fetch data in Income Tax Index - Madhu
    public function federaIndexData() {
        $encodedId      = isset($_GET["id"]) ? $_GET["id"] : null;
        $decodedId      = decodeBase64UrlParameter($encodedId);
        $limit          = $this->input->post("length");
        $start          = $this->input->post("start");
        $search         = $this->input->post("search")["value"];
        $orderField     = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection = "desc";
        $date           = $this->input->post("federal_date_search");
        $emp_name       = $this->input->post('employee_name');
        $items          = $this->Hrm_model->getPaginatedfederalincometax($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name, $decodedId);
        $totalItems     = $this->Hrm_model->getTotalfederalincometax($search, $date, $emp_name, $decodedId);
        $fed_tax_emplr  = $this->Hrm_model->employr($emp_name, $date);
        $data           = [];
        $i              = $start + 1;
        $edit           = "";
        $delete         = "";
        foreach ($items as $item) {
            $s_stax_emplr = isset($fed_tax_emplr[$i]['f_ftax']) ? $fed_tax_emplr[$i]['f_ftax'] : 0;
            $row          = [
                'table_id'     => $i,
                "first_name"   => $item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"],
                "employee_tax" => $item["employee_tax"],
                "timesheet_id" => $item["timesheet"],
                "month"        => $item["month"],
                "cheque_date"  => $item["cheque_date"],
                "f_ftax"       => number_format($item['f_tax'], 2),
            ];
            $data[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
// Old Social Security Tax Index - Madhu
    public function social_tax_report() {
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata();
        $emp_name               = $this->input->post('employee_name');
        $data['setting_detail'] = $setting_detail;
        $date                   = $this->input->post('daterangepicker-field');
        $split                  = explode(" - ", $date);
        $data['start']          = isset($split[0]) ? $split[0] : null;
        $data['end']            = isset($split[1]) ? $split[1] : null;
        $data['fed_tax']        = $this->Hrm_model->employe($emp_name, $date);
        $timesheetId            = $data['fed_tax'][0]['timesheet_id'];
        $data['fed_tax_emplr']  = $this->Hrm_model->employr($emp_name, $date);
        $data['employee_data']  = $this->Hrm_model->employee_data_get($timesheetId);
        $content                = $this->load->view('hr/reports/social_security_tax', $data, true);
        $this->template->full_admin_html_view($content);
    }
// Fetch data in Security Income Tax - Madhu
    public function securitytaxIndexData() {
        $encodedId      = isset($_GET["id"]) ? $_GET["id"] : null;
        $decodedId      = decodeBase64UrlParameter($encodedId);
        $limit          = $this->input->post("length");
        $start          = $this->input->post("start");
        $search         = $this->input->post("search")["value"];
        $orderField     = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection = "desc";
        $date           = $this->input->post("federal_date_search");
        $emp_name       = $this->input->post('employee_name');
        $items          = $this->Hrm_model->getPaginatedfederalincometax($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name, $decodedId);
        $totalItems     = $this->Hrm_model->getTotalfederalincometax($search, $date, $emp_name, $decodedId);
        $fed_tax_emplr  = $this->Hrm_model->employr($emp_name, $date);
        $data           = [];
        $i              = $start + 1;
        $edit           = "";
        $delete         = "";
        $merged_results = [];
        $tax_map        = [];
        foreach ($fed_tax_emplr as $tax_entry) {
            $tax_map[$tax_entry['timesheet']] = $tax_entry;
        }
        foreach ($items as $item) {
            $timesheet_id = $item['timesheet'];
            if (isset($tax_map[$timesheet_id])) {
                $merged_results[] = array_merge($item, $tax_map[$timesheet_id]);
            } else {
                $merged_results[] = $item;
            }
        }
        foreach ($merged_results as $key => $item) {
            $row = [
                'table_id'     => $i,
                "first_name"   => $item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"],
                "employee_tax" => $item["employee_tax"],
                "timesheet_id" => $item["timesheet"],
                "month"        => $item["month"],
                "cheque_date"  => $item["cheque_date"],
                "s_stax"       => number_format($item['s_tax'], 2),
                "ts_stax"      => number_format($item['s_stax'], 2),
            ];
            $data[] = $row;
            $i++;
            $index++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
// Old Medicare Tax - Madhu
    public function medicare_tax_report() {
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata();
        $emp_name               = $this->input->post('employee_name');
        $data['setting_detail'] = $setting_detail;
        $date                   = $this->input->post('daterangepicker-field');
        $split                  = explode(" - ", $date);
        $data['start']          = isset($split[0]) ? $split[0] : null;
        $data['end']            = isset($split[1]) ? $split[1] : null;
        $data['fed_tax']        = $this->Hrm_model->employe($emp_name, $date);
        $timesheetId            = $data['fed_tax'][0]['timesheet_id'];
        $data['fed_tax_emplr']  = $this->Hrm_model->employr($emp_name, $date);
        $data['employee_data']  = $this->Hrm_model->employee_data_get($timesheetId);
        $content                = $this->load->view('hr/reports/medicare_tax', $data, true);
        $this->template->full_admin_html_view($content);
    }
// Fetch data in Medicare Tax - Madhu
    public function medicaretaxIndexData() {
        $encodedId      = isset($_GET["id"]) ? $_GET["id"] : null;
        $decodedId      = decodeBase64UrlParameter($encodedId);
        $limit          = $this->input->post("length");
        $start          = $this->input->post("start");
        $search         = $this->input->post("search")["value"];
        $orderField     = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection = "desc";
        $date           = $this->input->post("federal_date_search");
        $emp_name       = $this->input->post('employee_name');
        $items          = $this->Hrm_model->getPaginatedfederalincometax($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name, $decodedId);
        $totalItems     = $this->Hrm_model->getTotalfederalincometax($search, $date, $emp_name, $decodedId);
        $fed_tax_emplr  = $this->Hrm_model->employr($emp_name, $date);
        $data           = [];
        $i              = $start + 1;
        $edit           = "";
        $delete         = "";
        $merged_results = [];
        $tax_map        = [];
        foreach ($fed_tax_emplr as $tax_entry) {
            $tax_map[$tax_entry['timesheet']] = $tax_entry;
        }
        foreach ($items as $item) {
            $timesheet_id = $item['timesheet'];
            if (isset($tax_map[$timesheet_id])) {
                $merged_results[] = array_merge($item, $tax_map[$timesheet_id]);
            } else {
                $merged_results[] = $item;
            }
        }
        foreach ($merged_results as $key => $item) {
            $row = [
                'table_id'     => $i,
                "first_name"   => $item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"],
                "employee_tax" => $item["employee_tax"],
                "timesheet_id" => $item["timesheet"],
                "month"        => $item["month"],
                "cheque_date"  => $item["cheque_date"],
                "m_mtax"       => number_format($item['m_tax'], 2),
                "tm_mtax"      => number_format($item['m_mtax'], 2),
            ];
            $data[] = $row;
            $i++;
            $index++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
// Old Unemployment Tax - Madhu
    public function unemployment_tax_report() {
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata();
        $emp_name               = $this->input->post('employee_name');
        $data['setting_detail'] = $setting_detail;
        $date                   = $this->input->post('daterangepicker-field');
        $split                  = explode(" - ", $date);
        $data['start']          = isset($split[0]) ? $split[0] : null;
        $data['end']            = isset($split[1]) ? $split[1] : null;
        $data['fed_tax']        = $this->Hrm_model->employe($emp_name, $date);
        $timesheetId            = $data['fed_tax'][0]['timesheet_id'];
        $data['fed_tax_emplr']  = $this->Hrm_model->employr($emp_name, $date);
        $data['employee_data']  = $this->Hrm_model->employee_data_get($timesheetId);
        $content                = $this->load->view('hr/reports/unemployment_tax', $data, true);
        $this->template->full_admin_html_view($content);
    }
// Fetch data in Medicare Tax - Madhu
    public function unemploymenttaxIndexData() {
        $encodedId               = isset($_GET["id"]) ? $_GET["id"] : null;
        $decodedId               = decodeBase64UrlParameter($encodedId);
        $limit                   = $this->input->post("length");
        $start                   = $this->input->post("start");
        $search                  = $this->input->post("search")["value"];
        $orderField              = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection          = "desc";
        $date                    = $this->input->post("federal_date_search");
        $emp_name                = $this->input->post('employee_name');
        $items                   = $this->Hrm_model->getPaginatedfederalincometax($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name, $decodedId);
        $totalItems              = $this->Hrm_model->getTotalfederalincometax($search, $date, $emp_name, $decodedId);
        $fed_tax_emplr           = $this->Hrm_model->employr($emp_name, $date);
        $data                    = [];
        $i                       = $start + 1;
        $edit                    = "";
        $delete                  = "";
        $employerContributionMap = [];
        foreach ($fed_tax_emplr as $fed) {
            $employerContributionMap[$fed['timesheet']] = $fed['u_utax'];
        }
        foreach ($items as $item) {
            $s_stax_emplr         = isset($employerContributionMap[$item['timesheet']]) ? $employerContributionMap[$item['timesheet']] : 0;
            $employeeContribution = isset($item['u_tax']) ? $item['u_tax'] : 0;
            $row                  = [
                'table_id'     => $i,
                "first_name"   => trim($item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"]),
                "employee_tax" => $item["employee_tax"],
                "timesheet_id" => $item["timesheet"],
                "month"        => $item["month"],
                "cheque_date"  => $item["cheque_date"],
                "u_utax"       => number_format($employeeContribution, 2),
                "tu_utax"      => number_format($s_stax_emplr, 2),
            ];
            $data[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
// Federal Overall Summary - Madhu
    public function federal_summary() {
        $setting_detail                 = $this->Web_settings->retrieve_setting_editdata();
        $data['setting_detail']         = $setting_detail;
        $data['fed_tax']                = $this->Hrm_model->social_tax_sumary();
        $data['fed_tax_emplr']          = $this->Hrm_model->social_tax_employer();
        $data['state_tax_list']         = $this->Hrm_model->stateTaxlist();
        $data['state_summary_employee'] = $this->Hrm_model->state_summary_employee();
        $data['state_list']             = $this->db->select('*')->from('state_and_tax')->order_by('state', 'ASC')->where('created_by', $this->session->userdata('user_id'))->where('Status', 2)->group_by('id')->get()->result_array();
        $mergedArray                    = array();
        foreach ($data['fed_tax'] as $item1) {
            $mergedItem = $item1;
            foreach ($data['fed_tax_emplr'] as $item2) {
                if ($item1['employee_id'] == $item2['employee_id']) {
                    foreach ($item2 as $key => $value) {
                        if (!isset($mergedItem[$key])) {
                            $mergedItem[$key] = $value;
                        }
                    }
                    $mergedArray[] = $mergedItem;
                    break;
                }
            }
        }
        $data['mergedArray']   = $mergedArray;
        $data['employee_data'] = $this->Hrm_model->employee_data_get();
        $content               = $this->parser->parse('hr/reports/federal_summary', $data, true);
        $this->template->full_admin_html_view($content);
    }
// Fetch data in Overall Social Tax - Madhu
    public function overallSocialtaxIndexData() {
        $encodedId          = isset($_GET["id"]) ? $_GET["id"] : null;
        $decodedId          = decodeBase64UrlParameter($encodedId);
        $limit              = $this->input->post("length");
        $start              = $this->input->post("start");
        $search             = $this->input->post("search")["value"];
        $orderField         = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection     = "desc";
        $date               = $this->input->post("federal_date_search");
        $emp_name           = $this->input->post('employee_name');
        $items              = $this->Hrm_model->getPaginatedSocialTaxSummary($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name, $decodedId);
        $totalItems         = $this->Hrm_model->getSocialOveralltax($search, $date, $emp_name, $decodedId);
        $fed_tax            = $this->Hrm_model->social_tax_sumary($date, $emp_name);
        $fed_tax_emplr      = $this->Hrm_model->social_tax_employer($date, $emp_name);
        $data['employe']    = $this->Hrm_model->so_tax_report_employee($emp_name, $date);
        $aggregated_employe = [];
        if ($data['employe']) {
            foreach ($data['employe'] as $row) {
                $key = $row['id'];
                if (!isset($aggregated_employe[$key])) {
                    $aggregated_employe[$key] = [
                        'id'           => $row['id'],
                        'first_name'   => $row['first_name'],
                        'middle_name'  => $row['middle_name'],
                        'last_name'    => $row['last_name'],
                        'employee_tax' => $row['employee_tax'],
                        'gross'        => $row['gross'],
                        'net'          => $row['net'],
                        'fftax'        => 0,
                        'mmtax'        => 0,
                        'sstax'        => 0,
                        'uutax'        => 0,
                    ];
                }
                $aggregated_employe[$key]['fftax'] += $row['fftax'];
                $aggregated_employe[$key]['mmtax'] += $row['mmtax'];
                $aggregated_employe[$key]['sstax'] += $row['sstax'];
                $aggregated_employe[$key]['uutax'] += $row['uutax'];
            }
        }
        $mergedArray = [];
        foreach ($fed_tax as $item1) {
            $mergedArray[$item1['employee_id']] = $item1;
        }
        foreach ($fed_tax_emplr as $item2) {
            if (isset($mergedArray[$item2['employee_id']])) {
                foreach ($item2 as $key => $value) {
                    if (!isset($mergedArray[$item2['employee_id']][$key])) {
                        $mergedArray[$item2['employee_id']][$key] = $value;
                    }
                }
            }
        }
        foreach ($mergedArray as $employee_id => &$data) {
            if (isset($aggregated_employe[$employee_id])) {
                $data['gross'] = $aggregated_employe[$employee_id]['gross'];
                $data['net']   = $aggregated_employe[$employee_id]['net'];
            } else {
                echo "No match found for employee ID: $employee_id\n";
            }
        }
        $responseData = [];
        $i            = $start + 1;
        foreach ($items as $item) {
            $employeeId = $item["employee_id"];
            $mergedItem = $mergedArray[$employeeId] ?? [];
            $row        = [
                'table_id'                => $i,
                "first_name"              => $item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"],
                "employee_tax"            => $item["employee_tax"],
                'gross'                   => number_format($mergedItem['gross'] ?? 0, 2),
                'net'                     => number_format($mergedItem['net'] ?? 0, 2),
                'f_employee'              => number_format($mergedItem['f_ftax_sum'] ?? 0, 2),
                'f_employer'              => number_format($mergedItem['f_ftax_sum_er'] ?? 0, 2),
                'socialsecurity_employee' => number_format($mergedItem['s_stax_sum'] ?? 0, 2),
                'socialsecurity_employer' => number_format($mergedItem['s_stax_sum_er'] ?? 0, 2),
                'medicare_employee'       => number_format($mergedItem['m_mtax_sum'] ?? 0, 2),
                'medicare_employer'       => number_format($mergedItem['m_mtax_sum_er'] ?? 0, 2),
                'unemployment_employee'   => number_format($mergedItem['u_utax_sum'] ?? 0, 2),
                'unemployment_employer'   => number_format($mergedItem['u_utax_sum_er'] ?? 0, 2),
            ];
            $responseData[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $responseData,
        ];
        echo json_encode($response);
    }
    public function city_tax_report() {
        $setting_detail                   = $this->Web_settings->retrieve_setting_editdata();
        $data['setting_detail']           = $setting_detail;
        $data['getEmployeeContributions'] = $this->Hrm_model->getEmployeeContributions();
        $data['employee_data']            = $this->Hrm_model->employee_data_get();
        $content                          = $this->parser->parse('hr/reports/city_tax', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function city_tax_search() {
        $setting_detail                   = $this->Web_settings->retrieve_setting_editdata();
        $date                             = $this->input->post('daterangepicker-field');
        $data['setting_detail']           = $setting_detail;
        $emp_name                         = $this->input->post('employee_name');
        $data['getEmployeeContributions'] = $this->Hrm_model->getEmployeeContributions($emp_name, $date);
        $data['employee_data']            = $this->Hrm_model->employee_data_get();
        echo json_encode($data['getEmployeeContributions']);
    }
    public function city_local_tax() {
        $setting_detail                   = $this->Web_settings->retrieve_setting_editdata();
        $data['setting_detail']           = $setting_detail;
        $data['getEmployeeContributions'] = $this->Hrm_model->getEmployeeContributions_local();
        $data['employee_data']            = $this->Hrm_model->employee_data_get();
        $content                          = $this->parser->parse('hr/reports/city_local_tax', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function city_local_tax_search() {
        $setting_detail                   = $this->Web_settings->retrieve_setting_editdata();
        $data['setting_detail']           = $setting_detail;
        $date                             = $this->input->post('daterangepicker-field');
        $emp_name                         = $this->input->post('employee_name');
        $data['getEmployeeContributions'] = $this->Hrm_model->getEmployeeContributions_local($emp_name, $date);
        $data['employee_data']            = $this->Hrm_model->employee_data_get();
        echo json_encode($data['getEmployeeContributions']);
    }
    public function hr_tools() {
        $this->CI->load->model('Web_settings');
        $data['Web_settings'] = $this->CI->Web_settings->retrieve_setting_editdata();
        $content              = $this->parser->parse('hr/toolkit_index', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function checkTimesheet() {
        $selectedDate    = $this->input->post('selectedDate');
        $employeeId      = $this->input->post('employeeId');
        $timesheetExists = $this->Hrm_model->checkTimesheetInfo($employeeId, $selectedDate);
        if ($timesheetExists) {
            echo 'Timesheet exists for this date and employee';
        } else {
            echo 'No timesheet found for this date and employee';
        }
    }
    public function edit_timesheet() {
        $id                      = $this->input->get('timesheet_id');
        $setting_detail          = $this->Web_settings->retrieve_setting_editdata();
        $data['title']           = display('Payment_Administration');
        $data['time_sheet_data'] = $this->Hrm_model->time_sheet_data($id);
        $data['setting_detail']  = $setting_detail;
        $data['employee_name']   = $this->Hrm_model->employee_name($data['time_sheet_data'][0]['templ_name']);
        $data['payment_terms']   = $this->Hrm_model->get_payment_terms();
        $data['dailybreak']      = $this->Hrm_model->get_dailybreak();
        $data['duration']        = $this->Hrm_model->get_duration_data();
        $data['administrator']   = $this->Hrm_model->administrator_data();
        $content                 = $this->parser->parse('hr/edit_timesheet', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function state_tax($endDate, $employee_id, $employee_tax, $working_state_tax, $user_id, $this_period, $tax_type, $timesheet_id,$payroll, $payroll_frequency) {
       $state_tax            = $this->Hrm_model->get_state_details('state', 'state_and_tax', 'state', $working_state_tax, $user_id);
        $state                = $this->Hrm_model->get_state_details('tax', 'state_and_tax', 'state', $state_tax[0]['state'], $user_id);
        $tax_split            = explode(',', $state[0]['tax']);
        $overall_state_tax    = [];
        $this_period_statetax = [];
        $table = '';
    
            foreach ($tax_split as $tax) {
                if (strpos($tax, 'Income') !== false) {
                    if ($payroll == 'Hourly' || $payroll == 'Fixed') {
                        switch ($payroll_frequency) {
                            case 'Weekly':
                                $table = 'weekly_tax_info';
                                break;
                            case 'Bi-Weekly':
                                $table = 'biweekly_tax_info';
                                break;
                            case 'Monthly':
                                $table = 'monthly_tax_info';
                                break;
                            default:
                                $table = 'state_localtax'; 
                        }
                    }
                } else {
                    $table = 'state_localtax';
                }

            $tax_data = $this->Hrm_model->get_state_details('*', $table, 'tax', $state_tax[0]['state'] . "-" . $tax, $user_id);
         
            foreach ($tax_data as $tx) {
                $split = explode('-', $tx[$employee_tax]);
                if (count($split) > 1 && $split[0] != '' && $split[1] != '') {
                    if ($this_period >= $split[0] && $this_period <= $split[1]) {
                        $range               = $split[0] . "-" . $split[1];

                        $data['working_tax'] = $this->Hrm_model->working_state_tax($tax_data[0]['tax'], $employee_tax, $this_period, $range, $state_tax[0]['state'], $user_id, $payroll, $payroll_frequency);

                       if (!empty($data['working_tax'])) {
                            foreach ($data['working_tax'] as $contribution) {
                                $employee              = $contribution['employee'];
                                $employer              = $contribution['employer'];

                                // Tax Add amount
                                $employeeTax           = $contribution[$employee_tax];
                                $employeeTaxExplode    = explode('-', $employeeTax);
                                $checkFinalAmount      = floatval($this_period - $employeeTaxExplode[0]);

                                $employee_contribution = floatval(($employee / 100) * $checkFinalAmount + $contribution['details']);
                                 
                                $employer_contribution = floatval(($employer / 100) * $this_period);

                                $row                   = $this->db->select('*')->from($table)->where('employee', $employee)->where('tax', $tax_data[0]['tax'])->where($employee_tax, $range)->where('created_by', $user_id)->count_all_results();
                                $employee_tax_key      = "'employee_" . $tax_data[0]['tax'] . "'";
                                $employer_tax_key      = "'employer_" . $tax_data[0]['tax'] . "'";
                                $search_tax            = explode('-', $tax_data[0]['tax']);
                                if ($row == 1) {
                                    $result = $this->Hrm_model->get_tax_history($tax_type, $search_tax[1], $timesheet_id);
                                    
                                    if (empty($result)) {

                                        $f = $this->countryTax('Federal Income tax', $employee_tax, $this_period, $employee_id, 'f_tax', $user_id, $endDate, $timesheet_id);

                                        $s = $this->countryTax('Social Security', $employee_tax, $this_period, $employee_id, 's_tax', $user_id, $endDate, $timesheet_id);

                                        $m = $this->countryTax('Medicare', $employee_tax, $this_period, $employee_id, 'm_tax', $user_id, $endDate, $timesheet_id);

                                        $u = $this->countryTax('Federal unemployment', $employee_tax, $this_period, $employee_id, 'u_tax', $user_id, $endDate, $timesheet_id);

                                        $tax_name = trim(substr($contribution['tax'], strpos($contribution['tax'], '-') + 1, strrpos($contribution['tax'], '-') - strpos($contribution['tax'], '-') - 1));

                                        $code = trim(substr($contribution['tax'], strrpos($contribution['tax'], '-') + 1));

                                        if ($employee_contribution) {
                                            $tax_history_employee = array(
                                                'employee_id'   => $employee_id,
                                                'time_sheet_id' => $timesheet_id,
                                                's_tax'         => $s['tax_value'],
                                                'm_tax'         => $m['tax_value'],
                                                'f_tax'         => $f['tax_value'],
                                                'u_tax'         => $u['tax_value'],
                                                'tax_type'      => $tax_type,
                                                'code'          => $code,
                                                'tax'           => $tax_name,
                                                'amount'        => round($employee_contribution, 3),
                                                'created_by'    => $user_id,
                                            );
                                            $this->db->insert('tax_history', $tax_history_employee);

                                        }
                                        if ($employer_contribution) {
                                            $tax_history_employer = array(
                                                'employee_id'   => $employee_id,
                                                'time_sheet_id' => $timesheet_id,
                                                's_tax'         => $s['tax_value'],
                                                'm_tax'         => $m['tax_value'],
                                                'f_tax'         => $f['tax_value_employer'],
                                                'u_tax'         => $u['tax_value_employer'],
                                                'tax_type'      => $tax_type,
                                                'code'          => $code,
                                                'tax'           => $tax_name,
                                                'amount'        => round($employer_contribution, 3),
                                                'created_by'    => $user_id,
                                            );
                                            $this->db->insert('tax_history_employer', $tax_history_employer);
                                        }
                                        $employeedata  = $this->Hrm_model->employee_info($employee_id, $user_id);
                                        $timesheetdata = $this->Hrm_model->timesheet_info_data($timesheet_id, $user_id);
                                        $this->db->where('timesheet_id', $timesheet_id);
                                        if ($this->db->count_all_results('info_payslip') > 0) {
                                            $this->db->delete('info_payslip', ['timesheet_id' => $timesheet_id]);
                                        }
                                        $info_payslip = array(
                                            's_tax'        => $s['tax_value'],
                                            'm_tax'        => $m['tax_value'],
                                            'f_tax'        => $f['tax_value'],
                                            'u_tax'        => $u['tax_value'],
                                            'tax'          => $tax_name,
                                            'total_amount' => $this_period,
                                            'timesheet_id' => $timesheet_id,
                                            'total_hours'  => $timesheetdata[0]['total_hours'],
                                            'templ_name'   => $timesheetdata[0]['templ_name'],
                                            'employee_tax' => $employeedata[0]['employee_tax'],
                                            'hrate'        => $employeedata[0]['hrate'],
                                            'create_by'    => $user_id,
                                        );
                                        $this->db->insert('info_payslip', $info_payslip);
                                    } else {
                                        $amount           = $result ? $result : 0;
                                        $sum_of_state_tax = $this->Hrm_model->get_cumulative_tax_amount($search_tax[1], $endDate, $employee_id, $tax_type);
                                        $overall_amount   = $sum_of_state_tax ? $sum_of_state_tax : 0;
                                        if ($amount > 0) {
                                            $this_period_statetax[$employee_tax_key] = $amount;
                                        }
                                        if ($overall_amount > 0) {
                                            $overall_state_tax[$employee_tax_key] = $overall_amount;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
        $data = array(
            'this_perid_state_tax' => $this_period_statetax,
            'overall_state_tax'    => $overall_state_tax,
        );
       
        return $data;
    }
    public function time_list() {
        list($user_id, $admin_id) = array_map('decodeBase64UrlParameter', [$_GET['id'], $_GET['admin_id']]);
        $timesheet_id             = $_GET['timesheet_id'];
        $employee_id              = $this->input->get('templ_name');
        $company_info             = $this->Hrm_model->retrieve_companyinformation($user_id);
        $default_setting          = $this->Web_settings->default_company_setting($user_id);
        $employeedata             = $this->Hrm_model->employee_info($employee_id, $user_id);
        $timesheetdata            = $this->Hrm_model->timesheet_info_data($timesheet_id, $user_id);
        $overtime_hour            = $this->Hrm_model->get_overtime_data($user_id);
        $working_state_tax        = $employeedata[0]['working_state_tax'];
        $living_state_tax         = $employeedata[0]['living_state_tax'];
        $hrate                    = $timesheetdata[0]['h_rate'];
        $total_hours              = $timesheetdata[0]['total_hours'];
        $payperiod                = $timesheetdata[0]['month'];
        $get_date                 = explode('-', $payperiod);
        $end_date                 = $get_date[1];
        $scAmount                 = $this->saleCommission($employee_id, $payperiod, $user_id, $admin_id);
        $thisPeriodAmount         = $this->thisPeriodAmount($timesheetdata[0]['payroll_type'], $timesheetdata[0]['payroll_freq'], $total_hours, $hrate, $scAmount, $timesheetdata[0]['extra_amount'], $timesheetdata[0]['amount'], $user_id, $admin_id);
        $admin_name               = $this->Hrm_model->getDatas('administrator', '*', ['adm_id' => $timesheetdata[0]['admin_name']]);
        // Country Tax Starts //
        $f                    = $this->countryTax('Federal Income tax', $employeedata[0]['employee_tax'], $thisPeriodAmount, $employee_id, 'f_tax', $user_id, $end_date, $timesheetdata[0]['timesheet_id']);
        $this_period_federal  = $f['tax_value'];
        $overall_federal      = $f['tax_data']['t_f_tax'];
        $s                    = $this->countryTax('Social Security', $employeedata[0]['employee_tax'], $thisPeriodAmount, $employee_id, 's_tax', $user_id, $end_date, $timesheetdata[0]['timesheet_id']);
        $this_period_social   = $s['tax_value'];
        $overall_social       = $s['tax_data']['t_s_tax'];
        $m                    = $this->countryTax('Medicare', $employeedata[0]['employee_tax'], $thisPeriodAmount, $employee_id, 'm_tax', $user_id, $end_date, $timesheetdata[0]['timesheet_id']);
        $this_period_medicare = $m['tax_value'];
        $overall_medicare     = $m['tax_data']['t_m_tax'];
        $u                    = $this->countryTax('Federal unemployment', $employeedata[0]['employee_tax'], $thisPeriodAmount, $employee_id, 'u_tax', $user_id, $end_date, $timesheetdata[0]['timesheet_id']);
        $this_period_unemp    = $u['tax_value'];
        $overall_unemp        = $u['tax_data']['t_u_tax'];
        // Country Tax Ends //
        $working_state_tax = $this->state_tax($end_date, $employeedata[0]['id'], $employeedata[0]['employee_tax'], $working_state_tax, $user_id, $thisPeriodAmount, 'state_tax', $timesheetdata[0]['timesheet_id'],$employeedata[0]['payroll_type'], $employeedata[0]['payroll_freq']);
        if (trim($employeedata[0]['working_state_tax']) != trim($employeedata[0]['living_state_tax'])) {
            $living_state_tax = $this->state_tax($end_date, $employeedata[0]['id'], $employeedata[0]['employee_tax'], $living_state_tax, $user_id, $thisPeriodAmount, 'living_state_tax', $timesheetdata[0]['timesheet_id'],$employeedata[0]['payroll_type'], $employeedata[0]['payroll_freq']);
        }
        $data = array(
            'id'               => $user_id,
            'admin_id'         => $admin_id,
            'working_state'    => $working_state_tax,
            'living_state'     => $living_state_tax,
            'this_federal'     => $f,
            'overall_federal'  => $overall_federal,
            'this_social'      => $s,
            'overall_social'   => $overall_social,
            'this_medicare'    => $m,
            'overall_medicare' => $overall_medicare,
            'this_unemp'       => $u,
            'overall_unemp'    => $overall_unemp,
            'company_info'     => $company_info,
            'employee_info'    => $employeedata,
            'timesheet_info'   => $timesheetdata,
            'overtime_hour'    => $overtime_hour,
            'setting'          => $default_setting,
            'admin'            => $admin_name,
            'ytd'              => $f['ytd'],
        );
        $content = $this->parser->parse('hr/pay_slip', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function check_employee_pay_type() {
        $employeeId = $this->input->post('employeeId');
        $pay_type   = $this->db->select('payroll_type')->from('employee_history')->where('id', $employeeId)->get()->row()->payroll_type;
        if (empty($pay_type)) {
            $pay_type = 'Sales Partner';
        } else {
            echo $pay_type;
        }
    }
    public function updatepayslipinvoicedesign($id) {
        $query = 'update payslip_invoice_design set template=' . $id;
        $this->db->query($query);
        redirect('Chrm/payslip_setting');
    }
    public function add_taxname_data() {
        $this->load->model('Hrm_model');
        $postData = $this->input->post('value');
        $data     = $this->Hrm_model->insert_taxesname($postData);
    }
    public function payslip_setting() {

        $data['title'] = display('payslip');

        $this->CI->load->model('Web_settings');
        $this->CI->load->model('Invoice_content');
        $setting_detail = $this->CI->Web_settings->retrieve_setting_editdata();
        $dataw          = $this->CI->Invoice_content->get_data_payslip();
        $datacontent    = $this->CI->Invoice_content->retrieve_data();

        $data = array(
            'header'       => (!empty($dataw[0]['header']) ? $dataw[0]['header'] : ''),
            'logo'         => (!empty($dataw[0]['logo']) ? $dataw[0]['logo'] : ''),
            'color'        => (!empty($dataw[0]['color']) ? $dataw[0]['color'] : ''),
            'invoice_logo' => (!empty($setting_detail[0]['invoice_logo']) ? $setting_detail[0]['invoice_logo'] : ''),
            'address'      => (!empty($datacontent[0]['address']) ? $datacontent[0]['address'] : ''),
            'cname'        => (!empty($datacontent[0]['business_name']) ? $datacontent[0]['business_name'] : ''),
            'mobile'       => (!empty($datacontent[0]['phone']) ? $datacontent[0]['phone'] : ''),
            'email'        => (!empty($datacontent[0]['email']) ? $datacontent[0]['email'] : ''),
            'template'     => (!empty($dataw[0]['template']) ? $dataw[0]['template'] : ''),
        );
        $content = $this->parser->parse('hr/payslip_view', $data, true);
        $this->template->full_admin_html_view($content);
    }

    public function employee_payslip_permission() {
        $data['title']           = display('Payment_Administration');
        $id                      = $this->input->get('timesheet_id');
        $user_id                 = $this->input->get('id');
        $company_id              = $this->input->get('admin_id');
        $decodedId               = decodeBase64UrlParameter($user_id);
        $data['time_sheet_data'] = $this->Hrm_model->time_sheet_data($id);
        $data['employee_name']   = $this->Hrm_model->employee_name($data['time_sheet_data'][0]['templ_name']);
        $data['designation']     = $this->Hrm_model->getemp_data($id);
        $data['employee']        = $this->Hrm_model->employee_partner($data['time_sheet_data'][0]['templ_name']);
        $data['payment_terms']   = $this->Hrm_model->get_payment_terms();
        $setting_detail          = $this->Web_settings->retrieve_setting_editdata(decodeBase64UrlParameter($_GET['id']));
        $data['dailybreak']      = $this->Hrm_model->get_dailybreak();
        $data['duration']        = $this->Hrm_model->get_duration_data();
        $data['setting_detail']  = $setting_detail;
        $data['administrator']   = $this->Hrm_model->administrator_data();
        $data['extratime_info']  = $this->Hrm_model->get_overtime_data($decodedId);

        $content = $this->parser->parse('hr/emp_payslip_permission', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function officeloan_edit($transaction_id) {
        $this->load->model('Hrm_model');
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $CI->load->model('Invoices');
        $CI->load->model('Settings');
        $office_loan_datas = $this->Hrm_model->office_loan_datas($transaction_id);
        $setting_detail    = $CI->Web_settings->retrieve_setting_editdata();
        $bank_name         = $CI->db->select('bank_id,bank_name')
            ->from('bank_add')
            ->get()
            ->result_array();
        $data['bank_list'] = $CI->Web_settings->bank_list();
        $paytype           = $CI->Invoices->payment_type();
        $CI                = &get_instance();
        $CI->load->model('Web_settings');
        $selected_bank_name  = $this->db->select('bank_name')->from('bank_add')->where('bank_id', $office_loan_datas[0]['bank_name'])->get()->row()->bank_name;
        $data['payment_typ'] = $paytype;
        $data['bank_name']   = $bank_name;
        $person_listdaa      = $CI->Settings->office_loan_person();
        $data                = array(
            'id'                 => $office_loan_datas[0]['id'],
            'person_id'          => $office_loan_datas[0]['person_id'],
            'date'               => $office_loan_datas[0]['date'],
            'debit'              => $office_loan_datas[0]['debit'],
            'details'            => $office_loan_datas[0]['details'],
            'phone'              => $office_loan_datas[0]['phone'],
            'paytype'            => $office_loan_datas[0]['paytype'],
            'bank_name1'         => $office_loan_datas[0]['bank_name'],
            'selected_bank_name' => $selected_bank_name,
            'transaction_id'     => $office_loan_datas[0]['transaction_id'],
            'person_list'        => $person_listdaa,
            'status'             => $office_loan_datas[0]['status'],
            'description'        => $office_loan_datas[0]['description'],
            'bank_name'          => $bank_name,
            'payment_typ'        => $paytype,
            'tran_id'            => $transaction_id,
            'setting_detail'     => $setting_detail,
        );
        $content = $this->parser->parse('hr/edit_officeloan', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function delete_expense($id = null) {
        $this->db->where('id', $id);
        $this->db->delete('expense');
        redirect('Chrm/expense_list');
        $this->template->full_admin_html_view($content);
    }
    public function edit_expense($id) {
        $this->load->library('lsettings');
        $content = $this->lsettings->expense_show_by_id($id);
        $this->template->full_admin_html_view($content);
    }
    public function employee_update_form() {
        $employee_id                 = isset($_GET['employee']) ? $_GET['employee'] : null;
        $encodedId                   = isset($_GET['id']) ? $_GET['id'] : null;
        $decodedId                   = decodeBase64UrlParameter($encodedId);
        $setting_detail              = $this->Web_settings->retrieve_setting_editdata($decodedId);
        $currency_details            = $this->Web_settings->retrieve_setting_editdata($decodedId);
        $curn_info_default           = $this->Hrm_model->curn_info_default($currency_details[0]["currency"], $decodedId);
        $data["setting_detail"]      = $setting_detail;
        $data["curn_info_default"]   = $curn_info_default[0]["currency_name"];
        $data["currency"]            = $currency_details[0]["currency"];
        $data["get_info_city_tax"]   = $this->Hrm_model->get_info_city_tax($decodedId);
        $data["get_info_county_tax"] = $this->Hrm_model->get_info_county_tax($decodedId);
        $data["encodedId"]           = $decodedId;
        $data["title"]               = display("employee_update");
        $data["employee_data"]       = $this->Hrm_model->employee_editdata($employee_id);
        $data["attachmentData"]      = $this->Hrm_model->editAttachment($employee_id, $decodedId);
        $data["state_tx"]            = $this->Hrm_model->state_tax($decodedId);
        $data["cty_tax"]             = $this->Hrm_model->state_tax($decodedId);
        $data["designation"]         = $this->Hrm_model->getdesignation($data["employee_data"][0]["designation"], $decodedId);
        $data["country_data"]        = $this->Hrm_model->getDatas('country', '*', ['id !=' => '']);

        $data["desig"] = $this->Hrm_model->designation_dropdown($decodedId);
        $content       = $this->parser->parse("hr/employee_updateform", $data, true);
        $this->template->full_admin_html_view($content);
    }

    public function update_employee() {
        $this->load->model("Hrm_model");
        $response     = array();
        $insertImages = '';
        if (isset($_FILES["files"]) && is_array($_FILES["files"]["name"])) {
            $no_files = count($_FILES["files"]["name"]);
            $images   = [];
            for ($i = 0; $i < $no_files; $i++) {
                if ($_FILES["files"]["error"][$i] > 0) {
                } else {
                    move_uploaded_file(
                        $_FILES["files"]["tmp_name"][$i],
                        "assets/uploads/employeedetails/" . $_FILES["files"]["name"][$i]
                    );
                    $images[] = $_FILES["files"]["name"][$i];
                }
            }
            $insertImages = implode(', ', $images);
            $old_images   = isset($_POST['old_image']) ? $_POST['old_image'] : [];
        } else {
            $old_images = isset($_POST['old_image']) ? $_POST['old_image'] : [];
        }
        if ($_FILES["profile_image"]["name"]) {
            $config["upload_path"]   = "assets/uploads/profile";
            $config["allowed_types"] = "gif|jpg|png|jpeg|JPEG|GIF|JPG|PNG";
            $config["encrypt_name"]  = true;
            $config["max_size"]      = 2048;
            $this->load->library("upload", $config);
            if (!$this->upload->do_upload("profile_image")) {
                $error = ["error" => $this->upload->display_errors()];
                redirect(base_url("Chrm"));
            } else {
                $data                     = $this->upload->data();
                $profile_image            = $data["file_name"];
                $config["image_library"]  = "gd2";
                $config["source_image"]   = $data["full_path"];
                $config["create_thumb"]   = false;
                $config["maintain_ratio"] = true;
                $config["width"]          = 200;
                $config["height"]         = 200;
                $this->load->library("image_lib", $config);
                $this->image_lib->resize();
            }
        } else {
            $profile_image = isset($_POST['old_profileimage']) ? $_POST['old_profileimage'] : '';
        }
        $state_tax         = $this->input->post("state_tax");
        $living_state_tax  = $this->input->post("living_state_tax");
        $city_tax          = $this->input->post("city_tax");
        $living_city_tax   = $this->input->post("living_city_tax");
        $county_tax        = $this->input->post("county_tax");
        $living_county_tax = $this->input->post("living_county_tax");
        $other_working_tax = $this->input->post("other_working_tax");
        $other_living_tax  = $this->input->post("other_living_tax");
        $data_employee     = [
            "working_state_tax"  => $state_tax,
            "living_state_tax"   => ($state_tax != $living_state_tax) ? $living_state_tax : $state_tax,
            "working_city_tax"   => $city_tax,
            "living_city_tax"    => ($city_tax != $living_city_tax) ? $living_city_tax : $city_tax,
            "working_county_tax" => $county_tax,
            "living_county_tax"  => ($county_tax != $living_county_tax) ? $living_county_tax : $county_tax,
            "working_other_tax"  => $other_working_tax,
            "living_other_tax"   => ($other_working_tax != $other_living_tax) ? $other_living_tax : $other_working_tax,
        ];
        $postData = [
            "id"                     => $this->input->post("employee_id", true),
            "first_name"             => $this->input->post("first_name", true),
            "middle_name"            => $this->input->post("middle_name", true),
            "last_name"              => $this->input->post("last_name", true),
            "designation"            => $this->input->post("designation", true),
            "phone"                  => $this->input->post("phone", true),
            "files"                  => !empty($old_images) ? $old_images : $insertImages,
            "rate_type"              => $this->input->post("paytype", true),
            "sc"                     => $this->input->post("sc", true),
            "email"                  => $this->input->post("email", true),
            "employee_tax"           => $this->input->post("emp_tax_detail", true),
            "social_security_number" => $this->input->post("ssn", true),
            "routing_number"         => $this->input->post("routing_number", true),
            "hrate"                  => $this->input->post("hrate", true),
            "address_line_1"         => $this->input->post("address_line_1", true),
            "address_line_2"         => $this->input->post("address_line_2", true),
            "country"                => $this->input->post("country", true),
            "city"                   => $this->input->post("city", true),
            "zip"                    => $this->input->post("zip", true),
            "state"                  => $this->input->post("state", true),
            "emergencycontact"       => $this->input->post("emergencycontact", true),
            "emergencycontactnum"    => $this->input->post("emergencycontactnum", true),
            "profile_image"          => $profile_image,
            "payroll_type"           => $this->input->post("payroll_type"),
            "working_state_tax"      => $data_employee["working_state_tax"],
            "working_city_tax"       => $data_employee["working_city_tax"],
            "working_county_tax"     => $data_employee["working_county_tax"],
            "working_other_tax"      => $data_employee["working_other_tax"],
            "living_state_tax"       => $data_employee["living_state_tax"],
            "living_city_tax"        => $data_employee["living_city_tax"],
            "living_county_tax"      => $data_employee["living_county_tax"],
            "living_other_tax"       => $data_employee["living_other_tax"],
        ];
        $result = $this->Hrm_model->update_employee($this->input->post("employee_id", true), $postData);
        if ($result) {
            $response['status'] = 'success';
            $response['msg']    = 'Employee has been updated successfully';
        } else {
            $response['status'] = 'failure';
            $response['msg']    = 'Failed to update Employee. Please try again.';
        }
        echo json_encode($response);
    }

    public function update_expense($id) {
        $this->load->library('lsettings');
        $content = $this->lsettings->update_expense_id($id);
        $this->template->full_admin_html_view($content);
        redirect('Chrm/expense_list');
    }
    // Expense Insert data
    public function create_expense() {
        $this->form_validation->set_rules('expense_name', display('expense_name'), 'required|max_length[100]');
        $this->form_validation->set_rules('expense_date', display('expense_date'), 'required|max_length[100]');
        $this->form_validation->set_rules('expense_payment_date', display('expense_payment_date'), 'required|max_length[100]');
        $postData = [
            'emp_name'             => $this->input->post('person_id', true),
            'expense_name'         => $this->input->post('expense_name', true),
            'expense_date'         => $this->input->post('expense_date', true),
            'expense_amount'       => $this->input->post('expense_amount', true),
            'total_amount'         => $this->input->post('total_amount', true),
            'expense_payment_date' => $this->input->post('expense_payment_date', true),
            'description'          => $this->input->post('description', true),
            'unique_id'            => $this->session->userdata('unique_id'),
            'create_by'            => $this->session->userdata('user_id'),
        ];
        $this->db->insert('expense', $postData);
        redirect(base_url('Chrm/expense_list'));
    }
    private function processTaxData($key, $value) {
        if (trim(round($value, 3)) > 0) {
            $split = explode('-', $key);
            $tx_n  = str_replace("'", "", $split[1]);
            $code  = isset($split[2]) ? str_replace("'", "", $split[2]) : '';
            return [
                'tx_n' => $tx_n,
                'code' => $code,
            ];
        }
        return null;
    }
    public function office_loan_inserthtml($transaction_id) {
        $CC = &get_instance();
        $CA = &get_instance();
        $CI = &get_instance();
        $CI->auth->check_admin_auth();
        $CI->load->model('invoice_content');
        $w = &get_instance();
        $w->load->model('Ppurchases');
        $CI->load->model('Invoices');
        $CI->load->model('Web_settings');
        $CC->load->model('invoice_content');
        $this->load->model('Hrm_model');
        $company_info      = $w->Ppurchases->retrieve_company();
        $office_loan_datas = $this->Hrm_model->office_loan_datas($transaction_id);
        $datacontent       = $CC->invoice_content->retrieve_data();
        $dataw             = $CA->Invoice_content->retrieve_data();
        $setting           = $CI->Web_settings->retrieve_setting_editdata();
        $data              = array(
            'header'            => $dataw[0]['header'],
            'logo'              => (!empty($setting[0]['invoice_logo']) ? $setting[0]['invoice_logo'] : $company_info[0]['logo']),
            'color'             => $dataw[0]['color'],
            'template'          => $dataw[0]['template'],
            'person_id'         => $office_loan_datas[0]['person_id'],
            'date'              => $office_loan_datas[0]['date'],
            'debit'             => $office_loan_datas[0]['debit'],
            'details'           => $office_loan_datas[0]['details'],
            'phone'             => $office_loan_datas[0]['phone'],
            'paytype'           => $office_loan_datas[0]['paytype'],
            'paytype'           => $office_loan_datas[0]['paytype'],
            'paytype'           => $office_loan_datas[0]['paytype'],
            'company'           => $datacontent,
            'company'           => (!empty($datacontent[0]['company_name']) ? $datacontent[0]['company_name'] : $company_info[0]['company_name']),
            'phone'             => (!empty($datacontent[0]['mobile']) ? $datacontent[0]['mobile'] : $company_info[0]['mobile']),
            'email'             => (!empty($datacontent[0]['email']) ? $datacontent[0]['email'] : $company_info[0]['email']),
            'website'           => (!empty($datacontent[0]['website']) ? $datacontent[0]['website'] : $company_info[0]['website']),
            'address'           => (!empty($datacontent[0]['address']) ? $datacontent[0]['address'] : $company_info[0]['address']),
            'office_loan_datas' => $office_loan_datas,
        );
        $content = $this->load->view('hr/office_loan_html', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function time_sheet_pdf($id) {
        $CI = &get_instance();
        $CC = &get_instance();
        $CA = &get_instance();
        $w  = &get_instance();
        $w->load->model('Ppurchases');
        $CI->load->model('Web_settings');
        $CC->load->model('invoice_content');
        $CI = &get_instance();
        $this->auth->check_admin_auth();
        $CI->load->model('Hrm_model');
        $pdf           = $CI->Hrm_model->time_sheet_data($id);
        $company_info  = $w->Ppurchases->retrieve_company();
        $employee_data = $this->db->select('first_name,last_name,designation,id')->from('employee_history')->where('id', $pdf[0]['templ_name'])->get()->row();
        $setting       = $CI->Web_settings->retrieve_setting_editdata();
        $dataw         = $CA->Invoice_content->retrieve_data();
        $datacontent   = $CC->invoice_content->retrieve_data();
        $data          = array(
            'header'        => $dataw[0]['header'],
            'logo'          => (!empty($setting[0]['invoice_logo']) ? $setting[0]['invoice_logo'] : $company_info[0]['logo']),
            'color'         => $dataw[0]['color'],
            'template'      => $dataw[0]['template'],
            'company'       => $datacontent,
            'employee_name' => $employee_data->first_name . " " . $employee_data->last_name,
            'destination'   => $employee_data->designation,
            'id'            => $employee_data->id,
            'company'       => (!empty($datacontent[0]['company_name']) ? $datacontent[0]['company_name'] : $company_info[0]['company_name']),
            'phone'         => (!empty($datacontent[0]['mobile']) ? $datacontent[0]['mobile'] : $company_info[0]['mobile']),
            'email'         => (!empty($datacontent[0]['email']) ? $datacontent[0]['email'] : $company_info[0]['email']),
            'website'       => (!empty($datacontent[0]['website']) ? $datacontent[0]['website'] : $company_info[0]['website']),
            'address'       => (!empty($datacontent[0]['address']) ? $datacontent[0]['address'] : $company_info[0]['address']),
            'time_sheet'    => $pdf,
        );
        $content = $this->load->view('hr/timesheet_pdf', $data, true);
        $this->template->full_admin_html_view($content);
    }

    public function timesheed_inserted_data() {
        $this->auth->check_admin_auth();
        $this->load->model('Web_settings');
        $this->load->model('Hrm_model');
        $type         = $this->input->get('type');
        $emp_data     = [];
        $setting      = $this->CI->Web_settings->retrieve_setting_editdata();
        $company_info = $this->CI->Web_settings->retrieve_companysetting_editdata();
        if ($type == 'emp_data') {
            $id       = $this->input->get('employee');
            $emp_data = $this->Hrm_model->getDatas('employee_history', '*', ['id' => $id]);
        } else {
            /* return timesheet_info and employee history datas */
            $id                = $this->input->get('timesheet_id');
            $timesheet_data    = $this->CI->Hrm_model->timesheet_data($id);
            $timesheet_details = $this->CI->Hrm_model->getDatas('timesheet_info_details', '*', ['timesheet_id' => $id]);
            $admin_name        = $this->Hrm_model->getDatas('administrator', '*', ['adm_id' => $timesheet_data[0]['admin_name']]);
        }
        $fname = 'Employee';
        $data  = array(
            'company_name' => $company_info[0]['company_name'],
            'com_phone'    => $company_info[0]['mobile'],
            'com_email'    => $company_info[0]['email'],
            'website'      => $company_info[0]['website'],
            'address'      => $company_info[0]['address'],
            'currency'     => $company_info[0]['currency'],
            'logo'         => (!empty($setting[0]['invoice_logo']) ? $setting[0]['invoice_logo'] : $company_info[0]['logo']),
            'color'        => $setting[0]['button_color'],
            'type'         => $type,
            'emp_datas'    => $emp_data,
        );
        if (!empty($timesheet_data)) {
            $fname = 'Timesheet';
            $data  = array(
                'id'             => $timesheet_data[0]['id'],
                'first_name'     => $timesheet_data[0]['first_name'],
                'last_name'      => $timesheet_data[0]['last_name'],
                'payroll_type'   => $timesheet_data[0]['payroll_type'],
                'designation'    => $timesheet_data[0]['designation'],
                'sheet_date'     => $timesheet_data[0]['month'],
                'cheque_date'    => $timesheet_data[0]['cheque_date'],
                'cheque_no'      => $timesheet_data[0]['cheque_no'],
                'payment_method' => $timesheet_data[0]['payment_method'],
                'timesheet_data' => $timesheet_details,
                'total_hours'    => $timesheet_data[0]['total_hours'],
                'admin_name'     => $admin_name[0]['adm_name'],
            );
        }
        $content = $this->load->view('hr/emp_timesheet_html', $data, true);
        $PDF     = new Dompdf();
        $PDF->loadHtml($content);
        $PDF->setPaper('A4', 'portrait');
        $PDF->set_option('isHtml5ParserEnabled', true);
        $PDF->set_option('isCssFloatEnabled', true);
        $PDF->render();
        $filename = $fname . '-details.pdf';
        if (empty($pdf)) {
            $PDF->stream($filename, array('Attachment' => 0));
        } else {
            return $content;
        }
    }
    public function office_loan_delete($transaction_id) {
        $this->load->model('Hrm_model');
        $this->Hrm_model->delete_off_loan($transaction_id);
        $this->session->set_userdata(array('message' => display('successfully_delete')));
        redirect("Chrm/manage_officeloan");
    }
// Manage Timesheet
    public function manage_timesheet() {
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $this->load->model('Hrm_model');
        $setting_detail             = $CI->Web_settings->retrieve_setting_editdata();
        $data['setting_detail']     = $setting_detail;
        $data['title']              = 'Manage Timesheet';
        $data['timesheet_list']     = $this->Hrm_model->timesheet_list();
        $data['timesheet_data_get'] = $this->Hrm_model->timesheet_data_get();
        $data['employee_data']      = $this->Hrm_model->employee_data_get();
        $content                    = $this->parser->parse('hr/timesheet_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
// Fetch data in Manage TimeSheet List - Madhu
    public function manageTimesheetListData() {
        $encodedId      = isset($_GET["id"]) ? $_GET["id"] : null;
        $admin_id       = isset($_GET['admin_id']) ? $_GET['admin_id'] : null;
        $decodedId      = decodeBase64UrlParameter($encodedId);
        $limit          = $this->input->post("length");
        $start          = $this->input->post("start");
        $search         = $this->input->post("search")["value"];
        $orderField     = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection = 'desc';
        $emp_name       = $this->input->post('employee_name');
        $items          = $this->Hrm_model->getPaginatedmanagetimesheetlist($limit, $start, $orderField, $orderDirection, $search, $emp_name);
        $totalItems     = $this->Hrm_model->getTotalmanagetimesheetlist($search, $emp_name);
        $data           = [];
        $i              = $start + 1;
        $edit           = "";
        $delete         = "";
        foreach ($items as $item) {
            $user     = '<a href="' . base_url("Chrm/employee_payslip_permission?id=" . $encodedId . "&admin_id=" . $admin_id . "&timesheet_id=" . $item['timesheet_id']) . '" class="btnclr btn btn-sm"> <i class="fa fa-user" aria-hidden="true"></i> </a>';
            $download = '<a href="' . base_url("Chrm/timesheed_inserted_data?id=" . $encodedId . "&admin_id=" . $admin_id . "&timesheet_id=" . $item['timesheet_id'] . "&type=timesheet") . '" class="btnclr btn btn-sm">
                <i class="fa fa-download" aria-hidden="true"></i>
             </a>';
            $delete = '<a onClick="deleteTimesheetdata(' . $item["timesheet_id"] . ', \'' . $item["month"] . '\')" class="btnclr btn btn-sm" style="background-color:#424f5c; margin-right: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>';
            $status = ($item['uneditable'] == 1) ? '<span class="green">Generated</span>' : '<span class="red">Pending</span>';
            $edit   = ($item['uneditable'] == 1) ? "" : '<a href="' . base_url("Chrm/edit_timesheet?id=" . $encodedId . "&admin_id=" . $admin_id . "&timesheet_id=" . $item['timesheet_id']) . '" class="btnclr btn btn-sm" title="Edit"> <i class="fa fa-edit"></i> </a>';
            $row    = [
                'id'           => $i,
                "first_name"   => $item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"],
                "job_title"    => $item["job_title"],
                "payroll_type" => $item["payroll_type"],
                "month"        => $item["month"],
                "total_hours"  => $item["total_hours"],
                "uneditable"   => $status,
                "action"       => $user . " " . $download . " " . $edit . " " . $delete,
            ];
            $data[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
// Manage Time Sheet Data Delete - Madhu
    public function timesheet_delete() {
        $this->load->model('Hrm_model');
        $id     = $this->input->post('id');
        $month  = $this->input->post('month');
        $result = $this->Hrm_model->deleteTimesheetdata($id);
        logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), $id, $month, $this->session->userdata('userName'), 'Delete Timesheet', 'Human Resource', 'TimeSheet has been deleted successfully', 'Delete', date('m-d-Y'));
        if ($result) {
            $response = array('status' => 'success', 'msg' => 'TimeSheet has been deleted successfully!');
        } else {
            $response = array('status' => 'failure', 'msg' => 'Unable to delete the timeSheet. Please try again!');
        }
        echo json_encode($response);
    }
    public function manage_officeloan() {
        $this->load->model('Hrm_model');
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $setting_detail              = $CI->Web_settings->retrieve_setting_editdata();
        $data['title']               = display('manage_employee');
        $data['office_loan_list']    = $this->Hrm_model->office_loan_list();
        $data['officeloan_data_get'] = $this->Hrm_model->officeloan_data_get();
        $data['setting_detail']      = $setting_detail;
        $content                     = $this->parser->parse('hr/officeloan_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function add_dailybreak_info() {
        $postData = $this->input->post('dailybreak_name');
        $data     = $this->Hrm_model->insert_dailybreak_data($postData);
        echo json_encode($data);
    }
// Payslip Function - Madhu
    public function pay_slip() {

        // echo '<pre>'; print_r($_POST); echo '</pre>'; die;

        list($user_id, $company_id)       = array_map('decodeBase64UrlParameter', [$this->input->post('admin_company_id'), $this->input->post('adminId')]);
        $company_info                     = $this->Hrm_model->retrieve_companyinformation($user_id);
        $datacontent                      = $this->Hrm_model->retrieve_companydata($user_id);
        $data['title']                    = display('pay_slip');
        $data['business_name']            = (!empty($datacontent[0]['company_name']) ? $datacontent[0]['company_name'] : $company_info[0]['company_name']);
        $data['phone']                    = (!empty($datacontent[0]['mobile']) ? $datacontent[0]['mobile'] : $company_info[0]['mobile']);
        $data['email']                    = (!empty($datacontent[0]['email']) ? $datacontent[0]['email'] : $company_info[0]['email']);
        $data['address']                  = (!empty($datacontent[0]['address']) ? $datacontent[0]['address'] : $company_info[0]['address']);
        $data_timesheet['total_hours']    = $this->input->post('total_net');
        $data_timesheet['templ_name']     = $this->input->post('templ_name');
        $data_timesheet['payroll_type']   = $this->input->post('payroll_type');
        $data_timesheet['payroll_freq']   = $this->input->post('payroll_freq');
        $data_timesheet['duration']       = $this->input->post('duration');
        $data_timesheet['job_title']      = $this->input->post('job_title');
        $data_timesheet['month']          = $this->input->post('date_range');
        $date_split                       = explode(' - ', $this->input->post('date_range'));
        $data_timesheet['start']          = $date_split[0];
        $data_timesheet['end']            = $date_split[1];
        $start_date                       = $data_timesheet['start'];
        $month                            = date('m', strtotime(str_replace('/', '-', $start_date)));
        $quarter                          = $this->getQuarter($month);
        $data_timesheet['quarter']        = $quarter;
        $data_timesheet['timesheet_id']   = $this->input->post('tsheet_id');
        $data_timesheet['create_by']      = $this->session->userdata('user_id');
        $data_timesheet['admin_name']     = (!empty($this->input->post('administrator_person', TRUE)) ? $this->input->post('administrator_person', TRUE) : '');
        $data_timesheet['payment_method'] = (!empty($this->input->post('payment_method', TRUE)) ? $this->input->post('payment_method', TRUE) : '');
        $data_timesheet['cheque_no']      = (!empty($this->input->post('cheque_no', TRUE)) ? $this->input->post('cheque_no', TRUE) : '');
        $data_timesheet['cheque_date']    = (!empty($this->input->post('cheque_date', TRUE)) ? $this->input->post('cheque_date', TRUE) : '');
        $data_timesheet['bank_name']      = (!empty($this->input->post('bank_name', TRUE)) ? $this->input->post('bank_name', TRUE) : '');
        $data_timesheet['payment_ref_no'] = (!empty($this->input->post('payment_refno', TRUE)) ? $this->input->post('payment_refno', TRUE) : '');
        if (!empty($this->input->post('administrator_person', TRUE))) {
            $data_timesheet['uneditable'] = 1;
        } else {
            $data_timesheet['uneditable'] = 0;
        }
        $u_id                        = $this->input->post('unique_id');
        $data_timesheet['unique_id'] = $u_id;
        $purchase_id_1               = $this->db->where('templ_name', $this->input->post('templ_name'))
            ->where('timesheet_id', $data_timesheet['timesheet_id'])
            ->where('create_by', $user_id);
        $q      = $this->db->get('timesheet_info');
        $row    = $q->row_array();
        $old_id = isset($row['timesheet_id']) ? trim($row['timesheet_id']) : null;
        if (!empty($old_id)) {
            $this->session->set_userdata("timesheet_id_old", $row['timesheet_id']);
            $this->db->where('timesheet_id', $this->session->userdata("timesheet_id_old"));
            $this->db->delete('timesheet_info');
            $this->db->where('timesheet_id', $this->session->userdata("timesheet_id_old"));
            $this->db->delete('timesheet_info_details');
            logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), $data_timesheet['timesheet_id'], $data_timesheet['month'], $this->session->userdata('userName'), 'Add TimeSheet', 'Human Resource', 'TimeSheet has been added successfully', 'Add', date('m-d-Y'));
            $this->db->insert('timesheet_info', $data_timesheet);
        } else {
            logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), $data_timesheet['timesheet_id'], $data_timesheet['month'], $this->session->userdata('userName'), 'Add TimeSheet', 'Human Resource', 'TimeSheet has been added successfully', 'Add', date('m-d-Y'));
            $this->db->insert('timesheet_info', $data_timesheet);
        }
        $purchase_id_2 = $this->db->select('timesheet_id')
            ->from('timesheet_info')
            ->where('templ_name', $this->input->post('templ_name'))
            ->where('month', $this->input->post('date_range'))
            ->get()->row()->timesheet_id;

        $this->session->set_userdata("timesheet_id_new", $purchase_id_2);
        $date1          = $this->input->post('date');
        $day1           = $this->input->post('day');
        $time_start1    = $this->input->post('start');
        $time_end1      = $this->input->post('end');
        $hours_per_day1 = $this->input->post('sum');
        $daily_bk1      = $this->input->post('dailybreak');
        $present1       = $this->input->post('block');
        for ($i = 0, $n = count($this->input->post('date')); $i < $n; $i++) {
            $present       = isset($present1[$i]) ? $present1[$i] : null;
            $date          = isset($this->input->post('date')[$i]) ? $this->input->post('date')[$i] : null;
            $day           = isset($this->input->post('day')[$i]) ? $this->input->post('day')[$i] : null;
            $time_start    = isset($this->input->post('start')[$i]) ? $this->input->post('start')[$i] : null;
            $time_end      = isset($this->input->post('end')[$i]) ? $this->input->post('end')[$i] : null;
            $hours_per_day = isset($this->input->post('sum')[$i]) ? $this->input->post('sum')[$i] : null;
            $daily_bk      = isset($this->input->post('dailybreak')[$i]) ? $this->input->post('dailybreak')[$i] : null;
            $data_info     = array(
                'timesheet_id'  => $this->session->userdata("timesheet_id_new"),
                'present'       => $present,
                'Date'          => $date,
                'Day'           => $day,
                'time_start'    => $time_start,
                'daily_break'   => $daily_bk,
                'time_end'      => $time_end,
                'hours_per_day' => $hours_per_day,
                'created_by'    => $user_id,
            );
            $this->db->insert('timesheet_info_details', $data_info);
        }
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect(base_url('Chrm/manage_timesheet?id=' . urlencode($this->input->post('admin_company_id')) . '&admin_id=' . urlencode($this->input->post('adminId'))));
    }
    public function expense_list() {
        $setting_detail            = $this->Web_settings->retrieve_setting_editdata();
        $data['expen_list']        = $this->Hrm_model->expense_list();
        $data['expenses_data_get'] = $this->Hrm_model->expenses_data_get();
        $data['setting_detail']    = $setting_detail;
        $content                   = $this->parser->parse('hr/expense_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function pay_slip_list() {
        $data['title'] = display('pay_slip_list');
        $this->load->model('Hrm_model');
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $setting_detail        = $CI->Web_settings->retrieve_setting_editdata();
        $data['employee_data'] = $this->Hrm_model->employee_data_get();
        $content               = $this->parser->parse('hr/pay_slip_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function payslipIndexData() {
        $encodedId          = isset($_GET['id']) ? $_GET['id'] : null;
        $admin_id           = isset($_GET['admin_id']) ? $_GET['admin_id'] : null;
        $decodedId          = decodeBase64UrlParameter($encodedId);
        $limit              = $this->input->post("length");
        $start              = $this->input->post("start");
        $search             = $this->input->post("search")["value"];
        $orderField         = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection     = $this->input->post("order")[0]["dir"];
        $date               = $this->input->post("payslip_date_search");
        $emp_name           = $this->input->post('employee_name');
        $items              = $this->Hrm_model->getPaginatedpayslip($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name);
        $sc_no_datainfo     = $this->Hrm_model->getPaginatedscpayslip($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name);
        $sc_info_choice_yes = $this->Hrm_model->getPaginatedscchoiceyes($limit, $start, $orderField, $orderDirection, $search, $date, $emp_name);
        array_merge($items, $sc_no_datainfo, $sc_info_choice_yes);
        $totalItems = $this->Hrm_model->getTotalpayslip($search, $date, $emp_name);
        $data       = [];
        $i          = $start + 1;
        $edit       = "";
        $delete     = "";
        foreach ($items as $item) {
            $row = [
                "table_id"    => $i,
                "first_name"  => $item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"],
                "job_title"   => $item["job_title"],
                "month"       => $item["month"],
                "cheque_date" => $item["cheque_date"],
                "total_hours" => (!empty($item['total_hours']) ? $item['total_hours'] : 0),
                "tot_amt"     => (!empty($item['extra_hour']) ? ($item['amount'] + $item['extra_amount']) : $item['extra_amount']),
                "overtime"    => !empty($item['extra_hour']) ? $item['hour'] : '0',
                "sales_comm"  => $item['sales_c_amount'],
                "action"      => "<a href='" . base_url('Chrm/time_list?id=' . $encodedId . '&admin_id=' . $admin_id . '&timesheet_id=' . $item['timesheet_id'] . '&templ_name=' . $item['templ_name']) . "' class='btnclr btn btn-success btn-sm'> <i class='fa fa-window-restore'></i> </a>",
            ];
            $data[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
// Admin Approve this Function
    public function adminApprove() {
        // echo '<pre>'; print_r($_POST); echo '</pre>'; die;
        list($user_id, $company_id)     = array_map('decodeBase64UrlParameter', [$this->input->post('admin_company_id'), $this->input->post('adminId')]);
        $company_info                   = $this->Hrm_model->retrieve_companyinformation($user_id);
        $datacontent                    = $this->Hrm_model->retrieve_companydata($user_id);
        $data_timesheet['total_hours']  = $this->input->post('total_net');
        $data_timesheet['templ_name']   = $this->input->post('templ_name');
        $data_timesheet['duration']     = $this->input->post('duration');
        $data_timesheet['job_title']    = $this->input->post('job_title');
        $data_timesheet['payroll_type'] = $this->input->post('payroll_type');
        $data_timesheet['payroll_freq'] = $this->input->post('payroll_freq');
        $extra_hour                     = $this->input->post('extra_hour');
        $data_timesheet['ytd']          = $this->input->post('above_extra_ytd');
        $data_timesheet['month']        = $this->input->post('date_range');
        $date_split                     = explode(' - ', $this->input->post('date_range'));
        $data_timesheet['start']        = $date_split[0];
        $data_timesheet['end']          = $date_split[1];
        if ($this->input->post('payment_method') == 'Cash') {
            $data_timesheet['cheque_date'] = (!empty($this->input->post('cash_date', TRUE)) ? $this->input->post('cash_date', TRUE) : '');
        } else if ($this->input->post('payment_method') == 'Cheque') {
            $data_timesheet['cheque_date'] = (!empty($this->input->post('cheque_date', TRUE)) ? $this->input->post('cheque_date', TRUE) : '');
        }
        $month                          = intval(substr($data_timesheet['start'], 0, 2));
        $quarter                        = $this->getQuarter($month);
        $data_timesheet['quarter']      = $quarter;
        $data_timesheet['timesheet_id'] = $this->input->post('tsheet_id');
        $data['employee_data']          = $this->Hrm_model->employee_info($this->input->post('templ_name'), $user_id);
        $data['timesheet_data']         = $this->Hrm_model->timesheet_info_data($data_timesheet['timesheet_id'], $user_id);
        $timesheetdata                  = $data['timesheet_data'];
        $employeedata                   = $data['employee_data'];
        if (!empty($extra_hour) && $employeedata[0]['payroll_type'] == 'Hourly') {
            $data_timesheet['extra_rate']   = $this->input->post('extra_rate');
            $data_timesheet['extra_amount'] = $this->input->post('extra_thisrate');
            $data_timesheet['extra_hour']   = $this->input->post('extra_this_hour');
            $data_timesheet['extra_ytd']    = $this->input->post('extra_ytd');
        } else {
            $data_timesheet['extra_rate']   = 0;
            $data_timesheet['extra_amount'] = 0;
            $data_timesheet['extra_hour']   = 0;
            $data_timesheet['extra_ytd']    = 0;
        }

        $data_timesheet['hour']   = $this->input->post('above_this_hours');
        $data_timesheet['amount'] = $this->input->post('above_extra_sum');

        $data_timesheet['create_by']      = $user_id;
        $data_timesheet['admin_name']     = (!empty($this->input->post('administrator_person', TRUE)) ? $this->input->post('administrator_person', TRUE) : '');
        $data_timesheet['payment_method'] = (!empty($this->input->post('payment_method', TRUE)) ? $this->input->post('payment_method', TRUE) : '');
        $data_timesheet['cheque_no']      = (!empty($this->input->post('cheque_no', TRUE)) ? $this->input->post('cheque_no', TRUE) : '');
        $data_timesheet['bank_name']      = (!empty($this->input->post('bank_name', TRUE)) ? $this->input->post('bank_name', TRUE) : '');
        $data_timesheet['payment_ref_no'] = (!empty($this->input->post('payment_refno', TRUE)) ? $this->input->post('payment_refno', TRUE) : '');
        $timesheet_id                     = $this->input->post('tsheet_id');
        $working_state_tax                = $data['employee_data'][0]['working_state_tax'];
        $living_state_tax                 = $data['employee_data'][0]['living_state_tax'];
        $hrate                            = $data['employee_data'][0]['hrate'];
        $data_timesheet['h_rate']         = $data['employee_data'][0]['hrate'];
        $payperiod                        = $data['timesheet_data'][0]['month'];
        $timesheet_id                     = $this->input->get('timesheet_id');
        $employee_id                      = $this->input->get('templ_name');
        if ($data['timesheet_data'][0]['payroll_type'] !== 'Sales Partner' || $data['employee_data'][0]['choice'] == 'Yes') {
            if (!empty($this->input->post('administrator_person', TRUE))) {
                $data_timesheet['uneditable'] = 1;
            } else {
                $data_timesheet['uneditable'] = 0;
            }
            $u_id                        = $this->input->post('unique_id');
            $data_timesheet['unique_id'] = $company_id;
            $date1                       = $this->input->post('date');
            $day1                        = $this->input->post('day');
            $time_start1                 = $this->input->post('start');
            $time_end1                   = $this->input->post('end');
            $hours_per_day1              = $this->input->post('sum');
            $daily_bk1                   = $this->input->post('dailybreak');
            $present1                    = $this->input->post('block');
            $purchase_id_1               = $this->db->where('templ_name', $this->input->post('templ_name'))->where('timesheet_id', $data_timesheet['timesheet_id']);
            $q                           = $this->db->get('timesheet_info');
            $row                         = $q->row_array();

            $old_id                      = trim($row['timesheet_id']);
            if (!empty($old_id)) {
                $this->session->set_userdata("timesheet_id_old", $row['timesheet_id']);
                $this->db->where('timesheet_id', $this->session->userdata("timesheet_id_old"));
                $this->db->delete('timesheet_info');
                $this->db->where('timesheet_id', $this->session->userdata("timesheet_id_old"));
                $this->db->delete('timesheet_info_details');
                logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), $data_timesheet['timesheet_id'], $data_timesheet['month'], $this->session->userdata('userName'), 'Add TimeSheet', 'Human Resource', 'TimeSheet has been added successfully', 'Add', date('m-d-Y'));
                $this->db->insert('timesheet_info', $data_timesheet);
            } else {
                logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), $data_timesheet['timesheet_id'], $data_timesheet['month'], $this->session->userdata('userName'), 'Add TimeSheet', 'Human Resource', 'TimeSheet has been added successfully', 'Add', date('m-d-Y'));
                $this->db->insert('timesheet_info', $data_timesheet);
            }
            $data['timesheet_data'] = $this->Hrm_model->timesheet_info_data($data_timesheet['timesheet_id'], $user_id);
            $purchase_id_2          = $this->db->select('timesheet_id')->from('timesheet_info')->where('templ_name', $this->input->post('templ_name'))->where('month', $this->input->post('date_range'))->get()->row()->timesheet_id;
            $this->session->set_userdata("timesheet_id_new", $purchase_id_2);
            if ($date1) {
                for ($i = 0, $n = count($date1); $i < $n; $i++) {
                    $date          = $date1[$i];
                    $day           = $day1[$i];
                    $daily_bk      = $daily_bk1[$i];
                    $time_start    = $time_start1[$i];
                    $time_end      = $time_end1[$i];
                    $hours_per_day = $hours_per_day1[$i];
                    $present       = isset($present1[$i]) ? $present1[$i] : null;
                    $data1         = array(
                        'timesheet_id'  => $this->session->userdata("timesheet_id_new"),
                        'Date'          => $date,
                        'Day'           => $day,
                        'daily_break'   => $daily_bk,
                        'time_start'    => $time_start,
                        'time_end'      => $time_end,
                        'hours_per_day' => $hours_per_day,
                        'present'       => $present,
                        'created_by'    => $this->session->userdata('user_id'),
                    );
                    $this->db->insert('timesheet_info_details', $data1);
                }
            }

            $payroll_type      = $data['timesheet_data'][0]['payroll_type'];
            $payroll_freq      = $data['timesheet_data'][0]['payroll_freq'];
            $hrate             = $hrate;
            $extra_thisrate    = $data['timesheet_data'][0]['extra_amount'];
            $above_extra_sum   = $data['timesheet_data'][0]['amount'];
            $scAmount          = 0;
            $final             = $this->thisPeriodAmount($payroll_type, $payroll_freq, $data_timesheet['total_hours'], $hrate, $scAmount, $extra_thisrate, $above_extra_sum, $user_id, $company_id);
            $s                 = '';
            $u                 = '';
            $m                 = '';
            $f                 = '';
            $f                 = $this->countryTax('Federal Income tax', $employeedata[0]['employee_tax'], $final, $timesheetdata[0]['templ_name'], 'f_tax', $user_id, $data_timesheet['end'], $employeedata[0]['id'], $timesheetdata[0]['timesheet_id']);
            $s                 = $this->countryTax('Social Security', $employeedata[0]['employee_tax'], $final, $timesheetdata[0]['templ_name'], 's_tax', $user_id, $data_timesheet['end'], $employeedata[0]['id'], $timesheetdata[0]['timesheet_id']);
            $m                 = $this->countryTax('Medicare', $employeedata[0]['employee_tax'], $final, $timesheetdata[0]['templ_name'], 'm_tax', $user_id, $data_timesheet['end'], $employeedata[0]['id'], $timesheetdata[0]['timesheet_id']);
            $u                 = $this->countryTax('Federal unemployment', $employeedata[0]['employee_tax'], $final, $timesheetdata[0]['templ_name'], 'u_tax', $user_id, $data_timesheet['end'], $employeedata[0]['id'], $timesheetdata[0]['timesheet_id']);

           
            $working_state_tax = $this->state_tax($data_timesheet['end'], $employeedata[0]['id'], $employeedata[0]['employee_tax'], $working_state_tax, $user_id, $final, 'state_tax', $timesheetdata[0]['timesheet_id'],$employeedata[0]['payroll_type'], $payroll_freq);
         
            if (trim($employeedata[0]['working_state_tax']) != trim($employeedata[0]['living_state_tax'])) {
               
                $living_state_tax = $this->state_tax($data_timesheet['end'], $employeedata[0]['id'], $employeedata[0]['employee_tax'], $employeedata[0]['living_state_tax'], $user_id, $final, 'living_state_tax', $timesheetdata[0]['timesheet_id'],$employeedata[0]['payroll_type'], $payroll_freq);
            }
            
        }
    
        redirect(base_url('Chrm/manage_timesheet?id=' . $this->input->post('admin_company_id') . '&admin_id=' . $this->input->post('adminId')));
    }
// Country Tax - Madhu
    public function countryTax($tax_type, $employee_tax_column, $final, $templ_name, $tax_history_column, $user_id, $endDate, $timesheet_id) {
        echo 
        $tax                = $this->db->select('*')->from('federal_tax')->where('tax', $tax_type)->where('created_by', $user_id)->get()->result_array();
        $tax_range          = '';
        $ytd                = [];
        $tax_value          = 0;
        $tax_value_employer = 0;
        $tax_employer       = 0;

        foreach ($tax as $amt) {
            $split = explode('-', $amt[$employee_tax_column]);
            if (count($split) == 2 && $final >= $split[0] && $final <= $split[1]) {
                $tax_range = $split[0] . "-" . $split[1];
                break;
            }
        }

        $tax_info_method = strtolower(str_replace(' ', '_', $tax_type)) . '_tax_info';

        $data[$tax_type] = $this->Hrm_model->federal_tax_info($tax_type, $employee_tax_column, $final, $tax_range, $user_id);

        if (isset($data[$tax_type][0]['employee']) && is_numeric($data[$tax_type][0]['employee'])) {
            $tax_employee = $data[$tax_type][0]['employee'];
            $tax_value    = round(($tax_employee / 100) * $final, 3);
        }

        // Calculate total unemployment
        $total_unemployment = $this->Hrm_model->total_unemployment($templ_name, $user_id);
        $total_unemployment_rounded = round($total_unemployment['unempltotal']);

        if (isset($data[$tax_type][0]['employer']) && is_numeric($data[$tax_type][0]['employer'])) {
            $tax_employer = $data[$tax_type][0]['employer'];

            // Set Cap Amount For Unemployment
            if ($total_unemployment_rounded <= $data[$tax_type][0]['details']) {
                $tax_value_employer = round(($tax_employer / 100) * $final, 3);
            } else {
                $tax_value_employer = 0; 
            }
        }

        $sum_of_country_tax = $this->Hrm_model->sum_of_country_tax($endDate, $templ_name, $timesheet_id, $user_id);

        if (!empty($sum_of_country_tax)) {
            $ytd['ytd_days']                        = $sum_of_country_tax[0]['ytd_days'] ?? 0;
            $ytd['ytd_salary']                      = $sum_of_country_tax[0]['ytd_salary'] ?? 0;
            $ytd['ytd_overtime_salary']             = $sum_of_country_tax[0]['ytd_overtime_salary'] ?? 0;
            $ytd['ytd_hours_only_overtime']         = $sum_of_country_tax[0]['ytd_hours_only_overtime'] ?? 0;
            $ytd['ytd_hours_excl_overtime']         = $sum_of_country_tax[0]['ytd_hours_excl_overtime'] ?? 0;
            $ytd['total_hours']                     = $sum_of_country_tax[0]['total_hours'] ?? 0;
            $ytd['ytd_hours_excl_overtime_in_time'] = $sum_of_country_tax[0]['ytd_hours_excl_overtime_in_time'] ?? 0;

            $data['t_s_tax'] = $sum_of_country_tax[0]['t_s_tax'] ?? 0;
            $data['t_m_tax'] = $sum_of_country_tax[0]['t_m_tax'] ?? 0;
            $data['t_f_tax'] = $sum_of_country_tax[0]['t_f_tax'] ?? 0;
            $data['t_u_tax'] = $sum_of_country_tax[0]['t_u_tax'] ?? 0;
        }

        return [
            'ytd'                => $ytd,
            'tax_data'           => $data,
            'tax_value'          => $tax_value,
            'tax_value_employer' => $tax_value_employer,
        ];
    }

    public function payroll_reports() {
        $this->load->model('Hrm_model');
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $setting_detail = $CI->Web_settings->retrieve_setting_editdata();
        $data['title']  = display('payroll_manage');
        $datainfo       = $this->Hrm_model->get_data_payslip();
        $emplinfo       = $this->Hrm_model->empl_data_info();
        $data           = array(
            'dataforpayslip' => $datainfo,
            'employee_info'  => $emplinfo,
            'setting_detail' => $setting_detail,
        );
        $content = $this->parser->parse('hr/payroll_manage_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function add_state() {
        $CI         = &get_instance();
        $state_name = $this->input->post('state_name');
        $userId     = $this->input->post('admin_company_id');
        $decodedId  = decodeBase64UrlParameter($userId);
        $companyId  = $this->input->post('adminId');
        $data       = array(
            'state'      => $state_name,
            'Type'       => 'State',
            'created_by' => $decodedId,
        );
        logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), '', '', $this->session->userdata('userName'), 'Federal Taxes', 'Human Resource', 'New State Added Successfully', 'Add', date('m-d-Y'));
        $this->db->insert('state_and_tax', $data);
        $this->session->set_userdata(array('message' => 'New State Added Successfully'));
        redirect(base_url('Chrm/payroll_setting?id=' . $userId . '&admin_id=' . $companyId));
    }
    public function add_state_tax() {
        $CI             = &get_instance();
        $tx             = $this->input->post('state_tax_name');
        $st_code        = explode("-", $tx);
        $state_code     = trim($st_code[1]);
        $selected_state = $this->input->post('selected_state');
        $user_id        = $this->input->post('admin_company_id');
        $decodedId      = decodeBase64UrlParameter($user_id);
        $companyId      = $this->input->post('adminId');
        $this->db->where('state', $selected_state);
        $this->db->set('tax', "CONCAT(tax,',','" . $tx . "')", FALSE);
        $this->db->update('state_and_tax');
        logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), '', '', $this->session->userdata('userName'), 'Federal Taxes', 'Human Resource', 'New Tax Has been assigned Successfully', 'Add', date('m-d-Y'));
        $sql1 = "UPDATE state_and_tax SET state_code = '$state_code', tax = TRIM(BOTH ',' FROM tax) WHERE state = '$selected_state' AND created_by = '$decodedId'";
        $this->db->query($sql1);
        $this->session->set_userdata(array('message' => 'New Tax Has been assigned Successfully'));
        redirect(base_url('Chrm/payroll_setting?id=' . $user_id . '&admin_id=' . $companyId));
    }
    public function add_designation_data() {
        $this->load->model('Hrm_model');
        $postData = $this->input->post('designation');
        $data     = $this->Hrm_model->designation_info($postData);
        echo json_encode($data);
    }
    public function add_office_loan() {
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $CI->load->model('Invoices');
        $CI->load->model('Settings');
        $data['person_list'] = $CI->Settings->office_loan_person();
        $setting_detail      = $CI->Web_settings->retrieve_setting_editdata();
        $bank_name           = $CI->db->select('bank_id,bank_name')
            ->from('bank_add')
            ->get()
            ->result_array();
        $data['bank_list'] = $CI->Web_settings->bank_list();
        $CI                = &get_instance();
        $paytype           = $CI->Invoices->payment_type();
        $noofpayment_type  = $CI->Invoices->noofpayment_type();
        $CI->load->model('Web_settings');
        $data['payment_typ']      = $paytype;
        $data['bank_name']        = $bank_name;
        $data['noofpayment_type'] = $noofpayment_type;
        $data['setting_detail']   = $setting_detail;
        $currency_details         = $CI->Web_settings->retrieve_setting_editdata();
        $data['title']            = display('add_office_loan');
        $data['currency']         = $currency_details[0]['currency'];
        $content                  = $this->parser->parse('hr/add_office_loan', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function add_expense_item() {
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $CI->load->model('Hrm_model');
        $currency_details       = $CI->Web_settings->retrieve_setting_editdata();
        $setting_detail         = $CI->Web_settings->retrieve_setting_editdata();
        $data['setting_detail'] = $setting_detail;
        $data['person_list']    = $CI->Hrm_model->employee_list();
        $data['title']          = display('expense_item_form');
        $data['currency']       = $currency_details[0]['currency'];
        $content                = $this->parser->parse('hr/expense_item_form', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function tax_list() {
        $data['title'] = display('tax_list');
        $content       = $this->parser->parse('hr/payroll_setting', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function payroll_setting() {
        $CI = &get_instance();
        $CI->load->model('Web_settings');
        $setting_detail                        = $CI->Web_settings->retrieve_setting_editdata();
        $data['timesheet_data_emp']            = $CI->Hrm_model->timesheet_data_emp();
        $data['setting_detail']                = $setting_detail;
        $data['states_list']                   = $CI->Hrm_model->getDatas('state_and_tax', '*', ['Type' => 'State', 'created_by' => $this->session->userdata('user_id')]);
        $data['city_list']                     = $CI->Hrm_model->getDatas('state_and_tax', '*', ['Type' => 'City', 'created_by' => $this->session->userdata('user_id')]);
        $data['county_list']                   = $CI->Hrm_model->getDatas('state_and_tax', '*', ['Type' => 'County', 'created_by' => $this->session->userdata('user_id')]);
        $data['title']                         = display('federal_taxes');
        $data['get_data_salespartner']         = $CI->Hrm_model->get_data_salespartner();
        $data['get_data_salespartner_another'] = $CI->Hrm_model->get_data_salespartner_another();
        $data['merged_data_salespartner']      = array_merge($data['get_data_salespartner'], $data['get_data_salespartner_another']);
        $data['state_selected']                = $CI->Hrm_model->getDatas('state_and_tax', '*', ['Status' => 1, 'created_by' => $this->session->userdata('user_id')]);
        $content                               = $this->parser->parse('hr/federal_taxes', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function formfl099nec($selectedValue = null) {
        $CI = &get_instance();
        $this->load->model('Hrm_model');
        $data['get_cominfo']            = $this->Hrm_model->get_company_info();
        $data['get_f1099nec_info']      = $this->Hrm_model->get_f1099nec_info($selectedValue);
        $data['choice']                 = $data['get_f1099nec_info'][0]['choice'];
        $data['no_salecommission']      = $this->Hrm_model->no_salecommission($selectedValue);
        $data['emp_yes_salecommission'] = $this->Hrm_model->emp_yes_salecommission($selectedValue);
        $data['sss']                    = $data['emp_yes_salecommission'][0]['emp_sc_amount'];
        $currency_details               = $CI->Web_settings->retrieve_setting_editdata();
        $data['currency']               = $currency_details[0]['currency'];
        $content                        = $CI->parser->parse('hr/fl099nec', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function delete_tax() {
        $tax   = $this->input->post('tax');
        $state = $this->input->post('state');
        $this->load->model('Hrm_model');
        logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), '', '', $this->session->userdata('userName'), 'State Taxes', 'Human Resource', 'State Taxes been deleted successfully', 'Delete', date('m-d-Y'));
        $this->Hrm_model->delete_tax($tax, $state);
        $this->session->set_flashdata('show', display('successfully_delete'));
        redirect("Chrm/payroll_setting");
    }
    public function citydelete_tax() {
        $citytax = $this->input->post('citytax');
        $city    = $this->input->post('city');
        $this->load->model('Hrm_model');
        $this->Hrm_model->citydelete_tax($citytax, $city);
        $this->session->set_flashdata('show', display('successfully_delete'));
    }
    public function countydelete_tax() {
        $countytax = $this->input->post('countytax');
        $county    = $this->input->post('county');
        $this->load->model('Hrm_model');
        $this->Hrm_model->countydelete_tax($countytax, $county);
        $this->session->set_flashdata('show', display('successfully_delete'));
        redirect("Chrm/payroll_setting");
    }
    public function getemployee_data() {
        $CI = &get_instance();
        $this->auth->check_admin_auth();
        $CI->load->model('Hrm_model');
        $value         = $this->input->post('value', TRUE);
        $customer_info = $CI->Hrm_model->getemp_data($value);
        echo json_encode($customer_info);
    }
public function add_state_taxes_detail($tax=null)
{
    $CI = & get_instance();
    $CI->load->model('Web_settings');
    $setting_detail = $CI->Web_settings->retrieve_setting_editdata();
    $data['setting_detail'] = $setting_detail;
    $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $parts = parse_url($url);
    $user_id      = isset($_GET['id']) ? $_GET['id'] : null;
    $decodedId      = decodeBase64UrlParameter($user_id);
    parse_str($parts['query'], $query);
    // Hourly Data
    $data['taxinfo'] = $this->db->select("*")->from('state_localtax')->where('tax',$query['tax'])->where('created_by',$decodedId)->get()->result_array();
    // Weekly Data
    $data['weekly_taxinfo'] = $this->db->select("*")->from('weekly_tax_info')->where('tax',$query['tax'])->where('created_by',$decodedId)->get()->result_array();
    // BiWeekly Data
    $data['biweekly_taxinfo'] = $this->db->select("*")->from('biweekly_tax_info')->where('tax',$query['tax'])->where('created_by',$decodedId)->get()->result_array();
    // Monthly Data
    $data['monthly_taxinfo'] = $this->db->select("*")->from('monthly_tax_info')->where('tax',$query['tax'])->where('created_by',$decodedId)->get()->result_array();
    $data['title'] = display('add_taxes_detail');
    $content = $this->parser->parse('hr/add_state_tax_detail', $data, true);
    $this->template->full_admin_html_view($content);
}
    // Federal Tax - Madhu
    public function add_taxes_detail() {
        $user_id                = $this->input->get('id');
        $company_id             = $this->input->get('admin_id');
        $decodedId              = decodeBase64UrlParameter($user_id);
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata($decodedId);
        $data['setting_detail'] = $setting_detail;
        $tax                    = $this->input->post('tax');
        $data['taxinfo']        = $this->Hrm_model->allFederaltaxes('Federal Income tax', $decodedId);
        $data['title']          = display('add_taxes_detail');
        $content                = $this->parser->parse('hr/add_taxes_detail', $data, true);
        $this->template->full_admin_html_view($content);
    }
    // Social Security Tax - Madhu
    public function socialsecurity_detail() {
        $user_id                = $this->input->get('id');
        $company_id             = $this->input->get('admin_id');
        $decodedId              = decodeBase64UrlParameter($user_id);
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata($decodedId);
        $data['setting_detail'] = $setting_detail;
        $data['taxinfo']        = $this->Hrm_model->allFederaltaxes('Social Security', $decodedId);
        $data['title']          = display('add_taxes_detail');
        $content                = $this->parser->parse('hr/social_security_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    // Medicare Tax - Madhu
    public function medicare_detail() {
        $user_id                = $this->input->get('id');
        $company_id             = $this->input->get('admin_id');
        $decodedId              = decodeBase64UrlParameter($user_id);
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata($decodedId);
        $data['setting_detail'] = $setting_detail;
        $data['taxinfo']        = $this->Hrm_model->allFederaltaxes('Medicare', $decodedId);
        $data['title']          = display('add_taxes_detail');
        $content                = $this->parser->parse('hr/medicare_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    // Federal Unemployment Tax - Madhu
    public function federalunemployment_detail() {
        $user_id                = $this->input->get('id');
        $company_id             = $this->input->get('admin_id');
        $decodedId              = decodeBase64UrlParameter($user_id);
        $setting_detail         = $this->Web_settings->retrieve_setting_editdata($decodedId);
        $data['taxinfo']        = $this->Hrm_model->allFederaltaxes('Federal unemployment', $decodedId);
        $data['title']          = display('add_taxes_detail');
        $data['setting_detail'] = $setting_detail;
        $content                = $this->parser->parse('hr/federalunemployment_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function add_timesheet() {
        $data['title'] = display('add_timesheet');
        $CI            = &get_instance();
        $this->load->model('Hrm_model');
        $CI->load->model('Web_settings');
        $setting_detail         = $CI->Web_settings->retrieve_setting_editdata();
        $data['employee_name']  = $this->Hrm_model->employee_name1();
        $data['payment_terms']  = $this->Hrm_model->get_payment_terms();
        $data['setting_detail'] = $setting_detail;
        $data['dailybreak']     = $this->Hrm_model->get_dailybreak();
        $data['duration']       = $this->Hrm_model->get_duration_data();
        $content                = $this->parser->parse('hr/add_timesheet', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function add_durat_info() {
        $CI = &get_instance();
        $CI->auth->check_admin_auth();
        $CI->load->model('Hrm_model');
        $postData = $this->input->post('duration_name');
        $data     = $this->Hrm_model->insert_duration_data($postData);
        echo json_encode($data);
    }
    public function add_adm_data() {
        $CI = &get_instance();
        $CI->auth->check_admin_auth();
        $CI->load->model('Hrm_model');
        $postData = $this->input->post('data_name');
        $postData = $this->input->post('data_adres');
        $data     = $this->Hrm_model->insert_adsrs_data($postData);
        echo json_encode($data);
    }
    public function insert_data_adsr() {
        $CI = &get_instance();
        $CI->auth->check_admin_auth();
        $CI->load->model('Hrm_model');
        $data = array(
            'adm_name'    => $this->input->post('adms_name', TRUE),
            'adm_address' => $this->input->post('address', TRUE),
            'create_by'   => $this->session->userdata('user_id'),
        );
        $data = $this->Hrm_model->insert_adsrs_data($data);
        echo json_encode($data);
    }
    public function add_city() {
        $city_name = $this->input->post('city_name');
        $userId    = $this->input->post('admin_company_id');
        $decodedId = decodeBase64UrlParameter($userId);
        $companyId = $this->input->post('adminId');
        $data      = array(
            'state'      => $city_name,
            'Type'       => 'City',
            'created_by' => $decodedId,
        );
        $this->db->insert('state_and_tax', $data);
        $this->session->set_userdata(array('message' => 'New City Added Successfully'));
        redirect(base_url('Chrm/payroll_setting?id=' . $userId . '&admin_id=' . $companyId));
    }
    public function add_city_state_tax() {
        $CI            = &get_instance();
        $selected_city = $this->input->post('selected_city');
        $citytax       = $this->input->post('city_tax_name');
        $userId        = $this->input->post('admin_company_id');
        $decodedId     = decodeBase64UrlParameter($userId);
        $companyId     = $this->input->post('adminId');
        $this->db->where('state', $selected_city);
        $this->db->set('tax', "CONCAT(tax,',','" . $citytax . "')", FALSE);
        $this->db->update('state_and_tax');
        $query = $this->db->get();
        $sql1  = "UPDATE state_and_tax
 SET tax = TRIM(BOTH ',' FROM tax)";
        $query1 = $this->db->query($sql1);
        $this->session->set_userdata(array('message' => 'New Tax Has been assigned Successfully'));
        redirect(base_url('Chrm/payroll_setting?id=' . $userId . '&admin_id=' . $companyId));
    }
    public function add_county_tax() {
        $CI              = &get_instance();
        $selected_county = $this->input->post('selected_county');
        $ctax            = $this->input->post('county_tax_name');
        $userId          = $this->input->post('admin_company_id');
        $decodedId       = decodeBase64UrlParameter($userId);
        $companyId       = $this->input->post('adminId');
        $this->db->where('state', $selected_county);
        $this->db->set('tax', "CONCAT(tax,',','" . $ctax . "')", FALSE);
        $this->db->update('state_and_tax');
        $query = $this->db->get();
        $sql1  = "UPDATE state_and_tax
SET tax = TRIM(BOTH ',' FROM tax)";
        $query1 = $this->db->query($sql1);
        $this->session->set_userdata(array('message' => 'New Tax Has been assigned Successfully'));
        redirect(base_url('Chrm/payroll_setting?id=' . $userId . '&admin_id=' . $companyId));
    }
    public function add_county() {
        $CI        = &get_instance();
        $county    = $this->input->post('county');
        $userId    = $this->input->post('admin_company_id');
        $decodedId = decodeBase64UrlParameter($userId);
        $companyId = $this->input->post('adminId');
        $data      = array(
            'state'      => $county,
            'created_by' => $decodedId,
            'Type'       => 'County',
        );
        $this->db->insert('state_and_tax', $data);
        $this->session->set_userdata(array('message' => 'New County Added Successfully'));
        redirect(base_url('Chrm/payroll_setting?id=' . $userId . '&admin_id=' . $companyId));
    }
    //Designation form
    public function add_designation() {
        $data['title'] = display('add_designation');
        $content       = $this->parser->parse('hr/employee_type', $data, true);
        $this->template->full_admin_html_view($content);
    }
    // create designation
    public function create_designation() {
        $this->form_validation->set_rules('designation', display('designation'), 'required|max_length[100]');
        $this->form_validation->set_rules('details', display('details'), 'max_length[250]');
        #-------------------------------#
        if ($this->form_validation->run()) {
            $postData = [
                'id'          => $this->input->post('id', true),
                'designation' => $this->input->post('designation', true),
                'details'     => $this->input->post('details', true),
            ];
            if (empty($this->input->post('id', true))) {
                if ($this->Hrm_model->create_designation($postData)) {
                    $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                    $this->session->set_flashdata('error_message', display('please_try_again'));
                }
            } else {
                if ($this->Hrm_model->update_designation($postData)) {
                    $this->session->set_flashdata('message', display('successfully_updated'));
                } else {
                    $this->session->set_flashdata('error_message', display('please_try_again'));
                }
            }
            redirect("Chrm/manage_designation");
        }
        redirect("Chrm/add_designation");
    }
    //Manage designation
    public function manage_designation() {
        $this->load->model('Hrm_model');
        $data['title']            = display('manage_designation');
        $data['designation_list'] = $this->Hrm_model->designation_list();
        $content                  = $this->parser->parse('hr/designation_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    //designation Update Form
    public function designation_update_form($id) {
        $this->load->model('Hrm_model');
        $data['title']            = display('designation_update_form');
        $data['designation_data'] = $this->Hrm_model->designation_editdata($id);
        $content                  = $this->parser->parse('hr/employee_type', $data, true);
        $this->template->full_admin_html_view($content);
    }
    // designation delete
    public function designation_delete($id) {
        $this->load->model('Hrm_model');
        $this->Hrm_model->delete_designation($id);
        $this->session->set_userdata(array('message' => display('successfully_delete')));
        redirect("Chrm/manage_designation");
    }
    public function add_employee() {
        $this->auth->check_admin_auth();
        $this->CI->load->model('Web_settings');
        $this->load->model('Hrm_model');
        $setting_detail            = $this->CI->Web_settings->retrieve_setting_editdata();
        $country_data              = $this->Hrm_model->getDatas('country', '*', ['id !=' => '']);
        $curn_info_default         = $this->Hrm_model->getDatas('currency_tbl', '*', ['icon' => $setting_detail[0]['currency']]);
        $data['title']             = display('add_employee');
        $data['setting_detail']    = $setting_detail;
        $data['curn_info_default'] = (!empty($curn_info_default[0]['currency_name']) ? $curn_info_default[0]['currency_name'] : '');
        $data['country_data']      = (!empty($country_data) ? $country_data : '');
        $data['currency']          = $setting_detail[0]['currency'];
        $data['paytype']           = $this->Hrm_model->paytype_dropdown();
        $data['citytx']            = $this->Hrm_model->city_tax_dropdown();
        $data['cty_tax']           = $this->Hrm_model->city_tax();
        $data['desig']             = $this->Hrm_model->designation_dropdown();
        $data['get_info_city_tax']   = $this->Hrm_model->get_info_city_tax();
        $data['get_info_county_tax'] = $this->Hrm_model->get_info_county_tax();
        $data['state_tx']            = $this->Hrm_model->state_tax();
        $data['payroll_data']        = $this->Hrm_model->getDatas('payroll_type', '*', ['created_by' => $this->session->userdata('user_id')]);
        $data['bank_data']           = $this->Hrm_model->getDatas('bank_add', '*', ['created_by' => $this->session->userdata('user_id')]);
        $data['emp_data']            = $this->Hrm_model->getDatas('employee_type', '*', ['created_by' => $this->session->userdata('user_id')]);
        $content                     = $this->parser->parse('hr/employee_form', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function salespartner_create() {
        if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
            $no_files = count($_FILES["files"]['name']);
            for ($i = 0; $i < $no_files; $i++) {
                if ($_FILES["files"]["error"][$i] > 0) {
                    echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
                } else {
                    move_uploaded_file(
                        $_FILES["files"]["tmp_name"][$i],
                        "assets/uploads/salespartner/" . $_FILES["files"]["name"][$i]
                    );
                    $images[]     = $_FILES["files"]["name"][$i];
                    $insertImages = implode(', ', $images);
                }
            }
            if ($_FILES['profile_image']['name']) {
                $config['upload_path']   = 'assets/uploads/profile/salespartner/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|GIF|JPG|PNG';
                $config['encrypt_name']  = TRUE;
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('profile_image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_userdata(array('error_message' => $this->upload->display_errors()));
                    redirect(base_url('Cweb_setting'));
                } else {
                    $data                     = $this->upload->data();
                    $profile_image            = $data['file_name'];
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $profile_image;
                    $config['create_thumb']   = false;
                    $config['maintain_ratio'] = TRUE;
                    $config['width']          = 200;
                    $config['height']         = 200;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $profile_image = $profile_image;
                }
            }
        } else {
            if ($_FILES['profile_image']['name']) {
                $config['upload_path']   = 'assets/uploads/profile';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|GIF|JPG|PNG';
                $config['encrypt_name']  = TRUE;
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('profile_image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_userdata(array('error_message' => $this->upload->display_errors()));
                    redirect(base_url('Cweb_setting'));
                } else {
                    $data                     = $this->upload->data();
                    $profile_image            = $data['file_name'];
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $profile_image;
                    $config['create_thumb']   = false;
                    $config['maintain_ratio'] = TRUE;
                    $config['width']          = 200;
                    $config['height']         = 200;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $profile_image = $profile_image;
                }
            }
        }
        $data_empolyee['last_name']                   = $this->input->post('last_name');
        $data_empolyee['designation']                 = $this->input->post('designation');
        $data_empolyee['first_name']                  = $this->input->post('first_name');
        $data_empolyee["middle_name"]                 = $this->input->post("middle_name");
        $data_empolyee['phone']                       = $this->input->post('phone');
        $data_empolyee['files']                       = $insertImages;
        $data_empolyee['employee_tax']                = $this->input->post('emp_tax_detail');
        $data_empolyee['employee_type']               = $this->input->post('employee_type');
        $data_empolyee['salesbusiness_name']          = $this->input->post('salesbusiness_name');
        $data_empolyee['federalidentificationnumber'] = $this->input->post('federalidentificationnumber');
        $data_empolyee['federaltaxclassification']    = $this->input->post('federaltaxclassification');
        $data_empolyee['email']                       = $this->input->post('email');
        $data_empolyee['sc']                          = $this->input->post('sc');
        $data_empolyee['address_line_1']              = $this->input->post('address_line_1');
        $data_empolyee['address_line_2']              = $this->input->post('address_line_2');
        $data_empolyee['social_security_number']      = $this->input->post('ssn');
        $data_empolyee['routing_number']              = $this->input->post('routing_number');
        $data_empolyee['sales_partner']               = 'Sales_Partner';
        $data_empolyee['choice']                      = $this->input->post('choice');
        $data_empolyee['account_number']              = $this->input->post('account_number');
        $data_empolyee['bank_name']                   = $this->input->post('bank_name');
        $data_empolyee['country']                     = $this->input->post('country');
        $data_empolyee['city']                        = $this->input->post('city');
        $data_empolyee['zip']                         = $this->input->post('zip');
        $data_empolyee['state']                       = $this->input->post('state');
        $data_empolyee['emergencycontact']            = $this->input->post('emergencycontact');
        $data_empolyee['emergencycontactnum']         = $this->input->post('emergencycontactnum');
        $data_empolyee['withholding_tax']             = $this->input->post('withholding_tax');
        $data_empolyee['last_name']                   = $this->input->post('last_name');
        $data_empolyee['profile_image']               = $profile_image;
        $data_empolyee['create_by']                   = $this->session->userdata('user_id');
        $data_empolyee['e_type']                      = 2;
        $data_empolyee['sp_withholding']              = $this->input->post('choice');
        // State Tax Information
        $state_tax        = $this->input->post('state_tax');
        $living_state_tax = $this->input->post('living_state_tax');
        if ($state_tax == $living_state_tax) {
            $data_empolyee['working_state_tax'] = $state_tax;
        } else {
            $data_empolyee['working_state_tax'] = $state_tax;
            $data_empolyee['living_state_tax']  = $living_state_tax;
        }
        // Local (City) Tax Information
        $city_tax        = $this->input->post('city_tax');
        $living_city_tax = $this->input->post('living_city_tax');
        if ($city_tax == $living_city_tax) {
            $data_empolyee['working_city_tax'] = $city_tax;
        } else {
            $data_empolyee['working_city_tax'] = $city_tax;
            $data_empolyee['living_city_tax']  = $living_city_tax;
        }
        //  City Tax Information
        $county_tax        = $this->input->post('county_tax');
        $living_county_tax = $this->input->post('living_county_tax');
        if ($county_tax == $living_county_tax) {
            $data_empolyee['working_county_tax'] = $county_tax;
        } else {
            $data_empolyee['working_county_tax'] = $county_tax;
            $data_empolyee['living_county_tax']  = $living_county_tax;
        }
        // Other Tax Info
        $other_working_tax = $this->input->post('other_working_tax');
        $other_living_tax  = $this->input->post('other_living_tax');
        if ($county_tax == $county_tax) {
            $data_empolyee['working_other_tax'] = $other_working_tax;
        } else {
            $data_empolyee['working_other_tax'] = $other_working_tax;
            $data_empolyee['living_other_tax']  = $other_living_tax;
        }
        $living_state_tax                   = $this->input->post('living_state_tax');
        $data_empolyee['working_state_tax'] = $state_tax;
        $data_empolyee['living_state_tax']  = $living_state_tax;
        // Local (City) Tax Information
        $city_tax                          = $this->input->post('city_tax');
        $living_city_tax                   = $this->input->post('living_city_tax');
        $data_empolyee['working_city_tax'] = $city_tax;
        $data_empolyee['living_city_tax']  = $living_city_tax;
        //  City Tax Information
        $county_tax                          = $this->input->post('county_tax');
        $living_county_tax                   = $this->input->post('living_county_tax');
        $data_empolyee['working_county_tax'] = $county_tax;
        $data_empolyee['living_county_tax']  = $living_county_tax;
        // Other Tax Info
        $other_working_tax                  = $this->input->post('other_working_tax');
        $other_living_tax                   = $this->input->post('other_living_tax');
        $data_empolyee['working_other_tax'] = $other_working_tax;
        $data_empolyee['living_other_tax']  = $other_living_tax;
        logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), '', '', $this->session->userdata('userName'), 'Add Sales Partner', 'Human Resource', 'Sales Partner has been Added successfully', 'Add', date('m-d-Y'));
        $this->db->insert('employee_history', $data_empolyee);
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect(base_url("Chrm/manage_employee?id=" . $this->input->post('company_id') . "&admin_id=" . $this->input->post('admin_id')));
    }
    public function employee_create() {
        $decodedId = decodeBase64UrlParameter($this->input->post('company_id'));
        $admin_id  = decodeBase64UrlParameter($this->input->post('admin_id'));

        if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
            $no_files = count($_FILES["files"]['name']);
            for ($i = 0; $i < $no_files; $i++) {
                if ($_FILES["files"]["error"][$i] > 0) {
                    echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
                } else {
                    move_uploaded_file(
                        $_FILES["files"]["tmp_name"][$i],
                        "assets/uploads/employeedetails/" . $_FILES["files"]["name"][$i]
                    );
                    $images[]     = $_FILES["files"]["name"][$i];
                    $insertImages = implode(', ', $images);
                }
            }
            if ($_FILES['profile_image']['name']) {
                $config['upload_path']   = 'assets/uploads/profile/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|GIF|JPG|PNG';
                $config['encrypt_name']  = TRUE;
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('profile_image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_userdata(array('error_message' => $this->upload->display_errors()));
                    redirect(base_url('Cweb_setting'));
                } else {
                    $data                     = $this->upload->data();
                    $profile_image            = $data['file_name'];
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $profile_image;
                    $config['create_thumb']   = false;
                    $config['maintain_ratio'] = TRUE;
                    $config['width']          = 200;
                    $config['height']         = 200;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $profile_image = $profile_image;
                }
            }
        } else {
            if ($_FILES['profile_image']['name']) {
                $config['upload_path']   = 'assets/uploads/profile/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|GIF|JPG|PNG';
                $config['encrypt_name']  = TRUE;
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('profile_image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_userdata(array('error_message' => $this->upload->display_errors()));
                    redirect(base_url('Cweb_setting'));
                } else {
                    $data                     = $this->upload->data();
                    $profile_image            = $data['file_name'];
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $profile_image;
                    $config['create_thumb']   = false;
                    $config['maintain_ratio'] = TRUE;
                    $config['width']          = 200;
                    $config['height']         = 200;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $profile_image = $profile_image;
                }
            }
        }

        $data_empolyee['last_name']              = $this->input->post('last_name');
        $data_empolyee['designation']            = $this->input->post('designation');
        $data_empolyee['first_name']             = $this->input->post('first_name');
        $data_empolyee["middle_name"]            = $this->input->post("middle_name");
        $data_empolyee['phone']                  = $this->input->post('phone');
        $data_empolyee['files']                  = $insertImages;
        $data_empolyee['employee_tax']           = $this->input->post('emp_tax_detail');
        $data_empolyee['employee_type']          = $this->input->post('employee_type');
        $data_empolyee['rate_type']              = $this->input->post('paytype');
        $data_empolyee['payroll_type']           = $this->input->post('payroll_type');
        $data_empolyee['payroll_freq']           = $this->input->post('payroll_freq');
        $data_empolyee['choice']                 = $this->input->post('choice');
        $data_empolyee['email']                  = $this->input->post('email');
        $data_empolyee['hrate']                  = $this->input->post('hrate');
        $data_empolyee['sc']                     = $this->input->post('sc');
        $data_empolyee['address_line_1']         = $this->input->post('address_line_1');
        $data_empolyee['address_line_2']         = $this->input->post('address_line_2');
        $data_empolyee['social_security_number'] = $this->input->post('ssn');
        $data_empolyee['routing_number']         = $this->input->post('routing_number');
        $data_empolyee['account_number']         = $this->input->post('account_number');
        $data_empolyee['bank_name']              = $this->input->post('bank_name');
        $data_empolyee['country']                = $this->input->post('country');
        $data_empolyee['city']                   = $this->input->post('city');
        $data_empolyee['zip']                    = $this->input->post('zip');
        $data_empolyee['state']                  = $this->input->post('state');
        $data_empolyee['emergencycontact']       = $this->input->post('emergencycontact');
        $data_empolyee['emergencycontactnum']    = $this->input->post('emergencycontactnum');
        $data_empolyee['withholding_tax']        = $this->input->post('withholding_tax');
        $data_empolyee['last_name']              = $this->input->post('last_name');
        $data_empolyee['profile_image']          = $profile_image;
        $data_empolyee['create_by']              = $decodedId;
        $data_empolyee['e_type']                 = 1;
        // State Tax Information
        $state_tax        = $this->input->post('state_tax');
        $living_state_tax = $this->input->post('living_state_tax');
        if ($state_tax == $living_state_tax) {
            $data_empolyee['working_state_tax'] = $state_tax;
        } else {
            $data_empolyee['working_state_tax'] = $state_tax;
            $data_empolyee['living_state_tax']  = $living_state_tax;
        }
        // Local (City) Tax Information
        $city_tax        = $this->input->post('city_tax');
        $living_city_tax = $this->input->post('living_city_tax');
        if ($city_tax == $living_city_tax) {
            $data_empolyee['working_city_tax'] = $city_tax;
        } else {
            $data_empolyee['working_city_tax'] = $city_tax;
            $data_empolyee['living_city_tax']  = $living_city_tax;
        }
        //  City Tax Information
        $county_tax        = $this->input->post('county_tax');
        $living_county_tax = $this->input->post('living_county_tax');
        if ($county_tax == $living_county_tax) {
            $data_empolyee['working_county_tax'] = $county_tax;
        } else {
            $data_empolyee['working_county_tax'] = $county_tax;
            $data_empolyee['living_county_tax']  = $living_county_tax;
        }
        // Other Tax Info
        $other_working_tax = $this->input->post('other_working_tax');
        $other_living_tax  = $this->input->post('other_living_tax');
        if ($county_tax == $county_tax) {
            $data_empolyee['working_other_tax'] = $other_working_tax;
        } else {
            $data_empolyee['working_other_tax'] = $other_working_tax;
            $data_empolyee['living_other_tax']  = $other_living_tax;
        }
        $living_state_tax                   = $this->input->post('living_state_tax');
        $data_empolyee['working_state_tax'] = $state_tax;
        $data_empolyee['living_state_tax']  = $living_state_tax;
        // Local (City) Tax Information
        $city_tax                          = $this->input->post('city_tax');
        $living_city_tax                   = $this->input->post('living_city_tax');
        $data_empolyee['working_city_tax'] = $city_tax;
        $data_empolyee['living_city_tax']  = $living_city_tax;
        //  City Tax Information
        $county_tax                          = $this->input->post('county_tax');
        $living_county_tax                   = $this->input->post('living_county_tax');
        $data_empolyee['working_county_tax'] = $county_tax;
        $data_empolyee['living_county_tax']  = $living_county_tax;
        // Other Tax Info
        $other_working_tax                  = $this->input->post('other_working_tax');
        $other_living_tax                   = $this->input->post('other_living_tax');
        $data_empolyee['working_other_tax'] = $other_working_tax;
        $data_empolyee['living_other_tax']  = $other_living_tax;
        logEntry($this->session->userdata('user_id'), $this->session->userdata('unique_id'), $this->session->userdata('userName'), 'Add Employee', '', '', 'Human Resource', 'Employee Added Successfully', 'Add', date('m-d-Y'));
        $this->db->insert('employee_history', $data_empolyee);
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect(base_url("Chrm/manage_employee?id=" . $this->input->post('company_id') . "&admin_id=" . $this->input->post('admin_id')));
    }
    public function manage_employee() {
        $data['id']                = $encodedId                = isset($_GET['id']) ? $_GET['id'] : '';
        $data['admin_id']          = isset($_GET['admin_id']) ? $_GET['admin_id'] : '';
        $decodedId                 = decodeBase64UrlParameter($encodedId);
        $data['title']             = display('manage_employee');
        $data['employee_list']     = $this->Hrm_model->employee_list($decodedId);
        $data['employee_data_get'] = $this->Hrm_model->employee_data_get($decodedId);
        $data['setting_detail']    = $this->Web_settings->retrieve_setting_editdata($decodedId);
        $content                   = $this->parser->parse('hr/employee_list', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function employeeListdatatable() {
        $limit          = $this->input->post("length");
        $start          = $this->input->post("start");
        $search         = $this->input->post("search")["value"];
        $orderField     = $this->input->post("columns")[$this->input->post("order")[1]["column"]]["data"];
        $orderDirection = $this->input->post("order")[0]["dir"];
        $items          = $this->Hrm_model->getEmployeeListdata($limit, $start, $orderField, $orderDirection, $search);
        $totalItems     = $this->Hrm_model->getTotalEmployeeListdata($search);
        $data           = [];
        $i              = $start + 1;
        $edit           = "";
        $delete         = "";
        foreach ($items as $item) {
            $user     = '<a href="' . base_url("Chrm/employee_details/" . $item["id"]) . '" class="btnclr btn btn-sm" style="background-color:#424f5c; margin-right: 5px;"><i class="fa fa-user" aria-hidden="true"></i></a>';
            $download = '<a href="' . base_url("Chrm/timesheed_inserted_data/" . $item["id"] . "/emp_data") .
                '" class="btnclr btn btn-sm" style="background-color:#424f5c; margin-right: 5px;"><i class="fa fa-download" aria-hidden="true"></i></a>';
            $edit =
            '<a href="' . base_url("Chrm/employee_update_form/" . $item["id"]) .
                '" class="btnclr btn btn-sm" style="background-color:#424f5c; margin-right: 5px;"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            $delete = '<a onClick=deleteEmployeedata(' . $item["id"] . ') class="btnclr btn btn-sm" style="background-color:#424f5c; margin-right: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>';
            $row    = [
                "id"                     => $i,
                "first_name"             => $item["first_name"] . ' ' . $item["middle_name"] . ' ' . $item["last_name"],
                "designation"            => $item["designation"],
                "phone"                  => $item["phone"],
                "email"                  => $item['email'],
                "blood_group"            => $item['blood_group'],
                "social_security_number" => $item['social_security_number'],
                "routing_number"         => $item['routing_number'],
                "employee_tax"           => $item['employee_tax'],
                "action"                 => $user . $download . $edit . $delete,
            ];
            $data[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
    // Manage Employee Index  - hr
    public function getEmployeeDatas() {
        $encodedId      = isset($_GET['id']) ? $_GET['id'] : null;
        $encodedAdmin   = isset($_GET['admin_id']) ? $_GET['admin_id'] : null;
        $decodeAdmin    = decodeBase64UrlParameter($encodedAdmin);
        $decodedId      = decodeBase64UrlParameter($encodedId);
        $limit          = $this->input->post('length');
        $start          = $this->input->post('start');
        $search         = $this->input->post('search')['value'];
        $orderField     = 'e.' . $this->input->post('columns')[$this->input->post('order')[0]['column']]['data'];
        $orderDirection = $this->input->post('order')[0]['dir'];
        $totalItems     = $this->Hrm_model->getTotalEmployee($search, $decodedId);
        $items          = $this->Hrm_model->getPaginatedEmployee($limit, $start, $orderField, $orderDirection, $search, $decodedId);
        $data           = [];
        $i              = $start + 1;
        foreach ($items as $item) {
            $profile = '<a href="' . base_url('Chrm/employee_details?id=' . $encodedId . '&admin_id=' . $encodedAdmin . '&employee=' . $item['id']) . '" class="btnclr btn m-b-5 m-r-2"><i class="fa fa-user"></i></a>';
            $empinv  = '<a href="' . base_url('Chrm/timesheed_inserted_data?id=' . $encodedId . '&admin_id=' . $encodedAdmin . '&employee=' . $item['id'] . '&type=emp_data') . '" class="btnclr btn m-b-5 m-r-2"><i class="fa fa-download" aria-hidden="true"></i></a>';
            $edit    = '<a href="' . base_url('Chrm/employee_update_form?id=' . $encodedId . '&admin_id=' . $encodedAdmin . '&employee=' . $item['id']) . '" class="btnclr btn m-b-5 m-r-2" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            $delete  = '<a href="' . base_url('Chrm/employee_delete?id=' . $encodedId . '&admin_id=' . $encodedAdmin . '&employee=' . $item['id']) . '" class="btnclr btn" style="margin-bottom: 5px;"  onclick="return confirm(\'' . display('are_you_sure') . '\')" data-toggle="tooltip" data-placement="right" title="' . display('delete') . '"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
            $row     = [
                "id"                     => $i,
                "first_name"             => $item['first_name'] . ' ' . $item['middle_name'] . ' ' . $item['last_name'],
                "designation"            => $item['des_name'],
                "phone"                  => $item['phone'],
                "email"                  => $item['email'],
                "social_security_number" => $item['social_security_number'],
                "employee_type"          => $item['employee_type'],
                "payroll_type"           => $item['payroll_type'],
                'created_admin'          => $decodeAdmin,
                "routing_number"         => $item['routing_number'],
                "account_number"         => $item['account_number'],
                "employee_tax"           => $item['employee_tax'],
                'action'                 => $profile . $empinv . $edit . $delete,
            ];
            $data[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post('draw'),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
    public function form1099nec() {
        $CI = &get_instance();
        $this->load->model("Hrm_model");
        $data = array(
            'title' => '1099 NECForm',
        );
        $content = $CI->parser->parse("hr/1099necform", $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function w4form() {
        $this->load->model("Hrm_model");
        $data = array(
            'id'       => $_GET['id'],
            'admin_id' => $_GET['admin_id'],
            'title'    => 'w4form',
            'c_name'   => $this->Hrm_model->getDatas('company_information', '*', ['create_by' => $this->session->userdata('user_id')]),
        );
        $content = $this->CI->parser->parse("hr/w4_form", $data, true);
        $this->template->full_admin_html_view($content);
    }
// w9 Form
    public function w9form() {
        $data = array(
            'id'       => $_GET['id'],
            'admin_id' => $_GET['admin_id'],
            'title'    => 'w9form',
        );
        $content = $this->CI->parser->parse("hr/w9_form", $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function employee_details() {
        $this->CI->load->model('Web_settings');
        $this->load->model('Hrm_model');
        list($user_id, $company_id) = array_map('decodeBase64UrlParameter', [$_GET['id'], $_GET['admin_id']]);
        $emp_id                     = !empty($_GET['employee']) ? $_GET['employee'] : 0;
        $data['setting_detail']     = $this->CI->Web_settings->retrieve_setting_editdata();
        $data['title']              = display('employee_update');
        $data['row']                = $this->Hrm_model->employee_detl($emp_id);
        $content                    = $this->parser->parse('hr/resumepdf', $data, true);
        $this->template->full_admin_html_view($content);
    }
    // create employee
    public function create_employee() {
        $this->load->model('Hrm_model');
        $this->form_validation->set_rules('first_name', display('first_name'), 'required|max_length[100]');
        $this->form_validation->set_rules('last_name', display('last_name'), 'required|max_length[100]');
        $this->form_validation->set_rules('designation', display('designation'), 'required|max_length[100]');
        $this->form_validation->set_rules('phone', display('phone'), 'max_length[20]');
        $this->form_validation->set_rules('employee_type', 'Employee Type', 'required');
        $this->form_validation->set_rules('emp_tax_detail', 'Employee Tax Detail', 'required');
        $this->form_validation->set_rules('in_department', 'In Department', 'required');
        if ($this->form_validation->run()) {
            if ($_FILES['image']['name']) {
                $config['upload_path']   = 'assets/images/employee/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']      = "*";
                $config['max_width']     = "*";
                $config['max_height']    = "*";
                $config['encrypt_name']  = TRUE;
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_userdata(array('error_message' => $this->upload->display_errors()));
                } else {
                    $image     = $this->upload->data();
                    $image_url = base_url() . "assets/images/employee/" . $image['file_name'];
                }
            }
            $postData = [
                'first_name'             => $this->input->post('first_name', true),
                'last_name'              => $this->input->post('last_name', true),
                'designation'            => $this->input->post('designation', true),
                'phone'                  => $this->input->post('phone', true),
                'files'                  => $image_url,
                'rate_type'              => $this->input->post('rate_type', true),
                'payroll_type'           => $this->input->post('payroll_type', true),
                'cty_tax'                => $this->input->post('citytx', true),
                'email'                  => $this->input->post('email', true),
                'hrate'                  => $this->input->post('hrate', true),
                'address_line_1'         => $this->input->post('address_line_1', true),
                'address_line_2'         => $this->input->post('address_line_2', true),
                'state_local_tax'        => $this->input->post('state_local_tax', true),
                'local_tax'              => $this->input->post('local_tax', true),
                'state_tx'               => $this->input->post('state_tx', true),
                'social_security_number' => $this->input->post('social_security_number', true),
                'routing_number'         => $this->input->post('routing_number', true),
                'country'                => $this->input->post('country', true),
                'city'                   => $this->input->post('city', true),
                'zip'                    => $this->input->post('zip', true),
            ];
            if ($this->Hrm_model->create_employee($postData)) {
                $this->session->set_flashdata('message', display('save_successfully'));
                redirect("Chrm/manage_employee");
            } else {
                $this->session->set_flashdata('error_message', display('please_try_again'));
                redirect("Chrm/manage_employee");
            }
        } else {
            echo validation_errors();
        }
    }
    public function w2Form($id = null) {
        if ($id) {
        }
        $employee_ids = $this->input->post('employee_ids');
        $CI           = &get_instance();
        $this->load->model('Hrm_model');
        $this->load->model('Web_settings');
        $currency_details   = $CI->Web_settings->retrieve_setting_editdata();
        $curn_info_default  = $CI->db->select('*')->from('currency_tbl')->where('icon', $currency_details[0]['currency'])->get()->result_array();
        $employee_details   = $this->Hrm_model->employeeDetailsdata($id);
        $data['get_cdata']  = $this->Hrm_model->get_employer_federaltax();
        $get_cominfo        = $this->Hrm_model->get_company_info();
        $fed_tax            = $this->Hrm_model->getoveralltaxdata($id);
        $get_payslip_info   = $this->Hrm_model->w2get_payslip_info($id);
        $state_taxtype      = $this->Hrm_model->tax_statecode_info($id);
        $other_tx1          = $this->Hrm_model->getother_tax($id);
        $get_payslipalldata = $this->Hrm_model->w2get_payslip_alldata($id);
        $state_tax          = $this->Hrm_model->w2total_state_tax($id);
        $state_taxworking   = $this->Hrm_model->w2totalstatetaxworking($id);
        $county_tax         = $this->Hrm_model->getcounty_tax($id);
        $local_tax          = $this->Hrm_model->w2total_local_tax($id);
        $livinglocaldata    = $this->Hrm_model->w2total_livinglocaldata($id);
        $gettaxother_info   = $this->Hrm_model->gettaxother_info($id);
        $company_details    = $CI->db->select('*')->from('company_information')->where('company_id', $this->session->userdata('user_id'))->get()->result_array();
        $data               = array(
            'title'             => 'W2 Form',
            'getlocation'       => $get_cominfo,
            'gettaxdata'        => $fed_tax,
            'curn_info_default' => $curn_info_default[0]['currency_name'],
            'currency'          => $currency_details[0]['currency'],
            'other_tx'          => $other_tx1,
            'countyTax'         => $county_tax,
            'stateTax'          => $state_tax,
            'e_details'         => $employee_details,
            'stateworkingtax'   => $state_taxworking,
            'localTax'          => $local_tax,
            'StatetaxType'      => $state_taxtype,
            'c_details'         => $company_details,
            'get_payslip_info'  => $get_payslip_info,
            'livinglocaldata'   => $livinglocaldata,
            'gettaxother_info'  => $gettaxother_info,
        );
        $content = $CI->parser->parse('hr/w2_taxform', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function formw3Form() {
        $CI = &get_instance();
        $this->load->model('Hrm_model');
        $get_cominfo             = $this->Hrm_model->get_company_info();
        $get_payslip_info        = $this->Hrm_model->get_payslip_info();
        $total_state_tax         = $this->Hrm_model->total_state_tax();
        $get_sc_info             = $this->Hrm_model->get_sc_info();
        $sum_of_weekly_array     = $this->Hrm_model->sum_of_weekly();
        $sum_of_hourly_array     = $this->Hrm_model->sum_of_hourly();
        $sum_of_biweekly_array   = $this->Hrm_model->sum_of_biweekly();
        $sum_of_monthly_array    = $this->Hrm_model->sum_of_monthly();
        $sum_of_weekly           = !empty($sum_of_weekly_array) ? $sum_of_weekly_array[0]['weekly'] : 0;
        $sum_of_hourly           = !empty($sum_of_hourly_array) ? $sum_of_hourly_array[0]['amount'] : 0;
        $sum_of_biweekly         = !empty($sum_of_biweekly_array) ? $sum_of_biweekly_array[0]['biweekly'] : 0;
        $sum_of_monthly          = !empty($sum_of_monthly_array) ? $sum_of_monthly_array[0]['monthly'] : 0;
        $total_sum               = $sum_of_weekly + $sum_of_hourly + $sum_of_biweekly + $sum_of_monthly;
        $total_local_tax         = $this->Hrm_model->total_local_tax();
        $employeer_details       = $this->Hrm_model->employeerDetailsdata();
        $get_employer_federaltax = $this->Hrm_model->get_employer_federaltax();
        $get_total_customersData = $this->Hrm_model->get_total_customersData();
        $data                    = array(
            'title'                   => 'W3 Form',
            'get_cominfo'             => $get_cominfo,
            'get_payslip_info'        => $get_payslip_info,
            'employeer'               => $employeer_details,
            'total_state_tax'         => $total_sum,
            'total_local_tax'         => $total_local_tax,
            'get_employer_federaltax' => $get_employer_federaltax,
            'get_total_customersData' => $get_total_customersData,
            'get_sc_info'             => $get_sc_info,
        );
        $content = $CI->parser->parse('hr/w3_taxform', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function sc_cnt() {
        $CI = &get_instance();
        $this->load->model('Hrm_model');
        $employeeId  = $this->input->post('employeeId', TRUE);
        $reportrange = $this->input->post('reportrange', TRUE);
        $data['sc']  = $this->Hrm_model->sc_info_count($employeeId, $reportrange);
        echo json_encode($data['sc']);
    }
    public function form940Form() {
        $CI = &get_instance();
        $this->load->model('Hrm_model');
        $data['get_cominfo']       = $this->Hrm_model->get_company_info();
        $data['get_cdata']         = $this->Hrm_model->get_employer_federaltax();
        $data['get_sc_info']       = $this->Hrm_model->get_sc_info();
        $data['get_paytotal']      = $this->Hrm_model->get_paytotal();
        $data['get_userlist']      = $CI->db->select('*')->from('users')->where('user_id', $this->session->userdata('user_id'))->get()->result_array();
        $data['amountGreaterThan'] = $this->Hrm_model->f940_excess_emp();
        $totalAmount               = 0;
        if ($data['amountGreaterThan']) {
            foreach ($data['amountGreaterThan'] as $row) {
                $totalAmount += $row['totalAmount'];
            }
            $value = $totalAmount / 2;
            if (!empty($value)) {
                $final_amount = $value - 7000;
            } else {
                $final_amount = 0;
            }
            if (!empty($final_amount)) {
                $totalAmount = $final_amount;
            }
        }
        $data = array(
            'title'             => '940 Form',
            'get_cominfo'       => $data['get_cominfo'],
            'get_cdata'         => $data['get_cdata'],
            'get_paytotal'      => $data['get_paytotal'],
            'get_userlist'      => $data['get_userlist'],
            'amountGreaterThan' => $data['amountGreaterThan'],
            'get_sc_info'       => $data['get_sc_info'],
            'amt'               => $totalAmount,
        );
        $content = $CI->parser->parse('hr/f940', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function form941Form($selectedValue = null) {
        $CI = &get_instance();
        $this->load->model('Hrm_model');
        $data['get_cdata']   = $this->Hrm_model->get_employer_federaltax();
        $data['get_cominfo'] = $this->Hrm_model->get_company_info();
        $data['fed_tax']     = $this->Hrm_model->social_tax();
        $data['tat']         = $this->Hrm_model->so_total_amount($selectedValue);
        $total               = 0;
        foreach ($data['tat'] as $item) {
            $total += $item['tamount'];
        }
        $data['tamount']         = $total;
        $data['get_userlist']    = $CI->db->select('*')->from('users')->where('user_id', $this->session->userdata('user_id'))->get()->result_array();
        $data['tif']             = $this->Hrm_model->get_taxinfomation($selectedValue);
        $data['get_941_sc_info'] = $this->Hrm_model->get_941_sc_info($selectedValue);
        $data['gt']              = $CI->db->select('COUNT(DISTINCT templ_name) AS count_rows')
            ->from('timesheet_info')->where('quarter', $selectedValue)->where('create_by', $this->session->userdata('user_id'))->where('payroll_type !=', 'Sales Partner')->get()->row()->count_rows;
        $view_data = array(
            'title'           => '941 Form',
            'tamount'         => $data['tamount'],
            'get_cdata'       => $data['get_cdata'],
            'get_cominfo'     => $data['get_cominfo'],
            'tif'             => $data['tif'],
            'get_userlist'    => $data['get_userlist'],
            'gt'              => $data['gt'],
            'get_941_sc_info' => $data['get_941_sc_info'],
            'selectedValue'   => $selectedValue,
        );
        $content = $CI->parser->parse('hr/f941', $view_data, true);
        $this->template->full_admin_html_view($content);
    }
// Federal Tax Form
    public function form942Form() {
        $CI = &get_instance();
        $this->load->model('Hrm_model');
        $data['get_cdata']        = $this->Hrm_model->get_employer_federaltax();
        $data['get_cominfo']      = $this->Hrm_model->get_company_info();
        $data['tif']              = $this->Hrm_model->get_taxinfomation_old();
        $data['get_userlist']     = $CI->db->select('*')->from('users')->where('user_id', $this->session->userdata('user_id'))->get()->result_array();
        $data['fed_tax']          = $this->Hrm_model->social_tax();
        $data['get_payslip_info'] = $this->Hrm_model->get_payslip_info();
        $currency_details         = $CI->Web_settings->retrieve_setting_editdata();
        $curn_info_default        = $CI->db->select('*')->from('currency_tbl')->where('icon', $currency_details[0]['currency'])->get()->result_array();
        $data['currency']         = $currency_details[0]['currency'];
        $content                  = $CI->parser->parse('hr/f942', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function manage_workinghours() {
        $CI = &get_instance();
        $CI->load->model("Web_settings");
        $this->load->model("Hrm_model");
        $w_hourdata = $this->db->select('*')->from('working_time')->where('created_by', $this->session->userdata('user_id'))->get()->result_array();
        $data       = array(
            'title'  => 'Manage Working Hours',
            'w_data' => $w_hourdata,
        );
        $content = $this->parser->parse("hr/workinghour_list", $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function working_hours() {
        $CI = &get_instance();
        $this->load->model("Hrm_model");
        $data = array(
            'title' => 'Working Hours',
        );
        $content = $CI->parser->parse("hr/setworking_hours", $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function insertworking_hours() {
        $hour_rate   = $this->input->post('work_hour');
        $exhour_rate = $this->input->post('extra_workamount');
        $data        = array(
            'work_hour'        => $hour_rate,
            'extra_workamount' => $exhour_rate,
            'created_by'       => $this->session->userdata('user_id'),
        );
        $this->db->insert("working_time", $data);
        $this->session->set_flashdata("message", display("save_successfully"));
        redirect(base_url("Chrm/working_hours"));
    }
    public function week_setting() {
        $setting_detail             = $this->Web_settings->retrieve_setting_editdata();
        $data['timesheet_data_emp'] = $this->Hrm_model->timesheet_data_emp();
        $data['setting_detail']     = $setting_detail;
        $data['title']              = display('federal_taxes');
        $content                    = $this->parser->parse('hr/week_setting', $data, true);
        $this->template->full_admin_html_view($content);
    }
    public function save_week_setting() {
        $CI         = &get_instance();
        $uid        = $this->session->userdata('user_id');
        $start_week = $this->input->post('start_week');
        $end_week   = $this->input->post('end_week');

        $url_id       = $this->input->post('url_id');
        $url_admin_id = $this->input->post('url_admin_id');

        $CI->Hrm_model->updateData('web_setting', ['start_week' => $start_week, 'end_week' => $end_week], ['create_by' => $uid]);
        $this->session->set_flashdata("message", display("successfully_updated"));
        redirect(base_url("Chrm/week_setting?id=" . $url_id . '&admin_id=' . $url_admin_id));
    }
    // Get Quater Function - Madhu
    public function getQuarter($month) {
        if ($month >= 1 && $month <= 3) {
            return 'Q1';
        } elseif ($month >= 4 && $month <= 6) {
            return 'Q2';
        } elseif ($month >= 7 && $month <= 9) {
            return 'Q3';
        } elseif ($month >= 10 && $month <= 12) {
            return 'Q4';
        } else {
            return 'Unknown';
        }
    }
    // This Period Final Amount - Madhu
    public function thisPeriodAmount($payroll_type, $payroll_frequency, $total_hours, $hrate, $scAmount, $extra_thisrate, $above_extra_sum, $user_id, $company_id) 
    {
        $workingHour = $this->db->select('work_hour, created_by')->from('working_time')->where('created_by', $user_id)->get()->row();
        $limit_hours = $workingHour->work_hour;
        $final       = 0;
        if (in_array($payroll_type, ['Hourly', 'Fixed'])) {
            list($hours, $minutes) = explode(':', $total_hours);
            $decimal_hours = $hours + ($minutes / 60);
            $total_cost = $hrate * $decimal_hours;
            $frequency_limits = [
                'Bi-Weekly' => 14,
                'Weekly' => 7,
                'Monthly' => 30,
            ];

            if (!isset($frequency_limits[$payroll_frequency])) {
                $limit = ($payroll_type === 'Hourly') ? $limit_hours : 0;
            } else {
                $limit = $frequency_limits[$payroll_frequency];
            }

            if ($decimal_hours <= $limit) {
                $final = $total_cost + $scAmount;
            } else {
                $final = $extra_thisrate + $above_extra_sum;
            }
        }
        return $final;
    }

    // Sales Commision Amount - Madhu
    public function saleCommission($employee_id, $payperiod, $user_id, $company_id) {
        $salescommision  = $this->Hrm_model->sc_info_count($employee_id, $payperiod);
        $scValue         = $salescommision['scValueAmount'];
        $sc_totalAmount1 = $salescommision['total_gtotal'];
        $sc_count        = $salescommision['count'];
        $scValue         = $scValue / 100;
        $scValueAmount1  = $scValue * $sc_totalAmount1;
        return $scValueAmount1;
    }

    // Log Data Table List
    public function logIndexData() {
        $encodedId      = isset($_GET["id"]) ? $_GET["id"] : null;
        $decodedId      = decodeBase64UrlParameter($encodedId);
        $limit          = $this->input->post("length");
        $start          = $this->input->post("start");
        $search         = $this->input->post("search")["value"];
        $orderField     = $this->input->post("columns")[$this->input->post("order")[0]["column"]]["data"];
        $orderDirection = "desc";
        $date           = $this->input->post("date_search");
        $status         = $this->input->post('status_name');
        $items          = $this->Hrm_model->getPaginatedLogs($limit, $start, $orderField, $orderDirection, $search, $date, $status, $decodedId);
        $totalItems     = $this->Hrm_model->getTotalLogs($search, $date, $status, $decodedId);
        $data           = [];
        $i              = $start + 1;
        $edit           = "";
        $delete         = "";
        foreach ($items as $item) {
            $status = $item['status'] == 'Error' || $item['status'] == 'Delete' ?
            '<i class="fa-solid fa-xmark text-danger test-white"></i>' :
            ($item['status'] == 'Update' ?
                '<i class="fa-solid fa-pen text-warning" style="font-size: 11px;"></i>' :
                '<i class="fa-solid fa-check text-success"></i>'
            );

            $row = [
                "id"             => $i,
                "c_date"         => $status . ' ' . $item["c_date"],
                "c_time"         => $item["c_time"],
                "username"       => $item["username"],
                "user_actions"   => $item["user_actions"],
                "details"        => $item["details"],
                "module"         => $item["module"],
                "admin_id"       => $item["admin_id"],
                "field_id"       => $item["field_id"],
                "hint"           => $item["hint"],
                "user_ipaddress" => $item["user_ipaddress"],
                "user_platform"  => $item["user_platform"],
                "user_browser"   => $item["user_browser"],
            ];
            $data[] = $row;
            $i++;
        }
        $response = [
            "draw"            => $this->input->post("draw"),
            "recordsTotal"    => $totalItems,
            "recordsFiltered" => $totalItems,
            "data"            => $data,
        ];
        echo json_encode($response);
    }
    public function add_employee_type() {
        $this->load->model('Hrm_model');
        $data = array(
            'employee_type' => $this->input->post('employee_type'),
            'created_by'    => $this->session->userdata('user_id'),
        );
        $this->Hrm_model->insertData('employee_type', $data);
        $employee_data = $this->Hrm_model->getDatas('employee_type', '*', ['created_by' => $this->session->userdata('user_id')]);
        echo json_encode($employee_data);
    }
    public function add_payment_type() {
        $this->load->model('Hrm_model');
        $payroll_data = $this->Hrm_model->add_payment_type($this->input->post('new_payment_type'));
        echo json_encode($payroll_data);
    }
    public function add_new_bank() {
        $coa = $this->Hrm_model->headcode_bank();
        if ($coa->HeadCode != NULL) {
            $headcode = $coa->HeadCode + 1;
        } else {
            $headcode = "102010201";
        }
        $createby   = $this->session->userdata('user_id');
        $createdate = date('Y-m-d H:i:s');
        $data       = array(
            'created_by' => $createby,
            'bank_id'    => $this->auth->generator(10),
            'bank_name'  => $this->input->post('bank_name', TRUE),
            'ac_name'    => $this->input->post('ac_name', TRUE),
            'ac_number'  => $this->input->post('ac_no', TRUE),
            'branch'     => $this->input->post('branch', TRUE),
            'country'    => $this->input->post('country', TRUE),
            'currency'   => $this->input->post('currency1', TRUE),
            'status'     => 1,
        );
        $bank_coa = [
            'HeadCode'         => $headcode,
            'HeadName'         => $this->input->post('bank_name', TRUE),
            'PHeadName'        => 'Cash At Bank',
            'HeadLevel'        => '4',
            'IsActive'         => '1',
            'IsTransaction'    => '1',
            'IsGL'             => '0',
            'HeadType'         => 'A',
            'IsBudget'         => '0',
            'IsDepreciation'   => '0',
            'DepreciationRate' => '0',
            'CreateBy'         => $createby,
            'CreateDate'       => $createdate,
        ];
        $bankinfo = $this->Hrm_model->bank_entry($data);
        $this->db->insert('acc_coa', $bank_coa);
        echo json_encode($bankinfo);
    }
}
