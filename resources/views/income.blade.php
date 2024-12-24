<!DOCTYPE html>
<html>
<head>
    <title>El-Saleh Incomes Report</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
    <meta http-equiv="Content-Type" content="text/html" charset='utf-8'/>
</head>
<body>
<h1>{{ $data['title'] }}</h1>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>اسم المشروع</th>
        <th>Description</th>
        <th>Payer</th>
        <th>Total</th>
        <th>Paid At</th>
    </tr>
    @foreach($data['incomes'] as $income)
        <tr>
            <td>{{ $income->id }}</td>
            <td>{{ $income->project->name }}</td>
            <td>{{ $income->description }}</td>
            <td>{{ $income->paid_to }}</td>
            <td>{{ $income->total }}</td>
            <td>{{ $income->paid_at }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>

