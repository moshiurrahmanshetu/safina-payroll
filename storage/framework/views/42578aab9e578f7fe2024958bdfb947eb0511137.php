<?php $__env->startSection('title', 'Permanent Employee Create'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">Permanent Employee Create <?php echo e(link_to_route('permanent_employees.index','Permanent Employee List',[],array('class'=>'btn btn-success pull-right'))); ?></h3>

<?php echo e(Form::model(Request::old(),array('route' => array('permanent_employees.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal'))); ?>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Select Employee Type</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Employee Type *</label>
          <div>
            <label class="radio-inline">
              <input type="radio" name="employee_type" value="existing" onchange="toggleEmployeeType()" checked> Select Existing User
            </label>
            <label class="radio-inline">
              <input type="radio" name="employee_type" value="new" onchange="toggleEmployeeType()"> New Employee
            </label>
          </div>
        </div>

        <!-- Existing User Section -->
        <div id="existing_user_section">
          <div class="form-group">
            <label class="control-label">Select User *</label>
            <select name="user_id" id="user_id" class="form-control" onchange="loadUserDetails()">
              <option value="">Select User</option>
              <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php echo $errors->first('user_id', '<p class="text-danger">:message</p>'); ?>

          </div>
        </div>

        <!-- New Employee Section -->
        <div id="new_employee_section" style="display: none;">
          <div class="alert alert-info">
            <strong>Note:</strong> Fill in all employee details manually.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Employee Photo</label>
        <?php echo e(Form::file('photo', array('class' => 'form-control'))); ?>

        <?php echo $errors->first('photo', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Full Name *</label>
        <?php echo e(Form::text('full_name',null, array('class' => 'form-control', 'required'=>'required', 'id'=>'full_name'))); ?>

        <?php echo $errors->first('full_name', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Father's Name *</label>
        <?php echo e(Form::text('father_name',null, array('class' => 'form-control', 'required'=>'required'))); ?>

        <?php echo $errors->first('father_name', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Mother's Name *</label>
        <?php echo e(Form::text('mother_name',null, array('class' => 'form-control', 'required'=>'required'))); ?>

        <?php echo $errors->first('mother_name', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Gender *</label>
        <?php echo e(Form::select('gender', ['Male'=>'Male', 'Female'=>'Female', 'Other'=>'Other'], null, array('class' => 'form-control', 'required'=>'required'))); ?>

        <?php echo $errors->first('gender', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Date of Birth</label>
        <?php echo e(Form::date('date_of_birth',null, array('class' => 'form-control datetimepicker1'))); ?>

        <?php echo $errors->first('date_of_birth', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">NID</label>
        <?php echo e(Form::text('nid',null, array('class' => 'form-control'))); ?>

        <?php echo $errors->first('nid', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Mobile *</label>
        <?php echo e(Form::text('mobile',null, array('class' => 'form-control', 'required'=>'required', 'id'=>'mobile'))); ?>

        <?php echo $errors->first('mobile', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Email</label>
        <?php echo e(Form::email('email',null, array('class' => 'form-control', 'id'=>'email'))); ?>

        <?php echo $errors->first('email', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Present Address</label>
        <?php echo e(Form::textarea('present_address',null, array('class' => 'form-control', 'rows'=>'3'))); ?>

        <?php echo $errors->first('present_address', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Permanent Address</label>
        <?php echo e(Form::textarea('permanent_address',null, array('class' => 'form-control', 'rows'=>'3'))); ?>

        <?php echo $errors->first('permanent_address', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Emergency Contact</label>
        <?php echo e(Form::text('emergency_contact',null, array('class' => 'form-control'))); ?>

        <?php echo $errors->first('emergency_contact', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Blood Group</label>
        <?php echo e(Form::select('blood_group', [''=>'Select', 'A+'=>'A+', 'A-'=>'A-', 'B+'=>'B+', 'B-'=>'B-', 'AB+'=>'AB+', 'AB-'=>'AB-', 'O+'=>'O+', 'O-'=>'O-'], null, array('class' => 'form-control'))); ?>

        <?php echo $errors->first('blood_group', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Joining Date *</label>
        <?php echo e(Form::date('joining_date',null, array('class' => 'form-control datetimepicker1', 'required'=>'required'))); ?>

        <?php echo $errors->first('joining_date', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Department *</label>
        <?php echo e(Form::select('department_id', [''=>'Select Department'] + $departments, null, array('class' => 'form-control', 'required'=>'required'))); ?>

        <?php echo $errors->first('department_id', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Designation *</label>
        <?php echo e(Form::select('designation_id', [''=>'Select Designation'] + $designations, null, array('class' => 'form-control', 'required'=>'required'))); ?>

        <?php echo $errors->first('designation_id', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Employment Status *</label>
        <?php echo e(Form::select('employment_status',config('myhelpers.status'),1,array('class' => 'form-control', 'required'=>'required'))); ?>

        <?php echo $errors->first('employment_status', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Notes</label>
        <?php echo e(Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>'3'))); ?>

        <?php echo $errors->first('notes', '<p class="text-danger">:message</p>'); ?>

      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Create Permanent Employee
        </button>
      </div>
    </div>
  </div>
</div>
<?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
function toggleEmployeeType() {
  var employeeType = document.querySelector('input[name="employee_type"]:checked').value;
  
  if (employeeType === 'existing') {
    document.getElementById('existing_user_section').style.display = 'block';
    document.getElementById('new_employee_section').style.display = 'none';
    document.getElementById('user_id').required = true;
  } else {
    document.getElementById('existing_user_section').style.display = 'none';
    document.getElementById('new_employee_section').style.display = 'block';
    document.getElementById('user_id').required = false;
    document.getElementById('user_id').value = '';
  }
}

function loadUserDetails() {
  var userId = document.getElementById('user_id').value;
  
  if (!userId) {
    document.getElementById('full_name').value = '';
    document.getElementById('mobile').value = '';
    document.getElementById('email').value = '';
    return;
  }

  var url = '<?php echo e(route("permanent_employees.get_user_details", ":id")); ?>'.replace(':id', userId);

  $.ajax({
    url: url,
    type: 'GET',
    success: function(response){
      if(response.success){
        document.getElementById('full_name').value = response.name;
        document.getElementById('mobile').value = response.mobile;
        document.getElementById('email').value = response.email;
      }
    },
    error: function(){
      alert('Failed to load user details');
    }
  });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/permanent_employees/create.blade.php ENDPATH**/ ?>