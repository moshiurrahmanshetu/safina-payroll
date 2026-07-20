<?php
    $current_location=class_basename(Route::currentRouteAction());
?>

<nav class="sidebar-nav no-print">
  <ul class="nav">

    <li class="nav-item">
      <a class="nav-link" href="<?php echo e(route('dashboard')); ?>">
        <i class="nav-icon icon-speedometer"></i> Dashboard
      </a>
    </li>
    <?php if(checkMenuActive(['RoleController@create','RoleController@index','RegisterController@create','RegisterController@showUserLists','DesignationController@create','DepartmentController@create','SiteSettingController@edit'],$menu_list)): ?>
    <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-user"></i> Master Settings</a>
      <ul class="nav-dropdown-items">
        <?php if(checkMenuActive('RoleController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('roles.create')); ?>"><i class="nav-icon icon-note"></i> Role Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('RoleController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('roles.index')); ?>"><i class="nav-icon icon-list"></i> Role lists</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('RegisterController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('users.create')); ?>"><i class="nav-icon icon-note"></i> User Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('RegisterController@showUserLists',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('users.index')); ?>"><i class="nav-icon icon-list"></i> User lists</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('DesignationController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('designation.create')); ?>">
            <i class="nav-icon icon-note"></i> Designation Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('DepartmentController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('department.create')); ?>">
            <i class="nav-icon icon-note"></i> Department Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('SiteSettingController@edit',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('site_settings.edit', 1)); ?>">
            <i class="nav-icon icon-note"></i> Edit Site Settings</a>
        </li>
        <?php endif; ?>             
      </ul>
    </li>
    <?php endif; ?>

    <?php if(checkMenuActive(['SupplierController@create','SupplierController@index'],$menu_list)): ?>   
     <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-people"></i> Supplier Management</a>
      <ul class="nav-dropdown-items">
        <?php if(checkMenuActive('SupplierController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('supplier.create')); ?>">
            <i class="nav-icon icon-note"></i> Supplier Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('SupplierController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('supplier.index')); ?>"><i class="nav-icon icon-list"></i> Supplier lists</a>
        </li> 
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>
    <?php if(checkMenuActive(['CategoryController@create','ItemController@create','ItemController@index'],$menu_list)): ?>   
    <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-magic-wand"></i> Product Management</a>
      <ul class="nav-dropdown-items"> 
        <?php if(checkMenuActive('CategoryController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('category.create')); ?>"><i class="nav-icon icon-note"></i> Category Create/List</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('ItemController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('item.create')); ?>"><i class="nav-icon icon-note"></i> Product Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('ItemController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('item.index')); ?>">
            <i class="nav-icon icon-list"></i> Product lists</a>
        </li> 
        <?php endif; ?>      
      </ul>
    </li>
    <?php endif; ?>
    <?php if(checkMenuActive(['PurchaseController@create','PurchaseController@index'],$menu_list)): ?>   
    <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-basket-loaded"></i> Purchase Management</a>
      <ul class="nav-dropdown-items">
        <?php if(checkMenuActive('PurchaseController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('purchase.create')); ?>"><i class="nav-icon icon-note"></i> Purchase Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('PurchaseController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('purchase.index')); ?>"><i class="nav-icon icon-list"></i> Purchase lists</a>
        </li> 
        <?php endif; ?> 
        <?php if(checkMenuActive('PurchaseTransactionController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('purchase_transaction.index')); ?>">
            <i class="nav-icon icon-list"></i>Purchase Transaction lists</a>
        </li> 
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <?php if(checkMenuActive(['WarehouseController@create','StockInController@create','StockInController@index','StockInController@stock_summary'],$menu_list)): ?>   
    <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-basket-loaded"></i> Inventory Management</a>
      <ul class="nav-dropdown-items">        
        <?php if(checkMenuActive('WarehouseController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('warehouse.create')); ?>">
            <i class="nav-icon icon-note"></i>Warehouse Create/List</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('StockInController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('stock_in.create')); ?>">
            <i class="nav-icon icon-note"></i>WH Stock In Create</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('StockInController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('stock_in.index')); ?>">
            <i class="nav-icon icon-list"></i>WH Stock In lists</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('StockInController@stock_summary',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('stock_summary')); ?>">
            <i class="nav-icon icon-list"></i>Stock Summary lists</a>
        </li> 
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?> 

    <?php if(checkMenuActive(['RequisitionController@create','RequisitionController@index','RequisitionController@counter_sign_list','RequisitionController@admin_requisition_summary','RequisitionController@admin_requisition_list','PurposeController@create', 'RequisitionController@item_wise_requisition'],$menu_list)): ?>
    <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-basket-loaded"></i>Requisition Management</a>
      <ul class="nav-dropdown-items">
        <?php if(checkMenuActive('PurposeController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('purpose.create')); ?>"><i class="nav-icon icon-note"></i>Purpose Create/List</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('RequisitionController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('requisition.create')); ?>"><i class="nav-icon icon-note"></i>Requisition Request</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('RequisitionController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('requisition.index')); ?>"><i class="nav-icon icon-list"></i>Requisition lists</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('RequisitionController@counter_sign_list',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('counter_sign_list')); ?>"><i class="nav-icon icon-list"></i>Counter Sign Lists</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('RequisitionController@admin_requisition_list',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('admin_requisition_list')); ?>"><i class="nav-icon icon-list"></i>Admin Requisition Lists</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('RequisitionController@admin_requisition_summary',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('admin_requisition_summary')); ?>"><i class="nav-icon icon-list"></i>Admin Requisition Summary</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('RequisitionController@item_wise_requisition',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('item_wise_requisition')); ?>"><i class="nav-icon icon-list"></i>Item Wise Requisition</a>
        </li> 
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <?php if(checkMenuActive(['IndentController@indent_list','IndentController@admin_indent_list'],$menu_list)): ?>
    <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-basket-loaded"></i>Returnable Items Management</a>
      <ul class="nav-dropdown-items">
        <?php if(checkMenuActive('IndentController@indent_list',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('indent_list')); ?>"><i class="nav-icon icon-list"></i>Returnable Items Lists</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('IndentController@admin_indent_list',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('admin_indent_list')); ?>"><i class="nav-icon icon-list"></i>All Returnable Items Lists</a>
        </li> 
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <?php if(checkMenuActive(['MrsItemController@create','MrsItemController@index','MrsItemController@mrs_item_summary','MrsItemController@my_mrs_item_list'],$menu_list)): ?>
    <li class="nav-item nav-dropdown">
      <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon icon-basket-loaded"></i>MRS Item Management</a>
      <ul class="nav-dropdown-items">
        <?php if(checkMenuActive('MrsItemController@my_mrs_item_list',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('my_mrs_item_list')); ?>"><i class="nav-icon icon-list"></i>My MRS Item List</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('MrsItemController@create',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('mrs_item.create')); ?>"><i class="nav-icon icon-note"></i>MRS Item Receive</a>
        </li>
        <?php endif; ?>
        <?php if(checkMenuActive('MrsItemController@index',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('mrs_item.index')); ?>"><i class="nav-icon icon-list"></i>MRS Item Lists</a>
        </li> 
        <?php endif; ?>
        <?php if(checkMenuActive('MrsItemController@mrs_item_summary',$menu_list)): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo e(route('mrs_item_summary')); ?>"><i class="nav-icon icon-list"></i>MRS Item Summary</a>
        </li>
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <?php if(checkMenuActive(['TicketController@create', 'TicketController@index', 'GateController@create', 'GateController@index', 'TicketSaleController@create', 'TicketSaleController@index', 'TicketSaleController@scan', 'TicketSaleController@report','TicketSaleController@validationForm', 'TicketCashHandoverController@index', 'TicketCashHandoverController@approval'], $menu_list)): ?>

      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon icon-tag"></i> Ticket Management</a>
        <ul class="nav-dropdown-items">
          <?php if(checkMenuActive('TicketController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('tickets.create')); ?>">
                <i class="nav-icon icon-note"></i>Ticket Type Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TicketController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('tickets.index')); ?>">
                <i class="nav-icon icon-list"></i>Ticket Type lists</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('GateController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('gates.create')); ?>">
                <i class="nav-icon icon-note"></i>Ticket Counter Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('GateController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('gates.index')); ?>">
                <i class="nav-icon icon-list"></i>Ticket Counter lists</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TicketSaleController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('ticket_sales.create')); ?>">
                <i class="nav-icon icon-note"></i>Ticket Sale Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TicketSaleController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('ticket_sales.index')); ?>">
                <i class="nav-icon icon-list"></i>Ticket Sale lists</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TicketSaleController@report', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('ticket_sales.report')); ?>">
              <i class="nav-icon icon-chart"></i>Ticket Sales Report</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TicketSaleController@scan', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('ticket_sales.scan')); ?>">
              <i class="nav-icon icon-camera"></i>Ticket Scan</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TicketCashHandoverController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('ticket_cash_handovers.index')); ?>">
              <i class="nav-icon icon-wallet"></i>Ticket Cash Handover</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TicketCashHandoverController@approval', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('ticket_cash_handovers.approval')); ?>">
              <i class="nav-icon icon-check"></i>Ticket Cash Approval</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

    
    <?php if(checkMenuActive(['ServiceCategoryController@index', 'ServiceController@create', 'ServiceController@index', 'BookingController@create', 'BookingController@index', 'PricingRuleController@index', 'DiscountRuleController@index', 'AmenityController@index', 'AmenityController@create', 'BookingCashHandoverController@index', 'BookingCashHandoverController@approval'], $menu_list)): ?>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon icon-calendar"></i> Service & Booking</a>
        <ul class="nav-dropdown-items">
          <?php if(checkMenuActive('ServiceCategoryController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('service_categories.index')); ?>">
                <i class="nav-icon icon-tag"></i>Service Categories</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ServiceController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('services.create')); ?>">
                <i class="nav-icon icon-note"></i>Service Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ServiceController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('services.index')); ?>">
                <i class="nav-icon icon-list"></i>Service lists</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('BookingController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('bookings.create')); ?>">
                <i class="nav-icon icon-note"></i>Booking Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('BookingController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('bookings.index')); ?>">
                <i class="nav-icon icon-list"></i>Booking lists</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('AmenityController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('amenities.create')); ?>">
                <i class="nav-icon icon-note"></i>Amenity Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('AmenityController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('amenities.index')); ?>">
                <i class="nav-icon icon-list"></i>Amenity lists</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('bookings.availability.calendar')); ?>">
              <i class="nav-icon icon-calendar"></i>Availability Calendar</a>
          </li>
          <?php if(checkMenuActive('PricingRuleController@index', $menu_list)): ?>
          
          
          <?php endif; ?>
          <?php if(checkMenuActive('DiscountRuleController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('discount_rules.index')); ?>">
                <i class="nav-icon icon-tag"></i>Discount Rules</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('TimeSlotController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('time-slots.index')); ?>">
                <i class="nav-icon icon-clock"></i>Time Slots</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('CounterController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('counters.index')); ?>">
                <i class="nav-icon icon-screen-desktop"></i>Counters</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('bookings.counter_report')); ?>">
              <i class="nav-icon icon-chart"></i>Counter Report</a>
          </li>
          <?php if(checkMenuActive('BookingCashHandoverController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('booking_cash_handovers.index')); ?>">
              <i class="nav-icon icon-wallet"></i>Booking Cash Handover</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('BookingCashHandoverController@approval', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('booking_cash_handovers.approval')); ?>">
              <i class="nav-icon icon-check"></i>Booking Cash Approval</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

     <?php if(checkMenuActive(['PackageController@create', 'PackageController@index', 'PackageBookingController@create', 'PackageBookingController@index', 'PackageBookingController@showScanForm', 'PackageReportController@index', 'PackageCounterController@index', 'PackageCounterController@create', 'PackageBookingController@showScanForm', 'PackageCashHandoverController@index', 'PackageCashHandoverController@approval'], $menu_list)): ?>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon icon-grid"></i> Package Management</a>
        <ul class="nav-dropdown-items">
          <?php if(checkMenuActive('PackageController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('packages.create')); ?>">
                <i class="nav-icon icon-note"></i>Package Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PackageController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('packages.index')); ?>">
                <i class="nav-icon icon-list"></i>Package Lists</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PackageBookingController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('package_bookings.create')); ?>">
                <i class="nav-icon icon-note"></i>Package Sale Create</a>
            </li>
          <?php endif; ?>
            <?php if(checkMenuActive('PackageBookingController@index', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('package_bookings.index')); ?>">
                  <i class="nav-icon icon-list"></i>Package Sale Lists</a>
              </li>
            <?php endif; ?>
             <?php if(checkMenuActive('PackageCounterController@create', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('package_counters.create')); ?>">
                  <i class="nav-icon icon-note"></i>Package Counter Create</a>
              </li>
            <?php endif; ?>
             <?php if(checkMenuActive('PackageCounterController@index', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('package_counters.index')); ?>">
                  <i class="nav-icon icon-list"></i>Package Counter Lists</a>
              </li>
            <?php endif; ?>
            <?php if(checkMenuActive('PackageReportController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('package_reports.index')); ?>">
              <i class="nav-icon icon-chart"></i>Package Reports</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PackageBookingController@showScanForm', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('package_bookings.scan_form')); ?>">
              <i class="nav-icon icon-camera"></i>Package QR Scan</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PackageCashHandoverController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('package_cash_handovers.index')); ?>">
              <i class="nav-icon icon-wallet"></i>Package Cash Handover</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PackageCashHandoverController@approval', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('package_cash_handovers.approval')); ?>">
              <i class="nav-icon icon-check"></i>Package Cash Approval</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

     <?php if(checkMenuActive(['ParkingTicketController@create', 'ParkingTicketController@index', 'ParkingReportController@index', 'ParkingTicketController@scanCamera', 'VehicleController@index', 'ParkingCounterController@index', 'ParkingCounterController@create', 'ParkingCashHandoverController@index', 'ParkingCashHandoverController@approval'], $menu_list)): ?>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon fa fa-car"></i> Parking Management</a>
        <ul class="nav-dropdown-items">
          <?php if(checkMenuActive('VehicleController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('vehicles.index')); ?>">
                <i class="nav-icon fa fa-truck"></i>Vehicle Types</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingCounterController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('parking_counters.create')); ?>">
                <i class="nav-icon icon-note"></i>Parking Counter Create</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingCounterController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('parking_counters.index')); ?>">
                <i class="nav-icon icon-list"></i>Parking Counters</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingTicketController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('parking_tickets.create')); ?>">
                <i class="nav-icon icon-note"></i>Create Parking Ticket</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingTicketController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('parking_tickets.index')); ?>">
                <i class="nav-icon icon-list"></i>Parking Ticket List</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingReportController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('parking_reports.index')); ?>">
              <i class="nav-icon icon-chart"></i>Parking Reports</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingTicketController@scanCamera', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('parking_tickets.scan_camera')); ?>">
              <i class="nav-icon icon-camera"></i>Scan with Camera</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingCashHandoverController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('parking_cash_handovers.index')); ?>">
              <i class="nav-icon icon-wallet"></i>Parking Cash Handover</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ParkingCashHandoverController@approval', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('parking_cash_handovers.approval')); ?>">
              <i class="nav-icon icon-check"></i>Parking Cash Approval</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>
    
    <!-- Water Park Management Menu -->
    <?php if(checkMenuActive(['WaterParkTicketController@create', 'WaterParkTicketController@index', 'WaterParkTicketController@scanCamera', 'WaterParkSettingController@edit', 'WaterParkCounterController@index', 'WaterParkCashHandoverController@index', 'WaterParkCashHandoverController@approval'], $menu_list)): ?>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon icon-drop"></i> Water Park Management</a>
        <ul class="nav-dropdown-items">
          <?php if(checkMenuActive('WaterParkTicketController@create', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('water_park_tickets.create')); ?>">
                <i class="nav-icon icon-note"></i>Create Ticket</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WaterParkTicketController@index', $menu_list)): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('water_park_tickets.index')); ?>">
                <i class="nav-icon icon-list"></i>Ticket List</a>
            </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WaterParkTicketController@scanCamera', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('water_park_tickets.scan_camera')); ?>">
              <i class="nav-icon icon-camera"></i>Scan with Camera</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WaterParkCounterController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('water_park_counters.index')); ?>">
              <i class="nav-icon icon-drawer"></i>Counters</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WaterParkSettingController@edit', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('water_park_settings.edit')); ?>">
              <i class="nav-icon icon-settings"></i>Settings</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WaterParkCashHandoverController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('water_park_cash_handovers.index')); ?>">
              <i class="nav-icon icon-wallet"></i>Water Park Cash Handover</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WaterParkCashHandoverController@approval', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('water_park_cash_handovers.approval')); ?>">
              <i class="nav-icon icon-check"></i>Water Park Cash Approval</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>
    
    <!-- Locker & Gear Management Menu -->
    <?php if(checkMenuActive(['LockerItemController@index', 'GearItemController@index', 'ItemPricingController@index', 'LockerGearTicketController@index', 'LockerGearTicketController@create', 'LockerGearTicketController@scanCamera', 'LockerGearReportController@index', 'LockerGearCounterController@index', 'LockerGearCashHandoverController@index', 'LockerGearCashHandoverController@approval'], $menu_list)): ?>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon icon-handbag"></i> Locker & Gear</a>
        <ul class="nav-dropdown-items">
          <?php if(checkMenuActive('LockerGearTicketController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_tickets.create')); ?>">
              <i class="nav-icon icon-note"></i>Create Ticket</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearTicketController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_tickets.index')); ?>">
              <i class="nav-icon icon-list"></i>Ticket List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearTicketController@scanCamera', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_tickets.scan_camera')); ?>">
              <i class="nav-icon icon-camera"></i>Scan with Camera</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearReportController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_reports.index')); ?>">
              <i class="nav-icon icon-chart"></i>Reports</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearReportController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_reports.stock_report')); ?>">
              <i class="nav-icon icon-drawer"></i>Stock</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearReportController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_reports.active_rentals')); ?>">
              <i class="nav-icon icon-clock"></i>Active Rentals</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearReportController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_reports.overdue')); ?>">
              <i class="nav-icon icon-exclamation"></i>Overdue</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearCounterController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_counters.index')); ?>">
              <i class="nav-icon icon-drawer"></i>Counters</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerItemController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_items.index')); ?>">
              <i class="nav-icon fa fa-lock"></i>Lockers</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('GearItemController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('gear_items.index')); ?>">
              <i class="nav-icon icon-bag"></i>Gear Items</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ItemPricingController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('item_pricings.index')); ?>">
              <i class="nav-icon fa fa-tags"></i>Pricing</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearCashHandoverController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_cash_handovers.index')); ?>">
              <i class="nav-icon icon-wallet"></i>Locker & Gear Cash Handover</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('LockerGearCashHandoverController@approval', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('locker_gear_cash_handovers.approval')); ?>">
              <i class="nav-icon icon-check"></i>Locker & Gear Cash Approval</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

    <!-- HR & Payroll Management Menu -->
    <?php if(checkMenuActive(['PermanentEmployeeController@create', 'PermanentEmployeeController@index', 'PermanentEmployeeController@edit', 'WorkAreaController@create', 'WorkAreaController@index', 'WorkAreaController@edit', 'DailyWorkerController@create', 'DailyWorkerController@index', 'DailyWorkerController@edit', 'ContractWorkerController@create', 'ContractWorkerController@index', 'ContractWorkerController@edit', 'SalaryStructureController@create', 'SalaryStructureController@index', 'SalaryStructureController@edit', 'PayrollController@create', 'PayrollController@index', 'PayrollController@edit', 'AttendanceController@create', 'AttendanceController@index', 'AttendanceController@edit'], $menu_list)): ?>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon icon-people"></i> HR & Payroll</a>
        <ul class="nav-dropdown-items">
          <?php if(checkMenuActive('PermanentEmployeeController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('permanent_employees.create')); ?>">
              <i class="nav-icon icon-note"></i>Add Permanent Employee</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PermanentEmployeeController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('permanent_employees.index')); ?>">
              <i class="nav-icon icon-list"></i>Permanent Employees List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WorkAreaController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('work_areas.create')); ?>">
              <i class="nav-icon icon-note"></i>Add Work Area</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('WorkAreaController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('work_areas.index')); ?>">
              <i class="nav-icon icon-list"></i>Work Areas List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('DailyWorkerController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('daily_workers.create')); ?>">
              <i class="nav-icon icon-note"></i>Add Daily Worker</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('DailyWorkerController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('daily_workers.index')); ?>">
              <i class="nav-icon icon-list"></i>Daily Workers List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ContractWorkerController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('contract_workers.create')); ?>">
              <i class="nav-icon icon-note"></i>Add Contract Worker</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ContractWorkerController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('contract_workers.index')); ?>">
              <i class="nav-icon icon-list"></i>Contract Workers List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('SalaryStructureController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('salary_structures.create')); ?>">
              <i class="nav-icon icon-note"></i>Add Salary Structure</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('SalaryStructureController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('salary_structures.index')); ?>">
              <i class="nav-icon icon-list"></i>Salary Structures List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('SalaryRevisionController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('salary_revisions.create')); ?>">
              <i class="nav-icon icon-note"></i>New Salary Revision</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('SalaryRevisionController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('salary_revisions.index')); ?>">
              <i class="nav-icon icon-list"></i>Salary Revisions List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('SalaryController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('salaries.index')); ?>">
              <i class="nav-icon icon-clock"></i>Salary History</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('SalaryDisbursementController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('salary_disbursements.index')); ?>">
              <i class="nav-icon icon-wallet"></i>Salary Disbursements</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('AttendanceController@daily', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('attendances.daily')); ?>">
              <i class="nav-icon icon-note"></i>Daily Attendance Entry</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('AttendanceController@bulk', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('attendances.bulk')); ?>">
              <i class="nav-icon icon-grid"></i>Bulk Attendance Editor</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('AttendanceController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('attendances.index')); ?>">
              <i class="nav-icon icon-list"></i>Attendance List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('ShiftController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('shifts.index')); ?>">
              <i class="nav-icon icon-clock"></i>Shift Management</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('EmployeeShiftController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('employee_shifts.index')); ?>">
              <i class="nav-icon icon-user"></i>Employee Shift Assignment</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('FingerprintLogController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('fingerprint_logs.index')); ?>">
              <i class="nav-icon icon-fingerprint"></i>Fingerprint Logs</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('FingerprintSessionController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('fingerprint_sessions.index')); ?>">
              <i class="nav-icon icon-clock"></i>Fingerprint Sessions</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('FingerprintAttendanceController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('fingerprint_attendance.index')); ?>">
              <i class="nav-icon icon-calculator"></i>Generate Attendance</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('DailyAttendanceController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('daily_attendance.index')); ?>">
              <i class="nav-icon icon-calendar"></i>Daily Attendance</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive(['AttendanceReportController@index','AttendanceReportController@employeeDaily','AttendanceReportController@employeeMonthly','AttendanceReportController@dailyRegister','AttendanceReportController@monthlyRegister','AttendanceReportController@lateReport','AttendanceReportController@departmentReport','AttendanceReportController@shiftReport'], $menu_list)): ?>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
              <i class="nav-icon icon-chart"></i>Attendance Reports</a>
            <ul class="nav-dropdown-items">
              <?php if(checkMenuActive('AttendanceReportController@employeeDaily', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('attendance_reports.employee_daily')); ?>">
                  <i class="nav-icon icon-user"></i>Employee Daily</a>
              </li>
              <?php endif; ?>
              <?php if(checkMenuActive('AttendanceReportController@employeeMonthly', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('attendance_reports.employee_monthly')); ?>">
                  <i class="nav-icon icon-calendar"></i>Employee Monthly</a>
              </li>
              <?php endif; ?>
              <?php if(checkMenuActive('AttendanceReportController@dailyRegister', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('attendance_reports.daily_register')); ?>">
                  <i class="nav-icon icon-users"></i>Daily Register</a>
              </li>
              <?php endif; ?>
              <?php if(checkMenuActive('AttendanceReportController@monthlyRegister', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('attendance_reports.monthly_register')); ?>">
                  <i class="nav-icon icon-calendar-check-o"></i>Monthly Register</a>
              </li>
              <?php endif; ?>
              <?php if(checkMenuActive('AttendanceReportController@lateReport', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('attendance_reports.late_report')); ?>">
                  <i class="nav-icon icon-clock-o"></i>Late Report</a>
              </li>
              <?php endif; ?>
              <?php if(checkMenuActive('AttendanceReportController@departmentReport', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('attendance_reports.department_report')); ?>">
                  <i class="nav-icon icon-building"></i>Department Report</a>
              </li>
              <?php endif; ?>
              <?php if(checkMenuActive('AttendanceReportController@shiftReport', $menu_list)): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('attendance_reports.shift_report')); ?>">
                  <i class="nav-icon icon-clock"></i>Shift Report</a>
              </li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PayrollController@create', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payrolls.create')); ?>">
              <i class="nav-icon icon-note"></i>Generate Payroll</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PayrollController@index', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payrolls.index')); ?>">
              <i class="nav-icon icon-list"></i>Payroll List</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PayrollController@approval', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payrolls.approval')); ?>">
              <i class="nav-icon icon-check"></i>Payroll Approval</a>
          </li>
          <?php endif; ?>
          <?php if(checkMenuActive('PayrollController@approved', $menu_list)): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payrolls.approved')); ?>">
              <i class="nav-icon icon-check-circle"></i>Approved Payroll</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

  </ul>
</nav><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/nav.blade.php ENDPATH**/ ?>