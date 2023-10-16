<!DOCTYPE html>
<html>
<head>
    <title>Al-Ajmi SSHR Dashboard</title>
    <title>Register Report</title>
    <link rel="icon" href="{{asset("img/ajmi.png")}}" type="image/png">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>
<body>
<div class="container">
    <div class="col-12">
        <div class="row">
            <div>
                <h1 class="alert alert-secondary" style="font-weight: bold">Main Process</h1>
            </div>
            @foreach($main_process as $item)
                <div class="col-lg-3">
                    <div class="card" style="width: 16rem;margin-top: 10px">
                      <a target="_blank" href="{{$item->url}}">
                          <img class="card-img-top" src="{{asset("img/dashboard/$item->image")}}" alt="Card image cap">
                      </a>
                        <div class="card-body">
                            <h5 class="card-title">{{$item->name}}</h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</div>

</body>
</html>
