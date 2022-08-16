{{-- 載入主要的版型 --}}
@extends('layouts.master')

@section('extraHeaderInfo')
	
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <script>
		window.onload = function () {
			var charts = [];
			var toolTip = {
				shared: true
			},
			legend = {
				cursor: "pointer",
				itemclick: function (e) {
					if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
						e.dataSeries.visible = false;
					} else {
						e.dataSeries.visible = true;
					}
					e.chart.render();
				}
			};
			var last1MinMQTTS = [], last10MinMQTTS=[], last20MinMQTTS = [], last30MinMQTTS = [], totalHotspots = [], totalOnlines=[];

			var cpuChartOptions = {
				animationEnabled: true,
			    // 圖表樣式，有這些->"light1", "light2", "dark1", "dark2"
			    theme: "light2",
			    // 標題
			    title:{
			      	text: "MQTT Status"
			    },
			    // Y軸設定
			    axisY: {
			      	valueFormatString: "#,###",
			    },
			    toolTip: toolTip,
			    legend: legend,
			    data: [
				    // 第一段的設定
				    {
				    	// 類型
						type: "line",
						// 要顯示icon嗎(下面那個標記)
						showInLegend: "true",
						// 這個區塊的名稱
						name: "1min",
						// Y軸小框框顯示的格式
						yValueFormatString: "",
						// 顏色
						color: "#64b5f6",
						// X軸類型
						xValueType: "dateTime",
						// X軸格式
						xValueFormatString: "YY-MM-DD HH:mm",
						// 圖標類型
						legendMarkerType: "square",
						// 數據點名稱
						dataPoints: last1MinMQTTS
					},
				    // 第二段的設定
				    {
						type: "line", 
						showInLegend: "true",
						name: "10min",
						yValueFormatString: "",
						color: "#2196f3",
						xValueType: "dateTime",
						xValueFormatString: "YY-MM-DD HH:mm",
						legendMarkerType: "square",
						dataPoints: last10MinMQTTS
				    },
				    // 第三段的設定
				    {
						type: "line", 
						showInLegend: "true",
						name: "20min",
						yValueFormatString: "",
						color: "#C678FF",
						xValueType: "dateTime",
						xValueFormatString: "YY-MM-DD HH:mm",
						legendMarkerType: "square",
						dataPoints: last20MinMQTTS
				    },
				    // 第四段的設定
				    {
						type: "line", 
						showInLegend: "true",
						name: "30min",
						yValueFormatString: "",
						color: "#FF6EFF",
						xValueType: "dateTime",
						xValueFormatString: "YY-MM-DD HH:mm",
						legendMarkerType: "square",
						dataPoints: last30MinMQTTS
				    }
				]
			};
			var memoryChartOptions = {
				animationEnabled: true,
			    theme: "light2",
			    title:{
			    	text: "Hotspot"
			    },
			    axisY: {
			    	suffix: ""
			    },
			    toolTip: toolTip,
			    legend: legend,
			    data: [
				    // 第一段的設定(最高)
				    {
				      	type: "line", 
				      	showInLegend: "true",
				      	name: "Total",
				      	color: "#e57373",
				     	xValueType: "dateTime",
				     	xValueFormatString: "YY-MM-DD HH:mm",
				     	yValueFormatString: "",
				     	legendMarkerType: "square",
				      	dataPoints: totalHotspots
				    },
				    // 第二段的設定(中間)
				    {
						type: "line", 
						showInLegend: "true",
						name: "Onlines",
						color: "#f44336",
						xValueType: "dateTime",
						xValueFormatString: "YY-MM-DD HH:mm",
						yValueFormatString: "",
						legendMarkerType: "square",
						dataPoints: totalOnlines
				    }
				]
			};

			// 建立圖表
			charts.push(new CanvasJS.Chart("chartContainer1", cpuChartOptions));
			charts.push(new CanvasJS.Chart("chartContainer2", memoryChartOptions));

			var time = '';
			var date = '';
			$.get("https://linxdot-map.v7idea.com/hotspotStatus", function(data) {
				// 設定資料
				for (var i = 0; i < data.data.total; i++) {
					result = data.data.result[i];
					date = new Date(result.Time);
					time = date.getTime();
					// 把資料塞進數據點(?
					totalHotspots.push({x: parseInt(time), y: parseFloat(result.TotalHotspots)});
					totalOnlines.push({x: parseInt(time), y: parseFloat(result.TotalOnlines)});
				}
			});

			$.get("https://linxdot-map.v7idea.com/mqttStatus", function(data) {
				// 設定資料
				for (var i = 0; i < data.data.total; i++) {
					result = data.data.result[i];

					date = new Date(result.Time);
					time = date.getTime();
					// 把資料塞進數據點(?
					last1MinMQTTS.push({x: parseInt(time), y: parseFloat(result.Last1MinMQTTS)});
					last10MinMQTTS.push({x: parseInt(time), y: parseFloat(result.Last10MinMQTTS)});
					last20MinMQTTS.push({x: parseInt(time), y: parseFloat(result.Last20MinMQTTS)});
					last30MinMQTTS.push({x: parseInt(time), y: parseFloat(result.Last30MinMQTTS)});
				}
				for( var i = 1; i < charts.length; i++){
					charts[i].options.axisX = {
						labelAngle: 0,
						crosshair: {
							enabled: true,
							snapToDataPoint: true,
							valueFormatString: "HH:mm"
						}
					}
				}

				// 圖表同步設定(圖表資料, 同步提示框, 同步十字準線, 同步X軸線)
			    syncCharts(charts, true, true, true);

			    for( var i = 0; i < charts.length; i++){
			      	charts[i].render();
			    }
			});
				
			
			$.get("https://linxdot-map.v7idea.com/getFirmwareStatus", function(data) {
				var dataPoint = [];
				var result = [];

				// 設定資料
				for (var i = 0; i < 6; i++) {
					console.log(data.data.result[i]);
					result = data.data.result[i];
					// alert(result.VersionNo);
					// alert(result.Firmwarename);
					if(result.VersionNo != null && result.VersionNo != ''){
						Firmwarename = result.VersionNo;
					}else if(result.Firmware == null){
						Firmwarename = 'null';
					}else{
						Firmwarename = result.Firmware;
					}
					Percent = result.Percent * 100;
					dataPoint.push({ y:result.TotalCount,name:Firmwarename });
				}

				var chart = new CanvasJS.Chart("chartContainer", {
					exportEnabled: true,
					animationEnabled: true,
					// 標題
					title:{
						text: "Firmware Version"
					},
					legend:{
						cursor: "pointer",
						itemclick: explodePie
					},
					data: [{
						type: "pie",
						showInLegend: true,
						toolTipContent: "{name}: <strong>{y}</strong>",
						indexLabel: "{y}",
						dataPoints: dataPoint
					}]
				});
				chart.render();
			});

			$.get("https://linxdot-map.v7idea.com/getMinerStatus", function(data) {
				var dataPoint = [];
				var result = [];

				// 設定資料
				for (var i = 0; i < 6; i++) {
					console.log(data.data.result[i]);
					result = data.data.result[i];
					if(result.MinerVersion == null){
						pointname = "null";
					}else{
						pointname = result.MinerVersion.slice(-13)
					}
					dataPoint.push({ y:result.TotalCount,name:pointname });
				}

				var chart21 = new CanvasJS.Chart("chartContainer21", {
					exportEnabled: true,
					animationEnabled: true,
					// 標題
					title:{
						text: "Miner Version"
					},
					legend:{
						cursor: "pointer",
						itemclick: explodePie
					},
					data: [{
						type: "pie",
						showInLegend: true,
						toolTipContent: "{name}: <strong>{y}</strong>",
						indexLabel: "{y}",
						dataPoints: dataPoint
					}]
				});
				chart21.render();
			});
		}



		function syncCharts(charts, syncToolTip, syncCrosshair, syncAxisXRange) {
		    if(!this.onToolTipUpdated){
		    	this.onToolTipUpdated = function(e) {
		    		for (var j = 0; j < charts.length; j++) {
		    			if (charts[j] != e.chart)
		    				charts[j].toolTip.showAtX(e.entries[0].xValue);
		    		}
		    	}
		    }

		    if(!this.onToolTipHidden){
		      	this.onToolTipHidden = function(e) {
		      		for( var j = 0; j < charts.length; j++){
		      			if(charts[j] != e.chart)
		      				charts[j].toolTip.hide();
		      		}
		      	}
		    }

		    if(!this.onCrosshairUpdated){
		    	this.onCrosshairUpdated = function(e) {
			        for(var j = 0; j < charts.length; j++){
			        	if(charts[j] != e.chart)
			        		charts[j].axisX[0].crosshair.showAt(e.value);
			        }
			    }
		    }

		    if(!this.onCrosshairHidden){
		    	this.onCrosshairHidden =  function(e) {
		    		for( var j = 0; j < charts.length; j++){
		    			if(charts[j] != e.chart)
		            	charts[j].axisX[0].crosshair.hide();
		        	}
		      	}
		    }

		    if(!this.onRangeChanged){
		      	this.onRangeChanged = function(e) {
		        	for (var j = 0; j < charts.length; j++) {
		          		if (e.trigger === "reset") {
				            charts[j].options.axisX.viewportMinimum = charts[j].options.axisX.viewportMaximum = null;
				            charts[j].options.axisY.viewportMinimum = charts[j].options.axisY.viewportMaximum = null;
				            charts[j].render();
		          		} else if (charts[j] !== e.chart) {
				            charts[j].options.axisX.viewportMinimum = e.axisX[0].viewportMinimum;
				            charts[j].options.axisX.viewportMaximum = e.axisX[0].viewportMaximum;
				            charts[j].render();
		          		}
		        	}
		      	}
		    }

		    for(var i = 0; i < charts.length; i++) { 
			    	//Sync ToolTip
			    	if(syncToolTip) {
			    		if(!charts[i].options.toolTip)
			    			charts[i].options.toolTip = {};

			    		charts[i].options.toolTip.updated = this.onToolTipUpdated;
			    		charts[i].options.toolTip.hidden = this.onToolTipHidden;
			    	}

			      	//Sync Crosshair
			      	if(syncCrosshair) {
			        	if(!charts[i].options.axisX)
			          		charts[i].options.axisX = { crosshair: { enabled: true }};
					
			        	charts[i].options.axisX.crosshair.updated = this.onCrosshairUpdated; 
			        	charts[i].options.axisX.crosshair.hidden = this.onCrosshairHidden; 
			     	 }

			      	//Sync Zoom / Pan
			      	if(syncAxisXRange) {
			        	charts[i].options.zoomEnabled = true;
			        	charts[i].options.rangeChanged = this.onRangeChanged;
			      	}
		    }
		}

		function explodePie (e) {
			if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
				e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
			} else {
				e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
			}
			e.chart.render();
		}
	</script>
