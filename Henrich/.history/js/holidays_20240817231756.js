//Holiday Calendar

//api key 
var APIkey = "b20adf21-34a3-49e8-893f-465fe7a71600";
var country = "PH";
var year = "2023";
var url = "https://holidayapi.com/v1/holidays?pretty&key=" + APIkey + "&country=" + country + "&year=" + year;

// var url = "https://holidayapi.com/v1/holidays?pretty&key=b20adf21-34a3-49e8-893f-465fe7a71600&country=PH&year=2023";

//get holidays
function getHolidays() {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log(data);

            for (var i = 0; i < data["holidays"].length; i++) {
                var date = data["holidays"][i]["date"];
                var name = data["holidays"][i]["name"];
                var country = data["holidays"][i]["country"];
                var description = data["holidays"][i]["description"];
                var type = data["holidays"][i]["type"];
                var image = data["holidays"][i]["image"];
                var country = data["holidays"][i]["country"];
            } 
        })
        .catch(error => console.error(error))
}

getHolidays();
console.log(url);

