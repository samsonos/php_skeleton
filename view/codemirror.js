/**
 * Created by Vitaly Egorov <egorov@samsonos.com> on 12.06.14.
 */
s(document).pageInit(function(){

    // Bind HTML editor
    var ta = document.getElementsByName('source');
    var hm = CodeMirror.fromTextArea(ta[0], {
        lineNumbers: true,
        mode: "htmlmixed"
    });

    // Bind LESS editor
    var ta2 = document.getElementsByName('output');
    var lm = CodeMirror.fromTextArea(ta2[0], {
        lineNumbers: true,
        matchBrackets : true,
        mode: "text/x-less"
    });

    // Bind LESS Code selection for copy
    s('.selectAll').click(function(){
        lm.execCommand('selectAll');
    }, true, true);

    var loader = s('.overlay');

    var converter = s('.converter');
    // Bind asynchronous form send
    converter.ajaxSubmit(function(response){
        // If we have received converted LESS
        loader.hide();
        if(typeof response.less != 'undefined') {
            // Show it
            lm.setValue(response.less);
        }
    }, function(){
        loader.show();
        return true;
    });

    // Imitate form submit
    s('.submit').click(function(){
        //converter.submit();
        s('#submit').click();
    },true, true);
});