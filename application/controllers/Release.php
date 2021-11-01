<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Create By : Aryo
 * Youtube : Aryo Coding
 */
class Release extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
        $this->load->model(array('Mod_releases'));
    }

    public function index()
    {
      $this->load->helper('url');
      $data['company'] = $this->Mod_releases->get_company();
      $this->template->load('layoutbackend','releases', $data);
  }

  public function ajax_list()
  {
    ini_set('memory_limit','512M');
    set_time_limit(3600);
    $list = $this->Mod_releases->get_datatables();
    $data = array();
    $no = $_POST['start'];
    foreach ($list as $pel) {
        $no++;
        $row = array();
            $row[] = $pel->company;//array 0
            // $row[] = $pel->releaseDate;//array 1
            $row[] = $pel->version;
            $row[] = $pel->changelog;//array 2
            $row[] = $pel->instruction;
            $row[] = "<a  href='./assets/File/$pel->fileName' title=\"File\" target='_blank'>$pel->fileName</a>";
            $row[] = $pel->full_name;
            $row[] = "<a class=\"btn btn-xs btn-outline-primary\" href=\"javascript:void(0)\" title=\"Edit\" onclick=\"edit($pel->id)\"><i class=\"fas fa-edit\"></i></a><a class=\"btn btn-xs btn-outline-danger\" href=\"javascript:void(0)\" title=\"Delete\"  onclick=\"hapus($pel->id)\"><i class=\"fas fa-trash\"></i></a>";
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Mod_releases->count_all(),
            "recordsFiltered" => $this->Mod_releases->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function insert()
    {
        $this->_validate();
        $date=date("Y-m-d H-i-s");
         if(!empty($_FILES['imagefile']['name'])) {
        $config['upload_path']   = './assets/File/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|zip|xls|xlsx|pdf'; //mencegah upload backdor
            $config['max_size']      = '1000';
            $config['max_width']     = '2000';
            $config['max_height']    = '1024';
            $config['file_name']     = slug($this->input->post('imagefile')); 
            
            $this->upload->initialize($config);
            $id_user = $this->session->userdata('id_user');
            if ($this->upload->do_upload('imagefile')){
                $gambar = $this->upload->data();
                $save  = array(
                    'company' => $this->input->post('company'),
                    // 'releasedate' => $this->input->post('releasedate'),
                    'version' => $this->input->post('version'),
                    'changelog' => $this->input->post('changelog'),
                    'instruction' => $this->input->post('instruction'),
                    'publisher' => $id_user,
                    'created' => $date,
                    'filename' => $gambar['file_name']
                );
                $this->Mod_releases->insert("releases", $save);
                echo json_encode(array("status" => TRUE));
            }
        }
        
    }

    public function update()
    {
        $this->_validate();
        $date=date("Y-m-d H-i-s");
        $id      = $this->input->post('id');
        $config['upload_path']   = './assets/File/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|zip|xls|xlsx|pdf'; //mencegah upload backdor
            $config['max_size']      = '1000';
            $config['max_width']     = '2000';
            $config['max_height']    = '1024';
            $config['file_name']     = slug($this->input->post('imagefile')); 
            $id_user = $this->session->userdata('id_user');
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('imagefile')){
                $gambar = $this->upload->data();
                $save  = array(
                    'company' => $this->input->post('company'),
                    // 'releasedate' => $this->input->post('releasedate'),
                    'version' => $this->input->post('version'),
                    'changelog' => $this->input->post('changelog'),
                    'instruction' => $this->input->post('instruction'),
                    'updated' => $date,
                    'filename' => $gambar['file_name']
                );
                $g = $this->Mod_releases->get_file($id)->row_array();

                if ($g != null || $g != "") {
                //hapus gambar yg ada diserver
                    unlink('./assets/File/'.$g['filename']);
                }
                $this->Mod_releases->update($id, $save);
                echo json_encode(array("status" => TRUE));
            }else{
                $save  = array(
                    'company' => $this->input->post('company'),
                    // 'releasedate' => $this->input->post('releasedate'),
                    'version' => $this->input->post('version'),
                    'changelog' => $this->input->post('changelog'),
                    'instruction' => $this->input->post('instruction'),
                    'updated' => $date,
                );
                $this->Mod_releases->update($id, $save);
                echo json_encode(array("status" => TRUE));
            }
            
        }

        public function edit($id)
        {
            $data = $this->Mod_releases->get_releases($id);
            echo json_encode($data);
        }

        public function delete()
        {
            $id = $this->input->post('id');
             $g = $this->Mod_releases->get_file($id)->row_array();
                if ($g != null) {
                //hapus gambar yg ada diserver
                    unlink('./assets/File/'.$g['fileName']);
                }
            $this->Mod_releases->delete($id, 'releases');        
            echo json_encode(array("status" => TRUE));
        }
        private function _validate()
        {
            $data = array();
            $data['error_string'] = array();
            $data['inputerror'] = array();
            $data['status'] = TRUE;

            if($this->input->post('company') == '')
            {
                $data['inputerror'][] = 'company';
                $data['error_string'][] = 'company is required';
                $data['status'] = FALSE;
            }
            /*if($this->input->post('releasedate') == '')
            {
                $data['inputerror'][] = 'releasedate';
                $data['error_string'][] = 'Release Date is required';
                $data['status'] = FALSE;
            }*/
            if($this->input->post('version') == '')
            {
                $data['inputerror'][] = 'version';
                $data['error_string'][] = 'Version is required';
                $data['status'] = FALSE;
            }
            if($this->input->post('changelog') == '')
            {
                $data['inputerror'][] = 'changelog';
                $data['error_string'][] = 'Changelog is required';
                $data['status'] = FALSE;
            }
            if($this->input->post('instruction') == '')
            {
                $data['inputerror'][] = 'instruction';
                $data['error_string'][] = 'Instruction is required';
                $data['status'] = FALSE;
            }
           

            if($data['status'] === FALSE)
            {
                echo json_encode($data);
                exit();
            }
        }
    }