@extends('layouts.admin')
@section('title', 'Ticket Sale Create')
@section('content')
<h3 class="page-header">Ticket Sale Create {{link_to_route('ticket_sales.index','Ticket Sale List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model(Request::old(),array('route' => array('ticket_sales.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal','id'=>'ticket_sale_form')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Select Ticket * <span class="text-success"><b>( Counter: {{$gateName}})</b></span></label>
        <select name="ticket_id" id="ticket_id" class="form-control" required onchange="loadTicketPrice()">
          <option value="">Select Ticket</option>
          @foreach($tickets as $id => $name)
            <option value="{{ $id }}" data-name="{{ $name }}">{{ $name }}</option>
          @endforeach
        </select>
        {!! $errors->first('ticket_id', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Price (per ticket) *</label>
        {{Form::number('price',null, array('class' => 'form-control','required'=>'required','step'=>'0.01','id'=>'price','readonly'=>'readonly'))}}
        {!! $errors->first('price', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Quantity *</label>
        {{Form::number('quantity',1, array('class' => 'form-control','required'=>'required','min'=>'1','id'=>'quantity','onchange'=>'calculateTotal()','onkeyup'=>'calculateTotal()'))}}
        {!! $errors->first('quantity', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Total Price</label>
        {{Form::number('total_price',null, array('class' => 'form-control','readonly'=>'readonly','step'=>'0.01','id'=>'total_price'))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Discount Amount</label>
        {{Form::number('discount_amount',0, array('class' => 'form-control','min'=>'0','step'=>'0.01','id'=>'discount_amount','onchange'=>'calculateTotal()','onkeyup'=>'calculateTotal()'))}}
        
        {!! $errors->first('discount_amount', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Final Price</label>
        {{Form::number('final_price',null, array('class' => 'form-control','readonly'=>'readonly','step'=>'0.01','id'=>'final_price'))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <button type="button" class="btn btn-info" onclick="previewTickets()">
          <i class="nav-icon icon-eye"></i> Preview Tickets
        </button>
        <button type="submit" class="btn btn-success" name="action" value="sale_print">
          <i class="nav-icon icon-printer"></i> Sale & Print
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}

<!-- Ticket Preview Section -->
<div id="previewSection" style="display:none; margin-top: 30px;">
  <h4>Ticket Preview</h4>
  <div id="ticketPreviewContainer" class="row"></div>
</div>

@endsection

@section('script')
<script>
function loadTicketPrice(){
  var ticket_id = document.getElementById('ticket_id').value;
  if(!ticket_id){
    document.getElementById('price').value = '';
    calculateTotal();
    return;
  }

  var url = '{{ route("ticket_sales.get_price", ":id") }}'.replace(':id', ticket_id);

  $.ajax({
    url: url,
    type: 'GET',
    success: function(response){
      if(response.success){
        document.getElementById('price').value = response.price;
        calculateTotal();
      }
    },
    error: function(){
      alert('Failed to load ticket price');
    }
  });
}

function calculateTotal(){
  var price = parseFloat(document.getElementById('price').value) || 0;
  var quantity = parseInt(document.getElementById('quantity').value) || 1;
  var discount = parseFloat(document.getElementById('discount_amount').value) || 0;

  var total = price * quantity;
  var final = total - discount;

  if (final < 0) final = 0;

  document.getElementById('total_price').value = total.toFixed(2);
  document.getElementById('final_price').value = final.toFixed(2);
}

function previewTickets(){
  var ticket_id = document.getElementById('ticket_id').value;
  var ticket_name = $('#ticket_id option:selected').data('name');
  var price = parseFloat(document.getElementById('price').value) || 0;
  var quantity = parseInt(document.getElementById('quantity').value) || 1;
  var discount = parseFloat(document.getElementById('discount_amount').value) || 0;

  if(!ticket_id){
    alert('Please select a ticket first');
    return;
  }

  var total = price * quantity;
  var final = total - discount;
  var perTicketDiscount = discount / quantity;
  var perTicketFinal = final / quantity;

  var html = '';
  var now = new Date();
  var dateStr = now.getDate().toString().padStart(2,'0') + '-' + (now.getMonth()+1).toString().padStart(2,'0') + '-' + now.getFullYear();
  var timeStr = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');

  for(var i=0; i<quantity; i++){
    var ticketNum = generateTicketNumber();
    html += `
    <div class="col-md-4" style="margin-bottom: 20px;">
      <div style="border: 2px solid #333; padding: 15px; width: 3in; min-height: 5in; background: #fff;">
        <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px;">
          
          <h4 style="margin: 0; font-weight: bold;">Safina Park & Resort</h4>
          <h6 style="margin-bottom: 15px; font-weight: bold;">Godagari, Rajshahi, Bangladesh</h6>
          <h3 style="margin: 0; font-weight: bold;">${ticket_name}</h3>
        </div>
        <div style="font-size: 13px; margin: 8px 0;">
          <strong>Price:</strong> ${price.toFixed(2)} Tk
        </div>
        ${discount > 0 ? `<div style="font-size: 13px; margin: 8px 0; color: #d9534f;">
          <strong>Discount:</strong> -${perTicketDiscount.toFixed(2)} Tk
        </div>` : '<div style="font-size: 13px; margin: 8px 0;"> <strong>Discount:</strong> 0.00 Tk </div>'}
        <div style="font-size: 14px; margin: 8px 0; font-weight: bold; color: #5cb85c;">
          <strong>Final Price:</strong> ${perTicketFinal.toFixed(2)} Tk
        </div>
        <div style="font-size: 13px; margin: 8px 0;">
          <strong>Date:</strong> ${dateStr}
        </div>
        <div style="font-size: 13px; margin: 8px 0;">
          <strong>Time:</strong> ${timeStr}
        </div>
        <div style="text-align: center; margin: 15px 0;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent('{{ url('/') }}/ticket/verify/' + ticketNum)}" alt="QR Code" style="width: 120px; height: 120px;">
        </div>
        <div style="text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 15px;">
          Valid for single entry only
        </div>
      </div>
    </div>`;
  }

  $('#ticketPreviewContainer').html(html);
  $('#previewSection').show();
  $('html, body').animate({ scrollTop: $('#previewSection').offset().top }, 500);
}

function generateTicketNumber(){
  var now = new Date();
  var dateTime = now.getFullYear().toString() +
                 (now.getMonth()+1).toString().padStart(2,'0') +
                 now.getDate().toString().padStart(2,'0') +
                 now.getHours().toString().padStart(2,'0') +
                 now.getMinutes().toString().padStart(2,'0') +
                 now.getSeconds().toString().padStart(2,'0');
  var random = Math.floor(Math.random() * 10000).toString().padStart(4,'0');
  return dateTime + random;
}

// Calculate total on page load
$(document).ready(function(){
  calculateTotal();
});
</script>
@endsection
