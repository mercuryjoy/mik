<meta charset="UTF-8">

<div class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-hover dataTable" role="grid">
                <thead>
                <tr role="row">
                    <th>终端ID</th>
                    <th>终端名称</th>
                    <th>地址</th>
                    <th>餐饮类别</th>
                </thead>
                <tbody>
                @foreach($shops as $shop)
                    <tr role="row">
                        <td>{{ $shop->id }}</td>
                        <td>{{ $shop->name }}</td>
                        <td>{{ $shop->address }}</td>
                        <td>{{ $shop->category->name }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
