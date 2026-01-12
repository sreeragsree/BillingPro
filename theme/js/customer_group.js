$('#save,#update').on("click",function (e) {
	var base_url=$("#base_url").val();
    var flag=true;

    function check_field(id)
    {
      if(!$("#"+id).val() )
        {
            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
        }
    }

    check_field("group_name");
    if(flag==false)
    {
		toastr["warning"]("You have Missed Something to Fillup!")
		return;
    }

    var this_id=this.id;

    if(this_id=="save")
    {
		e.preventDefault();
		data = new FormData($('#customer-group-form')[0]);
		if(!xss_validation(data)){ return false; }
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
		$("#"+this_id).attr('disabled',true);
		$.ajax({
			type: 'POST',
			url: base_url+'customer_groups/newgroup',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			success: function(result){
				if(result=="success")
				{
					window.location=base_url+"customer_groups/view";
					return;
				}
				else if(result=="failed")
				{
					toastr["error"]("Sorry! Failed to save Record.Try again!");
				}
				else
				{
					toastr["error"](result);
				}
				$("#"+this_id).attr('disabled',false);
				$(".overlay").remove();
			}
		});
    }
	else if(this_id=="update")
    {
		e.preventDefault();
		data = new FormData($('#customer-group-form')[0]);
		if(!xss_validation(data)){ return false; }
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
		$("#"+this_id).attr('disabled',true);
		$.ajax({
			type: 'POST',
			url: base_url+'customer_groups/update_group',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			success: function(result){
				if(result=="success")
				{
					window.location=base_url+"customer_groups/view";
				}
				else if(result=="failed")
				{
					toastr["error"]("Sorry! Failed to save Record.Try again!");
				}
				else
				{
					toastr["error"](result);
				}
				$("#"+this_id).attr('disabled',false);
				$(".overlay").remove();
			}
		});
    }
});

function update_status(id,status)
{
	var base_url=$("#base_url").val();
	$.post(base_url+"customer_groups/update_status",{id:id,status:status},function(result){
		if(result=="success")
		{
			toastr["success"]("Status Updated Successfully!");
			success.currentTime = 0; 
			success.play();
			if(status==0)
			{
				status="Inactive";
				var span_class="label label-danger";
				$("#span_"+id).attr('onclick','update_status('+id+',1)');
			}
			else{
				status="Active";
				var span_class="label label-success";
				$("#span_"+id).attr('onclick','update_status('+id+',0)');
			}
			$("#span_"+id).attr('class',span_class);
			$("#span_"+id).html(status);
			return false;
		}
		else if(result=="failed"){
			toastr["error"]("Failed to Update Status.Try again!");
			failed.currentTime = 0; 
			failed.play();
			return false;
		}
		else{
			toastr["error"](result);
			failed.currentTime = 0; 
			failed.play();
			return false;
		}
	});
}

function delete_group(q_id)
{
	var base_url=$("#base_url").val();
   if(confirm("Do You Wants to Delete Record ?")){
   	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"customer_groups/delete_group",{q_id:q_id},function(result){
   	if(result=="success")
		{
			toastr["success"]("Record Deleted Successfully!");
			$('#example2').DataTable().ajax.reload();
		}
		else if(result=="failed"){
			toastr["error"]("Failed to Delete .Try again!");
		}
		else{
			toastr["error"](result);
		}
		$(".overlay").remove();
		return false;
   });
   }
}

function multi_delete(){
	var base_url=$("#base_url").val();
    var this_id=this.id;
	if(confirm("Are you sure ?")){
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
		$("#"+this_id).attr('disabled',true);
		data = new FormData($('#table_form')[0]);
		$.ajax({
			type: 'POST',
			url: base_url+'customer_groups/multi_delete',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			success: function(result){
				result=result;
				if(result=="success")
				{
					toastr["success"]("Record Deleted Successfully!");
					success.currentTime = 0; 
					success.play();
					$('#example2').DataTable().ajax.reload();
					$(".delete_btn").hide();
					$(".group_check").prop("checked",false).iCheck('update');
				}
				else if(result=="failed")
				{
				   toastr["error"]("Sorry! Failed to save Record.Try again!");
				   failed.currentTime = 0; 
				   failed.play();
				}
				else
				{
					toastr["error"](result);
					failed.currentTime = 0; 
					failed.play();
				}
				$("#"+this_id).attr('disabled',false);
				$(".overlay").remove();
		   }
		});
	}
}

