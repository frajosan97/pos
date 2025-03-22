@extends('layouts.app')

@section('pageTitle', 'Chat')

@section('content')

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card shadow-none rounded-0">
            <div class="card-body">

                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-md-4 col-lg-3 chat-sidebar border-end">
                        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="m-0">Chats</h5>
                            <div class="">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newGroupModal">
                                    <i class="bi bi-people"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-3 bg-white">
                            <input type="text" id="chat-search" class="form-control" placeholder="Search chats...">
                        </div>
                        <ul class="list-group list-group-flush overflow-auto chat-list">
                        </ul>
                    </div>

                    <!-- No message chat box -->
                    <div class="col-md-8 col-lg-9 chat-window bg-light px-0 no-msg-chat-window">
                        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                            <div class="text-center">
                                <i class="fas fa-sms fa-2x mb-2"></i>
                                <p class="text-muted">No messages to display</p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages chat box -->
                    <div class="col-md-8 col-lg-9 chat-window bg-light px-0 msg-chat-window d-none">
                        <!-- Chat header -->
                        <div id="chat-header" class="p-3 bg-white border-bottom">
                            <h5 class="m-0 text-capitalize" id="chat-header-text"></h5>
                        </div>

                        <!-- Chat messages container -->
                        <div id="chat-messages-container" class="flex-grow-1 p-3 overflow-auto chat-messages"></div>

                        <!-- Chat input area -->
                        <div id="chat-input-area" class="p-3 bg-white border-top">
                            <form action="{{ route('message.send') }}" method="post" id="send-sms-form">
                                <div class="input-group">
                                    <input type="hidden" name="receiver_id" id="receiver_id" value="" />
                                    <input type="text" id="message-input" name="message" class="form-control"
                                        placeholder="Type a message..." required autocomplete="off" />
                                    <button type="submit" id="send-message" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newChatModalLabel">Start New Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="create-chat-form">
                    <select name="user_id" id="user_id" class="form-control mb-3" required>
                        <option value="">Select User</option>
                        @foreach($employees as $employee)
                        @if(!($employee->id===auth()->user()->id))
                        <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                        @endif
                        @endforeach
                    </select>
                    <button class="btn btn-primary w-100" id="startChat">Start Chat</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- New Group Modal -->
