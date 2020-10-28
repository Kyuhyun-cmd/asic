/*
NodeJS CMX Receiver

A basic web service to accept CMX data from a Cisco Meraki network
- Accept a GET request from Meraki and respond with a validator
- Meraki will POST to server, if validated.
- POST will contain a secret, which can be verified by the server.
- JSON data will be in the req.body.data. This will be available in the cmxData function's data object.

-- This skeleton app will only place the data received on the console. It's up to the developer to use this how ever required

*/

// CHANGE THESE CONFIGURATIONS to match your CMX configuration
var port = process.env.OVERRIDE_PORT || process.env.PORT || 1890;
var secret = process.env.SECRET || "12345678";
var validator = process.env.VALIDATOR || "fed205925ab7135ec6bb684a235572e1715815d8";
var route = process.env.ROUTE || "/cmx";

var mysql = require('mysql');
var conn = mysql.createConnection({
    host: 'localhost',
    user: 'cmx_info',
    password: '12345678',
    database: 'cmx_info'
});

conn.connect();

// All CMX JSON data will end up here. Send it to a database or whatever you fancy.
// data format specifications: https://documentation.meraki.com/MR/Monitoring_and_Reporting/CMX_Analytics#Version_2.0
function cmxData(data) {
    console.log("JSON Feed: " + JSON.stringify(data, null, 2));
};


//**********************************************************

// Express Server
var express = require('express');
var app = express();
var bodyParser = require('body-parser')
app.use(bodyParser.json())

// CMX Location Protocol, see https://documentation.meraki.com/MR/Monitoring_and_Reporting/CMX_Analytics#API_Configuration
//
// Meraki asks for us to know the secret
app.get(route, function (req, res) {
    console.log("Validator = " + validator);
    res.status(200).send(validator);
});
//
// Getting the flow of data every 1 to 2 minutes
app.post(route, function (req, res) {
    if (req.body.secret == secret) {
        console.log("Secret verified");
        var data = req.body;
        if(data.type != 'DevicesSeen') {
            console.log('[LOG] Bluetooth detected. Skipping...');
            return;
        }

        var param_list = [];
        for(var i = 0, len = data.data.observations.length; i < len; i++) {
            var li = data.data.observations[i];
            console.log('[LOG] ' + i + ': Location>>');
            console.dir(li.location);
            if(li.location == null) {
                console.log('[LOG] EMPTY LOCATION DETECTED');
                li['location'] = {
                    x: [''],
                    y: [''],
                    lat: 0,
                    lng: 0
                };
            }
            param_list.push([
                li.ipv4,
                data.data.apMac,
                li.clientMac,
                li.seenEpoch,
                li.manufacturer,
                li.os,
                li.location.lat,
                li.location.lng,
                li.location['x'][0],
                li.location['y'][0]
            ]);
        }

        var sql = "INSERT INTO cmx_info (IPV4, AP_MAC, CLIENT_MAC, SEEN_TIME, MANU, OS, LAT, LNG, X, Y) VALUES ?";


        conn.query(sql, [param_list], function(err, result) {
            if(err) throw err;
            console.log('[LOG] Records Inserted: ' + result.affectedRows);
        });
        //cmxData(req.body);
    } else {
        console.log("Secret was invalid");
    }
    res.status(200);
});

// Start server
app.listen(port, function () {
    console.log("CMX Receiver listening on port: " + port);
});