function add_existing_customers(group_id, group_name) {
	var base_url=$("#base_url").val();
	
	// Ensure parameters are valid
	if (!group_id) {
		toastr["error"]("Invalid group ID");
		return;
	}
	
	// Ensure group_name is a string and handle undefined/null values
	if (typeof group_name === 'undefined' || group_name === null) {
		group_name = '';
	}
	
	// Sanitize group_name to prevent XSS
	group_name = String(group_name).replace(/[<>'"&]/g, function(match) {
		return {
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#x27;',
			'&': '&amp;'
		}[match];
	});
	
	// Create a modal to select customers
	var modalHtml = '<div class="modal fade" id="addCustomersModal" tabindex="-1" role="dialog" aria-labelledby="addCustomersModalLabel" aria-hidden="true">';
	modalHtml += '<div class="modal-dialog modal-lg" role="document">';
	modalHtml += '<div class="modal-content">';
	modalHtml += '<div class="modal-header">';
	modalHtml += '<h4 class="modal-title" id="addCustomersModalLabel">Add Existing Customers to Group: ' + group_name + '</h4>';
	modalHtml += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	modalHtml += '</div>';
	modalHtml += '<div class="modal-body">';
	modalHtml += '<div class="row"><div class="col-md-12"><select id="customerSelect" class="form-control select2" multiple="multiple" style="width: 100%;" data-placeholder="Select customers to add...">';
	modalHtml += '</select></div></div>';
	modalHtml += '<div class="row"><div class="col-md-12" style="margin-top: 20px;"><div id="selectedCustomersList"></div></div></div>';
	modalHtml += '</div>';
	modalHtml += '<div class="modal-footer">';
	modalHtml += '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
	modalHtml += '<button type="button" class="btn btn-primary" id="assignCustomersBtn">Assign to Group</button>';
	modalHtml += '</div>';
	modalHtml += '</div>';
	modalHtml += '</div>';
	modalHtml += '</div>';
	
	// Remove any existing modal
	$('#addCustomersModal').remove();
	
	// Add the modal to the page
	$('body').append(modalHtml);
	
	// Show the modal
	$('#addCustomersModal').modal('show');
	
	// Load customers into the select2
	$.post(base_url + 'customers/get_customers_list', {}, function(result) {
		try {
			var data = JSON.parse(result);
			var selectElement = $('#customerSelect');
			
			$.each(data, function(index, customer) {
				selectElement.append('<option value="' + customer.id + '">' + customer.customer_name + ' (' + (customer.mobile || '') + ')</option>');
			});
			
			// Initialize select2
			selectElement.select2({
				placeholder: "Select customers to add to this group...",
				allowClear: true
			});
			
			// Handle selection change
			selectElement.on('change', function() {
				var selectedValues = $(this).val();
				var selectedHtml = '<h5>Selected Customers:</h5><ul>';
				if(selectedValues && selectedValues.length > 0) {
					$.each(selectedValues, function(index, customerId) {
						var selectedOption = selectElement.find('option[value="' + customerId + '"]');
						selectedHtml += '<li>' + selectedOption.text() + '</li>';
					});
				} else {
					selectedHtml += '<li>No customers selected</li>';
				}
				selectedHtml += '</ul>';
				$('#selectedCustomersList').html(selectedHtml);
			});
		} catch(e) {
			console.error('Error parsing customer list:', e);
			toastr["error"]("Error loading customer list");
		}
	}).fail(function() {
		toastr["error"]("Failed to load customer list");
		// Keep the modal open and show an inline error so the user sees the issue
		if (!$('#addCustomersModal .modal-body .alert-danger').length) {
			$('#addCustomersModal .modal-body').prepend('<div class="alert alert-danger" role="alert">Unable to load customers. Please check permissions or try again.</div>');
		}
	});
	
	// Handle assign button click
	$(document).off('click', '#assignCustomersBtn').on('click', '#assignCustomersBtn', function() {
		var selectedCustomers = $('#customerSelect').val();
		if(!selectedCustomers || selectedCustomers.length === 0) {
			toastr["warning"]("Please select at least one customer to assign!");
			return;
		}
		
		// Show confirmation
		if(confirm("Are you sure you want to assign " + selectedCustomers.length + " customer(s) to this group?")) {
			$(".modal-content").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
			
			$.post(base_url + 'customer_groups/assign_customers_to_group', {
				customer_ids: selectedCustomers,
				group_id: group_id
			}, function(result) {
				$(".overlay").remove();
				if(result == "success") {
					toastr["success"]("Customers assigned to group successfully!");
					$('#addCustomersModal').modal('hide');
				} else {
					toastr["error"]("Failed to assign customers to group. Please try again.");
				}
			}).fail(function() {
				$(".overlay").remove();
				toastr["error"]("Network error. Please try again.");
				// Keep the modal open so the user can retry
				if (!$('#addCustomersModal .modal-body .alert-danger').length) {
					$('#addCustomersModal .modal-body').prepend('<div class="alert alert-danger" role="alert">Network error assigning customers. Please try again.</div>');
				}
			});
		}
	});
	
	// Handle modal close
	$('#addCustomersModal').on('hidden.bs.modal', function () {
		$(this).remove();
	});
}

// Use event delegation to handle clicks on dynamically generated links
$(document).on('click', '.add-existing-customers', function(e) {
	e.preventDefault();
	var groupId = $(this).data('group-id');
	var groupName = $(this).data('group-name');
	
	// Ensure groupName is properly handled as a string
	if(typeof groupName === 'undefined' || groupName === null) {
		groupName = '';
	}
	
	add_existing_customers(groupId, groupName);
});
