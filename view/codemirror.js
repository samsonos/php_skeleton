/**
 * Created by Vitaly Egorov <egorov@samsonos.com> on 12.06.14.
 */
s(document).pageInit(function(){
    var ta = document.getElementsByName('source');
    var myCodeMirror = CodeMirror.fromTextArea(ta[0], {
        lineNumbers: true,
        mode: "htmlmixed"
    });
});