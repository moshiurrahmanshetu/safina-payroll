@extends('layouts.admin')

@section('content')
<div class="row">
  @if (session('status'))
  <div class="alert alert-success" role="alert">
    {{ session('status') }}
  </div>
  @endif
  <h1 class="overview-heading">Overview of My Dashboard</h1>
</div>

<div class="animated fadeIn">
  <div class="row">
    <!-- /.col-->
    @if(checkMenuActive(['RegisterController@showUserLists'],$menu_list))
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          @php $total=0; @endphp
          @foreach($users as $data)
            @php $total+=$data->total; @endphp
            <div class="text-value">{{$data->total}} </div>
            <div>Number of @if($data->status==0) Inactive @else Active @endif Users</div>
            <hr>
          @endforeach
          <div class="text-value"># {{$total}}</div>
          <div>Number of Total Users</div>
        </div>
      </div>
    </div>
    @endif
    @if(checkMenuActive(['DesignationController@create','DepartmentController@create'],$menu_list))
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <div class="text-value"># {{$designations}}</div>
          <div>Number of Designations</div>
          <hr>
          <div class="text-value"># {{$departments}} </div>
          <div>Number of Departments</div>
        </div>
      </div>
    </div>
    @endif
    @if(checkMenuActive(['CategoryController@create','ItemController@index'],$menu_list))
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white" style="background-color:#6a0dad;">
        <div class="card-body">
          <div class="text-value"># {{$categories}}</div>
          <div>Number of Categories</div>
          <hr>
          <div class="text-value"># {{$products}}</div>
          <div>Number of Products</div>
        </div>
      </div>
    </div>
    @endif
    <!-- /.col-->
    @if(checkMenuActive(['SupplierController@index','PurchaseController@index'],$menu_list))
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white" style="background-color:#0c4da2;">
        <div class="card-body">
          <div class="text-value"># {{$suppliers}}</div>
          <div>Number of Suppliers</div>
          <hr>
          <div class="text-value"># {{$purchases}} </div>
          <div>Number of Purchase</div>
        </div>
      </div>
    </div>
    @endif
    <!-- /.col-->
    @if(checkMenuActive(['RequisitionController@index','IndentController@indent_list'],$menu_list))
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white" style="background-color:#139381;">
        <div class="card-body">
          <div class="text-value"># {{$myrequisitions}}</div>
          <div>Number of My Requisitions</div>
          <hr>
          <div class="text-value"># {{$my_indent}}</div>
          <div>Number of My Indents</div>
          <hr>
          <div class="text-value"># {{$my_mrs_items}}</div>
          <div>Number of My MRS Items</div>
        </div>
      </div>
    </div>
    @endif
    <!-- /.col-->
    @if(checkMenuActive(['RequisitionController@admin_requisition_list','IndentController@admin_indent_list'],$menu_list))
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <div class="text-value"># {{$requisitions}}</div>
          <div>Number of Total Requisitions</div>
          <hr>
          <div class="text-value"># {{$all_indent}}</div>
          <div>Number of Total Indents</div>
          <hr>
          <div class="text-value"># {{$mrs_items}}</div>
          <div>Number of Total MRS Items</div>
        </div>
      </div>
    </div>
    @endif
    <!-- /.col-->
    @if(checkMenuActive(['StockInController@low_stock_reminder'],$menu_list))
    <div class="col-sm-12 col-md-6"><h3>Low Stock Reminder <a class="pull-right" href="{{ route('low_stock_reminder') }}">View All</a></h3>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <td>#</td>
              <th>Product Name</th>
              <th>Balance</th>
              <th class="text-center">Low Stock Value</th>
            </tr>
          </thead>
          <tbody id="low_stock_load">

          </tbody>
        </table>
      </div>
    </div>
    @endif

  </div>
  <!-- /.row-->

</div>

@endsection

@section('script')
<script src="{{asset('public/js/custom-tooltips.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
  var req_item_id='';
  $.ajax({
    type: "get",
    url:"{{ route('ajax.lowstock_summary') }}",
    data:{req_item_id:req_item_id},
    success: function(data){
      if(data){
        $.each(data, function(key, value) {
          var html='';
          html='<tr><td>'+key+'</td><td>'+value.name+'</td><td>'+value.balance+'</td><td>'+value.low_stock+'</td></tr>';
          $('#low_stock_load').append($(html));
        });
      }
    }
  });
});
</script>
@endsection