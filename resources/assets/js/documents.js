$(document).ready(() => {
    console.log('swagging');

    $('.trumbowyg-editor').css('height', 600);

    $(document).on('change', '#revert-to', async (e) => {
        let revisionId = parseInt(e.target.value);

        let result = await swal({
            title: 'Warning',
            text: "This will overwrite your current content. Are you sure you want to do this?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4caf50',
            cancelButtonColor: '#999',
            confirmButtonText: 'Yes'
        });

        if (!result.value) {
            //Reset the select
            $(e.target).val('0');
            return false;
        }

        let revisions = JSON.parse($('input[name=revision_json]').val());

        let revision = _.find(revisions, (revision) => {
            return revision.id === revisionId
        });

        if (revision) {
            $('#title').val(revision.title);
            $('.trumbowyg-textarea').trumbowyg('html', revision.content);

            //Reset the select
            $(e.target).val('0');
        }
    });
});
