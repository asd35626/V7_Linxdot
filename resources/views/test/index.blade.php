{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')

@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <style type="text/css">
        .mapboxgl-ctrl{
            display: none;
        }
        .marker {
            background-image: url('/favicon.ico');
            background-size: cover;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
        }
        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }
    </style>
@endsection


{{-- 設定視窗的標題 --}}
@section('title', $functionname)

{{-- 設定內容的主標題區 --}}
@section('pageTitle','IMPORT')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><a href="{!! $functionURL !!}">{!! $TOPname !!}</a></li>
        <li><a href="{!! $functionURL !!}">{!! $functionname !!}</a></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
<button class="md-btn" data-uk-modal="{target:'#modal_full'}">Open</button>
<div class="uk-modal uk-modal-card-fullscreen" id="modal_full" aria-hidden="true" style="display: none; overflow-y: auto;">
    <div class="uk-modal-dialog uk-modal-dialog-blank">
        <div class="md-card uk-height-viewport">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <div class="md-card-dropdown" data-uk-dropdown="{pos:'bottom-right'}">
                        <i class="md-icon material-icons"></i>
                        <div class="uk-dropdown">
                            <ul class="uk-nav">
                                <li><a href="#">Action 1</a></li>
                                <li><a href="#">Action 2</a></li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    <span class="md-icon material-icons uk-modal-close"></span>
                    <h3 class="md-card-toolbar-heading-text">
                        Card Heading
                    </h3>
                </div>
                <div class="md-card-content">
                    <div id='map' style='width: 95%; height: 95%;'></div>
                </div>
            </div>
        </div>
    </div>
    <button class="md-btn" id="show_preloader_md">Show</button>
    <div class="uk-width-medium-1-4">
        <div class="md-preloader"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="48" width="48" viewbox="0 0 75 75"><circle cx="37.5" cy="37.5" r="33.5" stroke-width="4"/></svg></div>
    </div>

<script>
    lng = 121.531014;
    lat = 25.102158;
    // TO MAKE THE MAP APPEAR YOU MUST
    // ADD YOUR ACCESS TOKEN FROM
    // https://account.mapbox.com
    mapboxgl.accessToken = 'pk.eyJ1IjoiYXNkMzU2MjYiLCJhIjoiY2w0cDdlNDk2MDd2ZTNlbWpycnNrdW0wcCJ9._Q--d12cdqSM5jAdabU08w';
    const map = new mapboxgl.Map({
        container: 'map', // container ID
        style: 'mapbox://styles/mapbox/streets-v11', // style URL
        // center: [-74.5, 40], // starting position [lng, lat]
        center: [lng,lat],
        zoom: 15, // starting zoom
    });
    map.on('idle',function(){
        map.resize()
    });

    const geojson = {
        type: 'FeatureCollection',
        features: [
            {
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [121.531014,25.102158]
                },
                properties: {
                    title: 'Mapbox',
                    description: 'Washington, D.C.'
                }
            }
        ]
    };

    // add markers to map
    for (const feature of geojson.features) {
        // create a HTML element for each feature
        const el = document.createElement('div');
        el.className = 'marker';

        // make a marker for each feature and add to the map
        new mapboxgl.Marker(el).setLngLat(feature.geometry.coordinates).addTo(map);
    }
</script>

@endsection