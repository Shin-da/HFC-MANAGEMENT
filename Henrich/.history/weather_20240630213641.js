



$.getJSON(
    "https://api.openweathermap.org/data/2.5/weather?lat={lat}&lon={lon}&appid={API key}"
    ,function(data){
    console.log(data);

});