<?php
echo "hiii run".'<br/>';
exit();
//header_remove();
header("X-Frame-Options: ALLOW-FROM suppliedorigin");
ob_start();
include_once 'db_connection.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Question - Answer</title>
        <link href="css.css" type="text/css" rel="stylesheet" />
        <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    </head>

    <body>
        <script type="text/javascript">
            $(document).ready(function(){
                getNextQue('first');
            });
            function getNextQue(type){
                var currentId=$("#qa_id").val();
                var answere=$('#answere').val();
//                if(answere=='' && type!='first'){
////                    alert("Please give answere.");
//                }else{
                    $.ajax({
                        url:"question.php",
                        type:'post',
                        data:{'qa_id':currentId,'type':type},
                        beforeSend: function(){
                            unoload();
                            if(type!='first')
                                find_answeres(currentId)
                        },
                        success:function( response )
                        {
                            unoloaded();
                            obj = JSON.parse(response);
                            $(".link").html(obj.question);
                            $("#qa_id").val(obj.nextQueId);
                        }
                    }); 
                }
//            }
            function find_answeres(queno){
                var answere=$('#answere').val();
                var extra_id=$('#extra_id').val();
                $.ajax({
                    url:"findans.php",
                    type:'post',
                    data:{'qa_id':queno,'handler':'ford','find':answere,'extra_id':extra_id},
                    success:function( response )
                    {
                      console.log(response);
                      if(response=='fail'){
                          $('#answere').css('border','1px solid red');
                      }
                      $('#answere').val('');
                        if(response!='pass' && response!='fail' && response!='No query found for that question.'){
                            $('#extra_id').val(response);
                        }
                    }
                });
            }
            function unoload(text , zindex)
            {
                var html = jQuery('html');
                html.css('overflow', 'hidden').scrollTop();
                var horizontalCenter = ($(window).width()/2)-85;
                var verticalCener = (html.outerHeight()/2)-85;
                zindex =zindex || 700;
                text=typeof(text)!='undefined'?text:'Loading';
                var textobj = $('<span/>').html('&nbsp;&nbsp;'+text+'&nbsp;&nbsp;');
                var randomoverlay=$('<div class="unoapploadingoverlay"  style="position:absolute;height:100%;width:100%;background:#f5f5f5;z-index:'+zindex+';opacity:0.7;filter: alpha(opacity=70);-moz-opacity:0.7;-khtml-opacity: 0.7;top:0"></div>');
                text = '&nbsp;&nbsp;'+text+'&nbsp;&nbsp;';
                var str=['.&nbsp;&nbsp;','..&nbsp;','...']; 
                var dots = $('<span></span>') ;
                var text_span = $('<span></span>').append(textobj , dots); 
                var loadingdiv=$('<div class="transparentpopup" id="unoapploading" style="padding-top:'+verticalCener+'px;line-height: 40px;position: absolute;padding-left:'+horizontalCenter+'px"></div>').css({
                    "z-index": zindex+1
                });

                var loadimg=$('<img id="rimg" src="images/loading.gif" style="vertical-align:middle;" width="35"  />');
                $('body').prepend(randomoverlay);
                $('body').prepend(loadingdiv);
                loadingdiv.prepend(loadimg,text_span);
                return textobj;
            }


            function unoloaded()
            {
                var html = jQuery('html');
                $("#unoapploading").animate({
                    opacity:'0.1'
                },function(){
                    $('#unoapploading, .unoapploadingoverlay').remove();
                    html.css('overflow', 'scroll').scrollTop();
                });
    
    
                //$("#unoapploading").css("display","");
        
            }
        </script>
        <form name="questionFrm" id="questionFrm">
            <input type="hidden" name="qa_id" id="qa_id" value="1"/>
            <input type="hidden" name="extra_id" id="extra_id" value=""/>
            <div class="container">
                <iframe src="http://web.stagram.com/n/ford" style="width:100%; height:500px; border:1px solid #000; border-bottom:none;">
<!--                    <div style="width:100%; height:500px; border:1px solid #000; border-bottom:none;"> <a href="http://www.instagram.com/ford" target="_blank">http://www.instagram.com/ford</a</div>-->
                </iframe>
<!--                <div class="frame">
                </div>
-->                <div class="btn-div">
                    <div class="btn-back"><a href="#"><img src="images/btn-back.png" alt="" onclick="getNextQue(this.id);" id="prev"/></a></div>
                    <center><div class="link" style="color: red;width: 86%;background-color: #eee;"></div></center>
<!--                    <div class="link"><a href="#">copy the current number of followers.</a></div>-->
                    <div class="btn-next"><a href="#"><img src="images/btn-next.png" alt="" onclick="getNextQue(this.id);" id="next"/></a></div>
                    <div style="clear:both"></div>
                </div>
                <div class="input">
                    <input type="text" id="answere" placeholder="answer." />
                </div>
            </div>
        </form>

    </body>
</html>
