<ul class="media-list comment-list" id="commentlist">
       <li class="media"> <a class="pull-left" href="#"> <img  src="{{URL::asset('assets/members/img/user5.png')}}" alt="" class="media-object thumbnail" /> </a>
                  
           <?php
		   if(!empty($feedback_reply))
		   {
		   ?>
           
                <h4>Replied</h4>
                <small class="text-muted">{{date("d M, Y H:i:s", strtotime($feedback_reply[0]->replied_on))}}
                      <?php
		  if ($feedback_reply[0]->replied_account_types ==0) 
                    { 
					if($feedback_reply[0]->relation_id == 1)
					{
                   echo "<span data-hint='Success tooltip' class='label label-warning hint--success hint--success' data-toggle='tooltip' >Company</span>";
				   }
                    } 
          if ($feedback_reply[0]->replied_account_types==1) 
                    { 
                   echo "<span data-hint='Success tooltip' class='label label-success hint--bottom hint--success' data-toggle='tooltip' >Admin</span>";
              } 
			
		  ?>
                </small>
                <p>{{$feedback_reply[0]->reply_comments}}</p>
               
             
            </li>
          </ul>


<table  class="table table-striped" border="1" BORDERCOLOR="#CCC">
  <thead>                                     
  </thead>
    <tbody>     
        <tr>
          <td>Replied By </td>
          <td><?php
		  if ($feedback_reply[0]->replied_account_types ==0) 
                    { 
					if($feedback_reply[0]->relation_id == 1)
					{
                   echo "<span data-hint='Success tooltip' class='label label-warning hint--success hint--success' data-toggle='tooltip' >Company</span>";
				   }
                    } 
          if ($feedback_reply[0]->replied_account_types==1) 
                    { 
                   echo "<span data-hint='Success tooltip' class='label label-success hint--bottom hint--success' data-toggle='tooltip' >Admin</span>";
              } 
			
		  ?>
		  </td>              
        </tr>
        <tr>
          <td>Description</td>
          <td><p>{{$feedback_reply[0]->reply_comments}}</p></td>              
        </tr>
        <tr>
          <td>Replied On </td>
          <td>{{$feedback_reply[0] ->replied_on}}</td>              
        </tr>

         
        
      
        
        <?php if(isset($feedback_reply )){?>  
         <tr>
          <td>Status</td>
          <td>
    
          <?php 
           if ($feedback_reply[0]->relation_id==0) 
                    { 
                   echo "<span data-hint='Success tooltip' class='label label-warning hint--bottom hint--warning' data-toggle='tooltip' >New</span>";
                    } 
          if ($feedback_reply[0]->relation_id==1) 
                    { 
                   echo "<span data-hint='Success tooltip' class='label label-success hint--bottom hint--success' data-toggle='tooltip' >Replied</span>";
                    } 

					
                    } ?>
                  </td>
                  </tr>
                    
                                
    </tbody>
</table>
<?php
}
?>