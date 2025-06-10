<!-- <?= "returns"?> -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<style>
    .boxes {
        border: 1px solid black;
        width: 800px;
      padding: 10px;
        /* display: flex;
        flex-wrap: wrap;
        flex-direction: column;
        justify-content: space-between;
        align-items: center; */
    }
    .box {
        width: 100px;
        height: 100px;
    }
    .box1 {
        background-color: red;
    }
    .box2 {
        background-color: blue;
    }
    .box3 {
        background-color: green;
    }
</style>
<div class="box boxes">
    <div class="box1">
        1
    </div>
    <div class="box box2">
        2
    </div>
    <div class="box box3">
        3
    </div>
</div>
    
</body>
</html>