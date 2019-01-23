(function(){
    function G(s){
        return document.getElementById(s);
    }
    
    function getStyle(obj, attr){
        if(obj.currentStyle){
            return obj.currentStyle[attr];
        }else{
            return getComputedStyle(obj, false)[attr];
        }
    }
    
    function Animate(obj, json){
        if(obj.timer){
            clearInterval(obj.timer);
        }
        obj.timer = setInterval(function(){
            for(var attr in json){
                var iCur = parseInt(getStyle(obj, attr));
                iCur = iCur ? iCur : 0;
                var iSpeed = (json[attr] - iCur) / 5;
                iSpeed = iSpeed > 0 ? Math.ceil(iSpeed) : Math.floor(iSpeed);
                obj.style[attr] = iCur + iSpeed + 'px';
                if(iCur == json[attr]){
                    clearInterval(obj.timer);
                }
            }
        }, 30);
    }
    
    var oPic = G("picBox");
    var oList = G("listBox");
    
    var oPrev = G("prev");
    var oNext = G("next");
    
    var oPicLi = oPic.getElementsByTagName("li");
    var oListLi = oList.getElementsByTagName("li");
    var len1 = oPicLi.length;
    var len2 = oListLi.length;
    
    var oPicUl = oPic.getElementsByTagName("ul")[0];
    var oListUl = oList.getElementsByTagName("ul")[0];
    var w1 = oPicLi[0].offsetWidth;
    var w2 = oListLi[0].offsetWidth;
    
    oPicUl.style.width = w1 * len1 + "px";
    oListUl.style.width = w2 * len2 + "px";
    
    var index = 0;
    
    //缩略图，每屏显示个数
    var num = 3;
    
    var num2 = Math.ceil(num / 2);
    
    function Change()
    {
        Animate(oPicUl, {left: - index * w1});
        
        if(index < num2){
            Animate(oListUl, {left: 0});
        }else if(index + num2 <= len2){
            Animate(oListUl, {left: - (index - num2 + 1) * w2});
        }else{
            Animate(oListUl, {left: - (len2 - num) * w2});
        }
        
        for (var i = 0; i < len2; i++) {
            oListLi[i].className = "";
            if(i == index){
                oListLi[i].className = "on";
            }
        }
    }
    
    oNext.onclick = function(){
        index ++;
        index = index == len2 ? 0 : index;
        Change();
    }
    
    oPrev.onclick = function(){
        index --;
        index = index == -1 ? len2 -1 : index;
        Change();
    }
    
    for (var i = 0; i < len2; i++) {
        oListLi[i].index = i;
        oListLi[i].onclick = function(){
            index = this.index;
            Change();
        }
    }
})();