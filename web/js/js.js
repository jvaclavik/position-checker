$(document).ready(
  function() {
    init();
  }
  );

function init() {
  $('#addKeywordsForm').hide()
  $('#addWebsiteForm').hide()
  $('.jtable').dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "sScrollX": "100%",
    "sScrollXInner": "100%",
    "iDisplayLength": 50,
    "bScrollCollapse": true
  });
  
  // Datepicker
  $('#showStats_from').datepicker({
    dateFormat: 'yy-mm-dd'
  });
  $('#showStats_to').datepicker({
    dateFormat: 'yy-mm-dd'
  });
  $('#focusTable_filter input').focus();
  

  
  
  $('#tabs').tabs();
  
  jQuery.fn.dataTableExt.oSort['html-undefined']  = function(a,b) {
    return false;
  };
  $('.sorting-disabled').unbind('click');
  
  
  $('#addWebsiteToggle').click(function(e){
    $('#addWebsiteForm').toggle("fast")
    return false;
  });
  
  $('#addKeywordsToggle').click(function(e){
    $('#addKeywordsForm').toggle("fast")
    return false;
  });
  $('.delete').click(function(e){
    if (!confirm("Opravdu chcete položku smazat? Data budou nenávratně ztracena.")) {
      return false;
    }
  });
  
  
  $('.changeActiveWebsite').click(
    function(e){
      $.ajax({
        url: url,
        type:"POST",
        contentType: 'application/json',
        data:json,
        success:function (data) {
          $(this).html(data);
        }
      });
      return false;
    }
    );
  
  
  $('#addWebsiteButton').click(
    function(e){
      
      //value from input
      //            var email = $(this).val();
      
      //send form
      $.get("/Symfony/web/addWebsiteAjax", {
        website: website,
        keywords: keywords
      }, function ( data ){ 
        //                $("#contact-is-user").html(data.message);
        }, 'JSON');
      //stop
      return false;
    }
    );
  
  
  /*$('#DataTables_Table_0_filter input').keydown(function (e) {
    var keyCode = e.keyCode || e.which,
    arrow = {
      left: 37, 
      up: 38, 
      right: 39, 
      down: 40
    };
    
    switch (keyCode) {
      case arrow.left:
        //..
        break;
      case arrow.up:
        alert( " pressed" );
        //      $(next()+" a").focus();
        break;
      case arrow.right:
        //..
        break;
      case arrow.down:
        alert( "down pressed" );
        break;
    }
  });*/
  
  

  
  
  //  
  //  
  //  
  //  $(".jtable th").each(function(){
  // 
  //    $(this).addClass("ui-state-default");
  // 
  //  });
  //  $(".jtable td").each(function(){
  // 
  //    $(this).addClass("ui-widget-content");
  // 
  //  });
  //  $(".jtable tr").hover(
  //    function()
  //    {
  //      $(this).children("td").addClass("ui-state-hover");
  //    },
  //    function()
  //    {
  //      $(this).children("td").removeClass("ui-state-hover");
  //    }
  //    );
  //  $(".jtable tr").click(function(){
  //   
  //    $(this).children("td").toggleClass("ui-state-highlight");
  //  });
  // 
  
  
  
  




}

