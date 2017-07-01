
<script language="javascript">
<!--
//图片滚动列表 mengjia 070927
var Speed_1 = 5; //速度(毫秒)
var Space_1 = 10; //每次移动(px)
var PageWidth_1 = 303 * 1; //翻页宽度
var interval_1 = 4000; //翻页间隔
var fill_1 = 4; //整体移位
var MoveLock_1 = false;
var MoveTimeObj_1;
var MoveWay_1="right";
var Comp_1 = 4;
var AutoPlayObj_1=1;
function GetObj(objName){if(document.getElementById){return eval('document.getElementById("'+objName+'")')}else{return eval('document.all.'+objName)}}
function AutoPlay_1(){clearInterval(AutoPlayObj_1);AutoPlayObj_1=setInterval('ISL_GoDown_1();ISL_StopDown_1();',interval_1)}
function ISL_GoUp_1(){if(MoveLock_1)return;clearInterval(AutoPlayObj_1);MoveLock_1=true;MoveWay_1="left";MoveTimeObj_1=setInterval('ISL_ScrUp_1();',Speed_1);}
function ISL_StopUp_1(){if(MoveWay_1 == "right"){return};clearInterval(MoveTimeObj_1);
if((GetObj('ISL_Cont_1').scrollLeft-fill_1)%PageWidth_1!=0){Comp_1=fill_1-(GetObj('ISL_Cont_1').scrollLeft%PageWidth_1);CompScr_1()}else{MoveLock_1=false}
if((GetObj('ISL_Cont_1').scrollRight-fill_1)%PageWidth_1!=0){Comp_1=fill_1-(GetObj('ISL_Cont_1').scrollRight%PageWidth_1);CompScr_1()}else{MoveLock_1=false}
AutoPlay_1()}
function ISL_ScrUp_1(){if(GetObj('ISL_Cont_1').scrollLeft<=0){GetObj('ISL_Cont_1').scrollLeft=GetObj('ISL_Cont_1').scrollLeft+GetObj('List1_1').offsetWidth}
GetObj('ISL_Cont_1').scrollLeft-=Space_1}
function ISL_GoDown_1(){clearInterval(MoveTimeObj_1);if(MoveLock_1)return;clearInterval(AutoPlayObj_1);MoveLock_1=true;MoveWay_1="right";ISL_ScrDown_1();MoveTimeObj_1=setInterval('ISL_ScrDown_1()',Speed_1)}
function ISL_StopDown_1(){if(MoveWay_1 == "left"){return};clearInterval(MoveTimeObj_1);if(GetObj('ISL_Cont_1').scrollLeft%PageWidth_1-(fill_1>=0?fill_1:fill_1+1)!=0){Comp_1=PageWidth_1-GetObj('ISL_Cont_1').scrollLeft%PageWidth_1+fill_1;CompScr_1()}else{MoveLock_1=false}
AutoPlay_1()}
function ISL_ScrDown_1(){if(GetObj('ISL_Cont_1').scrollLeft>=GetObj('List1_1').scrollWidth){GetObj('ISL_Cont_1').scrollLeft=GetObj('ISL_Cont_1').scrollLeft-GetObj('List1_1').scrollWidth}
GetObj('ISL_Cont_1').scrollLeft+=Space_1}
function CompScr_1(){if(Comp_1==0){MoveLock_1=false;return}
var num,TempSpeed=Speed_1,TempSpace=Space_1;if(Math.abs(Comp_1)<PageWidth_1/2){TempSpace=Math.round(Math.abs(Comp_1/Space_1));if(TempSpace<1){TempSpace=1}}
if(Comp_1<0){if(Comp_1<-TempSpace){Comp_1+=TempSpace;num=TempSpace}else{num=-Comp_1;Comp_1=0}
GetObj('ISL_Cont_1').scrollLeft-=num;setTimeout('CompScr_1()',TempSpeed)}else{if(Comp_1>TempSpace){Comp_1-=TempSpace;num=TempSpace}else{num=Comp_1;Comp_1=0}
GetObj('ISL_Cont_1').scrollLeft+=num;setTimeout('CompScr_1()',TempSpeed)}}
function picrun_ini(){
GetObj("List2_1").innerHTML=GetObj("List1_1").innerHTML;
GetObj('ISL_Cont_1').scrollLeft=fill_1>=0?fill_1:GetObj('List1_1').scrollWidth-Math.abs(fill_1);
GetObj("ISL_Cont_1").onmouseover=function(){clearInterval(AutoPlayObj_1)}
GetObj("ISL_Cont_1").onmouseout=function(){AutoPlay_1()}
AutoPlay_1();
}
//产品展示滚动图片结束
//-->
</script>
<style type="text/css">
<!--
BODY { FONT-SIZE: 12px; MARGIN: 10px; FONT-FAMILY: 宋体; BACKGROUND-COLOR: #ffffff;  border-width:0px; }
 
.blk_18 { WIDTH: 303px;  ZOOM: 1; position:relative; }

.blk_18 .pcont { FLOAT: left; OVERFLOW: hidden; WIDTH: 303px; }

.blk_18 .ScrCont { WIDTH: 32766px; ZOOM: 1; }

.blk_18 #List1_1 { FLOAT: left; }

.blk_18 #List2_1 { FLOAT: left;}

.blk_18 .LeftBotton { BACKGROUND:url(images/bt_03.jpg) no-repeat; FLOAT: left; WIDTH: 14px; HEIGHT: 14px; position:absolute;   left:230px; top:-10px;}

.blk_18 .RightBotton { BACKGROUND:url(images/bt_03.jpg) no-repeat; FLOAT:right;  WIDTH: 14px; HEIGHT: 14px; position:absolute;  left:250px; top:-10px; }

.blk_18 .LeftBotton { BACKGROUND-POSITION: 0px 0px; MARGIN-LEFT:2px; }

.blk_18 .RightBotton { BACKGROUND-POSITION: 0px -120px; MARGIN-right:2px; }

.blk_18 .LeftBotton:hover {
BACKGROUND-POSITION: -48px 0px
}
.blk_18 .RightBotton:hover {
BACKGROUND-POSITION: -48px -120px
}


.blk_18 .pl  { BORDER: #ffffff 1px solid; background-color:#ffffff; FLOAT: left; padding:1px; WIDTH:303px; LINE-HEIGHT: 18px; TEXT-ALIGN: center; TEXT-DECORATION:none;}



.commu_cont3 {
MARGIN: 9px 7px 7px; LINE-HEIGHT: 150%
}
.commu_cont3 UL {
WIDTH: 303px
}


.con_box01_tit {
    font-size: 12px;
    font-weight: bold;
    padding-bottom: 10px;
}
.feedback dl dt {
    display: block;
    float: left;
    height: 73px;
    padding-right: 10px;
}
.feedback dl dd {
    color: #555555;
    float: left;
    height: 72px; margin:0;
	padding:0;overflow: hidden;
    width: 215px;
}
.feedback span {
    background: url("../images/ping_bg.gif") no-repeat scroll 0 -180px rgba(0, 0, 0, 0);
    color: #555555;
    display: block;
    padding-left: 20px;
    position: relative;
    width: 205px;
}
.feedback i {
    background: url("../images/ping_bg.gif") no-repeat scroll 0 -148px rgba(0, 0, 0, 0);
    height: 15px;
    position: absolute;
    width: 15px;
}
.feedback b {
    clear: both;
    display: block;
    text-align: right;
}


-->
</style>
</head>
<body>
<!-- picrotate_left start  -->
<DIV class=blk_18>
<A onmouseup=ISL_StopUp_1() class=LeftBotton onmousedown=ISL_GoUp_1() onmouseout=ISL_StopUp_1() href="javascript:void(0);" target=_self>P</A>
<A onmouseup=ISL_StopDown_1() class=RightBotton onmousedown=ISL_GoDown_1() onmouseout=ISL_StopDown_1() href="javascript:void(0);" target=_self>N</A>
<div class="con_box01_tit">Customer Feedback</div>
    <DIV class=pcont id=ISL_Cont_1>
        <DIV class=ScrCont>
        
            <DIV id=List1_1 class="feedback">
            
                <!-- piclist begin -->
        <dl class=pl >
        <dt><img src="images/feedback_pic.jpg" alt="FiberStore " title="FiberStore "></dt>
        <dd><span>CWDM and DWDM technologies are popular with telecommunications companies because they provide an effective way to share&nbsp;&nbsp; <i></i></span></dd>
        <b>-- Dyron S1.</b>
      </dl>
      <dl class=pl >
        <dt><img src="images/feedback_pic.jpg" alt="FiberStore " title="FiberStore "></dt>
        <dd><span>CWDM and DWDM technologies are popular with telecommunications companies because they provide an effective way to share&nbsp;&nbsp; <i></i></span></dd>
        <b>-- Dyron S2.</b>
      </dl>
      <dl class=pl >
        <dt><img src="images/feedback_pic.jpg" alt="FiberStore " title="FiberStore "></dt>
        <dd><span>CWDM and DWDM technologies are popular with telecommunications companies because they provide an effective way to share&nbsp;&nbsp; <i></i></span></dd>
        <b>-- Dyron S3.</b>
      </dl>
      <dl class=pl >
        <dt><img src="images/feedback_pic.jpg" alt="FiberStore " title="FiberStore "></dt>
        <dd><span>CWDM and DWDM technologies are popular with telecommunications companies because they provide an effective way to share&nbsp;&nbsp; <i></i></span></dd>
        <b>-- Dyron S4.</b>
      </dl>
            </DIV>
            <DIV id=List2_1></DIV>
        </DIV>
    </DIV>
    </DIV>

