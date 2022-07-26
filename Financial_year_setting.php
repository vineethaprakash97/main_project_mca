<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Financial_year_setting extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Financial_year_setting_model');
    } 

    /*
     * Listing of financial_year_settings
     */
	 
	   function home()
    { 
		
        $data['financial_year_settings'] = $this->Financial_year_setting_model->get_all_financial_year_settings();
        
        $data['_view'] = 'financial_year_setting/index';
     // $this->load->view('layouts/main',$data);
	  $this->load->view('financial_year_setting/index',$data);
	  
	  
    }
	 
	 
	 
    function index()
    {
		
		$this->load->library('form_validation');

		$sdate=date("Y/m/d",strtotime($this->input->post('start_date')));
			$edate=date("Y/m/d",strtotime($this->input->post('end_date')));
			
		
		$this->form_validation->set_rules('start_date','Start Date','required');
		$this->form_validation->set_rules('end_date','End Date','required');
		$dates=array($sdate,$edate);
		
		$this->form_validation->set_rules('custom_error','', 'callback_form_date_range_exist['.json_encode($dates).']');
		$this->form_validation->set_message('form_date_range_exist', 'Financial Year already exist');
		
		$this->form_validation->set_rules('custom_error_range','', 'callback_form_valid_date_range['.json_encode($dates).']');
		$this->form_validation->set_message('form_valid_date_range', 'Date range not Valid');
		
		
		if($this->form_validation->run())     
        {   
	           
		 	
            $params = array(
				'start_date' => $sdate,
				'end_date' => $edate,
				'fin_label' => $this->generateFinancialLabel($sdate,$edate),
				'status' => $this->input->post('status'),
            );
            
            $financial_year_setting_id = $this->Financial_year_setting_model->add_financial_year_setting($params);
			
			 redirect("Financial_year_setting/index");
            
        }
		
		
        $data['financial_year_settings'] = $this->Financial_year_setting_model->get_all_financial_year_settings();
        
        $data['_view'] = 'financial_year_setting/index';
      $this->load->view('layouts/main',$data);
	  //$this->load->view('financial_year_setting/index',$data);
	  
	  
    }

	function form_valid_date_range($val,$dates)
    { 
		$dates=json_decode($dates);
		 
		  return is_valid_date_range($dates[0],$dates[1]);
    }   
	
	function form_date_range_exist($val,$dates)
    { 
		$dates=json_decode($dates);
		 $f=$this->Financial_year_setting_model->get_all_financial_year_settings_by_sdate_edate($dates[0],$dates[1]);
		 if(empty($f))
			 return true;
		 else
			 return false;
		   
    }  
	
	
	
	private function generateFinancialLabel($sdate,$edate)
	{
		$data=date("Y",strtotime($sdate))."-".date("Y",strtotime($edate));
		return $data;
	}
     

    /*
     * Editing a financial_year_setting
     */
    function edit($fin_id)
    {   
        // check if the financial_year_setting exists before trying to edit it
        $data['financial_year_setting'] = $this->Financial_year_setting_model->get_financial_year_setting($fin_id);
        
        if(isset($data['financial_year_setting']['fin_id']))
        {
            $this->load->library('form_validation');

			$sdate=date("Y/m/d",strtotime($this->input->post('start_date')));
			$edate=date("Y/m/d",strtotime($this->input->post('end_date')));
			
		
		$this->form_validation->set_rules('start_date','Start Date','required');
		$this->form_validation->set_rules('end_date','End Date','required');
		$dates=array($sdate,$edate);
		$this->form_validation->set_rules('custom_error','', 'callback_form_valid_date_range['.json_encode($dates).']');
		$this->form_validation->set_message('form_valid_date_range', 'Date range not Valid');
		
			if($this->form_validation->run())     
            {   
                
			
            $params = array(
				'start_date' => $sdate,
				'end_date' => $edate,
				'fin_label' => $this->generateFinancialLabel($sdate,$edate),
				'status' => $this->input->post('status'),
            );
				$status=$this->input->post('status');
				if($status=="active"){
					$params1 = array(
						 
						'status' =>'inactive'
					);
					$this->Financial_year_setting_model->update_all_financial_year_setting($params1);  	
				}
                $this->Financial_year_setting_model->update_financial_year_setting($fin_id,$params);            
                redirect('financial_year_setting/index');
            }
            else
            {
                $data['_view'] = 'financial_year_setting/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The financial_year_setting you are trying to edit does not exist.');
    } 

    /*
     * Deleting financial_year_setting
     */
    function remove($fin_id)
    {
        $financial_year_setting = $this->Financial_year_setting_model->get_financial_year_setting($fin_id);

        // check if the financial_year_setting exists before trying to delete it
        if(isset($financial_year_setting['fin_id']))
        {
            $this->Financial_year_setting_model->delete_financial_year_setting($fin_id);
            redirect('financial_year_setting/index');
        }
        else
            show_error('The financial_year_setting you are trying to delete does not exist.');
    }
    function close($fin_id){
		  $this->load->model('Student_model');
        $this->load->model('Student_fee_model');
        $this->load->model('Student_financial_account_model');
       
		$financial_year_setting = $this->Financial_year_setting_model->get_financial_year_setting($fin_id);

		  $from_date= $financial_year_setting["start_date"];
		$to_date= $financial_year_setting["end_date"];
		 			
		$students = $this->Student_model->get_all_student();
        
		foreach ($students as $student) {
            
			$total_fee_in_financial=0;
            $studentid    = $student["student_id"];
           
			$student_fees = $this->Student_fee_model->get_student_fee_by_student_ID_start_date_end_date($studentid,$from_date,$to_date);
           //  var_dump($student_fees);
			foreach ($student_fees as $student_fee) {
				$fee_start_date= $student_fee["start_date"];
				$fee_end_date= $student_fee["end_date"];
				$fee_amount= $student_fee["fee_amount"];
				
				if($fee_start_date < $from_date){
					//echo "<h1>Need to set $fee_start_date as $from_date</h1>"; 
					$fee_start_date = $from_date;
				}
				else{
					 if($student_fee["fee_mode"]==0){
					$total_fee_in_financial+= $student_fee["fee_amount"];
				}
					
				}
				
				if($fee_end_date ==NULL  || $fee_end_date > $to_date ){
					//echo "<h2>Need to set $fee_end_date as $to_date</h2>"; 
					$fee_end_date = $to_date;
				}	
				// echo "<h2> $fee_start_date - $fee_end_date is $fee_amount</h2>"; 
               
				 if($student_fee["fee_mode"]==1){
					$fee_amount= $student_fee["fee_amount"];
					 $total_months=$this->getMonthDifference($fee_start_date,$fee_end_date);
					 $sub_total= $fee_amount * $total_months;
					 $total_fee_in_financial+=$sub_total;
				}
			}
			
			// do insert
			
			  //$total_fee_in_financial;
			
		$student_financial = $this->Student_financial_account_model->get_Student_financial_account_model_by_fin_student($fin_id,$studentid);
		$param["student_id"]=$studentid;
		$total_paid_amount= $this->Student_fee_model->getStudentTotalFeePaidAmount($studentid,$from_date,$to_date);
			$param["total_fee"]=$total_fee_in_financial;
			$param["op_balance"]=$this->Student_financial_account_model->get_Student_PreviousClosingBalance($fin_id,$studentid);;
			 $param["closing_balance"]=$total_paid_amount-$total_fee_in_financial+$param["op_balance"];
			$param["financial_id"]=$fin_id;
			 
			 //echo $total_paid_amount."-".$total_fee_in_financial."xxxxxx".$param["op_balance"];
	    if($student_financial==null){
			//insert
			$this->Student_financial_account_model->add_Student_financial_account($param);
		}else{
			//update
			  "UPDATE";
			
		  $student_fin_id=	$student_financial["student_fin_id"];
			$this->Student_financial_account_model->update_Student_financial_account($student_fin_id,$param);
		
		
		
		}
			
			 //break;
		}
		 
		 redirect("Financial_year_setting/index");
		 
	}
	function getMonthDifference($start , $end )
{
   

  $start = new DateTime("$start");
 
$end   = new DateTime("$end");
$diff  = $start->diff($end);
$days=  $diff->format('%a');
  $month=$days/30;
  $m=floor($month);
return $m;
//return $diff->format('%y') * 12 + $diff->format('%m') ;
   
}



function set_current_financial_year($fin_id){
	$status="active";
				if($status=="active"){
					$params1 = array(
						 
						'status' =>'inactive'
					);
					$this->Financial_year_setting_model->update_all_financial_year_setting($params1);  	
				}
				$params = array(
						 
					'status' =>'active'
				);
                $this->Financial_year_setting_model->update_financial_year_setting($fin_id,$params);            
                redirect('Dashboard/index');
}




}
