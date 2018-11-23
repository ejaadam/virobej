@extends('supplier.common.layout')
@section('pagetitle')
{{$title}}
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')

<div id="main_content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    @if($show_submit_feedback)
                    <button id="submit_new_feedbck" class="btn btn-success btn-sm pull-right "><span class="icon-plus"></span>Submit Your Feedback </button>
                    @endif
                    <h4 class="panel-title">{{$title}}</h4>
                </div>
                <div id="fbck_list">
                    <div class="panel-body" style="padding:0px;">
                        <div class="row">
                            <div  class="col-sm-3">
                                <div class="panel_controls" style="padding:25px;">
                                    <div class="row">
                                        <div class="form-horizontal">
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <input type="text" placeholder="Search Term" id="search_term" name="search_term"class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                                        <input class="form-control" type="text" id="start_date" placeholder="From">
                                                        <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                                        <input class="form-control" type="text" id="end_date" placeholder="To">
                                                        <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <select name="feedback_type" id="feedback_type" class="form-control">
                                                        <option value="">All</option>
                                                        <option value="0"> Replied</option>
                                                        <option value="1">New</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button id="search" class="btn btn-primary btn-sm">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <table id="feedback_list" class="table">
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="replied_msg">

                </div>
                <div id="create_feedback"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reply_msg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> </h4>
            </div>
            <div class="modal-body" id="reply_form">

                <input type="hidden" id="feedback_id" name="feedback_id"/>
                <div class="row">
                    <div class="col-md-12">
                        <label></label>
                        <textarea class="form-control" name = "description" id="description"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>&nbsp;</label>
                        <input type="submit" name="reply" id="reply" class="btn btn-primary" value="Reply" >
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_data1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Reply Feedback</h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
{{ HTML::script('supports/supplier/feedback/feedback_list.js') }}
@stop
