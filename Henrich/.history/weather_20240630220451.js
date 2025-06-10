// weather API
var APIkey = "d189a36e54852bb0b9b7edeba90591c1";

// ask user for their location
navigator.geolocation.getCurrentPosition(function(position) {
    var lat = position.coords.latitude;
    var lon = position.coords.longitude;

    var url = "https://api.openweathermap.org/data/2.5/weather?lat=" + lat + "&lon=" + lon + "&appid=d189a36e54852bb0b9b7edeba90591c1";

    fetch(url)
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            console.log(data);
            var icon = "https://openweathermap.org/img/wn/" + data.weather[0].icon + ".png" ;
            $(".weather-icon").attr("src", icon);
        })

});

