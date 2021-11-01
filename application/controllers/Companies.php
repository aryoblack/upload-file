<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Create By : Aryo
 * Youtube : Aryo Coding
 */
class Companies extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
        $this->load->model(array('Mod_companies'));
    }

    public function index()
    {
      $this->load->helper('url');
      $this->template->load('layoutbackend','companies');
  }

  public function ajax_list()
  {
    ini_set('memory_limit','512M');
    set_time_limit(3600);
    $list = $this->Mod_companies->get_datatables();
    $data = array();
    $no = $_POST['start'];
    foreach ($list as $pel) {
        $no++;
        $row = array();
            $row[] = $pel->code;//array 0
            $row[] = $pel->name;//array 1
            $row[] = $pel->address;//array 2
            $row[] = $pel->email;
            $row[] = $pel->phone;
            $row[] = $pel->owner;
            /*$row[] = "<a  href='./assets/File/$pel->releaseFolder' title=\"File\" target='_blank'>$pel->releaseFolder</a>";*/
            $row[] = "<a class=\"btn btn-xs btn-outline-primary\" href=\"javascript:void(0)\" title=\"Edit\" onclick=\"edit($pel->id)\"><i class=\"fas fa-edit\"></i></a><a class=\"btn btn-xs btn-outline-danger\" href=\"javascript:void(0)\" title=\"Delete\"  onclick=\"hapus($pel->id)\"><i class=\"fas fa-trash\"></i></a>";
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Mod_companies->count_all(),
            "recordsFiltered" => $this->Mod_companies->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function insert()
    {
        $this->_validate();
         /*if(!empty($_FILES['imagefile']['name'])) {
        $config['upload_path']   = './assets/File/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|zip|xls|xlsx|pdf'; //mencegah upload backdor
            $config['max_size']      = '1000';
            $config['max_width']     = '2000';
            $config['max_height']    = '1024';
            $config['file_name']     = slug($this->input->post('imagefile')); 
            
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('imagefile')){
                $gambar = $this->upload->data();*/
                $save  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'address' => $this->input->post('address'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'owner' => $this->input->post('owner'),
                    // 'releasefolder' => $gambar['file_name']
                );
                $this->Mod_companies->insert("companies", $save);
                echo json_encode(array("status" => TRUE));
           /* }
        }*/
        
    }

    public function update()
    {
        $this->_validate();
        $date=date("Y-m-d H-i-s");
        $id      = $this->input->post('id');
        /*$config['upload_path']   = './assets/File/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|zip|xls|xlsx|pdf'; //mencegah upload backdor
            $config['max_size']      = '1000';
            $config['max_width']     = '2000';
            $config['max_height']    = '1024';
            $config['file_name']     = slug($this->input->post('imagefile')); 
            
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('imagefile')){
                $gambar = $this->upload->data();
                $save  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'address' => $this->input->post('address'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'owner' => $this->input->post('owner'),
                    'updated' => $date,
                    // 'releaseFolder' => $gambar['file_name']
                );
                $g = $this->Mod_companies->get_file($id)->row_array();

                if ($g != null || $g != "") {
                //hapus gambar yg ada diserver
                    unlink('./assets/File/'.$g['releaseFolder']);
                }
                $this->Mod_companies->update($id, $save);
                echo json_encode(array("status" => TRUE));
            }else{*/
                $save  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'address' => $this->input->post('address'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'owner' => $this->input->post('owner'),
                    'updated' => $date,
                );
                $this->Mod_companies->update($id, $save);
                echo json_encode(array("status" => TRUE));
            // }
            
        }

        public function edit($id)
        {
            $data = $this->Mod_companies->get_companies($id);
            echo json_encode($data);
        }

        public function delete()
        {
            $id = $this->input->post('id');
            $this->Mod_companies->delete($id, 'companies');        
            echo json_encode(array("status" => TRUE));
        }
        private function _validate()
        {
            $data = array();
            $data['error_string'] = array();
            $data['inputerror'] = array();
            $data['status'] = TRUE;

            if($this->input->post('code') == '')
            {
                $data['inputerror'][] = 'code';
                $data['error_string'][] = 'Code is required';
                $data['status'] = FALSE;
            }
            if($this->input->post('name') == '')
            {
                $data['inputerror'][] = 'name';
                $data['error_string'][] = 'Name is required';
                $data['status'] = FALSE;
            }
            if($this->input->post('address') == '')
            {
                $data['inputerror'][] = 'address';
                $data['error_string'][] = 'Address is required';
                $data['status'] = FALSE;
            }
            if($this->input->post('email') == '')
            {
                $data['inputerror'][] = 'email';
                $data['error_string'][] = 'Email is required';
                $data['status'] = FALSE;
            }
            if($this->input->post('phone') == '')
            {
                $data['inputerror'][] = 'phone';
                $data['error_string'][] = 'Phone is required';
                $data['status'] = FALSE;
            }
            if($this->input->post('owner') == '')
            {
                $data['inputerror'][] = 'owner';
                $data['error_string'][] = 'Owner is required';
                $data['status'] = FALSE;
            }

            if($data['status'] === FALSE)
            {
                echo json_encode($data);
                exit();
            }
        }
    }