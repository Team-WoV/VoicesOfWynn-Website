//var npcId; - filled by PHP in the view
//var userId; - filled by PHP in the view
//var userName; - filled by PHP in the view
//var userAvatar; - filled by PHP in the view
var commentItemHtml = '<div class="comment" style="margin: auto;"><table><tr><td rowspan="2"><img src="{gravatar}" alt="Avatar" class="comment-avatar" /></td><td><strong>{name}</strong><button data-comment-id="{id}" class="delete-comment-button">×</button></td></tr><tr><td><div class="comment-content">{comment}</div></td></tr></table></div><br>';

$("#new-comment-button").on('click', function () {
    $("form").slideDown(1500);
    $("#new-comment-button").hide();
    $("#hide-form-button").show();
})
$("#hide-form-button").on('click', function () {
    $("form").slideUp(1500);
    $("#hide-form-button").hide();
    $("#new-comment-button").show();
})

$("#contributor-option").on('click', function () {
    $("#contributor-option").addClass('selected');
    $("#guest-option").removeClass('selected');
    $("#guest-form").hide();
    $("#contributor-form").show();
})
$("#guest-option").on('click', function () {
    $("#guest-option").addClass('selected');
    $("#contributor-option").removeClass('selected');
    $("#contributor-form").hide();
    $("#guest-form").show();
})

$("form").on('submit', function (event) {
    event.preventDefault();
    let recordingId = $(event.target).attr('data-recording-id');
    let name, email, content, antispam, verified;
    if ($("#contributor-option").length === 1 && $("#contributor-option").hasClass('selected')) {
        //Posting as a contributor
        verified = true;
        content = $("#content-contributor").val();
    }
    else {
        verified = false;
        name = $("#name").val();
        email = $("#email").val();
        content = $("#content-guest").val();
        antispam = $("#antispam").val();
    }

    $.ajax({
        url: "contents/npc/" + npcId + "/comments/" + recordingId + "/new",
        type: 'POST',
        data: {
            'verified': verified,
            'name': name,
            'email': email,
            'content': content,
            'antispam': antispam
        },
        success: function (result, message) {
            let name, gravatar, content;
            if ($("#contributor-option").length === 1 && $("#contributor-option").hasClass('selected')) {
                name = "<a href='cast/" + userId + "'>" + userName + "</a>";
                gravatar = userAvatar;
                content = $("#content-contributor").val().replace(/\n/g, '<br>');
            }
            else {
                name = $("#name").val();
                if (name === '') {
                    name = 'Anonymous';
                }
                gravatar = "https://www.gravatar.com/avatar/" + md5(email) + "?d=identicon";
                content = $("#content-guest").val().replace(/\n/g, '<br>');
            }

            let comment;
            comment = commentItemHtml.replace('{name}', name);
            comment = comment.replace('{gravatar}', gravatar);
            comment = comment.replace('{id}', result); //Response from the server is just the number representing the ID of the new comment
            comment = comment.replace('{comment}', content);
            $("#comments").prepend($(comment));
            $("#hide-form-button").click();
            $("#comments :first-child").fadeIn(3500);
            $("#content-contributor").val("");
            $("#content-guest").val("");

        },
        error: function (result, message, error) {
            alert("An error occurred: " + error);
        }
    });
});

var $deletingComment;
$(".delete-comment-button").on('click', function (event) {
    if (!confirm('Do you really want to delete this comment?')) {
        return;
    }

    $deletingComment = $(event.target).closest('.comment');

    $.ajax({
        url: "contents/npc/" + npcId + "/comments/" + $("form").attr('data-recording-id') + "/delete/" + $(event.target).attr('data-comment-id'),
        type: 'DELETE',
        success: function (result, message) {
            $deletingComment.slideUp(500);
            $deletingComment = undefined;
        },
        error: function (result, message, error) {
            alert("An error occurred: " + error);
        }
    });
});