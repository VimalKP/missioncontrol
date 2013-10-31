<?php
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
        <style>
            #container	{height: 100%;border: 1px solid black;}
            /*            iframe 	{width:90%;height:750px;margin:0% 5%;}*/
            #channel_iframe 	{width:90%;margin:0% 5%;padding-top: 20%;padding-bottom: 20%;}
        </style>
        <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    </head>

    <body>
        <script type="text/javascript">
            $(document).ready(function(){
                getNextQue('next');
            });
            function getNextQue(type){
                var currentquestionId=$("#qa_id").val();
                var index=$("#index").val();
                var answere=$('#answere').val();
                var channel=$('#channel').val();
                var brand=$('#brand').val();
                var firstqa=$('#firstqa').val();
                var ans_id=$('#ans_id').val();
                //                if(answere=='' && type!='first'){
                ////                    alert("Please give answere.");
                //                }else{
                $.ajax({
                    url:"question.php",
                    type:'post',
                    data:{'ans_id':ans_id,'qa_id':currentquestionId,'index':index,'type':type,'channel':channel},
                    beforeSend: function(){
                        unoload();
                        if(firstqa=='' && type=='next' && ans_id!=0 )
                            find_answeres(ans_id,currentquestionId,brand,channel)
                    },
                    success:function( response )
                    {
                        unoloaded();
                        console.log(response)
                        obj = JSON.parse(response);
                        if(obj.nextQueId!=null){
                            $("#index").val(obj.index);
                            $(".link").html(obj.question);
                            $("#qa_id").val(obj.nextQueId);
                            $("#ans_id").val(obj.ans_id);
                            $('#channel_iframe').attr("src","");
                            $('#channel').val(obj.channel);
                            $('#brand').val(obj.handle);
                            $('#firstqa').val('');
                            //                        //                        if(obj.channel=='Instagram'){
                            //                        //                             $('#channel_iframe').attr("src","http://web.stagram.com/n/ford/")
                            //                        //                        }else if(channel=='facebook'){
                            //                        //                             $('#channel_iframe').attr("src","fetchFrame.php?type=facebook&handle=ford")
                            //                        //                        
                            //                        //                        }else if(channel=='twitter'){
                            //                        //                             $('#channel_iframe').attr("src","fetchFrame.php?type=twitter&handle=ford")
                            //                        //                             
                            //                        //                        }else{
                            //                        //                            $('#channel_iframe').attr("src","fetchFrame.php?type=youtube&handle=ford")
                            //                        //  
                            //                        //                                              }
                            var lable='<div style="color: black;">please click on above Link & answer the question stays in below Link</div>';
                            if(obj.channel=='Instagram'){
                                $('#channel_iframe').html("<a href='http://www.instagram.com/"+obj.handle+"' target='_blank'>http://www.instagram.com/"+obj.handle+"<a>"+lable+" ")
                            }else if(channel=='facebook'){
                                $('#channel_iframe').html("<a href='http://www.facebook.com/"+obj.handle+"' target='_blank'>http://www.facebook.com/"+obj.handle+" <a>"+lable+" ")
                        
                            }else if(channel=='twitter'){
                                $('#channel_iframe').html("<a href='http://www.twitter.com/"+obj.handle+"' target='_blank'>http://www.twitter.com/"+obj.handle+" <a>"+lable+" ")
                             
                            }else{
                                $('#channel_iframe').html("<a href='http://www.youtube.com/"+obj.handle+"' target='_blank'>http://www.facebook.com/"+obj.handle+" <a>"+lable+" ")
                            }
                        }else if(type=='next'){
                            $(".link").html("No More Question available please Go back to remain question or just stop here!!");
                            //                            $('#answere').attr('readonly', 'readonly');
                            
                        }else if(type=='prev'){
                            $("#ans_id").val(0);
                            $("#qa_id").val(0);
                            $(".link").html("No More Question available please Go next to remain question or just stop here!!");
                            
                        }
                    }
                }); 
            }
            //            }
            function find_answeres(ans_id,currentquestionId,brand,channel){
                var answere=$('#answere').val();
                var extra_id=$('#extra_id').val();
                $.ajax({
                    url:"findans.php",
                    type:'post',
                    data:{'ans_id':ans_id,'qa_id':currentquestionId,'handler':brand,'find':answere,'extra_id':extra_id,'channel':channel},
                    success:function( response )
                    {
                        console.log(response);
                        if(response=='fail'){
                            $('#answere').css('border','1px solid red');
                        }else{
                            $('#answere').css('border','1px solid black');
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
            <input type="hidden" name="qa_id" id="qa_id" value="0"/>
            <input type="hidden" name="index" id="index" value="0"/>
            <input type="hidden" name="extra_id" id="extra_id" value=""/>
            <input type="hidden" name="channel" id="channel" value=""/>
            <input type="hidden" name="brand" id="brand" value=""/>
            <input type="hidden" name="firstqa" id="firstqa" value="firstqa"/>
            <input type="hidden" name="ans_id" id="ans_id" value="0"/>
            <div class="container" id="container">
<!--                <iframe src="https://www.facebook.com/ford" style="width:100%; height:500px; border:1px solid #000; border-bottom:none;">-->
                <center><div id="channel_iframe">

                    </div>

                </center>


<!--                <iframe id="channel_iframe" src="fetchFrame.php"></iframe>-->
                <div class="btn-div">
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
