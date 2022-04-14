var IeducarSingletonMap = function () {
  this.map = null;
  this.lat          = $j('#latitude');
  this.lng          = $j('#longitude');
  this.street       = $j('#logradouro_logradouro');
  this.street_str   = $j('#logradouro');
  this.number       = $j('#numero');
  this.city         = $j('#municipio_municipio');
};

IeducarSingletonMap.prototype.getAddress = function () {
  var formatMunicipio = function (text) {
    return text.replace(/[\d-]/g, '').replace(/ \(.*$/, '').trim();
  };

  var formatUF = function (text) {
    return text.match(/\(([^)]+)\)/)[1];
  };

  var address = '',
      addresses = [
        this.street.val() || this.street_str.val(),
        this.number.val(),
        formatMunicipio(this.city.val()),
        formatUF(this.city.val()),
        'brasil'
      ];

  $j.each(addresses, function(index, item) {
    address += item + ' ';
  });

  return address.trim();
};

IeducarSingletonMap.prototype.buildMap = function () {
  this.map = new GMaps({
    div: '#map',
    lat: this.lat.val() || -14.2400732,
    lng: this.lng.val() || -53.1805018,
    zoomControl: true,
    zoomControlOpt: {
      style: 'SMALL',
      position: 'TOP_LEFT'
    },
    panControl: false
  });
};

IeducarSingletonMap.prototype.addMarker = function (lat, lng) {
  var that = this;

  this.map.addMarker({
    lat: lat || this.lat.val(),
    lng: lng || this.lng.val(),
    draggable: true,
    dragend: function(event) {
      that.lat.val(event.latLng.lat());
      that.lng.val(event.latLng.lng());
    }
  });
};

IeducarSingletonMap.prototype.render = function () {
  var that = this, address = this.getAddress();

  this.buildMap();

  if (this.lat.val() && this.lng.val()) {
    this.addMarker();
  } else if (!$j.isEmptyObject(address)) {
    GMaps.geocode({
      address: address,
      callback: function(results, status) {
        if (status == 'OK') {
          var latlng = results[0].geometry.location;

          that.map.setCenter(latlng.lat(), latlng.lng());

          that.addMarker(latlng.lat(), latlng.lng());
        }
      }
    });
  } else {
    GMaps.geolocate({
      success: function(position) {
       that.map.setCenter(position.coords.latitude, position.coords.longitude);
     }
    });
  }

  GMaps.on('marker_added', this.map, function(marker) {
    that.lat.val(marker.getPosition().lat());
    that.lng.val(marker.getPosition().lng());
  });
};

IeducarSingletonMap.prototype.reload = function () {
  var that = this, address = this.getAddress();

  GMaps.geocode({
    address: address,
    callback: function(results, status) {
      if (status == 'OK') {
        var latlng = results[0].geometry.location;

        that.map.setCenter(latlng.lat(), latlng.lng());

        if ($j.isEmptyObject(that.map.markers)) {
          that.addMarker(latlng.lat(), latlng.lng());
        } else {
          that.map.markers[0].setPosition(new google.maps.LatLng(latlng.lat(), latlng.lng()));
          that.lat.val(latlng.lat());
          that.lng.val(latlng.lng());
        }
      }
    }
  });
};