/* --------------------------------------------
Google Map
-------------------------------------------- */
window.onload = MapLoadScript;
function GmapInit() {
    Gmap = $(".map-canvas");
    Gmap.each(function () {
        var $this = $(this),
            lat = "",
            lng = "",
            zoom = 12,
            scrollwheel = false,
            zoomcontrol = true,
            draggable = true,
            mapType = google.maps.MapTypeId.ROADMAP,
            title = "",
            contentString = "",
            theme_icon_path = $this.data("icon-path"),
            dataLat = $this.data("lat"),
            dataLng = $this.data("lng"),
            dataZoom = $this.data("zoom"),
            dataType = $this.data("type"),
            dataScrollwheel = $this.data("scrollwheel"),
            dataZoomcontrol = $this.data("zoomcontrol"),
            dataHue = $this.data("hue"),
            dataTitle = $this.data("title"),
            dataContent = $this.data("content");

        if (dataZoom !== undefined && dataZoom !== false) {
            zoom = parseFloat(dataZoom);
        }
        if (dataLat !== undefined && dataLat !== false) {
            lat = parseFloat(dataLat);
        }
        if (dataLng !== undefined && dataLng !== false) {
            lng = parseFloat(dataLng);
        }
        if (dataScrollwheel !== undefined && dataScrollwheel !== null) {
            scrollwheel = dataScrollwheel;
        }
        if (dataZoomcontrol !== undefined && dataZoomcontrol !== null) {
            zoomcontrol = dataZoomcontrol;
        }
        if (dataType !== undefined && dataType !== false) {
            if (dataType == "satellite") {
                mapType = google.maps.MapTypeId.SATELLITE;
            } else if (dataType == "hybrid") {
                mapType = google.maps.MapTypeId.HYBRID;
            } else if (dataType == "terrain") {
                mapType = google.maps.MapTypeId.TERRAIN;
            }
        }
        if (dataTitle !== undefined && dataTitle !== false) {
            title = dataTitle;
        }
        if (navigator.userAgent.match(/iPad|iPhone|Android/i)) {
            draggable = false;
        }

        var mapOptions = {
            zoom: zoom,
            scrollwheel: scrollwheel,
            zoomControl: zoomcontrol,
            draggable: draggable,
            center: new google.maps.LatLng(lat, lng),
            mapTypeId: mapType,
        };
        var map = new google.maps.Map($this[0], mapOptions);

        //var image = 'images/icons/map-marker.png';
        var image = theme_icon_path;

        if (dataContent !== undefined && dataContent !== false) {
            contentString =
                '<div class="map-data">' +
                "<h6>" +
                title +
                "</h6>" +
                '<div class="map-content">' +
                dataContent +
                "</div>" +
                "</div>";
        }
        var infowindow = new google.maps.InfoWindow({
            content: contentString,
        });

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lng),
            map: map,
            icon: image,
            title: title,
        });
        if (dataContent !== undefined && dataContent !== false) {
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map, marker);
            });
        }

        if (dataHue !== undefined && dataHue !== false) {
            var styles = [
                {
                    featureType: "landscape",
                    stylers: [
                        {
                            saturation: -100,
                        },
                        {
                            lightness: 60,
                        },
                    ],
                },
                {
                    featureType: "road.local",
                    stylers: [
                        {
                            saturation: -100,
                        },
                        {
                            lightness: 40,
                        },
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "transit",
                    stylers: [
                        {
                            saturation: -100,
                        },
                        {
                            visibility: "simplified",
                        },
                    ],
                },
                {
                    featureType: "administrative.province",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "water",
                    stylers: [
                        {
                            visibility: "on",
                        },
                        {
                            lightness: 30,
                        },
                    ],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry.fill",
                    stylers: [
                        {
                            color: "#ef8c25",
                        },
                        {
                            lightness: 40,
                        },
                    ],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry.stroke",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "poi.park",
                    elementType: "geometry.fill",
                    stylers: [
                        {
                            color: "#b6c54c",
                        },
                        {
                            lightness: 40,
                        },
                        {
                            saturation: -40,
                        },
                    ],
                },
                {},
            ];
            map.setOptions({ styles: styles });
        }
    });
}

function MapLoadScript() {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "https://maps.googleapis.com/maps/api/js";
    script.onload = GmapInit; // Call GmapInit after the script has loaded
    document.body.appendChild(script);
}
