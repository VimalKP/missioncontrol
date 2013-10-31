<?php
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Pagination</title>
        <style type="text/css">
            @charset "utf-8";
            /* CSS Document */
            body
            {
                background:url(images/bg.jpg);
                margin-top:100px;
                font-family:"MS Serif", "New York", serif
            }
            table {
                *border-collapse: collapse; /* IE7 and lower */
                border-spacing: 0;
                width: 450px; 
                text-transform:capitalize;   
            }
            tr{
                background: #FFFFD9;
            }    

            td,th {
                border-left: 1px solid #ccc;
                border-right: 1px solid #ccc;
                border-top: 1px solid #ccc;
                border-bottom: 1px solid #ccc;
                padding: 10px;
                text-align:center    
            }

            th {
                background-color: #dce9f9;
                background-image: -webkit-gradient(linear, left top, left bottom, from(#ebf3fc), to(#dce9f9));
                background-image: -webkit-linear-gradient(top, #ebf3fc, #dce9f9);
                background-image:    -moz-linear-gradient(top, #ebf3fc, #dce9f9);
                background-image:     -ms-linear-gradient(top, #ebf3fc, #dce9f9);
                background-image:      -o-linear-gradient(top, #ebf3fc, #dce9f9);
                background-image:         linear-gradient(top, #ebf3fc, #dce9f9);
                -webkit-box-shadow: 0 1px 0 rgba(255,255,255,.8) inset; 
                -moz-box-shadow:0 1px 0 rgba(255,255,255,.8) inset;  
                box-shadow: 0 1px 0 rgba(255,255,255,.8) inset;        
                border-top: none;
                text-shadow: 0 1px 0 rgba(255,255,255,.5); 
            }


            .paginate {
                font-family:Arial, Helvetica, sans-serif;
                padding: 3px;
                margin: 3px;
                width: 600px;
            }

            .paginate a {
                padding:2px 8px 2px 8px;
                /*margin:2px;*/
                border:1px solid #999;
                text-decoration:none;
                color: #666;
            }
            .paginate a:hover, .paginate a:active {
                border: 1px solid #999;
                color: #000;
            }
            .paginate span.current {
                margin: 2px;
                padding: 2px 5px 2px 5px;
                border: 1px solid #999;

                font-weight: bold;
                background-color: #999;
                color: #FFF;
            }
            .paginate span.disabled {
                padding:2px 5px 2px 5px;
                margin:2px;
                border:1px solid #eee;
                color:#DDD;
            }

            li{
                padding:4px;
                margin-bottom:3px;
                background-color:#FCC;
                list-style:none;}

            ul{margin:6px;
               padding:0px;}
            </style>
            <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
        </head>
        <body>
            <script type="text/javascript">
                $(function(){
                    getPageData('1');
                });
                function getPageData(pageNo){
                    $.ajax({
                        url:"fetchData.php",
                        data:{'page':pageNo},
                        type: "POST",
                        success:function(data){
                            $("#tblData").html(data);
                        }
                    });                 
                }
            </script>
            <table width="876"  border="1" align="center" cellspacing="0" id="tblData">

        </table>
    </body>
</html>