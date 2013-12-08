getUrlParam = (function () {
    var get = {
        push:function (key,value){
            var cur = this[key];
            if (cur.isArray){
                this[key].push(value);
            }else {
                this[key] = [];
                this[key].push(cur);
                this[key].push(value);
            }
        }
    },
    search = document.location.search,
    decode = function (s,boo) {
        var a = decodeURIComponent(s.split("+").join(" "));
        return boo? a.replace(/\s+/g,''):a;
    };
    search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function (a,b,c) {
        if (get[decode(b,true)]){
            get.push(decode(b,true),decode(c));
        }else {
            get[decode(b,true)] = decode(c);
        }
    });
    return get;
})();

//Countries and Colors:
var borderLine="#848484";//white-gray
var withoutData="#FFFFFF";//white
var onHover="#324F17";//green

function initiateMap(db,query){
    //geomap/?db=["data/NCI/data.db"]&query=all 
    console.log("geomap/php/mapael.php"+"?db="+db+"&query="+query);
    $.ajax({
        type: 'GET',
        //url: 'areas.json',
        url: "php/mapael.php",
        data:"db="+db+"&query="+query,
        contentType: "application/json",
        //dataType: 'jsonp',
        success : function(data){ 
            console.log(data);
            $(".maparea6").mapael({
                map : {
                    name : "world_countries",
                    zoom: {
                        enabled: true,
                        maxLevel : 10
                    },
                    defaultArea: {
                        attrs : {
                            stroke : borderLine, //Country border line
                            "stroke-width" : 1,
                            fill: withoutData, //Color of Countries value = 0
                            cursor: "pointer"
                        },
                        attrsHover : {
                            fill: onHover, //Country on Hover
                            animDuration : 300
                        }
                    },			
                    afterInit : function($self, paper, areas, plots, options) {
                        var mapConf = $.fn.mapael.maps[this.name]
                        var coords = {};
                        console.log("salut monde");
                        //id="image0";
                        //coords = mapConf.getCoords(options.images[id].latitude, options.images[id].longitude);
                        //paper.image(options.images[id].src, coords.x - options.images[id].width / 2, coords.y - options.images[id].height / 2, options.images[id].width, options.images[id].height);
                    }
		
                },
                
                'images' : {
                    'image0' : {
                        'src' : 'http://www.neveldo.fr/mapael/cross-icon.png',
                        'latitude' : '6.452667',
                        'longitude' : '7.510333',
                        'width':170,
                        'height':150
                    }
                },
                legend : {
                    area : {
                        display : false,
                        title :"Population by country", 
                        slices : data["slices"]
                    }/*,
                                    plot :{
                                            display : true,
                                            title: "Some cities ..."
                                            , slices : [
                                                    {
                                                            max :500000, 
                                                            attrs : {
                                                                    fill : "#f99200"
                                                            },
                                                            attrsHover :{
                                                                    transform : "s1.5",
                                                                    "stroke-width" : 1
                                                            }, 
                                                            label :"less than 500 000 inhabitants", 
                                                            size : 10
                                                    },
                                                    {
                                                            min :500000, 
                                                            max :1000000, 
                                                            attrs : {
                                                                    fill : "#f99200"
                                                            },
                                                            attrsHover :{
                                                                    transform : "s1.5",
                                                                    "stroke-width" : 1
                                                            }, 
                                                            label :"Between 500 000 and 1 000 000 inhabitants", 
                                                            size : 20
                                                    },
                                                    {
                                                            min :1000000, 
                                                            attrs : {
                                                                    fill : "#f99200"
                                                            },
                                                            attrsHover :{
                                                                    transform : "s1.5",
                                                                    "stroke-width" : 1
                                                            }, 
                                                            label :"More than 1 million inhabitants", 
                                                            size : 30
                                                    }
                                            ]
                                    }*/
                },/*
                            plots : {
                                    'paris' : {
                                            latitude :48.86, 
                                            longitude :2.3444, 
                                            value : 500000000, 
                                            tooltip: {content : "Paris<br />Population: 500000000"}
                                    },
                                    'newyork' : {
                                            latitude :40.667, 
                                            longitude :-73.833, 
                                            value : 200001, 
                                            tooltip: {content : "New york<br />Population: 200001"}
                                    },
                                    'sydney' : {
                                            latitude :-33.917, 
                                            longitude :151.167, 
                                            value : 600000, 
                                            tooltip: {content : "Sydney<br />Population: 600000"}
                                    },
                                    'brasilia' : {
                                            latitude :-15.781682, 
                                            longitude :-47.924195, 
                                            value : 200000001, 
                                            tooltip: {content : "Brasilia<br />Population: 200000001"}
                                    },
                                    'tokyo': {
                                            latitude :35.687418, 
                                            longitude :139.692306, 
                                            value : 200001, 
                                            tooltip: {content : "Tokyo<br />Population: 200001"}
                                    }
                            },*/
                areas: data["areas"]
            });
            $(".map").on("mousewheel", function(e) {
                if (e.deltaY > 0)
                    $(".maparea6").trigger("zoom", $(".maparea6").data("zoomLevel") + 1);
                else
                    $(".maparea6").trigger("zoom", $(".maparea6").data("zoomLevel") - 1);
			
                return false;
            });
        },
        error: function(){ 
            console.log("Page Not found.");
        }
    });
}
