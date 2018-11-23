 	<div id="assign_msg"></div>
	<div class="panel panel-info">
		<div class="panel-heading">
			<h4 class="panel-title">Allocate Store
				<button class="btn btn-default btn-sm close_btn pull-right btn-danger">Close</button>
			</h4>
		</div>
	<form class="form-horizontal form-bordered" id="supplier-stores-form" action="#" method="post" novalidate="novalidate" autocomplete="off">
		<div class="panel-body">
			<table class="table">
				<tr>
					<th>{{trans('general.seller.store')}}</th>
					<th>{{trans('general.seller.allocate')}}</th>
					<th>{{trans('general.seller.login_access')}}</th>
				</tr>
				<tbody id="supplier-stores">
					
				</tbody>
			</table>
		</div>
			<div class="panel-footer text-right">
				<button type="submit" class="btn btn-primary" id="update_store">
					<i class="fa fa-save margin-r-5"></i>{{trans('general.btn.assign')}}
				</button>
			</div>
	</form>
</div>