<div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newGroupModalLabel">Create New Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" id="groupName" placeholder="Group Name">
                <button class="btn btn-primary w-100" id="createGroup">Create Group</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Get chats
        function loadChats() {
            $.get(`{{ url('chat/load-chats') }}`, function(data) {
                // Get chats box
                let chatsContainer = $('.chat-list');
                chatsContainer.html('');

                // bind chats
                if (data.chats.length > 0) {
                    data.chats.forEach(chat => {
                        let unreadBadge = chat.unread_count > 0 ?
                            `<span class="badge bg-success rounded-circle px-2 py-1">${chat.unread_count}</span>` :
                            '';

                        let chatBlock = `
                            <li class="list-group-item list-group-item-action chat-item d-flex align-items-start gap-2 py-2"
                                data-conversation-id="${chat.conversation_id}" 
                                data-id="${chat.id}"
                                data-name="${chat.name}">
                                
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <img src="${chat.avatar ?? '/assets/images/defaults/passport.png'}" alt="Avatar" class="rounded-circle" width="45" height="45">
                                </div>

                                <!-- Chat content -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-capitalize text-truncate" style="max-width: 120px;">${chat.name}</strong>
                                        <small class="text-muted">${chat.last_message_time ?? ''}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="small text-muted text-truncate mb-0" style="max-width: 120px;">
                                            ${chat.last_message ?? 'No messages yet'}
                                        </p>
                                        ${unreadBadge}
                                    </div>
                                </div>
                            </li>
                        `;

                        chatsContainer.append(chatBlock);
                    });
                } else {
                    chatsContainer.html(`
                        <li class="list-group-item text-center text-muted" id="no-chats">
                            <i class="fas fa-users fa-2x"></i>
                            <p>No chats available</p>
                        </li>
                    `);
                }
            }).fail(function() {
                Swal.fire('Error', 'Failed to fetch user details.', 'error');
            });
        }

        // Start new chat form
        $("#create-chat-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                const userId = $('#user_id').val();
                const url = `{{ url('chat/get-user') }}/${userId}`;
                $('#newChatModal').modal('hide');

                // Show loading state in SweetAlert
                Swal.fire({
                    title: 'Loading...',
                    text: 'Getting User Information.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.get(url, function(data) {
                    // Close the loading SweetAlert
                    Swal.close();
                    // Add real chatbox
                    $('.no-msg-chat-window').addClass('d-none');
                    $('.msg-chat-window').removeClass('d-none');
                    // Clear message box
                    $('#chat-messages-container').html(`
                        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                            <div class="text-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <p>Loading Messages...</p>
                            </div>
                        </div>
                    `);
                    // update chat-box
                    $('#chat-header-text').text(data.user.name);
                    $('#receiver_id').val(data.user.id);
                    $('#message-input').focus();
                    loadMessages(data.conversation_id);
                }).fail(function() {
                    Swal.fire('Error', 'Failed to fetch user details.', 'error');
                });
            }
        });

        $("#send-sms-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = new FormData(form);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content')); // Add CSRF token

                // Submit
                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token in headers
                    },
                    success: function(response) {
                        // clear message input
                        $('#message-input').val('');
                        $('#send-message').addClass('d-none');
                        // Update chatbox
                        loadMessages(response.data.conversation_id);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || xhr.responseJSON.message);
                    }
                });
            }
        });

        // Open Chat on Click
        $(document).on('click', '.chat-item', function() {
            currentConversationId = $(this).data('conversation-id');
            let chatName = $(this).data('name');
            let chatId = $(this).data('id');
            // Show chat window and hide placeholder
            $('.no-msg-chat-window').addClass('d-none');
            $('.msg-chat-window').removeClass('d-none');
            // Clear message box
            $('#chat-messages-container').html(`
                <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                    <div class="text-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <p>Loading Messages...</p>
                    </div>
                </div>
            `);
            // Update chat header
            $('#chat-header-text').text(chatName);
            $('#receiver_id').val(chatId);
            $('#message-input').focus();
            // Load messages
            loadMessages(currentConversationId);
        });

        // Load Messages
        function loadMessages(conversationId) {
            $.ajax({
                url: `/chat/messages/${conversationId}`,
                type: 'GET',
                success: function(data) {
                    let messagesContainer = $('.chat-messages');
                    messagesContainer.html('');

                    if (data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            let messageClass = msg.sender_id == '{{ auth()->id() }}' ? 'sent' : 'received';
                            let messageBlock = `
                                <div class="message ${messageClass}">
                                    <div class="message-text">${escapeHtml(msg.message)}</div>
                                    <div class="message-time">${formatDate(msg.created_at)}</div>
                                </div>
                            `;
                            messagesContainer.append(messageBlock);
                        });

                        messagesContainer.scrollTop(messagesContainer.prop("scrollHeight"));
                    } else {
                        messagesContainer.html(`
                        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                            <div class="text-center">
                                <i class="fas fa-sms fa-2x mb-2"></i>
                                <p class="text-muted">No messages to display</p>
                            </div>
                        </div>`);
                    }
                },
                error: function(xhr) {
                    alert("Error loading messages:", xhr.responseText);
                }
            });
        }

        $('#message-input').on('keyup', function() {
            var input = $(this).val();
            if (input.length > 0) {
                $('#send-message').removeClass('d-none');
            } else {
                $('#send-message').addClass('d-none');
            }
        });

        setInterval(function() {
            loadChats();
        }, 1000);
    });
</script>
@endpush