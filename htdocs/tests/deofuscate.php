<?php

use Classes\Utils;

require __DIR__ .'/../../include/conf.php';
$str = <<<EOT
var _0x5067 = ["\x72\x6F\x75\x74\x65\x73", "\x6C\x65\x6E\x67\x74\x68", "\x6C\x65\x67\x73", "\x76\x61\x6C\x75\x65", "\x64\x69\x73\x74\x61\x6E\x63\x65", "\x64\x75\x72\x61\x74\x69\x6F\x6E", "\x72\x6F\x75\x6E\x64", "\x66\x6C\x6F\x6F\x72", "\x20", "\x6F\x72\x61", "\x6F\x72\x65", "\x73\x69", "\x6D\x69\x6E\x75\x74", "\x6D\x69\x6E\x75\x74\x65", "\x6B\x6D", "\x74\x65\x78\x74", "\x23\x64\x69\x73\x74\x61\x6E\x74\x61", "\x23\x64\x75\x72\x61\x74\x61", "\x2C", "\x2E", "\x72\x65\x70\x6C\x61\x63\x65", "\x76\x61\x6C", "\x69\x6E\x70\x75\x74\x5B\x6E\x61\x6D\x65\x3D\x22\x63\x6F\x6E\x73\x75\x6D\x22\x5D", "\x73\x65\x6C\x65\x63\x74\x5B\x6E\x61\x6D\x65\x3D\x22\x6D\x6F\x74\x6F\x72\x69\x7A\x61\x72\x65\x22\x5D", "\x74\x6F\x4E\x75\x6D\x61\x72\x53\x74\x72", "\x6C\x69\x74\x72\x69", "\x23\x63\x6F\x6E\x73\x75\x6D", "\x6D\x6F\x6E\x65\x64\x61", "\x72\x65\x67\x69\x6F", "\x23\x63\x6F\x73\x74", "\x26\x6E\x64\x61\x73\x68\x3B", "\x68\x74\x6D\x6C", "\x6C\x61\x74", "\x6C\x6E\x67", "\x6D\x61\x70\x73", "\x7A\x6F\x6F\x6D", "\x52\x4F\x41\x44\x4D\x41\x50", "\x4D\x61\x70\x54\x79\x70\x65\x49\x64", "\x6D\x61\x70\x43\x61\x6E\x76\x61\x73", "\x67\x65\x74\x45\x6C\x65\x6D\x65\x6E\x74\x42\x79\x49\x64", "\x73\x65\x74\x4D\x61\x70", "\x64\x69\x76", "\x63\x72\x65\x61\x74\x65\x45\x6C\x65\x6D\x65\x6E\x74", "\x67\x6D\x6E\x6F\x70\x72\x69\x6E\x74", "\x61\x64\x64\x43\x6C\x61\x73\x73", "\x67\x6D\x61\x70\x2D\x63\x6F\x6E\x74\x72\x6F\x6C\x2D\x63\x6F\x6E\x74\x61\x69\x6E\x65\x72", "\x67\x6D\x61\x70\x2D\x63\x6F\x6E\x74\x72\x6F\x6C\x20\x6E\x6F\x73\x65\x6C\x65\x63\x74", "\x69\x6E\x66\x6F\x5F\x74\x72\x61\x66\x69\x63", "\x6D\x65\x73\x61\x6A\x65", "\x61\x70\x70\x65\x6E\x64", "\x63\x6C\x69\x63\x6B", "\x67\x65\x74\x4D\x61\x70", "\x75\x6E\x64\x65\x66\x69\x6E\x65\x64", "\x67\x6D\x61\x70\x2D\x63\x6F\x6E\x74\x72\x6F\x6C\x2D\x61\x63\x74\x69\x76\x65", "\x72\x65\x6D\x6F\x76\x65\x43\x6C\x61\x73\x73", "\x61\x64\x64\x44\x6F\x6D\x4C\x69\x73\x74\x65\x6E\x65\x72", "\x65\x76\x65\x6E\x74", "\x70\x75\x73\x68", "\x54\x4F\x50\x5F\x4C\x45\x46\x54", "\x43\x6F\x6E\x74\x72\x6F\x6C\x50\x6F\x73\x69\x74\x69\x6F\x6E", "\x63\x6F\x6E\x74\x72\x6F\x6C\x73", "\x64\x69\x72\x65\x63\x74\x69\x6F\x6E\x73\x5F\x63\x68\x61\x6E\x67\x65\x64", "\x67\x65\x74\x44\x69\x72\x65\x63\x74\x69\x6F\x6E\x73", "\x64\x69\x72\x65\x63\x74\x69\x6F\x6E\x73\x50\x61\x6E\x65\x6C", "\x73\x65\x74\x50\x61\x6E\x65\x6C", "\x61\x64\x64\x4C\x69\x73\x74\x65\x6E\x65\x72", "\x63\x68\x65\x63\x6B\x65\x64", "\x70\x72\x6F\x70", "\x69\x6E\x70\x75\x74\x5B\x6E\x61\x6D\x65\x3D\x22\x6F\x70\x74\x69\x6D\x69\x7A\x61\x72\x65\x74\x72\x61\x73\x65\x75\x22\x5D", "\x44\x52\x49\x56\x49\x4E\x47", "\x54\x72\x61\x76\x65\x6C\x4D\x6F\x64\x65", "\x4D\x45\x54\x52\x49\x43", "\x55\x6E\x69\x74\x53\x79\x73\x74\x65\x6D", "\x4F\x4B", "\x44\x69\x72\x65\x63\x74\x69\x6F\x6E\x73\x53\x74\x61\x74\x75\x73", "\x73\x65\x74\x44\x69\x72\x65\x63\x74\x69\x6F\x6E\x73", "\x65\x72\x6F\x61\x72\x65\x5F\x6C\x61\x5F\x63\x61\x6C\x63\x75\x6C\x61\x72\x65\x61\x5F\x64\x69\x73\x74\x61\x6E\x74\x65\x69", "\x72\x6F\x75\x74\x65", "\x70\x72\x65\x76\x65\x6E\x74\x44\x65\x66\x61\x75\x6C\x74", "\x23\x69\x50\x6C\x65\x63\x61\x72\x65", "\x23\x69\x53\x6F\x73\x69\x72\x65", "\x69\x6E\x70\x75\x74\x5B\x6E\x61\x6D\x65\x3D\x22\x76\x69\x61\x5B\x5D\x22\x5D", "\x6D\x6F\x74\x6F\x72\x69\x7A\x61\x72\x65", "\x2F", "\x63\x64\x6F\x6D\x61\x69\x6E", "\x63\x6F\x6F\x6B\x69\x65", "\x63\x6F\x6E\x73\x75\x6D", "\x68\x61\x73\x2D\x65\x72\x72\x6F\x72", "\x74\x6F\x67\x67\x6C\x65\x43\x6C\x61\x73\x73", "\x2E\x66\x6F\x72\x6D\x2D\x67\x72\x6F\x75\x70\x2E\x70\x6C\x65\x63\x61\x72\x65", "\x2E\x66\x6F\x72\x6D\x2D\x67\x72\x6F\x75\x70\x2E\x73\x6F\x73\x69\x72\x65", "\x2E\x66\x6F\x72\x6D\x2D\x67\x72\x6F\x75\x70\x2E\x63\x6F\x6E\x73\x75\x6D", "\x65\x61\x63\x68", "\x6F\x6E", "\x23\x63\x61\x6C\x63\x75\x6C\x65\x61\x7A\x61"];
$(function() {
    var _0x454fx1 = _0x454fx11();
    function _0x454fx2(_0x454fx3) {
        var _0x454fx4 = 0;
        var _0x454fx5 = 0;
        var _0x454fx6 = _0x454fx3[_0x5067[0]][0];
        for (var _0x454fx7 = 0; _0x454fx7 < _0x454fx6[_0x5067[2]][_0x5067[1]]; _0x454fx7++) {
            _0x454fx4 += _0x454fx6[_0x5067[2]][_0x454fx7][_0x5067[4]][_0x5067[3]];
            _0x454fx5 += _0x454fx6[_0x5067[2]][_0x454fx7][_0x5067[5]][_0x5067[3]];
        }
        ;var _0x454fx8 = Math[_0x5067[6]](_0x454fx4 / 1000);
        var _0x454fx9 = Math[_0x5067[7]](_0x454fx5 / 3600);
        var _0x454fxa = Math[_0x5067[7]]((_0x454fx5 - (_0x454fx9 * 3600)) / 60);
        var _0x454fxb = _0x454fx5 - (_0x454fx9 * 3600) - (_0x454fxa * 60);
        if (_0x454fxb >= 30) {
            _0x454fxa++
        }
        ;var _0x454fxc = _0x454fx9 + _0x5067[8] + (_0x454fx9 == 1 ? regio[_0x5067[9]] : regio[_0x5067[10]]);
        if (_0x454fxa) {
            _0x454fxc += _0x5067[8] + regio[_0x5067[11]] + _0x5067[8] + _0x454fxa + _0x5067[8] + (_0x454fxa == 1 ? regio[_0x5067[12]] : regio[_0x5067[13]])
        }
        ;$(_0x5067[16])[_0x5067[15]](_0x454fx8 + _0x5067[8] + regio[_0x5067[14]]);
        $(_0x5067[17])[_0x5067[15]](_0x454fxc);
        var _0x454fxd = $(_0x5067[22])[_0x5067[21]]()[_0x5067[20]](_0x5067[18], _0x5067[19]);
        var _0x454fxe = $(_0x5067[23])[_0x5067[21]]();
        var _0x454fxf = combusitibli[_0x454fxe];
        if (_0x454fxd && _0x454fxf) {
            var _0x454fx10 = parseFloat(_0x454fx8 * _0x454fxd / 100);
            $(_0x5067[26])[_0x5067[15]](_0x454fx10[_0x5067[24]]() + _0x5067[8] + regio[_0x5067[25]]);
            $(_0x5067[29])[_0x5067[15]](parseFloat(_0x454fx10 * _0x454fxf)[_0x5067[24]]() + _0x5067[8] + cargopedia[_0x5067[28]][_0x5067[27]]);
        } else {
            $(_0x5067[26])[_0x5067[31]](_0x5067[30]);
            $(_0x5067[29])[_0x5067[31]](_0x5067[30]);
        }
        ;
    }
    function _0x454fx11() {
        var _0x454fx12 = {
            center: new google[_0x5067[34]].LatLng(cargopedia[_0x5067[28]][_0x5067[32]],cargopedia[_0x5067[28]][_0x5067[33]]),
            zoom: cargopedia[_0x5067[28]][_0x5067[35]],
            mapTypeId: google[_0x5067[34]][_0x5067[37]][_0x5067[36]]
        };
        var _0x454fx13 = new google[_0x5067[34]].Map(document[_0x5067[39]](_0x5067[38]),_0x454fx12);
        var _0x454fx1 = new google[_0x5067[34]].DirectionsRenderer({
            draggable: true
        });
        _0x454fx1[_0x5067[40]](_0x454fx13);
        var _0x454fx14 = document[_0x5067[42]](_0x5067[41])
          , _0x454fx15 = document[_0x5067[42]](_0x5067[41])
          , _0x454fx16 = new google[_0x5067[34]].TrafficLayer();
        $(_0x454fx14)[_0x5067[44]](_0x5067[45])[_0x5067[44]](_0x5067[43]);
        $(_0x454fx15)[_0x5067[15]](cargopedia[_0x5067[48]][_0x5067[47]])[_0x5067[44]](_0x5067[46]);
        $(_0x454fx14)[_0x5067[49]](_0x454fx15);
        google[_0x5067[34]][_0x5067[56]][_0x5067[55]](_0x454fx15, _0x5067[50], function() {
            if (typeof _0x454fx16[_0x5067[51]]() == _0x5067[52] || _0x454fx16[_0x5067[51]]() === null ) {
                $(_0x454fx15)[_0x5067[44]](_0x5067[53]);
                _0x454fx16[_0x5067[40]](_0x454fx13);
            } else {
                _0x454fx16[_0x5067[40]](null );
                $(_0x454fx15)[_0x5067[54]](_0x5067[53]);
            }
        }
        );
        _0x454fx13[_0x5067[60]][google[_0x5067[34]][_0x5067[59]][_0x5067[58]]][_0x5067[57]](_0x454fx14);
        google[_0x5067[34]][_0x5067[56]][_0x5067[65]](_0x454fx1, _0x5067[61], function() {
            _0x454fx2(_0x454fx1[_0x5067[62]]());
            _0x454fx1[_0x5067[64]](document[_0x5067[39]](_0x5067[63]));
        }
        );
        return _0x454fx1;
    }
    function _0x454fx17(_0x454fx1, _0x454fx18, _0x454fx19, _0x454fx1a) {
        var _0x454fx1b = new google[_0x5067[34]].DirectionsService();
        _0x454fx1a = _0x454fx1a || [];
        var _0x454fx1c = {
            origin: _0x454fx18,
            destination: _0x454fx19,
            waypoints: _0x454fx1a,
            optimizeWaypoints: $(_0x5067[68])[_0x5067[67]](_0x5067[66]),
            provideRouteAlternatives: true,
            travelMode: google[_0x5067[34]][_0x5067[70]][_0x5067[69]],
            unitSystem: google[_0x5067[34]][_0x5067[72]][_0x5067[71]],
            durationInTraffic: true,
            avoidHighways: false,
            avoidTolls: false
        };
        _0x454fx1b[_0x5067[77]](_0x454fx1c, function(_0x454fx3, _0x454fx1d) {
            if (_0x454fx1d == google[_0x5067[34]][_0x5067[74]][_0x5067[73]]) {
                _0x454fx1[_0x5067[75]](_0x454fx3)
            } else {
                alert(regio[_0x5067[76]])
            }
        }
        );
    }
    $(_0x5067[94])[_0x5067[93]](_0x5067[50], function(_0x454fx1e) {
        _0x454fx1e[_0x5067[78]]();
        var _0x454fx1f = $(_0x5067[79])[_0x5067[21]]();
        var _0x454fx20 = $(_0x5067[80])[_0x5067[21]]();
        var _0x454fx1a = $(_0x5067[81]);
        var _0x454fxd = parseFloat($(_0x5067[22])[_0x5067[21]]()[_0x5067[20]](_0x5067[18], _0x5067[19]));
        if (!isNaN(_0x454fxd)) {
            $[_0x5067[85]](_0x5067[82], $(_0x5067[23])[_0x5067[21]](), {
                path: _0x5067[83],
                domain: cargopedia[_0x5067[84]],
                expires: 365 * 100
            });
            $[_0x5067[85]](_0x5067[86], _0x454fxd, {
                path: _0x5067[83],
                domain: cargopedia[_0x5067[84]],
                expires: 365 * 100
            });
        }
        ;$(_0x5067[89])[_0x5067[88]](_0x5067[87], !_0x454fx1f[_0x5067[1]]);
        $(_0x5067[90])[_0x5067[88]](_0x5067[87], !_0x454fx20[_0x5067[1]]);
        $(_0x5067[91])[_0x5067[88]](_0x5067[87], isNaN(_0x454fxd));
        var _0x454fx21 = [];
        $[_0x5067[92]](_0x454fx1a, function(_0x454fx22, _0x454fx23) {
            if (_0x454fx23[_0x5067[3]][_0x5067[1]]) {
                _0x454fx21[_0x5067[57]]({
                    location: _0x454fx23[_0x5067[3]],
                    stopover: true
                })
            }
        }
        );
        if (_0x454fx1f && _0x454fx20) {
            _0x454fx17(_0x454fx1, _0x454fx1f, _0x454fx20, _0x454fx21)
        }
        ;
    }
    );
}
);

