var map = new BMap.Map("mapbox", {enableMapClick:false}); //创建Map实例(关闭底图可点功能)
    map.centerAndZoom(city_name, 11); //初始化地图,用城市名设置地图中心点
    
//添加 缩放 与 平移控件
var top_left_control = new BMap.ScaleControl({anchor: BMAP_ANCHOR_TOP_LEFT});// 左上角，添加比例尺
var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件
map.addControl(top_left_control);    // 添加比例尺
map.addControl(top_left_navigation); // 默认缩放平移控件
map.enableScrollWheelZoom(true);

//地图缩放监听
var lastLevel;
map.addEventListener("zoomstart", function(){
    lastLevel = this.getZoom();
});

map.addEventListener("zoomend", function(){
    //当前地图级别
    var zoomLevel = this.getZoom();
    
    if (zoomLevel >= 12 || isSearch === true)
    {
        //农家自定义覆盖物
        if (BuildingModel.length > 0)
        {
            addBuilding(BuildingModel, 14);
        }
        else
        {
            addRangeOverlay(RegionPoint, 12);
        }
    }
    else
    {
        //输出行政区自定义覆盖物
        addRangeOverlay(RegionPoint, 12);
    }
});

function rangeOverlay(point, text, code, url,zoom)
{
    this._point = point;
    this._text = text;
    this._code = code;
    this._url = url;
    this._zoom = zoom;
}

rangeOverlay.prototype = new BMap.Overlay();
rangeOverlay.prototype.initialize = function(map){
    this._map = map;
    var div = this._div = document.createElement("div");
    div.setAttribute("id", this._code);
    div.setAttribute("class", "range-overlay");
    div.style.zIndex = BMap.Overlay.getZIndex(this._point.lat);
    
    //保存code
    var code = this._code,   //区域代码
        url = this._url,
        point = this._point,
        zoom = this._zoom;
    div.onclick = function businessCirclePoint(){
        //Ajax上传code，并改变中心点
        doSearch(code);
        //根据坐标点进行跳转,改变层级
        map.setZoom(zoom);
    }
    var span = this._span = document.createElement("span");
    div.appendChild(span);
    div.getElementsByTagName("span")[0].innerHTML =  this._text;
    div.onmouseover = function(){ this.style.zIndex = "9"; }
    div.onmouseout = function(){ this.style.zIndex = "1"; }
    map.getPanes().labelPane.appendChild(div);
    
    return div;
}
rangeOverlay.prototype.draw = function(){
    var map = this._map;
    var pixel = map.pointToOverlayPixel(this._point);
    this._div.style.left = pixel.x - 30 + "px";
    this._div.style.top  = pixel.y - 30 + "px";
}

function buildingOverlay(point, text, mouseoverTxt, code, NO, zoom)
{
    this._point = point;
    this._text = text;
    this._mouseoverTxt = mouseoverTxt;
    this._code = code;
    this._NO = NO;
    this._zoom = zoom;
}

function closeInfo(id)
{
    $("#building-infoWindow-"+id).remove();
}

