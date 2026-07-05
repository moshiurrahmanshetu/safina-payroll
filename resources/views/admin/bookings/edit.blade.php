@extends('layouts.admin')
@section('title', 'Booking Edit')
@section('content')
    <h3 class="page-header">Booking Edit
        {{ link_to_route('bookings.index', 'Booking List', [], ['class' => 'btn btn-success pull-right']) }}</h3>

    <!-- Display General Errors -->
    @if ($errors->has('error'))
        <div class="alert alert-danger">
            {{ $errors->first('error') }}
        </div>
    @endif

    <!-- Display Flash Error Message -->
    @if (session('flash_error'))
        <div class="alert alert-danger">
            {{ session('flash_error') }}
        </div>
    @endif

    <!-- Display Flash Success Message -->
    @if (session('flash_success'))
        <div class="alert alert-success">
            {{ session('flash_success') }}
        </div>
    @endif

    {{ Form::model($booking, ['route' => ['bookings.update', $booking->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'id' => 'booking_form']) }}
    <div class="row">


        <!-- Check-in / Check-out -->
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="nav-icon icon-calendar"></i> Check-in / Check-out Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Check-in Date *</label>
                                <input type="text" name="check_in_date" id="check_in_date"
                                    class="form-control datepicker" required autocomplete="off" placeholder="DD-MM-YYYY"
                                    value="{{ $booking->check_in_date ? date('d-m-Y', strtotime($booking->check_in_date)) : '' }}">
                                {!! $errors->first('check_in_date', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Check-in Time </label>
                                <input type="time" name="check_in_time" class="form-control"
                                    value="{{ $booking->check_in_time ?? '' }}">
                                {!! $errors->first('check_in_time', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Check-out Date *</label>
                                <input type="text" name="check_out_date" id="check_out_date"
                                    class="form-control datepicker" required autocomplete="off" placeholder="DD-MM-YYYY"
                                    value="{{ $booking->check_out_date ? date('d-m-Y', strtotime($booking->check_out_date)) : '' }}">
                                {!! $errors->first('check_out_date', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Check-out Time </label>
                                <input type="time" name="check_out_time" class="form-control"
                                    value="{{ $booking->check_out_time ?? '' }}">
                                {!! $errors->first('check_out_time', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Category Selection -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Service Category *</label>
                <select name="service_category_id" id="service_category_id" class="form-control" required
                    onchange="loadServicesByCategory(this.value)">
                    <option value="">Select Category</option>
                    @foreach ($service_categories as $id => $name)
                        <option value="{{ $id }}"
                            {{ $booking->service && $booking->service->service_category_id == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                {!! $errors->first('service_category_id', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <!-- Service Selection -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Service *</label>
                <select name="service_id" id="service_id" class="form-control" required disabled
                    onchange="loadServiceFields(this.value)">
                    <option value="">Select Category First</option>
                </select>
                {!! $errors->first('service_id', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>


        <!-- Time Slot Selection (Always Visible) -->
        <div class="row time-slot-main">
            <div id="slot_container" class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Time Slot *</label>
                    <select name="time_slot_id" id="time_slot_id" class="form-control"
                        onchange="onTimeSlotChange(this)">
                        <option value="">Select Time Slot</option>
                    </select>
                    <small class="text-muted">Select a date first to see available slots</small>
                    {!! $errors->first('time_slot_id', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <!-- Auto-filled Time Display (Readonly) -->
            <div class="col-md-8">
                <div id="time_display_container" class="row" style="display:none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Start Time</label>
                            <input type="text" id="display_start_time" class="form-control" readonly>
                            <input type="hidden" name="start_time" id="start_time"
                                value="{{ $booking->start_time ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">End Time</label>
                            <input type="text" id="display_end_time" class="form-control" readonly>
                            <input type="hidden" name="end_time" id="end_time"
                                value="{{ $booking->end_time ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dynamic Fields Container -->
        <div id="dynamic_fields_container" class="row"></div>


        <!-- Promo Code and Manual Discount -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Promo Code</label>
                    <div class="input-group">
                        {{ Form::text('promo_code', $booking->promo_code, ['class' => 'form-control', 'placeholder' => 'Enter promo code if available', 'id' => 'promo_code']) }}
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-info" onclick="validatePromoCode()">Update</button>
                        </span>
                    </div>
                    <small class="text-muted" id="promo_status"></small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Manual Discount</label>
                    {{ Form::number('manual_discount', $booking->manual_discount ? $booking->manual_discount : 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'id' => 'manual_discount', 'onchange' => 'recalculateAll()', 'placeholder' => '0.00']) }}
                    <small class="text-muted">Enter manual discount amount (optional)</small>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading mb-2"><b><i class="nav-icon icon-user"></i> Customer Information</b></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Name *</label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="Full Name" value="{{ $booking->name ?? '' }}">
                                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Phone *</label>
                                <input type="text" name="phone" class="form-control" required
                                    placeholder="Phone Number" value="{{ $booking->phone ?? '' }}">
                                {!! $errors->first('phone', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email Address"
                                    value="{{ $booking->email ?? '' }}">
                                {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Emergency Contact *</label>
                                <input type="text" name="emergency_contact" class="form-control" required
                                    placeholder="Emergency Contact Number"
                                    value="{{ $booking->emergency_contact ?? '' }}">
                                {!! $errors->first('emergency_contact', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Full Address">{{ $booking->address ?? '' }}</textarea>
                                {!! $errors->first('address', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <!-- Price Breakdown -->
        <div class="row" id="price_breakdown" style="display:none;">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><b>Price Breakdown</b></div>
                    <div class="panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td>Pricing Rules Extra:</td>
                                <td align="right" id="display_extra_amount">0.00</td>
                            </tr>
                            <tr>
                                <td>Subtotal:</td>
                                <td align="right" id="display_subtotal">0.00</td>
                            </tr>
                            <tr id="promo_discount_row" style="display:none;">
                                <td>Promo Discount:</td>
                                <td align="right" id="display_promo_discount">0.00</td>
                            </tr>
                            <tr>
                                <td>Manual Discount:</td>
                                <td align="right" id="display_manual_discount">0.00</td>
                            </tr>
                            <tr>
                                <td>Total Discount:</td>
                                <td align="right" id="display_discount">0.00</td>
                            </tr>
                            <tr class="success">
                                <td><strong>Final Price:</strong></td>
                                <td align="right"><strong id="display_final_price">0.00</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Total Price</label>
                    {{ Form::number('total_price', null, ['class' => 'form-control', 'readonly' => 'readonly', 'step' => '0.01', 'id' => 'total_price']) }}
                    <small class="text-muted">Auto-calculated based on pricing type</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Discount Amount</label>
                    {{ Form::number('discount_amount', $booking->discount_amount, ['class' => 'form-control', 'readonly' => 'readonly', 'step' => '0.01', 'id' => 'discount_amount']) }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Final Price</label>
                    {{ Form::number('final_price', $booking->final_price, ['class' => 'form-control', 'readonly' => 'readonly', 'step' => '0.01', 'id' => 'final_price']) }}
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Status *</label>
                    {{ Form::select('status', config('myhelpers.booking_status'), $booking->status ?? null, ['class' => 'form-control', 'required' => 'required']) }}
                    {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <button type="submit" id="submit_booking_btn" class="btn btn-primary">
                        Update Booking
                    </button>
                    <span id="submit_warning" class="text-danger ml-2" style="display:none;">
                        <i class="nav-icon icon-close"></i> No availability - cannot submit
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog"
        aria-labelledby="imagePreviewModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="text-align: center;justify-content: center;align-items: center;">
                <div class="modal-header">
                    <button type="button" style="position: absolute;right: 0;" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true"
                            style="border: 1px solid;padding: 5px 10px 7px 10px;text-align: center;display: flex;color: red;background: black;">×</span>
                    </button>
                    <h4 class="modal-title" id="imagePreviewModalLabel">Image Preview</h4>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" src="" alt="Preview"
                        style="max-width: 100%; max-height: 80vh; object-fit: contain;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}
@endsection

@section('script')
    <script>
        var currentPricingType = 0;
        var currentServiceId = null;
        var validatedPromoDiscount = {{ $booking->discount_amount ?? 0 }}; // Restore saved promo discount

        // Store existing meta values for pre-filling
        var existingMetaValues = @json($meta_values_array ?? []);

        // Base URL for file paths
        var baseUrl = "{{ url('/') }}";

        // Initialize display fields with saved values on page load
        $(document).ready(function() {
            var savedPromoDiscount = validatedPromoDiscount;
            var savedManualDiscount = parseFloat(document.getElementById('manual_discount').value) || 0;
            var savedTotalDiscount = parseFloat(document.getElementById('discount_amount').value) || 0;
            var savedFinalPrice = parseFloat(document.getElementById('final_price').value) || 0;

            // Update display fields with saved values
            document.getElementById('display_promo_discount').textContent = savedPromoDiscount.toFixed(2);
            document.getElementById('display_manual_discount').textContent = savedManualDiscount.toFixed(2);
            document.getElementById('display_discount').textContent = savedTotalDiscount.toFixed(2);
            document.getElementById('display_final_price').textContent = savedFinalPrice.toFixed(2);

            // Show/hide promo discount row based on saved promo
            if (savedPromoDiscount > 0) {
                document.getElementById('promo_discount_row').style.display = 'table-row';
                document.getElementById('promo_status').textContent = 'Promo code applied';
                document.getElementById('promo_status').className = 'text-success';
            } else {
                document.getElementById('promo_discount_row').style.display = 'none';
            }

            // Show price breakdown
            document.getElementById('price_breakdown').style.display = 'block';

            // Initialize: Load services for current category
            var categoryId = document.getElementById('service_category_id').value;
            if (categoryId) {
                loadServicesByCategory(categoryId);
            }

            // After services load, select current service and load fields
            setTimeout(function() {
                var serviceId = '{{ $booking->service_id ?? '' }}';
                if (serviceId) {
                    document.getElementById('service_id').value = serviceId;
                    loadServiceFields(serviceId);
                }
            }, 500);
        });

        // Load services by category via AJAX
        function loadServicesByCategory(category_id) {
            var serviceSelect = document.getElementById('service_id');

            if (!category_id) {
                serviceSelect.innerHTML = '<option value="">Select Category First</option>';
                serviceSelect.disabled = true;
                return;
            }

            // Show loading
            serviceSelect.innerHTML = '<option value="">Loading...</option>';
            serviceSelect.disabled = true;

            // Get booking date and current service ID
            var bookingDate = document.getElementById('check_in_date').value;
            var currentServiceId = {{ $booking->service_id ?? 'null' }};

            // Build URL with booking date parameter
            var url = "{{ url('/admin/get-services') }}/" + category_id;
            if (bookingDate) {
                url += '?booking_date=' + encodeURIComponent(bookingDate);
            }

            // Fetch services via AJAX
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === true) {
                        var options = '<option value="">Select Service</option>';

                        if (response.data.length === 0) {
                            options = '<option value="">No services available for this category and date</option>';
                        } else {
                            for (var i = 0; i < response.data.length; i++) {
                                var service = response.data[i];
                                options += '<option value="' + service.id + '">' + service.name + '</option>';
                            }
                            serviceSelect.disabled = false;
                        }

                        serviceSelect.innerHTML = options;

                        // Re-select current service if it exists in the list
                        if (currentServiceId) {
                            var currentOption = serviceSelect.querySelector('option[value="' + currentServiceId + '"]');
                            if (currentOption) {
                                serviceSelect.value = currentServiceId;
                            } else {
                                // Current service not in available list (fully booked), add it anyway
                                var currentServiceName = "{{ $booking->service ? $booking->service->name : '' }}";
                                if (currentServiceName) {
                                    var newOption = document.createElement('option');
                                    newOption.value = currentServiceId;
                                    newOption.textContent = currentServiceName + ' (Fully Booked)';
                                    serviceSelect.appendChild(newOption);
                                    serviceSelect.value = currentServiceId;
                                }
                            }
                        }

                        // Clear dynamic fields and base price when category changes
                        document.getElementById('dynamic_fields_container').innerHTML = '';
                        currentServiceId = null;
                    } else {
                        console.error('Error in response:', response.message);
                        serviceSelect.innerHTML = '<option value="">' + (response.message ||
                            'Error loading services') + '</option>';
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading services:', error);
                    console.log('Full response:', xhr.responseText);
                    serviceSelect.innerHTML = '<option value="">Error loading services</option>';
                }
            });
        }

        function loadServiceFields(service_id) {
            if (!service_id) {
                document.getElementById('dynamic_fields_container').innerHTML = '';
                document.getElementById('total_price').value = '';
                document.getElementById('discount_amount').value = '0.00';
                document.getElementById('final_price').value = '';
                document.getElementById('price_breakdown').style.display = 'none';
                currentServiceId = null;
                return;
            }

            currentServiceId = service_id;
            var url = '{{ route('bookings.get_fields', ':id') }}'.replace(':id', service_id);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    currentPricingType = parseInt(response.pricing_type) || 0;

                    // Load slots for ALL services (slot-based booking system)
                    loadTimeSlots(service_id);

                    // Show price breakdown
                    document.getElementById('price_breakdown').style.display = 'block';

                    // Store fields data globally for conditional checking
                    window.metaFieldsData = response.meta_fields;

                    var html = '';
                    if (response.meta_fields && response.meta_fields.length > 0) {
                        response.meta_fields.forEach(function(field) {
                            // Build conditional attributes
                            var conditionalAttrs = '';
                            if (field.conditional_field && field.conditional_value) {
                                conditionalAttrs = ' data-conditional-field="' + field
                                    .conditional_field + '" data-conditional-value="' + field
                                    .conditional_value + '" style="display:none;"';
                            }

                            html += '<div class="col-md-6 meta-field-wrapper" id="wrapper_' + field
                                .field_name.replace(/\s+/g, '_') + '"' + conditionalAttrs + '>';
                            html += '<div class="form-group">';
                            html += '<label class="control-label">' + field.field_name + (field
                                .required ? ' *' : '') + '</label>';

                            // Get existing value for this field
                            var existingValue = '';
                            var existingFilePath = '';
                            // Database stores ALL meta_values using field_name keys
                            var lookupKey = field.field_name;
                            if (existingMetaValues && existingMetaValues[lookupKey]) {
                                if (typeof existingMetaValues[lookupKey] === 'object') {
                                    existingValue = existingMetaValues[lookupKey].value || '';
                                    existingFilePath = existingMetaValues[lookupKey].file_path || '';
                                } else {
                                    existingValue = existingMetaValues[lookupKey];
                                }
                            }

                            console.log('FIELD NAME:', field.field_name);
                            console.log('LOOKUP KEY:', lookupKey);
                            console.log('FOUND META:', existingMetaValues[lookupKey]);
                            console.log('EXISTING VALUE:', existingValue);

                            if (field.field_type == 0) { // Text
                                html += '<input type="text" name="meta_values[' + field.field_name +
                                    ']" class="form-control meta-input" ' + (field.required ?
                                        'required' : '') + ' value="' + existingValue + '">';
                                if(field.help_text){
                                    html += '<div class="help-block text-muted">' + field.help_text + '</div>';
                                }
                            } else if (field.field_type == 1) { // Number
                                html += '<input type="number" name="meta_values[' + field.field_name +
                                    ']" class="form-control meta-input" ' + (field.required ?
                                        'required' : '') + ' value="' + existingValue + '">';
                                if(field.help_text){
                                    html += '<div class="help-block text-muted">' + field.help_text + '</div>';
                                }
                            } else if (field.field_type == 2) { // Select
                                html += '<select name="meta_values[' + field.field_name +
                                    ']" id="select_' + field.field_name.replace(/\s+/g, '_') +
                                    '" class="form-control meta-select" ' + (field.required ?
                                        'required' : '') +
                                    ' onchange="checkConditionalFields()" data-existing-value="' +
                                    existingValue + '">';
                                html += '<option value="">Select</option>';
                                if (field.options) {
                                    var options = JSON.parse(field.options);
                                    for (var key in options) {
                                        var selected = existingValue == key ? 'selected' : '';
                                        html += '<option value="' + key + '" ' + selected + '>' +
                                            options[key] + '</option>';
                                    }
                                }
                                html += '</select>';
                                if(field.help_text){
                                    html += '<div class="help-block text-muted">' + field.help_text + '</div>';
                                }
                            } else if (field.field_type == 3) { // Date
                                html += '<input type="text" name="meta_values[' + field.field_name +
                                    ']" class="form-control datepicker meta-input" ' + (field.required ?
                                        'required' : '') +
                                    ' autocomplete="off" placeholder="DD-MM-YYYY" value="' +
                                    existingValue + '">';
                                if(field.help_text){
                                    html += '<div class="help-block text-muted">' + field.help_text + '</div>';
                                }
                            } else if (field.field_type == 4) { // File
                                if (existingFilePath) {
                                    // Show existing file preview
                                    var fileExtension = existingFilePath.split('.').pop().toLowerCase();
                                    var imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                    var isImage = imageExtensions.includes(fileExtension);

                                    if (isImage) {
                                        html +=
                                            '<div class="file-preview" style="margin-bottom: 10px;">';
                                        html +=
                                            '<div style="display: inline-block; margin-right: 10px;">';
                                        html += '<img src="' + baseUrl + '/storage/app/public/' +
                                            existingFilePath + '" alt="' + field.field_name +
                                            '" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="openImagePreview(\'' +
                                            baseUrl + '/storage/app/public/' + existingFilePath +
                                            '\')">';
                                        html += '</div>';
                                        html +=
                                            '<button type="button" class="btn btn-sm btn-info" onclick="openImagePreview(\'' +
                                            baseUrl + '/storage/app/public/' + existingFilePath +
                                            '\')">';
                                        html += '<i class="glyphicon glyphicon-eye-open"></i> Preview';
                                        html += '</button>';

                                        html += '</div>';
                                    } else {
                                        html +=
                                            '<div class="file-preview" style="margin-bottom: 10px;">';
                                        html += '<a href="' + baseUrl + '/storage/app/public/' +
                                            existingFilePath +
                                            '" target="_blank" class="btn btn-sm btn-info">';
                                        html +=
                                            '<i class="glyphicon glyphicon-download-alt"></i> View/Download File';
                                        html += '</a>';

                                        html += '</div>';
                                    }
                                }
                                html += '<input type="file" name="meta_files[' + field.field_name +
                                    ']" class="form-control meta-file" ' + (field.required && !
                                        existingFilePath ? 'required' : '') +
                                    ' accept="image/*,.pdf,.doc,.docx">';
                                html +=
                                    '<small class="text-muted">Leave empty to keep existing file</small>';
                                if(field.help_text){
                                    html += '<div class="help-block text-muted">' + field.help_text + '</div>';
                                }
                            }

                            html += '</div></div>';
                        });
                    }
                    document.getElementById('dynamic_fields_container').innerHTML = html;

                    // Re-initialize datepicker for new fields
                    $('.datepicker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true
                    });

                    // Check conditional fields visibility after rendering
                    checkConditionalFields();
                },
                error: function() {
                    alert('Failed to load service fields');
                }
            });
        }

        // Load time slots for a service (filtered by date availability)
        function loadTimeSlots(serviceId) {
            console.log('Loading time slots for service:', serviceId);

            var slotSelect = document.getElementById('time_slot_id');
            slotSelect.innerHTML = '<option value="">Loading slots...</option>';

            // Get selected date for availability filtering
            var checkInDate = $('#check_in_date').val();

            var url = '{{ route('get.slots', ':id') }}'.replace(':id', serviceId);
            if (checkInDate) {
                url += '?date=' + encodeURIComponent(checkInDate);
            }
            // Add exclude_booking_id parameter for edit page
            var currentBookingId = '{{ $booking->id ?? '' }}';
            if (currentBookingId) {
                url += (checkInDate ? '&' : '?') + 'exclude_booking_id=' + currentBookingId;
            }

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Time slots loaded:', response);

                    var options = '<option value="">Select Time Slot</option>';
                    if (response && response.length > 0) {
                        response.forEach(function(slot) {
                            var startTime = slot.start_time.substring(0, 5);
                            var endTime = slot.end_time.substring(0, 5);
                            var price = parseFloat(slot.price) || 0;
                            var priceDisplay = price > 0 ? ' - ৳' + price.toFixed(2) : '';
                            var selected = '{{ $booking->time_slot_id ?? '' }}' == slot.id ?
                                'selected' : '';
                            options += '<option value="' + slot.id + '" data-start="' + startTime +
                                '" data-end="' + endTime + '" data-price="' + price + '" ' + selected +
                                '>' + slot.name + ' (' + startTime + ' - ' + endTime + ')' +
                                priceDisplay + '</option>';
                        });
                    } else {
                        options += '<option value="" disabled>No available slots for this date</option>';
                    }
                    slotSelect.innerHTML = options;

                    // Select current slot if exists
                    var currentSlotId = '{{ $booking->time_slot_id ?? '' }}';
                    if (currentSlotId) {
                        slotSelect.value = currentSlotId;
                        onTimeSlotChange(slotSelect);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading time slots:', error);
                    slotSelect.innerHTML = '<option value="">Error loading slots</option>';
                }
            });
        }

        // Handle time slot selection
        function onTimeSlotChange(select) {
            var selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                var startTime = selectedOption.getAttribute('data-start');
                var endTime = selectedOption.getAttribute('data-end');
                var slotPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;

                // Set hidden form fields
                document.getElementById('start_time').value = startTime;
                document.getElementById('end_time').value = endTime;

                // Show readonly display fields
                document.getElementById('display_start_time').value = startTime;
                document.getElementById('display_end_time').value = endTime;
                document.getElementById('time_display_container').style.display = 'flex';

                console.log('Time slot selected:', selectedOption.value, 'Start:', startTime, 'End:', endTime, 'Price:',
                    slotPrice);

                // Trigger price calculation and availability check
                calculateTotalPrice();
                triggerAvailabilityCheck();
            } else {
                // Clear values and hide display
                document.getElementById('start_time').value = '';
                document.getElementById('end_time').value = '';
                document.getElementById('display_start_time').value = '';
                document.getElementById('display_end_time').value = '';
                document.getElementById('time_display_container').style.display = 'none';
            }
        }

        // Trigger availability check when dates, time, or service change
        $(document).ready(function() {
            // Watch for date changes
            $('#check_in_date, #check_out_date').on('change changeDate', function() {
                console.log('Date changed:', this.id, 'Value:', $(this).val());

                // Reload time slots when date changes (for ALL services - slot-based system)
                var serviceId = $('#service_id').val();
                if (serviceId) {
                    loadTimeSlots(serviceId);
                }

                // Reload services when date changes to apply availability filter
                var categoryId = $('#service_category_id').val();
                if (categoryId) {
                    loadServicesByCategory(categoryId);
                }
            });

            // Watch for time changes (slot-based availability check)
            $('#start_time, #end_time').on('change', function() {
                console.log('Time changed:', this.id, 'Value:', $(this).val());
            });

            // Watch for service change - this loads new fields
            $('#service_id').on('change', function() {
                console.log('Service changed');
            });
        });

        function calculateBasePrice() {
            var basePrice = 0;

            // Get slot price from selected time slot
            var timeSlotSelect = document.getElementById('time_slot_id');
            if(timeSlotSelect && timeSlotSelect.value){
                var selectedOption = timeSlotSelect.options[timeSlotSelect.selectedIndex];
                if(selectedOption && selectedOption.dataset.price){
                    basePrice = parseFloat(selectedOption.dataset.price) || 0;
                }
            }

            if (currentPricingType == 1) { // Hourly
                var startTime = document.getElementById('start_time').value;
                var endTime = document.getElementById('end_time').value;

                if (startTime && endTime) {
                    var start = new Date('2000-01-01 ' + startTime);
                    var end = new Date('2000-01-01 ' + endTime);
                    var diffMs = end - start;
                    var diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
                    if (diffHours < 1) diffHours = 1;
                    basePrice = basePrice * diffHours;
                }
            }
            return basePrice;
        }

        function recalculateAll() {
            var basePrice = calculateBasePrice();
            var promoCode = document.getElementById('promo_code').value;
            var manualDiscount = parseFloat(document.getElementById('manual_discount').value) || 0;

            // We'll do a simple calculation on client side
            // Server will do the accurate calculation with pricing rules from database
            var subtotal = basePrice;
            var promoDiscount = validatedPromoDiscount; // Use validated promo discount
            var totalDiscount = promoDiscount + manualDiscount;

            // Simple client-side discount estimate (actual discount calculated server-side)
            if (promoCode && promoCode.length > 0 && validatedPromoDiscount === 0) {
                document.getElementById('promo_status').textContent = 'Click Update to validate promo code';
            } else if (promoCode && promoCode.length > 0 && validatedPromoDiscount > 0) {
                document.getElementById('promo_status').textContent = 'Promo code applied';
                document.getElementById('promo_status').className = 'text-success';
            } else {
                document.getElementById('promo_status').textContent = '';
                document.getElementById('promo_status').className = '';
            }

            var finalPrice = subtotal - totalDiscount;

            document.getElementById('total_price').value = subtotal.toFixed(2);
            document.getElementById('discount_amount').value = promoDiscount.toFixed(2);
            document.getElementById('final_price').value = finalPrice.toFixed(2);

            document.getElementById('display_extra_amount').textContent = '0.00 (calculated server-side)';
            document.getElementById('display_subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('display_promo_discount').textContent = promoDiscount.toFixed(2);
            document.getElementById('display_manual_discount').textContent = manualDiscount.toFixed(2);
            document.getElementById('display_discount').textContent = totalDiscount.toFixed(2);
            document.getElementById('display_final_price').textContent = finalPrice.toFixed(2);

            if (promoDiscount > 0) {
                document.getElementById('promo_discount_row').style.display = 'table-row';
            } else {
                document.getElementById('promo_discount_row').style.display = 'none';
            }
        }

        function validatePromoCode() {
            var promoCode = document.getElementById('promo_code').value;
            var serviceId = document.getElementById('service_id').value;
            var basePrice = calculateBasePrice();
            var manualDiscount = parseFloat(document.getElementById('manual_discount').value) || 0;

            if (!promoCode) {
                validatedPromoDiscount = 0;
                var subtotal = basePrice;
                var promoDiscount = 0;
                var totalDiscount = manualDiscount;
                var finalPrice = subtotal - totalDiscount;

                document.getElementById('discount_amount').value = promoDiscount.toFixed(2);
                document.getElementById('final_price').value = finalPrice.toFixed(2);
                document.getElementById('display_subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('display_promo_discount').textContent = '0.00';
                document.getElementById('display_manual_discount').textContent = manualDiscount.toFixed(2);
                document.getElementById('display_discount').textContent = totalDiscount.toFixed(2);
                document.getElementById('display_final_price').textContent = finalPrice.toFixed(2);
                document.getElementById('promo_discount_row').style.display = 'none';
                document.getElementById('promo_status').textContent = '';
                document.getElementById('promo_status').className = '';
                return;
            }

            if (!serviceId) {
                document.getElementById('promo_status').textContent = 'Please select a service first';
                return;
            }

            document.getElementById('promo_status').textContent = 'Validating...';

            $.ajax({
                url: '{{ route('bookings.validate_promo') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    promo_code: promoCode,
                    service_id: serviceId,
                    total_price: basePrice
                },
                success: function(response) {
                    if (response.success) {
                        validatedPromoDiscount = response.discount_amount || 0;
                        var promoDiscount = validatedPromoDiscount;
                        var totalDiscount = promoDiscount + manualDiscount;
                        var subtotal = basePrice;
                        var finalPrice = subtotal - totalDiscount;

                        document.getElementById('discount_amount').value = promoDiscount.toFixed(2);
                        document.getElementById('final_price').value = finalPrice.toFixed(2);

                        document.getElementById('display_subtotal').textContent = subtotal.toFixed(2);
                        document.getElementById('display_promo_discount').textContent = promoDiscount.toFixed(
                        2);
                        document.getElementById('display_manual_discount').textContent = manualDiscount.toFixed(
                            2);
                        document.getElementById('display_discount').textContent = totalDiscount.toFixed(2);
                        document.getElementById('display_final_price').textContent = finalPrice.toFixed(2);

                        document.getElementById('promo_discount_row').style.display = 'table-row';

                        document.getElementById('promo_status').textContent = 'Promo code applied: ' + response
                            .message;
                        document.getElementById('promo_status').className = 'text-success';
                    } else {
                        validatedPromoDiscount = 0;
                        var subtotal = basePrice;
                        var promoDiscount = 0;
                        var totalDiscount = manualDiscount;
                        var finalPrice = subtotal - totalDiscount;

                        document.getElementById('discount_amount').value = promoDiscount.toFixed(2);
                        document.getElementById('final_price').value = finalPrice.toFixed(2);
                        document.getElementById('display_subtotal').textContent = subtotal.toFixed(2);
                        document.getElementById('display_promo_discount').textContent = '0.00';
                        document.getElementById('display_manual_discount').textContent = manualDiscount.toFixed(
                            2);
                        document.getElementById('display_discount').textContent = totalDiscount.toFixed(2);
                        document.getElementById('display_final_price').textContent = finalPrice.toFixed(2);
                        document.getElementById('promo_discount_row').style.display = 'none';

                        document.getElementById('promo_status').textContent = response.message ||
                            'Invalid promo code';
                        document.getElementById('promo_status').className = 'text-danger';
                    }
                },
                error: function() {
                    validatedPromoDiscount = 0;
                    var subtotal = basePrice;
                    var promoDiscount = 0;
                    var totalDiscount = manualDiscount;
                    var finalPrice = subtotal - totalDiscount;

                    document.getElementById('discount_amount').value = promoDiscount.toFixed(2);
                    document.getElementById('final_price').value = finalPrice.toFixed(2);
                    document.getElementById('display_subtotal').textContent = subtotal.toFixed(2);
                    document.getElementById('display_promo_discount').textContent = '0.00';
                    document.getElementById('display_manual_discount').textContent = manualDiscount.toFixed(2);
                    document.getElementById('display_discount').textContent = totalDiscount.toFixed(2);
                    document.getElementById('display_final_price').textContent = finalPrice.toFixed(2);
                    document.getElementById('promo_discount_row').style.display = 'none';

                    document.getElementById('promo_status').textContent = 'Error validating promo code';
                    document.getElementById('promo_status').className = 'text-danger';
                }
            });
        }

        function calculateTotalPrice() {
            recalculateAll();
        }

        function findNextAvailableDate(serviceId, fromDate) {
            var url = '{{ route('bookings.availability.check') }}';
            var currentDate = new Date(fromDate.split('-').reverse().join('-'));

            // Check next 30 days
            for (var i = 1; i <= 30; i++) {
                var nextDate = new Date(currentDate);
                nextDate.setDate(nextDate.getDate() + i);
                var formattedDate = ('0' + nextDate.getDate()).slice(-2) + '-' + ('0' + (nextDate.getMonth() + 1)).slice(-
                    2) + '-' + nextDate.getFullYear();

                // AJAX call for each date (simplified - in production use a dedicated endpoint)
                (function(checkDate) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            service_id: serviceId,
                            date: checkDate
                        },
                        success: function(response) {
                            if (response.success && response.is_available) {
                                document.getElementById('next_available_date_display').textContent =
                                    checkDate + ' (' + response.available + ' slots)';
                                document.getElementById('next_available_info').style.display = 'block';
                                return false; // Stop checking once found
                            }
                        }
                    });
                })(formattedDate);
            }
        }

        // Add event listeners for real-time checking
        document.addEventListener('DOMContentLoaded', function() {
            var dateInput = document.querySelector('input[name="date"]');
            var serviceSelect = document.querySelector('select[name="service_id"]');

            if (dateInput) {
                dateInput.addEventListener('change', checkFormAvailability);
                dateInput.addEventListener('blur', checkFormAvailability);
            }

            if (serviceSelect) {
                serviceSelect.addEventListener('change', function() {
                    // Reset availability check when service changes
                    lastCheckedService = '';
                    lastCheckedDate = '';
                    checkFormAvailability();
                });
            }
        });

        // Initialize datepicker for main date fields
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        });

        // Conditional Fields Logic
        function checkConditionalFields() {
            var wrappers = document.querySelectorAll('.meta-field-wrapper');
            wrappers.forEach(function(wrapper) {
                var conditionalField = wrapper.getAttribute('data-conditional-field');
                var conditionalValue = wrapper.getAttribute('data-conditional-value');

                if (conditionalField && conditionalValue) {
                    // Find the trigger field value
                    var triggerSelect = document.querySelector('select[name="meta_values[' + conditionalField +
                        ']"]');
                    if (triggerSelect) {
                        var triggerValue = triggerSelect.value;
                        if (triggerValue == conditionalValue) {
                            wrapper.style.display = 'block';
                            // Enable required fields inside
                            var requiredInputs = wrapper.querySelectorAll('input, select');
                            requiredInputs.forEach(function(input) {
                                if (input.getAttribute('data-required') == 'true') {
                                    input.setAttribute('required', 'required');
                                }
                            });
                        } else {
                            wrapper.style.display = 'none';
                            // Disable required fields inside to allow form submission
                            var requiredInputs = wrapper.querySelectorAll('input, select');
                            requiredInputs.forEach(function(input) {
                                if (input.hasAttribute('required')) {
                                    input.setAttribute('data-required', 'true');
                                    input.removeAttribute('required');
                                }
                            });
                        }
                    }
                }
            });
        }

        // =====================================
        // REAL-TIME AVAILABILITY CHECK
        // =====================================

        // Debounce timer for availability check
        var availabilityCheckTimer = null;

        // Function to trigger availability check
        function triggerAvailabilityCheck() {
            console.log('triggerAvailabilityCheck called');
            // Clear previous timer
            if (availabilityCheckTimer) {
                clearTimeout(availabilityCheckTimer);
            }
            // Set new timer (300ms delay to avoid too many requests)
            availabilityCheckTimer = setTimeout(checkFormAvailability, 300);
        }

        // Watch for changes on service, check_in_date, check_out_date
        $(document).ready(function() {
            console.log('Document ready - binding availability events');

            // Handle regular change events (including time fields for hourly services)
            $(document).on('change', '#service_id, #check_in_date, #check_out_date, #start_time, #end_time',
                function() {
                    console.log('Change event on:', this.id);
                    triggerAvailabilityCheck();
                });

            // Handle datepicker changeDate events - bind directly to elements
            $('#check_in_date, #check_out_date').on('changeDate', function(e) {
                console.log('Datepicker changeDate event on:', this.id);
                triggerAvailabilityCheck();
            });

            // Also trigger when service dropdown is populated after category selection
            $(document).on('change', '#category_id', function() {
                console.log('Category changed, will check availability when service loaded');
                // Wait for service dropdown to populate
                setTimeout(function() {
                    if ($('#service_id').val()) {
                        console.log('Service loaded, triggering check');
                        triggerAvailabilityCheck();
                    }
                }, 500);
            });

            // Initial check on page load (if fields are pre-filled)
            setTimeout(function() {
                var serviceId = $('#service_id').val();
                var checkIn = $('#check_in_date').val();
                var checkOut = $('#check_out_date').val();
                console.log('Initial check - service:', serviceId, 'checkIn:', checkIn, 'checkOut:',
                    checkOut);
                if (serviceId && checkIn && checkOut) {
                    checkFormAvailability();
                }
            }, 800);
        });

        // Check availability for the booking form
        function checkFormAvailability() {
            var serviceId = $('#service_id').val();
            var checkIn = $('#check_in_date').val();
            var checkOut = $('#check_out_date').val();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();

            console.log('checkFormAvailability called - serviceId:', serviceId, 'checkIn:', checkIn, 'checkOut:', checkOut,
                'startTime:', startTime, 'endTime:', endTime, 'pricingType:', currentPricingType);

            // Only check if all required fields are filled
            if (!serviceId || !checkIn || !checkOut) {
                console.log('Missing fields, hiding panel');
                $('#availability_panel').hide();
                $('#submit_booking_btn').prop('disabled', false);
                $('#submit_warning').hide();
                return;
            }

            // Show loading state in availability panel
            $('#availability_panel').show();
            $('#availability_alert').hide();
            $('#availability_status').html('<i class="nav-icon icon-refresh"></i> Checking...');

            // Build request data
            var requestData = {
                service_id: serviceId,
                check_in_date: checkIn,
                check_out_date: checkOut
            };

            // Add time parameters when slot is selected (slot-based availability check)
            if (startTime && endTime) {
                requestData.start_time = startTime;
                requestData.end_time = endTime;
                console.log('Slot-based availability check with time:', startTime, '-', endTime);
            }

            // Call availability API
            $.ajax({
                url: '{{ route('bookings.availability.check') }}',
                type: 'GET',
                dataType: 'json',
                data: requestData,
                success: function(response) {
                    console.log('Availability AJAX response:', response);
                    if (response.success) {
                        updateAvailabilityDisplay(response);
                    } else {
                        showAvailabilityError(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Availability check error:', status, error);
                    console.log('XHR response:', xhr.responseText);
                    showAvailabilityError('Error checking availability');
                }
            });
        }

        // Update the availability display
        function updateAvailabilityDisplay(response) {
            console.log('updateAvailabilityDisplay - status:', response.status);
            var panel = $('#availability_panel .panel');
            var alertDiv = $('#availability_alert');
            var submitBtn = $('#submit_booking_btn');
            var submitWarning = $('#submit_warning');

            // Update status
            $('#availability_status').html('<span class="label" style="background-color: ' + getStatusColor(response.status) + '">' + response.message + '</span>');

            if (response.status === 'unavailable') {
                console.log('No availability - disabling submit button');
                // No availability - show warning and disable submit
                panel.removeClass('panel-info panel-success').addClass('panel-danger');
                alertDiv.removeClass('alert-success alert-info').addClass('alert-danger');
                alertDiv.html('<strong><i class="nav-icon icon-close"></i> Fully Booked!</strong> ' +
                    'Service is not available for selected slot. Please choose different dates or slot.');
                alertDiv.show();

                submitBtn.prop('disabled', true);
                submitWarning.show();
            } else {
                // Available - show success
                panel.removeClass('panel-info panel-danger panel-warning').addClass('panel-success');
                alertDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
                alertDiv.html('<strong><i class="nav-icon icon-check"></i> ' + response.message + '!</strong>');
                alertDiv.show();

                submitBtn.prop('disabled', false);
                submitWarning.hide();
            }
        }

        // Show availability error
        function showAvailabilityError(message) {
            var panel = $('#availability_panel .panel');
            var alertDiv = $('#availability_alert');

            panel.removeClass('panel-info panel-success panel-warning').addClass('panel-danger');
            alertDiv.removeClass('alert-success alert-info alert-warning').addClass('alert-danger');
            alertDiv.html('<strong><i class="nav-icon icon-close"></i> Error!</strong> ' + (message ||
                'Could not check availability'));
            alertDiv.show();

            $('#submit_booking_btn').prop('disabled', true);
            $('#submit_warning').show();
        }

        // Get status color for availability
        function getStatusColor(status) {
            switch (status) {
                case 'available':
                    return '#28a745';
                case 'limited':
                    return '#ffc107';
                case 'fully_booked':
                    return '#dc3545';
                default:
                    return '#6c757d';
            }
        }

        // Open image preview modal
        function openImagePreview(imageUrl) {
            console.log('Opening preview:', imageUrl);
            document.getElementById('previewImage').src = imageUrl;
            console.log('Preview image src:', document.getElementById('previewImage').src);

            // Try Bootstrap modal first, fallback to jQuery show
            if (typeof $.fn.modal === 'function') {
                $('#imagePreviewModal').modal('show');
            } else {
                $('#imagePreviewModal').show();
            }
        }

        // Close image preview modal
        function closeImagePreview() {
            if (typeof $.fn.modal === 'function') {
                $('#imagePreviewModal').modal('hide');
            } else {
                $('#imagePreviewModal').hide();
            }
        }

        // Initialize modal close handlers
        $(document).ready(function() {
            // Close on X button click
            $('#imagePreviewModal .close').on('click', function() {
                closeImagePreview();
            });

            // Close on Close button click
            $('#imagePreviewModal .btn-default').on('click', function() {
                closeImagePreview();
            });

            // Close on click outside modal (if not using Bootstrap modal)
            if (typeof $.fn.modal !== 'function') {
                $('#imagePreviewModal').on('click', function(e) {
                    if (e.target === this) {
                        closeImagePreview();
                    }
                });
            }
        });
    </script>
@endsection
