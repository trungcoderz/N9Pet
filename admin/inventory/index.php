<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Danh sách hàng tồn kho</h3>
		<div class="card-tools">
			<a href="?page=inventory/manage_inventory" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Tạo mới</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="25%">
					<col width="10%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Sản phẩm</th>
						<th>Đơn vị</th>
						<th>Độ tuổi / Kích thước</th>
						<th>Giá</th>
						<th>Số lượng còn lại</th>
						<th>Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT i.*,p.product_name as product from `inventory` i inner join `products` p on p.id = i.product_id order by unix_timestamp(i.date_created) desc ");
						while($row = $qry->fetch_assoc()):
						$sold = $conn->query("SELECT SUM(ol.quantity) as sold FROM order_list ol inner join orders o on o.id = ol.order_id where ol.product_id='{$row['id']}' and o.`status` != 4 ");
						$sold = $sold->num_rows > 0 ? $sold->fetch_assoc()['sold'] : 0;
						$avail = $row['quantity'] - $sold;
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['product'] ?></td>
							<td><?php echo $row['unit'] ?></td>
							<td><?php echo $row['size'] ?></td>
							<td class="text-right"><?php echo number_format($row['price']) ?></td>
							<td class="text-right"><?php echo $avail ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Hành động
				                    <span class="sr-only">Xuống</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=inventory/manage_inventory&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Chỉnh sửa</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Xóa</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Bạn có chắc muốn xóa sản phẩm này không?","delete_inventory",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function delete_inventory($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_inventory",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("Lỗi đã xảy ra.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("Lỗi đã xảy ra.",'error');
					end_loader();
				}
			}
		})
	}
</script>