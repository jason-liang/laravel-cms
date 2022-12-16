<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    html, body {
      margin: 0;
    }

    html, body, #page_404 {
      height: 100%;
      width: 100%;
      overflow: hidden;
      position: relative;
    }

    #page_404 .wrapper {
      position: absolute;
      width: 100%;
      top: 40%;
      text-align: center;
      transform: translateY(-50%);
    }
    #page_404 .code {
      letter-spacing: 5px;
      font-size: 30px;
    }
  </style>
</head>
<body>
  <div id="page_404">
    <div class="wrapper">
      <div class="code">
        404 
      </div> 
      <!-- <div class="word">
        页面丢失了
      </div> -->
      <div class="btn">
        <button>点击返回首页</button>
      </div>
    </div>
  </div>
</body>
</html>