@endsection

{{-- 設定視窗的標題 --}}
@section('title', 'HOME')

{{-- 設定內容的主標題區 --}}
@section('pageTitle', '')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    
@endsection

{{-- 設定內容 --}}
@section('content')
    {{-- <p>Welcome to Linxdot Admin！</p> --}}
    <style>
		.row:after {
		    content: "";
		    display: table;
		    clear: both;
		}
		.col {
		    /*float: left;*/
		    width: 100%;
		    height: 270px;
		 }
	</style>

	<!-- <div class="uk-grid row" data-uk-grid-margin>
        <div class="uk-width-medium-1-2">
            <div class="md-card">
                <div class="md-card-content">
                    {{-- Hostpot --}}
                    <div class="col"id="chartContainer1"></div>
                </div>
            </div>
        </div>
        <div class="uk-width-medium-1-2">
            <div class="md-card">
                <div class="md-card-content">
                    {{-- Fimware --}}
                    <div class="col" id="chartContainer2"></div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="uk-grid row" data-uk-grid-margin>
        <div class="uk-width-medium-1-2">
            <div class="md-card">
                <div class="md-card-content">
                    {{-- MQTT_Action --}}
                    <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="uk-width-medium-1-2">
            <div class="md-card">
                <div class="md-card-content">
                    {{-- MinerVersion --}}
                    <div id="chartContainer21" style="height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    <embed src="https://linxdot-map.v7idea.com/worldmap/" width="100%" height="500">

    <script src="js/canvasjs/canvasjs.min.js"></script>
@endsection