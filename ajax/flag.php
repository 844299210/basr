
<script type="text/javascript">
var lastFaqClick=null;//上次点击的faq
window.onload=function(){
  var faq=document.getElementById("faq");
  var dls=faq.getElementsByTagName("dl");
  for (var i=0,dl;dl=dls[i];i++){
    var dt=dl.getElementsByTagName("dt")[0];//取得标题
     dt.id = "faq_dt_"+(Math.random()*100);
     dt.onclick=function(){
       var p=this.parentNode;//取得父节点
        if (lastFaqClick!=null&&lastFaqClick.id!=this.id){
          var dds=lastFaqClick.parentNode.getElementsByTagName("dd");
          for (var i=0,dd;dd=dds[i];i++)
            dd.style.display='none';
        }
        lastFaqClick=this;
        var dds=p.getElementsByTagName("dd");//取得该父节点所有的子节点，也就是所有的答案
        var tmpDisplay='none';
        if (gs(dds[0],'display')=='none')
          tmpDisplay='block';
        for (var i=0;i<dds.length;i++)
          dds[i].style.display=tmpDisplay;
      }
  }
}
/**
*取得元素的真实css属性
*written in 06.7 sheneyan
*/
function gs(d,a){
  if (d.currentStyle){ 
    var curVal=d.currentStyle[a]
  }else{ 
    var curVal=document.defaultView.getComputedStyle(d, null)[a]
  } 
  return curVal;
}
</script>
<style type="text/css">
*,html,body{margin:0;padding:0}
#faq dt{font-weight:bold;cursor:pointer}
#faq dd{display:none;}
</style>
</head>
<body>
<ul id="faq">
<li>
<dl><dt>问题1 </dt><dd>答：1 </dd></dl>
</li>
<li>
<dl><dt>问题2 </dt><dd>答：2 </dd></dl>
</li>
<li>
<dl><dt>问题3 </dt><dd>答：3 </dd></dl>
</li>
<li>
<dl><dt>问题4 </dt><dd>答：4 </dd></dl>
</li>
</li>
</ul>

