jQuery(document).ready(function ($) {
  $("#event-submission-form").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
      url: em_ajax_obj.ajax_url,
      method: "POST",
      data: {
        action: "submit_event",
        event_title: $('input[name="event_title"]').val(),
        event_content: $('textarea[name="event_content"]').val(),
        event_date: $('input[name="event_date"]').val(),
        event_location: $('input[name="event_location"]').val(),
      },
      success: function (response) {
        $("#event-form-message").text(response.data).css("color", "green");
      },
      error: function () {
        $("#event-form-message")
          .text("Something went wrong")
          .css("color", "red");
      },
    });
  });
});
