var citybranding_markers = [];
function citybranding_mod_map_initialize() {

    var poiCenter = new google.maps.LatLng(poiLat, poiLng);

    var mapOptions = {
        scrollwheel: true,
        center: poiCenter,
        zoom: parseInt(zoom)
    }

    var citybranding_mod_map = new google.maps.Map(document.getElementById('citybranding-mod-map-canvas'),
        mapOptions);

    var infowindow = new google.maps.InfoWindow({
        content: poiTitle //+ ' (' + poiAddress + ')'
    });

    //create POI marker
    var marker = new google.maps.Marker({
        position: poiCenter,
        map: citybranding_mod_map,
        title: poiTitle,
        animation: google.maps.Animation.DROP
    });
    marker.addListener('click', function() {
        infowindow.open(citybranding_mod_map, marker);
    });

    if (poiIcon != '') {
        marker.setIcon(poiIcon);
    }
    citybranding_markers.push(marker);


    var circle ={
        path: google.maps.SymbolPath.CIRCLE,
        fillColor: 'red',
        fillOpacity: 0.8,
        scale: 6.5,
        strokeColor: 'white',
        strokeWeight: 1
    };

    //create relative brand markers
    for (var i = 0, length = relativeBrands.length; i < length; i++) {
        var data = relativeBrands[i],
            latLng = new google.maps.LatLng(data.latitude, data.longitude);
        // Create marker and putting it on the map
        var brand = new google.maps.Marker({
            position: latLng,
            //icon: 'https://storage.googleapis.com/support-kms-prod/SNP_2752125_en_v0',
            icon: circle,
            map: citybranding_mod_map,
            title: data.title,
            id: data.id
        });
        citybranding_markers.push(brand);

        //infoBox(map, marker, data);
        //
        if(data.moderation == 1){
            brand.setIcon('http://maps.google.com/mapfiles/ms/icons/blue-dot.png');
        }
    }

    resetBounds(citybranding_mod_map, citybranding_markers);


    //infowindow.open(citybranding_mod_map, marker);
}

function resetBounds(map, gmarkers) {
    var a = 0;
    bounds = null;
    bounds = new google.maps.LatLngBounds();
    for (var i=0; i<gmarkers.length; i++) {
        if(gmarkers[i].getVisible()){
            a++;
            bounds.extend(gmarkers[i].position);
        }
    }
    if(a > 0){
        map.fitBounds(bounds);
        var listener = google.maps.event.addListener(map, 'idle', function() {
            if (map.getZoom() > 18) map.setZoom(18);
            google.maps.event.removeListener(listener);
        });
    }
}