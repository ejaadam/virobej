<ul class="media-list comment-list" id="commentlist">
  <?php
    if (!empty($feedback_detail))
		   {
		  
		   ?>
  <li><small class="text-muted">{{date("F j,  Y H:i:s", strtotime($feedback_detail ->created_on))}} </small>
  <p class="text-info"><strong>{{$feedback_detail ->uname}}</strong> ({{$feedback_detail ->full_name}})</p>
  </li>
</ul>
<table  class="table table-striped" border="1" BORDERCOLOR="#CCC">
  <thead>
  </thead>
  <tbody>
    <tr>
      <td width="10%">Subject</td>
      <td>{{$feedback_detail->subject}} </td>
    </tr>
    <tr>
      <td>Comments</td>
      <td>{{$feedback_detail->description}} </td>
    </tr>
  </tbody>
</table>
<br />
<?php
       }    
	   if ($account_id != $feedback_detail->account_id )
		   {
		   ?>
   <div class="row">
<form id="replied" action="post">

<div class="form-group">
  <div class="col-sm-12 ">
    <textarea placeholder="Leave your comments" name="description" id="description" class="form-control" rows="4" ></textarea>
  </div>
</div>
<div class="col-sm-2 ">
<button type="submit" class="btn btn-success btn-sm" id="reply">Submit Comments</button>
</div>
</form>
</div>
  <?php }      if(!empty($admin_reply)){?>
  
  <h5>Replied Feedback </h5>
  <div id ="msg"></div>
  <ul class="media-list feedbacklist">
  <?php
 

  foreach($admin_reply as $replys)
  {
  ?>
   <li> <p class="text-info">{{$replys->admin_name}}</p>
        <small class="text-muted text-info"><strong></strong>{{date("j, F Y H:i:s", strtotime($replys->created_on))}}
       Replied To ({{$replys->full_name}}) </small>
         <p>{{$replys->reply_comments}}</p> 
         </li>   <hr />
  <?php
  }
  }
  ?>
</ul>
 
  <h4>Replied Comments </h4>
  <div id ="msg"></div>
  <ul class="media-list feedbacklist">
 
    <?php
	   if(!empty($feedback_reply))
	   {
	   
	   foreach($feedback_reply as $feedback)
	   {
	   ?>           
        <li>
       <strong  class="text-info">{{$feedback->full_name}}</strong> <small class="text-muted">({{date("d-M-Y H:i:s", strtotime($feedback->created_on))}})
        
   	                                            
       <?php /*?> <?php
        if ($feedback->replied_account_types ==0) 
        { 
            if($feedback->relation_id == 1 && $feedback->relative_account_id == 1)
            {
            echo "<span data-hint='Success tooltip' class='label label-warning hint--success hint--success' data-toggle='tooltip' >Company</span>";
            }
        } 
        else if ($feedback->replied_account_types==1) 
        { 
        echo "<span data-hint='Success tooltip' class='label label-success hint--bottom hint--success' data-toggle='tooltip' >Admin</span>";
        }
        ?><?php */?>
         @if($account_id == $feedback->relative_account_id)
        <a href="<?php echo URL::to('/') . '/supplier/feedback/reply/' . $feedback->feedback_reply_id.'/delete';?>" class="delete_btn">{{trans('create_group.delete')}}</a>
         @endif
        </small>
        <p>{{$feedback->reply_comments}}</p>    
        <hr />
        <?php
        }
        }
        ?>
        </li>
  </ul>
{{ HTML::script('supports/member/validator/feedback_list.js') }}
