<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Transcation Log- <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
								<tr>                                                    
                                <th>{{trans('admin/finance.transcation.created_on')}}</th>
                                <th>{{trans('admin/finance.transcation.full_name')}}</th>
                                <th>{{trans('admin/finance.transcation.remarks')}}</th>
                                <th>{{trans('admin/finance.transcation.wallet')}}</th>
                                <th>{{trans('admin/finance.transcation.transaction_id')}}</th>
								<th>{{trans('admin/finance.transcation.cr_amt')}}</th>
					        	<th>{{trans('admin/finance.transcation.dr_amt')}}</th>
                                <th>{{trans('admin/finance.transcation.balance')}}</th>
                            </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($transcation_details != '' && isset($transcation_details))
                {
                    foreach ($transcation_details as $row)
                    {
                        ?>
                        <tr>
						   
                            <td class="text-left">{{$row->created_on}}</td>
                            <td class="text-left">{{$row->fullname}}</td>
                            <td class="text-left">{{$row->remark}}</td>
                            <td class="text-left">{{$row->wallet}}</td> 
                            <td class="text-left">{{$row->transaction_id}}</td>
                            <td class="text-left">{{$row->CR_Fpaidamt}}</td>
                            <td class="text-left">{{$row->DR_Fpaidamt}}</td>
                            <td class="text-left">{{$row->Fcurrent_balance}}</td>
                        </tr>
                        <?php
                        $i++;
                    }
                 }
				 else {
					echo "<tr><td colspan='9'>No Records Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>