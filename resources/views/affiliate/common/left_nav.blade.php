<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">            
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li><a href="{{route('aff.dashboard')}}"><i class="fa fa-book"></i> <span>Dashboard</span></a></li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Profile</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">
            <li><a href="{{route('aff.profile')}}"><i class="fa fa-circle-o"></i>My Profile</a></li>
            <li><a href="{{route('aff.profile.kyc')}}"><i class="fa fa-circle-o"></i>KYC Verification</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Settings</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">
            <li><a href="{{route('aff.settings.change_pwd')}}"><i class="fa fa-circle-o"></i>Change Password</a></li>
            <li><a href="{{route('aff.settings.change_securitypin')}}"><i class="fa fa-circle-o"></i>Change Security Password</a></li>
			<li><a href="{{route('aff.settings.change_email')}}"><i class="fa fa-circle-o"></i>Change Email</a></li>
            <li><a href="{{route('aff.settings.payouts')}}"><i class="fa fa-circle-o"></i>Payout Settings</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Package</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">
            <li><a href="{{route('aff.package.browse')}}"><i class="fa fa-circle-o"></i>Purchase Package</a></li>
            <li><a href="{{route('aff.package.my_packages')}}"><i class="fa fa-circle-o"></i>My Packages</a></li>
            <li><a href="{{route('aff.package.upgrade_history')}}"><i class="fa fa-circle-o"></i>Purchase/Upgrade History</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Referrals</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">
        
            <li><a href="{{route('aff.referrals.my_referrals')}}"><i class="fa fa-circle-o"></i>My Directs </a></li>			
            <li><a href="{{route('aff.referrals.my_team')}}"><i class="fa fa-circle-o"></i>My Generation Report</a></li>
            <li><a href="{{route('aff.referrals.my_geneology')}}"><i class="fa fa-circle-o"></i>My Generation View</a></li>            
          </ul>
        </li>        
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Wallet</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">
            <li><a href="{{route('aff.wallet.balance')}}"><i class="fa fa-circle-o"></i>My Wallet</a></li>            
            <li><a href="{{route('aff.wallet.fundtransfer')}}"><i class="fa fa-circle-o"></i>Fund Transfer</a></li>
            <li><a href="{{route('aff.wallet.fundtransfer.history')}}"><i class="fa fa-circle-o"></i>Transfer History</a></li>
            <li><a href="{{route('aff.wallet.transactions')}}"><i class="fa fa-circle-o"></i>Wallet Transactions</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Withdrawals</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">
            <li><a href="{{route('aff.withdrawal.create')}}"><i class="fa fa-circle-o"></i>Create New Request</a></li>
            <li><a href="{{route('aff.withdrawal.history',['status'=>'pending'])}}"><i class="fa fa-circle-o"></i>Pending</a></li>
            <li><a href="{{route('aff.withdrawal.history',['status'=>'processing'])}}"><i class="fa fa-circle-o"></i>Processing</a></li>
            <li><a href="{{route('aff.withdrawal.history',['status'=>'transferred'])}}"><i class="fa fa-circle-o"></i>Transferred</a></li>
            <li><a href="{{route('aff.withdrawal.history',['status'=>'cancelled'])}}"><i class="fa fa-circle-o"></i>Cancelled</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Reports</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">            
            <li><a href="{{route('aff.reports.personal_commission')}}"><i class="fa fa-circle-o"></i>Personal Customer Commission</a></li>
            <li><a href=""><i class="fa fa-circle-o"></i>Ambassador Bonus </a></li>
            <li><a href="{{route('aff.reports.fast_start')}}"><i class="fa fa-circle-o"></i>Fast Start Bonus</a></li>            
            <li><a href="{{route('aff.reports.team_bonus')}}"><i class="fa fa-circle-o"></i>Team Commission</a></li>  
            <li><a href="{{route('aff.reports.leadership')}}"><i class="fa fa-circle-o"></i>Leadership Bonus</a></li> 
            <li><a href=""><i class="fa fa-circle-o"></i>Car Bonus</a></li>            
            <li><a href=""><i class="fa fa-circle-o"></i>Star Bonus</a></li>            
            <li><a href=""><i class="fa fa-circle-o"></i>Rank</a></li>            
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Rank Qualifications</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">            
            <li><a href="{{route('aff.ranks.myrank')}}"><i class="fa fa-circle-o"></i>My Rank</a></li>
            <li><a href="{{route('aff.ranks.history')}}"><i class="fa fa-circle-o"></i>My Rank History</a></li>
            <li><a href="{{route('aff.ranks.eligibilities')}}"><i class="fa fa-circle-o"></i>Ranks Eligibilities</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i> <span>Support Center</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
          <ul class="treeview-menu">
            <li><a href="{{route('aff.support.tickets')}}"><i class="fa fa-circle-o"></i>Tickets</a></li>
            <li><a href="{{route('aff.support.faqs')}}"><i class="fa fa-circle-o"></i>FAQs</a></li>            
            <li><a href="{{route('aff.support.downloads')}}"><i class="fa fa-book"></i> <span>Downloads</span></a></li>
            <li><a href="{{route('aff.support.announcements')}}"><i class="fa fa-book"></i> <span>Announcements</span></a></li>        
          </ul>
        </li>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>