$(function(){

        console.log("WOLOLO WOLOLO WOLOLO WOLOLO");
        min=20;
        max=519;
        med=max/2;
        $.ajax({
            type: 'GET',
            //url: 'areas.json',
            url: "php/mapael.php",
            contentType: "application/json",
            //dataType: 'jsonp',
            success : function(data){ 
                console.log(data);
                    $(".maparea6").mapael({
                            map : {
                                    name : "world_countries",
                                    defaultArea: {
                                            attrs : {
                                                    stroke : "#FAFAFA", 
                                                    "stroke-width" : 1
                                            }
                                    }
                            },
                            legend : {
                                    area : {
                                            display : true,
                                            title :"Population by country", 
                                            slices : [
                                                    {
                                                            max :1, 
                                                            attrs : {
                                                                    fill : "#FFFF00"
                                                            },
                                                            label :"less than 1"
                                                    },
                                                    {
                                                            min: 1,
                                                            max :2, 
                                                            attrs : {
                                                                    fill : "#FFEE00"
                                                            },
                                                            label :"1"
                                                    },
                                                    {
                                                            min: 2,
                                                            max :3, 
                                                            attrs : {
                                                                    fill : "#FFDD00"
                                                            },
                                                            label :"2"
                                                    },
                                                    
                                                    {
                                                            min: 3,
                                                            max :4, 
                                                            attrs : {
                                                                    fill : "#FFCC00"
                                                            },
                                                            label :"3"
                                                    },
                                                    {
                                                            min: 5,
                                                            max :6, 
                                                            attrs : {
                                                                    fill : "#FFBB00"
                                                            },
                                                            label :"5"
                                                    },
                                                    {
                                                            min: 6,
                                                            max :7, 
                                                            attrs : {
                                                                    fill : "#FFAA00"
                                                            },
                                                            label :"6"
                                                    },
                                                    {
                                                            min: 7,
                                                            max :8, 
                                                            attrs : {
                                                                    fill : "#FF9900"
                                                            },
                                                            label :"7"
                                                    },
                                                    {
                                                            min: 8,
                                                            max :9, 
                                                            attrs : {
                                                                    fill : "#FF8800"
                                                            },
                                                            label :"8"
                                                    },
                                                    {
                                                            min: 9,
                                                            max :10, 
                                                            attrs : {
                                                                    fill : "#FF7700"
                                                            },
                                                            label :"9"
                                                    },
                                                    {
                                                            min: 10,
                                                            max :11, 
                                                            attrs : {
                                                                    fill : "#FF6600"
                                                            },
                                                            label :"10"
                                                    },
                                                    {
                                                            min: 11,
                                                            max :12, 
                                                            attrs : {
                                                                    fill : "#FF5500"
                                                            },
                                                            label :"11"
                                                    },
                                                    {
                                                            min: 12,
                                                            max :13, 
                                                            attrs : {
                                                                    fill : "#FF4400"
                                                            },
                                                            label :"12"
                                                    },
                                                    {
                                                            min: 13,
                                                            max :14, 
                                                            attrs : {
                                                                    fill : "#FF3300"
                                                            },
                                                            label :"13"
                                                    },
                                                    {
                                                            min: 14,
                                                            max :15, 
                                                            attrs : {
                                                                    fill : "#FF1100"
                                                            },
                                                            label :"14"
                                                    },
                                                    {
                                                            min :15, 
                                                            attrs : {
                                                                    fill : "#FF0000"
                                                            },
                                                            label :"15"
                                                    }
                                                    
                                                /*
                                                    {
                                                            max :min, 
                                                            attrs : {
                                                                    fill : "#6aafe1"
                                                            },
                                                            label :"Less than de "+min+" humans"
                                                    },
                                                    {
                                                            min :min, 
                                                            max :med, 
                                                            attrs : {
                                                                    fill : "#459bd9"
                                                            },
                                                            label :"Between "+min+" and "+med+" humans"
                                                    },
                                                    {
                                                            min :med, 
                                                            max :max, 
                                                            attrs : {
                                                                    fill : "#2579b5"
                                                            },
                                                            label :"Between "+med+" and "+max+" humans"
                                                    },
                                                    {
                                                            min :max, 
                                                            attrs : {
                                                                    fill : "#1a527b"
                                                            },
                                                            label :"More than "+max+" humans"
                                                    }
                                                    */
                                            ]
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
                            areas: data
                    });
            },
            error: function(){ 
                console.log("Page Not found.");
            }
        });
	
	// Example #6
	
	
});
