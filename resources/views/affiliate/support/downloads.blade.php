@extends('affiliate.layout.dashboard')
@section('title',"Downloads")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Downloads</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li>Support</li>
        <li class="active">Downloads</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-sm-12">
			<div class="panel panel-default">
                <div class="panel-body">
                    <table id="example4" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Uploaded On</th>
                                <th>Document</th>
                                <th class="text-center">Download</th>                             
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($downloadlist))
                            {
                                foreach ($downloadlist as $val)
                                {
                                    $file_name = $val->doc_path;
                                    /* $get_ext = explode('.',$file_name);
                                      if() */
                                    ?>
                                    <tr>
                                        <td><?php echo date('d-M-Y H:i:s', strtotime($val->created_date));?> </td>
                                        <td><?php echo '<b>'.$val->doc_title.'</b><br>'.$val->doc_desc?> </td>                                                
                                        <td class="text-center"><a target="_blank" href="{{$val->doc_path}}" download><button class="btn btn-info"><i class="fa fa-download"></i> Download</button></a></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
				
				
				
			</div>
			<!-- ./col -->			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@stop