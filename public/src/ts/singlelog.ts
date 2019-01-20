import '../modules/common';

function edit(): void {
    const logid: string = $('#logid').val();
    location.assign(`edit/${logid}`);
}

function showAddCommentForm(): void {
    $('#newComment').val('');
    $('#add-comment-form').show();
    $('#newComment').focus();
}

function hideAddCommentForm(): void {
    $('#add-comment-form').hide();
}

function saveComment(e: Event): boolean {
    e.preventDefault();
    e.returnValue = false;

    const commentText: string = $('#newComment').val();
    const logid: string = $('#logid').val();

    if (!commentText) {
        $('#error-message').text('Comments can not be blank');
        return false;
    }

    $.post('../api/i/comments/add', { 'comment': commentText, 'logid': logid }, null, 'json')
        .done(() => location.reload());

    return true;
}

function init(): void {
    $('#add-comment-btn').click(showAddCommentForm);
    $('#add-comment-form').submit(saveComment);
    $('#cancel-new-btn').click(hideAddCommentForm);
    $('#edit-log-btn').click(edit);
}
init();
