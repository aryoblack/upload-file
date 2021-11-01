
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header bg-light">
						<h3 class="card-title"><i class="fa fa-list text-blue"></i> Data Companies</h3>
						<div class="text-right">
							<button type="button" class="btn btn-sm btn-outline-primary" onclick="add()" title="Add Data"><i class="fas fa-plus"></i> Add</button>
						</div>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<table id="tblegori" class="table table-bordered table-striped table-hover">
							<thead>
								<tr class="bg-info">
									<th>Code</th>
									<th>Name</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Owner</th>
                                    <!-- <th>File</th> -->
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>


<script type="text/javascript">
var save_method; //for save method string
var table;

$(document).ready(function() {

    //datatables
    table =$("#tblegori").DataTable({
    	"responsive": true,
    	"autoWidth": false,
    	"language": {
    		"sEmptyTable": "Data Companies Belum Ada"
    	},
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        // Load data for the table's content from an Ajax source
        "ajax": {
        	"url": "<?php echo site_url('companies/ajax_list')?>",
        	"type": "POST"
        },

    });

 //set input/textarea/select event when change value, remove class error and remove text help block 
 $("input").change(function(){
 	$(this).parent().parent().removeClass('has-error');
 	$(this).next().empty();
 	$(this).removeClass('is-invalid');
 });
 $("textarea").change(function(){
 	$(this).parent().parent().removeClass('has-error');
 	$(this).next().empty();
 	$(this).removeClass('is-invalid');
 });
 $("select").change(function(){
 	$(this).parent().parent().removeClass('has-error');
 	$(this).next().empty();
 	$(this).removeClass('is-invalid');
 });

});

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

const Toast = Swal.mixin({
	toast: true,
	position: 'top-end',
	showConfirmButton: false,
	timer: 3000
});


//delete
function hapus(id){

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.value) {
        $.ajax({
            url:"<?php echo site_url('companies/delete');?>",
            type:"POST",
            data:"id="+id,
            cache:false,
            dataType: 'json',
            success:function(respone){
                if (respone.status == true) {
                    reload_table();
                    Swal.fire(
                      'Deleted!',
                      'Your file has been deleted.',
                      'success'
                      );
                }else{
                  Toast.fire({
                      icon: 'error',
                      title: 'Delete Error!!.'
                  });
              }
          }
      });
    }
})
}



function add()
{
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal({backdrop: 'static', keyboard: false}); // show bootstrap modal
    $('.modal-title').text('Add Companies'); // Set Title to Bootstrap modal title
}

function edit(id){
	save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
    	url : "<?php echo site_url('companies/edit')?>/" + id,
    	type: "GET",
    	dataType: "JSON",
    	success: function(data)
    	{

    		$('[name="id"]').val(data.id);
    		$('[name="code"]').val(data.code);
            $('[name="name"]').val(data.name);
            $('[name="address"]').val(data.address);
            $('[name="email"]').val(data.email);
            $('[name="phone"]').val(data.phone);
            $('[name="owner"]').val(data.owner);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Companies'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
        	alert('Error get data from ajax');
        }
    });
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    if(save_method == 'add') {
        url = "<?php echo site_url('companies/insert')?>";//arahin ke companies insert
    } else {
        url = "<?php echo site_url('companies/update')?>";//arahin ke companies update
    }

    var formdata = new FormData($('#form')[0]);
    $.ajax({
      url : url,
      type: "POST",
      data: formdata,
      dataType: "JSON",
      cache: false,
      contentType: false,
      processData: false,
    	success: function(data)
    	{

            if(data.status) //if success close modal and reload ajax table
            {
            	$('#modal_form').modal('hide');
            	reload_table();
            	Toast.fire({
            		icon: 'success',
            		title: 'Success!!.'
            	});
            }
            else
            {
            	for (var i = 0; i < data.inputerror.length; i++) 
            	{
            		$('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
            		$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]).addClass('invalid-feedback');
            	}
            }
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
        	alert('Error adding / update data');
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

        }
    });
}

</script>



<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content ">

			<div class="modal-header">
				<h3 class="modal-title"></h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>

			</div>
			<div class="modal-body form">
				<form action="#" id="form" class="form-horizontal" enctype="multipart/form-data">
					<input type="hidden" value="" name="id"/> 
					<div class="card-body">
						<div class="form-group row ">
							<label for="code" class="col-sm-3 col-form-label">CODE</label>
							<div class="col-sm-9 kosong">
								<input type="text" class="form-control" name="code" id="code" placeholder="CODE" >
								<span class="help-block"></span>
							</div>
						</div>
                        <div class="form-group row ">
                            <label for="name" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9 kosong">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="address" class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9 kosong">
                                <textarea type="text" class="form-control" name="address" id="address" placeholder="Address" ></textarea> 
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9 kosong">
                                <input type="text" class="form-control" name="email" id="email" placeholder="Email" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">Phone</label>
                            <div class="col-sm-9 kosong">
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                         <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">Owner</label>
                            <div class="col-sm-9 kosong">
                                <input type="text" class="form-control" name="owner" id="owner" placeholder="Owner" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                       <!--  <div class="form-group row ">
                            <label for="foto" class="col-sm-3 col-form-label">File</label>
                          <div class="col-sm-9 kosong ">
                            <input type="file" class="form-control btn-file"  name="imagefile" id="imagefile" placeholder="File" value="UPLOAD">
                            <span class="help-block"></span>
                        </div>
                    </div> -->
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->