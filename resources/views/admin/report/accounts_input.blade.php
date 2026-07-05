@extends('layouts.admin')
@section('title', 'Total Statement')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3 class="txt_green">Total Statement </h3></div>
  <div class="col-sm-12 col-md-7"><h3>
    <form action="" method="GET" role="search" >
    <table class="table table-borderless">
        <tr>  
          <td> {{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
           <td> {{Form::text('end_date',$search_array['end_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td> 
           <td><span class="input-group-btn">   
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button>
            </span>
          </td>
       </tr>
      </table> 
    </form> </h3>
  </div>
  
</div>

@endsection