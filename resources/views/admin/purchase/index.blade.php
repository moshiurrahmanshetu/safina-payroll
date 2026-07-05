@extends('layouts.admin')
@section('title', 'Purchase List')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>Purchase List </h3></div>
  <div class="col-sm-12 col-md-7"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>
          <td>{{Form::select('supplier_id',array(''=>'Select Supplier Com.')+$supllier_lists,$search_array['supplier_id'],array('class' => 'form-control'))}}</td> 
          <?php if($search_array['start_date']){ $s_date=date('d-m-Y',strtotime($search_array['start_date'])); }else{ $s_date=''; }
          if($search_array['end_date']){ $e_date=date('d-m-Y',strtotime($search_array['end_date'])); }else{ $e_date=''; } ?>
          <td> {{Form::text('start_date',$s_date,array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
          <td> {{Form::text('end_date',$e_date,array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td>
          <td><span class="input-group-btn">
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button>
          </span>
        </td>
        @if($search_array['start_date']!='')
        <td> 
          <!-- <a class="btn btn-info pull-right txt_white" target="_blank" href="{{ route('purchase_print',['download'=>'purchase','start_date'=>$search_array['start_date'],'end_date'=>$search_array['end_date']]) }}">Print</a> -->
        </td>
        @endif
      </tr>
    </table> 
  </form> </h3>
</div>
<div class="col-sm-12 col-md-2"><h1>
  {{link_to_route('purchase.create','New Purchase',[],array('class'=>'btn btn-success pull-right'))}} </h1>
</div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>
            <th>Supplier Name(Company)</th>
            <th>Mobile</th>
            <th>Purchase Date</th>
            <th>Purch. Items (Qty.)</th>
            <th>Grand Total TK</th>
            <th>Due / Paid TK</th>
            <!-- <th>PO Number</th> -->
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; ;@endphp  
          @foreach ($purchases as $data)
          <tr>
            <td>{{$i}}</td>
            <td>{!!$data->supplier->contact_name!!}({!!$data->supplier->company_name!!})</td>
            <td>{!!$data->supplier->mobile!!}</td>
            <td>{{date('d-m-Y',strtotime($data->purchase_date))}}</td> 
            <td>
              @foreach($data->purchase_items as $items)
                {!!$items->name!!} ({!!$items->quantity+0!!})<br> 
              @endforeach
            </td>
            <td>{!!$data->grand_total+0!!}</td>
            <td>
              @if($data->purchase_transactions)
                @foreach($data->purchase_transactions as $item)
                  @php $due=$data->grand_total-$item->amount; @endphp
                  @if($due == (int)$due)
                    {{$due}}
                  @else
                    {{number_format($due,2)}}
                  @endif
                  / {{$item->amount+0}}
                @endforeach
              @else
                {!!$data->grand_total+0!!}
              @endif
            </td>
            <!-- <td>{!!$data->po_number!!}</td> -->
            <td><strong class="btn-{{ config('myhelpers.purchase_status_color')[$data->status] }}">{{config('myhelpers.purchase_status')[$data->status]}}</strong></td>
            <td>
              {{link_to_route('purchase_transaction.create','+ TK ',['id'=>$data->id],array('class'=>'btn btn-primary pull-center'))}}
              {!! HTML::decode(link_to_route('purchase.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
              {{ Form::open(array('route' => array('purchase.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
              <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
              {{ Form::close() }}
            </td>
          </tr>
          @php $i=$i+1; @endphp
          @endforeach
        </tbody>

      </table>
    </div>
  </div>
</div>

@endsection