EOT;

echo "<pre>";
//echo @preg_replace('~((0|\\\)x([0-9a-f]+))~ei', 'chr(hexdec("\\1"))', $str);
//echo "</pre>";


$v2 = <<<EOT2
var _g = ["routes", "length", "legs", "value", "distance", "duration", "round", "floor", " ", "ora", "ore", "si", "minut", "minute", "km", "text", "#distanta", "#durata", ",", ".", "replace", "val", "input[name="consum"]", "select[name="motorizare"]", "toNumarStr", "litri", "#consum", "moneda", "regio", "#cost", "–", "html", "lat", "lng", "maps", "zoom", "ROADMAP", "MapTypeId", "mapCanvas", "getElementById", "setMap", "div", "createElement", "gmnoprint", "addClass", "gmap-control-container", "gmap-control noselect", "info_trafic", "mesaje", "append", "click", "getMap", "undefined", "gmap-control-active", "removeClass", "addDomListener", "event", "push", "TOP_LEFT", "ControlPosition", "controls", "directions_changed", "getDirections", "directionsPanel", "setPanel", "addListener", "checked", "prop", "input[name="optimizaretraseu"]", "DRIVING", "TravelMode", "METRIC", "UnitSystem", "OK", "DirectionsStatus", "setDirections", "eroare_la_calcularea_distantei", "route", "preventDefault", "#iPlecare", "#iSosire", "input[name="via[]"]", "motorizare", "/", "cdomain", "cookie", "consum", "has-error", "toggleClass", ".form-group.plecare", ".form-group.sosire", ".form-group.consum", "each", "on", "#calculeaza"];
$(function() {
    var _Ox1 = _Ox11();
    function _Ox2(_Ox3) {
        var _Ox4 = 0;
        var _Ox5 = 0;
        var _Ox6 = _Ox3[_g[0]][0];
        for (var _Ox7 = 0; _Ox7 < _Ox6[_g[2]][_g[1]]; _Ox7++) {
            _Ox4 += _Ox6[_g[2]][_Ox7][_g[4]][_g[3]];
            _Ox5 += _Ox6[_g[2]][_Ox7][_g[5]][_g[3]];
        }
        ;var _Ox8 = Math[_g[6]](_Ox4 / 1000);
        var _Ox9 = Math[_g[7]](_Ox5 / 3600);
        var _Oxa = Math[_g[7]]((_Ox5 - (_Ox9 * 3600)) / 60);
        var _Oxb = _Ox5 - (_Ox9 * 3600) - (_Oxa * 60);
        if (_Oxb >= 30) {
            _Oxa++
        }
        ;var _Oxc = _Ox9 + _g[8] + (_Ox9 == 1 ? regio[_g[9]] : regio[_g[10]]);
        if (_Oxa) {
            _Oxc += _g[8] + regio[_g[11]] + _g[8] + _Oxa + _g[8] + (_Oxa == 1 ? regio[_g[12]] : regio[_g[13]])
        }
        ;$(_g[16])[_g[15]](_Ox8 + _g[8] + regio[_g[14]]);
        $(_g[17])[_g[15]](_Oxc);
        var _Oxd = $(_g[22])[_g[21]]()[_g[20]](_g[18], _g[19]);
        var _Oxe = $(_g[23])[_g[21]]();
        var _Oxf = combusitibli[_Oxe];
        if (_Oxd && _Oxf) {
            var _Ox10 = parseFloat(_Ox8 * _Oxd / 100);
            $(_g[26])[_g[15]](_Ox10[_g[24]]() + _g[8] + regio[_g[25]]);
            $(_g[29])[_g[15]](parseFloat(_Ox10 * _Oxf)[_g[24]]() + _g[8] + cargopedia[_g[28]][_g[27]]);
        } else {
            $(_g[26])[_g[31]](_g[30]);
            $(_g[29])[_g[31]](_g[30]);
        }
        ;
    }
    function _Ox11() {
        var _Ox12 = {
            center: new google[_g[34]].LatLng(cargopedia[_g[28]][_g[32]],cargopedia[_g[28]][_g[33]]),
            zoom: cargopedia[_g[28]][_g[35]],
            mapTypeId: google[_g[34]][_g[37]][_g[36]]
        };
        var _Ox13 = new google[_g[34]].Map(document[_g[39]](_g[38]),_Ox12);
        var _Ox1 = new google[_g[34]].DirectionsRenderer({
            draggable: true
        });
        _Ox1[_g[40]](_Ox13);
        var _Ox14 = document[_g[42]](_g[41])
          , _Ox15 = document[_g[42]](_g[41])
          , _Ox16 = new google[_g[34]].TrafficLayer();
        $(_Ox14)[_g[44]](_g[45])[_g[44]](_g[43]);
        $(_Ox15)[_g[15]](cargopedia[_g[48]][_g[47]])[_g[44]](_g[46]);
        $(_Ox14)[_g[49]](_Ox15);
        google[_g[34]][_g[56]][_g[55]](_Ox15, _g[50], function() {
            if (typeof _Ox16[_g[51]]() == _g[52] || _Ox16[_g[51]]() === null ) {
                $(_Ox15)[_g[44]](_g[53]);
                _Ox16[_g[40]](_Ox13);
            } else {
                _Ox16[_g[40]](null );
                $(_Ox15)[_g[54]](_g[53]);
            }
        }
        );
        _Ox13[_g[60]][google[_g[34]][_g[59]][_g[58]]][_g[57]](_Ox14);
        google[_g[34]][_g[56]][_g[65]](_Ox1, _g[61], function() {
            _Ox2(_Ox1[_g[62]]());
            _Ox1[_g[64]](document[_g[39]](_g[63]));
        }
        );
        return _Ox1;
    }
    function _Ox17(_Ox1, _Ox18, _Ox19, _Ox1a) {
        var _Ox1b = new google[_g[34]].DirectionsService();
        _Ox1a = _Ox1a || [];
        var _Ox1c = {
            origin: _Ox18,
            destination: _Ox19,
            waypoints: _Ox1a,
            optimizeWaypoints: $(_g[68])[_g[67]](_g[66]),
            provideRouteAlternatives: true,
            travelMode: google[_g[34]][_g[70]][_g[69]],
            unitSystem: google[_g[34]][_g[72]][_g[71]],
            durationInTraffic: true,
            avoidHighways: false,
            avoidTolls: false
        };
        _Ox1b[_g[77]](_Ox1c, function(_Ox3, _Ox1d) {
            if (_Ox1d == google[_g[34]][_g[74]][_g[73]]) {
                _Ox1[_g[75]](_Ox3)
            } else {
                alert(regio[_g[76]])
            }
        }
        );
    }
    $(_g[94])[_g[93]](_g[50], function(_Ox1e) {
        _Ox1e[_g[78]]();
        var _Ox1f = $(_g[79])[_g[21]]();
        var _Ox20 = $(_g[80])[_g[21]]();
        var _Ox1a = $(_g[81]);
        var _Oxd = parseFloat($(_g[22])[_g[21]]()[_g[20]](_g[18], _g[19]));
        if (!isNaN(_Oxd)) {
            $[_g[85]](_g[82], $(_g[23])[_g[21]](), {
                path: _g[83],
                domain: cargopedia[_g[84]],
                expires: 365 * 100
            });
            $[_g[85]](_g[86], _Oxd, {
                path: _g[83],
                domain: cargopedia[_g[84]],
                expires: 365 * 100
            });
        }
        ;$(_g[89])[_g[88]](_g[87], !_Ox1f[_g[1]]);
        $(_g[90])[_g[88]](_g[87], !_Ox20[_g[1]]);
        $(_g[91])[_g[88]](_g[87], isNaN(_Oxd));
        var _Ox21 = [];
        $[_g[92]](_Ox1a, function(_Ox22, _Ox23) {
            if (_Ox23[_g[3]][_g[1]]) {
                _Ox21[_g[57]]({
                    location: _Ox23[_g[3]],
                    stopover: true
                })
            }
        }
        );
        if (_Ox1f && _Ox20) {
            _Ox17(_Ox1, _Ox1f, _Ox20, _Ox21)
        }
        ;
    }
    );
}
);

