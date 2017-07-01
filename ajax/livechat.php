<div id="aa" style="width:200px;height:200px;color:#fff;" >
	<div id='live124793'><img src="images/livechat.jpg" alt="live chat"/></div>
</div>

	<script>
    function scroll(p){
    	var d = document,w = window,o = d.getElementById(p.id),ie6 = /msie 6/i.test(navigator.userAgent);
    	if(o){
    		o.style.cssText +=";position:"+(p.f&&!ie6?'fixed':'absolute')+";"+(p.r?'left':"right")+":0;"+(p.t!=undefined?'top:'+p.t+'px':'bottom:0');
    		if(!p.f||ie6){
    			-function(){
	        		var t = 500,st = d.documentElement.scrollTop||d.body.scrollTop,c;
	                c = st  - o.offsetTop + (p.t!=undefined?p.t:(w.innerHeight||d.documentElement.clientHeight)-o.offsetHeight);//�������html 4.01��ĳ�d.body,���ﲻ�����Լ��ٴ���
	            	c!=0&&(o.style.top = o.offsetTop + Math.ceil(Math.abs(c)/10)*(c<0?-1:1) + 'px',t=10);
	            	setTimeout(arguments.callee,t)
        		}()
    		}
    	}
    }
    scroll({
    	id:'aa'

    })
</script>
