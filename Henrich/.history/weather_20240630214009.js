
var APIkey = "1a6a0a2b6f6f7a8e2f7c5d6e9f8a9b9c";
var city = "London";
var lat = "51.5085";
var lon = "-0.1257";
var url = "https://api.openweathermap.org/data/2.5/weather?q=" + city + "&appid=" + APIkey;



$.getJSON(
    "https://api.openweathermap.org/data/2.5/weather?lat=" + lat + "&lon=" + lon + "&appid=" + APIkey
    ,function(data){
    console.log(data);

});


https://api.openweathermap.org/data/2.5/weather?lat=51.5085&lon=-0.1257&appid=1a6a0a2b6f6f7a8e2f7c5d6e9f8a9b9c