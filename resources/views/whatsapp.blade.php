<!DOCTYPE html>
<html>
<head>
    <title>Send WhatsApp Message</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="p-6">

    <h2>Send WhatsApp Text</h2>

    <form id="sendTextForm">
        <label>Recipient (WhatsApp Number)</label><br>
        <input type="text" name="recipient" id="recipient" class="border p-2" required><br><br>

        <label>Message</label><br>
        <textarea name="text" id="text" class="border p-2" required></textarea><br><br>

        <button type="submit" id="submitBtn">Send Message</button>
    </form>

    <br>
    <div id="responseBox" style="padding:10px; border:1px solid #ddd; display:none;"></div>

    <!-- jQuery (optional but easiest) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {

            $("#sendTextForm").on("submit", function (e) {
                e.preventDefault();

                let formData = {
                    recipient: $("#recipient").val(),
                    text: $("#text").val(),
                };

                $.ajax({
                    url: "/send-text",
                    type: "POST",
                    data: JSON.stringify(formData),
                    contentType: "application/json",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },

                    beforeSend: function () {
                        $("#submitBtn").prop("disabled", true).text("Sending...");
                    },

                    success: function (response) {
                        $("#responseBox")
                            .show()
                            .html("<strong>Success:</strong> " + JSON.stringify(response));
                    },

                    error: function (xhr) {
                        $("#responseBox")
                            .show()
                            .html("<strong>Error:</strong> " + xhr.responseText);
                    },

                    complete: function () {
                        $("#submitBtn").prop("disabled", false).text("Send Message");
                    }
                });
            });

        });
    </script>

</body>
</html>
