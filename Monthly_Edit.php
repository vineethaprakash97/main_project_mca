<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Monthly_Edit extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Monthly_edit_model');        
     
    } 

    /*
     * Listing of category
     */
    function index()
    {
        
        

        $data['months'] = $this->Monthly_edit_model->get_all_Monthly_edit();

       
        $data['_view'] = 'monthly/index';
        $this->load->view('layouts/main',$data);
    }

    /*
     * Adding a new category
     */
    function add()
    {   
         
        $this->load->library('form_validation');

		$this->form_validation->set_rules('date','date','required');
		$this->form_validation->set_rules('month','month','required');
		
		if($this->form_validation->run())     
        {   
            $params = array(
				'month' => $this->input->post('month'),
                'year' => $this->input->post('year'),
                'date' => date("Y-m-d",strtotime($this->input->post('date'))),
                
            );
           
            $category_id = $this->Monthly_edit_model->add_Monthly_edit($params);
           
          
        }
        
        redirect('Monthly_Edit/index');
    }  

    

    /*
     * Deleting category
     */
    function remove($edit_id)
    {
         
        $this->Monthly_edit_model->delete_Monthly_edit($edit_id);

        redirect('Monthly_Edit/index');
       
    }
    


    

    function edit($edit_id)
    {   
        
         
        
        // check if the category exists before trying to edit it
        $data["village"]=  $this->Monthly_edit_model->get_Monthly_edit($edit_id);
        
        if(isset($data['village']['edit_id']))
        {
            $this->load->library('form_validation');

			$this->form_validation->set_rules('date','date','required');
            $this->form_validation->set_rules('month','month','required|trim');
            $this->form_validation->set_rules('year','year','required|trim');
		    
            
			if($this->form_validation->run())     
            {
                
                
                $params = array(
                    'month' => $this->input->post('month'),
                    'year' => $this->input->post('year'),
                    'date' => date("Y-m-d",strtotime($this->input->post('date'))),
                    
                );
               
                $category_id = $this->Monthly_edit_model->update_Monthly_edit($edit_id,$params);
               
                ?>
                   <script type="text/javascript">
                       alert("Updated Successfully");
                          location.href="<?php echo base_url()?>index.php/Monthly_Edit/index";
                    </script>
             
                <?php 
               
            }
            
               
                $data['_view'] = 'monthly/edit';
                $this->load->view('layouts/main',$data);
           
        }
        else
            show_error('The category you are trying to edit does not exist.');
    } 







}
?>