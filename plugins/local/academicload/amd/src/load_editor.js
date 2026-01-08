define([
    'jquery',
    'core_form/modalform',
    'core/toast',
    'core/str'
], function($, ModalForm, Toast, Str) {

    function bindAssignModal(){
        const root = $('.local-academicload');

        root.on('click', '.js-assign, .js-retry',
            function(e) {
                e.preventDefault();
                const subjectid = $(this).data('subjectid');
                const cohortid = $(this).data('cohortid');
                const teacherid = $(this).data('teacherid') || 0;

                const titlePromise = getString('assignteacher');
                const toastPromise = getString('teacherassigned');
                console.log("voy bien");
                
                Promise.all(
                    [titlePromise, toastPromise]
                ).then(function(strings){
                    const title = strings[0];
                    const msg = strings[1];

                    const modal = new ModalForm({
                        formClass: 'local_academicload\\form\\assign_modal',
                        args: {
                            subjectid: subjectid,
                            cohortid: cohortid,
                            teacherid: teacherid
                        },
                        modalConfig: {
                            title: title
                        }
                    });

                    modal.addEventListener(
                        modal.EVENTS.FORM_SUBMITTED, function() {
                            Toast.add(msg, {type: 'success'});
                            window.location.reload();
                        }
                    );
                    modal.show();
                });
            }
        );
    }

    function getString(string){
        return Str.get_string(
            string,
            'local_academicload'
        );
    }

    return {
        init: bindAssignModal
    };
});
