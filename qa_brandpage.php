
<!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />-->
<!--<script type="text/javascript" charset="utf-8" language="javascript" src="js/jquery.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
                                                                                                 
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->

<div id="review-data" class="tab_content">
    <input type="hidden" id="channel_name" name="channel_name" value=""/>
    <input type="hidden" id="channel_brand_name" name="channel_brand_name" value=""/>
    <input type="hidden" id="qa_index" name="qa_index" value=""/>
    <input type="hidden" id="main_index" name="main_index" value=""/>
    <input type="hidden" id="qa_id" name="qa_id" value=""/>
    <input type="hidden" id="z_testid_pk" name="z_testid_pk" value=""/>
    <input type="hidden" id="extra_id" name="extra_id" value=""/>
    <input type="hidden" id="post_link" name="post_link" value=""/>
    <input type="hidden" id="userhandle" name="userhandle" value=""/>
    <div class="qa-top" id="top_bar">
        <div class="btn-qa"> <a class="selected" class="" id="review_data" href="">Review Data</a> </div>
        <div class="btn-qa"> <a href="" id="history_data">History</a> </div>
        <div class="qa-top-right">

            <div class="styled-select2">
                <select id="brand_list" style=" width: 144px !important;"onchange="fetch_individualbrandqa_page()">
                </select>
            </div>

            <div class="qa-facebook"> <img src="" alt="" id="brand_image"/><span id="brand_name"></span></div>
            <div class="qa-check">Total Checks : <b><span id="total_checks"></span></b></div>
            <div class="qa-status">Previous Status:<img src="" id="previous_status_icon" alt="" /></div>
            <div class="qa-varified">Last Varified:<b><span id="lastchechked"></span></b></div>
        </div>
        <div class="clear"></div>
        <div class="random-name" style="display: none;">Random Selected User : Kiran Joshi</div>
    </div>
    
    <div id="history_review_data" style=" height: 530px;overflow-y: scroll;">
    </div>
    <div id="display_result_page" style="display: none;">
        <div id="result_display_view">
            <div class="query"></div>
            <div class="btn-qa btn-edit"><a href="javascript:void(0);" id="edit_button" onclick="">edit</a></div>
        </div>
    </div>
   
    <div id="main_frame">
        <div id="brand_link">
            <div style="text-align: center;width:100%; margin: 150px 0;" id="channel_iframe"></div>
            <!--            <div style="text-align: center;width:100%; margin: 150px 0;" id="channel_iframe"><a href="http://www.facebook.com/ford" target="_blank">http://www.facebook.com/ford</a></div>-->
            <div id="instagram_iframe_link" style="display: none;">
<!--                <iframe src="http://web.stagram.com/n/ford/" id="instagram_brand_link" style="width:100%; height:300px; border: none;"></iframe>-->
            </div>
        </div>
        <div class="black-text">
            <span id="trackNo"></span>
            <span id="question"></span>
        </div>
        <div class="prev-next">
            <div class="btn-back"><a href="javascript:void(0);"><img src="images/btn-back.png" alt="" onclick="getNextQue(this.id);" id="prev"/></a></div>
            <input type="text" placeholder="answer" id="inputed_answer">
            <div id="dropdown_post"></div>
            <div class="btn-next"><a href="javascript:void(0);"><img src="images/btn-next.png" alt="" onclick="getNextQue(this.id);" id="next"/></a></div>
        </div>
        <div class="clear"></div>
        <div class="qa-paginate pagination pagination-centered">
        </div>
    </div>
</div>
