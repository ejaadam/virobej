@extends('admin.common.layout')
@section('title',$title)
@section('layoutContent')
<section class="content">
    <div class = "row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"> {{$title}}</h3>
            </div><!-- /.box-header -->
            <form class="form-horizontal form-bordered" method="post" id="commission-form">
                <div class="date_div">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="col-md-12" for="search_term">Search Term</label>
                            <div class="col-md-12">
                                <input type="text"  placeholder="Search Term" name="search_term" id="search_term" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="col-md-12" for="status">Status</label>
                            <div class="col-md-12">
                                <select name="status" id="status" class="form-control" >
                                    <option value="">All</option>
                                    @if(!empty($commission_status))
                                    @foreach($commission_status as $s)
                                    <option value="{{$s->com_status_id}}">{{$s->status_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="col-md-12" for="from">From Date</label>
                            <div class="col-md-12">
                                <input type="text"  placeholder="From Date" name="from" id="from" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="col-md-12" for="to">To Date</label>
                            <div class="col-md-12">
                                <input type="text"  placeholder="To Date" name="to" id="to" class="form-control" />
                            </div>
                        </div>
                    </div> <div class="col-lg-12">
                       	<div class="form-group form-actions">
                            <div class="col-md-12">
                                <input  name ="submit" type="button" class="btn btn-sm btn-primary" value="Search" id="search" />
                                <input	name ="submit" type="submit" class="btn btn-sm btn-primary" value="Export" />
                                <input  name ="submit" type="submit" class="btn btn-sm btn-primary" value="Print" />
                                <button type="reset" class="btn btn-sm btn-warning" id="reset_btn"><i class="fa fa-repeat"></i> Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
            <div class="box-body table-responsive">
                <table id="commission_table" class="table table-bordered table-striped franchi_list">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Transaction Details</th>
                            <th nowrap="nowrap">Amount</th>
                            <th nowrap="nowrap">Commission</th>
                            <th>Status</th>
                            <th>Verified On</th>
                            <?php /* ?><th>Action</th><?php */?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
{{HTML::script('js/providers/admin/franchisee/franchisee/franchisee_pg_addfund_commission.js')}}
@stop


<?php
/* ?>@extends('admin.common.layout')
  @section('title')
  | {{$title}}
  @stop
  @section('layoutContent')
  <section class="content">
  <div class = "row">
  <div class="box box-primary">
  <div class="box-header">
  <h3 class="box-title"> {{$title}}</h3>
  </div><!-- /.box-header -->
  <form class="form-horizontal form-bordered" method="post" id="commission-form">
  <div class="date_div">
  <div class="col-lg-3">
  <div class="form-group">
  <label class="col-md-12" for="search_term">Search Term</label>
  <div class="col-md-12">
  <input type="text"  placeholder="Search Term" name="search_term" id="search_term" class="form-control"/>
  </div>
  </div>
  </div>
  <div class="col-lg-3">
  <div class="form-group">
  <label class="col-md-12" for="status">Status</label>
  <div class="col-md-12">
  <select name="status" id="status" class="form-control" >
  <option value="">All</option>
  @if(!empty($commission_status))
  @foreach($commission_status as $s)
  <option value="{{$s->com_status_id}}">{{$s->status_name}}</option>
  @endforeach
  @endif
  </select>
  </div>
  </div>
  </div>
  <div class="col-lg-3">
  <div class="form-group">
  <label class="col-md-12" for="from">From Date</label>
  <div class="col-md-12">
  <input type="text"  placeholder="From Date" name="from" id="from" class="form-control"/>
  </div>
  </div>
  </div>
  <div class="col-lg-3">
  <div class="form-group">
  <label class="col-md-12" for="to">To Date</label>
  <div class="col-md-12">
  <input type="text"  placeholder="To Date" name="to" id="to" class="form-control" />
  </div>
  </div>
  </div> <div class="col-lg-12">
  <div class="form-group form-actions">
  <div class="col-md-12">
  <input  name ="submit" type="button" class="btn btn-sm btn-primary" value="Search" id="search" />
  <input	name ="submit" type="submit" class="btn btn-sm btn-primary" value="Export" />
  <input  name ="submit" type="submit" class="btn btn-sm btn-primary" value="Print" />
  <button type="reset" class="btn btn-sm btn-warning"><i class="fa fa-repeat"></i> Reset</button>
  </div>
  </div>
  </div>
  <div class="clearfix"></div>
  </div>
  </form>
  <div class="box-body table-responsive">
  <table id="commission_table" class="table table-bordered table-striped franchi_list">
  <thead>
  <tr>
  <th>Date</th>
  <th>From</th>
  <th>Transaction Details</th>
  <th>Currency</th>
  <th>Amount</th>
  <th>Commission</th>
  <th>Status</th>
  <th>Verified On</th>
  <th>Action</th>
  </tr>
  </thead>
  <tbody>
  </tbody>
  </table>
  </div>
  </div>
  </div>
  </section>
  {{HTML::script('js/providers/admin/franchisee/franchisee/franchisee_pg_addfund_commission.js?v='.time())}}
  @stop
  <?php */?>