buildingOverlay.prototype = new BMap.Overlay();
buildingOverlay.prototype.initialize = function(map){
    var num = this._NO,
        buildingText = this._text;
    this._map = map;
    var div = this._div = document.createElement("div"); // 父级元素
    var childOverlay = document.createElement("div"); // 第三级覆盖物div
        div.setAttribute("class", "building-parent");
        div.style.zIndex = BMap.Overlay.getZIndex(this._point.lat);
        childOverlay.setAttribute("class", "building-overlay");
        childOverlay.setAttribute("id", this._NO);
        childOverlay.onclick = function(){
            var childInfoWindow = document.createElement("div");
            childInfoWindow.id = "building-infoWindow-" + num;
            childInfoWindow.className = "building-infoWindow";
            var buildingOverlayObj = BuildingModel[num];
            // 拼接字符串内容
            var childInfoWindow_Content =
                "<h2>" +
                    "<span onClick=\"closeInfo("+num+");\" class=\"closeme\">" +
                        "<img src=\"/static/map/ico-close-60-px.png\">" +
                    "</span>" +
                    "<p class=\"building_name\">" + buildingOverlayObj.name + "</p>" +
                    "<p class=\"building_dayBeginning\">" + "人均消费：￥" + "<b>" + buildingOverlayObj.avg_price + "</b>" + "元" + "</p>" +
                "</h2>" +
                "<p><a href=\"" + buildingOverlayObj.url + "\" target=\"_blank\">" +
                    "<img src=\"" + buildingOverlayObj.picUrl + "\" alt=\"\">" +
                "</a></p>" +
                "<div class=\"moxinfo\">" +
                        "<div class=\"building_ContentBox\">" + "<strong>特色：</strong><span>" + buildingOverlayObj.tags + "</span>" + "</div>" +
                        "<div class=\"building_ContentBox\">" + "<strong>联系：</strong><span>"+ buildingOverlayObj.contact +" "+ buildingOverlayObj.tel +"</span>" +  "</div>" +
                "</div>"
            childInfoWindow.innerHTML =  childInfoWindow_Content;   // 信息窗口加入内容结构；
            childOverlay.parentNode.insertBefore(childInfoWindow,childOverlay);
            var allInfoWindow = document.getElementsByClassName("building-infoWindow"); // 获取所有信息窗口
            //百度infoWindo效果
            var buildingOverlayObj = BuildingModel[num];        //获取childInfoWindow_Content信息窗口的高度
            var childInfoWindow_Content_height = document.getElementById("building-infoWindow-" + num).offsetHeight; // 可以获取到自定义信息窗口的高度
            var childInfoWindow_Content_width = document.getElementById("building-infoWindow-" + num).offsetWidth; // 可以获取到自定义信息窗口的高度
            
            var buildingContent_Baidu =
                "<div " + "style = \"height:" + childInfoWindow_Content_height + "px; width:" + childInfoWindow_Content_width + "px; \"" + ">" + "aaa"  + " </div>" +
                "</div>";
            var infoWindow = new BMap.InfoWindow(buildingContent_Baidu);  // 创建信息窗口对象
            var point = new BMap.Point(buildingOverlayObj.latitude, buildingOverlayObj.longitude);
            map.openInfoWindow(infoWindow, point); //开启信息窗口
        };
        div.appendChild(childOverlay);
        
    var span = this._span = document.createElement("span");
        span.appendChild(document.createTextNode(this._text));
        childOverlay.appendChild(span);
    var that = this;
    var arrow = this._arrow = document.createElement("div");// 箭头
        arrow.setAttribute("class", "arrow");
        childOverlay.appendChild(arrow);
    childOverlay.onmouseover = function(){ this.getElementsByTagName("span")[0].innerHTML = that._mouseoverTxt; }
    childOverlay.onmouseout = function(){ this.getElementsByTagName("span")[0].innerHTML = that._text; }
    map.getPanes().labelPane.appendChild(div);
    
    return div;
}
buildingOverlay.prototype.draw = function(){
    var map = this._map;
    var pixel = map.pointToOverlayPixel(this._point);
    this._div.style.left = pixel.x - 30 + "px";
    this._div.style.top  = pixel.y - 30 + "px";
}

function addRangeOverlay(ObjGroup, setZoom)
{
    //清理地图上面所有点
    map.clearOverlays();
    
    for (var i = 0; i < ObjGroup.length; i++)
    {
        var arr = new Object();
        arr = ObjGroup[i];
        var code = arr.code,
            url = arr.url,
            text = arr.name + "<br />" + arr.resourceAmount + "家"; // 拼接字符串
        var zoom = setZoom; // 获取地图层级
        var RangeOverlay = new rangeOverlay(
            new BMap.Point(arr.latitude, arr.longitude), text, code, url,zoom
        );
        map.addOverlay(RangeOverlay);
    }
};

var buildingOverlayArr = [];

function addBuilding(ObjGroup, setZoom)
{
    //清理地图上面所有点
    map.clearOverlays();
    
    for (var i = 0; i < ObjGroup.length; i++)
    {
        var buildingArr = new Object();
        buildingArr = ObjGroup[i];
        
        //获取地图层级
        var zoom = setZoom;
        
        //拼接属性文字内容
        var text = buildingArr.name,
            mouseoverTxt = buildingArr.name;
        buildingOverlayArr[i] = BuildingOverlay = new buildingOverlay(
            new BMap.Point(buildingArr.latitude, buildingArr.longitude), text, mouseoverTxt, buildingArr.code,i,zoom
        );
        
        map.addOverlay(BuildingOverlay);
        
        buildingOverlayArr[i] = BuildingOverlay;
    }
};

function doSearch(area, search = false)
{
    var q = $("#q").val();
    isSearch = search;
    
    $.ajax({
        url:'/map/ajax/moxlist/?city_id='+city_id+'&area='+area+'&q='+q+'&mudi=聚会&ren=qinzi/',
        type:'GET',
        async:false,
        timeout:5000,
        dataType:'json',
        success:function(data){
            BuildingModel = data;
        },
        error:function(xhr,textStatus){
           alert('数据加载失败');
        }
    })
    
    addBuilding(BuildingModel, 16);
}