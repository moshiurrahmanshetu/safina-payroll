@extends('layouts.admin')
@section('title', 'Product List')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-4"><h3>Product List </h3></div>
  <div class="col-sm-12 col-md-6">
    <form action="" method="GET" role="search">
    {{ csrf_field() }}
    <div class="input-group">
      <input type="text" class="form-control" name="search"
        placeholder="Search "> 
    <span class="input-group-btn">
        <button type="submit" class="btn btn-default search_btn"><i class="nav-icon icon-magnifier"></i> 
        </button>
      </span>
    </div>
    </form> 
  </div>
  <div class="col-sm-12 col-md-2"><h1>
  {{link_to_route('item.create','Create New Product',[],array('class'=>'btn btn-success pull-right'))}} </h1>
  </div>  
</div>
{{ session()->get('langsname') }}

<div class="row">
  <div class="col-sm-12 col-md-12">        
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>                
            <th>Product Name</th>  
            <th>Product Category</th>
            <th>Measuring Unit</th>          
            <th>Status</th>       
            <th>Item Img</th>      
            <th>Action</th>   
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp  
          @foreach ($items as $data)
          <tr>
            <td>{{$i}}</td>
            <td>{!!$data->name!!}</td>
            <td>{{$data->category->name}}</td>
           <td>{{config('myhelpers.measuring_unit')[$data->measuring_unit]}}</td>
            <td>{{config('myhelpers.status')[$data->status]}}</td>
            <td>@if($data->item_img)
              <img src="{{ asset('storage/app/admin/item/'.$data->item_img) }}" alt="" width="60px" height="60px">
            @endif </td>                                
            <td>              
              {!!HTML::decode(link_to_route('item.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}      
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