// weather API
var APIkey = "d189a36e54852bb0b9b7edeba90591c1";

// ask user for their location
navigator.geolocation.getCurrentPosition(function(position) {
    var lat = position.coords.latitude;
    var lon = position.coords.longitude;

    var url = "https://api.opencagedata.com/geocode/v1/json?q=" + lat + "%2C" + lon + "&key=f5f877536c354a1585e698822c0f84c1";

    fetch(url)
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            console.log(data);
            var components = data.results[0].components;
            var city = components.city;
            var country = components.country;

            var url = "https://api.openweathermap.org/data/2.5/weather?q=" + city + "," + country + "&appid=" + APIkey;

            fetch(url)
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    console.log(data);
                    var icon = "https://openweathermap.org/img/wn/" + data.weather[0].icon + ".png" ;
                    $(".weather-icon").attr("src", icon);

                    var city = data.name;
                    $(".city").text(city);
                    
                    var weatherDescription = data.weather[0].description;
                    $(".weather-description").text(weatherDescription);

                    var temp = data.main.temp;
                    temp = Math.round(temp - 273.15);
                    $(".temp").append(temp);
                });
        });

});


