// weather API

// ask 



var APIkey = "d189a36e54852bb0b9b7edeba90591c1";
var city = "London";
var lat = "51.5085";
var lon = "-0.1257";
var url = "https://api.openweathermap.org/data/2.5/weather?lat=" + lat + "&lon=" + lon + "&appid=" + APIkey;

fetch(url)
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        console.log(data);
        var icon = "https://openweathermap.org/img/wn/" + data.weather[0].icon + ".png" ;
        $(".weather-icon").attr("src", icon);

    })



