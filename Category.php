<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Category extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');        
        $this->load->model('Login_model'); 
    } 

    /*
     * Listing of category
     */
    function index()
    {
        
        $params['limit'] = RECORDS_PER_PAGE; 
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        
        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('index.php/category/index?');
        $config['total_rows'] = $this->Category_model->get_all_category_count();
        $this->pagination->initialize($config);

        $data['category'] = $this->Category_model->get_all_category($params);

        
        $data['offset']=$params['offset'];
        $data['_view'] = 'category/index';
        $this->load->view('layouts/main',$data);
    }

    /*
     * Adding a new category
     */
    function add()
    {   
         
        $this->load->library('form_validation');

		$this->form_validation->set_rules('category_name','Category Name','required');
		
		if($this->form_validation->run())     
        {   
            $params = array(
				'category_name' => $this->input->post('category_name'),
            );
            $check=$this->Category_model->check_category_already_exits($this->input->post('category_name'));
            if($check==NULL)
            {

            $category_id = $this->Category_model->add_category($params);
           
         
            ?>
                   <script type="text/javascript">
                       alert("Success");
                          location.href="<?php echo base_url()?>index.php/category/index";
                    </script>
                <?php 

         
            }else
            {
                ?>
                    <script type="text/javascript">
                       alert("Category Already Exists");
                          location.href="<?php echo base_url()?>index.php/category/add";
                    </script>
                <?php 
            }
        }
        else
        {   
          
                  
            $data['_view'] = 'category/add';
            $this->load->view('layouts/main',$data);
        }
    }  

    /*
     * Editing a category
     */
    function edit($category_id)
    {   
        
         
        
        // check if the category exists before trying to edit it
        $data['category'] = $this->Category_model->get_category($category_id);
        
        if(isset($data['category']['category_id']))
        {
            $this->load->library('form_validation');

			$this->form_validation->set_rules('category_name','Category Name','required');
		
			if($this->form_validation->run())     
            {   
                $params = array(
					'category_name' => $this->input->post('category_name'),
                );
                $check=$this->Category_model->check_category_already_exits($this->input->post('category_name'));
                if($check==NULL)
                {

                $this->Category_model->update_category($category_id,$params);            
                //redirect('index.php/lib_category/index');
                ?>
                   <script type="text/javascript">
                       alert("Updated Successfully");
                          location.href="<?php echo base_url()?>index.php/category/index";
                    </script>
                <?php 
                }else
                {
                ?>
                    <script type="text/javascript">
                       alert("Category Already Exists");
                          location.href="<?php echo base_url()?>index.php/category/index";
                    </script>
                <?php 
               }
            }
            else
            {
               
                $data['_view'] = 'category/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The category you are trying to edit does not exist.');
    } 

    /*
     * Deleting category
     */
    function remove($category_id)
    {
        
        $this->load->model('Recovery_model');
        $category = $this->Category_model->get_category($category_id);

        // check if the lib_category exists before trying to delete it
        if(isset($category['category_id']))
        {
			$params['category_id'] = $category_id;
            $data['Recoverys'] = $this->Recovery_model->recovery_category($params);
			if(empty($data['Recoverys']))
			{
            $this->Category_model->delete_category($category_id);
			?><script type="text/javascript">
                       alert("Category Deleted");
                          location.href="<?php echo base_url()?>index.php/category/index";
                    </script>
           <?php //redirect('index.php/lib_category/index');
			}
			else{ ?><script type="text/javascript">
                       alert("Oops..The category you are trying to delete contains Recoverys");
                          location.href="<?php echo base_url()?>index.php/category/index";
                    </script>
           <?php } 
            
        }
       
    }
    
}
?>