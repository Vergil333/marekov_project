$(document).ready(function(){
  $("#snmpBtn").click(function(){
    $.ajax({
      url: "include/functions.php",
      data: { snmp_dev_ip: $(this).val() },
      type: "GET",
      success: function(response) { $("#snmpDiv").html(response); }
    });
  });
  $("#cstmrInputEditBtn").click(function(){
    $.ajax({
      url: "include/functions.php",
      data: { customer_id: $(this).val() },
      type: "GET",
      success: function(response) { $("#cstmrInputContent").html(response); }
    });
  });
  $("form").submit(function(event){
    event.preventDefault();
    var formId = $(this).attr("id");
    var formVal = $(this).serialize();
    $.ajax({
      url: "include/functions.php",
      data: { formId: formId, formVal },
      type: "GET",
//      success: function(response) { alert(response); }
      success: function(response) { if(!alert(response)){window.location.reload();} }
    });
  });
  $("#cstmrInputContent").on("submit", "form:not(#cstmrInputDevUpdate)", function(evt) {
    evt.preventDefault();
    var formId = $(this).attr("id");
    var formVal = $(this).serialize();
    $.ajax({
      url: "include/functions.php",
      data: { formId: formId, formVal },
      type: "GET",
      success: function(response) { if(!alert(response)){window.location.reload();} }
    });
  });
  $("#cstmrInputContent").on("submit", "#cstmrInputDevUpdate", function(evt) {
    evt.preventDefault();
    var ciid = $("#cstmrInputDevUpdate :hidden").serializeArray();
    var optdata = $("#cstmrInputDevUpdate input:checkbox").map(function() {
      return { name: this.name, value: this.checked ? this.value : "0" };
    });
    var myobj_array = $.map(optdata, function(name, value) {
      return [name,value];
    });
    var filtered = myobj_array.filter(function (value) {
      return typeof value !== "number";
    })
    $.ajax({
      url: "include/functions.php",
      data: { customer_input_id: ciid, opt: filtered },
      type: "POST",
      success: function(response) { if(!alert(response)){window.location.reload();} }
    });
  });
});