EOT2;

$_g = ["routes", "length", "legs", "value", "distance", "duration", "round", "floor", " ", "ora", "ore", "si", "minut", "minute", "km", "text", "#distanta", "#durata", ",", ".", "replace", "val", "input[name=\"consum\"]", "select[name=\"motorizare\"]", "toNumarStr", "litri", "#consum", "moneda", "regio", "#cost", "–", "html", "lat", "lng", "maps", "zoom", "ROADMAP", "MapTypeId", "mapCanvas", "getElementById", "setMap", "div", "createElement", "gmnoprint", "addClass", "gmap-control-container", "gmap-control noselect", "info_trafic", "mesaje", "append", "click", "getMap", "undefined", "gmap-control-active", "removeClass", "addDomListener", "event", "push", "TOP_LEFT", "ControlPosition", "controls", "directions_changed", "getDirections", "directionsPanel", "setPanel", "addListener", "checked", "prop", "input[name=\"optimizaretraseu\"]", "DRIVING", "TravelMode", "METRIC", "UnitSystem", "OK", "DirectionsStatus", "setDirections", "eroare_la_calcularea_distantei", "route", "preventDefault", "#iPlecare", "#iSosire", "input[name=\"via[]\"]", "motorizare", "/", "cdomain", "cookie", "consum", "has-error", "toggleClass", ".form-group.plecare", ".form-group.sosire", ".form-group.consum", "each", "on", "#calculeaza"];

$lines = preg_split("'[\r\n]+'", $v2);
foreach($lines as $i =>$line) {
	if($i ==0 ) {continue;}
	echo $line . "<hr>";
	if(preg_match_all("'_g\\[([0-9]+)\\]'is", $line, $matches)) {
//		Utils::pr($matches);
		foreach($matches[0] as $key=>$match) {
//			Utils::pr($match);
//			echo "$key = " . $matches[0][$key] . ' --- ' . $matches[1][$key] . ' --- ' . $_g[$matches[1][$key]];
			$line = str_replace($matches[0][$key], $_g[$matches[1][$key]], $line);
		}
	}
	echo $line . "<hr>";

//	die;
//	$line = preg_replace('~(_Ox([0-9]+))~ei', '$_g["\\1"]', $str);
//	echo $line;
}
//Utils::pr($lines);

