// import './bootstrap';
import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
});


// Your other JavaScript code
// Replace your existing loadMessages function
function loadMessages(receiverId) {
    window.Echo.private('message.' + receiverId)
        .listen('MessageSent', (event) => {
            // Handle the new message event and update the UI
            console.log('Received new message:', event.message);
            updateMessageUI(receiverId, event.message);
        });
}

// Replace your existing sendMessage function
function sendMessage(receiverId) {
    var messageInput = $('#message-input-' + receiverId);
    var fileInput = $('#attached-' + receiverId)[0];
    var message = messageInput.val();

    var formData = new FormData();
    formData.append('receiver_id', receiverId);
    formData.append('message', message);

    // Check if a file is selected before appending it to formData
    if (fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    $.ajax({
        type: 'POST',
        url: '/save-message',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (savedMessage) {
            // Clear the input fields
            messageInput.val('');
            fileInput.value = ''; // Clear the file input

            // No need to load messages here, as it will be handled by the pusher event
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error('Error sending message:', errorThrown);
        }
    });
}

// Function to update the UI with a new message
function updateMessageUI(receiverId, message) {
    var messageList = $('#message-list-' + receiverId);
    if (['jpg', 'jpeg', 'png', 'gif'].includes(message.file)) {
        // Display image with a download link
        var imageHtml = '<div class="message">';
        imageHtml += '<img src="https://engagedlearning.net/message/' + message.content + '" alt="File">';
        imageHtml += '<a href="https://engagedlearning.net/message/' + message.content + '" download="' + message.filename + '">Download Image</a>';
        imageHtml += '</div>';
        messageList.append(imageHtml);
    } else if (message.file === 'pdf') {
        // Display PDF using <embed> with a download link
        var pdfHtml = '<div class="message">';
        pdfHtml += '<a href="https://engagedlearning.net/message/' + message.content + '" download="' + message.filename + '">Download PDF</a>';
        pdfHtml += '</div>';
        messageList.append(pdfHtml);
    } else if (message.file === 'text') {
        // Display text for text messages
        messageList.append('<li>' + message.content + '</li>');
    }
}

